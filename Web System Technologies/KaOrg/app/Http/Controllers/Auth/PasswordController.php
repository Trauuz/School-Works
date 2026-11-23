<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\SupabaseAuth;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PasswordController extends Controller
{
    public function forgot(): View
    {
        return view('auth.forgot-password');
    }

    public function email(Request $request, SupabaseAuth $auth): RedirectResponse
    {
        $data = $request->validate(['email' => ['required', 'email', 'max:255']]);
        $auth->call('POST', 'recover?redirect_to='.rawurlencode(route('auth.confirm', ['flow' => 'recovery'])), $data);

        return back()->with('success', 'If an account exists, a password reset email has been requested.');
    }

    public function reset(Request $request): View|RedirectResponse
    {
        if ($request->has('token_hash')) {
            return redirect()->route('auth.confirm', ['token_hash' => $request->query('token_hash'), 'type' => 'recovery'])->withHeaders(['Cache-Control' => 'no-store', 'Referrer-Policy' => 'no-referrer']);
        }
        try {
            $this->recovery($request);
        } catch (HttpException) {
            return view('auth.callback', ['flow' => 'recovery']);
        }

        return view('auth.reset-password');
    }

    public function update(Request $request, SupabaseAuth $auth): RedirectResponse
    {
        $data = $request->validate(['password' => ['required', 'string', 'confirmed', Password::min(8)]]);
        $recovery = $this->recovery($request);
        $auth->call('PUT', 'user', ['password' => $data['password']], $recovery['access_token']);
        $auth->forget($request);

        return redirect()->route('login')->with('success', 'Password changed. Sign in with your new password.');
    }

    /** @return array{access_token: string, expires_at: int} */
    private function recovery(Request $request): array
    {
        try {
            $data = json_decode(Crypt::decryptString((string) $request->session()->get('password_recovery')), true, flags: JSON_THROW_ON_ERROR);
        } catch (DecryptException|\JsonException) {
            abort(403, 'Request a new password reset link.');
        }
        abort_unless(is_array($data) && ($data['expires_at'] ?? 0) > time(), 403, 'This reset link has expired. Request another one.');

        return $data;
    }
}
