<?php
class RESPONSAVEL {

	public static function verificaRespByCPF( $cpf ) {
		$result = $GLOBALS['conn']->Execute("
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

	public static function verificaRespByID( $pessoaRespID, $pessoaID ) {
		$result = $GLOBALS['conn']->Execute("
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

	public static function existeMenorByRespID( $pessoaRespID ) {
		$result = $GLOBALS['conn']->Execute("
			SELECT *
			  FROM CON_ATIVOS
			 WHERE ID_PESSOA_RESP = ?
		", array( $pessoaRespID ) );
		return !$result->EOF;
	}

}
?>
