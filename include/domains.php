<?php
fConnDB();
function getDomainMembrosAtivos(){
	$arr = array();
	
	$qtdZeros = zeroSizeID();
	$result = $GLOBALS['conn']->Execute("SELECT ID, NM FROM CON_ATIVOS ORDER BY NM");
	foreach($result as $l => $f):
		$id = fStrZero($f['ID'], $qtdZeros);
		$arr[] = array(
			"id" => $f['ID'],
			"ds" => $f['NM'],
			"sb" => $id
		);
	endforeach;
	return $arr;
}

function getDomainMembrosInativos(){
	$arr = array();
	
	$qtdZeros = zeroSizeID();
	$result = $GLOBALS['conn']->Execute("SELECT cp.ID, cp.NM FROM CAD_PESSOA cp WHERE NOT EXISTS (SELECT 1 FROM CAD_ATIVOS WHERE ID = cp.ID AND NR_ANO = YEAR(NOW())) ORDER BY cp.NM");
	foreach($result as $l => $f):
		$id = fStrZero($f['ID'], $qtdZeros);
		$arr[] = array(
			"id" => $f['ID'],
			"ds" => $f['NM'],
			"sb" => $id
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
			"id"	=> $f['ID'],
			"ds"	=> $f['DS'],
			"sb"	=> strftime("%d/%m/%Y",strtotime($f["DH_S"]))
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
	foreach ($result as $k => $f):
		$arr[] = array( 
			"id"	=> $f['ID'],
			"ds"	=> $f['DS']
		);
	endforeach;
	return $arr;
}

function getTipoAprendizado(){
	$arr = array();
	
	$result = $GLOBALS['conn']->Execute("
		SELECT ID, DS
		  FROM TAB_TP_APRENDIZADO
		  ORDER BY ID");
	foreach ($result as $k => $f):
		$arr[] = array( 
			"id"	=> $f['ID'],
			"ds"	=> $f['DS']
		);
	endforeach;
	return $arr;
}

function getTipoMateriais(){
	$arr = array();
	
	$result = $GLOBALS['conn']->Execute("
		SELECT DISTINCT TP
		  FROM TAB_MATERIAIS
		  ORDER BY TP");
	foreach ($result as $k => $f):
		$arr[] = array(
				"id"	=> $f['TP'],
				"ds"	=> $f['TP']
		);
	endforeach;
	return $arr;
}

function getDomainClasses(){
	$arr = array();
	
	$result = $GLOBALS['conn']->Execute("
		SELECT ID, DS_ITEM
		  FROM TAB_APRENDIZADO
		WHERE TP_ITEM = ?
	  ORDER BY CD_ITEM_INTERNO", array( "CL" ) );
	foreach ($result as $k => $f):
		$arr[] = array( 
			"id"	=> $f['ID'],
			"ds"	=> $f['DS_ITEM'],
			"sb"	=> $f['CD_ITEM_INTERNO']
		);
	endforeach;
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
	foreach ($result as $k => $f):
		$arr[] = array( 
			"id"	=> $f['ID'],
			"ds"	=> $f['DS_ITEM'],
			"sb"	=> $f['CD_ITEM_INTERNO']
		);
	endforeach;
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
	foreach ($result as $k => $f):
		$arr[] = array( 
			"id"	=> $f['ID'],
			"ds"	=> $f['DS_ITEM'],
			"sb"	=> $f['CD_ITEM_INTERNO']
		);
	endforeach;
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
	foreach ($result as $k => $f):
		$arr[] = array( 
			"id"	=> $f['CD_AREA_INTERNO'],
			"ds"	=> $f['DS_ITEM'],
			"sb"	=> $f['CD_ITEM_INTERNO']
		);
	endforeach;
	return $arr;
}

function getMesAniversario(){
	$result = $GLOBALS['conn']->Execute("
		SELECT DISTINCT DATE_FORMAT(DT_NASC,'%m') AS MES
		  FROM CON_ATIVOS
	  ORDER BY 1
	");
	foreach ($result as $k => $f):
		$arr[] = array( "id" => $f["MES"], "ds" => mb_strtoupper(fDescMes($f["MES"])) );
	endforeach;
	return $arr;
}
?>