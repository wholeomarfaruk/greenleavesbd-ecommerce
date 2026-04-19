<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'phone' => preg_replace('/\s+/', '', (string) $this->input('phone')),
            'transaction_id' => $this->filled('transaction_id')
                ? strtoupper(trim((string) $this->input('transaction_id')))
                : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['required', 'string', 'max:2000'],
            'city' => ['required', 'string', 'max:120'],
            'area' => ['nullable', 'string', 'max:120'],
            'order_note' => ['nullable', 'string', 'max:2000'],
            'payment_method' => ['required', 'in:cod,bkash'],
            'transaction_id' => ['nullable', 'string', 'max:120', 'required_if:payment_method,bkash'],
        ];
    }

    public function messages(): array
    {
        return [
            'transaction_id.required_if' => 'The transaction ID field is required when bKash is selected.',
        ];
    }
}
