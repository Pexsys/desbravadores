var comDataTable = undefined;
var rowSelected = undefined;

$(document).ready(function(){

	comDataTable = $('#comDataTable').DataTable({
        lengthChange: false,
		ordering: true,
		paging: false,
		scrollY: 300,
		searching: true,
		processing: true,
		language: {
			info: "_END_ acordos",
			search: "",
			searchPlaceholder: "Procurar...",
			infoFiltered: " de _MAX_",
			loadingRecords: "Aguarde - carregando...",
			zeroRecords: "Dados indispon&iacute;veis para esta sele&ccedil;&atilde;o",
			infoEmpty: "0 encontrados"
		},
		ajax: {
			type	: "GET",
			url	: jsLIB.rootDir+"admin/rules/acordos.php",
			data	: function (d) {
				d.MethodName = "getAcordos",
                d.data = {
                    filtro: 'T',
                    filters: jsFilter.jSON()
                }
			},
			dataSrc: "source"
		},
		columns: [
            {	data: "id",
				visible: false
			},
			{	data: 'cd',
				sortable: true,
				width: "10%"
			},
			{	data: 'pt',
				type: 'ptbr-string',
				sortable: true,
				width: "40%"
			},
			{	data: 'bn',
				type: 'ptbr-string',
				sortable: true,
				width: "40%"
			},
            {	data: "tp",
				type: 'ptbr-string',
				width: "10%",
				render: function (data, type, row) {
					if (data == 'P'){
						return "PENDENTE";
					} else if (data == 'L'){
						return "LIBERADO";
					} else {
                        return "CONCLUÍDO";
					}
				}
			}
		],
		fnRowCallback: function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
			if (aData.tp == "P") {
				$(nRow.cells[3]).css('color', '#fdedc4' ).css('font-weight', 'bold');
			} else if ( aData.tp == "L" ) {
				$(nRow.cells[3]).css('color', '#b0ffb3' );
			} else {
				$(nRow.cells[3]).css('color', '#b0ffb3' );
			}
        }
	}).order( [ 4, 'asc' ] );

    $("#btnNovo").on("click", function(event){
        alternaFormularios(true);
    });

    $("#btnFechar").on("click", function(event){
        alternaFormularios(false);
    });

    $("#btnGravar").on("click", function(event){
        //TODO: GRAVAR DADOS
        refreshDataTable();
        alternaFormularios(false);
    });
});

function alternaFormularios(exibe){
    $("#divLista").visible(!exibe);
    $("#divAcordo").visible(exibe);
}

function refreshDataTable(){
    comDataTable.ajax.reload( function(){
	});
}
