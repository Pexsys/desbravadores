var dataTable = undefined;

$(document).ready(function(){
	$.fn.dataTable.moment( 'DD/MM/YYYY' );

	dataTable = $('#matHstTable')
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
				type	: "GET",
				url	: jsLIB.rootDir+"admin/rules/materiaisHist.php",
				data	: function (d) {
						d.MethodName = "getHistorico",
						d.data = {
								 filtro: 'T',
								 filters: jsFilter.jSON()
							}
					},
				dataSrc: "hist"
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
				{	data: "tp",
					type: 'ptbr-string',
					sortable: true,
					width: "12%"
				},
				{	data: "ds",
					type: 'ptbr-string',
					sortable: true,
					width: "40%"
				},
				{	data: "dt",
					sortable: true,
					width: "10%",
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

	$('#matHstTable tbody').on('click', 'tr', function () {
		$(this).toggleClass('selected');
	});

	//$(".date").mask('00/00/0000');
});
