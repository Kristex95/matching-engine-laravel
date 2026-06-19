<?php

declare(strict_types=1);

namespace App\Modules\Balances\Domain;

use App\Modules\Accounts\Domain\Account;
use App\Modules\Balances\Database\Factories\BalanceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $account_id
 * @property string $currency
 * @property string $available
 * @property string $locked
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Balance extends Model
{
    /** @use HasFactory<BalanceFactory> */
    use HasFactory;

    protected $table = 'balances';

    protected $fillable = [
        'account_id',
        'currency',
        'available',
        'locked',
    ];

    protected $casts = [
        'available' => 'string',
        'locked'    => 'string',
    ];

    /**
     * @return BelongsTo<Account, $this>
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    protected static function newFactory(): BalanceFactory
    {
        return BalanceFactory::new();
    }
}
