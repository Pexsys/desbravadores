<?php
function verificaRespByCPF( $cpf ) {
	$noFormat = str_replace("-","",str_replace(".","",$cpf));
	
	$result = $GLOBALS['conn']->Execute("
		SELECT cp.*
		FROM CAD_PESSOA cp
		INNER JOIN CAD_RESP_LEGAL crl ON (crl.ID_PESSOA_RESP = cp.ID)
		WHERE REPLACE(REPLACE(cp.NR_CPF,'.',''),'-','') = ?
	", array( $noFormat ) );
	if (!$result->EOF):
		return $result->fields;
	endif;
	return null;
}

function verificaRespByID( $pessoaRespID ) {
	$result = $GLOBALS['conn']->Execute("
		SELECT *
		  FROM CAD_RESP_LEGAL
		 WHERE ID_PESSOA_RESP = ?
	", array( $pessoaRespID ) );
	if (!$result->EOF):
		return $result->fields;
	endif;
	return null;
}

function existeMenorByRespID( $pessoaRespID ) {
	$result = $GLOBALS['conn']->Execute("
		SELECT *
		  FROM CON_ATIVOS
		 WHERE ID_PESSOA_RESP = ?
	", array( $pessoaRespID ) );
	return !$result->EOF;
}
?>