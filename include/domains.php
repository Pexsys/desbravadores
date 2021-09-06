<?php
function getDomainMembrosAtivos(){
	$arr = array();
	
	$qtdZeros = zeroSizeID();
	$result = CONN::get()->Execute("
		SELECT ID_CAD_MEMBRO, ID_MEMBRO, NM, IDADE_HOJE
		FROM CON_ATIVOS 
		ORDER BY NM
	");
	foreach($result as $l => $f):
		$a = array(
			"id" => $f['ID_CAD_MEMBRO'],
			"ds" => $f['NM'],
			"sb" => fStrZero($f['ID_MEMBRO'], $qtdZeros),
			"icon" => ($f['IDADE_HOJE'] < 18 ? "fas fa-circle" : "far fa-circle")
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
	$result = CONN::get()->Execute("
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
			"icon" => ($f['IDADE_HOJE'] < 18 ? "fas fa-circle" : "far fa-circle")
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
	
	$result = CONN::get()->Execute("
		SELECT ID, DS, DH_S 
		FROM EVE_SAIDA 
		ORDER BY DH_S DESC
	");
	foreach ($result as $k => $f):
		$arr[] = array(
			"id"	=> $f['ID'],
			"ds"	=> $f['DS'],
			"sb"	=> strftime("%d/%m/%Y",strtotime($f["DH_S"])),
			"icon" => "far fa-calendar-alt"
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
	
	$result = CONN::get()->Execute($query);
	foreach ($result as $k => $f):
		$arr[] = array( 
			"id"	=> $f['ID'],
			"ds"	=> $f['DS'],
			"sb"  => $f['IDADE'],
			"icon" => ($f['TP'] == "F" ? "fas fa-female" : ( $f['TP'] == "M" ? "fas fa-male" : "fas fa-user-secret" ) )
		);
	endforeach;
	return $arr;
}

function getDomainRegimeAlimentar($fAtivo = false){
	$arr = array();
	$result = CONN::get()->Execute("SELECT ID, DS FROM TAB_TP_REG_ALIM ORDER BY DS");
	foreach ($result as $k => $f):
		$arr[] = array( 
			"id"	=> $f['ID'],
			"ds"	=> $f['DS'],
		);
	endforeach;
	return $arr;
}

function getDomainRestricaoAlimentar($fAtivo = false){
	$arr = array();
	$result = CONN::get()->Execute("SELECT ID, DS FROM TAB_TP_REST_ALIM ORDER BY DS");
	foreach ($result as $k => $f):
		$arr[] = array( 
			"id"	=> $f['ID'],
			"ds"	=> $f['DS'],
		);
	endforeach;
	return $arr;
}

function getTipoAprendizado(){
	$arr = array();
	
	$result = CONN::get()->Execute("
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
	
	$result = CONN::get()->Execute("
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
	
	$result = CONN::get()->Execute("
		SELECT ID, DS_ITEM, CD_AREA_INTERNO
		  FROM TAB_APRENDIZADO
		WHERE TP_ITEM = 'CL'
	  ORDER BY CD_ITEM_INTERNO");
	foreach ($result as $k => $f):
		$arr[] = array( 
			"id"	=> $f['ID'],
			"ds"	=> $f['DS_ITEM'],
			"sb"	=> $f['CD_ITEM_INTERNO'],
			"icon" => $f['CD_AREA_INTERNO'] == "REGULAR" ? "fas fa-check-square" : "far fa-check-square"
		);
	endforeach;
	return $arr;
}

function getDomainMestrados(){
	$arr = array();
	
	$result = CONN::get()->Execute("
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
			"icon" => "fas fa-check-circle"
		);
	endforeach;
	return $arr;
}

function getDomainEspecialidades(){
	$arr = array();
	
	$result = CONN::get()->Execute("
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
			"icon" => "fas fa-check-circle"
		);
	endforeach;
	return $arr;
}

function getDomainAreasEspecialidades(){
	$arr = array();
	
	$result = CONN::get()->Execute("
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
			"icon" => $f['CD_AREA_INTERNO'] == "ME" ? "fas fa-check-circle" : "fas fa-check-circle"
		);
	endforeach;
	return $arr;
}

function getDomainMerito(){
	$arr = array();
	$result = CONN::get()->Execute("
		SELECT ID, CD_ITEM_INTERNO, DS_ITEM
		  FROM TAB_APRENDIZADO
		 WHERE TP_ITEM = ?
	  ORDER BY CD_ITEM_INTERNO", array( "MT" ) );
    foreach ($result as $k => $f):
		$arr[] = array(
			"id" => $f["ID"], 
			"ds" => $f["DS_ITEM"],
			"sb" => $f['CD_ITEM_INTERNO'],
			"icon" => $f['CD_ITEM_INTERNO'] == "ETS" ? "far fa-star" : "fas fa-award"
		);
	endforeach;
	return $arr;
}

function getMesAniversario(){
	$result = CONN::get()->Execute("
		SELECT DISTINCT DATE_FORMAT(DT_NASC,'%m') AS MES
		  FROM CON_ATIVOS
	  ORDER BY 1
	");
	foreach ($result as $k => $f):
		$arr[] = array( 
			"id" => $f["MES"], 
			"ds" => mb_strtoupper(fDescMes($f["MES"])),
			"sb" => $f["MES"],
			"icon" => "far fa-calendar-alt"
		);
	endforeach;
	return $arr;
}
?>
