<?php

declare(strict_types=1);

namespace App\Modules\Orders\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class OrderForCurrentAccountScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        /** @var object{account_id:int|null}|null $user */
        $user = Auth::user();

        $accountId = $user?->account_id;

        if (!$accountId) {
            $builder->whereRaw('1 = 0');
            return;
        }

        $builder->where(
            $model->getTable() . '.account_id',
            $accountId
        );
    }
}
