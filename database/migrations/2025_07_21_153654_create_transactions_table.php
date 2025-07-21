<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id(); // ID único para cada transação
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // ID do usuário a que a transação pertence
            $table->decimal('amount', 10, 2); // Valor da transação (ex: 123.45) - 10 dígitos no total, 2 decimais
            $table->string('description'); // Descrição breve (ex: "Almoço no restaurante")
            $table->date('transaction_date'); // Data da transação
            $table->enum('type', ['income', 'expense']); // Tipo: 'income' (receita) ou 'expense' (despesa)
            $table->timestamps(); // created_at e updated_at (Laravel gerencia automaticamente)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};