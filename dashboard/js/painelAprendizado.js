var datasets = undefined;

$(document).ready(function(){
	var optionPie = {
		series: {
			pie: {
				show: true,
				radius: 1,
				label: {
					show: true,
					radius: 1/2,
					formatter: labelFormatter,
					background: {
						opacity: 1
					}
				}
			}
		},
		legend: {
			show: false
		}
	};
	
	var optionBarP = {
		series: {
			stack: false,
			shadowSize: false,
			bars: {
				show: true,
				barWidth: 0.95,
				align: "left",
				zero: true,
				horizontal: false,
				fillColor: {
					colors:	[
						{opacity: 0.9},
						{opacity: 0.75}
					]
				},
				fill: true,
				lineWidth: 2
			}
		},
		legend: {
			show: true,
			labelFormatter: function(v, axis) {
				return "&nbsp;" + v + "%&nbsp;";
			},
			noColumns: 12,
			backgroundOpacity: 1
		},
		xaxis: {
			show: true,
			axisLabel: "CLASSES REGULARES E AVANÇADAS",
			axisLabelUseCanvas: true,
			axisLabelFontSizePixels: 10,
			axisLabelFontFamily: 'Verdana, Arial',
			axisLabelPadding: 10,
			tickFormatter: function(v, axis) {
				return "";
			}
		},
		yaxis: {
			show: true,
			axisLabel: "ITENS ASSINADOS",
			axisLabelUseCanvas: true,
			axisLabelFontSizePixels: 10,
			axisLabelFontFamily: 'Verdana, Arial',
			min: 0,
			max: 100,
			tickFormatter: function(v, axis) {
				return v + "%";
			}
		}
	};

	var optionBarC = {
		series: {
			stack: false,
			shadowSize: false,
			bars: {
				show: true,
				barWidth: 0.95,
				align: "left",
				zero: true,
				horizontal: false,
				fillColor: {
					colors:	[
						{opacity: 0.9},
						{opacity: 0.75}
					]
				},
				fill: true,
				lineWidth: 2
			}
		},
		legend: {
			show: true,
			labelFormatter: function(v, axis) {
				return "&nbsp;" + v + "&nbsp;";
			},
			noColumns: 12,
			backgroundOpacity: 1
		},
		xaxis: {
			show: true,
			axisLabel: "CLASSES REGULARES E AVANÇADAS",
			axisLabelUseCanvas: true,
			axisLabelFontSizePixels: 10,
			axisLabelFontFamily: 'Verdana, Arial',
			axisLabelPadding: 10,
			tickFormatter: function(v, axis) {
				return "";
			}
		},
		yaxis: {
			show: true,
			axisLabel: "QUANTIDADE CONCLUÍDA",
			axisLabelUseCanvas: true,
			axisLabelFontSizePixels: 10,
			axisLabelFontFamily: 'Verdana, Arial',
			min: 0,
			tickFormatter: function(v, axis) {
				return v;
			}
		}
	};	
	
	datasets = jsLIB.ajaxCall( undefined, jsLIB.rootDir+"rules/painelAprendizado.php", { MethodName : 'getGraphData' }, 'RETURN' );
	if (datasets.clsP){
		$.plot("#phGhaphP", datasets.clsP, optionBarP );
	}
	if (datasets.rgP){
		$.plot('#phRegularP', datasets.rgP, optionPie );
	}
	if (datasets.avP){
		$.plot('#phAvancadaP', datasets.avP, optionPie );
	}
	if (datasets.clsC){
		$.plot("#phGhaphC", datasets.clsC, optionBarC );
	}
	if (datasets.rgC){
		$.plot('#phRegularC', datasets.rgC, optionPie );
	}
	if (datasets.avC){
		$.plot('#phAvancadaC', datasets.avC, optionPie );
	}

	$('[cd-area]')
		.on('click', function (e) {
		})
		.on('show.bs.collapse', function (e) {
			e.stopPropagation();

			var area = $(this).attr("cd-area");
			showDetailEspec( $(this).find("#"+area ), { cdArea : area } );
			
			$('[it-int]')
				.on('click', function (ex) {
					ex.stopPropagation();
					showDetailEspecPeople( $(this).find("#detalhes"), { item: $(this).attr("it-int") } );
					 $(this).find("#detalhes").addClass('in');
				});
		});
});

function labelFormatter(label, series) {
	return "<div style='font-size:9px;text-align:center;color:black;font-weight:bolder'>" + label + "<br/>" + Math.round(series.percent) + "%</div>";
}
