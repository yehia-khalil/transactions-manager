<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionSubCategoryRequest;
use App\Http\Requests\UpdateTransactionSubCategoryRequest;
use App\Http\Resources\TransactionSubCategoryResource;
use App\Models\TransactionCategory;
use App\Models\TransactionSubCategory;

class TransactionSubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($transaction_category = null)
    {
        $transaction_sub_categories = TransactionSubCategory::when($transaction_category, function ($q) use ($transaction_category) {
            $q->where('transaction_category_id', $transaction_category);
        })->get();

        return TransactionSubCategoryResource::collection($transaction_sub_categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransactionSubCategoryRequest $request, TransactionCategory $transaction_category)
    {
        $subCategory = $transaction_category->subCategories()->create($request->validated());

        return TransactionSubCategoryResource::make($subCategory);
    }

    /**
     * Display the specified resource.
     */
    public function show(TransactionCategory $transaction_category, TransactionSubCategory $transaction_sub_category)
    {
        $subCategory = TransactionSubCategory::where('transaction_category_id', $transaction_category->id)->findOrFail($transaction_sub_category->id);

        return TransactionSubCategoryResource::make($subCategory);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTransactionSubCategoryRequest $request, TransactionCategory $transaction_category, TransactionSubCategory $transaction_sub_category)
    {
        if ($transaction_sub_category->transaction_category_id != $transaction_category->id) {
            abort(404);
        }
        $transaction_sub_category->update($request->validated());

        return TransactionSubCategoryResource::make($transaction_sub_category);
    }
}
