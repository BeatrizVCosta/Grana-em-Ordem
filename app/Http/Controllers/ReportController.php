<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\Category;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // --- Dados para Gráfico de Receitas vs. Despesas Mensais (Últimos 12 meses) ---
        $monthlyData = [];
        $labels = [];
        $incomes = [];
        $expenses = [];

        // Loop para os últimos 12 meses
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->translatedFormat('M/Y'); // Ex: Jul/2025

            $labels[] = $monthName;

            $monthlyIncome = $user->transactions()
                                ->where('type', 'income')
                                ->whereMonth('transaction_date', $date->month)
                                ->whereYear('transaction_date', $date->year)
                                ->sum('amount');

            $monthlyExpense = $user->transactions()
                                ->where('type', 'expense')
                                ->whereMonth('transaction_date', $date->month)
                                ->whereYear('transaction_date', $date->year)
                                ->sum('amount');

            $incomes[] = round($monthlyIncome, 2);
            $expenses[] = round($monthlyExpense, 2);
        }

        $monthlyChartData = [
            'labels' => $labels,
            'incomes' => $incomes,
            'expenses' => $expenses,
        ];

        // --- Dados para Gráfico de Pizza de Gastos por Categoria (Ano Atual) ---
        $currentYear = Carbon::now()->year;

        $expensesByCategoryYearly = $user->transactions()
            ->where('type', 'expense')
            ->whereYear('transaction_date', $currentYear)
            ->selectRaw('category_id, sum(amount) as total_amount')
            ->groupBy('category_id')
            ->with('category')
            ->get();

        $pieChartLabels = [];
        $pieChartAmounts = [];
        $pieChartColors = [];

        $colors = [
            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40',
            '#E7E9ED', '#8AC926', '#1982C4', '#6A4C93', '#F45B69', '#FFC857'
        ];
        $colorIndex = 0;

        foreach ($expensesByCategoryYearly as $expense) {
            $categoryName = $expense->category ? $expense->category->name : 'Sem Categoria';
            $pieChartLabels[] = $categoryName;
            $pieChartAmounts[] = round($expense->total_amount, 2);
            $pieChartColors[] = $colors[$colorIndex % count($colors)];
            $colorIndex++;
        }

        $pieChartData = [
            'labels' => $pieChartLabels,
            'amounts' => $pieChartAmounts,
            'colors' => $pieChartColors,
        ];

        return view('reports.index', compact('monthlyChartData', 'pieChartData', 'currentYear'));
    }
}