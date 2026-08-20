<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HelpdeskV2StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return session()->has('user');
    }

    public function rules(): array
    {
        return [
            'comment' => ['required', 'string', 'max:3000'],
            'visibility' => ['required', 'in:public,internal,developer_to_nic'],
        ];
    }
}
