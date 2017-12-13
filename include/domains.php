<?php
fConnDB();
function getDomainMembrosAtivos(){
	$arr = array();
	
	$qtdZeros = zeroSizeID();
	$result = $GLOBALS['conn']->Execute("SELECT ID, NM FROM CON_ATIVOS ORDER BY NM");
	foreach($result as $l => $fields):
		$id = fStrZero($fields['ID'], $qtdZeros);
		$arr[] = array(
			"value" => $fields['ID'],
			"label" => "$id ".($fields['NM'])
		);
	endforeach;
	return $arr;
}

function getDomainMembrosInativos(){
	$arr = array();
	
	$qtdZeros = zeroSizeID();
	$result = $GLOBALS['conn']->Execute("SELECT cp.ID, cp.NM FROM CAD_PESSOA cp WHERE NOT EXISTS (SELECT 1 FROM CAD_ATIVOS WHERE ID = cp.ID AND NR_ANO = YEAR(NOW())) ORDER BY cp.NM");
	foreach($result as $l => $fields):
		$id = fStrZero($fields['ID'], $qtdZeros);
		$arr[] = array(
			"value" => $fields['ID'],
			"label" => "$id ".($fields['NM'])
		);
	endforeach;
	return $arr;
}

function getDomainEventos(){
	$arr = array();
	
	$result = $GLOBALS['conn']->Execute("
		SELECT ID, DS, DH_S 
		FROM EVE_SAIDA 
		ORDER BY DH_S DESC
	");
	foreach ($result as $k => $f):
		$arr[] = array(
			"value"	=> $f['ID'],
			"label"	=> strftime("%d/%m/%Y",strtotime($f["DH_S"])) ." - ". ($f['DS'])
		);
	endforeach;
	return $arr;
}

function getDomainUnidades($fAtivo = false){
	$arr = array();
	
	$query = "SELECT ta.ID, ta.DS FROM TAB_UNIDADE ta WHERE ta.FG_ATIVA = 'S' ORDER BY ta.DS";
	if ($fAtivo):
		$query = "SELECT DISTINCT ca.ID_UNIDADE AS ID, ca.DS_UNIDADE AS DS FROM CON_ATIVOS ca ORDER BY ca.DS_UNIDADE";
	endif;
	
	$result = $GLOBALS['conn']->Execute($query);
	while (!$result->EOF):
		$arr[] = array( 
			"value"	=> $result->fields['ID'],
			"label"	=> ($result->fields['DS'])
		);
		$result->MoveNext();
	endwhile;
	return $arr;
}

function getTipoAprendizado(){
	$arr = array();
	
	$result = $GLOBALS['conn']->Execute("
		SELECT ID, DS
		  FROM TAB_TP_APRENDIZADO
		  ORDER BY ID");
	while (!$result->EOF):
		$arr[] = array( 
			"value"	=> $result->fields['ID'],
			"label"	=> ($result->fields['DS'])
		);
		$result->MoveNext();
	endwhile;
	return $arr;
}

function getTipoMateriais(){
	$arr = array();
	
	$result = $GLOBALS['conn']->Execute("
		SELECT DISTINCT TP
		  FROM TAB_MATERIAIS
		  ORDER BY TP");
	while (!$result->EOF):
		$arr[] = array(
				"value"	=> ($result->fields['TP']),
				"label"	=> ($result->fields['TP'])
		);
		$result->MoveNext();
	endwhile;
	return $arr;
}

function getDomainClasses(){
	$arr = array();
	
	$result = $GLOBALS['conn']->Execute("
		SELECT ID, DS_ITEM
		  FROM TAB_APRENDIZADO
		WHERE TP_ITEM = ?
	  ORDER BY CD_ITEM_INTERNO", array( "CL" ) );
	while (!$result->EOF):
		$arr[] = array( 
			"value"	=> $result->fields['ID'],
			"label"	=> ($result->fields['DS_ITEM'])
		);
		$result->MoveNext();
	endwhile;
	return $arr;
}

function getDomainMestrados(){
	$arr = array();
	
	$result = $GLOBALS['conn']->Execute("
		SELECT ID, DS_ITEM, CD_ITEM_INTERNO
		  FROM TAB_APRENDIZADO
		WHERE TP_ITEM = ?
		  AND CD_AREA_INTERNO = ?
		  AND CD_ITEM_INTERNO IS NOT NULL
	  ORDER BY DS_ITEM", array( "ES", "ME" ) );
	while (!$result->EOF):
		$arr[] = array( 
			"value"	=> $result->fields['ID'],
			"label"	=> $result->fields['CD_ITEM_INTERNO']." ".($result->fields['DS_ITEM'])
		);
		$result->MoveNext();
	endwhile;
	return $arr;
}

function getDomainEspecialidades(){
	$arr = array();
	
	$result = $GLOBALS['conn']->Execute("
		SELECT ID, DS_ITEM, CD_ITEM_INTERNO
		  FROM TAB_APRENDIZADO
		WHERE TP_ITEM = ?
		  AND CD_AREA_INTERNO <> ?
		  AND CD_ITEM_INTERNO IS NOT NULL
	  ORDER BY DS_ITEM", array( "ES", "ME" ) );
	while (!$result->EOF):
		$arr[] = array( 
			"value"	=> $result->fields['ID'],
			"label"	=> $result->fields['CD_ITEM_INTERNO']." ".($result->fields['DS_ITEM'])
		);
		$result->MoveNext();
	endwhile;
	return $arr;
}

function getDomainAreasEspecialidades(){
	$arr = array();
	
	$result = $GLOBALS['conn']->Execute("
		SELECT CD_AREA_INTERNO, DS_ITEM, CD_ITEM_INTERNO
		  FROM TAB_APRENDIZADO
		WHERE TP_ITEM = ?
		  AND CD_ITEM_INTERNO IS NULL
	  ORDER BY DS_ITEM", array( "ES" ) );
	while (!$result->EOF):
		$arr[] = array( 
			"value"	=> $result->fields['CD_AREA_INTERNO'],
			"label"	=> $result->fields['CD_ITEM_INTERNO']." ".($result->fields['DS_ITEM'])
		);
		$result->MoveNext();
	endwhile;
	return $arr;
}

function getMesAniversario(){
	$result = $GLOBALS['conn']->Execute("
		SELECT DISTINCT DATE_FORMAT(DT_NASC,'%m') AS MES
		  FROM CON_ATIVOS
	  ORDER BY 1
	");
	foreach ($result as $k => $l):
		$arr[] = array( "value" => $l["MES"], "label" => mb_strtoupper(fDescMes($l["MES"])) );
	endforeach;
	return $arr;
}
?>