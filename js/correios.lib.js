jsLIB.consultaCEP = function( cep ){
	return jsLIB.ajaxCall( false, jsLIB.rootDir+'rules/consultaECT.php', { MethodName : 'consultaCEP', data : { cep: cep } }, 'RETURN');
}