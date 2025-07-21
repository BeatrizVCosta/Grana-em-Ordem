<?php

namespace App\Http\Controllers;

use App\Models\Transaction; // Importe o Model Transaction
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Importe o Facade Auth
use Illuminate\Validation\Rule;

class TransactionController extends Controller
{
    /**
     * Exibe a lista de transações do usuário logado.
     */
    public function index()
    {
        // Pega as transações do usuário logado, ordenadas pela mais recente
        $transactions = Auth::user()->transactions()->latest()->get();
        return view('transactions.index', compact('transactions'));
    }

    /**
     * Exibe o formulário para criar uma nova transação.
     */
    public function create()
    {
        return view('transactions.create');
    }

    public function store(Request $request)
    {
        // Validação dos dados do formulário
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'transaction_date' => 'required|date',
            'type' => 'required|in:income,expense', // Deve ser 'income' ou 'expense'
        ]);

        // Cria a transação associada ao usuário logado
        Auth::user()->transactions()->create($validated);

        // Redireciona de volta para a lista de transações com uma mensagem de sucesso
        return redirect()->route('transactions.index')->with('success', 'Transação adicionada com sucesso!');
    }
    public function edit(Transaction $transaction)
    {
        // Garante que o usuário só pode editar as próprias transações
        if ($transaction->user_id !== Auth::id()) {
            abort(403); // Acesso negado
        }

        return view('transactions.edit', compact('transaction'));
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
            'type' => ['required', Rule::in(['income', 'expense'])], // Validação para 'income' ou 'expense'
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