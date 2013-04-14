var column_grafic = {
	chart: {
		type: 'column'
	},
	title: {
		text: ''
	},
	subtitle: {
		text: ''
	},
	xAxis: {
		categories: []
	},
	yAxis: {
		min: 0,
		title: {
			text: ''
		}
	},
	tooltip: {
		headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
		pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td><td style="padding:0"><b>{point.y:.1f}/5</b></td></tr>',
		footerFormat: '</table>',
		shared: true,
		useHTML: true
	},
	plotOptions: {
		column: {
			pointPadding: 0.2,
			borderWidth: 0
		}
	},
	series: []
};