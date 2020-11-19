<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AccountTransactionRequest;
use App\Http\Resources\AccountTransactionCollection;
use App\Http\Resources\AccountTransactionResource;
use App\Models\Account;
use App\Models\AccountTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AccountTransactionController extends Controller
{
    public static $transactionTypeCredit = 'credit';
    public static $transactionTypeDebit = 'debit';

    /**
     * Display a listing of the resource.
     *
     * @param  \App\Models\Account  $account
     * @return AccountTransactionCollection
     */
    public function index(Account $account)
    {
        $transactions = $account->transactions()->latest('date')->paginate();
        return new AccountTransactionCollection($transactions);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AccountTransactionRequest $request
     * @param \App\Models\Account $account
     * @return AccountTransactionResource|\Illuminate\Http\JsonResponse
     */
    public function store(AccountTransactionRequest $request, Account $account)
    {
        try {
            $transaction = AccountTransaction::query()->create($request->validated());

            if($transaction->type == self::$transactionTypeCredit) {
                $account->update([
                    'balance' => $account->balance + $transaction->total
                ]);
            } else {
                $account->update([
                    'balance' => $account->balance - $transaction->total
                ]);
            }
            return new AccountTransactionResource($transaction);
        } catch (\Exception $exception) {
            Log::error("Could not add transaction with amount " . $request->input('total') . ". Message: " . $exception->getMessage());
            DB::rollBack();

            return response()->json([
                'message' => "Could not add transaction! " . $exception->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Account $account
     * @param AccountTransaction $transaction
     * @return AccountTransactionResource
     */
    public function show(Account $account, AccountTransaction $transaction)
    {
        return new AccountTransactionResource($transaction);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param AccountTransactionRequest $request
     * @param \App\Models\Account $account
     * @param AccountTransaction $transaction
     * @return AccountTransactionResource|\Illuminate\Http\JsonResponse
     */
    public function update(AccountTransactionRequest $request, Account $account, AccountTransaction $transaction)
    {
        try {
            //Revert the earlier transaction
            if($transaction->type == self::$transactionTypeCredit) {
                $account->update([
                    'balance' => $account->balance - $transaction->total
                ]);
            } else {
                $account->update([
                    'balance' => $account->balance + $transaction->total
                ]);
            }
            $transaction->update($request->validated());
            $transaction->refresh();

            //Now update with new transaction
            if($transaction->type == self::$transactionTypeCredit) {
                $account->update([
                    'balance' => $account->balance + $transaction->total
                ]);
            } else {
                $account->update([
                    'balance' => $account->balance - $transaction->total
                ]);
            }
            return new AccountTransactionResource($transaction->refresh());
        } catch (\Exception $exception) {
            Log::error("Could not add transaction with amount " . $request->input('total') . ". Message: " . $exception->getMessage());
            DB::rollBack();

            return response()->json([
                'message' => "Could not add transaction! " . $exception->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Account $account
     * @param AccountTransaction $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Account $account, AccountTransaction $transaction)
    {
        if($transaction->type == self::$transactionTypeCredit) {
            $account->update([
                'balance' => $account->balance - $transaction->total
            ]);
        } else {
            $account->update([
                'balance' => $account->balance + $transaction->total
            ]);
        }
        $transaction->delete();

        return response()->noContent();
    }
}
