<?php

declare(strict_types=1);

namespace App\Modules\Balances\Domain;

enum Currency: string
{
    case BTC = 'BTC';
    case ETH = 'ETH';
    case USDT = 'USDT';
    case SOL = 'SOL';

    /**
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * @return array<string>
     */
    public static function nonFiat(): array
    {
        return array_values(array_filter(
            self::values(),
            static fn (string $currency): bool => $currency !== self::USDT->value,
        ));
    }
}
