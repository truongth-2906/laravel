<?php

namespace App\Domains\Notification\Services;

use App\Domains\Job\Models\Job;
use App\Domains\Notification\Events\PushNotification;
use App\Domains\Notification\Models\Notification;
use App\Services\BaseService;
use DB;
use Exception;
use Illuminate\Http\Response;

/**
 * Class NotificationService.
 */
class NotificationService extends BaseService
{
    /**
     * @param Notification $notification
     */
    public function __construct(Notification $notification)
    {
        $this->model = $notification;
    }

    /**
     * @return int
     */
    public function countUnreadNotifications()
    {
        $this->newQuery();

        return $this->query
            ->whereHas('receivers', function ($e) {
                $e->where([
                    'user_id' => auth()->id(),
                    'is_read' => false
                ]);
            })
            ->count();
    }

    /**
     * @param int|null $paginate
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getMyNotifications($paginate = null)
    {
        $paginate = $paginate ? $paginate : config('paging.quantity');

        $this->newQuery()
            ->with(['sender:id,name', 'notifiable:id'])
            ->whereReceiverId(auth()->id())->eagerLoad();

        $notifications = $this->query
            ->select('id', 'sender_id', 'notifiable_id', 'notifiable_type', 'type')
            ->with('receiverPivot', function ($e) {
                $e->where('user_id', auth()->id());
            })
            ->orderBy('created_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->paginate($paginate);

        if ((int) request()->page > $notifications->lastPage()) {
            request()->instance()->query->set('page', $notifications->lastPage());
            return $this->getMyNotifications($paginate);
        }

        $this->unsetClauses();

        return $notifications;
    }

    /**
     * @param int $id
     * @return bool|mixed
     */
    public function delete(int $id)
    {
        $this->newQuery()->whereReceiverId(auth()->id());

        $notification = $this->query
            ->where('id', $id)
            ->first();

        throw_if(
            !$notification,
            Exception::class,
            'Notification not found.',
            Response::HTTP_NOT_FOUND
        );

        $notification->receivers()->updateExistingPivot(auth()->id(), [
            'deleted_at' => now(),
        ]);

        if (!$notification->isJobDoneType()) {
            return $notification->delete();
        }

        return true;
    }

    /**
     * @param Job $job
     * @param mixed $type
     * @return bool
     */
    public function createByJob(Job $job, $type)
    {
        try {
            DB::beginTransaction();
            /** @var \App\Domains\Auth\Models\User */
            $user = auth()->user();
            $senderId = null;
            $receiverId = null;

            if ($type == Notification::JOB_DONE_TYPE) {
                $senderId = $job->user->id;
                $receiverId = $job->applicants->pluck('id')->toArray();
            } else {
                $senderId = $user->id;
                $receiverId = $job->user->id;
            }

            throw_if(
                !$senderId || !$receiverId,
                Exception::class,
                'Create notification failed.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );

            $notification = $this->model->create([
                'type' => $type,
                'sender_id' => $senderId,
                'notifiable_id' => $job->id,
                'notifiable_type' => Notification::JOB_NOTIFIABLE_TYPE,
            ]);

            if (gettype($receiverId) === 'array') {
                $this->sendToMultiReceiver($notification, $receiverId);
            } else {
                $this->sendToSingleReceiver($notification, $receiverId);
            }

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function read(int $id)
    {
        $this->newQuery()->whereReceiverId(auth()->id());

        $notification = $this->query
            ->where('id', $id)
            ->with('receiverPivot', function ($e) {
                $e->where('user_id', auth()->id());
            })
            ->first();

        throw_if(!$notification, Exception::class, 'Notification not found.', Response::HTTP_NOT_FOUND);

        if ($notification->isRead()) {
            return true;
        }

        return $notification->receivers()->updateExistingPivot(auth()->id(), [
            'is_read' => true,
        ]);
    }

    /**
     * @param int $userId
     * @return $this
     */
    protected function whereReceiverId(int $userId)
    {
        $this->query->whereHas('receivers', function ($e) use ($userId) {
            $e->where('user_id', $userId);
        });

        return $this;
    }

    /**
     * @param Notification $notification
     * @param array $receiverIds
     * @return void
     */
    protected function sendToMultiReceiver(Notification $notification, array $receiverIds)
    {
        $notification->receivers()->syncWithPivotValues($receiverIds, ['is_read' => false]);
        DB::commit();
        $notification->load('receivers');
        foreach ($notification->receivers as $receiver) {
            broadcast(new PushNotification($notification->load([
                'receiverPivot' => function ($e) use ($receiver) {
                    $e->where('user_id', $receiver->id);
                }
            ]), $receiver->id));
        }
    }

    /**
     * @param Notification $notification
     * @param int $receiverId
     * @return void
     */
    protected function sendToSingleReceiver(Notification $notification, int $receiverId)
    {
        $notification->receivers()->attach($receiverId, ['is_read' => false]);
        DB::commit();
        $notification->load(['receiverPivot' => function ($e) use ($receiverId) {
            $e->where('user_id', $receiverId);
        }]);
        broadcast(new PushNotification($notification, $receiverId));
    }

    /**
     * @param $transaction
     * @return bool
     * @throws \Throwable
     */
    public function createByPayment($transaction)
    {
        try {
            throw_if(
                !$transaction,
                Exception::class,
                'Create notification failed.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
            $notification = $this->model->create([
                'type' => Notification::PAYMENT_TYPE,
                'sender_id' => $transaction->sender_id,
                'notifiable_id' => $transaction->id,
                'notifiable_type' => Notification::PAYMENT_NOTIFIABLE_TYPE,
            ]);
            $this->sendToSingleReceiver($notification, $transaction->receiver_id);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param $transaction
     * @return bool
     * @throws \Throwable
     */
    public function createByPaymentApproved($transaction)
    {
        try {
            throw_if(
                !$transaction,
                Exception::class,
                'Create notification failed.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
            $notification = $this->model->create([
                'type' => Notification::PAYMENT_TYPE,
                'sender_id' => $transaction->sender_id,
                'notifiable_id' => $transaction->id,
                'notifiable_type' => Notification::PAYMENT_NOTIFIABLE_TYPE,
            ]);
            $this->sendToSingleReceiver($notification, $transaction->sender_id);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
