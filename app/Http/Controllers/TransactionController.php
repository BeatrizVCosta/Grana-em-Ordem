<?php

namespace App\Http\Controllers;

use App\Models\Transaction; // Importe o Model Transaction
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Importe o Facade Auth
use Illuminate\Validation\Rule;
use App\Models\Category;
use App\Exports\TransactionsExport; 
use Maatwebsite\Excel\Facades\Excel; 

class TransactionController extends Controller
{
    public function index(Request $request)
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
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'transaction_date' => 'required|date',
            'type' => 'required|in:income,expense',
            'category_id' => 'nullable|exists:categories,id',
        ], [ 
            'amount.required' => 'O valor da transação é obrigatório.',
            'amount.numeric' => 'O valor deve ser um número válido.',
            'amount.min' => 'O valor deve ser maior que zero.',
            'description.required' => 'A descrição da transação é obrigatória.',
            'description.string' => 'A descrição deve ser um texto.',
            'description.max' => 'A descrição não pode ter mais de :max caracteres.',
            'transaction_date.required' => 'A data da transação é obrigatória.',
            'transaction_date.date' => 'A data da transação deve ser uma data válida.',
            'type.required' => 'O tipo da transação (receita ou despesa) é obrigatório.',
            'type.in' => 'O tipo de transação deve ser receita ou despesa.',
            'category_id.exists' => 'A categoria selecionada não é válida.',
        ]);

        Auth::user()->transactions()->create($validated);

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
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'transaction_date' => 'required|date',
            'type' => ['required', Rule::in(['income', 'expense'])],
            'category_id' => 'nullable|exists:categories,id',
        ], [
            'amount.required' => 'O valor da transação é obrigatório.',
            'amount.numeric' => 'O valor deve ser um número válido.',
            'amount.min' => 'O valor deve ser maior que zero.',
            'description.required' => 'A descrição da transação é obrigatória.',
            'description.string' => 'A descrição deve ser um texto.',
            'description.max' => 'A descrição não pode ter mais de :max caracteres.',
            'transaction_date.required' => 'A data da transação é obrigatória.',
            'transaction_date.date' => 'A data da transação deve ser uma data válida.',
            'type.required' => 'O tipo da transação (receita ou despesa) é obrigatório.',
            'type.in' => 'O tipo de transação deve ser receita ou despesa.',
            'category_id.exists' => 'A categoria selecionada não é válida.',
        ]); 

        $transaction->update($validated);

        return redirect()->route('transactions.index')->with('success', 'Transação atualizada com sucesso!');
    }

    public function destroy(Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }

        $transaction->delete();

        return redirect()->route('transactions.index')->with('success', 'Transação excluída com sucesso!');
    }
    public function export(Request $request)
    {
        $user = Auth::user();
        $query = $user->transactions()->latest(); // Começa com todas as transações do usuário, ordenadas pela mais recente

        // --- Lógica de Filtragem (copiada do método index) ---
        if ($request->filled('type') && in_array($request->type, ['income', 'expense'])) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('description')) {
            $query->where('description', 'like', '%' . $request->description . '%');
        }

        if ($request->filled('start_date')) {
            $query->whereDate('transaction_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('transaction_date', '<=', $request->end_date);
        }

        $transactionsToExport = $query->get(); // Pega as transações já filtradas

        // Gera o nome do arquivo com a data e hora atuais
        $fileName = 'transacoes_' . now()->format('Ymd_His') . '.xlsx'; // Formato Excel (xlsx)

        // Retorna o download do arquivo Excel
        return Excel::download(new TransactionsExport($transactionsToExport), $fileName);

        
    }
}