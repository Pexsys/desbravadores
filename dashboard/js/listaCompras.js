var dataTable = undefined;
var formPopulated = false;

$(document).ready(function(){

	dataTable = $('#comprasDatatable').DataTable({
		lengthChange: false,
		ordering: true,
		paging: false,
		scrollY: 300,
		searching: true,
		processing: true,
		language: {
			info: "_END_ itens",
			search: "",
			searchPlaceholder: "Procurar...",
			infoFiltered: " de _MAX_",
			loadingRecords: "Aguarde - carregando...",
			zeroRecords: "Dados indispon&iacute;veis para esta sele&ccedil;&atilde;o",
			infoEmpty: "0 encontrados"
		},
		ajax: {
			type	: "GET",
			url	: jsLIB.rootDir+"rules/listaCompras.php",
			data	: function (d) {
					d.MethodName = "getLista",
					d.data = { 
							 filtro: 'T',
							 filters: jsFilter.jSON()
						}
				},
			dataSrc: "compras"
		},
		order: [ 3, 'asc' ],
		columns: [
			{	data: "tp",
				visible: false
			},
			{	data: "id",
				visible: false
			},
			{	data: "ip",
				visible: false
			},
			{	data: "nm",
				type: 'ptbr-string',
				width: "40%"
			},
			{	data: "ds",
				type: 'ptbr-string',
				width: "50%"
			},
			{	data: "ic",
				width: "5%",
				sortable: true,
				render: function (data, type, row) {
					if (row.ip == 'S'){
						return "PREVISTO";
					} else {
						return (data == "S" ? "SIM" : "N&Atilde;O" );
					}
				}
			},
			{	data: "ie",
				width: "5%",
				sortable: true,
				render: function (data, type, row) {
					if (row.ip == 'S'){
						return "";
					} else {
						return (data == "S" ? "SIM" : "N&Atilde;O" );
					}
				}
			}
		],
		fnRowCallback: function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
			if (aData.ip == "S") {
				$('td', nRow).css('color', '#a0a0a0');
			} else if ( aData.ie == "S" ) {
				$(nRow.cells[2]).css('background-color', '#b0ffb3' ).css('font-weight', 'bold');
				$(nRow.cells[3]).css('background-color', '#b0ffb3' ).css('font-weight', 'bold');
			} else if ( aData.ic == "S" ) {
				$(nRow.cells[2]).css('background-color', '#fdedc4' ).css('font-weight', 'bold');
			}
        },
		select: {
			style: 'multi',
			selector: 'td:first-child'
		}
	});
	
	$("#controleForm")
		.on('init.field.fv', function(e, data) {
			var $parent = data.element.parents('.form-group'),
			$icon   = $parent.find('.form-control-feedback[data-fv-icon-for="' + data.field + '"]');
			$icon.on('click.clearing', function() {
				if ( $icon.hasClass('glyphicon-remove') ) {
					data.fv.resetField(data.element);
				}
			});
		})
		.on('success.form.fv', function(e) {
			e.preventDefault();
			e.stopPropagation();
		})	
		.submit( function(event) {
			e.preventDefault();
			e.stopPropagation();
		})
		.on("change", "[field]", function(e) {
			if (formPopulated){
				var input = $(this);
				var field = input.attr('field');
				var value = jsLIB.getValueFromField(input);
				var parameters = {
					id: $("#btnEdit").attr("id-item"),
					fd: field,
					vl: value
				};
				jsLIB.ajaxCall({
					url: jsLIB.rootDir+"rules/listaCompras.php",
					data: { MethodName : 'setAttr', data : parameters },
					success: function(data){
						if (field == 'fg_entregue' && value == 'S' && !$("#fgCompra").prop('checked') ){
							$("#fgCompra").prop('checked', true).triggerHandler('change');
						} else if (field == 'fg_compra' && value == 'N' && $("#fgEntregue").prop('checked') ){
							$("#fgEntregue").prop('checked', false).triggerHandler('change');
						}
						if ( data.est.close ) {
							$("#comprasModal").modal('hide');
						}
					}
				});
			}
		})
	;
	
	$("#cadListaForm")
		.on('init.field.fv', function(e, data) {
			var $parent = data.element.parents('.form-group'),
			$icon   = $parent.find('.form-control-feedback[data-fv-icon-for="' + data.field + '"]');
			$icon.on('click.clearing', function() {
				if ( $icon.hasClass('glyphicon-remove') ) {
					data.fv.resetField(data.element);
				}
			});
		})
		.on('success.form.fv', function(e) {
			e.preventDefault();
		})	
		.formValidation({
			framework: 'bootstrap',
			icon: {
				valid: 'glyphicon glyphicon-ok',
				invalid: 'glyphicon glyphicon-remove',
				validating: 'glyphicon glyphicon-refresh'
			}
		})
		.submit( function(event) {
			var parameter = {
				act: $('#listaModal').attr('action'),
				frm: jsLIB.getJSONFields( $('#cadListaForm') )
			};
			jsLIB.ajaxCall({
				waiting: true,
				url: jsLIB.rootDir+"rules/listaCompras.php",
				data: { MethodName : 'addCompras', data : parameter },
				success: function(data){
					dataTable.ajax.reload();
					$("#listaModal").modal('hide');
				}
			});
		})
	;
	
	$('#btnProcess').click(function(){
		BootstrapDialog.show({
			title: 'Alerta',
			message: 'Confirma reprocessamento da lista autom&aacute;tica de compras?',
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
				{ label: 'Sim, desejo processar!',
					icon: 'glyphicon glyphicon-trash',
					cssClass: 'btn-danger',
					autospin: true,
					action: function(dialogRef){
						dialogRef.enableButtons(false);
						dialogRef.setClosable(false);
						jsLIB.ajaxCall({
							waiting: true,
							url: jsLIB.rootDir+"rules/listaCompras.php",
							data: { MethodName : 'process' },
							success: function(data){
								dataTable.ajax.reload();
								dialogRef.close();
							}
						});
					}
				}
			]
	    });
	});

	$('#cmLista').change(function(){
		rulesGeracao( $(this) );
	});

	$("#printForm")
		.on('init.field.fv', function(e, data) {
			var $parent = data.element.parents('.form-group'),
			$icon   = $parent.find('.form-control-feedback[data-fv-icon-for="' + data.field + '"]');
			$icon.on('click.clearing', function() {
				if ( $icon.hasClass('glyphicon-remove') ) {
					data.fv.resetField(data.element);
				}
			});
		})
		.on('success.form.fv', function(e) {
			e.preventDefault();
		})	
		.formValidation({
			framework: 'bootstrap',
			icon: {
				valid: 'glyphicon glyphicon-ok',
				invalid: 'glyphicon glyphicon-remove',
				validating: 'glyphicon glyphicon-refresh'
			}
		})
	;

	$('#btnListagens').click(function(){
		jsLIB.resetForm( $('#printForm') );
		jsLIB.ajaxCall({
			type: "GET",
			async: false,
			url: jsLIB.rootDir+"rules/listaCompras.php",
			data: { MethodName : 'getData', data : { domains : [ "tipos" ] } },
			success: function(data){
				jsLIB.populateOptions( $("#cmTIPOS"), data.tipos );
				$("#printModal").modal();
			}
		});
	});
	
	$('#btnAdd').click(function(){
		var parameter = {
			domains : [ "tipos", "nomes" ]
		};
		jsLIB.ajaxCall({
			type: "GET",
			async: false,
			url: jsLIB.rootDir+"rules/listaCompras.php",
			data: { MethodName : 'getData', data : parameter },
			success: function(data){
				jsLIB.populateOptions( $("#cmTipo"), data.tipos );
				jsLIB.populateOptions( $("#cmNome"), data.nomes );
			}
		});
		
    $('#divItem').visible(false);
    $('#divCmpl').visible(false);
    $('#divOQue').visible(true);
    $('#divTipoMaterial').visible(false);
		$('#divParaQuem').visible(true);
		$('#divDataQuando').visible(false);
		jsLIB.resetForm( $('#cadListaForm') );
		$("#qtItens").val(1);
		$("#listaModal").attr('action','ADD').modal();
	});

	$('#btnEntrega').click(function(){
		var parameter = {
			domains : [ "tiposEntrega", "nomesEntrega" ]
		};
		jsLIB.ajaxCall({
			async: false,
			type: "GET",
			url: jsLIB.rootDir+"rules/listaCompras.php",
			data: { MethodName : 'getData', data : parameter },
			success: function(data){
				jsLIB.populateOptions( $("#cmTipoMaterial"), data.tipos );
				jsLIB.populateOptions( $("#cmNome"), data.nomes );
			}
		});

    $('#divItem').visible(false);
    $('#divCmpl').visible(false);
    $('#divOQue').visible(false);
    $('#divTipoMaterial').visible(true);
		$('#divParaQuem').visible(true);
		$('#divDataQuando').visible(true);
    jsLIB.resetForm( $('#cadListaForm') );
    $("#cmTipoMaterial").selectpicker('selectAll');
		$('[field=dt_quando]').val( new Date().toFormattedDate() );
		$("#listaModal").attr('action','SET').modal();
	});
	
	$('#btnEdit').click(function(){
		jsLIB.ajaxCall({
			type: "GET",
			url: jsLIB.rootDir+"rules/listaCompras.php",
			data: { MethodName : 'getAttr', data : { id: $(this).attr("id-item") } },
			success: function(data){
				formPopulated = false;
				jsLIB.populateForm( $("#controleForm"), data.attr );
				$("#comprasModal").modal();
				formPopulated = true;
			}
		});
	});
	
	$('#comprasModal .modal-footer').click(function(e){
		e.preventDefault();
		e.stopPropagation();
	});
	$('#comprasModal').on('hidden.bs.modal', function(e){
		ruleBtnEdit(false);
		dataTable.ajax.reload();
	});	

	$('#cmTipo').change(function(){
		var value = $(this).val();
		var visible = value != '';
		if (visible){
			var parameter = {
				key : value,
				domains : [ "itens" ]
			};
			jsLIB.ajaxCall({
				async: false,
				type: "GET",
				url: jsLIB.rootDir+"rules/listaCompras.php",
				data: { MethodName : 'getData', data : parameter },
				success: function(data){
					jsLIB.populateOptions( $("#cmItem"), data.itens );
				}
			});
    }
    $('#divMaterial').visible(false);
    $('#divItem').visible(visible);
    $('#divCmpl').visible(false);
		$('#cmItem').change();
	});
	
	$('#cmItem').change(function(){
		$('#divCmpl').visible( this.options[this.options.selectedIndex].getAttribute('cm') == 'S' );
	});
	
	$("#qtItens").TouchSpin({
		verticalbuttons: true,
		verticalupclass: 'glyphicon glyphicon-plus',
		verticaldownclass: 'glyphicon glyphicon-minus'
	});
	
	$('#btnRedist').click(function(){
		BootstrapDialog.show({
			title: 'Alerta',
			message: 'Confirma redistribuição automática do estoque?',
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
				{ label: 'Sim, desejo redistribuir!',
					icon: 'glyphicon glyphicon-trash',
					cssClass: 'btn-danger',
					autospin: true,
					action: function(dialogRef){
						ruleBtnDelete( false );
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
							type: "GET",
							url: jsLIB.rootDir+"rules/listaCompras.php",
							data: { MethodName : 'distribuirEstoque' },
							success: function(data){
								dataTable.ajax.reload( function(){
									dialogRef.close();
								});
							}
						});
					}
				}
			]
	    });
	});	
	
	$('#btnDel').click(function(){
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
						ruleBtnDelete( false );
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
							waiting: true,
							url: jsLIB.rootDir+"rules/listaCompras.php",
							data: { MethodName : 'delete', data : parameter },
							success: function(){
								dataTable.ajax.reload(function(){
									dialogRef.close();
								});
							}
						});
					}
				}
			]
	    });
	});

	$("#btnGerar").click( function(e) {
		var opt = $(this).attr('report-id');
		var cmpl = jsLIB.getURIFields( $("#printForm") );
		if (opt){
			url = jsLIB.rootDir+'report/'+opt;
			if (cmpl){
				url += ( opt.substr(opt.length-4,4) == ".php" ? "?" : "&")+cmpl;
			}
			window.open(url,'_blank','top=50,left=50,height=750,width=550,menubar=no,status=no,titlebar=no',true);
		}
	});
	
	$('#comprasDatatable tbody').on('click', 'tr', function () {
		$(this).toggleClass('selected');
		ruleBtnDelete();
		ruleBtnEdit();
	});	
	ruleBtnDelete(false);
	ruleBtnEdit(false);

	$('#divDataQuando').datetimepicker({
		locale: 'pt-br',
		language: 'pt-BR',
		format: 'DD/MM/YYYY',
		maskInput: true,
		pickDate: true,
		pickTime: false,
		pickSeconds: false,
		useCurrent: true
	});

	$(".date").mask('00/00/0000');
});

function rulesGeracao( obj ){
	$("[name=rowFilter]").visible(false);
	$("#btnGerar")
		.attr('report-id', obj.val() );
	
	var show = obj.find(":selected").attr('show');
	if (show !== undefined) {
		$("#"+show).visible(true);
	}
	$("#btnGerar").visible( $("#cbListagem").val() !== '' );
}

function ruleBtnDelete( force ){
	var data = dataTable.rows('.selected').data();
	var selected = false;
	for (var i=0;i<data.length;i++){
		selected = (data[i].tp == 'M' && data[0].ip == 'N');
		if (!selected){
			break;
		}
	}
	$("#btnDel").visible( force != undefined ? force : selected );
}

function showBtnEdit(selected){
	$("#btnEdit")
		.attr("id-item",selected)
		.visible( selected != "" );
}

function ruleBtnEdit( force ){
	var data = dataTable.rows('.selected').data();
	var selected = "";
	showBtnEdit(selected);

	if (force == undefined){
		if (data.length == 1 && data[0].ip == 'N'){
			selected = data[0].id;
			jsLIB.ajaxCall({
				waiting: true,
				type: "GET",
				url: jsLIB.rootDir+"rules/listaCompras.php",
				data: { MethodName : 'getAttrPerm', data : { id: selected } },
				success: function(data){
					if (!data || !data.edit){
						selected = "";
					}
					showBtnEdit(selected);
				}
			});
		}
	}
}