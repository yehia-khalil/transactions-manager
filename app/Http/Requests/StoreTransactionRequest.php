<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'transaction_category_id' => ['required', 'exists:transaction_categories,id'],
            'transaction_sub_category_id' => ['nullable', Rule::exists('transaction_sub_categories', 'id')->where('transaction_category_id', $this->transaction_category_id)],
            'amount' => ['required', 'numeric'],
            'payer' => ['required', 'exists:users,id'],
            'due_date' => ['required', 'date', 'after:today'],
            'vat' => ['required', 'numeric', 'min:0', 'max:100'],  // Assuming VAT is a percentage
            'is_vat_inclusive' => ['required', 'boolean']
        ];
    }
}
