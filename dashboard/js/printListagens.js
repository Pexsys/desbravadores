var opt = '';

$(document).ready(function(){

	$("#btnGerar").click( function(e) {
		if (opt !== ''){
			var url = jsLIB.rootDir+'report/';
			if (opt == "LST_ATIVOS"){
				url += 'geraListaAtivos.php?';
			} else if (opt == "LST_BATISMO"){
				url += 'geraListaBatismos.php?filter='+$("#cbBatismo").val();
			} else if (opt == "LST_CLASSE"){
				url += 'geraListaClasse.php?';
			} else if (opt == "LST_UNIFORMES"){
				url += 'geraListaUniformes.php?filter='+$("#cbUniformes").val();
			} else if (opt == "LST_ESTRELATS"){
				url += 'geraListaEstrelas.php?';
			} else if (opt == "LST_PRESPAIS") {
				url += 'geraListaPresencaPais.php?';
			} else if (opt == "LST_EVE_ALFA") {
				url += 'geraListaEvento.php?eve='+$("#cbEventos").val();
			} else if (opt == "LST_EVE_CTRL") {
				url += 'geraListaEventoAutoriz.php?eve='+$("#cbEventos").val();
			} else if (opt == "LST_EVE_PASS") {
			    url += 'geraListaEventoBus.php?eve='+$("#cbEventos").val();
			} else if (opt == "LST_EVE_PASS") {
			    url += 'geraListaEventoTent.php?eve='+$("#cbEventos").val();
			}
			window.open(url,'_blank','top=50,left=50,height=750,width=550,menubar=no,status=no,titlebar=no',true);
		}
	});

	$("#cbListagem").change(function(){
	    opt = $(this).val();
		rulesGeracao();
	});
	
});

function rulesGeracao(){
    $("#batismo").visible( opt == 'LST_BATISMO' );
    $("#uniformes").visible( opt == 'LST_UNIFORMES' );
    $("#eventos").visible( opt == 'LST_EVE_ALFA' || opt == 'LST_EVE_CTRL' || opt == 'LST_EVE_PASS' || opt == 'LST_EVE_TENT' );
	$("#btnGerar").visible( $("#cbListagem").val() !== '' );
}