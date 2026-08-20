<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HelpdeskV2UpdateTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return session()->has('user');
    }

    public function rules(): array
    {
        return [
            'subject' => ['required', 'string', 'max:250'],
            'description' => ['required', 'string', 'max:5000'],
            'request_type' => ['required', 'string', 'max:150'],
            'category' => ['required', 'string', 'max:120'],
            'priority' => ['required', 'string', 'max:30'],
            'forward_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
