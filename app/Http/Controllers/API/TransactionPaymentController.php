<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionPaymentRequest;
use App\Http\Resources\TransactionPaymentResource;
use App\Models\TransactionPayment;

class TransactionPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payments = TransactionPayment::paginate(config('constants.PER_PAGE'));

        return TransactionPaymentResource::collection($payments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransactionPaymentRequest $request)
    {
        $payment = TransactionPayment::create($request->validated());

        return TransactionPaymentResource::make($payment);
    }

    /**
     * Display the specified resource.
     */
    public function show(TransactionPayment $transactionPayment)
    {
        return TransactionPaymentResource::make($transactionPayment);
    }
}
