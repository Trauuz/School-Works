<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->can_manage_members;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'membership_transaction_id' => ['required', 'integer', 'exists:membership_transactions,id'],
            'amount' => ['required', 'regex:/^[0-9]{1,9}(\.[0-9]{1,2})?$/', 'numeric', 'gt:0'],
        ];
    }
}
