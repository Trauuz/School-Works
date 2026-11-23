<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateMemberRequest extends StoreMemberRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        $rules = [
            ...parent::rules(),
            'email' => ['required', 'email', 'max:255', Rule::unique('members', 'email')->ignore($this->route('member'))],
        ];
        if ($this->route('member')->user) {
            $rules['email'][] = Rule::in([$this->route('member')->user->email]);
        }

        return $rules;
    }
}
