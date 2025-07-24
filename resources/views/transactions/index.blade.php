<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Minhas Transações') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    {{-- Formulário de Filtro --}}
                    <form method="GET" action="{{ route('transactions.index') }}" class="mb-6 p-4 border rounded-lg bg-gray-50">
                        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            <!-- Filtro por Tipo -->
                            <div>
                                <x-input-label for="filter_type" :value="__('Tipo')" />
                                <select id="filter_type" name="type" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">{{ __('Todos') }}</option>
                                    <option value="income" @selected(isset($filters['type']) && $filters['type'] === 'income')>{{ __('Receita') }}</option>
                                    <option value="expense" @selected(isset($filters['type']) && $filters['type'] === 'expense')>{{ __('Despesa') }}</option>
                                </select>
                            </div>

                            <!-- Filtro por Categoria -->
                            <div>
                                <x-input-label for="filter_category" :value="__('Categoria')" />
                                <select id="filter_category" name="category_id" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">{{ __('Todas') }}</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" @selected(isset($filters['category_id']) && $filters['category_id'] == $category->id)>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filtro por Descrição -->
                            <div>
                                <x-input-label for="filter_description" :value="__('Descrição')" />
                                <x-text-input id="filter_description" class="block mt-1 w-full" type="text" name="description" :value="$filters['description'] ?? ''" />
                            </div>

                            <!-- Filtro por Data Inicial -->
                            <div>
                                <x-input-label for="filter_start_date" :value="__('Data Inicial')" />
                                <x-text-input id="filter_start_date" class="block mt-1 w-full" type="date" name="start_date" :value="$filters['start_date'] ?? ''" />
                            </div>

                            <!-- Filtro por Data Final -->
                            <div>
                                <x-input-label for="filter_end_date" :value="__('Data Final')" />
                                <x-text-input id="filter_end_date" class="block mt-1 w-full" type="date" name="end_date" :value="$filters['end_date'] ?? ''" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-4">
                                {{ __('Aplicar Filtros') }}
                            </x-primary-button>
                            <a href="{{ route('transactions.index') }}" class="ml-4 inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Limpar Filtros') }}
                            </a>
                        </div>
                    </form>
                    {{-- Fim do Formulário de Filtro --}}

                    {{-- Botão Adicionar Nova Transação - MOVIDO PARA CIMA DA TABELA --}}
                    <a href="{{ route('transactions.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mb-4">
                        {{ __('Adicionar Nova Transação') }}
                    </a>

                    {{-- Bloco de Resumo de Saldo - MOVIDO PARA CIMA DA TABELA --}}
                    <div class="mt-6 p-4 bg-gray-100 rounded-lg shadow mb-6">
                        <p class="text-lg font-bold mb-2">
                            {{ $areFiltersActive ? 'Resumo do Período Selecionado:' : 'Saldo Total:' }}
                        </p>
                        <p class="text-green-600">
                            Total de Receitas: R$ {{ number_format($totalIncome, 2, ',', '.') }}
                        </p>
                        <p class="text-red-600">
                            Total de Despesas: R$ {{ number_format($totalExpense, 2, ',', '.') }}
                        </p>
                        <p class="text-lg font-bold mt-2 @if($balance < 0) text-red-700 @else text-green-700 @endif">
                            Saldo: R$ {{ number_format($balance, 2, ',', '.') }}
                        </p>
                    </div>
                    {{-- Fim do Bloco de Resumo de Saldo --}}

                    @if ($transactions->isEmpty())
                        <p>Você ainda não tem transações registradas.</p>
                    @else
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Data
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Descrição
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Valor
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tipo
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Categoria
                                    </th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">Ações</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($transactions as $transaction)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $transaction->transaction_date->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $transaction->description }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap @if($transaction->type === 'expense') text-red-600 @else text-green-600 @endif">
                                            {{ $transaction->type === 'expense' ? '-' : '' }}R$ {{ number_format($transaction->amount, 2, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $transaction->type === 'income' ? 'Receita' : 'Despesa' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $transaction->category->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('transactions.edit', $transaction) }}" class="text-indigo-600 hover:text-indigo-900">Editar</a>

                                            <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" class="inline ml-4">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Tem certeza que deseja excluir esta transação?');">Excluir</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>