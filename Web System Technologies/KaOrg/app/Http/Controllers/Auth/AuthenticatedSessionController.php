<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\SupabaseAuth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request, SupabaseAuth $auth): RedirectResponse
    {
        $data = $request->validate(['email' => ['required', 'email', 'max:255'], 'password' => ['required', 'string']]);
        $tokens = $auth->call('POST', 'token?grant_type=password', $data);
        $user = $auth->establish($request, $tokens);

        return redirect()->route($user->profile_completed_at ? 'dashboard' : 'account.setup');
    }

    public function destroy(Request $request, SupabaseAuth $auth): RedirectResponse
    {
        $tokens = $auth->tokens($request);
        try {
            if ($tokens) {
                $auth->call('POST', 'logout?scope=local', token: $tokens['access_token']);
            }
        } catch (ValidationException|HttpException) {
            // Local sign-out must still succeed if the remote session has already ended.
        } finally {
            $auth->forget($request);
        }

        return redirect()->route('login');
    }
}
