<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

use App\Domains\Auth\Models\User;

Broadcast::channel('notification.{receiverId}', function (User $user, $receiverId) {
    return $user->id == $receiverId;
});

Broadcast::channel('online', function (User $user) {
    return [
        'id' => $user->id,
        'token' => echo_token(),
        'type'  => $user->type,
    ];
});

Broadcast::channel('chatroom.{senderId}.{receiverId}', function (User $user, $senderId, $receiverId) {
    return $user->id == $receiverId;
});

Broadcast::channel('chat_overview.{receiverId}', function (User $user, $receiverId) {
    return $user->id == $receiverId;
});

Broadcast::channel('reaction_message.{receiverId}', function (User $user, $receiverId) {
    return $user->id == $receiverId;
});
