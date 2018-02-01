$(document).ready(function(){
	jsLIB.ajaxCall({
		waiting : true,
		type: "GET",
		url: jsLIB.rootDir+"admin/rules/painelAprendizadoClasse.php",
		data: { MethodName : 'getGraphData' },
		success: function(data){
			if (data.cls){
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
						ticks: data.ticks
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
					
				$.plot("#phGhaphC", data.cls, optionBar );
			}
		}
	});

	$('.panel')
		.on('click', function (e) {
		})
		.on('show.bs.collapse', function (e) {
			var idCad = $(this).attr("cad-id");
			showDetailClass( this, { id: idCad, iil: $(this).attr("itm-int-like") } );
		})
		.on('hide.bs.collapse', function (e) {
			$(this).find('i').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');
		})
	;
});