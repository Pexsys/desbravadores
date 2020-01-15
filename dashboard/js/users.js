var dataTable = undefined;
var rowSelected = undefined;

$(document).ready(function(){
	$.fn.dataTable.moment( 'DD/MM/YYYY HH:mm:ss' );

	dataTable = $('#usrTable')
		.DataTable({
			lengthChange: false,
			ordering: true,
			paging: false,
			scrollY: 330,
			searching: true,
			processing: true,
			language: {
				info: "_END_ usuários",
				search: "",
				searchPlaceholder: "Procurar...",
				infoFiltered: " de _MAX_",
				loadingRecords: "Aguarde - carregando...",
				zeroRecords: "Dados indispon&iacute;veis para esta sele&ccedil;&atilde;o",
				infoEmpty: "0 encontrados"
			},
			ajax: {
				type	: "GET",
				url	: jsLIB.rootDir+"rules/users.php",
				data	: function (d) {
						d.MethodName = "getUsers",
						d.data = { 
								 filtro: 'T',
								 filters: jsFilter.jSON()
							}
					},
				dataSrc: "users"
			},
			order: [ 2, 'desc' ],
			columns: [
				{	data: "id"
				},
				{	data: "ds",
					sortable: true,
					type: 'ptbr-string',
					width: "80%"
				},
				{	data: "dh",
					sortable: true,
					width: "20%",
					render: function (data) {
						return (data == "" ? "" : moment.unix(data).format("DD/MM/YYYY HH:mm:ss") );
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

	$('#btnDelUsers').click(function(){
		BootstrapDialog.show({
			title: 'Alerta',
			message: 'Confirma exclus&atilde;o dos usuários selecionados?',
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
						var ids = [];
						for (var i=0;i<selected.length;i++){
							ids.push(selected[i].id);
						}
						jsLIB.ajaxCall({
							url: jsLIB.rootDir+"rules/users.php",
							data: { MethodName : 'delete', data : { ids } },
							success: function(){
								dialogRef.close();
								dataTable.ajax.reload();
							}
						});
					}
				}
			]
	    });
	});
	
	$('#usrTable tbody').on('click', 'tr', function () {
		$(this).toggleClass('selected');
		ruleBtnDelete();
		ruleBtnEdit();
	});
	
	$('#btnChangePass').click(function(){
		$("#comModal").modal();
	});
	
	$('#comModal .modal-footer').click(function(e){
		e.preventDefault();
		e.stopPropagation();
	});
	$('#comModal').on('hidden.bs.modal', function(e){
    	ruleBtnDelete(false);
    	ruleBtnEdit(false);
		dataTable.ajax.reload();
	});	
		
	$(".date").mask('00/00/0000');
	ruleBtnDelete(false);
	ruleBtnEdit(false);
});

function ruleBtnDelete( force ){
	$("#btnDelUsers").visible( force ? force : dataTable.rows('.selected').data().length > 0 );
}

function showBtnEdit(selected){
	$("#btnChangePass")
		.attr("id-item",selected)
		.visible( selected !== '' );
}

function ruleBtnEdit( force ){
	var data = dataTable.rows('.selected').data();
	showBtnEdit('');
	if (data.length == 1){
        showBtnEdit(data[0].id);
	}
}