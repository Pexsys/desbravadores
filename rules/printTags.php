<?php
@require_once("../include/functions.php");
@require_once("../include/tags.php");
responseMethod();

/****************************
 * Methods defined for use. *
 ****************************/
function getQueryByFilter( $parameters ) {
	$where = "";
	$aWhere = array();

	$query = "
		SELECT DISTINCT
			ca.ID_CAD_MEMBRO,
			ca.ID_MEMBRO,
			ca.NM
		FROM CON_ATIVOS ca
		LEFT JOIN APR_HISTORICO ah ON (ah.ID_CAD_PESSOA = ca.ID_CAD_PESSOA)
		LEFT JOIN TAB_APRENDIZADO ap ON (ap.ID = ah.ID_TAB_APREND)
		LEFT JOIN TAB_TP_APRENDIZADO ta ON (ta.ID = ap.TP_ITEM)
		LEFT JOIN CAD_COMPRAS ccp ON (ccp.ID_CAD_MEMBRO = ca.ID_CAD_MEMBRO)
		WHERE 1=1
	";

	if ( isset($parameters["filters"]) ):
		$keyAnt = "";
		foreach ($parameters["filters"] as $key => $v):
			$not = false;
			if ( isset($parameters["filters"][$key]["fg"]) ):
				$not = strtolower($parameters["filters"][$key]["fg"]) == "true";
			endif;
			$notStr = ( $not ? "NOT " : "" );
			if ( $key == "X" ):
				$where .= " AND ca.TP_SEXO ".$notStr."IN";
			elseif ( $key == "U" ):
				$where .= " AND ca.ID_UNIDADE ".$notStr."IN";
			elseif ( $key == "C" ):
				$where .= " AND NOT EXISTS (
							SELECT DISTINCT 1
							FROM TAB_APRENDIZADO ta1
							LEFT JOIN APR_HISTORICO ah1 ON (ah1.id_tab_aprend = ta1.id AND (ah1.dt_conclusao IS NOT NULL OR ah1.dt_investidura IS NOT NULL))
							WHERE ah1.ID_CAD_PESSOA = ca.ID_CAD_PESSOA
							  AND ta1.ID ".$notStr."IN
						";
			else:
				$where .= " AND";
			endif;

			$prim = true;
			$where .= " (";
			if ( is_array( $parameters["filters"][$key]["vl"] ) ):
				foreach ($parameters["filters"][$key]["vl"] as $value):
					if ( $key == "G" ):
						if ( $value == "3" ):
							$where .= (!$prim ? " OR " : "") ."ca.CD_FANFARRA IS ".( !$not ? "NOT NULL" : "NULL");
						elseif ( $value == "4" ):
						    $where .= (!$prim ? " OR " : "") ."ca.CD_CARGO ". ( empty($value) ? "IS ".$notStr."NULL" : $notStr."LIKE '2-04%'");
						elseif ( $value == "5" ):
						    $where .= (!$prim ? " OR " : "") ."ca.CD_CARGO ". ( empty($value) ? "IS ".$notStr."NULL" : $notStr."LIKE '2-07%'");
						elseif ( $value == "6" ):
						    $where .= (!$prim ? " OR " : "") ."ca.CD_CARGO ". ( empty($value) ? "IS ".$notStr."NULL" : $notStr."LIKE '1-01%'");
						else:
							$where .= (!$prim ? " OR " : "") ."ca.CD_CARGO ". ( empty($value) ? "IS ".$notStr."NULL" : $notStr."LIKE '$value%'");
						endif;
					elseif ( $key == "IC" ):
						if ( $value == "0" ):
							$where .= (!$prim ? " OR " : "") ."ccp.FG_COMPRA = 'S'";
						elseif ( $value == "1" ):
							$where .= (!$prim ? " OR " : "") ."ccp.FG_COMPRA = 'N'";
						elseif ( $value == "2" ):
							$where .= (!$prim ? " OR " : "") ."ccp.FG_ENTREGUE = 'S'";
						elseif ( $value == "3" ):
							$where .= (!$prim ? " OR " : "") ."ccp.FG_ENTREGUE = 'N'";
						elseif ( $value == "4" ):
							$where .= (!$prim ? " OR " : "") ."ccp.FG_PREVISAO = 'S'";
						elseif ( $value == "5" ):
							$where .= (!$prim ? " OR " : "") ."(ccp.FG_COMPRA = 'N' AND ccp.FG_PREVISAO = 'N')";
						endif;
					elseif ( empty($value) ):
						$aWhere[] = "NULL";
						$where .= (!$prim ? "," : "" )."?";
					else:
						$aWhere[] = $value;
						$where .= (!$prim ? "," : "" )."?";
					endif;
					$prim = false;
				endforeach;
			else:
				$aWhere[] = "$notStr"."NULL";
				$where .= "?";
			endif;
			$where .= ")";
			if ( $key == "C" ):
				$where .= ")";
			endif;
		endforeach;
	endif;
	$query .= " $where ORDER BY ca.NM";

	return CONN::get()->execute( $query, $aWhere );
}

function getTags( $parameters ) {
	$tags = PATTERNS::getBars()->getTagsTipo("tg","S");
	$arr = array();


	$result = CONN::get()->execute("
		SELECT DISTINCT pt.ID,
				pt.TP,
				pt.MD,
				ca.NM,
				ap.TP_ITEM,
				ap.CD_ITEM_INTERNO,
				ta.DS,
				ap.DS_ITEM
		FROM TMP_PRINT_TAGS pt
		INNER JOIN CON_ATIVOS ca ON (ca.ID_CAD_MEMBRO = pt.ID_CAD_MEMBRO)
		 LEFT JOIN CAD_COMPRAS ccp ON (ccp.ID_CAD_MEMBRO = pt.ID_CAD_MEMBRO)
		 LEFT JOIN TAB_APRENDIZADO ap ON (ap.ID = pt.ID_TAB_APREND)
		 LEFT JOIN TAB_TP_APRENDIZADO ta ON (ta.ID = ap.TP_ITEM)
	 ORDER BY pt.BC, ap.CD_ITEM_INTERNO, ca.NM
	");
	foreach ($result as $k => $fields):
		$option = PATTERNS::getBars()->getTagByID($fields['TP']);
		$ds = substr($option["ds"],2);

		if ($option["cl"] == "S" || !is_null($fields['DS_ITEM'])):
			$ds .= "-".($fields['DS_ITEM']);
		endif;

		$arr[] = array(
			"id" => $fields['ID'],
			"nm" => ($fields['NM']),
			"tp" => $ds,
			"md" => $fields['MD']
		);
	endforeach;
	return array( "result" => true, "tags" => $arr );
}

function getData( $parameters ){
	return array(	"result"	=> true,
					"membros"	=> getMembros( $parameters ),
					"tags"		=> PATTERNS::getBars()->getTagsTipo("tg","S"),
					"forms"		=> getFormsTipo()
	) ;
}

function getMembros( $parameters ) {
	$arr = array();

	$qtdZeros = zeroSizeID();

	$result = getQueryByFilter( $parameters );
	foreach ($result as $k => $fields):
		$arr[] = array(
			"id" => $fields['ID_CAD_MEMBRO'],
			"nm" => $fields['NM'],
			"sb" => fStrZero($fields['ID_MEMBRO'], $qtdZeros)
		);
	endforeach;
	return $arr;
}

function getMembrosFilter( $parameters ) {
	$arr = array();

	$result = getQueryByFilter( $parameters );
	foreach ($result as $k => $fields):
		$arr[] = $fields['ID_CAD_MEMBRO'];
	endforeach;
	return array( "filter" => $arr );
}

function getClasse( $parameters ){
	$arr = array();


	$query = "";

	$filtro = $parameters["filtro"];
	foreach ($filtro as $k => $ft):
		//PASTA DE AVALIACAO ou PASTA DE CLASSE
		//if ($ft == "1" || $ft == "C"):
		//	$query .= (!empty($query) ? " UNION " : "") ."SELECT ID, DS_ITEM, CD_ITEM_INTERNO
		//			  FROM TAB_APRENDIZADO
		//			 WHERE CD_ITEM_INTERNO LIKE '01%00'
		//	";
		//
		//CARTAO DE CLASSE ou CADERNO DE CLASSE
		//else:
			$query .= (!empty($query) ? " UNION " : "") ."SELECT ID, DS_ITEM, CD_ITEM_INTERNO
					  FROM TAB_APRENDIZADO
					 WHERE CD_ITEM_INTERNO LIKE '01%'
			";
		//endif;
	endforeach;

	$result = CONN::get()->execute("$query ORDER BY CD_ITEM_INTERNO");
	foreach ($result as $k => $line):
		$arr[] = array(
			"id"	=> $line['ID'],
			"ds"	=> $line['DS_ITEM'],
			"sb"	=> $line['CD_ITEM_INTERNO']
		);
	endforeach;
	return $arr;
}

function addTags( $parameters ){
	session_start();
	$tags = new TAGS();

	$frm = $parameters["frm"];
	$aTp = $frm["tp"];

	if ( isset($frm["ip"]) ):
		$aPessoa = $frm["ip"];
		$aAprend = array( null );

		if ( is_array($frm["ia"]) ):
			$aAprend = $frm["ia"];
		endif;

		foreach ($aTp as $t => $tp):
			foreach ($aPessoa as $k => $id):
				foreach ($aAprend as $j => $ia):
					$tags->insertItemTag($tp, $id, $ia);
				endforeach;
			endforeach;
		endforeach;
	endif;

	return array("result" => true);
}

function delete($parameters){
	$action = $parameters["action"];
	$tags = new TAGS();

	if ($action == "ALL"):
		session_start();
		$tags->deleteFila();
	else:
		$tags->deleteFilaIDS( implode(",", $parameters["ids"]) );
	endif;

	return array("result" => true);
}
?>
