<?php

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Models\SellerTransaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function get()
    {
        $user = request()->user();
        $transactions = SellerTransaction::where('user_id', $user->id)->orderBy('id', 'desc')->get();
        $total = 0;
        foreach($transactions as $transaction) {
            if($transaction->sign == "+")
            {
                $total += $transaction->amount;
            }
            else
            {
                $total -= $transaction->amount;
            }
        }
        return response()->json(['total' => $total, 'transactions' => $transactions]);
    }
}
