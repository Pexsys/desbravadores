jsLIB.consultaCEP = function( objParam ){
	jsLIB.ajaxCall({
		type: "GET", 
		url: jsLIB.rootDir+'rules/consultaECT.php',
		data: { MethodName : 'consultaCEP', data : { cep: objParam.value } },
		success: objParam.success
	});
}