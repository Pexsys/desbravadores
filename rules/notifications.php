<?php
@require_once("../include/functions.php");
responseMethod();

function getNotifications(){
	session_start();

	$arr = array();
	$arr = getItemNotify( $arr, "C", "fa-bullhorn", "Comunicados", PATTERNS::getVD()."dashboard/index.php?id=41" );
	$arr = getItemNotify( $arr, "O", "fa-exclamation-triangle", "Ocorr&ecirc;ncias", PATTERNS::getVD()."dashboard/index.php?id=51" );
	$arr = getItemNotify( $arr, "M", "fa-check-circle", "Mestrado Conclu&iacute;do", PATTERNS::getVD()."dashboard/index.php?id=16#mestrados" );

	$html = "";
	$qt = 0;

	foreach ($arr as $k => $item):
		if ($k > 0):
			$html .= "<li class=\"divider\"></li>";
		endif;
		$html .= $item["html"];
		$qt += $item["qt"];
	endforeach;
	return array( "result" => !empty($html), "html" => $html, "qt" => $qt );
}

function getItemNotify( $arr, $tp, $icon, $title, $url ){
	//VERIFICA COMUNICADOS PENDENTES
	$result = CONN::get()->Execute("
		SELECT MIN(DH_GERA) AS DT, COUNT(*) AS QT
		  FROM LOG_MENSAGEM
		 WHERE YEAR(DH_GERA) = YEAR(NOW())
		   AND DH_READ IS NULL
		   AND ID_CAD_USUARIO = ?
		   AND TP = ?
	", array($_SESSION['USER']['ID'], $tp));

	if (!$result->EOF):
		$qt = $result->fields["QT"];
		if ( $qt > 0 ):
			$arr[] = array(
				"html" => "<li>
					<a href=\"$url\">
						<div>
						<i class=\"fa $icon fa-fw\"></i>&nbsp;$title&nbsp;
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
?>
