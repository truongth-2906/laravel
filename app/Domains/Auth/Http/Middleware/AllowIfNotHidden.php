<?php

namespace App\Domains\Auth\Http\Middleware;

use App\Domains\Auth\Models\User;
use Closure;
use Illuminate\Http\Response;

/**
 * Class AllowIfNotHidden.
 */
class AllowIfNotHidden
{
    /**
     * @param $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->user() && $request->user()->isType(User::TYPE_FREELANCER) && $request->user()->is_hidden) {

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Forbidden'
                ], Response::HTTP_FORBIDDEN);
            }

            return redirect()->route('frontend.index')->withFlashDanger(__('You do not have access to do that.'));
        }

        return $next($request);
    }
}
