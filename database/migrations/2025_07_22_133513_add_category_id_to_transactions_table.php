<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null')->after('type'); // Adiciona a coluna e a chave estrangeira
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['category_id']); // Remove a chave estrangeira
            $table->dropColumn('category_id'); // Remove a coluna
        });
    }
};