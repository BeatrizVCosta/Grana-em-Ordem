<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- Formulário de Filtro de Tempo --}}
                    <form method="GET" action="{{ route('dashboard') }}" class="mb-6 p-4 border rounded-lg bg-gray-50">
                        {{-- A div abaixo agora usa flex, flex-wrap e items-end para alinhar em uma linha --}}
                        <div class="flex flex-wrap items-end gap-4">
                            <div>
                                <x-input-label for="month_filter" :value="__('Mês')" />
                                <select id="month_filter" name="month" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    @foreach ($months as $num => $name)
                                        <option value="{{ $num }}" @selected($selectedMonth == $num)>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="year_filter" :value="__('Ano')" />
                                <select id="year_filter" name="year" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    @foreach ($years as $year)
                                        <option value="{{ $year }}" @selected($selectedYear == $year)>{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- Os botões não precisam mais de mt-6 md:mt-0, pois items-end já os alinha --}}
                            <div class="flex gap-2">
                                <x-primary-button>
                                    {{ __('Filtrar') }}
                                </x-primary-button>
                                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                    {{ __('Mês Atual') }}
                                </a>
                            </div>
                        </div>
                    </form>
                    {{-- Fim do Formulário de Filtro de Tempo --}}

                    <h3 class="text-2xl font-bold mb-6 text-center">
                        {{ __('Resumo Financeiro de ') . $monthName . ' de ' . $selectedYear }}
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 text-center">
                        <div class="bg-blue-100 p-4 rounded-lg shadow-md">
                            <p class="text-lg font-semibold text-blue-800">{{ __('Receitas do Mês') }}</p>
                            <p class="text-3xl font-bold text-blue-600">R$ {{ number_format($totalIncome, 2, ',', '.') }}</p>
                        </div>
                        <div class="bg-red-100 p-4 rounded-lg shadow-md">
                            <p class="text-lg font-semibold text-red-800">{{ __('Despesas do Mês') }}</p>
                            <p class="text-3xl font-bold text-red-600">R$ {{ number_format($totalExpense, 2, ',', '.') }}</p>
                        </div>
                        <div class="p-4 rounded-lg shadow-md @if($balance < 0) bg-red-200 text-red-900 @else bg-green-200 text-green-900 @endif">
                            <p class="text-lg font-semibold">{{ __('Saldo do Mês') }}</p>
                            <p class="text-3xl font-bold">R$ {{ number_format($balance, 2, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row items-center justify-center gap-8 mb-8">
                        <a href="{{ route('transactions.create') }}" class="w-full md:w-auto inline-flex items-center justify-center px-6 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-white text-lg uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg">
                            {{ __('+ Adicionar Transação') }}
                        </a>
                        <a href="{{ route('transactions.index') }}" class="w-full md:w-auto inline-flex items-center justify-center px-6 py-3 bg-gray-200 border border-gray-300 rounded-md font-semibold text-gray-800 text-lg uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg">
                            {{ __('Ver Todas as Transações') }}
                        </a>
                    </div>

                    <hr class="my-8 border-gray-200">

                    <h3 class="text-2xl font-bold mb-6 text-center">{{ __('Gastos por Categoria (Despesas de ') . $monthName . ' de ' . $selectedYear . ')' }}</h3>

                    @if ($totalExpensesForChart > 0 && count($chartData) > 0)
                        <div class="flex flex-col md:flex-row items-center justify-center gap-8">
                            {{-- Gráfico de Pizza SVG --}}
                            <div class="relative w-64 h-64 flex-shrink-0">
                                <svg viewBox="0 0 100 100" class="w-full h-full">
                                    @php
                                        $currentAngle = 0;
                                    @endphp
                                    @foreach ($chartData as $data)
                                        @php
                                            $angle = ($data['percentage'] / 100) * 360;
                                            $largeArcFlag = $angle > 180 ? 1 : 0;

                                            $x1 = 50 + 50 * cos(deg2rad($currentAngle));
                                            $y1 = 50 + 50 * sin(deg2rad($currentAngle));

                                            $x2 = 50 + 50 * cos(deg2rad($currentAngle + $angle));
                                            $y2 = 50 + 50 * sin(deg2rad($currentAngle + $angle));

                                            $pathData = "M 50 50 L $x1 $y1 A 50 50 0 $largeArcFlag 1 $x2 $y2 Z";
                                        @endphp
                                        <path d="{{ $pathData }}" fill="{{ $data['color'] }}"></path>
                                        @php
                                            $currentAngle += $angle;
                                        @endphp
                                    @endforeach
                                </svg>
                            </div>

                            {{-- Legenda do Gráfico --}}
                            <div class="flex-grow">
                                <ul class="space-y-2">
                                    @foreach ($chartData as $data)
                                        <li class="flex items-center">
                                            <span class="inline-block w-4 h-4 rounded-full mr-2" style="background-color: {{ $data['color'] }};"></span>
                                            <span class="text-gray-800">{{ $data['category'] }} (R$ {{ number_format($data['amount'], 2, ',', '.') }}) - {{ $data['percentage'] }}%</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @else
                        <p class="text-center text-gray-600">{{ __('Não há despesas registradas para o período selecionado para exibir no gráfico.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>