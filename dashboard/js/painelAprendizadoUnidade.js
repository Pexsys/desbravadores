var datasets = undefined;

$(document).ready(function(){
	datasets = jsLIB.ajaxCall( undefined, jsLIB.rootDir+"rules/painelAprendizadoUnidade.php", { MethodName : 'getGraphData' }, 'RETURN' );

	if (datasets.cls){
		var optionBar = {
			series: {
				stack: false,
				shadowSize: false,
				bars: {
					show: true,
					barWidth: 0.7,
					align: "center",
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
				axisLabelUseCanvas: true,
				axisLabelFontSizePixels: 10,
				axisLabelFontFamily: 'Verdana, Arial',
				axisLabelPadding: 10,
				tickFormatter: function(v, axis) {
					return "";
				},
				ticks: datasets.ticks
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
			
		$.plot("#phGhaphC", datasets.cls, optionBar );
	}

	$('.panel')
		.on('click', function (e) {
		})
		.on('show.bs.collapse', function (e) {
			var idCad = $(this).attr("cad-id");
			showDetailClass( $(this).find("#m"+idCad), { id: idCad, un: $(this).attr("unidade") } );
			$(this).find('i').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up');
			
			$('[name=progress]')
				.on('click', function (e) {
					showDetailClassReq( $(this).find("#detalhes"), { id: $(this).attr("cad-id"), req: $(this).attr("req-id") } );
				});	
		})
		.on('hide.bs.collapse', function (e) {
			$(this).find('i').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');
		})
	;
});