<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMembershipStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->can_manage_members;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return ['payment_status' => ['required', 'in:paid,unpaid'], 'membership_status' => ['required', 'in:active,inactive']];
    }
}
