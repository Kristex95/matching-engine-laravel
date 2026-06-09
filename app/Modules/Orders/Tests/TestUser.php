<?php

declare(strict_types=1);

namespace App\Modules\Orders\Tests;

use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @property int $id
 * @property int|null $account_id
 */
class TestUser extends Authenticatable
{
    protected $fillable = ['id', 'account_id'];

    public $timestamps = false;
}
