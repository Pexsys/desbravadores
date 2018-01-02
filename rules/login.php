<?php
@require_once("../include/functions.php");
@require_once("../include/responsavel.php");
responseMethod();

/****************************
 * Methods defined for use. *
 ****************************/
function login( $parameters ) {
	unset($_SESSION);
	
	$profile = new PROFILE();
	
	$arr = array();
	$arr['page'] = "";
	$arr['login'] = false;
	
	$pag = mb_strtoupper($parameters["page"]);
	$usr = mb_strtoupper($parameters["username"]);
	$psw = strtolower($parameters["password"]);

	//Verificacao de Usuario/Senha
	if ( isset($usr) && !empty($usr) ):
		$barDecode	= $GLOBALS['pattern']->getBars()->decode($usr);
		$usrClube	= ($barDecode["lg"] == $GLOBALS['pattern']->getBars()->getLength() && 
					   $barDecode["cp"] == $GLOBALS['pattern']->getBars()->getClubePrefix() && 
					   $GLOBALS['pattern']->getBars()->has("id",$barDecode["split"]["id"])
					);

		fConnDB();
		$result = checkUser($usr, $pag);

		//SE NAO ENCONTROU E O CODIGO TEM OS CARACTERES MINIMOS PARA USUARIO DO CLUBE
		if ($result->EOF && $usrClube):
			$usrClube = (sha1(strtolower($usr)) == $psw);
		
			$usr = $GLOBALS['pattern']->getBars()->encode(array(
				"ni" => $barDecode["ni"]
			));
			if ($usrClube):
				$psw = sha1(strtolower($usr));
			endif;
			$result = checkUser($usr, $pag);

		//SE NAO ENCONTROU
		elseif ($result->EOF):
		
			//VERIFICA SE CPF CONSTA COMO RESPONSAVEL
			$resp = verificaRespByCPF($usr);
			if (!is_null($resp)):
			
				//RESPONSAVEL E MEMBRO ATIVO
				$result = checkMemberByCPF($usr);
				if (!$result->EOF):
					fInsertUserProfile($result->fields["ID_USUARIO"], 10 );
						
					return login( array(
						"page"		=> $pag,
						"username"	=> $result->fields["CD_USUARIO"],
						"password"	=> $result->fields["DS_SENHA"] ) );
								
				//VERIFICA SE RESPONSAVEL TEM ALGUM DEPENDENTE ATIVO
				elseif ( existeMenorByRespID($resp["ID"]) ):
					$psw = sha1(str_replace("-","",str_replace(".","",$usr)));
					fInsertUserProfile( fInsertUser( $usr, $resp["NM"], $psw, null ), 10 );
					
					return login( array(
						"page"		=> $pag,
						"username"	=> $usr,
						"password"	=> $psw ) );
				endif;
			endif;

		endif;
	
		//SE NAO ENCONTROU USUARIO E SENHA E EH MEMBRO DO CLUBE COM APRENDIZADO OU HISTORICO.
		if ($usrClube && $result->EOF):

			//VERIFICA SE ESTÁ ATIVO
			$rsHA = $GLOBALS['conn']->Execute("SELECT NM FROM CON_ATIVOS WHERE ID_CLUBE = ? AND ID_MEMBRO = ?", array( $barDecode["ci"], $barDecode["ni"] ) );
			if (!$rsHA->EOF):
				fInsertUserProfile( fInsertUser( $usr, $rsHA->fields['NM'], $psw, $rsHA->fields['ID_CAD_PESSOA'] ), 0 );
			
				return login( array( 
					"page" =>		$pag, 
					"username" =>	$usr, 
					"password" =>	$psw ) );
			endif;

		//SE EXISTE O USUARIO DIGITADO.
		elseif (!$result->EOF):
		
			if ($usrClube):
				//VERIFICA SE ESTÁ ATIVO
				$rsHA = $GLOBALS['conn']->Execute("SELECT 1 FROM CON_ATIVOS WHERE ID_CLUBE = ? AND ID_MEMBRO = ?", array( $barDecode["ci"], $barDecode["ni"] ) );
				if ($rsHA->EOF):
					$psw = null;
				endif;
			else:
				$resp = verificaRespByCPF($usr);
				if (!is_null($resp) && !existeMenorByRespID($resp["ID"])):
					fDeleteUserAndProfile( $result->fields["ID_USUARIO"], 10 );
					return $arr;
				endif;
			endif;

			$password = $result->fields['DS_SENHA'];

			if ($password == $psw):
				$profile->fSetSessionLogin($result);
				$GLOBALS['conn']->Execute("UPDATE CAD_USUARIOS SET DH_ATUALIZACAO = NOW() WHERE ID_USUARIO = ?",
					array( $result->fields['ID_USUARIO'] ) );

				if ( $pag == "READDATA" ):
					$arr['page'] = $GLOBALS['pattern']->getVD()."readdata.php";
				else:
					$arr['page'] = $GLOBALS['pattern']->getVD()."dashboard/index.php";
				endif;
				$arr['login'] = true;
			endif;
		endif;

	endif;

	return $arr;
}

function fInsertUser( $usr, $nm, $psw, $pessoaID ){
	$GLOBALS['conn']->Execute("
			INSERT INTO CAD_USUARIOS(
				CD_USUARIO,
				DS_USUARIO,
				DS_SENHA,
				ID_CAD_PESSOA
			) VALUES( ?, ?, ?, ? )",
	array( $usr, $nm, $psw, $pessoaID ) );
	return $GLOBALS['conn']->Insert_ID();
}

function fInsertUserProfile( $userID, $profileID ){
	$rs = $GLOBALS['conn']->Execute("
		SELECT 1 
		  FROM CAD_USU_PERFIL
		 WHERE ID_CAD_USUARIOS = ?
		   AND ID_PERFIL = ?
	", array( $userID, $profileID ) );
	if ($rs->EOF):
		$GLOBALS['conn']->Execute("
			INSERT INTO CAD_USU_PERFIL(
				ID_CAD_USUARIOS,
				ID_PERFIL
			) VALUES( ?, ? )
		", array( $userID, $profileID ) );
	endif;
}

function fDeleteUserAndProfile( $userID, $profileID ){
	$GLOBALS['conn']->Execute("
		DELETE FROM CAD_USU_PERFIL
		 WHERE ID_CAD_USUARIOS = ?
		   AND ID_PERFIL = ?
	", array( $userID, $profileID ) );
	
	$GLOBALS['conn']->Execute("
		DELETE FROM CAD_USUARIOS
		 WHERE ID_USUARIO = ?
	", array( $userID ) );
}

function checkMemberByCPF($cpf){
	$noFormat = str_replace("-","",str_replace(".","",$cpf));
	return $GLOBALS['conn']->Execute("
		SELECT cu.ID_USUARIO, cu.CD_USUARIO, cu.DS_USUARIO, cu.DS_SENHA, ca.ID AS ID_CAD_PESSOA, ca.TP_SEXO
		  FROM CON_ATIVOS ca
		INNER JOIN CAD_USUARIOS cu ON (cu.ID_CAD_PESSOA = ca.ID_CAD_PESSOA)
		 WHERE REPLACE(REPLACE(ca.NR_CPF,'.',''),'-','') = ?
	",array( $noFormat ) );
}

function checkUser($cdUser, $pag){
	return $GLOBALS['conn']->Execute("
		SELECT cu.ID_USUARIO, cu.CD_USUARIO, cu.DS_USUARIO, cu.DS_SENHA, cp.ID AS ID_CAD_PESSOA, cp.TP_SEXO
		  FROM CAD_USUARIOS cu
	    LEFT JOIN CAD_PESSOA cp ON (cp.ID = cu.ID_CAD_PESSOA OR cp.NR_CPF = ?)
	". ($pag == "READDATA" ? " INNER JOIN CAD_USU_PERFIL cuf ON (cuf.ID_CAD_USUARIOS = cu.ID_USUARIO AND cuf.ID_PERFIL = 2) " : "") ."
		 WHERE cu.CD_USUARIO = ?",
	array( $cdUser, $cdUser ) );
}

function logout() {
	session_start();
	session_destroy();
	unset($_SESSION);
	return array('logout' => true);
}
?>