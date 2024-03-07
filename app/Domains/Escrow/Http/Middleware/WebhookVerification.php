<?php

namespace App\Domains\Escrow\Http\Middleware;

use Closure;

/**
 * Class WebhookVerification.
 */
class WebhookVerification
{
    protected const EVENT_TYPE = 'transaction';

    /**
     * @param $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (
            (is_null(config('escrow.webhook_verification_key')) ||
                ($request->token === config('escrow.webhook_verification_key'))
            ) &&
            $request->get('event_type') === $this::EVENT_TYPE
        ) {
            return $next($request);
        }

        return abort(403);
    }
}
