<?php
@require_once("../include/functions.php");
responseMethod();

/****************************
 * Methods defined for use. *
 ****************************/
function getName( $parameters ) {
	$barDecode	= PATTERNS::getBars()->decode($parameters["codigo"]);
	$arr = array();
	$arr['ok'] = false;

	//Verificacao de Usuario/Senha
	if ( isset($barDecode["ni"]) && !empty($barDecode["ni"]) ):
		$result = CONN::get()->Execute("
			SELECT cm.ID, cp.NM
			  FROM CAD_MEMBRO cm
		INNER JOIN CAD_PESSOA cp ON (cp.ID = cm.ID_CAD_PESSOA)
			 WHERE cm.ID_CLUBE = ?
			   AND cm.ID_MEMBRO = ?
		", Array( $barDecode["ci"], $barDecode["ni"] ) );
		if (!$result->EOF):
			$arr["id"] = $result->fields['ID'];
			$arr["nome"] = $result->fields['NM'];
			$arr['ok'] = true;
		endif;
	endif;
	return $arr;
}

function getNames(){
	$arr = array();

	session_start();
	$usuarioID = $_SESSION['USER']['ID_USUARIO'];
	$qtdZeros = zeroSizeID();

	$unidadeID	= null;
	$pessoaID	= null;
	$cargo		= null;

	//MEMBRO ATIVO LOGADO
	$result = CONN::get()->Execute("
		SELECT cu.ID_CAD_PESSOA, ca.ID_UNIDADE, ca.CD_CARGO, ca.CD_CARGO2, ca.NM, ca.ID_MEMBRO, ca.ID_CAD_MEMBRO
		  FROM CON_ATIVOS ca
	INNER JOIN CAD_USUARIOS cu ON (cu.ID_CAD_PESSOA = ca.ID_CAD_PESSOA)
		 WHERE cu.ID_USUARIO = ?
	", array( $usuarioID ) );
	if (!$result->EOF):
		$unidadeID = $result->fields["ID_UNIDADE"];
		$membroID = $result->fields["ID_MEMBRO"];
		$pessoaID = $result->fields["ID_CAD_PESSOA"];
		$cadMembroID = $result->fields["ID_CAD_MEMBRO"];
		$membroNM = $result->fields["NM"];

		$arr[] = array( "id" => "$cadMembroID|$membroNM", "ds" => "<<mim>> - $membroNM", "fg" => "S", "sb" => fStrZero($membroID, $qtdZeros) );
		$cargo = $result->fields['CD_CARGO'];
		if (fStrStartWith($cargo,"2-07")):
			$cargo = $result->fields['CD_CARGO2'];
		endif;
	endif;

	$aQuery = array( "query" => "", "binds" => array() );

	//TRATAMENTO MEMBROS DA MINHA UNIDADE
	$aQuery = getUnionByUnidade( $aQuery, $unidadeID, $cadMembroID );

	//TRATAMENTO MEMBROS QUE ESTAO FAZENDO AS MESMAS CLASSES QUE EU
	$aQuery = getUnionByClasses( $aQuery, $pessoaID, $cadMembroID );

	//TRATAMENTO PARA INSTRUTOR DE CLASSE
	if ($cargo != "2-04-00" && fStrStartWith($cargo,"2-04")):
		$classe = "01-".substr($cargo,-2);

		$aQuery["query"] .= " UNION
			SELECT DISTINCT at.ID_CAD_MEMBRO, at.ID_MEMBRO, at.NM
			  FROM CON_APR_PESSOA cap
		    INNER JOIN CON_ATIVOS at ON (at.ID_CAD_PESSOA = cap.ID_CAD_PESSOA)
			 WHERE cap.CD_ITEM_INTERNO LIKE '$classe%'
			   AND cap.DT_CONCLUSAO IS NULL
			   AND at.ID_CAD_MEMBRO <> ?";
		$aQuery["binds"][] = $cadMembroID;
	endif;

	//TRATAMENTO PARA ADMINISTRACAO/INSTRUTORES NAO ESPECIFICOS
	if ($cargo == "2-04-00" || $cargo == "2-04-99" || fStrStartWith($cargo,"2-01") || fStrStartWith($cargo,"2-02")):
		$aQuery["query"] .= " UNION SELECT ID_CAD_MEMBRO, ID_MEMBRO, NM FROM CON_ATIVOS WHERE ID_CAD_MEMBRO <> ?";
		$aQuery["binds"][] = $cadMembroID;
	endif;

	//TRATAMENTO MEUS DEPENDENTES, SUAS UNIDADES OU SUAS CLASSES
	$rd = CONN::get()->Execute("
		SELECT ca.ID_CAD_PESSOA, ca.ID_UNIDADE
		FROM CAD_USUARIOS cu
		INNER JOIN CON_ATIVOS ca ON (ca.ID_PESSOA_RESP = cu.ID_CAD_PESSOA)
		WHERE cu.ID_USUARIO = ?
	", array($usuarioID) );
	foreach ($rd as $k => $l):
		$aQuery = getUnionByUnidade( $aQuery, $l["ID_UNIDADE"], $cadMembroID );
		$aQuery = getUnionByClasses( $aQuery, $l["ID_CAD_PESSOA"], $cadMembroID );
	endforeach;

	if (!empty($aQuery["query"])):
		$rs = CONN::get()->Execute( substr($aQuery["query"], 7)." ORDER BY 3", $aQuery["binds"] );
		foreach ($rs as $k => $line):
			$id = $line["ID_CAD_MEMBRO"];
			$nm = $line["NM"];
			$arr[] = array( "id" => "$id|$nm", "ds" => $nm, "sb" => fStrZero($line["ID_MEMBRO"], $qtdZeros) );
		endforeach;
	endif;

	return array( "result" => true, "names" => $arr );
}

function getUnionByUnidade($aQuery, $unidadeID, $cadMembroID){
	if (!is_null($unidadeID) && !is_null($cadMembroID)):
		$aQuery["query"] .=" UNION SELECT ID_CAD_MEMBRO, ID_MEMBRO, NM FROM CON_ATIVOS WHERE ID_UNIDADE = ? AND ID_CAD_MEMBRO <> ?";
		$aQuery["binds"][] = $unidadeID;
		$aQuery["binds"][] = $cadMembroID;
	endif;
	return $aQuery;
}

function getUnionByClasses($aQuery, $pessoaID, $cadMembroID){
	if (!is_null($pessoaID) && !is_null($cadMembroID)):
		$aQuery["query"] .= " UNION
				SELECT DISTINCT at.ID_CAD_MEMBRO, at.ID_MEMBRO, at.NM
				  FROM CON_APR_PESSOA cap
			    INNER JOIN CON_ATIVOS at ON (at.ID_CAD_PESSOA = cap.ID_CAD_PESSOA)
				 WHERE cap.CD_ITEM_INTERNO IN (SELECT DISTINCT CD_ITEM_INTERNO FROM CON_APR_PESSOA WHERE ID_CAD_PESSOA = ? AND TP_ITEM = 'CL' AND DT_CONCLUSAO IS NULL)
				   AND cap.DT_CONCLUSAO IS NULL
				   AND at.ID_CAD_MEMBRO <> ?";
		$aQuery["binds"][] = $pessoaID;
		$aQuery["binds"][] = $cadMembroID;
	endif;
	return $aQuery;
}

function getEspecialidades() {
	$arr = array();
	$result = CONN::get()->Execute("
	SELECT
		A.DS_ITEM AS DS_AREA,
		E.CD_ITEM_INTERNO AS CD_ITEM,
		E.DS_ITEM
	FROM TAB_APRENDIZADO E
	INNER JOIN TAB_APRENDIZADO A ON (A.CD_AREA_INTERNO = E.CD_AREA_INTERNO AND A.CD_ITEM_INTERNO IS NULL)
	WHERE E.TP_ITEM = 'ES'
	  AND E.CD_ITEM_INTERNO IS NOT NULL
	ORDER BY A.DS_ITEM, E.DS_ITEM");
	foreach ($result as $k => $line):
		$arr[] = array(
			"cd_item" => $line['CD_ITEM'],
			"ds_item" => $line['DS_ITEM'],
			"ds_area" => $line['DS_AREA']
		);
	endforeach;

	return array( "result" => true, "especialidades" => $arr );
}
?>
