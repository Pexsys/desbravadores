<?php
@require_once("../include/functions.php");
responseMethod();

function getNotifications(){
	session_start();
	
	$arr = array(
		array( "id" => "notifyAlerts", "html" => "", "qt" => 0 ),
		array( "id" => "notifyTasks", "html" => "", "qt" => 0 )
	);
	$arr[0] = getItemAlert( $arr[0], "C", "fas fa-bullhorn", "Comunicados", PATTERNS::getVD()."dashboard/index.php?id=41" );
	$arr[0] = getItemAlert( $arr[0], "O", "fas fa-exclamation-triangle", "Ocorr&ecirc;ncias", PATTERNS::getVD()."dashboard/index.php?id=51" );
	$arr[0] = getItemAlert( $arr[0], "M", "fas fa-check-circle", "Mestrado Conclu&iacute;do", PATTERNS::getVD()."dashboard/index.php?id=16#mestrados" );

	$arr[1] = getClassPend( $arr[1] );
	$arr[1] = getMestrMembros( $arr[1] );

	return array( "result" => true, "notifying" => $arr );
}

function getItemAlert( $arr, $tp, $icon, $title, $url ){
	$result = CONN::get()->Execute("
		SELECT MIN(DH_GERA) AS DT, COUNT(*) AS QT
		  FROM LOG_MENSAGEM
		 WHERE YEAR(DH_GERA) = YEAR(NOW())
		   AND DH_READ IS NULL
		   AND ID_CAD_USUARIO = ?
		   AND TP = ?
	", array($_SESSION['USER']['id'], $tp));
	
	if (!$result->EOF):
		$qt = $result->fields["QT"];
		if ( $qt > 0 ):
			$arr[] = array(
				"html" => "<li>
					<a href=\"$url\">
						<div>
						<i class=\"$icon fa-fw\"></i>&nbsp;$title&nbsp;
						<span class=\"badge progress-bar-danger\">$qt</span>
						<span class=\"text-muted pull-right\">". strftime("%d/%m/%Y",strtotime($result->fields["DT"])) ."</span>
						</div>
					</a>
				</li>",
				"qt" => $qt
			);
		endif;
	endif;
	

	/* TAREFAS
	 *
	 *
	 <li>
	 <a href="#">
	 <div>
	 <p>
	 <strong>Task 1</strong>
	 <span class="pull-right text-muted">40% Complete</span>
	 </p>
	 <div class="progress progress-striped active">
	 <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%">
	 <span class="sr-only">40% Complete (success)</span>
	 </div>
	 </div>
	 </div>
	 </a>
	 </li>
	 <li class="divider"></li>
	 */
	return $arr;
}
function getClassPend($arr){
	$rs = CONN::get()->Execute("
		SELECT ID_CAD_PESSOA, ID_TAB_APREND, TP_ITEM, CD_AREA_INTERNO, CD_ITEM_INTERNO, DS_ITEM, DT_INICIO, COUNT(*) AS QT_REQ
		FROM CON_APR_PESSOA
		WHERE ID_CAD_PESSOA = ? AND DT_CONCLUSAO IS NULL
		GROUP BY ID_CAD_PESSOA, ID_TAB_APREND, TP_ITEM, CD_AREA_INTERNO, CD_ITEM_INTERNO, DS_ITEM, DT_INICIO
		ORDER BY TP_ITEM, CD_ITEM_INTERNO
	", array( $_SESSION['USER']['id_cad_pessoa'] ) );
	foreach ($rs as $ks2 => $det):
		$qtdReq = $det["QT_REQ"];
		$tabAprID = $det["ID_TAB_APREND"];
		$pct = 0;
		$qtd = 0;
		$rc = CONN::get()->Execute("
			SELECT COUNT(*) AS QT_COMPL
			FROM CON_APR_PESSOA
			WHERE ID_CAD_PESSOA = ?
			AND ID_TAB_APREND = ?
			AND DT_ASSINATURA IS NOT NULL
		", array($_SESSION['USER']['id_cad_pessoa'], $tabAprID) );
		if (!$rc->EOF):
			$qtd = $rc->fields["QT_COMPL"];
		endif;
		$pctC = floor( ( $qtd / $qtdReq ) * 100);
		$pctP = floor( 100 - $pctC);
		$qtdP = ($qtdReq-$qtd);

		$arr["html"] .= "<li><a href=\"".PATTERNS::getVD()."dashboard/index.php?id=16\">";
		$arr["html"] .= "<div class=\"col-lg-12\">";
		$arr["html"] .= "<label class=\"control-label\"><i class=\"".getIconAprendizado( $det["TP_ITEM"], $det["CD_AREA_INTERNO"] )."\"></i>&nbsp;".titleCase($det["DS_ITEM"])."</label>";
		$arr["html"] .= "<div class=\"progress progress-striped active\" style=\"cursor:pointer\">";
		$arr["html"] .= "<div class=\"progress-bar progress-bar-success\" role=\"progressbar\" style=\"width:$pctC%\">$pctC%</div>";
		$arr["html"] .= "<div class=\"progress-bar progress-bar-danger\" role=\"progressbar\" style=\"width:$pctP%\">$pctP%</div>";
		$arr["html"] .= "</div></a></li>";
		$arr["qt"]++;
	endforeach;
	return $arr;
}
function getMestrMembros($arr){
	$can = CONN::get()->Execute("
		SELECT *
		FROM CAD_USUARIO cu
		INNER JOIN CAD_USU_PERFIL cuf ON (cuf.ID_CAD_USUARIO = cu.ID)
		INNER JOIN TAB_PERFIL_ITEM tpi ON (tpi.ID_TAB_PERFIL = cuf.ID_PERFIL)
		WHERE tpi.ID_TAB_DASHBOARD IN (2,6,18)
		AND cu.ID = ?
	", array($_SESSION['USER']['id']) );
	if (!$can->EOF):
		$rs = CONN::get()->Execute("
			SELECT lm.ID_ORIGEM, ah.ID_TAB_APREND, cu.ID_CAD_PESSOA, cu.DS_USUARIO, ta.DS_ITEM, ta.TP_ITEM, ta.CD_AREA_INTERNO
			FROM LOG_MENSAGEM lm
			INNER JOIN CAD_USUARIO cu ON (cu.ID = lm.ID_CAD_USUARIO)
			INNER JOIN CON_ATIVOS ca ON (ca.ID_CAD_PESSOA = cu.ID_CAD_PESSOA)
			INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = lm.ID_ORIGEM)
			LEFT JOIN APR_HISTORICO ah ON (ah.ID_TAB_APREND = ta.ID AND ah.ID_CAD_PESSOA = ca.ID_CAD_PESSOA)
			WHERE lm.TP = 'M'
			AND ah.DT_CONCLUSAO IS NULL
		");
		foreach ($rs as $ks2 => $det):
			$arr["html"] .= "<li>";
			$arr["html"] .= "<div class=\"col-lg-12\">";
			$arr["html"] .= "<label class=\"control-label\" style=\"text-align:center\"><i class=\"".getIconAprendizado( $det["TP_ITEM"], $det["CD_AREA_INTERNO"] )."\"></i>&nbsp;".titleCase($det["DS_ITEM"])."</label>";
			$arr["html"] .= "<div class=\"progress progress-striped active\">";
			$arr["html"] .= "<div class=\"progress-bar progress-bar-warning\" role=\"progressbar\" style=\"width:100%;color:#404040\">". titleCase($det["DS_USUARIO"]) ."</div>";
			$arr["html"] .= "</div></li>";
			$arr["qt"]++;
		endforeach;
	endif;
	return $arr;
}
?>