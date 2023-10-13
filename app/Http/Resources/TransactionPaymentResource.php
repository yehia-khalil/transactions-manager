<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionPaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "amount" => $this->amount,
            "paid_at" => $this->created_at,
            "details" => $this->details,
            "transaction" => $this->whenLoaded('transaction', function () {
                return TransactionResource::make($this->transaction);
            })
        ];
    }
}
