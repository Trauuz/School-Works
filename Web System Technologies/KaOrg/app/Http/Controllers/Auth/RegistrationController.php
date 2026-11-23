<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Services\SupabaseAuth;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RegistrationController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request, SupabaseAuth $auth): RedirectResponse
    {
        $ownerToken = Str::random(64);
        $expiresAt = now()->addMinutes((int) config('session.lifetime'));
        try {
            DB::transaction(function () use ($request, $auth, $ownerToken, $expiresAt): void {
                DB::table('username_reservations')->insert(['username' => $request->validated('username'), 'email' => $request->validated('email'), 'owner_token_hash' => hash('sha256', $ownerToken), 'pending_expires_at' => $expiresAt]);
                $auth->call('POST', 'signup?redirect_to='.rawurlencode(route('auth.confirm')), [...$request->safe()->only(['email', 'password']), 'data' => ['username' => $request->validated('username'), 'registration_role' => 'admin']]);
            });
        } catch (UniqueConstraintViolationException) {
            throw ValidationException::withMessages(['username' => 'This username is already taken.']);
        } catch (HttpException $exception) {
            if ($exception->getStatusCode() !== 503) {
                throw $exception;
            }
            throw ValidationException::withMessages(['auth' => $exception->getMessage()]);
        }

        $request->session()->put('pending_signup', ['email' => $request->validated('email'), 'username' => $request->validated('username'), 'role' => 'admin', 'owner_token' => $ownerToken, 'expires_at' => $expiresAt->timestamp]);
        RateLimiter::hit('verification:'.hash('sha256', $request->validated('email')), (int) config('services.supabase.verification_resend_seconds', 60));

        return redirect()->route('verification.notice');
    }

    private function pending(Request $request): ?array
    {
        $pending = $request->session()->get('pending_signup');

        return is_array($pending) && ($pending['expires_at'] ?? 0) > time() ? $pending : null;
    }

    public function notice(Request $request): Response
    {
        $pending = $this->pending($request);
        $email = $pending['email'] ?? null;

        return response()->view('auth.verify', ['pending' => $pending, 'retryAfter' => $email ? RateLimiter::availableIn('verification:'.hash('sha256', $email)) : 0])->withHeaders(['Cache-Control' => 'no-store', 'Referrer-Policy' => 'no-referrer']);
    }

    public function status(Request $request): JsonResponse
    {
        $pending = $this->pending($request);
        $verified = $pending && User::where('username', $pending['username'])->where('email', $pending['email'])->whereNotNull('email_verified_at')->exists();

        return response()->json(['verified' => (bool) $verified, 'continue_url' => $verified ? route($request->user() ? 'verification.confirmed' : 'login') : null])->header('Cache-Control', 'no-store');
    }

    public function correction(Request $request): Response|RedirectResponse
    {
        $pending = $this->pending($request);

        return $pending ? response()->view('auth.correct-email', ['pending' => $pending])->header('Cache-Control', 'no-store') : redirect()->route('verification.notice');
    }

    public function correct(Request $request, SupabaseAuth $auth): RedirectResponse
    {
        $pending = $this->pending($request);
        abort_unless($pending, 403);
        $data = $request->validate(['email' => ['required', 'email', 'max:255'], 'password' => ['required', 'string', 'min:8', 'confirmed']]);
        $email = mb_strtolower(trim($data['email']));
        $correctionKey = 'verification:'.hash('sha256', $email);
        if (RateLimiter::tooManyAttempts($correctionKey, 1)) {
            return back()->withErrors(['email' => 'Please wait before requesting another confirmation email.']);
        }
        try {
            DB::transaction(function () use ($pending, $data, $email, $auth): void {
                $reservation = DB::table('username_reservations')->where('username', $pending['username'])->lockForUpdate()->first();
                if (! $reservation || ! hash_equals((string) $reservation->owner_token_hash, hash('sha256', $pending['owner_token'] ?? '')) || ! $reservation->pending_expires_at || strtotime($reservation->pending_expires_at) <= time() || $reservation->email !== $pending['email'] || User::where('username', $pending['username'])->exists()) {
                    throw ValidationException::withMessages(['email' => 'This signup can no longer be changed. Sign in or request another confirmation email.']);
                }
                if (User::whereRaw('LOWER(email) = ?', [$email])->exists()) {
                    throw ValidationException::withMessages(['email' => 'Use another email address or sign in to your existing account.']);
                }
                $signup = $auth->call('POST', 'signup?redirect_to='.rawurlencode(route('auth.confirm')), ['email' => $email, 'password' => $data['password'], 'data' => ['username' => $pending['username'], 'registration_role' => $pending['role']]]);
                if (data_get($signup, 'user.identities') === []) {
                    throw ValidationException::withMessages(['email' => 'We could not restart signup with that address. Try signing in or use another address.']);
                }
                DB::table('username_reservations')->where('username', $pending['username'])->update(['email' => $email]);
            });
        } catch (HttpException $exception) {
            throw ValidationException::withMessages(['email' => 'We could not restart signup. Please try again shortly.']);
        }
        $request->session()->put('pending_signup', [...$pending, 'email' => $email]);
        RateLimiter::hit('verification:'.hash('sha256', $email), (int) config('services.supabase.verification_resend_seconds', 60));

        return redirect()->route('verification.notice')->with('success', 'Confirmation email requested for your corrected address.');
    }

    public function resend(Request $request, SupabaseAuth $auth): RedirectResponse
    {
        $pending = $this->pending($request);
        $email = $pending['email'] ?? mb_strtolower(trim($request->validate(['email' => ['required', 'email', 'max:255']])['email']));
        $key = 'verification:'.hash('sha256', $email);
        if (RateLimiter::tooManyAttempts($key, 1)) {
            return back()->withErrors(['auth' => 'Please wait '.RateLimiter::availableIn($key).' seconds before requesting another email.']);
        }
        RateLimiter::hit($key, (int) config('services.supabase.verification_resend_seconds', 60));
        try {
            $auth->call('POST', 'resend?redirect_to='.rawurlencode(route('auth.confirm')), ['type' => 'signup', 'email' => $email]);
        } catch (HttpException|ValidationException $exception) {
            return back()->withErrors(['auth' => 'We could not request another email right now. Please try again shortly.']);
        }

        return back()->with('success', 'If confirmation is needed, a confirmation email has been requested.');
    }

    public function confirmed(Request $request): Response|RedirectResponse
    {
        if (! $request->user()?->email_verified_at) {
            return redirect()->route('login');
        }

        if (! $request->user()->profile_completed_at) {
            return redirect()->route('account.setup')->withHeaders(['Cache-Control' => 'no-store', 'Referrer-Policy' => 'no-referrer']);
        }

        return response()->view('auth.confirmed')->withHeaders(['Cache-Control' => 'no-store', 'Referrer-Policy' => 'no-referrer']);
    }

    public function confirm(Request $request, SupabaseAuth $auth): Response|RedirectResponse
    {
        if ($request->isMethod('get') && $request->filled('token_hash') && in_array($request->query('type'), ['email', 'signup'], true)) {
            $request->session()->put('verification_link', Crypt::encryptString(json_encode(['token_hash' => $request->query('token_hash'), 'type' => $request->query('type'), 'expires_at' => time() + 600])));

            return redirect()->route('auth.confirm')->withHeaders(['Cache-Control' => 'no-store', 'Referrer-Policy' => 'no-referrer']);
        }
        if ($request->isMethod('get') && $request->session()->has('verification_link') && ! $request->has('token_hash')) {
            return response()->view('auth.confirm-link')->withHeaders(['Cache-Control' => 'no-store', 'Referrer-Policy' => 'no-referrer']);
        }
        if ($request->isMethod('get') && ! $request->has('token_hash')) {
            return response()->view('auth.callback')->withHeaders(['Cache-Control' => 'no-store', 'Referrer-Policy' => 'no-referrer']);
        }
        try {
            if ($request->isMethod('post') && $request->boolean('confirm_link')) {
                $data = json_decode(Crypt::decryptString($request->session()->pull('verification_link', '')), true);
                if (! is_array($data) || ($data['expires_at'] ?? 0) < time()) {
                    throw ValidationException::withMessages(['auth' => 'Request a new confirmation link.']);
                }
                unset($data['expires_at']);
                $tokens = $auth->call('POST', 'verify', $data);
            } elseif ($request->isMethod('post')) {
                $data = $request->validate([
                    'access_token' => ['required', 'string', 'max:16384'],
                    'refresh_token' => ['required', 'string', 'max:4096'],
                    'type' => ['required', 'in:email,signup,recovery'],
                ]);
                $identity = $auth->call('GET', 'user', token: $data['access_token']);
                if (! Str::isUuid($identity['id'] ?? '') || empty($identity['email_confirmed_at'])) {
                    throw ValidationException::withMessages(['auth' => 'This email link could not be verified.']);
                }
                $tokens = ['access_token' => $data['access_token'], 'refresh_token' => $data['refresh_token']];
            } else {
                $data = $request->validate(['token_hash' => ['required', 'string', 'max:2048'], 'type' => ['required', 'in:email,signup,recovery']]);
                $tokens = $auth->call('POST', 'verify', $data);
            }
            abort_unless(! empty($tokens['access_token']) && ! empty($tokens['refresh_token']), 503, 'The email link did not return a valid session. Request a new link.');
            if ($data['type'] === 'recovery') {
                $auth->forget($request);
                $request->session()->regenerate();
                $request->session()->put('password_recovery', Crypt::encryptString(json_encode(['access_token' => $tokens['access_token'], 'expires_at' => time() + 600], JSON_THROW_ON_ERROR)));

                return redirect()->route('password.reset')->withHeaders(['Cache-Control' => 'no-store', 'Referrer-Policy' => 'no-referrer']);
            }
            $user = $auth->establish($request, $tokens);

            return redirect()->route($user->profile_completed_at ? 'verification.confirmed' : 'account.setup')->withHeaders(['Cache-Control' => 'no-store', 'Referrer-Policy' => 'no-referrer']);
        } catch (ValidationException|DecryptException) {
            return response()->view('auth.link-invalid', ['message' => 'This email link is invalid, incomplete, or has expired. Request a new link below.'])->withHeaders(['Cache-Control' => 'no-store', 'Referrer-Policy' => 'no-referrer']);
        } catch (HttpException $exception) {
            if ($exception->getStatusCode() !== 503) {
                throw $exception;
            }

            return response()->view('auth.link-invalid', ['message' => $exception->getMessage()])->withHeaders(['Cache-Control' => 'no-store', 'Referrer-Policy' => 'no-referrer']);
        }
    }
}
