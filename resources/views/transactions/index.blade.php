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

                    {{-- Formulário de Filtro - MAIS ORGANIZADO EM UMA LINHA --}}
                    <form method="GET" action="{{ route('transactions.index') }}" class="mb-6 p-4 border rounded-lg bg-gray-50">
                        {{-- Usamos flexbox para manter os itens em linha e flex-wrap para quebrarem se necessário --}}
                        <div class="flex flex-wrap items-end gap-4">
                            <!-- Filtro por Tipo -->
                            <div class="flex-grow"> {{-- flex-grow permite que o item cresça --}}
                                <label for="filter_type" class="block mb-2 text-sm font-medium text-gray-900">{{ __('Tipo') }}</label>
                                <select id="filter_type" name="type" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                    <option value="">{{ __('Todos') }}</option>
                                    <option value="income" @selected(isset($filters['type']) && $filters['type'] === 'income')>{{ __('Receita') }}</option>
                                    <option value="expense" @selected(isset($filters['type']) && $filters['type'] === 'expense')>{{ __('Despesa') }}</option>
                                </select>
                            </div>

                            <!-- Filtro por Categoria -->
                            <div class="flex-grow">
                                <label for="filter_category" class="block mb-2 text-sm font-medium text-gray-900">{{ __('Categoria') }}</label>
                                <select id="filter_category" name="category_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                    <option value="">{{ __('Todas') }}</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" @selected(isset($filters['category_id']) && $filters['category_id'] == $category->id)>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filtro por Descrição -->
                            <div class="flex-grow">
                                <label for="filter_description" class="block mb-2 text-sm font-medium text-gray-900">{{ __('Descrição') }}</label>
                                <input type="text" id="filter_description" name="description" value="{{ $filters['description'] ?? '' }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" />
                            </div>

                            <!-- Filtro por Data Inicial -->
                            <div class="flex-grow">
                                <label for="filter_start_date" class="block mb-2 text-sm font-medium text-gray-900">{{ __('Data Inicial') }}</label>
                                <input type="date" id="filter_start_date" name="start_date" value="{{ $filters['start_date'] ?? '' }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" />
                            </div>

                            <!-- Filtro por Data Final -->
                            <div class="flex-grow">
                                <label for="filter_end_date" class="block mb-2 text-sm font-medium text-gray-900">{{ __('Data Final') }}</label>
                                <input type="date" id="filter_end_date" name="end_date" value="{{ $filters['end_date'] ?? '' }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" />
                            </div>

                            {{-- Botões de Ação do Filtro - Alinhados na mesma linha --}}
                            <div class="flex gap-2 mt-auto"> {{-- mt-auto alinha os botões com a base dos inputs --}}
                                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none">
                                    {{ __('Aplicar Filtros') }}
                                </button>
                                <a href="{{ route('transactions.index') }}" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5">
                                    {{ __('Limpar Filtros') }}
                                </a>
                            </div>
                        </div>
                    </form>
                    {{-- Fim do Formulário de Filtro --}}

                    {{-- Bloco de Resumo de Saldo - MAIS BONITO --}}
                    <div class="mt-6 p-5 bg-white border-l-4 border-blue-500 rounded-lg shadow-md mb-6"> {{-- p-5 para mais espaço, border-l-4 para destaque, shadow-md --}}
                        <p class="text-lg font-bold mb-2 text-gray-800"> {{-- text-gray-800 para contraste --}}
                            {{ $areFiltersActive ? 'Resumo do Período Selecionado:' : 'Saldo Total:' }}
                        </p>
                        <p class="text-green-600 text-base"> {{-- text-base para padronizar --}}
                            Total de Receitas: <span class="font-semibold">R$ {{ number_format($totalIncome, 2, ',', '.') }}</span>
                        </p>
                        <p class="text-red-600 text-base"> {{-- text-base para padronizar --}}
                            Total de Despesas: <span class="font-semibold">R$ {{ number_format($totalExpense, 2, ',', '.') }}</span>
                        </p>
                        <p class="text-xl font-extrabold mt-3 @if($balance < 0) text-red-700 @else text-green-700 @endif"> {{-- text-xl e font-extrabold para destaque --}}
                            Saldo: R$ {{ number_format($balance, 2, ',', '.') }}
                        </p>
                    </div>
                    {{-- Fim do Bloco de Resumo de Saldo --}}
                    <div class="flex items-center justify-between mb-4">
                        <a href="{{ route('transactions.create') }}" class="focus:outline-none text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2">
                            {{ __('Adicionar Nova Transação') }}
                        </a>

                        {{-- Botão de Exportar - AGORA COM LETRAS VERDES --}}
                        <a href="{{ route('transactions.export', request()->query()) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-green-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                            {{ __('Exportar (Excel)') }}
                        </a>
                    </div>
                    @if ($transactions->isEmpty())
                        <p class="text-center text-gray-600">Você ainda não tem transações registradas.</p>
                    @else
                        {{-- Tabela de Transações --}}
                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">
                                            Data
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Descrição
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Valor
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Tipo
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Categoria
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Ações
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transactions as $transaction)
                                        <tr class="bg-white border-b hover:bg-gray-50">
                                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                                {{ $transaction->transaction_date->format('d/m/Y') }}
                                            </td>
                                            <td class="px-6 py-4">
                                                {{ $transaction->description }}
                                            </td>
                                            <td class="px-6 py-4 @if($transaction->type === 'expense') text-red-600 @else text-green-600 @endif">
                                                {{ $transaction->type === 'expense' ? '-' : '' }}R$ {{ number_format($transaction->amount, 2, ',', '.') }}
                                            </td>
                                            <td class="px-6 py-4">
                                                {{ $transaction->type === 'income' ? 'Receita' : 'Despesa' }}
                                            </td>
                                            <td class="px-6 py-4">
                                                {{ $transaction->category->name ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <a href="{{ route('transactions.edit', $transaction) }}" class="font-medium text-blue-600 hover:underline">Editar</a>

                                                <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" class="inline ml-4">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="font-medium text-red-600 hover:underline" onclick="return confirm('Tem certeza que deseja excluir esta transação?');">Excluir</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>