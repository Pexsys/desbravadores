$(document).ready(function(){

	$("#capture").click( function() {
		window.location = "readbarcode://barfield";
		//onscan( 'PA05088' );
	});

	$("#myBtnLogout").click(function(){
		logout();
	});
	
	$('[name=tpCapture]').click( function(event){
		var tipoFuncao = $(this).attr('type-fn');
		$("#tipoFuncao").val( tipoFuncao );
		$("#divDatas").visible(tipoFuncao == "APRENDIZADO");
	});
	
	$('[name=toggle-dates]').on("change",function(e) {
		var value = jsLIB.getValueFromField($(this));
		$("#"+$(this).attr("for")).visible( value == 'S' );
	});
	
	jsLIB.resetForm( $('#cadBarCode') );
	
	$(".date").mask('00/00/0000');	
});

function onscan( bardata ) {
	var parameter = {
		brdt : bardata,
		frm: jsLIB.getJSONFields( $('#cadBarCode') )
	};
	sendBarCode( parameter,
		function(data,fx){
			$("#strResultado").html(data.result);
			$("#divResultado").show();			
		},
		function(data,fx){
			$("#strResultado").hide();
		}
	);
}

function logout(){
	jsLIB.ajaxCall({
		async: false,
		url: jsLIB.rootDir+'rules/login.php',
		data: { MethodName : 'logout' },
		success: function( data, jqxhr ) {
			window.location.replace( jsLIB.rootDir+'readdata.php' );
		}
	});
}
