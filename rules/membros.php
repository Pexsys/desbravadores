<?php
@require_once("../include/functions.php");
@require_once("../include/responsavel.php");
@require_once("../include/compras.php");
responseMethod();

/****************************
 * Methods defined for use. *
 ****************************/
function getQueryByFilter( $parameters ) {
	$where = "";
	$aWhere = array();
	if ( isset($parameters["filters"]) ):
		$keyAnt = "";
		foreach ($parameters["filters"] as $key => $v):
			$not = false;
			if ( isset($parameters["filters"][$key]["fg"]) ):
				$not = strtolower($parameters["filters"][$key]["fg"]) == "true";
			endif;
			$notStr = ( $not ? "NOT " : "" );
			if ( $key == "X" ):
				$where .= " AND p.tp_sexo ".$notStr."IN";
			elseif ( $key == "MA" ):
				$where .= " AND DATE_FORMAT(p.DT_NASC,'%m') ".$notStr."IN";
			elseif ( $key == "U" ):
				$where .= " AND a.id_unidade ".$notStr."IN";
			elseif ( $key == "C" ):
				$where .= " AND cap.TP_ITEM = 'CL' AND cap.ID_TAB_APREND ".$notStr."IN";
			elseif ( $key == "V" ):
				$where .= " AND p.TP_REGIME ".$notStr."IN";
			else:
				$where .= " AND";
			endif;

			$prim = true;
			$where .= " (";
			if ( is_array( $parameters["filters"][$key]["vl"] ) ):
				foreach ($parameters["filters"][$key]["vl"] as $value):
					if ( $key == "B" ):
						if ($value == "S"):
							$where .= (!$prim ? " OR " : "") ."p.dt_bat IS ". ( $value == "S" && !$not ? "NOT NULL" : "NULL");
						elseif ($value == "N"):
							$where .= (!$prim ? " OR " : "") ."p.dt_bat IS ". ( $value == "N" && !$not ? "NULL" : "NOT NULL");
						elseif (fStrStartWith($value,"A")):
							$where .= (!$prim ? " OR " : "") ."YEAR(p.dt_bat) ". ( !$not ? " < " : " >= ") . substr($value,1,4);
						else:
							$where .= (!$prim ? " OR " : "") ."YEAR(p.dt_bat) ". ( !$not ? " = " : " <> ") . $value;
						endif;
					elseif ( $key == "PC" ):
						if ( $value == "NC5" ):
							$where .= (!$prim ? " OR " : "") ."LENGTH(p.NM)<=5";
						elseif ( $value == "SEX" ):
							$where .= (!$prim ? " OR " : "") ."p.TP_SEXO NOT IN ('M','F')";
						elseif ( $value == "DTN" ):
							$where .= (!$prim ? " OR " : "") ."(p.DT_NASC IS NULL OR LENGTH(p.DT_NASC) = 0)";
						elseif ( $value == "DOC" ):
							$where .= (!$prim ? " OR " : "") ."(p.NR_DOC IS NULL OR LENGTH(p.NR_DOC) < 7 OR INSTR(TRIM(p.NR_DOC),' ') < 3)";
						elseif ( $value == "CPF" ):
							$where .= (!$prim ? " OR " : "") ."((p.NR_CPF IS NULL OR LENGTH(p.NR_CPF)=0) AND a.NR_CPF_RESP IS NULL)";
						elseif ( $value == "LOG" ):
							$where .= (!$prim ? " OR " : "") ."(p.LOGRADOURO IS NULL OR LENGTH(p.LOGRADOURO) = 0)";
						elseif ( $value == "NLG" ):
							$where .= (!$prim ? " OR " : "") ."(p.NR_LOGR IS NULL OR LENGTH(p.NR_LOGR) = 0)";
						elseif ( $value == "BAI" ):
							$where .= (!$prim ? " OR " : "") ."(p.BAIRRO IS NULL OR LENGTH(p.BAIRRO) = 0)";
						elseif ( $value == "CID" ):
							$where .= (!$prim ? " OR " : "") ."(p.CIDADE IS NULL OR LENGTH(p.CIDADE) = 0)";
						elseif ( $value == "EST" ):
							$where .= (!$prim ? " OR " : "") ."(p.UF IS NULL OR LENGTH(p.UF) = 0)";
						elseif ( $value == "CEP" ):
							$where .= (!$prim ? " OR " : "") ."(p.CEP IS NULL OR LENGTH(p.CEP) = 0)";
						elseif ( $value == "TEL" ):
							$where .= (!$prim ? " OR " : "") ."((p.FONE_RES IS NULL AND p.FONE_CEL IS NULL) OR LENGTH(CONCAT(p.FONE_RES,p.FONE_CEL)) = 0)";
						elseif ( $value == "UNI" ):
							$where .= (!$prim ? " OR " : "") ."(a.ID_UNIDADE IS NULL OR a.ID_UNIDADE = 0)";
						elseif ( $value == "CAR" ):
							$where .= (!$prim ? " OR " : "") ."(a.CD_CARGO IS NULL OR LENGTH(a.CD_CARGO)<=1)";
						elseif ( $value == "RSP" ):
							$where .= (!$prim ? " OR " : "") ."(p.IDADE_ANO < 18 AND (p.ID_PESSOA_RESP IS NULL OR (p.NR_DOC_RESP IS NULL OR LENGTH(p.NR_DOC_RESP) < 7 OR INSTR(TRIM(p.NR_DOC_RESP),' ') < 3) OR (p.NR_CPF_RESP IS NULL OR LENGTH(p.NR_CPF_RESP)=0)))";
						endif;
					elseif ( $key == "G" ):
						if ( $value == "3" ):
							$where .= (!$prim ? " OR " : "") ."a.CD_FANFARRA IS ".( !$not ? "NOT NULL" : "NULL");
						elseif ( $value == "4" ):
						    $where .= (!$prim ? " OR " : "") ."a.CD_CARGO ". ( empty($value) ? "IS ".$notStr."NULL" : $notStr."LIKE '2-04%'");
						elseif ( $value == "5" ):
						    $where .= (!$prim ? " OR " : "") ."a.CD_CARGO ". ( empty($value) ? "IS ".$notStr."NULL" : $notStr."LIKE '2-07%'");
						elseif ( $value == "6" ):
						    $where .= (!$prim ? " OR " : "") ."a.CD_CARGO ". ( empty($value) ? "IS ".$notStr."NULL" : $notStr."LIKE '1-01%'");
						else:
							$where .= (!$prim ? " OR " : "") ."a.CD_CARGO ". ( empty($value) ? "IS ".$notStr."NULL" : $notStr."LIKE '$value%'");
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
		endforeach;
	endif;
	if ( isset($parameters["filtro"]) ):
		if ( $parameters["filtro"] == "A" ):
			$where .= " AND a.ID_CAD_PESSOA IS NOT NULL";
		endif;
	endif;

//echo $where;
//exit;

	return CONN::get()->Execute("
	  SELECT DISTINCT
			m.ID,
			m.ID_MEMBRO,
			m.ID_CAD_PESSOA,
			p.NM,
			a.DS_UNIDADE,
			a.DS_CARGO,
			a.DT_NASC,
			a.IDADE_HOJE,
			a.IDADE_ANO
		FROM CAD_MEMBRO m
		INNER JOIN CON_PESSOA p ON (p.ID_CAD_PESSOA = m.ID_CAD_PESSOA)
		LEFT JOIN CON_ATIVOS a ON (a.ID_CAD_PESSOA = p.ID_CAD_PESSOA)
		LEFT JOIN CON_APR_PESSOA cap ON (cap.ID_CAD_PESSOA = p.ID_CAD_PESSOA AND cap.DT_CONCLUSAO IS NULL)
		WHERE 1=1 $where ORDER BY p.NM"
	,$aWhere);
}

function getMembros( $parameters ) {
	$arr = array();

	$qtdZeros = zeroSizeID();
	$result = getQueryByFilter( $parameters );
	foreach ($result as $k => $f):
		$arr[] = array(
			"id" => $f['ID'],
			"ic" => fStrZero($f['ID_MEMBRO'], $qtdZeros),
			"nm" => $f['NM'],
			"uni" => $f['DS_UNIDADE'],
			"cgo" => $f['DS_CARGO']
		);
	endforeach;

	return array( "result" => true, "membros" => $arr );
}

function updateMember( $parameters ) {
	$arr = array();
	$arr["result"] = false;

	$id = $parameters["id"];
	$pessoaID = null;

	$rc = CONN::get()->Execute("SELECT * FROM CAD_MEMBRO WHERE ID = ?", array( $id ) );
	if (!$rc->EOF):
		$pessoaID = $rc->fields["ID_CAD_PESSOA"];
	endif;

	$vl = $parameters["value"];
	$tf = explode("-",$parameters["field"]);

	//tratamento de field unico
	if ( count($tf) == 1 ):
		$field = mb_strtoupper($tf[0]);

		//tratamento para ativo/inativo
		if ($field == "FG_ATIVO"):

			CONN::get()->Execute( "
				UPDATE CAD_MEMBRO SET QT_UNIFORMES = NULL
				WHERE ID_CAD_MEMBRO = ? AND NR_ANO = YEAR(NOW())"
			, array( $id ) );

			if ( $vl == "N" ):
				CONN::get()->Execute( "
					DELETE FROM CAD_ATIVOS
					WHERE ID_CAD_MEMBRO = ? AND NR_ANO = YEAR(NOW())"
				, array( $id ) );

				PROFILE::deleteAllByPessoaID($pessoaID);

			else:
				$unid	= null;
				$cargo	= null;
				$cargo2 = null;
				$tpCami = null;
				$tpAgas = null;
				$instr	= null;
				$reun	= "S";

				//Ultimo ano ativo.
				$result = CONN::get()->Execute( "
					SELECT  a.ID_UNIDADE,
							a.CD_CARGO,
							a.CD_CARGO2,
							a.TP_CAMISETA,
							a.TP_AGASALHO,
							a.CD_FANFARRA,
							a.FG_REU_SEM,
							a.NR_ANO,
							YEAR(NOW())-1 AS ANO_ANT
					FROM CAD_ATIVOS a
						WHERE a.ID_CAD_MEMBRO = ?
						ORDER BY NR_ANO DESC
				", array( $id ) );

				if (!$result->EOF):
					if ($result->fields['ANO_ANT'] == $result->fields['NR_ANO']):
						$unid	= $result->fields['ID_UNIDADE'];
						$cargo	= $result->fields['CD_CARGO'];
						$cargo2 = $result->fields['CD_CARGO2'];
						$tpCami = $result->fields['TP_CAMISETA'];
						$tpAgas = $result->fields['TP_AGASALHO'];
						$instr	= $result->fields['CD_FANFARRA'];
						$reun	= $result->fields['FG_REU_SEM'];
					endif;
				endif;

				CONN::get()->Execute( "
					INSERT INTO CAD_ATIVOS (
						NR_ANO,
						DT_ATIVACAO,
						ID_CAD_MEMBRO,
						ID_UNIDADE,
						CD_CARGO,
						CD_CARGO2,
						TP_CAMISETA,
						TP_AGASALHO,
						CD_FANFARRA,
						FG_REU_SEM
					) VALUES (
						YEAR(NOW()),
						CURDATE(),
						?,
						?,
						?,
						?,
						?,
						?,
						?,
						?
					)", array( $id, $unid, $cargo, $cargo2, $tpCami, $tpAgas, $instr, $reun ) );

					PROFILE::apply(
						$pessoaID,
						array( "cargo" => $cargo, "cargo2" => $cargo2 ) 
					);

				return getMember( array( "id" => $id ) );
			endif;
		endif;

	else:
		$table = mb_strtoupper($tf[0]);
		$field = mb_strtoupper($tf[1]);

		$updateID = $id;

		if ($table == "CAD_RESP_LEGAL"):
			if ($field == "DS_TP"):
				$updateID = $pessoaID;
			else:
				$table = "CAD_PESSOA";
				$pessoaID = $parameters["rid"];
			endif;
		endif;

		if ($table == "CAD_PESSOA"):
			if ($field == "DT_NASC"):
				$vl = fStrToDate($vl,"Y-m-d");
				$arr["membro"] = array(
					"nr_idade"	=> fIdadeAtual($vl)
				);
			elseif ($field == "DT_BAT"):
				$vl = fStrToDate($vl,"Y-m-d");
			elseif ($field == "NR_CPF"):
				$vl = fClearBN($vl);
			endif;
			$updateID = $pessoaID;

		endif;

		$aUpdate = array( fReturnStringNull( $vl ), $updateID );

		$str = "UPDATE $table SET $field = ? WHERE ";
		if ($table == "CAD_RESP_LEGAL" && $field == "DS_TP"):
			$str .= " ID_CAD_PESSOA = ? AND ID_PESSOA_RESP = ?";
			$aUpdate[] = $parameters["rid"];
		elseif ($table == "CAD_ATIVOS"):
			$str .= " ID_CAD_MEMBRO = ? AND NR_ANO = YEAR(NOW())";
		else:
			$str .= " ID = ?";
		endif;

		//$arr["query"] = array( $str, $aUpdate);
		CONN::get()->Execute( $str, $aUpdate );

		//REGRA PARA CALCULO DA ESTRELA DE TEMPO DE SERVICO
		if ( $field == "ANO_DIR" || $field == "ESTR_DEVOL" || $field == "QT_UNIFORMES"):

		    //EXCLUI ESTRELAS DA LISTA DE COMPRAS
		    CONN::get()->Execute( "DELETE FROM CAD_COMPRAS WHERE ID_CAD_MEMBRO = ? AND ID_TAB_MATERIAIS IN (SELECT ID FROM TAB_MATERIAIS WHERE TP = 'ESTRELA')", array( $id ) );

		    //CALCULAR E INCLUIR ESTRELAS
		    $rs = CONN::get()->Execute("
        		SELECT (YEAR(NOW())-ANO_DIR+1) AS CALC_ATUAL, ESTR_DEVOL, QT_UNIFORMES
        		  FROM CON_ATIVOS
        		 WHERE ANO_DIR IS NOT NULL
        		   AND QT_UNIFORMES > 0
        		   AND ID_CAD_MEMBRO = ?
            ", array($id) );
            if (!$rs->EOF):
                $calcAtual = $rs->fields["CALC_ATUAL"];
                if ( $calcAtual > max( 1, $rs->fields["ESTR_DEVOL"]) ):
                    $compras = new COMPRAS();

                    $code = "03-01-".fStrZero($calcAtual, 2);
                    $qtItens = $rs->fields["QT_UNIFORMES"];
                    for ($qtd=1;$qtd<=$qtItens;$qtd++):
						if ($qtItens>1):
							$compl = "$qtd/$qtItens";
						endif;
						$compras->insertItemCompra( $code, $id, "M", $compl );
					endfor;
                endif;
			endif;

		elseif ( $field == "CD_CARGO" || $field == "CD_CARGO2"):
			if (!$rc->EOF):
				PROFILE::apply(
					$pessoaID,
					array( "cargo" => $rc->fields['CD_CARGO'], "cargo2" => $rc->fields['CD_CARGO2'] ) 
				);
			endif;

		endif;

		$arr["result"] = true;
	endif;

	return $arr;
}

function verificaResp( $parameters ) {
	$arr = array( "result" => true );

	$fields = null;
	$id = NULL;

	$membroID = $parameters["id"];
	$cpf = fClearBN($parameters["cpf"]);

	$rs = CONN::get()->Execute("SELECT * FROM CAD_MEMBRO WHERE ID = ?", array( $membroID ) );
	if (!$rs->EOF && !empty($cpf)):
		$fields = verificaRespByCPF($cpf);

		if (is_null($fields)):
			CONN::get()->Execute("INSERT INTO CAD_PESSOA(NR_CPF) VALUES (?)", array( $cpf ) );
			$fields["ID_CAD_PESSOA"] = CONN::get()->Insert_ID();
			$fields['NR_CPF'] = fCPF($cpf);
		endif;
		CONN::get()->Execute("
			DELETE FROM CAD_RESP_LEGAL
			WHERE ID_CAD_PESSOA = ?
		", array( $rs->fields["ID_CAD_PESSOA"]) );
		CONN::get()->Execute("
			INSERT INTO CAD_RESP_LEGAL( ID_CAD_PESSOA, ID_PESSOA_RESP )
			VALUES (?,?)
		", array( $rs->fields["ID_CAD_PESSOA"], $fields["ID_CAD_PESSOA"] ) );
	endif;

	return fMergeResp( $arr, $fields );
}

function fMergeResp( $arr, $fields ){
	if (!is_null($fields)):
		$arr["cad_resp_legal-id_cad_pessoa"] = $fields["ID_CAD_PESSOA"];
		$arr["cad_resp_legal-ds_tp"]	= trim($fields['DS_TP']);
		$arr["cad_resp_legal-nm"]		= trim($fields['NM']);
		$arr["cad_resp_legal-tp_sexo"]	= $fields['TP_SEXO'];
		$arr["cad_resp_legal-nr_doc"]	= $fields['NR_DOC'];
		$arr["cad_resp_legal-nr_cpf"]	= fCPF($fields['NR_CPF']);
		$arr["cad_resp_legal-fone_cel"]	= $fields['FONE_CEL'];
		$arr["cad_resp_legal-email"]	= trim($fields['EMAIL']);
	endif;
	return $arr;
}

function getMembroID(){
	$result = CONN::get()->Execute("
		SELECT MAX(ID_MEMBRO) + 1 AS ID_MEMBRO
		FROM CAD_MEMBRO
		WHERE ID_CLUBE = ?
	", array( PATTERNS::getBars()->getClubeID() ) );
	return $result->fields["ID_MEMBRO"];
}

function insertMember( $parameters ) {
	$arr = array();
	$arr["result"] = false;

	if ( isset($parameters["id"]) && $parameters["id"] == "Novo" ):
		if (isset($parameters["nm"]) && isset($parameters["dt"]) && isset($parameters["sx"])):
			$dc = $parameters["dc"];

			//PROCURA SE EXISTE A PESSOA
			$result = CONN::get()->Execute("
				SELECT ID
				FROM CAD_PESSOA
				WHERE NR_DOC = ?
				", array( $dc ) );
			if ($result->EOF):
				CONN::get()->Execute("
					INSERT INTO CAD_PESSOA(
						NM,
						DT_NASC,
						TP_SEXO,
						NR_DOC
					) VALUES (
						?,
						?,
						?,
						?
					)", array( $parameters["nm"], fStrToDate($parameters["dt"],"Y-m-d"), $parameters["sx"], $dc ) );
				$pessoaID = CONN::get()->Insert_ID();
			else:
				$pessoaID = $result->fields['ID'];
			endif;

			//PROCURA SE EXISTE COMO MEMBRO
			$result = CONN::get()->Execute("
				SELECT ID
				FROM CAD_MEMBRO
				WHERE ID_CAD_PESSOA = ?
					AND ID_CLUBE = ?
				", array( $pessoaID, PATTERNS::getBars()->getClubeID() ) );
			if ($result->EOF):
				$membroID = getMembroID();

				CONN::get()->Execute("
					INSERT INTO CAD_MEMBRO(
						ID_CLUBE,
						ID_MEMBRO,
						ID_CAD_PESSOA
					) VALUES (
						?,
						?,
						?
					)", array(
						PATTERNS::getBars()->getClubeID(),
						$membroID,
						$pessoaID )
					);
				$cadMembroID = CONN::get()->Insert_ID();
			else:
				$cadMembroID = $result->fields['ID'];
			endif;

			return getMember( array( "id" => $cadMembroID ) );
		endif;
	endif;
	return $arr;
}

function getMember( $parameters ) {
	$arr = array();
	$arr["result"] = false;

	$id = $parameters["id"];
	$cargo2 = false;

	$qtdZeros = zeroSizeID();
	$result = CONN::get()->Execute("
		SELECT  p.NM,
				p.EMAIL,
				p.NM_ESCOLA,
				p.DS_RELIG,
				p.DT_BAT,
				p.TP_SEXO,
				p.DT_NASC,
				p.NR_DOC,
				p.NR_CPF,
				p.LOGRADOURO,
				p.NR_LOGR,
				p.COMPLEMENTO,
				p.BAIRRO,
				p.CIDADE,
				p.UF,
				p.CEP,
				p.FONE_RES,
				p.FONE_CEL,
				p.TP_REGIME,
				p.ID_PESSOA_RESP,
				m.ID_CAD_PESSOA,
				m.ID_MEMBRO,
				m.ID_CLUBE,
				m.ANO_DIR,
				m.ESTR_DEVOL,
				m.QT_UNIFORMES,
				a.ID_CAD_MEMBRO AS ID_ATIVO,
				a.ID_UNIDADE,
				a.CD_CARGO,
				a.CD_CARGO2,
				a.TP_CAMISETA,
				a.TP_AGASALHO,
				a.CD_FANFARRA,
				a.FG_REU_SEM
		  FROM CAD_MEMBRO m
	INNER JOIN CON_PESSOA p ON (p.ID_CAD_PESSOA = m.ID_CAD_PESSOA)
	 LEFT JOIN CAD_ATIVOS a ON (a.ID_CAD_MEMBRO = m.ID AND a.NR_ANO = YEAR(NOW()) )
		 WHERE m.ID = ?", Array( $id ) );
	if (!$result->EOF):
		$arr["result"] = true;

		$idadeAtual = fIdadeAtual($result->fields['DT_NASC']);

		$barCODE = PATTERNS::getBars()->encode(array(
			"ni" => $result->fields['ID_MEMBRO']
		));

		$arr["membro"] = array(
			"cad_membro-id"			    => $id,
			"cad_membro-id_membro"	    => fStrZero($result->fields['ID_MEMBRO'], $qtdZeros),
			"cad_pessoa-id"			    => $result->fields['ID_CAD_PESSOA'],
			"cad_pessoa-bc"			    => $barCODE,
			"cad_pessoa-nm"			    => trim($result->fields['NM']),
			"cad_pessoa-email"		    => trim($result->fields['EMAIL']),
			"cad_pessoa-nm_escola"		=> trim($result->fields['NM_ESCOLA']),
			"cad_pessoa-ds_relig"		=> trim($result->fields['DS_RELIG']),
			"cad_pessoa-dt_bat"		    => is_null($result->fields['DT_BAT']) ? "" : date( 'd/m/Y', strtotime($result->fields['DT_BAT']) ),
			"cad_pessoa-tp_sexo"		=> $result->fields['TP_SEXO'],
			"cad_pessoa-dt_nasc"		=> is_null($result->fields['DT_NASC']) ? "" : date( 'd/m/Y', strtotime($result->fields['DT_NASC']) ),
			"cad_pessoa-nr_doc"		    => trim($result->fields['NR_DOC']),
			"cad_pessoa-nr_cpf"		    => fCPF($result->fields['NR_CPF']),
			"cad_pessoa-logradouro"		=> trim($result->fields['LOGRADOURO']),
			"cad_pessoa-nr_logr"		=> trim($result->fields['NR_LOGR']),
			"cad_pessoa-complemento"	=> trim($result->fields['COMPLEMENTO']),
			"cad_pessoa-bairro"		    => trim($result->fields['BAIRRO']),
			"cad_pessoa-cidade"		    => trim($result->fields['CIDADE']),
			"cad_pessoa-uf"			    => $result->fields['UF'],
			"cad_pessoa-cep"		    => $result->fields['CEP'],
			"cad_pessoa-fone_res"		=> $result->fields['FONE_RES'],
			"cad_pessoa-fone_cel"		=> $result->fields['FONE_CEL'],
			"cad_pessoa-tp_regime"		=> $result->fields['TP_REGIME'],
			"cad_membro-ano_dir"		=> $result->fields['ANO_DIR'],
			"cad_membro-estr_devol"		=> $result->fields['ESTR_DEVOL'],
			"cad_membro-qt_uniformes"	=> $result->fields['QT_UNIFORMES'],
			"cad_ativos-id_unidade"		=> $result->fields['ID_UNIDADE'],
			"cad_ativos-cd_cargo"		=> $result->fields['CD_CARGO'],
			"cad_ativos-cd_cargo2"		=> $result->fields['CD_CARGO2'],
			"cad_ativos-tp_camiseta"	=> $result->fields['TP_CAMISETA'],
			"cad_ativos-tp_agasalho"	=> $result->fields['TP_AGASALHO'],
			"cad_ativos-cd_fanfarra"	=> $result->fields['CD_FANFARRA'],
			"cad_ativos-fg_reu_sem"		=> $result->fields['FG_REU_SEM'],
			"nr_idade"			        => $idadeAtual,
			"fg_ativo"                  => isset($result->fields['ID_ATIVO']) ? "S" : "N"
		);

		if ($idadeAtual < 18):
			$arr["membro"] = fMergeResp( $arr["membro"], verificaRespByID( $result->fields['ID_PESSOA_RESP'], $result->fields['ID_CAD_PESSOA'] ) );
		else:
			CONN::get()->Execute("DELETE FROM CAD_RESP_LEGAL WHERE ID_CAD_PESSOA = ?", array( $result->fields['ID_CAD_PESSOA'] ) );
		endif;

		$cargo2 = strpos($result->fields['CD_CARGO'], "2-07") === 0;
	endif;

	$arr["unidades"] = getUnidades( $parameters );
	$arr["cargos"] = getCargos( $parameters );
	if ( $cargo2 ) {
		$param2 = $parameters;
		$param2["tp"] = true;
		$arr["cargos2"]	=  getCargos( $param2 );
	}
	$arr["instrumentos"] = getInstrumentos( $parameters );
	$arr["anos"] = getAnosDir( $parameters );

	return $arr;
}

function getUnidades( $parameters ) {
	$arr = array();

	$result = CONN::get()->Execute("
		SELECT cp.TP_SEXO, cp.DT_NASC, cp.IDADE_ANO, cp.ID_PESSOA_RESP
		FROM CAD_MEMBRO cm
		INNER JOIN CON_PESSOA cp ON (cp.ID_CAD_PESSOA = cm.ID_CAD_PESSOA)
		 WHERE cm.ID = ?", Array( $parameters["id"] ) );
	if (!$result->EOF):
		$fd = "";
		$fIdade = "";
		$dtNasc = $result->fields['DT_NASC'];
		if (isset($dtNasc)):
			$anos = datediff("yyyy", $dtNasc, date("Y-6-30") );
			$fIdade = " AND IDADE <= $anos";
			if ( $anos > 15 ):
				$fd = ",'A'";
			elseif ( $anos < 10 ):
				if ( $result->fields['IDADE_ANO'] == 10 && responsavelAtivo($result->fields['ID_PESSOA_RESP']) ):
					$fIdade = " AND IDADE = 10";
				else:
					$fIdade = " AND IDADE > 15";
					$fd = ",'A'";
				endif;
			endif;
		endif;

		$result = CONN::get()->Execute("
			SELECT ID, CONCAT(DS,' (',IDADE,')') AS DS
			  FROM TAB_UNIDADE
			 WHERE TP IN ('".$result->fields['TP_SEXO']."'$fd)
			   AND FG_ATIVA = ?
			   $fIdade
		  ORDER BY IDADE DESC", array('S') );
		foreach ($result as $k => $l):
			$arr[] = array(
				"id"	=> $l['ID'],
				"ds"	=> $l['DS']
			);
		endforeach;
	endif;
	return $arr;
}

function getCargos( $parameters ) {
	$arr = array();

	$result = CONN::get()->Execute("
		SELECT ID_UNIDADE, TP_SEXO
		  FROM CON_ATIVOS
		 WHERE ID_CAD_MEMBRO = ?
		   AND NR_ANO = YEAR(NOW())
	", Array( $parameters["id"] ) );
	if (!$result->EOF):
		$lsWhere = "";
		$id = $result->fields['ID_UNIDADE'];
		if (isset($id)):
			if ( $id == 1 || isset($parameters["tp"]) ):
				$lsWhere = "WHERE CD LIKE '2-%' AND CD NOT LIKE '2-07%'";
			else:
				$lsWhere = "WHERE CD LIKE '1-%' OR CD LIKE '2-07%'";
			endif;
		endif;
		$result = CONN::get()->Execute("
			SELECT CD AS ID, ".($result->fields['TP_SEXO'] == "F" ? "DSF" : "DSM"). " AS DS
			  FROM TAB_CARGO
			  $lsWhere
		  ORDER BY 2
		");
		while (!$result->EOF):
			$arr[] = array(
				"id"	=> $result->fields['ID'],
				"ds"	=> $result->fields['DS']
			);
			$result->MoveNext();
		endwhile;
	endif;
	return $arr;
}

function getInstrumentos( $parameters ) {
	$arr = array();

	$result = CONN::get()->Execute("
		SELECT DISTINCT i.CD AS ID, CONCAT( i.CD, '-', t.DS ) AS DS
		FROM CAD_INSTRUMENTO i
		INNER JOIN TAB_TP_INSTRUMENTO t ON (t.ID = i.ID_TP)
		WHERE i.CD NOT IN (SELECT DISTINCT CD_FANFARRA FROM CAD_ATIVOS WHERE NR_ANO = YEAR(NOW()) AND ID_CAD_MEMBRO <> ? AND CD_FANFARRA IS NOT NULL)
		ORDER BY i.CD
	", Array( $parameters["id"] ) );
	while (!$result->EOF):
		$arr[] = array(
			"id"	=> $result->fields['ID'],
			"ds"	=> $result->fields['DS']
		);
		$result->MoveNext();
	endwhile;
	return $arr;
}

function getAnosDir( $parameters ) {
	$arr = array();

	$result = CONN::get()->Execute("
		SELECT YEAR(cp.DT_NASC) AS ANO, MONTH(cp.DT_NASC) AS MES, YEAR(NOW()) AS HOJE
		  FROM CAD_MEMBRO cm
	INNER JOIN CAD_PESSOA cp ON (cp.ID = cm.ID_CAD_PESSOA)
		 WHERE cm.ID = ?", Array( $parameters["id"] ) );
	if (!$result->EOF):
		$ano = $result->fields['ANO'];
		if ($result->fields['MES'] > 6):
			$ano++;
		endif;
		if (isset($ano)):
			for ($idx = $ano+16; $idx<=$result->fields['HOJE']; $idx++):
				$arr[] = array(
					"id"	=> $idx,
					"ds"	=> $idx
				);
			endfor;
		endif;
	endif;
	return $arr;
}

function getAniversariantes( $parameters ){
	$arr = array();

	$result = getQueryByFilter( $parameters );
	foreach ($result as $k => $f):
		$dtNascimento = strtotime($f['DT_NASC']);

		$aniversario = mktime(0, 0, 0, strftime("%m",$dtNascimento), strftime("%d",$dtNascimento), date("Y") );
		if ( $f['IDADE_ANO'] == $f['IDADE_HOJE'] && strftime("%Y%m",$aniversario) < date("Ym") ):
			$aniversario = mktime(0, 0, 0, strftime("%m",$dtNascimento), strftime("%d",$dtNascimento), date("Y")+1 );
		endif;

		$arr[] = array(
			"nm" => $f['NM'],
			"uni" => $f['DS_UNIDADE'],
			"dm" => $aniversario,
			"ih" => $f['IDADE_HOJE']
		);
	endforeach;

	return array( "result" => true, "membros" => $arr );
}
?>
