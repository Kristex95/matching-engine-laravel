<?php

declare(strict_types=1);

namespace App\Modules\Accounts\Domain;

use App\Modules\Accounts\Database\Factories\AccountFactory;
use App\Modules\Auth\Domain\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    /** @use HasFactory<AccountFactory> */
    use HasFactory;

    protected $table = 'accounts';

    protected $fillable = [
        'account_number',
    ];

    /**
     * @return HasMany<User, $this>
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    protected static function newFactory(): AccountFactory
    {
        return AccountFactory::new();
    }
}
