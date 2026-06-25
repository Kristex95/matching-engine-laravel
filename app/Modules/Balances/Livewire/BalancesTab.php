<?php

declare(strict_types=1);

namespace App\Modules\Balances\Livewire;

use App\Modules\Balances\Application\Services\BalanceService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class BalancesTab extends Component
{
    public string $symbol;

    public function mount(string $symbol = 'BTC'): void
    {
        $this->symbol = $symbol;
    }

    public function render(BalanceService $balanceService): View
    {
        $user = Auth::user();

        if (!$user) {
            return view('livewire.trading.balances', [
                'balances' => [],
                'symbol' => $this->symbol,
            ]);
        }

        /** @var object{account_id:int} $user */
        $accountId = $user->account_id ?? null;

        if (!$accountId) {
            Log::warning('Authenticated user lacks an associated account_id.');
            return view('livewire.trading.balances', [
                'balances' => [],
                'symbol' => $this->symbol,
            ]);
        }

        $balances = $balanceService->getBalancesByAccount((int) $accountId);

        return view('livewire.trading.balances', [
            'balances' => $balances,
            'symbol' => $this->symbol,
        ]);
    }
}
