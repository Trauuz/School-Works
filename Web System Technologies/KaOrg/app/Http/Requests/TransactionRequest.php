<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->can_manage_members;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return ['member_id' => ['required', 'integer', 'exists:members,id'], 'type' => ['required', 'in:application,renewal']];
    }
}
