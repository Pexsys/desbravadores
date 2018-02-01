<?php
class TAGS {

	public static function deleteFila() {
		CONN::get()->Execute("TRUNCATE TABLE TMP_PRINT_TAGS");
	}

	public static function deleteFilaIDS( $ids ) {
		CONN::get()->Execute("DELETE FROM TMP_PRINT_TAGS WHERE ID IN ($ids)");
	}

	public static function deleteByID( $id ) {
		 CONN::get()->Execute("DELETE FROM CAD_COMPRAS WHERE ID = ?", array($id) );
	}

	public static function forceInsert( $arr ){
		CONN::get()->Execute("
			INSERT INTO TMP_PRINT_TAGS (
				ID_CAD_MEMBRO,
				TP,
				MD,
				ID_TAB_APREND,
				BC
			) VALUES (?,?,?,?,?)
		", $arr );
	}

	public static function insertItemTag( $tp, $cadMembroID, $aprendID = null ) {
		$option = PATTERNS::getBars()->getTagByID($tp);

		//SE NAO APONTADO APRENDIZADO, PROCURA MAIOR APRENDIZADO AVALIADO
		if (empty($aprendID) || is_null($aprendID)):
			$r = CONN::get()->Execute("
			SELECT * FROM (
				SELECT '1', MAX(ta.ID) AS ID_TAB_APREND
				  FROM CAD_MEMBRO cm
			INNER JOIN APR_HISTORICO ah ON (ah.ID_CAD_PESSOA = cm.ID_CAD_PESSOA)
			INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = ah.ID_TAB_APREND AND ta.TP_ITEM = 'CL')
				 WHERE YEAR(ah.DT_AVALIACAO) = YEAR(NOW())
				   AND cm.ID = ?
			UNION
				SELECT '2', MAX(ta.ID) AS ID_TAB_APREND
				FROM CAD_MEMBRO cm
		INNER JOIN APR_HISTORICO ah ON (ah.ID_CAD_PESSOA = cm.ID_CAD_PESSOA)
		INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = ah.ID_TAB_APREND AND ta.TP_ITEM = 'CL')
			WHERE YEAR(ah.DT_INICIO) = YEAR(NOW())
				AND cm.ID = ?
			UNION
				SELECT '3', MAX(ta.ID) AS ID_TAB_APREND
				FROM CAD_MEMBRO cm
		INNER JOIN APR_HISTORICO ah ON (ah.ID_CAD_PESSOA = cm.ID_CAD_PESSOA)
		INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = ah.ID_TAB_APREND AND ta.TP_ITEM = 'CL')
			WHERE ah.DT_AVALIACAO IS NOT NULL
				AND cm.ID = ?
			) x
			WHERE x.ID_TAB_APREND IS NOT NULL
			ORDER BY 1
			", array($cadMembroID,$cadMembroID,$cadMembroID) );
			if (!$r->EOF):
				$aprendID = $r->fields["ID_TAB_APREND"];
			endif;
		endif;

		$arr = array( $tp, $option["md"], $cadMembroID );
		if ( !is_null($aprendID) ):
			$arr[] = $aprendID;
		endif;

		$r = CONN::get()->Execute("
			SELECT 1
			  FROM TMP_PRINT_TAGS
			 WHERE TP = ?
			   AND MD = ?
			   AND ID_CAD_MEMBRO = ?
			   AND ID_TAB_APREND ". (!is_null($aprendID) ? "= ?" : "IS NULL") ."
		", $arr );
		if ($r->EOF):
			$r = CONN::get()->Execute("SELECT ID_MEMBRO FROM CAD_MEMBRO WHERE ID = ?", $cadMembroID );

			$barCODE = PATTERNS::getBars()->encode(array(
				"id" => $tp,
				"fi" => $aprendID,
				"ni" => $r->fields["ID_MEMBRO"]
			));

			TAGS::forceInsert(
				array(
					$cadMembroID,
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
