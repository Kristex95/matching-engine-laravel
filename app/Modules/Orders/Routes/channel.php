<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('account.notification.{accountId}', function ($user, int $accountId) {
    return (int) $user->account_id === (int) $accountId; 
});