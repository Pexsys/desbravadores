<?php
@require_once("../include/functions.php");
@require_once("../include/responsavel.php");
responseMethod();

/****************************
 * Methods defined for use. *
 ****************************/
function login( $parameters ) {
	unset($_SESSION);
	
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

			//VERIFICA SE CPF É DE UM MEMBRO ATIVO
			$result = checkMemberByCPF($usr);
			if (!$result->EOF):
				$usuarioID = $result->fields["ID"];
				$usr = $result->fields["CD_USUARIO"];
				$psw = $result->fields["DS_SENHA"];

				if (is_null($usuarioID) && 
				   (!is_null($result->fields["CD_CARGO"]) || !is_null($result->fields["CD_CARGO2"])) ):

					$usr = $GLOBALS['pattern']->getBars()->encode(array(
						"ni" => $result->fields["ID_MEMBRO"]
					));
					$psw = sha1(strtolower($usr));

					fInsertUser( $usr, $result->fields['NM'], $psw, $result->fields['ID_CAD_PESSOA'] );

					PROFILE::apply(
						$result->fields['ID_CAD_PESSOA'],
						array( "cargo" => $result->fields["CD_CARGO"], "cargo2" => $result->fields["CD_CARGO2"] ) 
					);
				endif;

				return login( array(
					"page"		=> $pag,
					"username"	=> $usr,
					"password"	=> $psw ) );

			//VERIFICA SE RESPONSAVEL TEM ALGUM DEPENDENTE ATIVO e SE CPF CONSTA COMO RESPONSAVEL
			else:
		
				$resp = verificaRespByCPF($usr);			
				if ( !is_null($resp) && existeMenorByRespID($resp["ID_CAD_PESSOA"]) ):
					$psw = sha1(str_replace("-","",str_replace(".","",$usr)));

					PROFILE::apply(
						$resp["ID_CAD_PESSOA"],
						array( "respLeg" => true ) 
					);
					
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
			$rsHA = CONN::get()->Execute("SELECT ID_CAD_PESSOA, NM FROM CON_ATIVOS WHERE ID_CLUBE = ? AND ID_MEMBRO = ?", array( $barDecode["ci"], $barDecode["ni"] ) );
			if (!$rsHA->EOF):
				fInsertUser( $usr, $rsHA->fields['NM'], $psw, $rsHA->fields['ID_CAD_PESSOA'] );

				PROFILE::apply(
					$rsHA->fields['ID_CAD_PESSOA'],
					array() 
				);
			
				return login( array( 
					"page" =>		$pag, 
					"username" =>	$usr, 
					"password" =>	$psw ) );
			endif;

		//SE EXISTE O USUARIO DIGITADO.
		elseif (!$result->EOF):
			if ($usrClube):
				//VERIFICA SE ESTÁ ATIVO
				$rsHA = CONN::get()->Execute("SELECT CD_CARGO, CD_CARGO2 FROM CON_ATIVOS WHERE ID_CLUBE = ? AND ID_MEMBRO = ?", array( $barDecode["ci"], $barDecode["ni"] ) );
				if ($rsHA->EOF):
					$psw = null;
				endif;

				PROFILE::apply(
					$result->fields['ID_CAD_PESSOA'],
					array( "cargo" => $rsHA->fields["CD_CARGO"], "cargo2" => $rsHA->fields["CD_CARGO2"] ) 
				);

			else:
				$resp = verificaRespByCPF($usr);
				if (!is_null($resp)):
					if (!existeMenorByRespID($resp["ID_CAD_PESSOA"])):
						fDeleteUserAndProfile( $result->fields["ID"], 11 );
						return $arr;
					else:
						PROFILE::apply(
							$resp["ID_CAD_PESSOA"],
							array( "respLeg" => true ) 
						);
					endif;
				endif;
			endif;

			$password = $result->fields['DS_SENHA'];

			if ($password == $psw):
				PROFILE::fSetSessionLogin($result);

				CONN::get()->Execute("
					UPDATE CAD_USUARIO SET 
						DH_ATUALIZACAO = NOW()
					WHERE ID = ?
				", array( $result->fields['ID'] ) );

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
	CONN::get()->Execute("
			INSERT INTO CAD_USUARIO(
				CD_USUARIO,
				DS_USUARIO,
				DS_SENHA,
				ID_CAD_PESSOA
			) VALUES( ?, ?, ?, ? )",
	array( $usr, $nm, $psw, $pessoaID ) );
	return CONN::get()->Insert_ID();
}

function fInsertUserProfile( $userID, $profileID ){
	$rs = CONN::get()->Execute("
		SELECT 1 
		  FROM CAD_USU_PERFIL
		 WHERE ID_CAD_USUARIO = ?
		   AND ID_PERFIL = ?
	", array( $userID, $profileID ) );
	if ($rs->EOF):
		CONN::get()->Execute("
			INSERT INTO CAD_USU_PERFIL(
				ID_CAD_USUARIO,
				ID_PERFIL
			) VALUES( ?, ? )
		", array( $userID, $profileID ) );
	endif;
}

function fDeleteUserAndProfile( $userID, $profileID ){
	CONN::get()->Execute("
		DELETE FROM CAD_USU_PERFIL
		 WHERE ID_CAD_USUARIO = ?
		   AND ID_PERFIL = ?
	", array( $userID, $profileID ) );
	
	CONN::get()->Execute("
		DELETE FROM CAD_USUARIO
		 WHERE ID = ?
	", array( $userID ) );
}

function checkMemberByCPF($cpf){
	return CONN::get()->Execute("
		SELECT cu.ID, cu.CD_USUARIO, cu.DS_USUARIO, cu.DS_SENHA, 
			   ca.ID_CAD_PESSOA, ca.TP_SEXO, ca.CD_CARGO, ca.CD_CARGO2, ca.NM, ca.ID_MEMBRO
		  FROM CON_ATIVOS ca
	 LEFT JOIN CAD_USUARIO cu ON (cu.ID_CAD_PESSOA = ca.ID_CAD_PESSOA)
		 WHERE ca.NR_CPF = ?
	",array( fClearBN($cpf) ) );
}

function checkUser($cdUser, $pag){

	//VERIFICA SE PRECISA ATUALIZAR USUARIO
	$rs = CONN::get()->Execute("
		SELECT cu.ID, cp.ID AS ID_CAD_PESSOA
		FROM CAD_USUARIO cu
		INNER JOIN CAD_PESSOA cp ON (cp.NR_CPF = cu.CD_USUARIO)
		WHERE cu.CD_USUARIO = ? 
		  AND cu.ID_CAD_PESSOA IS NULL
	", array($cdUser) );
	if (!$rs->EOF):
		CONN::get()->Execute("
			UPDATE CAD_USUARIO SET ID_CAD_PESSOA = ? WHERE ID = ?
		", array( $rs->fields["ID_CAD_PESSOA"], $rs->fields["ID"] ) );
	endif;

	return CONN::get()->Execute("
		SELECT cu.ID, cu.CD_USUARIO, cu.DS_USUARIO, cu.DS_SENHA, 
			   cm.ID_CAD_PESSOA, cp.TP_SEXO, cm.ID AS ID_CAD_MEMBRO, cm.ID_CLUBE, cm.ID_MEMBRO
		  FROM CAD_USUARIO cu
		LEFT JOIN CAD_PESSOA cp ON (cp.ID = cu.ID_CAD_PESSOA OR cp.NR_CPF = ?)
		LEFT JOIN CAD_MEMBRO cm ON (cm.ID_CAD_PESSOA = cp.ID)
	". ($pag == "READDATA" ? " INNER JOIN CAD_USU_PERFIL cuf ON (cuf.ID_CAD_USUARIO = cu.ID AND cuf.ID_PERFIL = 5) " : "") ."
		 WHERE cu.CD_USUARIO = ?
	", array( $cdUser, $cdUser ) );
}
//28550424889
function logout() {
	session_start();
	session_destroy();
	unset($_SESSION);
	return array('logout' => true);
}
?>