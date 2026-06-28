<?php

use App\Modules\Users\Domain\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Module.Users.Domain.User.{id}', function (User $user, $id) {
    return (int) $user->id === (int) $id;
});
