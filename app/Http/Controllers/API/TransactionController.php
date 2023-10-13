<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use Illuminate\Http\Response;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transactions = Transaction::forUser()
            ->with('transactionCategory', 'transactionSubCategory', 'user','payments')
            ->withSum('payments','amount')
            ->get();
        return TransactionResource::collection($transactions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransactionRequest $request)
    {
        $transaction = Transaction::create($request->validated());
        return TransactionResource::make($transaction);
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        $transaction->load('transactionCategory', 'transactionSubCategory', 'user', 'payments');
        $transaction->payments_sum_amount = $transaction->payments->sum('amount');
        return TransactionResource::make($transaction);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        $transaction->update($request->validated());
        return TransactionResource::make($transaction);
    }
}
