<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; // Certifique-se que Request está importado
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\Category;
use Carbon\Carbon; // Importe a classe Carbon para trabalhar com datas

class DashboardController extends Controller
{
    public function index(Request $request) // Adicione Request $request aqui
    {
        $user = Auth::user();

        // Obter o mês e o ano a partir da requisição, ou usar o mês/ano atual como padrão
        $selectedMonth = $request->input('month', Carbon::now()->month);
        $selectedYear = $request->input('year', Carbon::now()->year);

        // Criar um objeto Carbon para o primeiro dia do mês/ano selecionado
        $startOfMonth = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->startOfDay();
        $endOfMonth = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->endOfMonth()->endOfDay();

        // 1. Calcular Totais Gerais (Receita, Despesa, Saldo) para o PERÍODO SELECIONADO
        $totalIncome = $user->transactions()
                            ->where('type', 'income')
                            ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth]) // Filtra pelo período
                            ->sum('amount');

        $totalExpense = $user->transactions()
                            ->where('type', 'expense')
                            ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth]) // Filtra pelo período
                            ->sum('amount');

        $balance = $totalIncome - $totalExpense;

        // 2. Calcular Gastos por Categoria (para o gráfico de pizza) para o PERÍODO SELECIONADO
        $expensesByCategory = $user->transactions()
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth]) // Filtra pelo período
            ->selectRaw('category_id, sum(amount) as total_amount')
            ->groupBy('category_id')
            ->with('category')
            ->get();

        // Prepara os dados para o gráfico (restante do código igual)
        $chartData = [];
        $totalExpensesForChart = $expensesByCategory->sum('total_amount');

        $colors = [
            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40',
            '#E7E9ED', '#8AC926', '#1982C4', '#6A4C93', '#F45B69', '#FFC857'
        ];
        $colorIndex = 0;

        foreach ($expensesByCategory as $expense) {
            $categoryName = $expense->category ? $expense->category->name : 'Sem Categoria';
            $percentage = $totalExpensesForChart > 0 ? ($expense->total_amount / $totalExpensesForChart) * 100 : 0;

            $chartData[] = [
                'category' => $categoryName,
                'amount' => $expense->total_amount,
                'percentage' => round($percentage, 2),
                'color' => $colors[$colorIndex % count($colors)],
            ];
            $colorIndex++;
        }

        // Para exibir o nome do mês na dashboard
        $monthName = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->translatedFormat('F');

        // Para popular os dropdowns de filtro na view
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = Carbon::createFromDate(null, $i, 1)->translatedFormat('F');
        }

        $years = range(Carbon::now()->year, Carbon::now()->year - 5); // Ex: ano atual e os 5 anos anteriores

        return view('dashboard', compact(
            'totalIncome', 'totalExpense', 'balance', 'chartData', 'totalExpensesForChart',
            'monthName', 'selectedMonth', 'selectedYear', 'months', 'years'
        ));
    }
}