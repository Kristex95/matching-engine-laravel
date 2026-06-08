<?php

declare(strict_types=1);

namespace App\Modules\Auth\Http\Requests\Api\V1;

use App\Modules\Users\Application\DTO\NewUserDTO;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
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
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
            ],
            'password' => [
                'required',
                'string',
                Password::min(8)
                    ->mixedCase()
                    ->numbers(),
                'confirmed',
            ],
        ];
    }

    public function toDto(): NewUserDTO
    {
        $v = $this->validated();
        return new NewUserDTO(
            name: $v['name'],
            email: $v['email'],
            password: $v['password'],
            accountId: null,
        );
    }
}
