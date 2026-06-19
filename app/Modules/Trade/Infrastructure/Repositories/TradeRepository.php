<?php

declare(strict_types=1);

namespace App\Modules\Trade\Infrastructure\Repositories;

use App\Modules\Trade\Application\DTO\StoreTradeDTO;
use App\Modules\Trade\Domain\Trade;

interface TradeRepository
{
    public function findExistingTrade(StoreTradeDTO $dto): ?Trade;
    public function storeTrade(StoreTradeDTO $dto): Trade;
}
