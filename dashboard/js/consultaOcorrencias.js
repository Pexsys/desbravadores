var ocoDataTable = undefined;

$(document).ready(function(){
	$.fn.dataTable.moment( 'DD/MM/YYYY HH:mm' );

	ocoDataTable = $('#ocoDataTable')
		.DataTable({
			lengthChange: false,
			ordering: true,
			paging: false,
			scrollY: 300,
			searching: true,
			processing: true,
			language: {
				info: "_END_ ocorr&ecirc;ncias",
				search: "",
				searchPlaceholder: "Procurar...",
				infoFiltered: " de _MAX_",
				loadingRecords: "Aguarde - carregando...",
				zeroRecords: "Dados indispon&iacute;veis para esta sele&ccedil;&atilde;o",
				infoEmpty: "0 encontrados"
			},
			ajax: {
				type	: "POST",
				url	: jsLIB.rootDir+"rules/ocorrencias.php",
				data	: function (d) {
						d.MethodName = "getOcorrencias",
						d.data = { 
							filter: 'N'
						}
					},
				dataSrc: "ocorr"
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
	
	$('#ocoDataTable tbody').on('click', 'tr', function () {
		populateOcorrencias( ocoDataTable.row( this ).data() );
		$("#ocoModal").modal();
	});
	
	$('#btnCiente').on('click', function(){
		jsLIB.ajaxCall( false, jsLIB.rootDir+"rules/ocorrencias.php", { MethodName : 'fSetRead', data : { id : $(this).attr("ocorr-id") } }, 'RETURN' );
		ocoDataTable.ajax.reload();
		$("#ocoModal").modal('hide');

		updateNotifications();
	});
});

function populateOcorrencias( data ) {
	$("#btnCiente").visible(false);
	var cm = jsLIB.ajaxCall( false, jsLIB.rootDir+"rules/ocorrencias.php", { MethodName : 'fOcorrencia', data : { id : data.id, nomes : 'N' } }, 'RETURN' );
	if (cm.ocorrencia){
		$("#ocorrenciaTitle").html("<b>Ocorr&ecirc;ncia #"+cm.ocorrencia.cd+" - Data:"+moment.unix(cm.ocorrencia.dh/1000).format("DD/MM/YYYY")+" - Inserido por:&nbsp;"+cm.ocorrencia.owner+"</b>");
		$("#ocorrenciaBody").html(cm.ocorrencia.txt);
		
		if (data.st == 'S'){
			setTimeout(function(){
				$("#btnCiente").attr("ocorr-id",data.id).visible(true);
			}, 5000);			
		}
	}
}