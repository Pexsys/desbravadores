var comDataTable = undefined;
var rowSelected = undefined;

$(document).ready(function(){

	comDataTable = $('#comDataTable').DataTable({
		lengthChange: false,
		ordering: true,
		paging: false,
		scrollY: 390,
		searching: false,
		processing: true,
		language: {
			info: "_END_ unidades",
			search: "",
			searchPlaceholder: "Procurar...",
			infoFiltered: " de _MAX_",
			loadingRecords: "Aguarde - carregando...",
			zeroRecords: "Dados indispon&iacute;veis para esta sele&ccedil;&atilde;o",
			infoEmpty: "0 encontrados"
		},
		ajax: {
			type	: "GET",
			url	: jsLIB.rootDir+"rules/tabelas.php",
			data	: function (d) {
				d.MethodName = "getUnidades"
			},
			dataSrc: "source"
		},
		columns: [
			{	data: 'id',
				sortable: true,
				width: "5%"
			},
			{	data: 'ie',
				sortable: true,
				width: "10%"
			},
			{	data: 'ds',
				type: 'ptbr-string',
				sortable: true,
				width: "50%"
			},
			{	data: 'tp',
				sortable: true,
				width: "25%",
				render: function (data) {
					if (data == 'A')
						return "AMBOS";
					else {
						return (data == 'M' ? "MASCUL" : "FEMIN") + "INO";
					}
				}
			},
			{	data: 'cc',
				sortable: true,
				width: "25%",
				render: function (data) {
					return "<i class=\"fa fa-stop\" aria-hidden=\"true\"></i>";
				}
			},
			{	data: 'fg',
				sortable: true,
				width: "10%",
				render: function (data) {
					return (data == 'S' ? "SIM" : "N&Atilde;O");
				}
			}
		],
		fnRowCallback: function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
			if ( aData.fg == 'N' ) {
				$('td', nRow).css('color', '#d0d0d0');
			} else {
				$(nRow.cells[3]).css('color', aData.ccg ).css('font-weight', 'bold');
				$(nRow.cells[4]).css('color', aData.cc ).css('font-weight', 'bold');
			}
   }
  }).order( [ 5, 'desc' ], [ 1, 'asc' ], [ 3, 'asc' ] );
  
  $('#comDataTable tbody').on('click', 'tr', function () {
		rowSelected = this;
		populateUnidade( comDataTable.row( rowSelected ).data().id );
  });

  $("#cadComForm")
		.on('err.field.fv', function(e, data) {
			$('#btnGravar').visible(false);
		})
		.submit( function(e) {
			e.preventDefault();
			e.stopPropagation();
		})
	;
  
  $("#nrIdade").TouchSpin({
		verticalbuttons: true,
		verticalupclass: 'glyphicon glyphicon-plus',
		verticaldownclass: 'glyphicon glyphicon-minus'
	});

});

$('#btnGravar').click(function(){
  var parameter = {
		op: "UPDATE",
		frm: jsLIB.getJSONFields( $('#cadComForm') )
	};
	jsLIB.ajaxCall({
		waiting : true,
		url: jsLIB.rootDir+"rules/tabelas.php",
		data: { MethodName : 'fUnidade', data : parameter },
		success: function(tb){
      comDataTable.ajax.reload();
      $("#comModal").modal('hide');
		}
	});
});

function populateUnidade( id ) {
	jsLIB.ajaxCall({
		waiting : true,
		type: "GET",
		url: jsLIB.rootDir+"rules/tabelas.php",
		data: { MethodName : 'fUnidade', data : { id } },
		success: function(tb){
      if (tb.unidade.fg_edit !== 'S') return
      jsLIB.populateForm( $("#cadComForm"), tb.unidade );
      $("#comModal").modal(); 
		}
	});
}
