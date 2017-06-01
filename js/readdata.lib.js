function sendBarCode( parameter, cbSuccess, cbError ) {
	jsLIB.ajaxCall( 
		true, 
		jsLIB.rootDir+'rules/readdata.php', 
		{ MethodName : 'barcode', data : parameter }, 
		function( data, jqxhr ) {
			if ( data.logged == true ) {
				if (typeof(cbSuccess) == "function") {
					cbSuccess(data,jqxhr);
				}
			} else if (data.logout == true) {
				logout();
			} else {
				if (typeof(cbError) == "function") {
					cbError(data,jqxhr);
				}
				msgScanBarError(jqxhr, data.result);
			}
		}, 
		msgScanBarError 
	);
}

function msgScanBarError( jqxhr, errorMessage ) {
	BootstrapDialog.show({
		title: 'Erro',
		message: errorMessage == null ? 'Código Inválido!' : errorMessage,
		type: BootstrapDialog.TYPE_DANGER,
		size: BootstrapDialog.SIZE_SMALL,
		draggable: true,
		closable: true,
		closeByBackdrop: false,
		closeByKeyboard: false,
		buttons: [{
			label: 'Fechar',
			cssClass: 'btn-danger',
			action: function(dialogRef){
				dialogRef.close();
			}
		}]	
	});	
}