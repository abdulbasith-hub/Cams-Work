<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HelpdeskV2TransitionTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return session()->has('user');
    }

    public function rules(): array
    {
        return [
            'remarks' => ['nullable', 'string', 'max:5000'],
            'ticket_status' => ['nullable', 'string', 'max:80'],
            'additional_layer_userid' => ['nullable', 'string', 'max:80'],
            'layer_lead_userid' => ['nullable', 'string', 'max:80'],
            'developer_userid' => ['nullable', 'string', 'max:80'],
            'tester_userid' => ['nullable', 'string', 'max:80'],
            'expected_completion_date' => ['nullable', 'date'],
            'resolution' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
