<?php
@require_once("../include/functions.php");
responseMethod();

function getQueryByFilter( $parameters ) {
	session_start();
	
	$userID = $_SESSION['USER']['id_usuario'];
	$membroID = $_SESSION['USER']['id_cad_pessoa'];
	$out = array();
	$frm = null;
	
	$like = "";
	$result = $GLOBALS['conn']->Execute("
		SELECT CD_CARGO, CD_CARGO2
		  FROM CON_ATIVOS
		 WHERE ID_CAD_PESSOA = ?
	", array($membroID) );
	$cargo = $result->fields['CD_CARGO'];
	if (fStrStartWith($cargo,"2-07")):
		$cargo = $result->fields['CD_CARGO2'];
	endif;
	if (empty($cargo)):
		return $arr;
	endif;
	if ($cargo != "2-04-00" && fStrStartWith($cargo,"2-04")):
		$like = "01-".substr($cargo,-2);
	endif;

	$str = "
		SELECT cd.ID, cd.SQ, cd.DH, cd.FG_PEND, 
			ta.DS_ITEM, 
			taa.CD AS CD_AREA, taa.DS AS DS_AREA, 
			tap.CD_REQ_INTERNO, tap.DS,
			(SELECT COUNT(*)
				FROM CON_APR_PESSOA
				WHERE ID = tap.ID
				  AND DT_CONCLUSAO IS NULL
				". ($parameters["filter"] == "Y" ? " AND YEAR(DT_INICIO) = YEAR(NOW())" : "") .") AS QTD_TOTAL,
			(SELECT COUNT(*)
				FROM CON_APR_PESSOA 
				WHERE ID = tap.ID
				  AND DT_CONCLUSAO IS NULL
				  AND DT_ASSINATURA IS NOT NULL
				  ". ($parameters["filter"] == "Y" ? " AND YEAR(DT_INICIO) = YEAR(NOW())" : "") .") AS QTD_COMPL
		FROM CAD_DIARIO cd
	INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = cd.ID_TAB_APREND)
	LEFT JOIN TAB_APR_ITEM tap ON (tap.ID = cd.ID_TAB_APR_ITEM)
	LEFT JOIN TAB_APR_AREA taa ON (taa.ID = tap.ID_TAB_APR_AREA)
		WHERE ta.CD_ITEM_INTERNO LIKE '$like%'
		". ($parameters["filter"] == "Y" ? " AND YEAR(cd.DH) = YEAR(NOW())" : "") ."
		ORDER BY cd.SQ DESC
	";
	//exit($str);

	return $GLOBALS['conn']->Execute($str);
}

function fRegistro( $parameters ) {
	session_start();
	fConnDB();
	
	$userID = $_SESSION['USER']['id_usuario'];
	$membroID = $_SESSION['USER']['id_cad_pessoa'];
	$out = array();
	$frm = null;
	
	$like = "";
	$result = $GLOBALS['conn']->Execute("
		SELECT CD_CARGO, CD_CARGO2
		  FROM CON_ATIVOS
		 WHERE ID_CAD_PESSOA = ?
	", array($membroID) );
	$cargo = $result->fields['CD_CARGO'];
	if (fStrStartWith($cargo,"2-07")):
		$cargo = $result->fields['CD_CARGO2'];
	endif;
	if (empty($cargo)):
		return $arr;
	endif;
	if ($cargo != "2-04-00" && fStrStartWith($cargo,"2-04")):
		$like = "01-".substr($cargo,-2);
	endif;

	if ( isset($parameters["frm"]) ):
		$frm = $parameters["frm"];
	endif;
	$op = isset($parameters["op"]) ? $parameters["op"] : "";

	if ( $op == "UPDATE" ):
		$fg_pend = $frm["fg_pend"];
		$id = $frm["id"];
		
		$arr = array();
		//INSERT DE NOVA OCORRENCIA
		if ( !is_null($id) && is_numeric($id) ):
			$arr = array(
				fStrToDate($frm["dh"]),
				fReturnStringNull($fg_pend),
				fReturnStringNull(trim($frm["id_classe"])),
				fReturnStringNull(trim($frm["id_req"])),
				fReturnStringNull(trim($frm["id_ref"])),
				$frm["sq"],
				fReturnStringNull(trim($frm["txt"])),
				$id
			);
			$GLOBALS['conn']->Execute("
				UPDATE CAD_DIARIO SET
					DH = ?,
					FG_PEND = ?,
					ID_TAB_APREND = ?,
					ID_TAB_APR_ITEM = ?,
					ID_TAB_APR_ITEM_SEL = ?,
					SQ = ?,
					TXT = ?
				WHERE ID = ?
			",$arr);
		else:
			$arr = array(
				fStrToDate($frm["dh"]),
				fReturnStringNull($fg_pend),
				fReturnStringNull(trim($frm["id_classe"])),
				fReturnStringNull(trim($frm["id_req"])),
				fReturnStringNull(trim($frm["id_ref"])),
				$userID,
				$frm["sq"],
				fReturnStringNull(trim($frm["txt"]))
			);
			$GLOBALS['conn']->Execute("
				INSERT INTO CAD_DIARIO(
					DH,
					FG_PEND,
					ID_TAB_APREND,
					ID_TAB_APR_ITEM,
					ID_TAB_APR_ITEM_SEL,
					ID_USUARIO_INS,
					SQ,
					TXT
				) VALUES (?,?,?,?,?,?,?,?)
			",$arr);
			$id = $GLOBALS['conn']->Insert_ID();
		endif;
		
		$out["id"] = $id;
		$out["so"] = $fg_pend;
		$out["success"] = true;

	//EXCLUSAO DE SAIDA
	elseif ( $op == "DELETE" ):
		$GLOBALS['conn']->Execute("DELETE FROM CAD_DIARIO WHERE ID = ?", Array( $parameters["id"] ) );
		$out["success"] = true;

	//GET SAIDA
	else:

		if ( $parameters["id"] == "Novo" ):
			$out["success"] = true;
			$out["diario"] = array( "fg_pend" => "S" );
		else:
			$result = $GLOBALS['conn']->Execute("
				SELECT *
				  FROM CAD_DIARIO cd
			INNER JOIN CAD_USUARIOS cu ON (cu.ID_USUARIO = cd.ID_USUARIO_INS)
				 WHERE cd.ID = ?
			", array( $parameters["id"] ) );
			if (!$result->EOF):
				$out["success"] = true;
				$out["diario"] = array(
					"id"			=> $result->fields['ID'],
					"id_classe"		=> $result->fields['ID_TAB_APREND'],
					"id_req"		=> $result->fields['ID_TAB_APR_ITEM'],
					"id_ref"		=> $result->fields['ID_TAB_APR_ITEM_SEL'],
					"sq"			=> $result->fields['SQ'],
					"dh"			=> strtotime($result->fields['DH'])."000",
					"txt"			=> trim($result->fields['TXT']),
					"fg_pend"		=> $result->fields['FG_PEND']
				);
				$out["req"] = fGetReq( $result->fields['ID_TAB_APREND'] );
				$out["ref"] = fGetRefByID( $result->fields['ID_TAB_APR_ITEM'] );
			endif;
			
		endif;

		$rc = $GLOBALS['conn']->Execute("
			SELECT ID, DS_ITEM
				FROM TAB_APRENDIZADO
				WHERE CD_ITEM_INTERNO LIKE '$like%'
				AND TP_ITEM = 'CL'
				AND TP_PARA = 'DL'
			ORDER BY CD_ITEM_INTERNO
		");
		foreach ($rc as $r => $f):
			$out["classe"][] = array(
				"id" => $f['ID'],
				"ds" => $f['DS_ITEM']
			);
		endforeach;

	endif;
	return $out;
}

function fGetCompl( $parameters ){
	fConnDB();

	$result = $GLOBALS['conn']->Execute("
		SELECT MAX(SQ)+1 AS SQ 
		FROM CAD_DIARIO 
		WHERE ID_TAB_APREND = ?
		AND YEAR(DH) = YEAR(NOW())
	", array($parameters["id_classe"]) );
	$sq = ( !is_null($result->fields["SQ"]) ? $result->fields["SQ"] : 1);

	return array( "sq" => $sq, "req" => fGetReq( $parameters["id_classe"] ) );
}

function fGetReq( $classeID ){
	$arr = array();
	$result = $result = $GLOBALS['conn']->Execute("
		   SELECT tap.ID, taa.SEQ, taa.CD AS CD_AREA, taa.DS AS DS_AREA, tap.CD_REQ_INTERNO, tap.DS, tap.QT_MIN
			 FROM TAB_APR_ITEM tap 
		LEFT JOIN TAB_APR_AREA taa ON (taa.ID = tap.ID_TAB_APR_AREA)
			WHERE tap.ID_TAB_APREND = ? 
		 ORDER BY taa.SEQ, tap.CD_REQ_INTERNO
	", array( $classeID ) );

	foreach ($result as $k => $fields):
		$dsReq = (!is_null($fields['CD_AREA']) ? $fields['CD_AREA']."-" : "") . 
				substr($fields['CD_REQ_INTERNO'],-2) . 
				 (!is_null($fields['DS']) ? " ".$fields['DS'] : "");

		$arr[] = array(
			"id" => $fields['ID'],
			"ds" => $dsReq,
			"tp" => (!is_null($fields['QT_MIN']) ? "E" : "")
		);
	endforeach;
	return $arr;
}

function fGetRef( $parameters ){
	fConnDB();
	return fGetRefByID( $parameters["id_req"] );
}

function fGetRefByID( $refID ){
	session_start();
	$where = "
		AND NOT EXISTS (SELECT 1
		FROM CAD_DIARIO cd
	INNER JOIN TAB_APR_ITEM_SEL tais2 ON (tais2.ID = cd.ID_TAB_APR_ITEM_SEL)
	WHERE YEAR(cd.DH) = YEAR(NOW())
		AND tais2.ID_REF = tais.ID_REF
	)		
	";
	//SE PERFIL DE DIRETORES / INSTRUTOR GERAL, PERMITE A MESMA ESPECIALIDADE PARA MAIS DE UMA CLASSE
	$pessoaID = $_SESSION['USER']['id_cad_pessoa'];
	$result = $GLOBALS['conn']->Execute("
		SELECT CD_CARGO, CD_CARGO2
		  FROM CON_ATIVOS
		 WHERE ID_CAD_PESSOA = ?
	", array($pessoaID) );
	if (fStrStartWith($result->fields['CD_CARGO'],"2-01") || $result->fields['CD_CARGO'] == "2-04-00"):
		$where = "";
	endif;

	$arr = array();
	$result = $GLOBALS['conn']->Execute("
		SELECT tais.ID, ta.CD_AREA_INTERNO, ta.CD_ITEM_INTERNO, ta.DS_ITEM
		FROM TAB_APR_ITEM_SEL tais
		INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = tais.ID_REF)
		WHERE tais.ID_TAB_APR_ITEM = ?
		$where
		ORDER BY ta.CD_AREA_INTERNO, ta.DS_ITEM
	", array( $refID ) );

	foreach ($result as $k => $fields):
		$arr[] = array( 
			"id"	=> $fields['ID'],
			"ds"	=> $fields['DS_ITEM'],
			"sb"	=> $fields['CD_ITEM_INTERNO']
		);
	endforeach;
	return $arr;
}

function getListaDiario( $parameters ){
	$arr = array();
	fConnDB();
	
	$result = getQueryByFilter( $parameters );

	foreach ($result as $k => $fields):
		$dsReq = (!is_null($fields['CD_AREA']) ? $fields['CD_AREA']."-" : "") . 
				substr($fields['CD_REQ_INTERNO'],-2) . 
				 (!is_null($fields['DS']) ? " ".substr($fields['DS'],0,70) : "");

		$perc = floor(($fields['QTD_COMPL'] / max(1,$fields['QTD_TOTAL']))*100);		
		$cl = 'success';
		if ($perc < 75):
			$cl = 'danger';
		elseif ($perc < 100):
			$cl = 'primary';
		endif;

		$arr[] = array(
			"id" => $fields['ID'],
			"sq" => $fields['SQ'],
			"cl" => $fields['DS_ITEM'],
			"rq" => $dsReq,
			"st" => $fields['FG_PEND'],
			"so" => $fields['FG_PEND'],
			"dh" => strtotime($fields['DH']),
			"in" => array( "pc" => $perc, "cl" => $cl)
		);
	endforeach;
	return array( "result" => true, "diario" => $arr );
}

function fDetalheItem( $parameters ){
	fConnDB();
	$str = "";

	$pendentes = $GLOBALS['conn']->Execute("
		SELECT cap.DS, ca.NM
		FROM CAD_DIARIO cd
		INNER JOIN CON_APR_PESSOA cap ON (cap.ID = cd.ID_TAB_APR_ITEM)
		INNER JOIN CON_ATIVOS ca ON (ca.ID_CAD_PESSOA = cap.ID_CAD_PESSOA)
		WHERE cap.TP_ITEM = 'CL'
		AND cap.DT_CONCLUSAO IS NULL
		AND cap.DT_ASSINATURA IS NULL
		". ($parameters["filter"] == "Y" ? " AND YEAR(cap.DT_INICIO) = YEAR(NOW())" : "") ."
		AND cd.ID = ?
		ORDER BY ca.NM
	", array( $parameters["id"] ) );

	$completados = $GLOBALS['conn']->Execute("
		SELECT cap.DS, ca.NM, cap.DT_ASSINATURA
		FROM CAD_DIARIO cd
		INNER JOIN CON_APR_PESSOA cap ON (cap.ID = cd.ID_TAB_APR_ITEM)
		INNER JOIN CON_ATIVOS ca ON (ca.ID_CAD_PESSOA = cap.ID_CAD_PESSOA)
		WHERE cap.TP_ITEM = 'CL'
		AND cap.DT_CONCLUSAO IS NULL
		AND cap.DT_ASSINATURA IS NOT NULL
		". ($parameters["filter"] == "Y" ? " AND YEAR(cap.DT_INICIO) = YEAR(NOW())" : "") ."
		AND cd.ID = ?
		ORDER BY cap.DT_ASSINATURA, ca.NM
	", array( $parameters["id"] ) );

	if (!$pendentes->EOF || !$completados->EOF):
		$titulo = !$pendentes->EOF ? $pendentes->fields["DS"] : $completados->fields["DS"];
		$str .= "
		<div class=\"col-xs-12 col-md-12 col-sm-12 col-xl-12 col-lg-12\">
		<div class=\"panel panel-{$parameters["cl"]}\">
		<div class=\"panel-heading\"><h6 class=\"panel-title\">$titulo</h6></div>
			<div class=\"panel-body\">
		";
		$str .= blocoItemReq($pendentes, "danger", "fa-frown-o", "Pendentes");
		$str .= blocoItemReq($completados, "success", "fa-smile-o", "Completados");
		$str .= "</div></div></div>";
	endif;

	return $str;
}

function blocoItemReq($result, $class, $icon, $titulo){
	$str = "";
	if (!$result->EOF):
		$str .= "<div class=\"col-sm-6 col-xs-6 col-md-6 col-lg-6\">";
		$str .= "<div class=\"row\">";
		$str .= "<div class=\"panel panel-$class\" style=\"margin-bottom:1px\">";
		$str .= "<div class=\"panel-heading\" style=\"padding:3px 10px\">
					<i class=\"fa $icon\" aria-hidden=\"true\"></i>&nbsp;$titulo
					<span class=\"badge badge-pill progress-bar-$class pull-right\">{$result->RecordCount()}</span>
				</div>";
		$str .= "<div class=\"panel-body\" style=\"padding:5px 10px\">";
		$str .= "<div class=\"col-sm-12 col-xs-12 col-md-12 col-lg-12\">";
		foreach ($result as $k => $f):
			$aux = ( !is_null($f["DT_ASSINATURA"]) ? "<span class=\"badge pull-left\">".strftime("%d/%m/%Y",strtotime($f["DT_ASSINATURA"]))."</span>" : "");
			$str .= "<div class=\"row\">$aux&nbsp;{$f["NM"]}</div>";
		endforeach;
		$str .= "</div></div></div></div></div>";
	endif;
	return $str;
}
?>