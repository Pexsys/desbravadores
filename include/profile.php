<?php
@require_once('functions.php');

class PROFILE {
	
	public function fGetPerfil( $cd = NULL ) {
		$arr = array();
		$query = "SELECT DISTINCT td.id, td.cd, td.iconm, td.iconf, td.ds_menu, tf.ds_url
		    FROM CAD_USU_PERFIL cpp
	      INNER JOIN TAB_PERFIL_ITEM tpi ON ( tpi.id_tab_perfil = cpp.id_perfil AND tpi.dh_ini_valid <= NOW() )
	      INNER JOIN TAB_DASHBOARD td ON ( td.id = tpi.id_tab_dashboard )
	       LEFT JOIN TAB_FUNCTION tf ON ( tf.id = td.id_tab_function )
		   WHERE cpp.id_cad_usuarios = ?";
		if ( isset($cd) && !empty($cd) ):
			$query .= " AND td.cd LIKE '$cd.%' AND LENGTH(td.cd) = LENGTH('$cd')+3";
		else:
			$query .= " AND LENGTH(td.cd) = 2";
		endif;
		$query .= " ORDER BY td.cd";
		$result = $GLOBALS['conn']->Execute($query, array($_SESSION['USER']['id_usuario']) );
		while (!$result->EOF):
			$child = $this->fGetPerfil( $result->fields['cd'] );
			$arr[ $result->fields['id'] ] = array(
					"opt"	 => ($result->fields['ds_menu']),
					"ico"	 => ( $_SESSION['USER']['sexo'] == "F" && isset($result->fields['iconf']) ? $result->fields['iconf'] :  $result->fields['iconm'] ),
					"active" => false
			);
			if ( count( $child ) > 0 ):
				$arr[ $result->fields['id'] ]["child"] = $child;
			else:
				$arr[ $result->fields['id'] ]["url"] = $result->fields['ds_url'];
			endif;
			$result->MoveNext();
		endwhile;
		return $arr;
	}
	
	public function verificaPerfil(){
		$temPerfil = isset($_SESSION['USER']['ssid']);
		if (!$temPerfil):
			session_destroy();
			header("Location: ".$GLOBALS['pattern']->getVD()."index.php");
			exit;
		endif;
	}
	
	public function fSetSessionLogin( $result ){
		session_start();
		$_SESSION['USER']['ssid']			= session_id();
		$_SESSION['USER']['cd_usuario']		= $result->fields['CD_USUARIO'];
		$_SESSION['USER']['ds_usuario']		= $result->fields['DS_USUARIO'];
		$_SESSION['USER']['id_usuario']		= $result->fields['ID_USUARIO'];
		$_SESSION['USER']['id_cad_pessoa']	= $result->fields['ID_CAD_PESSOA'];
		$_SESSION['USER']['sexo']			= (!is_null($result->fields['TP_SEXO_RESP']) ? $result->fields['TP_SEXO_RESP'] : $result->fields['TP_SEXO']);
	}

	public function deleteAllByUserID( $userID ) {
		$GLOBALS['conn']->Execute("
			DELETE FROM CAD_USU_PERFIL
			 WHERE ID_CAD_USUARIOS = ? 
		", array( $userID ) );		
	}

	public function deleteAllByPessoaID( $pessoaID ){
		$rs = $GLOBALS['conn']->Execute("
			SELECT ID_USUARIO FROM CAD_USUARIOS
			WHERE ID_CAD_PESSOA = ?
		", array( $pessoaID ) );
		if (!$rs->EOF):
			$this->deleteAllByUserID( $rs->fields["ID_USUARIO"] );
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
			SELECT ID_USUARIO
			FROM CAD_USUARIOS
			WHERE ID_CAD_PESSOA = ? 
		", array( $pessoaID ) );
		if (!$rs->EOF):
			$this->deleteByUserID( $rs->fields["ID_USUARIO"], $profileID );
		endif;
	}

	 public function insertByPessoaID( $pessoaID, $profileID ) {
		$rs = $GLOBALS['conn']->Execute("
			SELECT ID_USUARIO
			  FROM CAD_USUARIOS
			 WHERE ID_CAD_PESSOA = ? 
		", array( $pessoaID ) );
		if (!$rs->EOF):
			$this->insert( $rs->fields["ID_USUARIO"], $profileID );
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
		//0 TODOS - LOGIN
		//1 ADMINISTRADOR
		//2 SECRETARIA
		//3 REGIONAL
		//4 DIRETORIA
		//5 INSTRUTOR
		//6 CONSELHEIRO
		//7 TESOURARIA
		//8 SECRETARIA ASSOCIADA
		//9 DIRETORES ASSOCIADOS
		//10 RESPONSAVEL LEGAL
		//11 RESPONSAVEL FINANCEIRO
		//12 GUEST

		$arr = array( 0 );

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

			$this->deleteAllByPessoaID($pessoaID);
			foreach ($arr as $k => $l):
				$this->insertByPessoaID( $pessoaID, $l );
			endforeach;
		endif;
	 }
}

//$PROFILE = new PROFILE();
//fConnDB();
//$PROFILE->rulesCargos( 40, "2-02-00", null );
?>