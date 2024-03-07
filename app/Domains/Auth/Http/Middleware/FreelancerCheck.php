<?php

namespace App\Domains\Auth\Http\Middleware;

use App\Domains\Auth\Models\User;
use Closure;

/**
 * Class FreelancerCheck.
 */
class FreelancerCheck
{
    /**
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->user() && $request->user()->isType(User::TYPE_FREELANCER)) {
            return $next($request);
        }

        return redirect()->route(homeRoute())->with('error', __('You do not have access to do that.'));
    }
}
