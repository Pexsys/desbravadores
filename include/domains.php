<?php
fConnDB();
function getDomainMembrosAtivos(){
	$arr = array();
	
	$qtdZeros = zeroSizeID();
	$result = $GLOBALS['conn']->Execute("SELECT ID, NM FROM CON_ATIVOS ORDER BY NM");
	foreach($result as $l => $fields):
		$id = str_pad($fields['ID'], $qtdZeros, "0", STR_PAD_LEFT);
		$arr[] = array(
			"value" => $fields['ID'],
			"label" => "$id ".utf8_encode($fields['NM'])
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
			"label"	=> strftime("%d/%m/%Y",strtotime($f["DH_S"])) ." - ". utf8_encode($f['DS'])
		);
	endforeach;
	return $arr;
}

function getDomainUnidades(){
	$arr = array();
	
	$result = $GLOBALS['conn']->Execute("
		SELECT ID, DS
		  FROM TAB_UNIDADE
		 WHERE FG_ATIVA = ?
	  ORDER BY DS", array('S') );
	while (!$result->EOF):
		$arr[] = array( 
			"value"	=> $result->fields['ID'],
			"label"	=> utf8_encode($result->fields['DS'])
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
			"label"	=> utf8_encode($result->fields['DS'])
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
		 WHERE FG_IM = 'S'
		  ORDER BY TP");
	while (!$result->EOF):
		$arr[] = array(
				"value"	=> utf8_encode($result->fields['TP']),
				"label"	=> utf8_encode($result->fields['TP'])
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
			"label"	=> utf8_encode($result->fields['DS_ITEM'])
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
			"label"	=> $result->fields['CD_ITEM_INTERNO']." ".utf8_encode($result->fields['DS_ITEM'])
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
			"label"	=> $result->fields['CD_ITEM_INTERNO']." ".utf8_encode($result->fields['DS_ITEM'])
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
			"label"	=> $result->fields['CD_ITEM_INTERNO']." ".utf8_encode($result->fields['DS_ITEM'])
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