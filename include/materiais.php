<?php
@require_once('functions.php');
@require_once('_core/lib/tcpdf/tcpdf.php');

class MATERIAIS {
	
	public function deleteItemPessoa( $pessoaID, $tabMateriaisID ) {
		$GLOBALS['conn']->Execute("
			DELETE FROM MAT_HISTORICO
			 WHERE ID_CAD_PESSOA = ? 
			   AND ID_TAB_MATERIAIS ? 
		", array( $pessoaID, $tabMateriaisID ) );		
	}
	
	public function deleteByID( $id ) {
		 $GLOBALS['conn']->Execute("DELETE FROM MAT_HISTORICO WHERE ID = ?", array($id) );
	}
	
	public function forceInsert( $arr ){
		$GLOBALS['conn']->Execute("
			INSERT INTO MAT_HISTORICO(
				ID_CAD_PESSOA,
				ID_TAB_MATERIAIS,
				DT_ENTREGA
			) VALUES (?,?,?)
		", $arr );
	}
	
	public function insertItemPessoa( $cd, $pessoaID, $dt ) {
		$r = $GLOBALS['conn']->Execute("
			SELECT ID
			  FROM TAB_MATERIAIS
			 WHERE CD = ?
		", array($cd) );
		if (!$r->EOF):
			$item = $r->fields["ID"];

			$r2 = $GLOBALS['conn']->Execute("
				SELECT 1
				  FROM MAT_HISTORICO
				 WHERE ID_CAD_PESSOA = ?
				   AND ID_TAB_MATERIAIS = ?
			", array( $pessoaID, $item ) );
			if ($r2->EOF):
				$this->forceInsert(
					array(
						$pessoaID,
						$item,
						$dt
					) 
				);
			endif;
		endif;
	}	

	public function addItemEstoque( $matID, $qtd ){
		if ( $qtd > 0 ):
			$GLOBALS['conn']->Execute("
				UPDATE TAB_MATERIAIS 
				SET QT_EST = QT_EST + ?
				WHERE ID = ?
			", array($qtd, $matID) );
		endif;
	}
	
	public function setQtdEstoque( $matID, $qtd ){
		$GLOBALS['conn']->Execute("
			UPDATE TAB_MATERIAIS
			SET QT_EST = ?
			WHERE ID = ?
		", array($qtd, $matID) );
	}
}
?>