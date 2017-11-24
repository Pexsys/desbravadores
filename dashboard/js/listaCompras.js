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
			type	: "POST",
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
		order: [ 2, 'asc' ],
		columns: [
			{	data: "tp",
				visible: false
			},
			{	data: "id",
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
				render: function (data) {
					return (data == "S" ? "SIM" : "N&Atilde;O" );
				}
			},
			{	data: "ie",
				width: "5%",
				sortable: true,
				render: function (data) {
					return (data == "S" ? "SIM" : "N&Atilde;O" );
				}
			}
		],
		fnRowCallback: function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
			if ( aData.ie == "S" ) {
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
				var attr = jsLIB.ajaxCall( false, jsLIB.rootDir+"rules/listaCompras.php", { MethodName : 'setAttr', data : parameters }, 'RETURN' );
				if (field == 'fg_entregue' && value == 'S' && !$("#fgCompra").prop('checked') ){
					$("#fgCompra").prop('checked', true).triggerHandler('change');
				} else if (field == 'fg_compra' && value == 'N' && $("#fgEntregue").prop('checked') ){
					$("#fgEntregue").prop('checked', false).triggerHandler('change');
				}
				if ( attr.est.close ) {
				    $("#comprasModal").modal('hide');
				}
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
			jsLIB.ajaxCall( false, jsLIB.rootDir+"rules/listaCompras.php", { MethodName : 'addCompras', data : parameter },
				function(){
					dataTable.ajax.reload();
					$("#listaModal").modal('hide');
				}
			);

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
						jsLIB.ajaxCall( false, jsLIB.rootDir+"rules/listaCompras.php", { MethodName : 'process' } );
						dataTable.ajax.reload();
						dialogRef.close();
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
		$("#printModal").modal();
	});
	
	$('#btnAdd').click(function(){
		var parameter = {
			domains : [ "tipos", "nomes" ]
		};
		var cg = jsLIB.ajaxCall( false, jsLIB.rootDir+"rules/listaCompras.php", { MethodName : 'getData', data : parameter }, 'RETURN' );
		jsLIB.populateOptions( $("#cmTipo"), cg.tipos );
		jsLIB.populateOptions( $("#cmNome"), cg.nomes );
		
		$('#divItem').visible(false);
		$('#divOQue').visible(true);
		$('#divParaQuem').visible(true);
		jsLIB.resetForm( $('#cadListaForm') );
		$("#qtItens").val(1);
		$("#listaModal").attr('action','ADD').modal();
	});

	$('#btnEntrega').click(function(){
		var parameter = {
			domains : [ "nomesEntrega" ]
		};
		var cg = jsLIB.ajaxCall( false, jsLIB.rootDir+"rules/listaCompras.php", { MethodName : 'getData', data : parameter }, 'RETURN' );
		jsLIB.populateOptions( $("#cmNome"), cg.nomes );
		
		$('#divItem').visible(false);
		$('#divOQue').visible(false);
		$('#divParaQuem').visible(true);
		jsLIB.resetForm( $('#cadListaForm') );
		$("#listaModal").attr('action','SET').modal();
	});
	
	$('#btnEdit').click(function(){
		var es = jsLIB.ajaxCall( false, jsLIB.rootDir+"rules/listaCompras.php", { MethodName : 'getAttr', data : { id: $(this).attr("id-item") } }, 'RETURN' );
		formPopulated = false;
		jsLIB.populateForm( $("#controleForm"), es.attr );
		$("#comprasModal").modal();
		formPopulated = true;
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
			var cg = jsLIB.ajaxCall( false, jsLIB.rootDir+"rules/listaCompras.php", { MethodName : 'getData', data : parameter }, 'RETURN' );
			jsLIB.populateOptions( $("#cmItem"), cg.itens );
		}
		$('#divItem').visible(visible);
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
						jsLIB.ajaxCall( false, jsLIB.rootDir+"rules/listaCompras.php", { MethodName : 'distribuirEstoque' },
							function(){
								dataTable.ajax.reload();
								dialogRef.close();
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
						jsLIB.ajaxCall( false, jsLIB.rootDir+"rules/listaCompras.php", { MethodName : 'delete', data : parameter }, function(){
							dataTable.ajax.reload();
							dialogRef.close();
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
		selected = (data[i].tp == 'M');
		if (!selected){
			break;
		}
	}
	$("#btnDel").visible( force != undefined ? force : selected );
}

function ruleBtnEdit( force ){
	var data = dataTable.rows('.selected').data();
	var selected = "";
	if (force == undefined){
		if (data.length == 1){
			selected = data[0].id;
			var es = jsLIB.ajaxCall( false, jsLIB.rootDir+"rules/listaCompras.php", { MethodName : 'getAttrPerm', data : { id: selected } }, 'RETURN' );
			if (!es || !es.edit){
				selected = "";
			}
		}
	}
	$("#btnEdit")
		.attr("id-item",selected)
		.visible( selected != "" );
}