<?php
class PROFILE {
	
	public static function fGetProfiles( $cd = NULL ) {
		$arr = array();
		$query = "SELECT DISTINCT td.id, td.cd, td.iconm, td.iconf, td.ds_menu, tf.ds_url
		    FROM CAD_USU_PERFIL cpp
	      INNER JOIN TAB_PERFIL_ITEM tpi ON ( tpi.id_tab_perfil = cpp.id_perfil AND tpi.dh_ini_valid <= NOW() )
	      INNER JOIN TAB_DASHBOARD td ON ( td.id = tpi.id_tab_dashboard )
	       LEFT JOIN TAB_FUNCTION tf ON ( tf.id = td.id_tab_function )
		   WHERE cpp.id_CAD_USUARIO = ?";
		if ( isset($cd) && !empty($cd) ):
			$query .= " AND td.cd LIKE '$cd.%' AND LENGTH(td.cd) = LENGTH('$cd')+3";
		else:
			$query .= " AND LENGTH(td.cd) = 2";
		endif;
		$query .= " ORDER BY td.cd";
		$result = $GLOBALS['conn']->Execute($query, array($_SESSION['USER']['id']) );
		while (!$result->EOF):
			$child = PROFILE::fGetProfiles( $result->fields['cd'] );
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
	
	public static function verificaPerfil(){
		$temPerfil = isset($_SESSION['USER']['ssid']);
		if (!$temPerfil):
			session_destroy();
			header("Location: ".$GLOBALS['pattern']->getVD()."index.php");
			exit;
		endif;
	}
	
	public static function fSetSessionLogin( $result ){
		session_start();
		$_SESSION['USER']['ssid']			= session_id();
		$_SESSION['USER']['cd_usuario']		= $result->fields['CD_USUARIO'];
		$_SESSION['USER']['ds_usuario']		= $result->fields['DS_USUARIO'];
		$_SESSION['USER']['id']				= $result->fields['ID'];
		$_SESSION['USER']['id_cad_membro']	= $result->fields['ID_CAD_MEMBRO'];
		$_SESSION['USER']['id_clube']		= $result->fields['ID_CLUBE'];
		$_SESSION['USER']['id_membro']		= $result->fields['ID_MEMBRO'];
		$_SESSION['USER']['id_cad_pessoa']	= $result->fields['ID_CAD_PESSOA'];
		$_SESSION['USER']['sexo']			= $result->fields['TP_SEXO'];
	}

	public static function deleteAllByUserID( $userID ) {
		$GLOBALS['conn']->Execute("
			DELETE FROM CAD_USU_PERFIL
			 WHERE ID_CAD_USUARIO = ? 
		", array( $userID ) );		
	}

	public static function deleteAllByPessoaID( $pessoaID ){
		$rs = $GLOBALS['conn']->Execute("
			SELECT ID 
			FROM CAD_USUARIO
			WHERE ID_CAD_PESSOA = ?
		", array( $pessoaID ) );
		if (!$rs->EOF):
			PROFILE::deleteAllByUserID( $rs->fields["ID"] );
		endif;
	}

	public static function deleteByUserID( $userID, $profileID ) {
		$GLOBALS['conn']->Execute("
			DELETE FROM CAD_USU_PERFIL
			 WHERE ID_CAD_USUARIO = ? 
			   AND ID_PERFIL = ? 
		", array( $userID, $profileID ) );		
	}

	 public static function deleteByPessoaID( $pessoaID, $profileID ){
		$rs = $GLOBALS['conn']->Execute("
			SELECT ID
			FROM CAD_USUARIO
			WHERE ID_CAD_PESSOA = ? 
		", array( $pessoaID ) );
		if (!$rs->EOF):
			PROFILE::deleteByUserID( $rs->fields["ID"], $profileID );
		endif;
	}

	 public static function insertByPessoaID( $pessoaID, $profileID ) {
		$rs = $GLOBALS['conn']->Execute("
			SELECT ID
			  FROM CAD_USUARIO
			 WHERE ID_CAD_PESSOA = ? 
		", array( $pessoaID ) );
		if (!$rs->EOF):
			PROFILE::insert( $rs->fields["ID"], $profileID );
		endif;
	}

	 public static function insert( $userID, $profileID ) {
		$rs = $GLOBALS['conn']->Execute("
			SELECT 1 FROM CAD_USU_PERFIL
			WHERE ID_CAD_USUARIO = ? 
			  AND ID_PERFIL = ?
		", array( $userID, $profileID ) );
		if ($rs->EOF):
			$GLOBALS['conn']->Execute("
				INSERT INTO CAD_USU_PERFIL (
					ID_CAD_USUARIO,
					ID_PERFIL
				) VALUES (
					?,
					?
				) 
			", array( $userID, $profileID ) );
		endif;
	}

	public static function applyCargos( $pessoaID, $cargoCD, $cargo2CD ) {
		$rules = PROFILE::rulesCargos( $pessoaID, $cargoCD, $cargo2CD );

		PROFILE::deleteAllByPessoaID($pessoaID);
		foreach ($rules as $k => $l):
			PROFILE::insertByPessoaID($pessoaID,$l);
		endforeach;
	}

	public static function rulesCargos( $pessoaID, $cargoCD, $cargo2CD ) {
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

			//DIRETORES ASSOCIADOS // ANCIAO/PASTORES
			elseif ( $cargoCD == "2-01-01" || $cargo2CD == "2-01-01" || fStrStartWith($cargoCD,"2-05") || fStrStartWith($cargo2CD,"2-05") ):
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
		return $arr;
	 }
}

//fConnDB();
//$PROFILE::rulesCargos( 40, "2-02-00", null );
?>