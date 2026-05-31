<?php

declare(strict_types=1);

namespace App\Modules\Orders\Http\Requests\Api\V1;

use App\Modules\Orders\Application\DTO\OrderFilterDTO;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ListOrdersFilteredRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'order_id'   => ['nullable', 'string'],
            'account_id' => ['nullable', 'integer'],
            'status'     => ['nullable', 'string', 'max:50'],
            'side'       => ['nullable', 'string', 'in:buy,sell'],
            'type'       => ['nullable', 'string', 'in:limit,market'],
            'currency'   => ['nullable', 'string', 'max:10'],
            'per_page'   => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function toDto(): OrderFilterDTO
    {
        $v = $this->validated();

        return new OrderFilterDTO(
            orderId: $v['order_id'] ?? null,
            accountId: isset($v['account_id']) ? (int) $v['account_id'] : null,
            status: $v['status'] ?? null,
            side: $v['side'] ?? null,
            type: $v['type'] ?? null,
            currency: $v['currency'] ?? null,
            perPage: isset($v['per_page']) ? (int) $v['per_page'] : null,
        );
    }
}
