<?php

declare(strict_types=1);

namespace App\Modules\Trade\Domain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int    $id
 * @property int    $taker_account_id
 * @property int    $maker_account_id
 * @property string $taker_order_id
 * @property string $maker_order_id
 * @property string $price
 * @property string $amount
 * @property string $currency
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Trade extends Model
{
    protected $fillable = [
        'taker_account_id',
        'maker_account_id',
        'taker_order_id',
        'maker_order_id',
        'price',
        'amount',
        'currency',
    ];
}
