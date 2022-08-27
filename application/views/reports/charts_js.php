<script type="text/javascript">
    var Dashboard = function () {
        var self = this;
        // Stores an array of all the Data for viewing in the Dashboard
        self.dashboardData = ko.observable({"figures": {"no_members": "0", "portfolio": "0"}, "percents": {"members_percent": 10}, "tables": {"income": [], "expenses": []}});
        self.startDate = ko.observable(startDate);
        self.endDate = ko.observable(endDate);

        // Operations
        self.updateData = function () {
            $.ajax({
                type: "post",
                dataType: "json",
                data: {start_date: self.startDate, end_date: self.endDate, origin: "dashboard"},
                url: "ajax_requests/ajax_data.php",
                success: function (response) {
                    // Now use this data to update the view models, 
                    // and Knockout will update the UI automatically 
                    self.dashboardData(response);

                    if (response.graph_data) {
                        draw_pie_chart(response.pie_chart_data);
                        draw_line_highchart(response.graph_data);//
                    }

                }
            })
        };
    };

    var dashModel = new Dashboard();
    dashModel.updateData();
    ko.applyBindings(dashModel);

    function handleDateRangePicker(start_date, end_date) {
        dashModel.startDate(start_date);
        dashModel.endDate(end_date);
        dashModel.updateData();
    }
// Pie chart
    function draw_pie_chart(url_data) {
        // Build the chart
        Highcharts.chart('pieChart', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: url_data.title,
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: false
                    },
                    showInLegend: true
                }
            },
            series: [{
                    name: url_data.series.name,
                    colorByPoint: true,
                    data: url_data.series.data
                }]
        });
    }
//Bar chart
    function draw_bar_chart(url_data) {
        $("#barChart").replaceWith('<canvas id="barChart"></canvas>');
        var ctx = $("#barChart").get(0).getContext("2d");

        var barChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: url_data.labels,
                datasets: url_data.datasets
            },
            options: {
                scales: {
                    yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                }
            }
        });
    }
// Line chart
    function draw_line_chart(url_data) {
        $("#lineChart").replaceWith('<canvas id="lineChart"></canvas>');
        var ctx = $("#lineChart").get(0).getContext("2d");

        var lineOptions = {
            scaleShowGridLines: true,
            scaleGridLineColor: "rgba(0,0,0,.05)",
            scaleGridLineWidth: 1,
            bezierCurve: true,
            bezierCurveTension: 0.4,
            pointDot: true,
            pointDotRadius: 4,
            pointDotStrokeWidth: 1,
            pointHitDetectionRadius: 20,
            datasetStroke: true,
            datasetStrokeWidth: 2,
            datasetFill: true,
            responsive: true,
        };
        var lineChart = new Chart(ctx, {
            type: 'line',
            data: url_data,
            options: lineOptions
        });
    }
    function draw_line_highchart(url_data) {
        Highcharts.chart('lineChart', {

            title: url_data.title,

            yAxis: url_data.yAxis,
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle'
            },
            xAxis: {categories: url_data.xAxis.categories},
            plotOptions: {
                line: {
                    dataLabels: {enabled: true}
                }
            },
            series: url_data.datasets
        });
    }
</script>