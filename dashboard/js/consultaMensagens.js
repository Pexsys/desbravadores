var msgDataTable = undefined;

$(document).ready(function(){
	$.fn.dataTable.moment( 'DD/MM/YYYY HH:mm:ss' );

	msgDataTable = $('#msgDataTable')
		.DataTable({
			lengthChange: false,
			ordering: true,
			paging: false,
			scrollY: 300,
			searching: true,
			processing: true,
			language: {
				info: "_END_ mensagens",
				search: "",
				searchPlaceholder: "Procurar...",
				infoFiltered: " de _MAX_",
				loadingRecords: "Aguarde - carregando...",
				zeroRecords: "Dados indispon&iacute;veis para esta sele&ccedil;&atilde;o",
				infoEmpty: "0 encontrados"
			},
			ajax: {
				type	: "GET",
				url	: jsLIB.rootDir+"rules/comunicados.php",
				data	: function (d) {
          d.MethodName = "getMensagens",
          d.data = {
            filtro: 'N',
            filters: jsFilter.jSON()
          }
        },
				dataSrc: "mensag"
			},
			order: [ 1, 'desc' ],
			columns: [
				{	data: 'tp',
					sortable: true,
					width: "5%",
          render: function (data) {
            if (data == 'C') return 'COMUNICADO';
            if (data == 'O') return 'OCORRÊNCIA';
            if (data == 'M') return 'MESTRADO';
            return '';
					}
        },
        {	data: 'usu',
          sortable: true,
          width: "33%"
        },
        {	data: 'dst',
          sortable: true,
          width: "20%"
        },
        {	data: 'dhg',
          sortable: true,
          width: "14%",
          render: function (data) {
            return formatData(data);
          }
        },
        {	data: 'dhe',
          sortable: true,
          width: "14%",
          render: function (data) {
            return formatData(data);
          }
        },
				{	data: 'dhr',
					sortable: true,
          width: "14%",
          render: function (data) {
            return formatData(data);
					}
				}
      ]
      // ,
			// fnRowCallback: function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
      //   if ( aData.dh < moment().unix() ) {
      //       $('td', nRow).css('color', '#d0d0d0');
      //   }
			// 	if ( aData.st == 'S' ) {
			// 		$(nRow.cells[2]).css('color', '#cc0000').css('font-weight', 'bold');
			// 	}
      // }
		})
	;

  const formatData = data => {
    if (data == '') return data;
    return moment.unix(data).format("DD/MM/YYYY HH:mm:ss");
  };

});