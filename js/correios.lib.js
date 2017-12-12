jsLIB.consultaCEP = function( objParam ){
	jsLIB.ajaxCall({
		waiting : false,
		async: true,
		type: "GET", 
		url: jsLIB.rootDir+'rules/consultaECT.php',
		data: { MethodName : 'consultaCEP', data : { cep: objParam.value } },
		callBackSucess: objParam.success
	});
}