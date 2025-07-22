<?php

namespace App\Http\Controllers;

use App\Models\Transaction; // Importe o Model Transaction
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Importe o Facade Auth
use Illuminate\Validation\Rule;
use App\Models\Category;

class TransactionController extends Controller
{
    public function index()
    {
        // Pega as transações do usuário logado, ordenadas pela mais recente
        $transactions = Auth::user()->transactions()->latest()->get();
        return view('transactions.index', compact('transactions'));
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