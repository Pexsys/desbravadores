var datasets = undefined;
var choiceContainer = undefined;
var previousPoint = null, previousLabel = null;


$(document).ready(function(){
	datasets = jsLIB.ajaxCall( undefined, jsLIB.rootDir+"rules/meuAprendizado.php", { MethodName : 'getGraphData' }, 'RETURN' );
	
	if (datasets.ob) {
		choiceContainer = $("#choices");
		$.each(datasets, function(key, val) {
			if (val.checkbox){
				choiceContainer.append('<label><input type="checkbox" name="'+ key +
					'" checked="checked" id="op'+ key +'"/>'+ val.label +'</label>&nbsp;');
			}
		});

		choiceContainer.find("input").click(plotAccordingToChoices);
		plotAccordingToChoices();
	}
});

$.fn.UseTooltip = function () {
	var monthNames = ["Jan", "Fev", "Mar", "Abr", "Mai", "Jun", "Jul", "Ago", "Set", "Out", "Nov", "Dez"];
	$(this).bind("plothover", function (event, pos, item) {
		if (item) {
			if ((previousLabel != item.series.label) || (previousPoint != item.dataIndex)) {
				previousPoint = item.dataIndex;
				previousLabel = item.series.label;
				$("#tooltip").remove();
				var x = item.datapoint[0];
				var y = item.datapoint[1];
				var color = item.series.color;
				var date = new Date(x);
				showTooltip(item.pageX,
					item.pageY,
					color,
					"<center>"+
					"<b>"+ item.series.label +"</b><br/>"+
					date.getDate()+"/"+monthNames[date.getMonth()] +": "+
					"<b>"+ Math.round(y,0) +"</b>(%)"+
					"</center>");
			}
		} else {
			$("#tooltip").remove();
			previousPoint = null;
		}
	});
};

function showTooltip(x, y, color, contents) {
	$('<div id="tooltip">' + contents + '</div>').css({
		position: 'absolute',
		display: 'none',
		top: y - 40,
		left: x - 120,
		border: '2px solid ' + color,
		padding: '3px',
		'font-size': '9px',
		'border-radius': '5px',
		'background-color': '#fff',
		'font-family': 'Verdana, Arial, Helvetica, Tahoma, sans-serif',
		opacity: 0.9
	}).appendTo("body").fadeIn(0);
}

function plotAccordingToChoices() {
	var data = [];
	$.each( datasets, function(key, val) {
		if (!val.checkbox){
			data.push(datasets[key]);
		}
	});
	choiceContainer.find("input:checked").each(function () {
		var key = $(this).attr("name");
		if (key && datasets[key]) {
			data.push(datasets[key]);
		}
	});
	if (data.length > 0) {
		var options = {
			series: {
				points: {
					radius: 3,
					fill: true,
					show: true
				},
				stack: true
			},
			yaxis: {
				min: 0,
				max: 100
			},
			xaxis: {
				tickDecimals: 0,
				mode: "time",
				tickSize: [45, "day"],
				timeformat: "%d/%m",
				axisLabelUseCanvas: true,
				axisLabelFontSizePixels: 2,
				axisLabelFontFamily: 'Verdana, Arial',
				axisLabelPadding: 2
			},
			crosshair: { mode: "xy"},
			legend: {
				noColumns: 1,
				labelBoxBorderColor: "#000000",
				position: "nw"
			},
			grid: {
				hoverable: true,
				borderWidth: 1,
				borderColor: "#000000",
				backgroundColor: { colors: ["#ffffff", "#EDF5FF"] }
			},
			highlightColor: "#000000"
		};
		var plot = $.plot( "#placeholder", data, options );
		$("#placeholder").UseTooltip();
		
		if (datasets.ob && datasets.ob.idx) {
			plot.highlight( 0, datasets.ob.idx );
		}
		
	}
}