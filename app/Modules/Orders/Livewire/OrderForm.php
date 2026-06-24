<?php

declare(strict_types=1);

namespace App\Modules\Orders\Livewire;

use App\Modules\Accounts\Application\Services\AccountService;
use App\Modules\Orders\Application\DTO\StoreOrderDTO;
use App\Modules\Orders\Application\Services\OrderService;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class OrderForm extends Component
{
    public string $symbol; // Passed from the parent view (e.g., BTC)

    public string $side = 'buy'; // 'buy' or 'sell'
    public string $type = 'limit'; // 'limit' or 'market'

    /** @var string */
    public $price = '';

    /** @var string */
    public $amount = '';

    /**
     * @var array<string, string>
     */
    protected array $rules = [
        'side'   => 'required|in:buy,sell',
        'type'   => 'required|in:limit,market',
        'price'  => 'required_if:orderType,limit|numeric|gt:0',
        'amount' => 'required|numeric|gt:0',
    ];

    public function mount(string $symbol): void
    {
        $this->symbol = strtoupper($symbol);
    }

    public function placeOrder(): void
    {
        $this->validate();

        try {
            $dto = new StoreOrderDTO(
                side:     $this->side,
                type:     $this->type,
                currency: $this->symbol,
                amount:   $this->amount,
                price:    $this->type === 'market' ? null : $this->price,
            );

            /** @var \App\Modules\Users\Domain\User $user */
            $user = Auth::user();
            $account = app(AccountService::class)->getById($user->account_id);

            app(OrderService::class)->storeNewOrder($dto, $account);

            session()->flash('success', 'Order placed successfully!');

            $this->reset(['price', 'amount']);
        } catch (Exception $e) {
            session()->flash('error', 'Failed to place order: ' . $e->getMessage());
        }
    }

    /**
     * @return mixed
     */
    public function redirectToLogin()
    {
        return $this->redirect(route('login'), navigate: true);
    }

    public function render(): View
    {
        return view('orders::livewire.order-form');
    }
}
