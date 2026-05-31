<?php

declare(strict_types=1);

namespace App\Modules\Orders\Http\Requests\Api\V1;

use App\Modules\Orders\Application\DTO\StoreOrderDTO;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
            'side'     => ['required', 'string', 'in:buy,sell'],
            'type'     => ['required', 'string', 'in:limit,market'],
            'currency' => ['required', 'string', 'max:10'],
            'price'    => [
                'nullable',
                'numeric',
                'min:0',
                'required_if:type,limit',
            ],
            'amount'   => ['required', 'numeric', 'gt:0'],
        ];
    }

    public function toDto(): StoreOrderDTO
    {
        $v = $this->validated();

        return new StoreOrderDTO(
            side: $v['side'],
            type: $v['type'],
            currency: $v['currency'],
            price: isset($v['price']) ? (string) $v['price'] : null,
            amount: (string) $v['amount'],
        );
    }
}
