@extends('public')

@section('content')
    <section class="content p-2">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <canvas id="myLineChart"></canvas>
                </div>
            </div>
        </div>
        </div>
    @endsection

    @section('js')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const data = {
                datasets: [
                    ...{{ json_encode($area) }}.map((areaData, index) => ({
                        label: 'A' + (index + 1),
                        data: areaData,
                        borderColor: 'rgb(190, 66, 69)',
                        borderWidth: 1,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.4)',
                            'rgba(54, 162, 235, 0.4)',
                            'rgba(255, 159, 64, 0.4)',
                            'rgba(153, 102, 255, 0.4)',
                            'rgba(75, 192, 192, 0.4)',
                            'rgba(201, 203, 207, 0.4)',
                            'rgba(255, 205, 86, 0.4)',
                        ],
                        fill: true,
                    })),
                    {
                        data: {{ json_encode($all) }},
                        label: "none",
                        backgroundColor: 'rgba(201, 203, 207, 0.4)',
                        fill: true,
                    },
                    ...{{ json_encode($rules) }}.map((rule, index) => ({
                        label: "none",
                        data: rule,
                        borderColor: 'rgb(75, 192, 192)',
                        borderWidth: 0.5,
                    })),
                ]
            };

            // Configuration options
            const config = {
                type: 'line',
                data: data,
                options: {
                    scales: {
                        x: {
                            type: 'linear',
                            position: 'bottom'
                        },
                        y: {
                            min: 0,
                            max: 1,
                        },
                    },
                    plugins: {
                        legend: {
                            labels: {
                                filter: item => item.text !== 'none'
                            }
                        }
                    }
                }
            };

            // Initialize the chart
            var myChart = new Chart(
                document.getElementById('myLineChart'),
                config
            );
        </script>
    @endsection
