<?php
class PROFILE {

	public static function getURLAccess($id) {
		session_start();
		$result = CONN::get()->Execute("
			SELECT DISTINCT tf.DS_NEW AS DS_URL
				FROM CAD_USU_PERFIL cpp
			  INNER JOIN TAB_PERFIL_ITEM tpi ON ( tpi.ID_TAB_PERFIL = cpp.ID_PERFIL AND tpi.DH_INI_VALID <= NOW() )
			  INNER JOIN TAB_DASHBOARD td ON ( td.ID = tpi.ID_TAB_DASHBOARD )
			   LEFT JOIN TAB_FUNCTION tf ON ( tf.ID = td.ID_TAB_FUNCTION )
			   WHERE cpp.ID_CAD_USUARIOS = ?
			     AND td.ID = ?
		", array($_SESSION['USER']['ID_USUARIO'], $id) );
		return (!$result->EOF ? $result->fields : null);
	}

	private static function fMenu( $menu, $active = null ){
		$ret = "";
		foreach ( $menu as $key => $value ):
			$opt = $value["OPT"];
			$ico = $value["ICO"];

			$cls = "";
			if (is_null($active) && !empty($value["URL"]) ):
				$cls = " class=\"active\"";
				$active = $key;
			endif;

			$ret .= "<li$cls>";
			$ret .= "<a";
			if ( !empty($value["URL"]) ):
				$ret .= " attr-menu=\"$key\"";
			endif;
			if ( count($value["CHILD"]) > 0 ):
				$ret .= " class=\"menu-toggle\"";
			endif;
			$ret .= ">";
			if ( !empty($ico) ):
				$ret .= "<i class=\"$ico\"></i>";
			endif;
			$ret .= "<span>$opt</span>";
			$ret .= "</a>";
			if ( count($value["CHILD"]) > 0 ):
				$ret .= "<ul class=\"ml-menu\">";
				$arr = PROFILE::fMenu( $value["CHILD"], $active );
				$active = $arr["ACTIVE"];
				$ret .= $arr["MENU"];
				$ret .= "</ul>";
			endif;
			$ret .= "</li>";
		endforeach;
		return array( "MENU" => $ret, "ACTIVE" => $active );
	}

	public static function montaMenu(){
		$arr = PROFILE::fMenu( PROFILE::fGetProfiles() );
		return array( "menu" => "<ul class=\"list\"><li class=\"header\">MENU PRINCIPAL</li>{$arr["MENU"]}</ul>", "active" => $arr["ACTIVE"] );
	}

	public static function fGetProfiles( $cd = NULL ) {
		$arr = array();
		$query = "SELECT DISTINCT td.ID, td.CD, td.ICONM, td.ICONF, td.DS_MENU, tf.DS_NEW as DS_URL
		    FROM CAD_USU_PERFIL cpp
	      INNER JOIN TAB_PERFIL_ITEM tpi ON ( tpi.ID_TAB_PERFIL = cpp.ID_PERFIL AND tpi.DH_INI_VALID <= NOW() )
	      INNER JOIN TAB_DASHBOARD td ON ( td.ID = tpi.ID_TAB_DASHBOARD )
	       LEFT JOIN TAB_FUNCTION tf ON ( tf.ID = td.ID_TAB_FUNCTION )
		   WHERE cpp.ID_CAD_USUARIOS = ?";
		if ( isset($cd) && !empty($cd) ):
			$query .= " AND td.CD LIKE '$cd.%' AND LENGTH(td.CD) = LENGTH('$cd')+3";
		else:
			$query .= " AND LENGTH(td.CD) = 2";
		endif;
		$query .= " ORDER BY td.CD";
		$result = CONN::get()->Execute($query, array($_SESSION['USER']['ID_USUARIO']) );
		while (!$result->EOF):
			$child = PROFILE::fGetProfiles( $result->fields['CD'] );
			$arr[ $result->fields['ID'] ] = array(
					"OPT"	 => ($result->fields['DS_MENU']),
					"ICO"	 => ( $_SESSION['USER']['TP_SEXO'] == "F" && isset($result->fields['ICONF']) ? $result->fields['ICONF'] : $result->fields['ICONM'] ),
					"ACTIVE" => false
			);
			if ( count( $child ) > 0 ):
				$arr[ $result->fields['ID'] ]["CHILD"] = $child;
			else:
				$arr[ $result->fields['ID'] ]["URL"] = $result->fields['DS_URL'];
			endif;
			$result->MoveNext();
		endwhile;
		return $arr;
	}

	public static function verificaPerfil(){
		$temPerfil = isset($_SESSION['USER']['ssid']);
		if (!$temPerfil):
			session_destroy();
			header("Location: ".PATTERNS::getVD()."index.php");
			exit;
		endif;
	}

	public static function fSetSessionLogin( $result ){
		session_start();
		$_SESSION['USER'] = $result->fields;
		$_SESSION['USER']['ssid'] = session_id();
	}

	public static function deleteAllByUserID( $userID ) {
		CONN::get()->Execute("
			DELETE FROM CAD_USU_PERFIL
			 WHERE ID_CAD_USUARIOS = ?
		", array( $userID ) );
	}

	public static function deleteAllByPessoaID( $pessoaID ){
		$rs = CONN::get()->Execute("
			SELECT ID_USUARIO FROM CAD_USUARIOS
			WHERE ID_CAD_PESSOA = ?
		", array( $pessoaID ) );
		if (!$rs->EOF):
			PROFILE::deleteAllByUserID( $rs->fields["ID_USUARIO"] );
		endif;
	}

	public static function deleteByUserID( $userID, $profileID ) {
		CONN::get()->Execute("
			DELETE FROM CAD_USU_PERFIL
			 WHERE ID_CAD_USUARIOS = ?
			   AND ID_PERFIL = ?
		", array( $userID, $profileID ) );
	}

	 public static function deleteByPessoaID( $pessoaID, $profileID ){
		$rs = CONN::get()->Execute("
			SELECT ID_USUARIO
			FROM CAD_USUARIOS
			WHERE ID_CAD_PESSOA = ?
		", array( $pessoaID ) );
		if (!$rs->EOF):
			PROFILE::deleteByUserID( $rs->fields["ID_USUARIO"], $profileID );
		endif;
	}

	 public static function insertByPessoaID( $pessoaID, $profileID ) {
		$rs = CONN::get()->Execute("
			SELECT ID_USUARIO
			  FROM CAD_USUARIOS
			 WHERE ID_CAD_PESSOA = ?
		", array( $pessoaID ) );
		if (!$rs->EOF):
			PROFILE::insert( $rs->fields["ID_USUARIO"], $profileID );
		endif;
	}

	 public static function insert( $userID, $profileID ) {
		$rs = CONN::get()->Execute("
			SELECT 1 FROM CAD_USU_PERFIL
			WHERE ID_CAD_USUARIOS = ?
			  AND ID_PERFIL = ?
		", array( $userID, $profileID ) );
		if ($rs->EOF):
			CONN::get()->Execute("
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
?>
