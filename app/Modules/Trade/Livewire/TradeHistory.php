<?php

declare(strict_types=1);

namespace App\Modules\Trade\Livewire;

use App\Modules\Trade\Application\Services\TradeService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class TradeHistory extends Component
{
    use WithPagination;

    public function mount(): void {}

    public function render(TradeService $tradeService): View
    {
        $user = Auth::user();

        if (!$user) {
            return view('livewire.trading.trade-history', [
                'trades' => [],
            ]);
        }

        /** @var object{account_id:int} $user */
        $accountId = $user->account_id ?? null;

        if (!$accountId) {
            Log::warning('Authenticated user lacks an associated account_id.');
            return view('livewire.trading.trade-history', [
                'trades' => [],
            ]);
        }

        $trades = $tradeService->getAllPaginated($accountId);

        return view('livewire.trading.trade-history', [
            'trades' => $trades,
        ]);
    }
}
