<?php
function responsavelAtivo($pessoaRespID) {
	$result = CONN::get()->execute("
		SELECT 1
		  FROM CON_ATIVOS 
		 WHERE ID_CAD_PESSOA = ?
	", array( $pessoaRespID ) );
	return (!$result->EOF);
}

function verificaRespByCPF( $cpf ) {
	$result = CONN::get()->execute("
		SELECT DISTINCT cp.*, rl.DS_TP
		  FROM CON_PESSOA cp
	 LEFT JOIN CAD_RESP_LEGAL rl ON (rl.ID_PESSOA_RESP = cp.ID_CAD_PESSOA)
		 WHERE (cp.IDADE_ANO >= 18 OR cp.IDADE_ANO IS NULL)
		   AND cp.NR_CPF = ?
	", array( fClearBN($cpf) ) );
	if (!$result->EOF):
		return $result->fields;
	endif;
	return null;
}

function verificaRespByID( $pessoaRespID, $pessoaID ) {
	$result = CONN::get()->execute("
		SELECT 
			cp.ID AS ID_CAD_PESSOA,
			rl.DS_TP,
			cp.NM,
			cp.TP_SEXO,
			cp.NR_DOC,
			cp.NR_CPF,
			cp.FONE_CEL,
			cp.EMAIL
		FROM CAD_PESSOA cp
		LEFT JOIN CAD_RESP_LEGAL rl ON (rl.ID_PESSOA_RESP = cp.ID AND rl.ID_CAD_PESSOA = ?)
		WHERE cp.ID = ?
	", array( $pessoaID, $pessoaRespID ) );
	if (!$result->EOF):
		return $result->fields;
	endif;
	return null;
}

function existeMenorByRespID( $pessoaRespID ) {
	$result = CONN::get()->execute("
		SELECT *
		  FROM CON_ATIVOS
		 WHERE ID_PESSOA_RESP = ?
	", array( $pessoaRespID ) );
	return !$result->EOF;
}
?>
