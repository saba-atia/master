<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('user')
            ->orderBy('date', 'desc')
            ->paginate(10);
    
        $users = User::orderBy('name')->get();
    
        return view('dash.pages.finance', compact('transactions', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:salary,expense,bonus,advance,other',
            'amount' => 'required|numeric',
            'date' => 'required|date',
            'status' => 'required|in:paid,pending,rejected',
            'description' => 'nullable|string'
        ]);

        Transaction::create($validated);

        return redirect()->route('finance.index')
            ->with('success', 'Transaction added successfully');
    }

    public function edit(Transaction $transaction)
    {
        $users = User::orderBy('name')->get();
        
        return view('partials.edit-transaction-form', compact('transaction', 'users'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:salary,expense,bonus,advance,other',
            'amount' => 'required|numeric',
            'date' => 'required|date',
            'status' => 'required|in:paid,pending,rejected',
            'description' => 'nullable|string'
        ]);

        $transaction->update($validated);

        return redirect()->route('finance.index')
            ->with('success', 'Transaction updated successfully');
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete();
        
        return response()->json(['success' => 'Transaction deleted successfully']);
    }
}