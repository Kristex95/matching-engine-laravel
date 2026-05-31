<?php

declare(strict_types=1);

namespace App\Modules\Orders\Domain;

/**
 * @property int $id
 * @property string $uuid
 * @property int $account_id
 * @property string $side
 * @property string $type
 * @property string $currency
 * @property string $price
 * @property float $amount
 * @property float $filled_amount
 * @property string $status
 */
class ActiveOrder extends Order
{
    protected $table = 'active_orders';
}
