<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionCategoryRequest;
use App\Http\Requests\UpdateTransactionCategoryRequest;
use App\Http\Resources\TransactionCategoryResource;
use App\Models\TransactionCategory;

class TransactionCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transactionCategories = TransactionCategory::all();
        return TransactionCategoryResource::collection($transactionCategories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransactionCategoryRequest $request)
    {
        $transactionCategory = TransactionCategory::create($request->validated());
        return TransactionCategoryResource::make($transactionCategory);
    }

    /**
     * Display the specified resource.
     */
    public function show(TransactionCategory $transaction_category)
    {
        return TransactionCategoryResource::make($transaction_category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTransactionCategoryRequest $request, TransactionCategory $transaction_category)
    {
        $transaction_category->update($request->validated());
        return TransactionCategoryResource::make($transaction_category);
    }
}
