$(document).ready(function(){

	$("#cmANO").on('change', function (e) {
		agendaConsulta();
	});

	agendaConsulta();
});

function agendaConsulta() {
	jsLIB.ajaxCall({
		waiting : true,
		async: true,
		type: "GET",
		url: jsLIB.rootDir+"rules/agenda.php",
		data: { MethodName : 'agendaConsulta', data : { ano: $("#cmANO").val() } },
		callBackSucess: function(dts){
			if (dts.years){
				jsLIB.populateOptions( $("#cmANO"), dts.years );
			}
			if (dts.agenda){
				$("#content").html(dts.agenda);
				$('.panel-heading').on('click', function () {
					$($(this).data('target')).collapse('toggle');
				});
			}
		}
	});
}