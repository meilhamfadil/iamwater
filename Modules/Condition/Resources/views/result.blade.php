@extends('public')

@section('content')
    <section class="content p-2">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <canvas id="myGraphChart"></canvas>
                    <p class="text-center">1: Sangat Buruk | 2. Buruk | 3. Sedang | 4. Baik | 5. Sangat Baik</p>
                    <br />
                    <canvas id="myLineChart"></canvas>
                </div>
            </div>
        </div>
        </div>
    @endsection

    @section('js')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js">
        </script>

        <script>
            const data = {
                datasets: [{
                    label: "{{ $type }}",
                    data: {!! json_encode($graph) !!},
                    borderColor: 'rgb(190, 66, 69)',
                    borderWidth: 1,
                    backgroundColor: 'rgba(255, 99, 132, 0.4)',
                    fill: false,
                }, ]
            };

            // Configuration options
            const config = {
                type: 'line',
                data: data,
                options: {
                    scales: {
                        x: {
                            type: 'time',
                            time: {
                                unit: 'day'
                            },
                            title: {
                                display: true,
                                text: 'Hours'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Value'
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

            // GRAPH

            const dataGraph = {
                datasets: [{
                    label: "{{ $typeGraph }}",
                    data: {!! json_encode($categories) !!},
                    borderColor: 'rgb(23, 49, 160)',
                    borderWidth: 1,
                    backgroundColor: 'rgba(23, 49, 160, 0.4)',
                    fill: false,
                }, ]
            };

            // Configuration options
            const configGraph = {
                type: 'line',
                data: dataGraph,
                options: {
                    scales: {
                        x: {
                            type: 'time',
                            time: {
                                unit: 'day'
                            },
                            title: {
                                display: true,
                                text: 'Hours'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Value'
                            }
                        }
                    }
                }
            };

            // Initialize the chart
            var myChartGraph = new Chart(
                document.getElementById('myGraphChart'),
                configGraph
            );
        </script>
    @endsection
