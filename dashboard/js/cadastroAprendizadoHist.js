var dataTable = undefined;
var rowSelected = undefined;

$(document).ready(function(){
	$.fn.dataTable.moment( 'DD/MM/YYYY' );

	dataTable = $('#aprHstTable')
		.DataTable({
			lengthChange: false,
			ordering: true,
			paging: false,
			scrollY: 330,
			searching: true,
			processing: true,
			language: {
				info: "_END_ itens",
				search: "",
				searchPlaceholder: "Procurar...",
				infoFiltered: " de _MAX_",
				loadingRecords: "Aguarde - carregando...",
				zeroRecords: "Dados indispon&iacute;veis para esta sele&ccedil;&atilde;o. Verifique opções de filtro.",
				infoEmpty: "0 encontrados"
			},
			ajax: {
				type	: "POST",
				url	: jsLIB.rootDir+"rules/aprendizadoHist.php",
				data	: function (d) {
						d.MethodName = "getAprHist",
						d.data = { 
								 filtro: 'T',
								 filters: jsFilter.jSON()
							}
					},
				dataSrc: "aprhist"
			},
			order: [ 1, 'asc' ],
			columns: [
				{	data: "id"
				},
				{	data: "nm",
					sortable: true,
					type: 'ptbr-string',
					width: "38%"
				},
				{	data: "dstpi",
					type: 'ptbr-string',
					sortable: true,
					width: "12%"
				},
				{	data: "dsitm",
					type: 'ptbr-string',
					sortable: true,
					width: "40%"
				},
				{	data: "pg",
					sortable: true,
					width: "3%"
				},
				{	data: "dta",
					sortable: true,
					width: "7%",
					render: function (data) {
						return (data == "" ? "" : moment.unix(data).format("DD/MM/YYYY") );
					}
				}
			],
			columnDefs: [
				{
					targets: [ 0 ],
					visible: false,
					searchable: false
				}
			],
			select: {
				style: 'multi',
				selector: 'td:first-child'
			}
		})
	;

	$('#btnDelHist').click(function(){
		BootstrapDialog.show({
			title: 'Alerta',
			message: 'Confirma exclus&atilde;o das linhas selecionadas?',
			type: BootstrapDialog.TYPE_WARNING,
			size: BootstrapDialog.SIZE_SMALL,
			draggable: true,
			closable: true,
			closeByBackdrop: false,
			closeByKeyboard: false,
			buttons: [
				{ label: 'N&atilde;o',
					cssClass: 'btn-success',
					action: function( dialogRef ){
						dialogRef.close();
					}
				},
				{ label: 'Sim, desejo excluir!',
					icon: 'glyphicon glyphicon-trash',
					cssClass: 'btn-danger',
					autospin: true,
					action: function(dialogRef){
						ruleBtnDelete(false);
						dialogRef.enableButtons(false);
						dialogRef.setClosable(false);
						
						var selected = dataTable.rows('.selected').data();
						var tmp = [];
						for (var i=0;i<selected.length;i++){
							tmp.push(selected[i].id);
						}
						var parameter = {
							ids: tmp
						};
						jsLIB.ajaxCall({
							waiting : false,
							async: true,
							url: jsLIB.rootDir+"rules/aprendizado.php",
							data: { MethodName : 'delete', data : parameter },
							callBackSucess: function(){
								dialogRef.close();
								dataTable.ajax.reload();
							}
						});
					}
				}
			]
	    });
	});
	
	$('#aprHstTable tbody').on('click', 'tr', function () {
		$(this).toggleClass('selected');
		ruleBtnDelete();
	});
		
	$(".date").mask('00/00/0000');
	ruleBtnDelete(false);
});

function ruleBtnDelete( force ){
	$("#btnDelHist").visible( force != undefined ? force : dataTable.rows('.selected').data().length > 0 );
}