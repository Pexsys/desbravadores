<?php
function verificaRespByCPF( $cpf ) {
	$noFormat = str_replace("-","",str_replace(".","",$cpf));
	
	$result = $GLOBALS['conn']->Execute("
		SELECT * 
		  FROM CAD_RESP
		 WHERE REPLACE(REPLACE(CPF_RESP,'.',''),'-','') = ?
	", array( $noFormat ) );
	if (!$result->EOF):
		return $result->fields;
	endif;
	return null;
}

function verificaRespByID( $id ) {
	$result = $GLOBALS['conn']->Execute("
		SELECT *
		  FROM CAD_RESP
		 WHERE ID = ?
	", array( $id ) );
	if (!$result->EOF):
		return $result->fields;
	endif;
	return null;
}

function existeMenorByRespID( $id ) {
	$result = $GLOBALS['conn']->Execute("
		SELECT *
		  FROM CON_ATIVOS
		 WHERE ID_RESP = ?
	", array( $id ) );
	return !$result->EOF;
}
?>