<?php

use Illuminate\Support\Facades\Broadcast;

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

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('enterprise.{id}', function ($user, $id) {
    $enterprise = \App\Models\Enterprise::find($id);
    if (!$enterprise) {
        return false;  // Không tồn tại enterprise → Deny
    }
    // Tạm return true cho dev/testing (cho phép tất cả user auth listen)
    return true;

    // Sau này thay bằng logic thật, ví dụ:
    // return $user->enterprises()->where('enterprise_id', $id)->exists();  // Nếu có relation user-enterprise
    // hoặc return Gate::allows('view', $enterprise); nếu dùng policy
});

Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;  // ← SỬA: Thay return true bằng check id để secure (chỉ user đó listen)
});