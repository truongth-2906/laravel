<?php

namespace App\Domains\Auth\Http\Middleware;

use App\Domains\Auth\Models\User;
use Closure;

/**
 * Class EmployerCheck.
 */
class EmployerCheck
{
    /**
     * @param $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->user() && $request->user()->isType(User::TYPE_EMPLOYER)) {
            return $next($request);
        }

        return redirect()->route(homeRoute())->with('error', __('You do not have access to do that.'));
    }
}
