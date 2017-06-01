var comDataTable = undefined;
var rowSelected = undefined;
var valuePend = undefined;
var valuePendOrig = undefined;

$(document).ready(function(){
	$.fn.dataTable.moment( 'DD/MM/YYYY HH:mm' );

	comDataTable = $('#comDataTable').DataTable({
		lengthChange: false,
		ordering: true,
		paging: false,
		scrollY: 150,
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
						filter: 'A'
					}
				},
			dataSrc: "comunic"
		},
		order: [ 2, 'desc' ],
		columns: [
			{	data: 'id',
				visible: false
			},
			{	data: 'so',
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
					return (data == 'S' ? "RASCUNHO" : "Efetivado");
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
	});
	
	$('#datetimepicker').datetimepicker({
		locale: 'pt-br',
		language: 'pt-BR',
		format: 'DD/MM/YYYY',
		maskInput: true,
		pickDate: true,
		pickTime: false,
		pickSeconds: false,
		useCurrent: false
	}).on('dp.change',function(){
		$("#cadComForm").formValidation('revalidateField', 'comDH');
		buttons();
	});
	
	tinymce.init({
		selector: '[type=wysiwyg]',
		height: 230,
		language: 'pt_BR',
		plugins: [
			"advlist link image lists print hr spellchecker",
		    "searchreplace wordcount code media nonbreaking",
		    "table contextmenu directionality textcolor paste textcolor colorpicker textpattern"
		],
		toolbar1: "newdocument | fontselect fontsizeselect | bold italic underline strikethrough | subscript superscript | alignleft aligncenter alignright alignjustify | bullist numlist | outdent indent",
		toolbar2: "undo redo | cut copy paste | searchreplace | image code | forecolor backcolor | table | spellchecker",
		fontsize_formats: '2px 3px 4px 5px 6px 7px 8px 9px 10px 11px 12px 13px 14px 15px 16px 17px 18px 19px 20px 21px 22px',
		content_style: "p{margin: 0px; padding: 0px}, br{margin: 0px; padding: 0px}",
		toolbar_items_size: 'small',
		save_enablewhendirty: false,
		menubar: false,
		browser_spellcheck: true,
		spellchecker_languages: 'Portugues=pt_BR',
		spellchecker_rpc_url: 'spellchecker.php',
		statusbar: false
	});

	$("#nrCopias").TouchSpin({
		verticalbuttons: true,
		verticalupclass: 'glyphicon glyphicon-plus',
		verticaldownclass: 'glyphicon glyphicon-minus'
	});

	$('#btnPrint').click(function(){
		window.open(
				jsLIB.rootDir+'report/geraComunicados.php?id='+$("#comID").val()+'&q='+$("#nrCopias").val(),
				'_blank',
				'top=50,left=50,height=750,width=550,menubar=no,status=no,titlebar=no',
				true
			);
	});

	$('#comDataTable tbody').on('click', 'tr', function () {
		rowSelected = this;
		valuePendOrig = comDataTable.row( rowSelected ).data().so;
		populateComunicado( comDataTable.row( rowSelected ).data().id );
		$("#comModal").modal();
	});
	
	$("#cadComForm")
		.on('err.field.fv', function(e, data) {
			$('#btnGravar').visible(false);
		})
		.formValidation({
			framework: 'bootstrap',
			fields: {
				comDH: {
					validators: {
						excluded: false,
						notEmpty: {
							message: 'A data do comunicado não pode estar em branco'
						},
						date: {
							format: 'DD/MM/YYYY',
							message: 'Data inv&aacute;lida!'
						}
					}
				}
			}
		})
		.submit( function(e) {
			e.preventDefault();
			e.stopPropagation();
		})
	;
	
	$('#btnDel').click(function(){
		BootstrapDialog.show({
			title: 'Alerta',
			message: 'Confirma exclus&atilde;o deste comunicado?',
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
						dialogRef.enableButtons(false);
						dialogRef.setClosable(false);
						var parameter = {
							id: $('#comID').val(),
							op: "DELETE"
						};
						jsLIB.ajaxCall( false, jsLIB.rootDir+"rules/comunicados.php", { MethodName : 'fComunicado', data : parameter } );
						comDataTable.ajax.reload();
						dialogRef.close();
						$("#comModal").modal('hide');
					}
				}
			]
		});
	});	
	
	$('#btnGravar').click(function(){
		if (valuePendOrig !== valuePend) {
			BootstrapDialog.show({
				title: 'Alerta',
				message: 'Confirma gravação, envio e geração deste comunicado?',
				type: BootstrapDialog.TYPE_SUCCESS,
				size: BootstrapDialog.SIZE_SMALL,
				draggable: true,
				closable: true,
				closeByBackdrop: false,
				closeByKeyboard: false,
				buttons: [
					{ label: 'N&atilde;o',
						cssClass: 'btn-warning',
						action: function( dialogRef ){
							$("#fgPend").prop('checked', false).change();
							dialogRef.close();
						}
					},
					{ label: 'Sim, desejo gravar!',
						icon: 'glyphicon glyphicon-trash',
						cssClass: 'btn-danger',
						autospin: true,
						action: function(dialogRef){
							dialogRef.enableButtons(false);
							dialogRef.setClosable(false);
							updateComunicado();
							updateNotifications();
							buttons();
							dialogRef.close();
						}
					}
					]
			});		
		} else {
			updateComunicado();
		}
	});
	
	$("#comModal").on('show.bs.modal', function(event){
		buttons();
	});
	
	$('#btnNovo').click(function(){
		jsLIB.resetForm( $('#cadComForm') );
		populateComunicado( $("#comID").val("Novo").val() );
		buttons();
		$("#comModal").modal();
	});
	
	$("#fgPend").change(function(){
		valuePend = jsLIB.getValueFromField( $("#fgPend") );
		buttons();
	});
});

function buttons(){
	$('#btnDel').visible( $("#comID").val() != "Novo" && valuePend == 'S' && valuePendOrig == 'S' );
	$('#divPrint').visible( $("#comID").val() != "Novo" && valuePendOrig == 'N' );
	$("#btnGravar").visible(valuePendOrig !== 'N' && $("#comDH").val() != '');
}

function rulefields(){
	$("#comDH").enable(valuePendOrig !== 'N');
	$("#fgPend").prop('disabled', (valuePendOrig == 'N') ).change();
	tinymce.get('txt').setMode(valuePendOrig == 'N'?'readonly':'design');
}

function populateComunicado( comunicadoID ) {
	var cm = jsLIB.ajaxCall( false, jsLIB.rootDir+"rules/comunicados.php", { MethodName : 'fComunicado', data : { id : comunicadoID } }, 'RETURN' );
	jsLIB.populateForm( $("#cadComForm"), cm.comunicado );
	valuePendOrig = cm.comunicado.fg_pend;
	valuePend = jsLIB.getValueFromField( $("#fgPend") );
	rulefields();
	buttons();
}

function updateComunicado(){
	var parameter = {
		op: "UPDATE",
		frm: jsLIB.getJSONFields( $('#cadComForm') )
	};
	var cm = jsLIB.ajaxCall( false, jsLIB.rootDir+"rules/comunicados.php", { MethodName : 'fComunicado', data : parameter }, 'RETURN' );
	$("#comID").val(cm.id);
	valuePendOrig = cm.so;
	valuePend = jsLIB.getValueFromField( $("#fgPend") );
	buttons();
	rulefields();
	comDataTable.ajax.reload();
}