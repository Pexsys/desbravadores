jsLIB.consultaCEP = function( objParam ){
	jsLIB.ajaxCallNew({
		waiting : false,
		async: true,
		url: jsLIB.rootDir+'rules/consultaECT.php',
		data: { MethodName : 'consultaCEP', data : { cep: objParam.value } },
		callBackSucess: objParam.success
	});
}