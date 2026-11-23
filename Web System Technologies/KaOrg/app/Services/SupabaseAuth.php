<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SupabaseAuth
{
    /** @param array<string, mixed> $payload @return array<string, mixed> */
    public function call(string $method, string $path, array $payload = [], ?string $token = null): array
    {
        $url = rtrim((string) config('services.supabase.url'), '/');
        abort_if($url === '' || ! config('services.supabase.key'), 503, 'Sign in is not configured yet. Please try again once setup is complete.');
        try {
            $request = Http::acceptJson()->asJson()->withHeaders(['apikey' => config('services.supabase.key')])->connectTimeout(5)->timeout(10);
            if ($token !== null) {
                $request = $request->withToken($token);
            }
            $response = $request->send($method, $url.'/auth/v1/'.$path, $method === 'GET' ? ['query' => $payload] : ['json' => $payload]);
        } catch (ConnectionException) {
            abort(503, 'Authentication is temporarily unavailable. Please try again.');
        }
        abort_if($response->serverError(), 503, 'Authentication is temporarily unavailable. Please try again.');
        if (! $response->successful()) {
            throw ValidationException::withMessages(['auth' => $response->status() === 429 ? 'Too many attempts. Please wait before trying again.' : 'Unable to complete authentication. Check your details or request a new confirmation link.']);
        }
        $data = $response->json();
        abort_if($data !== null && ! is_array($data), 503, 'Authentication returned an invalid response. Please try again.');

        return $data ?? [];
    }

    /** @param array<string, mixed> $tokens */
    public function establish(Request $request, array $tokens): User
    {
        if (empty($tokens['access_token']) || empty($tokens['refresh_token'])) {
            throw ValidationException::withMessages(['auth' => 'Authentication did not return a session. Verify your email and sign in again.']);
        }
        $identity = $this->call('GET', 'user', token: (string) ($tokens['access_token'] ?? ''));
        if (! Str::isUuid($identity['id'] ?? '') || empty($identity['email_confirmed_at']) || empty($identity['email'])) {
            throw ValidationException::withMessages(['auth' => 'Please verify your email before signing in.']);
        }
        $email = Str::lower(trim($identity['email']));
        $user = DB::transaction(function () use ($identity, $email): User {
            $user = User::where('supabase_id', $identity['id'])->lockForUpdate()->first()
                ?? User::whereRaw('LOWER(email) = ?', [$email])->lockForUpdate()->first();
            if ($user?->supabase_id && $user->supabase_id !== $identity['id']) {
                throw ValidationException::withMessages(['auth' => 'This email is already connected to a different account.']);
            }
            $user ??= new User(['name' => $email, 'email' => $email]);
            $user->forceFill(['supabase_id' => $identity['id'], 'email_verified_at' => now(), 'email' => $email])->save();

            return $user;
        });
        Auth::login($user);
        $request->session()->regenerate();
        $this->storeTokens($request, $tokens);

        return $user;
    }

    /** @param array<string, mixed> $tokens */
    public function storeTokens(Request $request, array $tokens): void
    {
        if (empty($tokens['access_token']) || empty($tokens['refresh_token'])) {
            throw ValidationException::withMessages(['auth' => 'Authentication did not return a valid session. Please sign in again.']);
        }
        $request->session()->put('supabase', Crypt::encryptString(json_encode([
            'access_token' => $tokens['access_token'],
            'refresh_token' => $tokens['refresh_token'],
            'expires_at' => $tokens['expires_at'] ?? time() + (int) ($tokens['expires_in'] ?? 3600),
        ], JSON_THROW_ON_ERROR)));
    }

    /** @return array{access_token: string, refresh_token: string, expires_at: int}|null */
    public function tokens(Request $request): ?array
    {
        $encrypted = $request->session()->get('supabase');
        if (! is_string($encrypted)) {
            return null;
        }
        try {
            $tokens = json_decode(Crypt::decryptString($encrypted), true, flags: JSON_THROW_ON_ERROR);

            return is_array($tokens) && isset($tokens['access_token'], $tokens['refresh_token'], $tokens['expires_at']) && is_numeric($tokens['expires_at']) ? $tokens : null;
        } catch (DecryptException|\JsonException) {
            return null;
        }
    }

    public function forget(Request $request): void
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
