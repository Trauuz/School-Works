<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Services\SupabaseAuth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request, SupabaseAuth $auth): RedirectResponse
    {
        $auth->call('POST', 'signup?redirect_to='.rawurlencode(route('auth.confirm')), $request->safe()->only(['email', 'password']));

        return redirect()->route('verification.notice')->with('email', $request->validated('email'));
    }

    public function notice(): View
    {
        return view('auth.verify');
    }

    public function resend(Request $request, SupabaseAuth $auth): RedirectResponse
    {
        $data = $request->validate(['email' => ['required', 'email', 'max:255']]);
        $auth->call('POST', 'resend', ['type' => 'signup', 'email' => $data['email']]);

        return back()->with('success', 'If confirmation is needed, a new email has been requested.')->with('email', $data['email']);
    }

    public function confirm(Request $request, SupabaseAuth $auth): RedirectResponse
    {
        $data = $request->validate(['token_hash' => ['required', 'string', 'max:2048'], 'type' => ['required', 'in:email,signup,recovery']]);
        $tokens = $auth->call('POST', 'verify', $data);
        abort_unless(! empty($tokens['access_token']) && ! empty($tokens['refresh_token']), 503, 'Authentication did not return a session. Request another link and try again.');
        if ($data['type'] === 'recovery') {
            $auth->forget($request);
            $request->session()->regenerate();
            $request->session()->put('password_recovery', Crypt::encryptString(json_encode(['access_token' => $tokens['access_token'], 'expires_at' => time() + 600], JSON_THROW_ON_ERROR)));

            return redirect()->route('password.reset')->withHeaders(['Cache-Control' => 'no-store', 'Referrer-Policy' => 'no-referrer']);
        }
        $user = $auth->establish($request, $tokens);

        return redirect()->route($user->profile_completed_at ? 'dashboard' : 'account.setup')->withHeaders(['Cache-Control' => 'no-store', 'Referrer-Policy' => 'no-referrer']);
    }
}
