var mesAtu = moment().format('MM');
var diaAtu = moment().format('DD');

$(document).ready(function(){
	$.fn.dataTable.moment( 'DD/MM' );
	
	$('#birthTable').DataTable({
		lengthChange: false,
		ordering: true,
		paging: false,
		scrollY: 330,
		searching: true,
		processing: true,
		language: {
			info: "_END_ aniversariantes",
			search: "",
			searchPlaceholder: "Procurar...",
			infoFiltered: " de _MAX_",
			loadingRecords: "Aguarde - carregando...",
			zeroRecords: "Dados indispon&iacute;veis para esta sele&ccedil;&atilde;o",
			infoEmpty: "0 encontrados"
		},
		ajax: {
			type	: "GET",
			url	: jsLIB.rootDir+"admin/rules/membros.php",
			data	: function (d) {
					d.MethodName = "getAniversariantes",
					d.data = { 
						 filtro: 'A',
						 filters: jsFilter.jSON()
					}
			},
			dataSrc: "membros"
		},
		columns: [
			{	data: "dm",
				visible: false
			},
			{	data: "nm",
				type: 'ptbr-string',
				width: "53%",
				sortable: true
			},
			{	data: "uni",
				width: "33%",
				sortable: true
			},
			{	data: "dm",
				width: "7%",
				sortable: true,
				render: function (data) {
					return moment.unix(data).format("DD/MM")
				}
			},
			{	data: "ih",
				width: "7%",
				sortable: true
			}
		],
		fnRowCallback: function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
			var mesAn = moment.unix(aData.dm).format('MM');
			if ( mesAn < mesAtu ) {
                $('td', nRow).css('color', '#d0d0d0');
            } else if (mesAn == mesAtu) {
            	if (diaAtu == moment.unix(aData.dm).format('DD')){
					$('td', nRow).css('background-color', '#FFACAA');
            	} else {
					$('td', nRow).css('background-color', '#FFFFAA');
				}
            }
       	}
	}).order( [ 0, 'asc' ], [ 1, 'asc' ] );
	
});