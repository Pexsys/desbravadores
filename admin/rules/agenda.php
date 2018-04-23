<?php
@require_once("../../include/functions.php");
responseMethod();

/****************************
 * Methods defined for use. *
 ****************************/
function events( $parameters ) {
	$DATA_NOW = date('Y-m-d H:i:s');

	$out = array();
	$aBind = array();

	$query = "SELECT e.*,
					 c.TP_GRUPO, c.ID_TAB_RGR_CHAMADA, c.ID_TAB_TP_UNIFORME,
					 r.DS as DS_RGR_CHAMADA,
					 u.DS as DS_TP_UNIFORME
		    FROM CAD_EVENTOS e
	       LEFT JOIN RGR_CHAMADA c ON (c.ID_EVENTO = e.ID_EVENTO)
		   LEFT JOIN TAB_RGR_CHAMADA r ON (r.ID = c.ID_TAB_RGR_CHAMADA)
		   LEFT JOIN TAB_TP_UNIFORME u ON (u.ID = c.ID_TAB_TP_UNIFORME)";

	if ( isset($parameters["id"]) ):
		$query .= " WHERE e.ID_EVENTO = ?";
		$aBind = array($parameters["id"]);
	else:
		$query .= " WHERE e.DTHORA_EVENTO_INI >= ?
			     AND e.DTHORA_EVENTO_FIM <= ?
			ORDER BY e.DTHORA_EVENTO_INI ";
		$aBind = array( $parameters["from"], $parameters["to"] );
	endif;
	$result = CONN::get()->Execute( $query, $aBind );

	while (!$result->EOF):

		$dh_ini = $result->fields['DTHORA_EVENTO_INI'];
		$dh_fim = $result->fields['DTHORA_EVENTO_FIM'];

		$dt_hora_eve = fDtHoraEvento( $dh_ini, $dh_fim, "%d/%m" );

		$ds_info_add = trim($result->fields['INFO_ADIC']);

		$title = $dt_hora_eve;
		$title = fConcatNoEmpty($title, " - ", $ds_info_add);
		if (!is_null($result->fields['TP_GRUPO'])):
			$title .= " - G:".$result->fields['TP_GRUPO'];
		endif;
		if (!is_null($result->fields['ID_TAB_RGR_CHAMADA'])):
			$title .= " - R:".$result->fields['DS_RGR_CHAMADA'];
		endif;
		if (!is_null($result->fields['ID_TAB_TP_UNIFORME'])):
			$title .= " - U:".$result->fields['DS_TP_UNIFORME'];
		endif;
		if (!is_null($result->fields['FG_INSTRUCAO'])):
			$title .= " - I:".$result->fields['FG_INSTRUCAO'];
		endif;

		$tipo_evento = trim($result->fields['TIPO_EVENTO']);

		$out[] = array(
			'id' => $result->fields['ID_EVENTO'],
			'title' => ($title),
			'url' => '',
			'class' => ( $dh_fim < $DATA_NOW ? '' : fGetClass($tipo_evento) ),
			'info' =>
				 array(
					"id"		=> $result->fields['ID_EVENTO'],
					"dh_ini"	=> strtotime($dh_ini) .'000',
					"dh_fim"	=> strtotime($dh_fim) .'000',
					"ds_info"	=> ($ds_info_add),
					"ds_local"	=> (trim($result->fields['DESC_LOCAL'])),
					"ds_logra"	=> (trim($result->fields['DESC_LOGRADOURO'])),
					"nr_logra"	=> (trim($result->fields['NUM_LOGRADOURO'])),
					"ds_cmpl"	=> (trim($result->fields['DESC_COMPLEMENTO'])),
					"ds_bai"	=> (trim($result->fields['DESC_BAIRRO'])),
					"ds_cid"	=> (trim($result->fields['DESC_CIDADE'])),
					"cd_uf"		=> $result->fields['COD_UF'],
					"fg_publ"	=> $result->fields['FLAG_PUBLICACAO'],
					"tp_eve"	=> $tipo_evento,
					"tp_grupo"	=> $result->fields['TP_GRUPO'],
					"id_regra"	=> $result->fields['ID_TAB_RGR_CHAMADA'],
					"id_uniforme"	=> $result->fields['ID_TAB_TP_UNIFORME'],
					"fg_instrucao"	=> $result->fields['FG_INSTRUCAO']
				),
			'start' => strtotime($dh_ini) .'000',
			'end' => strtotime($dh_fim) .'000'
		);
		$result->MoveNext();
	endwhile;
	return array( 'success' => 1, 'result' => $out );
}

function fEvent( $parameters ) {
	$out = array();
	$out["success"] = false;

	$frm = $parameters["frm"];
	$op = $parameters["op"];
	$id = 0;

	//LEITURA DE EVENTO.
	//ATUALIZACAO DE EVENTO
	if ( $op == "UPDATE" ):

		$arr = array();
		//INSERT DE NOVO EVENTO
		if ( !is_null($frm["id"]) && is_numeric($frm["id"]) ):
			$id = $frm["id"];
			$query = "
			UPDATE CAD_EVENTOS SET
				DTHORA_EVENTO_INI = ?,
				DTHORA_EVENTO_FIM = ?,
				DESC_LOCAL = ?,
				DESC_LOGRADOURO = ?,
				NUM_LOGRADOURO = ?,
				DESC_COMPLEMENTO = ?,
				DESC_BAIRRO = ?,
				DESC_CIDADE = ?,
				COD_UF = ?,
				INFO_ADIC = ?,
				TIPO_EVENTO = ?,
				FG_INSTRUCAO = ?,
				FLAG_PUBLICACAO = ?
			WHERE ID_EVENTO = ? ";
			$arr = array(
				fStrToDate($frm["dh_ini"]),
				fStrToDate($frm["dh_fim"]),
				fReturnStringNull($frm["ds_local"]),
				fReturnStringNull($frm["ds_logra"]),
				fReturnStringNull($frm["nr_logra"]),
				fReturnStringNull($frm["ds_cmpl"]),
				fReturnStringNull($frm["ds_bai"]),
				fReturnStringNull($frm["ds_cid"]),
				fReturnStringNull($frm["cd_uf"]),
				fReturnStringNull($frm["ds_info"]),
				fReturnStringNull($frm["tp_eve"]),
				fReturnStringNull($frm["fg_instrucao"]),
				fReturnStringNull($frm["fg_publ"]),
				$id
			);
			CONN::get()->Execute($query,$arr);

		else:
			$query = "
			INSERT INTO CAD_EVENTOS (
				DTHORA_EVENTO_INI,
				DTHORA_EVENTO_FIM,
				DESC_LOCAL,
				DESC_LOGRADOURO,
				NUM_LOGRADOURO,
				DESC_COMPLEMENTO,
				DESC_BAIRRO,
				DESC_CIDADE,
				COD_UF,
				INFO_ADIC,
				TIPO_EVENTO,
				FG_INSTRUCAO,
				FLAG_PUBLICACAO
			) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";

			$arr = array(
				fStrToDate($frm["dh_ini"]),
				fStrToDate($frm["dh_fim"]),
				fReturnStringNull($frm["ds_local"]),
				fReturnStringNull($frm["ds_logra"]),
				fReturnStringNull($frm["nr_logra"]),
				fReturnStringNull($frm["ds_cmpl"]),
				fReturnStringNull($frm["ds_bai"]),
				fReturnStringNull($frm["ds_cid"]),
				fReturnStringNull($frm["cd_uf"]),
				fReturnStringNull($frm["ds_info"]),
				fReturnStringNull($frm["tp_eve"]),
				fReturnStringNull($frm["fg_instrucao"],"N"),
				fReturnStringNull($frm["fg_publ"])
			);
			$out["arr"] = $arr;

			CONN::get()->Execute($query,$arr);
			$id = CONN::get()->Insert_ID();
		endif;

		//VERIFICA REGRA CHAMADA.
		$rgr = CONN::get()->Execute("SELECT * FROM RGR_CHAMADA WHERE ID_EVENTO = ?", Array( $id ) );

		//SE NAO EXISTE NO BANCO E TELA PREENCHIDA.
		if ( $rgr->EOF && ( is_numeric($frm["id_regra"]) || !empty($frm["tp_grupo"]) || is_numeric($frm["id_uniforme"]) ) ):
			CONN::get()->Execute("
			INSERT INTO RGR_CHAMADA (
				TP_GRUPO,
				ID_TAB_RGR_CHAMADA,
				ID_TAB_TP_UNIFORME,
				ID_EVENTO
			) VALUES ( ?, ?, ?, ? )",
				Array( fReturnStringNull($frm["tp_grupo"]),  fReturnNumberNull($frm["id_regra"]), fReturnNumberNull($frm["id_uniforme"]), $id ) );

		//SE EXISTE NO BANCO E TELA PREENCHIDA
		elseif ( !$rgr->EOF && ( is_numeric($frm["id_regra"]) || !empty($frm["tp_grupo"]) || is_numeric($frm["id_uniforme"]) ) ):
			CONN::get()->Execute("
			UPDATE RGR_CHAMADA SET
				TP_GRUPO = ?,
				ID_TAB_RGR_CHAMADA = ?,
				ID_TAB_TP_UNIFORME = ?
			WHERE ID_EVENTO = ?",
				Array( fReturnStringNull($frm["tp_grupo"]), fReturnNumberNull($frm["id_regra"]), fReturnNumberNull($frm["id_uniforme"]), $id ) );

		//SE EXISTE NO BANCO E TELA NAO PREENCHIDA
		elseif ( !$rgr->EOF && !( is_numeric($frm["id_regra"]) || !empty($frm["tp_grupo"]) || is_numeric($frm["id_uniforme"]) ) ):
			fDeleteRegraChamada($id);
		endif;
		$out["success"] = true;

	//EXCLUSAO DE EVENTO
	elseif ( $op == "DELETE" ):
		fDeleteRegraChamada($parameters["id"]);
		CONN::get()->Execute("DELETE FROM CAD_EVENTOS WHERE ID_EVENTO = ?", Array( $parameters["id"] ) );
		$out["success"] = true;

	endif;

	return $out;
}

function fDeleteRegraChamada($idEvento){
	CONN::get()->Execute("DELETE FROM RGR_CHAMADA WHERE ID_EVENTO = ?", Array( $idEvento ) );
}

function fGetClass($strTipoEvento){
	$eventClass = array(
		"CAMPO"		=> "event-important",	//VERMELHO
		"DISTRITO"	=> "event-info",	//AZUL
		"REGIAO"	=> "event-success",	//VERDE
		"DEFAULT"	=> "event-inverse",	//PRETO
		"COLEGIO"	=> "event-warning",	//AMARELO
		"SPECIAL"	=> "event-special"	//VINHO
	);
	if (array_key_exists($strTipoEvento,$eventClass)):
		return $eventClass[$strTipoEvento];
	else:
		return fGetClass("DEFAULT");
	endif;
}

function agendaConsulta( $parameters ) {
	session_start();
	$cadMembroID = $_SESSION['USER']['ID_CAD_MEMBRO'];

	$out = array();

	$ano = $parameters["ano"];
	if ( empty($ano) || is_null($ano) ):
		$out["years"] = array();
		$query = "SELECT DISTINCT NR_ANO
			  FROM CAD_ATIVOS ". ( is_null($cadMembroID) ? "" : "WHERE ID_CAD_MEMBRO = $cadMembroID" ) ." ORDER BY NR_ANO DESC";
		$result = CONN::get()->Execute($query);
		$ano = $result->fields["NR_ANO"];
		foreach ($result as $k => $line):
			$out["years"][] = array( "id" => $line["NR_ANO"], "ds" => $line["NR_ANO"] );
		endforeach;
	endif;
	if ( !empty($ano) ):
		$mesHoje = date("m");
		$str = "";
		$result = CONN::get()->Execute("
		 	SELECT *
		 	  FROM CAD_EVENTOS
		 	 WHERE YEAR(DTHORA_EVENTO_INI) = ?
		 	    OR YEAR(DTHORA_EVENTO_FIM) = ?
		      ORDER BY DTHORA_EVENTO_INI DESC
		", array($ano,$ano) );
		$mesAnt = "<div class=\"row\">";

		foreach ($result as $k => $line):
			$data = strtotime($line['DTHORA_EVENTO_INI']);
			$nomeMesAtu = utf8_encode(ucfirst(strftime("%B",$data)));
			if ($nomeMesAtu != $mesAnt):
				if ($mesAnt != ""):
					$str .= "</div>";
					$str .= "</div>";
					$str .= "</div>";
				endif;
				$mesAnt = $nomeMesAtu;

				$nrMesAtu = strftime("%m",$data);
				$class = "panel-default";
				if (strftime("%Y",$data) == date("Y")):
					if ($nrMesAtu == $mesHoje):
						$class = "panel-red";
					elseif ($nrMesAtu == ($mesHoje + 1)):
						$class = "panel-yellow";
					elseif ($nrMesAtu > $mesHoje):
						$class = "panel-info";
					endif;
				endif;
				$str .= "<div class=\"col-md-6 col-xs-12 col-sm-6 col-lg-4\">";
				$str .= "<div class=\"panel $class\">";
				$str .= "<div class=\"panel-heading\" style=\"cursor:pointer\"><h5 class=\"panel-title\" data-toggle=\"collapse\" data-target=\"#m$nrMesAtu\" href=\"#m$nrMesAtu\"><b>$nomeMesAtu</h5></b></div>";
				$str .= "<div id=\"m$nrMesAtu\" class=\"panel-body panel-collapse collapse\">";
			endif;
			$str .= "<div class=\"media row col-lg-12\">";
			$str .= "<div class=\"pull-left\"><i class=\"fa ". fGetClassTipoEvento($line['TIPO_EVENTO']) ." fa-2x\"></i></div>";
			$str .= "<div class=\"media-body\">";
			$str .= "<h4 class=\"media-heading\"><b>".fDtHoraEvento($line['DTHORA_EVENTO_INI'],$line['DTHORA_EVENTO_FIM'],"%d/%m")."</b></h4>";
			$str .= "<p>";
			$info = "";
			if (trim($line['INFO_ADIC']) != ""):
				$info .= trim($line['INFO_ADIC']);
			endif;
			if (trim($line['DESC_LOCAL']) != ""):
				$info .= " - ".trim($line['DESC_LOCAL']);
			endif;
			if ($info != ""):
				$str .= "$info<br/>";
			endif;
			$endereco = trim($line['DESC_LOGRADOURO']);
			if (trim($line['NUM_LOGRADOURO']) != ""):
				$endereco .= ", ".trim($line['NUM_LOGRADOURO']);
			endif;
			if (trim($line['DESC_COMPLEMENTO']) != ""):
				$endereco .= " - ".trim($line['DESC_COMPLEMENTO']);
			endif;
			if ($endereco != ""):
				$str .=  "$endereco<br/>";
			endif;
			$cidade = "";
			if (trim($line['DESC_BAIRRO']) != ""):
				$cidade .= trim($line['DESC_BAIRRO']);
			endif;
			if (trim($line['DESC_CIDADE']) != ""):
				if ($cidade != ""):
					$cidade .= " - ";
				endif;
				$cidade .= trim($line['DESC_CIDADE']);
			endif;
			if (trim($line['COD_UF']) != ""):
				if ($cidade != ""):
					$cidade .= " - ";
				endif;
				$cidade .= trim($line['COD_UF']);
			endif;
			if ($cidade != ""):
				$str .= "$cidade<br/>";
			endif;

			$str .= "</p>";
			$str .= "</div>";
			$str .= "</div>";
		endforeach;

		$str .= "</div>";
		$str .= "</div>";
		$str .= "</div>";
		$out["agenda"] = $str;
	endif;
	return $out;
}
?>
