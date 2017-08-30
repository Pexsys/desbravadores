$(document).ready(function(){

	$("#cmANO").on('change', function (e) {
		agendaConsulta();
	});

	agendaConsulta();
});

function agendaConsulta() {
	dts = jsLIB.ajaxCall( undefined, jsLIB.rootDir+"rules/agenda.php", { MethodName : 'agendaConsulta', data : { ano: $("#cmANO").val() } }, 'RETURN' );

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