<?php

use App\Modules\Balances\Application\Services\BalanceService;
use App\Modules\Balances\Domain\Currency; // Imported your new Enum
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] #[Title('Make a Deposit')] class extends Component
{
    public string $currency = Currency::USDT->value;
    public string $amount = '';

    /**
     * @return array<string, array<int, string|\Illuminate\Validation\Rules\In>>
     */
    protected function rules(): array
    {
        return [
            // Using Currency::values() dynamically for validation rules
            'currency' => ['required', 'string', Rule::in(Currency::values())],
            'amount'   => ['required', 'numeric', 'gt:0'],
        ];
    }

    /**
     * @return \App\Modules\Balances\Domain\Balance[]
     */
    public function getBalancesProperty(BalanceService $balanceService): array
    {
        /** @var \App\Modules\Users\Domain\User $user */
        $user = Auth::user();

        return $balanceService->getBalancesByAccount($user->account_id);
    }

    public function submitDeposit(BalanceService $balanceService): void
    {
        $this->validate();

        try {
            /** @var \App\Modules\Users\Domain\User $user */
            $user = Auth::user();

            $balanceService->deposit(
                accountId: $user->account_id,
                currency:  strtoupper($this->currency),
                amount:    $this->amount
            );

            session()->flash('success', "Successfully deposited {$this->amount} {$this->currency}!");
            $this->reset('amount');
        } catch (\Exception $e) {
            session()->flash('error', 'Deposit failed: ' . $e->getMessage());
        }
    }
}; ?>

<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Balances & Deposits') }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <div class="md:col-span-2 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Your Account Balances</h3>
            
            @if(empty($this->balances))
                <div class="text-center py-8 text-gray-500 dark:text-gray-400 text-sm">
                    No active asset balances found. Deposit funds to initialize your wallets.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700/50 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                            <tr>
                                <th scope="col" class="px-4 py-3">Asset</th>
                                <th scope="col" class="px-4 py-3 text-right">Available</th>
                                <th scope="col" class="px-4 py-3 text-right">Locked</th>
                                <th scope="col" class="px-4 py-3 text-right">Total Balance</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($this->balances as $balance)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                    <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">
                                        {{ $balance->currency }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-mono text-green-600 dark:text-green-400">
                                        {{ number_format((float) $balance->available, 8, '.', '') }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-mono text-amber-600 dark:text-amber-400">
                                        {{ number_format((float) $balance->locked, 8, '.', '') }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-mono font-medium text-gray-900 dark:text-gray-100">
                                        {{ number_format((float) $balance->available + (float) $balance->locked, 8, '.', '') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Make a Deposit</h3>

            @if (session()->has('success'))
                <div class="p-3 mb-4 text-sm text-green-700 bg-green-100 rounded dark:bg-green-900/30 dark:text-green-400">
                    {{ session('success') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="p-3 mb-4 text-sm text-red-700 bg-red-100 rounded dark:bg-red-900/30 dark:text-red-400">
                    {{ session('error') }}
                </div>
            @endif

            <form wire:submit.prevent="submitDeposit" class="space-y-4">
                <div>
                    <label for="currency" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Asset Currency</label>
                    <select id="currency" wire:model="currency" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2.5 text-sm focus:ring-blue-500 focus:border-blue-500 text-black dark:text-white">
                        @foreach(Currency::cases() as $case)
                            <option value="{{ $case->value }}">{{ $case->name }}</option>
                        @endforeach
                    </select>
                    @error('currency') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deposit Amount</label>
                    <input type="number" step="any" id="amount" wire:model="amount" placeholder="0.00" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2.5 text-sm focus:ring-blue-500 focus:border-blue-500 text-black dark:text-white">
                    @error('amount') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <button type="submit" class="w-full mt-4 font-bold py-3 px-4 rounded text-white shadow transition-colors bg-blue-600 hover:bg-blue-700">
                    Deposit
                </button>
            </form>
        </div>

    </div>
</div>