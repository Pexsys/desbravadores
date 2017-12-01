<?php
@require_once("../include/functions.php");
responseMethod();

/****************************
 * Methods defined for use. *
 ****************************/
function getName( $parameters ) {
	$barDecode	= $GLOBALS['pattern']->getBars()->decode($parameters["codigo"]);	
	$arr = array();
	$arr['ok'] = false;

	//Verificacao de Usuario/Senha
	if ( isset($barDecode["ni"]) && !empty($barDecode["ni"]) ):
		fConnDB();
		$result = $GLOBALS['conn']->Execute("SELECT nm FROM CAD_PESSOA WHERE id = ?", Array( $barDecode["ni"] ) );
		if (!$result->EOF):
			$arr["id"] = $barDecode["ni"];
			$arr["nome"] = $result->fields['nm'];
			$arr['ok'] = true;
		endif;
	endif;
	return $arr;
}

function getNames(){
	$arr = array();

	session_start();
	$usuarioID = $_SESSION['USER']['id_usuario'];
	
	$unidadeID	= null;
	$membroID	= null;
	$cargo		= null;
	
	fConnDB();
	
	//MEMBRO ATIVO LOGADO
	$result = $GLOBALS['conn']->Execute("
		SELECT cu.ID_CAD_PESSOA, ca.ID_UNIDADE, ca.CD_CARGO, ca.CD_CARGO2, ca.NM 
		  FROM CON_ATIVOS ca
	INNER JOIN CAD_USUARIOS cu ON (cu.ID_CAD_PESSOA = ca.ID)
		 WHERE cu.ID_USUARIO = ?
	", array( $usuarioID ) );
	if (!$result->EOF):
		$unidadeID = $result->fields["ID_UNIDADE"];
		$membroID = $result->fields["ID_CAD_PESSOA"];
		$membroNM = ($result->fields["NM"]);
		
		$arr[] = array( "id" => $result->fields["ID_CAD_PESSOA"]."|$membroNM", "ds" => "<<mim>> - $membroNM");
		$cargo = $result->fields['CD_CARGO'];
		if (fStrStartWith($cargo,"2-07")):
			$cargo = $result->fields['CD_CARGO2'];
		endif;
	endif;

	$aQuery = array( "query" => "", "binds" => array() );
	
	//TRATAMENTO MEMBROS DA MINHA UNIDADE
	$aQuery = getUnionByUnidade( $aQuery, $unidadeID, $membroID );

	//TRATAMENTO MEMBROS QUE ESTAO FAZENDO AS MESMAS CLASSES QUE EU
	$aQuery = getUnionByClasses( $aQuery, $membroID );
	
	//TRATAMENTO PARA INSTRUTOR DE CLASSE
	if ($cargo != "2-04-00" && fStrStartWith($cargo,"2-04")):
		$classe = "01-".substr($cargo,-2);

		$aQuery["query"] .= " UNION 
			SELECT DISTINCT at.ID, at.NM
			  FROM CON_APR_PESSOA cap
		    INNER JOIN CON_ATIVOS at ON (at.ID = cap.ID_CAD_PESSOA)
			 WHERE cap.CD_ITEM_INTERNO LIKE '$classe%'
			   AND cap.DT_CONCLUSAO IS NULL
			   AND at.ID <> ?";
		$aQuery["binds"][] = $membroID;
	endif;
	
	//TRATAMENTO PARA ADMINISTRACAO/INSTRUTORES NAO ESPECIFICOS
	if ($cargo == "2-04-00" || $cargo == "2-04-99" || fStrStartWith($cargo,"2-01") || fStrStartWith($cargo,"2-02")):
		$aQuery["query"] .= " UNION SELECT ID, NM FROM CON_ATIVOS WHERE ID <> ?";
		$aQuery["binds"][] = $membroID;
	endif;

	//TRATAMENTO MEUS DEPENDENTES, SUAS UNIDADES OU SUAS CLASSES
	$rd = $GLOBALS['conn']->Execute("
		SELECT ca.ID, ca.NM, ca.ID_UNIDADE
		FROM CAD_USUARIOS cu
		INNER JOIN CAD_RESP cr ON (REPLACE(REPLACE(cr.CPF_RESP,'.',''),'-','') = cu.CD_USUARIO)
		INNER JOIN CON_ATIVOS ca ON (ca.ID_RESP = cr.ID)
		WHERE cu.ID_USUARIO = ?
	", array($usuarioID) );
	foreach ($rd as $k => $l):
		$arr[] = array( "id" => $l["ID"]."|".($l["NM"]), "ds" =>($l["NM"]) );
	
		$aQuery = getUnionByUnidade( $aQuery, $l["ID_UNIDADE"], $l["ID"] );
		$aQuery = getUnionByClasses( $aQuery, $l["ID"] );
	endforeach;
	
	if (!empty($aQuery["query"])):
		$rs = $GLOBALS['conn']->Execute( substr($aQuery["query"], 7)." ORDER BY 2", $aQuery["binds"] );
		foreach ($rs as $k => $line):
			$id = $line["ID"];
			$nm = ($line["NM"]);
			$arr[] = array( "id" => "$id|$nm", "ds" => $nm );
		endforeach;
	endif;

	return array( "result" => true, "names" => $arr );
}

function getUnionByUnidade($aQuery, $unidadeID, $membroID){
	if (!is_null($unidadeID) && !is_null($membroID)):
		$aQuery["query"] .=" UNION SELECT ID, NM FROM CON_ATIVOS WHERE ID_UNIDADE = ? AND ID <> ?";
		$aQuery["binds"][] = $unidadeID;
		$aQuery["binds"][] = $membroID;
	endif;
	return $aQuery;
}

function getUnionByClasses($aQuery, $membroID){
	if (!is_null($membroID)):
		$aQuery["query"] .= " UNION
				SELECT DISTINCT at.ID, at.NM
				  FROM CON_APR_PESSOA cap
			    INNER JOIN CON_ATIVOS at ON (at.ID = cap.ID_CAD_PESSOA)
				 WHERE cap.CD_ITEM_INTERNO IN (SELECT DISTINCT CD_ITEM_INTERNO FROM CON_APR_PESSOA WHERE ID_CAD_PESSOA = ? AND TP_ITEM = ? AND DT_CONCLUSAO IS NULL)
				   AND cap.DT_CONCLUSAO IS NULL
				   AND at.ID <> ?";
		$aQuery["binds"][] = $membroID;
		$aQuery["binds"][] = "CL";
		$aQuery["binds"][] = $membroID;
	endif;
	return $aQuery;
}

function getEspecialidades() {
	$arr = array();

	fConnDB();
	$result = $GLOBALS['conn']->Execute("
	SELECT
		A.DS_ITEM AS DS_AREA,
		E.CD_ITEM_INTERNO AS CD_ITEM,
		E.DS_ITEM
	FROM TAB_APRENDIZADO E
	INNER JOIN TAB_APRENDIZADO A ON (A.CD_AREA_INTERNO = E.CD_AREA_INTERNO AND A.CD_ITEM_INTERNO IS NULL)
	WHERE E.TP_ITEM = ?
	  AND E.CD_ITEM_INTERNO IS NOT NULL
	ORDER BY A.DS_ITEM, E.DS_ITEM", array( "ES" ) );
	while (!$result->EOF):
		$arr[] = array( 
			"cd_item" => $result->fields['CD_ITEM'],
			"ds_item" => ($result->fields['DS_ITEM']),
			"ds_area" => ($result->fields['DS_AREA'])
		);
		$result->MoveNext();
	endwhile;

	return array( "result" => true, "especialidades" => $arr );
}
?>