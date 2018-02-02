<?php
@require_once('functions.php');
@require_once('_core/lib/tcpdf/tcpdf.php');

class MATERIAIS {
	
	public function deleteItemPessoa( $pessoaID, $tabMateriaisID ) {
		CONN::get()->Execute("
			DELETE FROM MAT_HISTORICO
			 WHERE ID_CAD_PESSOA = ? 
			   AND ID_TAB_MATERIAIS ? 
		", array( $pessoaID, $tabMateriaisID ) );		
	}
	
	public function deleteByID( $id ) {
		 CONN::get()->Execute("DELETE FROM MAT_HISTORICO WHERE ID = ?", array($id) );
	}
	
	public function forceInsert( $arr ){
		CONN::get()->Execute("
			INSERT INTO MAT_HISTORICO(
				ID_CAD_PESSOA,
				ID_TAB_MATERIAIS,
				DT_ENTREGA
			) VALUES (?,?,?)
		", $arr );
	}
	
	public function insertItemPessoa( $cd, $pessoaID, $dt ) {
		$r = CONN::get()->Execute("
			SELECT ID
			  FROM TAB_MATERIAIS
			 WHERE CD = ?
		", array($cd) );
		if (!$r->EOF):
			$item = $r->fields["ID"];

			$r2 = CONN::get()->Execute("
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
			CONN::get()->Execute("
				UPDATE TAB_MATERIAIS 
				SET QT_EST = QT_EST + ?
				WHERE ID = ?
			", array($qtd, $matID) );
		endif;
	}
	
	public function setQtdEstoque( $matID, $qtd ){
		CONN::get()->Execute("
			UPDATE TAB_MATERIAIS
			SET QT_EST = ?
			WHERE ID = ?
		", array($qtd, $matID) );
	}
}
?>