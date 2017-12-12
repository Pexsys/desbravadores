var comDataTable = undefined;
var valuePend = undefined;
var valuePendOrig = undefined;

$(document).ready(function(){
	$.fn.dataTable.moment( 'DD/MM/YYYY HH:mm' );

	comDataTable = $('#comDataTable')
		.DataTable({
			lengthChange: false,
			ordering: true,
			paging: false,
			scrollY: 300,
			searching: true,
			processing: true,
			language: {
				info: "_END_ comunicados",
				search: "",
				searchPlaceholder: "Procurar...",
				infoFiltered: " de _MAX_",
				loadingRecords: "Aguarde - carregando...",
				zeroRecords: "Dados indispon&iacute;veis para esta sele&ccedil;&atilde;o",
				infoEmpty: "0 encontrados"
			},
			ajax: {
				type	: "POST",
				url	: jsLIB.rootDir+"rules/comunicados.php",
				data	: function (d) {
						d.MethodName = "getComunicados",
						d.data = { 
							filter: 'N'
						}
					},
				dataSrc: "comunic"
			},
			order: [ 1, 'desc' ],
			columns: [
				{	data: 'id',
					visible: false
				},
				{	data: 'cd',
					sortable: true,
					width: "30%"
				},
				{	data: 'dh',
					sortable: true,
					width: "30%",
					render: function (data) {
						return moment.unix(data).format("DD/MM/YYYY")
					}
				},
				{	data: 'st',
					sortable: true,
					width: "30%",
					render: function (data) {
						return (data == 'S' ? "NÃO LIDO" : "LIDO");
					}
				}
			],
			fnRowCallback: function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                if ( aData.dh < moment().unix() ) {
                    $('td', nRow).css('color', '#d0d0d0');
                }
				if ( aData.st == 'S' ) {
					$(nRow.cells[2]).css('color', '#cc0000').css('font-weight', 'bold');
				}
            }
		})
	;
	
	$('#comDataTable tbody').on('click', 'tr', function () {
		populateComunicado( comDataTable.row( this ).data() );
		$("#comModal").modal();
	});
	
	$('#btnCiente').on('click', function(){
		jsLIB.ajaxCall({
			waiting : false,
			async: false,
			url: jsLIB.rootDir+"rules/comunicados.php",
			data: { MethodName : 'fSetRead', data : { id : $(this).attr("comunic-id") } },
			callBackSucess: function(data){
				comDataTable.ajax.reload( function(){
					$("#comModal").modal('hide');
					updateNotifications();					
				});
			}
		});
	});
});

function populateComunicado( data ) {
	$("#btnCiente").visible(false);
	jsLIB.ajaxCall({
		waiting : true,
		async: true,
		type: "GET",
		url: jsLIB.rootDir+"rules/comunicados.php",
		data: { MethodName : 'fComunicado', data : { id : data.id } },
		callBackSucess: function(cm){
			if (cm.comunicado){
				$("#comunicadoTitle").html("<b>Comunicado&nbsp;#"+cm.comunicado.cd+"&nbsp;&nbsp;&nbsp;[&nbsp;"+moment.unix(cm.comunicado.dh/1000).format("DD/MM/YYYY")+"&nbsp;]</b>");
				$("#comunicadoBody").html(cm.comunicado.txt);
				
				if (data.st == 'S'){
					setTimeout(function(){
						$("#btnCiente").attr("comunic-id",data.id).visible(true);
					}, 5000);			
				}
			}
		}
	});
}