<?php

namespace App\Http\Requests;

use App\Models\Transaction;
use App\Models\TransactionPayment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StoreTransactionPaymentRequest extends FormRequest
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
            'amount' => ['required', 'integer'],
            'transaction_id' => ['required', Rule::exists('transactions', 'id')],
            'details' => ['nullable', 'string', 'max:255']
        ];
    }

    protected function passedValidation()
    {
        $transactionPaidAmount = TransactionPayment::where('transaction_id', $this->transaction_id)->sum('amount');
        $transactionAmount = Transaction::find($this->transaction_id);
        if ($transactionPaidAmount == $transactionAmount->totalAmount) {
            throw ValidationException::withMessages(['invalid amount' => "You already paid this transaction fully."]);
        }
        if ($this->amount + $transactionPaidAmount > $transactionAmount->totalAmount) {
            throw ValidationException::withMessages(['invalid amount' => "Paid amount exceeds current transaction value."]);
        }
    }
}
