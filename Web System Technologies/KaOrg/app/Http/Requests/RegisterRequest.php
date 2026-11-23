<?php

namespace App\Http\Requests;

use App\Models\User;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return ['username' => ['required', 'string', 'min:3', 'max:30', 'regex:/\A[a-zA-Z0-9_]+\z/', Rule::unique('username_reservations', 'username'), function (string $attribute, mixed $value, Closure $fail): void {
            if (is_string($value) && User::whereRaw('LOWER(username) = ?', [mb_strtolower($value)])->exists()) {
                $fail('This username is already taken.');
            }
        }], 'email' => ['required', 'email', 'max:255'], 'password' => ['required', 'string', 'confirmed', Password::min(8)]];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['username' => is_string($this->input('username')) ? mb_strtolower($this->input('username')) : $this->input('username'), 'email' => mb_strtolower(trim((string) $this->input('email')))]);
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return ['username.regex' => 'The username must contain only letters, numbers, and underscores. Spaces are not allowed.'];
    }
}
