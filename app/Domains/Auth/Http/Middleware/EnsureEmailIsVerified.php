<?php

namespace App\Domains\Auth\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;

class EnsureEmailIsVerified
{
    /**
     * @param $request
     * @param Closure $next
     * @return RedirectResponse|mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->user() ||
            ($request->user() && !$request->user()->isVerified())) {
            return redirect()->route('frontend.auth.email.verification.step1');
        }

        return $next($request);
    }
}
