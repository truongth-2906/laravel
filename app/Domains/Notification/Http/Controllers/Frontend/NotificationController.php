<?php

namespace App\Domains\Notification\Http\Controllers\Frontend;

use App\Domains\Notification\Services\NotificationService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * NotificationController class
 */
class NotificationController
{
    /** @var NotificationService */
    protected NotificationService $notificationService;

    /**
     * @param NotificationService $notificationService
     * @return void
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index()
    {
        $notifications = $this->notificationService->getMyNotifications();

        return view('frontend.notification.index', compact('notifications'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            $this->notificationService->delete($id);

            return redirect()->back()->with('message', __('Delete notification successfully.'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', __('Delete notification failed.'));
        }
    }

    /**
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\Response
     */
    public function read(Request $request, $id)
    {
        try {
            $this->notificationService->read($id);
            $redirect = $request->query('redirect_to');

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => __('Read success notify.'),
                    'redirect_to' => $redirect
                ], Response::HTTP_OK);
            }

            return redirect($redirect);
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => __('Read failed notify.'),
                    'error' => true,
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return redirect()->back()->with('error', 'An error has occurred.');
        }
    }
}
