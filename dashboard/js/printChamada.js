$(document).ready(function(){

	$("#btnGerar").click( function(e) {
		var url = jsLIB.rootDir+'report/geraChamada.php?m='+$("#cbMeses").val()+'&u='+$("#cbUnidades").val();
		window.open(url,'_blank','top=50,left=50,height=750,width=550,menubar=no,status=no,titlebar=no',true);
	});

	$("#cbUnidades, #cbMeses").change(function(){
		ruleBotaoGerar();
	});
	
	var mb = jsLIB.ajaxCall( undefined, jsLIB.rootDir+"rules/printChamada.php", { MethodName : 'getDomains' }, 'RETURN' );
	if (mb){
		jsLIB.populateOptions( $("#cbMeses"), mb.meses );
		jsLIB.populateOptions( $("#cbUnidades"), mb.unidade );
		$("#cbUnidades").selectpicker('selectAll');
		ruleBotaoGerar();
	}
	
});

function ruleBotaoGerar(){
	$("#btnGerar").visible( $("#cbUnidades").val() !== null && $("#cbMeses").val() !== null  );
}