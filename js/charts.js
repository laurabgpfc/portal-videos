
function loadCharts() {
	if ($('#totalVisualizaciones').length > 0) {
		totalVisualizacionesChart();
	}

	if ($('#totalDescargas').length > 0) {
		totalDescargasChart();
	}

	if ($('#videosMasVistos').length > 0) {
		videosMasVistosChart();
	}

	if ($('#videosMasDescargados').length > 0) {
		videosMasDescargadosChart();
	}

	if ($('#adjuntosMasDescargados').length > 0) {
		adjuntosMasDescargadosChart();
	}
}

function totalVisualizacionesChart() {
	// Build the chart
	$.ajax({
		type: 'POST',
		url: 'modules-admin/charts.php',
		data: 'chartName=totalVisualizaciones',
		success: function(infoChart) {
			infoChart = window.JSON.parse(infoChart);
			
			$('#totalVisualizaciones').highcharts({
				chart: {
					type: 'pie',
					plotBackgroundColor: null,
					plotBorderWidth: null,
					plotShadow: false
				},
				title: {
					text: ''
				},
				tooltip: {
					pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
				},
				plotOptions: {
					pie: {
						allowPointSelect: true,
						cursor: 'pointer',
						dataLabels: {
							enabled: true,
							format: '<b>{point.name}</b>: {point.percentage:.1f} %',
							style: {
								color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
							},
							connectorColor: 'silver'
						},
						showInLegend: true
					}
				},
				series: [{
					name: 'Total reproducciones',
					data: infoChart
				}]
			});
		}
	});
}

function videosMasVistosChart() {
	// Build the chart
	$.ajax({
		type: 'POST',
		url: 'modules-admin/charts.php',
		data: 'chartName=videosMasVistos',
		success: function(infoChart) {
			infoChart = window.JSON.parse(infoChart);
			
			console.log(infoChart.categories);
			$('#videosMasVistos').highcharts({
				chart: {
					type: 'bar'
				},
				title: {
					text: ''
				},
				xAxis: {
					categories: infoChart.categories,
					title: {
						text: null
					}
				},
				yAxis: {
					min: 0,
					title: {
						text: '',
						align: 'high'
					},
					labels: {
						overflow: 'justify'
					}
				},
				tooltip: {
					valueSuffix: ' veces'
				},
				plotOptions: {
					bar: {
						dataLabels: {
							enabled: true
						}
					}
				},
				series: infoChart.info
			});
		}
	});
}

function totalDescargasChart() {
	// Build the chart
	$.ajax({
		type: 'POST',
		url: 'modules-admin/charts.php',
		data: 'chartName=totalDescargas',
		success: function(infoChart) {
			infoChart = window.JSON.parse(infoChart);
			
			$('#totalDescargas').highcharts({
				chart: {
					type: 'pie',
					plotBackgroundColor: null,
					plotBorderWidth: null,
					plotShadow: false
				},
				title: {
					text: ''
				},
				tooltip: {
					pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
				},
				plotOptions: {
					pie: {
						cursor: 'pointer',
						dataLabels: {
							enabled: true,
							format: '<b>{point.name}</b>: {point.percentage:.1f} %',
							style: {
								color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
							},
							connectorColor: 'silver'
						},
						showInLegend: true
					}
				},
				series: [{
					name: 'Total reproducciones',
					data: infoChart.series
				}],
				drilldown: infoChart.drilldown
			});
		}
	});
}

function videosMasDescargadosChart() {
	// Build the chart
	$.ajax({
		type: 'POST',
		url: 'modules-admin/charts.php',
		data: 'chartName=videosMasDescargados',
		success: function(infoChart) {
			infoChart = window.JSON.parse(infoChart);
			
			console.log(infoChart.categories);
			$('#videosMasDescargados').highcharts({
				chart: {
					type: 'bar'
				},
				title: {
					text: ''
				},
				xAxis: {
					categories: infoChart.categories,
					title: {
						text: null
					}
				},
				yAxis: {
					min: 0,
					title: {
						text: '',
						align: 'high'
					},
					labels: {
						overflow: 'justify'
					}
				},
				tooltip: {
					valueSuffix: ' veces'
				},
				plotOptions: {
					bar: {
						dataLabels: {
							enabled: true
						}
					}
				},
				series: infoChart.info
			});
		}
	});
}


function adjuntosMasDescargadosChart() {
	// Build the chart
	$.ajax({
		type: 'POST',
		url: 'modules-admin/charts.php',
		data: 'chartName=adjuntosMasDescargados',
		success: function(infoChart) {
			infoChart = window.JSON.parse(infoChart);
			
			console.log(infoChart.categories);
			$('#adjuntosMasDescargados').highcharts({
				chart: {
					type: 'bar'
				},
				title: {
					text: ''
				},
				xAxis: {
					categories: infoChart.categories,
					title: {
						text: null
					}
				},
				yAxis: {
					min: 0,
					title: {
						text: '',
						align: 'high'
					},
					labels: {
						overflow: 'justify'
					}
				},
				tooltip: {
					valueSuffix: ' veces'
				},
				plotOptions: {
					bar: {
						dataLabels: {
							enabled: true
						}
					}
				},
				series: infoChart.info
			});
		}
	});
}