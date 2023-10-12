<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        return [
            "id" => $this->id,
            "transaction_category_id" => $this->whenLoaded('transactionCategory', function () {
                return $this->transactionCategory->name;
            }),
            "transaction_sub_category_id" => $this->whenLoaded('transactionSubCategory', function () {
                return $this->transactionSubCategory->name;
            }),
            "amount" => $this->amount,
            "payer" => $this->whenLoaded('user', function () {
                return [
                    "id" => $this->user->id,
                    "email" => $this->user->email
                ];
            }),
            "due_date" => "2023-11-01",
            "vat" => 15,
            "is_vat_inclusive" => 1,
            "transaction_status_id" => $this->transactionStatus
        ];
    }
}
