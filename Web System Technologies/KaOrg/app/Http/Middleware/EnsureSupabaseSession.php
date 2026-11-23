<?php

namespace App\Http\Middleware;

use App\Services\SupabaseAuth;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class EnsureSupabaseSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return $next($request);
        }
        $auth = app(SupabaseAuth::class);
        $tokens = $auth->tokens($request);
        try {
            if (! $tokens || ! $request->user()->supabase_id) {
                throw ValidationException::withMessages(['auth' => 'Please sign in again.']);
            }
            if ($tokens['expires_at'] <= time() + 30) {
                $tokens = $auth->call('POST', 'token?grant_type=refresh_token', ['refresh_token' => $tokens['refresh_token']]);
                $auth->storeTokens($request, $tokens);
            }
            $identity = $auth->call('GET', 'user', token: $tokens['access_token']);
            if (($identity['id'] ?? null) !== $request->user()->supabase_id || empty($identity['email_confirmed_at'])) {
                throw ValidationException::withMessages(['auth' => 'Please sign in again.']);
            }
        } catch (ValidationException) {
            $auth->forget($request);

            return redirect()->route('login')->withErrors(['auth' => 'Your session has ended. Please sign in again.']);
        }
        $response = $next($request);
        $response->headers->set('Cache-Control', 'private, no-store');

        return $response;
    }
}
