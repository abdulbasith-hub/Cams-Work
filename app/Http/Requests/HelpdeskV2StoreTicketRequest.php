<?php

namespace App\Http\Requests;

use App\Models\HelpdeskTicket;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HelpdeskV2StoreTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return session()->has('user');
    }

    public function rules(): array
    {
        return [
            'submission_token' => ['required', 'string', 'max:100'],
            'subject' => ['required', 'string', 'max:200'],
            'description' => ['required', 'string', 'max:750'],
            'deptcode' => ['nullable', 'string', 'max:20'],
            'financial_year' => ['required', 'string', 'max:20'],
            'audit_quarter' => ['required', 'integer'],
            'institution' => ['nullable', 'string', 'max:200'],
            'request_type' => ['required', Rule::in(array_keys(HelpdeskTicket::REQUEST_TYPE_OPTIONS))],
            'category' => ['required', Rule::in(HelpdeskTicket::CATEGORY_OPTIONS)],
            'priority' => ['required', Rule::in(['low', 'medium', 'high', 'urgent'])],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => ['file', 'mimes:jpeg,jpg,png,pdf', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'attachments.*.max' => 'Each attachment must not be greater than 500 kilobytes.',
            'attachments.*.mimes' => 'Each attachment must be a JPEG, PNG, or PDF file.',
        ];
    }
}
