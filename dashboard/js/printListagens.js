$(document).ready(function(){

	$("#btnGerar").click( function(e) {
		var opt = $("#cbListagem").val();
		if (opt != ''){
			var url = jsLIB.rootDir+'report/';
			if (opt == "LST_ATIVOS"){
				url += 'geraListaAtivos.php?';
			} else if (opt == "LST_NAO_BATIZADOS"){
				url += 'geraListaNaoBatizados.php?';
			} else if (opt == "LST_CLASSE"){
				url += 'geraListaClasse.php?';
			} else if (opt == "LST_CAMISETAS"){
				url += 'geraListaCamisetas.php?';
			} else if (opt == "LST_ESTRELATS"){
				url += 'geraListaEstrelas.php?';
			}
			window.open(url,'_blank','top=50,left=50,height=750,width=550,menubar=no,status=no,titlebar=no',true);
		}
	});

	$("#cbListagem").change(function(){
		ruleBotaoGerar();
	});
	
});

function ruleBotaoGerar(){
	$("#btnGerar").visible( $("#cbListagem").val() !== ''  );
}