<?php
@require_once('functions.php');
@require_once('_core/lib/tcpdf/tcpdf.php');

class MATERIAIS {
	
	public function forceInsert( $arr ){
		CONN::get()->Execute("
			INSERT INTO MAT_HISTORICO(
				ID_CAD_MEMBRO,
				ID_TAB_MATERIAIS,
				DT_ENTREGA,
				COMPL
			) VALUES (?,?,?,?)
		", $arr );
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