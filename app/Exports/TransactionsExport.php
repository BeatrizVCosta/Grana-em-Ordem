<?php

namespace App\Exports;

use App\Models\Transaction;
use Illuminate\Support\Collection; 
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings; 
use Maatwebsite\Excel\Concerns\ShouldAutoSize; 

class TransactionsExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $transactions;

    public function __construct(Collection $transactions)
    {
        $this->transactions = $transactions;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Mapeia as transações para um formato mais legível para exportação
        return $this->transactions->map(function($transaction) {
            return [
                'ID' => $transaction->id,
                'Tipo' => $transaction->type === 'income' ? 'Receita' : 'Despesa',
                'Descricao' => $transaction->description,
                'Valor' => number_format($transaction->amount, 2, ',', '.'), // Formata o valor para o padrão brasileiro
                'Data' => $transaction->transaction_date->format('d/m/Y'),
                'Categoria' => $transaction->category->name ?? 'N/A', // Exibe o nome da categoria ou 'N/A'
                'Criado Em' => $transaction->created_at->format('d/m/Y H:i:s'),
            ];
        });
    }

    /**
     * Define os cabeçalhos das colunas para o arquivo exportado.
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Tipo',
            'Descrição',
            'Valor',
            'Data',
            'Categoria',
            'Criado Em',
        ];
    }
}