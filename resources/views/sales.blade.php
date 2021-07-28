@extends('layouts.app')

@push('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ 'Sales' }}</div>

                    <div class="card-body">
                        @livewire('sales')

                        <div id="daterange" class="col-md-4 form-control" style="cursor: pointer; margin-left: 15px; width: auto;">
                            <i class="fa fa-calendar"></i>
                            {{-- @todo: Reik atnaujint čia datą su tokia kokia įloadini pirmus grafikus --}}
                            <span></span> <b class="caret"></b>
                        </div>

                        <ul class="nav nav-tabs mt-3" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" href="#revenueTab" role="tab" data-toggle="tab">Revenue</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#salesTab" role="tab" data-toggle="tab">Items sold</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#ordersChartTab" role="tab" data-toggle="tab">Orders</a>
                            </li>
                        </ul>


                        <div class="container">
                            <!-- Tab panes -->
                                <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane active" id="revenueTab">
                                        <div class="row">
                                            <div class="col-12">
                                                <div id="RevenueChart" style="height: 600px" ></div>
                                            </div>
                                            <div class="col-12">
                                                <div id="RevenueChartTotals" style="height: 600px" ></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div role="tabpanel" class="tab-pane fade" id="salesTab">
                                        <div class="row">
                                            <div class="col-12">
                                                <div id="SalesChart" style="height: 600px" ></div>
                                            </div>
                                            <div class="col-12">
                                                <div id="SalesChartTotals" style="height: 600px" ></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div role="tabpanel" class="tab-pane fade" id="ordersChartTab">
                                        <div class="row">
                                            <div class="col-12">
                                                <div id="OrdersChart" style="height: 600px"></div>
                                            </div>
                                            <div class="col-12">
                                                <div id="OrdersChartTotals" style="height: 600px" ></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
@endsection

@push('js')
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <!-- Charting library -->
    <script src="https://unpkg.com/chart.js@2.9.3/dist/Chart.min.js"></script>
    <!-- Chartisan -->
    <script src="https://unpkg.com/@chartisan/chartjs@^2.1.0/dist/chartisan_chartjs.umd.js"></script>
    <!-- Your application script -->
    <script>

        const salesChart = new Chartisan({
            el: '#SalesChart',
            hooks: new ChartisanHooks()
                .datasets([{ type: 'line', fill: false }])
                .title('Items')
                .borderColors(['#FFDD00', '#CCCCCC'])
                .colors(['#FFDD00', '#CCCCCC']),
        });
        salesChart.update({ background: true })

        const salesChartTotals = new Chartisan({
            el: '#SalesChartTotals',
            hooks: new ChartisanHooks()
                .datasets([{ type: 'bar'}])
                .title('Items Totals')
                .borderColors(['#FFDD00', '#CCCCCC'])
                .colors(['#FFDD00', '#CCCCCC']),
        });
        salesChartTotals.update({ background: true })

        const revenueChart = new Chartisan({
            el: '#RevenueChart',
            hooks: new ChartisanHooks()
                .datasets([{ type: 'line', fill: false}])
                .title('Revenue')
                .tooltip({
                    enabled: true,
                    mode: 'single',
                    callbacks: {
                        label: function(tooltipItems) {
                            return tooltipItems.yLabel + '€';
                        }
                    }
                })
                .custom(function({ data, merge, server }) {
                    // data ->   Contains the current chart configuration
                    //           data that will be passed to the chart instance.
                    // merge ->  Contains a function that can be called to merge
                    //           two javascript objects and returns its merge.
                    // server -> Contains the server information in case you need
                    //           to acces the raw information provided by the server.
                    //           This is mostly used to access the `extra` field.

                    return merge(data, {
                        options: {
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        // Include a dollar sign in the ticks
                                        callback: function(value, index, values) {
                                            return value + '€';
                                        }
                                    }
                                }]
                            }
                        }
                    });

                    // The function must always return the new chart configuration.
                })
                .borderColors(['#FFDD00', '#CCCCCC'])
                .colors(['#FFDD00', '#CCCCCC'])
        });
        revenueChart.update({ background: true })

        const revenueChartTotals = new Chartisan({
            el: '#RevenueChartTotals',
            hooks: new ChartisanHooks()
                .datasets([{ type: 'bar'}])
                .title('Revenue Totals')
                .tooltip({
                    enabled: true,
                    mode: 'single',
                    callbacks: {
                        label: function(tooltipItems) {
                            return tooltipItems.yLabel + '€';
                        }
                    }
                })
                .custom(function({ data, merge, server }) {
                    // data ->   Contains the current chart configuration
                    //           data that will be passed to the chart instance.
                    // merge ->  Contains a function that can be called to merge
                    //           two javascript objects and returns its merge.
                    // server -> Contains the server information in case you need
                    //           to acces the raw information provided by the server.
                    //           This is mostly used to access the `extra` field.

                    return merge(data, {
                        options: {
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        // Include a dollar sign in the ticks
                                        callback: function(value, index, values) {
                                            return value + '€';
                                        }
                                    }
                                }]
                            }
                        }
                    });

                    // The function must always return the new chart configuration.
                })
                .borderColors(['#FFDD00', '#CCCCCC'])
                .colors(['#FFDD00', '#CCCCCC'])
        });
        revenueChartTotals.update({ background: true })

        const ordersChart = new Chartisan({
            el: '#OrdersChart',
            hooks: new ChartisanHooks()
                .datasets([{ type: 'line', fill: false}])
                .colors(['#FFDD00', '#CCCCCC'])
                .borderColors(['#FFDD00', '#CCCCCC'])
                .title('Orders'),
        });
        ordersChart.update({ background: true })

        const ordersChartTotals = new Chartisan({
            el: '#OrdersChartTotals',
            hooks: new ChartisanHooks()
                .datasets('bar')
                .colors(['#FFDD00', '#CCCCCC'])
                .title('Orders Totals'),
        });
        ordersChartTotals.update({ background: true })

    </script>

    <script type="text/javascript">
        $('#daterange').daterangepicker({
            locale: {
                format: 'DD/MM/YYYY'
            },
            startDate: startDate = moment().subtract(1, 'days'),
            endDate: endDate = moment(),
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'Last Year': [moment().subtract(1, 'year'), moment()]
            }
        }, setDates);

        function setDates(start, end) {
            $('#daterange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));

            salesChart.update({'url' : "@chart('sales_chart')" + "?from=" + start.format('YYYY-MM-DD') + '&till=' + end.format('YYYY-MM-DD')});
            salesChartTotals.update({'url' : "@chart('sales_chart_totals')" + "?from=" + start.format('YYYY-MM-DD') + '&till=' + end.format('YYYY-MM-DD')});
            revenueChart.update({'url' : "@chart('revenue_chart')" + "?from=" + start.format('YYYY-MM-DD') + '&till=' + end.format('YYYY-MM-DD')});
            revenueChartTotals.update({'url' : "@chart('revenue_chart_totals')" + "?from=" + start.format('YYYY-MM-DD') + '&till=' + end.format('YYYY-MM-DD')});
            ordersChart.update({'url' : "@chart('orders_chart')" + "?from=" + start.format('YYYY-MM-DD') + '&till=' + end.format('YYYY-MM-DD')});
            ordersChartTotals.update({'url' : "@chart('orders_chart_totals')" + "?from=" + start.format('YYYY-MM-DD') + '&till=' + end.format('YYYY-MM-DD')});
        }

        setDates(moment().subtract(30, 'days'), moment());

    </script>
@endpush
