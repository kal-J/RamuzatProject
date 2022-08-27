function draw_pie_chart(chart_data, chart_id,chart_title) {
        // Build the chart
        Highcharts.chart(chart_id, {
            chart: {
                type: 'pie',
                options3d: {
                    enabled: true,
                    alpha: 40,
                    beta: 0
                }
            },
            title: {
                 text: chart_title
            },
            tooltip: {
                 pointFormat: 'Amount: <b>{point.y:.1f}</b><br/>{series.name} <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    depth: 35,
                    dataLabels: {
                        enabled: true,
                        format: '{point.name}'
                    }
                }
            },
           
                series: [{
                type: 'pie',
                name: '%age',
                data: [
                    {
                        name:[chart_data.slice1.labels],
                        y: parseFloat(chart_data.slice1.amount),
                        sliced: true,
                        selected: true
                    },
                    [chart_data.slice2.labels,parseFloat(chart_data.slice2.amount)]
                ]
            }]
        });
    }
function draw_line_chart(chart_id,chart_data){
      
    Highcharts.chart(chart_id, {
            chart: {
                type: 'spline'
            },
            title: chart_data.title,
            xAxis: chart_data.xAxis,
            yAxis: {
                title: {
                    text: 'Uganda Shillings'
                },
                labels: {
                    formatter: function () {
                        return 'UGX '+ this.value;
                    }
                }
            },
            tooltip: {
                crosshairs: true,
                shared: true
            },
            plotOptions: {
                spline: {
                    marker: {
                        radius: 4,
                        lineColor: '#666666',
                        lineWidth: 1
                    }
                }
            },
            series: chart_data.datasets
        });
  }
  function draw_bar_chart(chart_id,chart_data){
      
      Highcharts.chart(chart_id, {
              chart: {
                  type: 'column'
              },
              title: chart_data.title,
              xAxis: chart_data.xAxis,
              yAxis: {
                  title: {
                      text: 'Uganda Shillings'
                  },
                  labels: {
                      formatter: function () {
                          return 'UGX '+ this.value;
                      }
                  }
              },
              tooltip: {
                  crosshairs: true,
                  shared: true
              },
              plotOptions: {
                  spline: {
                      marker: {
                          radius: 4,
                          lineColor: '#666666',
                          lineWidth: 1
                      }
                  }
              },
              series: chart_data.datasets
          });
    }

function draw_basic_line_graph(chart_id,chart_data){
    Highcharts.chart(chart_id, {

    title: chart_data.title,
    xAxis: chart_data.xAxis,
    yAxis: {
                  title: {
                      text: 'Uganda Shillings'
                  },
                  labels: {
                      formatter: function () {
                          return 'UGX '+ this.value;
                      }
                  }
              },
    legend: {
        layout: 'vertical',
        align: 'right',
        verticalAlign: 'middle'
    },
    series: chart_data.datasets


    });

}
function draw_basic_bar_graph(chart_id,chart_title,tooltip,chart_data){
    Highcharts.chart(chart_id, {
    chart: {
        type: 'column'
    },
    title: {
        text: chart_title
    },
   
    xAxis: {
        type: 'category',
        labels: {
            rotation: -45,
            style: {
                fontSize: '13px',
                fontFamily: 'Verdana, sans-serif'
            }
        }
    },
    yAxis: {
        min: 0,
        title: {
            text: 'Uganda Shillings'
        }
    },
    legend: {
        enabled: false
    },
    tooltip: {
        pointFormat: tooltip
    },
    series: [{
        name: '',
        data: [
            [chart_data.range1.name, parseFloat(chart_data.range1.amount)],
            [chart_data.range2.name, parseFloat(chart_data.range2.amount)],
            [chart_data.range3.name, parseFloat(chart_data.range3.amount)],
            [chart_data.range4.name, parseFloat(chart_data.range4.amount)]
        ],
        dataLabels: {
            enabled: true,
            rotation: -45,
            color: '#FFFFFF',
            align: 'right',
            format: '{point.y:.1f}', // one decimal
            y: 10, // 10 pixels down from the top
            style: {
                fontSize: '13px',
                fontFamily: 'Verdana, sans-serif'
            }
        }
    }]
});
}