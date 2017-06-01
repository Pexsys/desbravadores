function showDetailEspec( objDetail, parameters ){
	var espec = jsLIB.ajaxCall( undefined, jsLIB.rootDir+"rules/painelAprendizado.php", { MethodName : 'getEspec', data : parameters }, 'RETURN' );
	if (espec.detail){
		$(objDetail).html(espec.detail);
	}
}

function showDetailEspecPeople( objDetail, parameters ){
	var espec = jsLIB.ajaxCall( undefined, jsLIB.rootDir+"rules/painelAprendizado.php", { MethodName : 'getEspecPeople', data : parameters }, 'RETURN' );
	if (espec.people){
		$(objDetail).html(espec.people);
	}
}

function showDetailClass( objDetail, parameters ){
	var classes = jsLIB.ajaxCall( undefined, jsLIB.rootDir+"rules/painelAprendizado.php", { MethodName : 'getClasses', data : parameters }, 'RETURN' );
	if (classes.detail){
		$(objDetail).html(classes.detail);
	}
}

function showDetailClassReq( objDetail, parameters ){
	var detail = jsLIB.ajaxCall( undefined, jsLIB.rootDir+"rules/painelAprendizado.php", { MethodName : 'getPendentes', data : parameters }, 'RETURN' );
	if (detail.pend){
		$(objDetail).html(detail.pend);
	}
}