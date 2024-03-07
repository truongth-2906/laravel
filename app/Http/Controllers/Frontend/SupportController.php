<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Requests\Frontend\SupportRequest;
use App\Mail\MailSupport;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;

/**
 * Class SupportController.
 */
class SupportController
{
    /**
     * @param SupportRequest $request
     * @return JsonResponse
     */
    public function index(SupportRequest $request)
    {
        try {
            Mail::to(config('base.email_support'))->send(new MailSupport($request->all()));

            return response()->json([
                'message' => __('Send support success!')
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                'message' => __('Send support fail!'),
                'error' => true
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
