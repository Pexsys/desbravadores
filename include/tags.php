<?php
@require_once('functions.php');

class TAGS {
	
	public function deleteFila() {
		$GLOBALS['conn']->Execute("TRUNCATE TABLE TMP_PRINT_TAGS");
	}
	
	public function deleteFilaIDS( $ids ) {
		$GLOBALS['conn']->Execute("DELETE FROM TMP_PRINT_TAGS WHERE ID IN ($ids)");
	}
	
	public function deleteByID( $id ) {
		 $GLOBALS['conn']->Execute("DELETE FROM CAD_COMPRAS_PESSOA WHERE ID = ?", array($id) );
	}
	
	public function forceInsert( $arr ){
		$GLOBALS['conn']->Execute("
			INSERT INTO TMP_PRINT_TAGS (
				ID_CAD_PESSOA,
				TP,
				MD,
				ID_TAB_APREND,
				BC						
			) VALUES (?,?,?,?,?)
		", $arr );
	}
	
	public function insertItemTag( $tp, $pessoaID, $aprendID = null ) {
		$option = $GLOBALS['pattern']->getOptionsTag($tp);

		//SE NAO APONTADO APRENDIZADO, PROCURA MAIOR APRENDIZADO AVALIADO
		if (empty($aprendID) || is_null($aprendID)):
			$r = $GLOBALS['conn']->Execute("
			SELECT * FROM (
				SELECT '1', MAX(ta.ID) AS ID_TAB_APREND
				  FROM CON_ATIVOS ca
			INNER JOIN APR_HISTORICO ah ON (ah.ID_CAD_PESSOA = ca.ID)
			INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = ah.ID_TAB_APREND AND ta.TP_ITEM = 'CL')
				 WHERE YEAR(ah.DT_AVALIACAO) = YEAR(NOW()) 
				   AND ca.ID = ?
			UNION 
				SELECT '2', MAX(ta.ID) AS ID_TAB_APREND
				FROM CON_ATIVOS ca
		INNER JOIN APR_HISTORICO ah ON (ah.ID_CAD_PESSOA = ca.ID)
		INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = ah.ID_TAB_APREND AND ta.TP_ITEM = 'CL')
			WHERE YEAR(ah.DT_INICIO) = YEAR(NOW())
				AND ca.ID = ?
			UNION 
				SELECT '3', MAX(ta.ID) AS ID_TAB_APREND
				FROM CON_ATIVOS ca
		INNER JOIN APR_HISTORICO ah ON (ah.ID_CAD_PESSOA = ca.ID)
		INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = ah.ID_TAB_APREND AND ta.TP_ITEM = 'CL')
			WHERE ah.DT_AVALIACAO IS NOT NULL
				AND ca.ID = ?
			) x
			WHERE x.ID_TAB_APREND IS NOT NULL
			ORDER BY 1
			", array($pessoaID,$pessoaID,$pessoaID) );
			if (!$r->EOF):
				$aprendID = $r->fields["ID_TAB_APREND"];
			endif;
		endif;

		$arr = array( $tp, $option["md"], $pessoaID );
		if ( !is_null($aprendID) ):
			$arr[] = $aprendID;
		endif;

		$r = $GLOBALS['conn']->Execute("
			SELECT 1
			  FROM TMP_PRINT_TAGS
			 WHERE TP = ?
			   AND MD = ?
			   AND ID_CAD_PESSOA = ?
			   AND ID_TAB_APREND ". (!is_null($aprendID) ? "= ?" : "IS NULL") ."
		", $arr );
		if ($r->EOF):
			$pes = fStrZero(base_convert($pessoaID,10,36),3);
			$barCODE = mb_strtoupper("P$tp". (is_null($aprendID) ? "00" : fStrZero(base_convert($aprendID,10,36),2) ) . $pes);
			$this->forceInsert(
				array(
					$pessoaID,
					$tp,
					$option["md"],
					$aprendID,
					$barCODE
				)
			);
		endif;
	}	
}
?>