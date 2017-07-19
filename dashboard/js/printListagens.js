var opt = '';

$(document).ready(function(){

	$("#btnGerar").click( function(e) {
		if (opt !== ''){
			var url = jsLIB.rootDir+'report/';
			if (opt == "LST_ATIVOS"){
				url += 'geraListaAtivos.php?';
			} else if (opt == "LST_BATISMO"){
				url += 'geraListaBatismos.php?filter='+$("#cbBatismo").val();
			} else if (opt == "LST_CLASSES"){
				url += 'geraListaClasse.php?filter='+$("#cbClasses").val();
			} else if (opt == "LST_UNIDADES"){
				url += 'geraListaUnidade.php?filter='+$("#cbUnidades").val();
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
			} else if (opt == "LST_EVE_CTRL_GEN") {
				url += 'geraListaEventoAutorizGen.php?eve='+$("#cbEventos").val();
            } else if (opt == "LST_EVE_PASS") {
			    url += 'geraListaEventoBus.php?eve='+$("#cbEventos").val();
			} else if (opt == "LST_EVE_TENT") {
			    url += 'geraListaEventoTent.php?eve='+$("#cbEventos").val();
			} else if (opt == "LST_EVE_KITCHEN") {
			    url += 'geraListaEventoKitchen.php?eve='+$("#cbEventos").val();
			} else if (opt == "LST_EVE_MAT_C") {
			    url += 'geraListaUniformes.php?filter=C&eve='+$("#cbEventos").val();
			} else if (opt == "LST_EVE_MAT_A") {
			    url += 'geraListaUniformes.php?filter=A&eve='+$("#cbEventos").val();
			} else if (opt == "LST_EVE_DIS_ESC") {
			    url += 'geraListaDispensaEscola.php?eve='+$("#cbEventos").val();
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
    $("#unidades").visible( opt == 'LST_UNIDADES' );
    $("#classes").visible( opt == 'LST_CLASSES' );
    $("#eventos").visible( opt.startsWith('LST_EVE') );
	$("#btnGerar").visible( $("#cbListagem").val() !== '' );
}