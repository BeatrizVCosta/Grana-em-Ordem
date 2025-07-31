<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Relatórios Financeiros') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-2xl font-bold mb-6 text-center">{{ __('Evolução Mensal (Últimos 12 Meses)') }}</h3>
                    <div class="mb-8">
                        <canvas id="monthlyChart"></canvas>
                    </div>

                    <hr class="my-8 border-gray-200">

                    <h3 class="text-2xl font-bold mb-6 text-center">{{ __('Despesas por Categoria (Ano de ') . $currentYear . ')' }}</h3>
                    <div class="flex justify-center mb-8">
                        <div class="relative w-full md:w-1/2 lg:w-1/3"> {{-- Ajusta a largura do container do gráfico --}}
                            <canvas id="pieChart"></canvas>
                        </div>
                    </div>

                    {{-- Script para Chart.js --}}
                    @push('scripts')
                    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> {{-- Carrega Chart.js --}}
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            // Dados do controlador para o gráfico mensal
                            const monthlyChartData = @json($monthlyChartData);
                            const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
                            new Chart(monthlyCtx, {
                                type: 'bar', // Pode ser 'line' também
                                data: {
                                    labels: monthlyChartData.labels,
                                    datasets: [
                                        {
                                            label: 'Receitas',
                                            data: monthlyChartData.incomes,
                                            backgroundColor: 'rgba(54, 162, 235, 0.6)', // Azul
                                            borderColor: 'rgba(54, 162, 235, 1)',
                                            borderWidth: 1
                                        },
                                        {
                                            label: 'Despesas',
                                            data: monthlyChartData.expenses,
                                            backgroundColor: 'rgba(255, 99, 132, 0.6)', // Vermelho
                                            borderColor: 'rgba(255, 99, 132, 1)',
                                            borderWidth: 1
                                        }
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false, // Permite que o gráfico se ajuste ao container
                                    scales: {
                                        y: {
                                            beginAtZero: true
                                        }
                                    }
                                }
                            });

                            // Dados do controlador para o gráfico de pizza
                            const pieChartData = @json($pieChartData);
                            const pieCtx = document.getElementById('pieChart').getContext('2d');
                            new Chart(pieCtx, {
                                type: 'pie',
                                data: {
                                    labels: pieChartData.labels,
                                    datasets: [{
                                        data: pieChartData.amounts,
                                        backgroundColor: pieChartData.colors,
                                        hoverOffset: 4
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false, // Permite que o gráfico se ajuste ao container
                                }
                            });
                        });
                    </script>
                    @endpush
                </div>
            </div>
        </div>
    </div>
</x-app-layout>