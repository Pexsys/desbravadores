<?php
fConnDB();

function getDomainMembrosAtivos(){
	$arr = array();
	
	$qtdZeros = zeroSizeID();
	$result = $GLOBALS['conn']->Execute("
		SELECT ID_CAD_MEMBRO, ID_MEMBRO, NM, IDADE_HOJE
		FROM CON_ATIVOS 
		ORDER BY NM
	");
	foreach($result as $l => $f):
		$a = array(
			"id" => $f['ID_CAD_MEMBRO'],
			"ds" => $f['NM'],
			"sb" => fStrZero($f['ID_MEMBRO'], $qtdZeros),
			"icon" => ($f['IDADE_HOJE'] < 18 ? "fa fa-circle" : "fa fa-circle-thin")
		);
		if ($f['IDADE_HOJE'] < 18):
			$a["icon-color"] = "text-danger";
		endif;
		$arr[] = $a;
	endforeach;
	return $arr;
}

function getDomainMembrosInativos(){
	$arr = array();
	
	$qtdZeros = zeroSizeID();
	$result = $GLOBALS['conn']->Execute("
		SELECT cm.ID, cm.ID_MEMBRO, cp.NM, cp.IDADE_HOJE
		FROM CAD_MEMBRO cm
		INNER JOIN CON_PESSOA cp ON (cp.ID_CAD_PESSOA = cm.ID_CAD_PESSOA)
		WHERE NOT EXISTS (SELECT 1 FROM CON_ATIVOS WHERE ID_CAD_MEMBRO = cm.ID)
		ORDER BY cp.NM
	");
	foreach($result as $l => $f):
		$a = array(
			"id" => $f['ID'],
			"ds" => $f['NM'],
			"sb" => fStrZero($f['ID_MEMBRO'], $qtdZeros),
			"icon" => ($f['IDADE_HOJE'] < 18 ? "fa fa-circle" : "fa fa-circle-thin")
		);
		if ($f['IDADE_HOJE'] < 18):
			$a["icon-color"] = "text-danger";
		endif;
		$arr[] = $a;
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
			"sb"	=> strftime("%d/%m/%Y",strtotime($f["DH_S"])),
			"icon" => "fa fa-calendar-o"
		);
	endforeach;
	return $arr;
}

function getDomainUnidades($fAtivo = false){
	$arr = array();
	
	$query = "SELECT ID, DS, TP, IDADE FROM TAB_UNIDADE ta WHERE FG_ATIVA = 'S' ORDER BY DS";
	if ($fAtivo):
		$query = "
			SELECT DISTINCT 
				ca.ID_UNIDADE AS ID, 
				ca.DS_UNIDADE AS DS,
				ta.TP,
				ta.IDADE
			FROM CON_ATIVOS ca 
			INNER JOIN TAB_UNIDADE ta ON (ta.ID = ca.ID_UNIDADE)
			ORDER BY ca.DS_UNIDADE";
	endif;
	
	$result = $GLOBALS['conn']->Execute($query);
	foreach ($result as $k => $f):
		$arr[] = array( 
			"id"	=> $f['ID'],
			"ds"	=> $f['DS'],
			"sb"  => $f['IDADE'],
			"icon" => ($f['TP'] == "F" ? "fa fa-female" : ( $f['TP'] == "M" ? "fa fa-male" : "fa fa-user-secret" ) )
		);
	endforeach;
	return $arr;
}

function getTipoAprendizado(){
	$arr = array();
	
	$result = $GLOBALS['conn']->Execute("
		SELECT ID, DS, ICON
		  FROM TAB_TP_APRENDIZADO
		  ORDER BY ID");
	foreach ($result as $k => $f):
		$arr[] = array( 
			"id"	=> $f['ID'],
			"ds"	=> $f['DS'],
			"icon"	=> $f['ICON']
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
		SELECT ID, DS_ITEM, CD_AREA_INTERNO
		  FROM TAB_APRENDIZADO
		WHERE TP_ITEM = 'CL'
	  ORDER BY CD_ITEM_INTERNO");
	foreach ($result as $k => $f):
		$arr[] = array( 
			"id"	=> $f['ID'],
			"ds"	=> $f['DS_ITEM'],
			"sb"	=> $f['CD_ITEM_INTERNO'],
			"icon" => $f['CD_AREA_INTERNO'] == "REGULAR" ? "fa fa-check-square" : "fa fa-check-square-o"
		);
	endforeach;
	return $arr;
}

function getDomainMestrados(){
	$arr = array();
	
	$result = $GLOBALS['conn']->Execute("
		SELECT ID, DS_ITEM, CD_ITEM_INTERNO
		  FROM TAB_APRENDIZADO
		WHERE TP_ITEM = 'ES'
		  AND CD_AREA_INTERNO = 'ME'
		  AND CD_ITEM_INTERNO IS NOT NULL
	  ORDER BY DS_ITEM" );
	foreach ($result as $k => $f):
		$arr[] = array( 
			"id"	=> $f['ID'],
			"ds"	=> $f['DS_ITEM'],
			"sb"	=> $f['CD_ITEM_INTERNO'],
			"icon" => "fa fa-check-circle"
		);
	endforeach;
	return $arr;
}

function getDomainEspecialidades(){
	$arr = array();
	
	$result = $GLOBALS['conn']->Execute("
		SELECT ID, DS_ITEM, CD_ITEM_INTERNO
		  FROM TAB_APRENDIZADO
		WHERE TP_ITEM = 'ES'
		  AND CD_AREA_INTERNO <> 'ME'
		  AND CD_ITEM_INTERNO IS NOT NULL
	  ORDER BY DS_ITEM" );
	foreach ($result as $k => $f):
		$arr[] = array( 
			"id"	=> $f['ID'],
			"ds"	=> $f['DS_ITEM'],
			"sb"	=> $f['CD_ITEM_INTERNO'],
			"icon" => "fa fa-check-circle-o"
		);
	endforeach;
	return $arr;
}

function getDomainAreasEspecialidades(){
	$arr = array();
	
	$result = $GLOBALS['conn']->Execute("
		SELECT CD_AREA_INTERNO, DS_ITEM, CD_ITEM_INTERNO
		  FROM TAB_APRENDIZADO
		WHERE TP_ITEM = 'ES'
		  AND CD_ITEM_INTERNO IS NULL
	  ORDER BY DS_ITEM" );
	foreach ($result as $k => $f):
		$arr[] = array( 
			"id"	=> $f['CD_AREA_INTERNO'],
			"ds"	=> $f['DS_ITEM'],
			"sb"	=> $f['CD_ITEM_INTERNO'],
			"icon" => $f['CD_AREA_INTERNO'] == "ME" ? "fa fa-check-circle" : "fa fa-check-circle-o"
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
		$arr[] = array( 
			"id" => $f["MES"], 
			"ds" => mb_strtoupper(fDescMes($f["MES"])),
			"sb" => $f["MES"],
			"icon" => "fa fa-calendar"
		);
	endforeach;
	return $arr;
}
?>