<?php

declare(strict_types=1);

namespace App\Modules\Orders\Domain;

use App\Modules\Accounts\Domain\Account;
use App\Modules\Orders\Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $account_id
 * @property string $side
 * @property string $type
 * @property string $currency
 * @property string $price
 * @property float $amount
 * @property float $filled_amount
 * @property string $status
 */
class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'account_id',
        'side',
        'type',
        'currency',
        'price',
        'amount',
        'filled_amount',
        'status',
    ];

    protected $casts = [
        'price' => 'string',
    ];

    /**
     * @return BelongsTo<Account, $this>
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    protected static function newFactory(): OrderFactory
    {
        return OrderFactory::new();
    }
}
