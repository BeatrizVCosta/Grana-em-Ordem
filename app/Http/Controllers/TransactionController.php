<?php

namespace App\Http\Controllers;

use App\Models\Transaction; // Importe o Model Transaction
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Importe o Facade Auth
use Illuminate\Validation\Rule;
use App\Models\Category;

class TransactionController extends Controller
{public function index(Request $request)
    {
        $user = Auth::user();
        $query = $user->transactions()->latest();

        $areFiltersActive = false; 

        if ($request->filled('type') && in_array($request->type, ['income', 'expense'])) {
            $query->where('type', $request->type);
            $areFiltersActive = true;
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
            $areFiltersActive = true;
        }

        if ($request->filled('description')) {
            $query->where('description', 'like', '%' . $request->description . '%');
            $areFiltersActive = true;
        }

        if ($request->filled('start_date')) {
            $query->whereDate('transaction_date', '>=', $request->start_date);
            $areFiltersActive = true;
        }
        if ($request->filled('end_date')) {
            $query->whereDate('transaction_date', '<=', $request->end_date);
            $areFiltersActive = true;
        }

        $transactions = $query->get();

        // --- Cálculo dos Totais Filtrados ---
        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');
        $balance = $totalIncome - $totalExpense;

        $categories = $user->categories()->orderBy('name')->get();

        $filters = $request->only(['type', 'category_id', 'description', 'start_date', 'end_date']);

        // Passa a nova flag para a view
        return view('transactions.index', compact('transactions', 'categories', 'filters', 'totalIncome', 'totalExpense', 'balance', 'areFiltersActive'));
    }

    public function create()
    {
        $categories = Auth::user()->categories()->orderBy('name')->get();
        return view('transactions.create', compact('categories'));
    }

    public function store(Request $request)
    {
        // Validação dos dados do formulário
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'transaction_date' => 'required|date',
            'type' => 'required|in:income,expense',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        // Cria a transação associada ao usuário logado
        Auth::user()->transactions()->create($validated);

        // Redireciona de volta para a lista de transações com uma mensagem de sucesso
        return redirect()->route('transactions.index')->with('success', 'Transação adicionada com sucesso!');
    }
    public function edit(Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }

        $categories = Auth::user()->categories()->orderBy('name')->get();
        return view('transactions.edit', compact('transaction', 'categories'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        // Garante que o usuário só pode atualizar as próprias transações
        if ($transaction->user_id !== Auth::id()) {
            abort(403); // Acesso negado
        }

        // Validação dos dados do formulário
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'transaction_date' => 'required|date',
            'type' => ['required', Rule::in(['income', 'expense'])],
            'category_id' => 'nullable|exists:categories,id', 
        ]);

        $transaction->update($validated); // Atualiza a transação

        return redirect()->route('transactions.index')->with('success', 'Transação atualizada com sucesso!');
    }

    public function destroy(Transaction $transaction)
    {
        // Garante que o usuário só pode excluir as próprias transações
        if ($transaction->user_id !== Auth::id()) {
            abort(403); // Acesso negado
        }

        $transaction->delete(); // Exclui a transação

        return redirect()->route('transactions.index')->with('success', 'Transação excluída com sucesso!');
    }
}