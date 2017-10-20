<?php
class PROFILE {

	public function deleteAllByUserID( $userID ) {
		$GLOBALS['conn']->Execute("
			DELETE FROM CAD_USU_PERFIL
			 WHERE ID_CAD_USUARIOS = ? 
		", array( $userID ) );		
	}

	public function deleteAllByPessoaID( $pessoaID ){
		$rs = $GLOBALS['conn']->Execute("
			SELECT ID FROM CAD_USU_PERFIL
			WHERE ID_CAD_PESSOA = ?
		", array( $userCD ) );
		if (!$rs->EOF):
			$this->deleteAllByUserID( $rs->fields["ID"] );
		endif;
	}

	public function deleteByUserID( $userID, $profileID ) {
		$GLOBALS['conn']->Execute("
			DELETE FROM CAD_USU_PERFIL
			 WHERE ID_CAD_USUARIOS = ? 
			   AND ID_PERFIL = ? 
		", array( $userID, $profileID ) );		
	}

	public function deleteByPessoaID( $pessoaID, $profileID ){
		$rs = $GLOBALS['conn']->Execute("
			SELECT ID FROM CAD_USU_PERFIL
			WHERE ID_CAD_PESSOA = ?
		", array( $userCD ) );
		if (!$rs->EOF):
			$this->deleteByUserID( $rs->fields["ID"], $profileID );
		endif;
	}

	public function insert( $userID, $profileID ) {
		$rs = $GLOBALS['conn']->Execute("
			SELECT 1 FROM CAD_USU_PERFIL
			WHERE ID_CAD_USUARIOS = ? 
			  AND ID_PERFIL = ?
		", array( $userID, $profileID ) );
		if ($rs->EOF):
			$GLOBALS['conn']->Execute("
				INSERT INTO CAD_USU_PERFIL (
					ID_CAD_USUARIOS,
					ID_PERFIL
				) VALUES (
					?,
					?
				) 
			", array( $userID, $profileID ) );
		endif;
	}

	public function rulesCargos( $pessoaID, $cargoCD, $cargo2CD ) {
		/*
		0 TODOS - LOGIN
		1 ADMINISTRADOR
		2 SECRETARIA
		3 REGIONAL
		4 DIRETORIA
		5 INSTRUTOR
		6 CONSELHEIRO
		7 TESOURARIA
		8 SECRETARIA ASSOCIADA
		12 GUEST
		9 DIRETORES ASSOCIADOS
		10 RESPONSAVEL LEGAL
		11 RESPONSAVEL FINANCEIRO
		*/

		$arr = array();

		//DIRETORIA
		if ( fStrStartWith($cargoCD,"2-") || fStrStartWith($cargo2CD,"2-") ):
			$arr[] = 4;

			//DIRETOR
			if ( $cargoCD == "2-01-00" || $cargo2CD == "2-01-00" ):
				$arr[] = 1;

			//DIRETORES ASSOCIADOS
			elseif ( $cargoCD == "2-01-01" || $cargo2CD == "2-01-01" ):
				$arr[] = 9;

			//SECRETARIA
			elseif ( $cargoCD == "2-02-00" || $cargo2CD == "2-02-00" ):
				$arr[] = 2;

			//SECRETARIA ASSOCIADA
			elseif ( $cargoCD == "2-02-01" || $cargo2CD == "2-02-01" ):
				$arr[] = 8;

			//TESOURARIA
			elseif ( fStrStartWith($cargoCD,"2-03") || fStrStartWith($cargo2CD,"2-03") ):
				$arr[] = 7;

			//INSTRUTORES
			elseif ( fStrStartWith($cargoCD,"2-04") || fStrStartWith($cargo2CD,"2-04") ):
				$arr[] = 5;

			//CONSELHEIROS
			elseif ( fStrStartWith($cargoCD,"2-07") || fStrStartWith($cargo2CD,"2-07") ):
				$arr[] = 6;

			endif;

			

		endif;
	}
}
?>