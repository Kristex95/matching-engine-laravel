<?php

declare(strict_types=1);

namespace App\Modules\Balances\Http\Requests\Api\V1;

use App\Modules\Balances\Application\DTO\BalanceDTO;
use App\Modules\Balances\Domain\Currency;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreBalanceRequest extends FormRequest
{
    /**
     * Allow authenticated users only
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'currency' => ['required', 'string', new Enum(Currency::class)],
            'amount'   => ['required', 'numeric', 'gt:0'],
        ];
    }

    public function toDto(): BalanceDTO
    {
        $v = $this->validated();

        return new BalanceDTO(
            accountId: null,
            currency:  (string) $v['currency'],
            amount:    (string) $v['amount'],
        );
    }
}
