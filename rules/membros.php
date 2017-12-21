<?php
@require_once("../include/functions.php");
@require_once("../include/responsavel.php");
@require_once("../include/profile.php");
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
				$where .= " AND cap.TP_ITEM = ? AND cap.ID_TAB_APREND ".$notStr."IN";
				$aWhere[] = "CL";
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
							$where .= (!$prim ? " OR " : "") ."((p.NR_CPF IS NULL OR LENGTH(p.NR_CPF)=0) AND a.CPF_RESP IS NULL)";
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
			$where .= " AND a.id IS NOT NULL";
		endif;
	endif;

//echo $where;
//exit;

	return $GLOBALS['conn']->Execute(
		"SELECT DISTINCT
			p.id,
			p.NM,
			a.DS_UNIDADE,
			a.DS_CARGO,
			a.DT_NASC,
			a.IDADE_HOJE,
			a.IDADE_ANO
		FROM CAD_PESSOA p
		LEFT JOIN CON_ATIVOS a ON (a.id = p.id)
		LEFT JOIN CON_APR_PESSOA cap ON (cap.ID_CAD_PESSOA = a.ID AND cap.DT_CONCLUSAO IS NULL)
		WHERE 1=1 $where ORDER BY p.NM"
	,$aWhere);
}

function getMembros( $parameters ) {
	$arr = array();
	fConnDB();
	
	$qtdZeros = zeroSizeID();
	$result = getQueryByFilter( $parameters );
	while (!$result->EOF):
		$dtNascimento = strtotime($result->fields['DT_NASC']);
	
		$aniversario = mktime(0, 0, 0, strftime("%m",$dtNascimento), strftime("%d",$dtNascimento), date("Y") );
		if ( $result->fields['IDADE_ANO'] == $result->fields['IDADE_HOJE'] && strftime("%Y%m",$aniversario) < date("Ym") ):
			$aniversario = mktime(0, 0, 0, strftime("%m",$dtNascimento), strftime("%d",$dtNascimento), date("Y")+1 );
		endif;
		
		$arr[] = array( 
			"id" => fStrZero($result->fields['id'], $qtdZeros),
			"nm" => ($result->fields['NM']),
			"uni" => ($result->fields['DS_UNIDADE']),
			"cgo" => ($result->fields['DS_CARGO']),
			"dm" => $aniversario,
			"ih" => $result->fields['IDADE_HOJE']
		);
		$result->MoveNext();
	endwhile;

	return array( "result" => true, "membros" => $arr );
}

function updateMember( $parameters ) {
	$arr = array();
	$arr["result"] = false;

	$PROFILE = new PROFILE();
	
	fConnDB();

	$id = $parameters["id"];
	$vl = $parameters["val"];
	$tf = explode("-",$parameters["field"]);
	
	//tratamento de field unico
	if ( count($tf) == 1 ):
		$field = mb_strtoupper($tf[0]);
		
		//tratamento para ativo/inativo
		if ($field == "FG_ATIVO"):

			$GLOBALS['conn']->Execute( "
				UPDATE CAD_PESSOA SET QT_UNIFORMES = NULL
				WHERE ID = ?"
			, array( $id ) );
		
			if ( $vl == "N" ):
				$GLOBALS['conn']->Execute( "
					DELETE FROM CAD_ATIVOS 
					WHERE ID = ? AND NR_ANO = YEAR(NOW())"
				, array( $id ) );

				$PROFILE->deleteAllByPessoaID($id);
		
			else:
				$unid	= null;
				$cargo	= null;
				$cargo2 = null;
				$tpCami = null;
				$tpAgas = null;
				$instr	= null;
				$reun	= "S";

				//Ultimo ano ativo.
				$result = $GLOBALS['conn']->Execute( "
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
					WHERE a.ID = ?
					ORDER BY NR_ANO DESC"
				, array( $id ) );
				
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
				
				$GLOBALS['conn']->Execute( "
					INSERT INTO CAD_ATIVOS (
						NR_ANO,
						DT_ATIVACAO,
						ID,
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

					$PROFILE->rulesCargos( $id, $cargo, $cargo2 );
					
				return getMember( array( "id" => $id ) );
			endif;
		endif;

	else:
		$table = mb_strtoupper($tf[0]);
		$field = mb_strtoupper($tf[1]);

		if ($table == "CAD_PESSOA" && $field == "DT_NASC"):
			$vl = fStrToDate($vl,"Y-m-d");
			$arr["membro"] = array( 
				"nr_idade"	=> fIdadeAtual($vl)
			);
		elseif ($table == "CAD_PESSOA" && $field == "DT_BAT"):
			$vl = fStrToDate($vl,"Y-m-d");
		endif;
		
		$str = "UPDATE $table SET $field = ? WHERE ID = ?";
		if ($table == "CAD_ATIVOS"):
			$str .= " AND NR_ANO = YEAR(NOW())";
		endif;
		
		//$arr["query"] = array( $str, $vl, $id );
		$GLOBALS['conn']->Execute( $str, array( fReturnStringNull( $vl ), $id ) );
		
		//REGRA PARA CALCULO DA ESTRELA DE TEMPO DE SERVICO
		if ( $field == "ANO_DIR" || $field == "ESTR_DEVOL" || $field == "QT_UNIFORMES"):
		    
		    //EXCLUI ESTRELAS DA LISTA DE COMPRAS
		    $GLOBALS['conn']->Execute( "DELETE FROM CAD_COMPRAS_PESSOA WHERE ID_CAD_PESSOA = ? AND ID_TAB_MATERIAIS IN (SELECT ID FROM TAB_MATERIAIS WHERE TP = 'ESTRELA')", array( $id ) );
		    
		    //CALCULAR E INCLUIR ESTRELAS
		    $rs = $GLOBALS['conn']->Execute("
        		SELECT (YEAR(NOW())-ANO_DIR) AS CALC_ATUAL, ESTR_DEVOL, QT_UNIFORMES
        		  FROM CON_ATIVOS 
        		 WHERE ANO_DIR IS NOT NULL
        		   AND QT_UNIFORMES > 0
        		   AND ID = ?
            ", array($id) );
            if (!$rs->EOF):
                $calcAtual = $rs->fields["CALC_ATUAL"];
                if ( $calcAtual > max( 1, $rs->fields["ESTR_DEVOL"]) ):
                    $compras = new COMPRAS();
                    
                    $code = "03-01-".fStrZero($calcAtual+1, 2);
                    $qtItens = $rs->fields["QT_UNIFORMES"];
                    for ($qtd=1;$qtd<=$qtItens;$qtd++):
						if ($qtItens>1):
							$compl = "$qtd/$qtItens";
						endif;
						$compras->insertItemCompra( $code, $id, "A", $compl );
					endfor;
                endif;
			endif;
			
		elseif ( $field == "CD_CARGO" || $field == "CD_CARGO2"):
			$rc = $GLOBALS['conn']->Execute( "
					SELECT  a.CD_CARGO,
							a.CD_CARGO2
				FROM CON_ATIVOS a
					WHERE a.ID = ?"
			, array( $id ) );
			if (!$rc->EOF):
				$PROFILE->rulesCargos( $id, $rc->fields['CD_CARGO'], $rc->fields['CD_CARGO2'] );
			endif;
		    
		endif;
		
		$arr["result"] = true;
	endif;
	
	return $arr;
}

function verificaResp( $parameters ) {
	$arr = array( "result" => true );
	
	$membroID = $parameters["id"];
	$cpf = $parameters["cpf"];
		
	fConnDB();
	
	$fields = null;
	$id = NULL;

	if (!empty($cpf)):
		$fields = verificaRespByCPF($cpf);

		if (is_null($fields)):
			$GLOBALS['conn']->Execute("INSERT INTO CAD_RESP(CPF_RESP) VALUES (?)", array( $cpf ) );
			$id = $GLOBALS['conn']->Insert_ID();
			$fields['CPF_RESP'] = $cpf;
			$fields["ID"] = $id;
		else:
			$id = $fields["ID"];
		endif;
		
		//ATUALIZA RESPONSAVEL
		$GLOBALS['conn']->Execute("UPDATE CAD_PESSOA SET ID_RESP = ? WHERE ID = ?", array( $id, $membroID ) );
	endif;
		
	return fMergeResp( $arr, $fields );
}

function fMergeResp( $arr, $fields ){
	if (!is_null($fields)):
		$arr["cad_resp-id"] 			= $fields["ID"];
		$arr["cad_resp-ds_resp"]		= (trim($fields['DS_RESP']));
		$arr["cad_resp-nm_resp"]		= (trim($fields['NM_RESP']));
		$arr["cad_resp-tp_sexo_resp"]	= $fields['TP_SEXO_RESP'];
		$arr["cad_resp-doc_resp"]		= $fields['DOC_RESP'];
		$arr["cad_resp-cpf_resp"]		= $fields['CPF_RESP'];
		$arr["cad_resp-tel_resp"]		= $fields['TEL_RESP'];
		$arr["cad_resp-email_resp"]		= (trim($fields['EMAIL_RESP']));
	endif;
	return $arr;
}

function insertMember( $parameters ) {
	$arr = array();
	$arr["result"] = false;
	
	if ( isset($parameters["id"]) && $parameters["id"] == "Novo" ):
		if (isset($parameters["nm"])):
			if (isset($parameters["dt"])):
				if (isset($parameters["sx"])):
					$dc = $parameters["dc"];
					
					fConnDB();
					
					$str = "
						SELECT ID
						  FROM CAD_PESSOA
						 WHERE NR_DOC = ?";
					$result = $GLOBALS['conn']->Execute($str, array( $dc ) );
					if ($result->EOF):
						$GLOBALS['conn']->Execute("
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
						$id = $GLOBALS['conn']->Insert_ID();
					else:
						$id = $result->fields['ID'];
					endif;
					return getMember( array( "id" => $id ) );
				endif;
			endif;
		endif;
	endif;
	return $arr;
}

function getMember( $parameters ) {
	$arr = array();
	$arr["result"] = false;
	
	$id = $parameters["id"];

	fConnDB();

	$cargo2 = false;

	$qtdZeros = zeroSizeID();
	$result = $GLOBALS['conn']->Execute("
		SELECT  p.*,
				a.ID AS ID_ATIVO,
				a.ID_UNIDADE,
				a.CD_CARGO,
				a.CD_CARGO2,
				a.TP_CAMISETA,
				a.TP_AGASALHO,
				a.CD_FANFARRA,
				a.FG_REU_SEM
		  FROM CAD_PESSOA p
	 LEFT JOIN CAD_ATIVOS a ON (a.ID = p.ID AND a.NR_ANO = YEAR(NOW()) )
		 WHERE p.ID = ?", Array( $id ) );
	if (!$result->EOF):
		$arr["result"] = true;
		
		$idadeAtual = fIdadeAtual($result->fields['DT_NASC']);

		$barCODE = $GLOBALS['pattern']->getBars()->encode(array(
			"ni" => $id
		));
		
		$arr["membro"] = array( 
			"cad_pessoa-id"			    => fStrZero($result->fields['ID'], $qtdZeros),
			"cad_pessoa-bc"			    => $barCODE,
			"cad_pessoa-nm"			    => (trim($result->fields['NM'])),
			"cad_pessoa-email"		    => (trim($result->fields['EMAIL'])),
			"cad_pessoa-nm_escola"		=> (trim($result->fields['NM_ESCOLA'])),
			"cad_pessoa-ds_relig"		=> (trim($result->fields['DS_RELIG'])),
			"cad_pessoa-dt_bat"		    => is_null($result->fields['DT_BAT']) ? "" : date( 'd/m/Y', strtotime($result->fields['DT_BAT']) ),
			"cad_pessoa-tp_sexo"		=> $result->fields['TP_SEXO'],
			"cad_pessoa-dt_nasc"		=> is_null($result->fields['DT_NASC']) ? "" : date( 'd/m/Y', strtotime($result->fields['DT_NASC']) ),
			"cad_pessoa-nr_doc"		    => trim($result->fields['NR_DOC']),
			"cad_pessoa-nr_cpf"		    => $result->fields['NR_CPF'],
			"cad_pessoa-logradouro"		=> (trim($result->fields['LOGRADOURO'])),
			"cad_pessoa-nr_logr"		=> (trim($result->fields['NR_LOGR'])),
			"cad_pessoa-complemento"	=> (trim($result->fields['COMPLEMENTO'])),
			"cad_pessoa-bairro"		    => (trim($result->fields['BAIRRO'])),
			"cad_pessoa-cidade"		    => (trim($result->fields['CIDADE'])),
			"cad_pessoa-uf"			    => $result->fields['UF'],
			"cad_pessoa-cep"		    => $result->fields['CEP'],
			"cad_pessoa-fone_res"		=> $result->fields['FONE_RES'],
			"cad_pessoa-fone_cel"		=> $result->fields['FONE_CEL'],
			"cad_pessoa-ano_dir"		=> $result->fields['ANO_DIR'],
			"cad_pessoa-estr_devol"		=> $result->fields['ESTR_DEVOL'],
			"cad_pessoa-qt_uniformes"	=> $result->fields['QT_UNIFORMES'],
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
			$arr["membro"] = fMergeResp( $arr["membro"], verificaRespByID( $result->fields['ID_RESP'] ) );
		else:
			$GLOBALS['conn']->Execute("UPDATE CAD_PESSOA SET ID_RESP = NULL WHERE ID = ?", array( $id ) );
		endif;

		$cargo2 = strpos($result->fields['CD_CARGO'], "2-07") === 0;
	endif;

	$arr["unidades"]	= getUnidades( $parameters );
	$arr["cargos"]		= getCargos( $parameters );
	if ( $cargo2 ) {
		$param2 = $parameters;
		$param2["tp"] = true;
		$arr["cargos2"]	=  getCargos( $param2 );
	}
	$arr["instrumentos"]	= getInstrumentos( $parameters );
	$arr["anos"]		= getAnosDir( $parameters );

	return $arr;
}

function getUnidades( $parameters ) {
	$arr = array();
	fConnDB();
	
	$result = $GLOBALS['conn']->Execute("
		SELECT TP_SEXO, DT_NASC
		  FROM CAD_PESSOA 
		 WHERE ID = ?", Array( $parameters["id"] ) );
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
				$fIdade = " AND IDADE > 15";
				$fd = ",'A'";
			endif;
		endif;
		
		$result = $GLOBALS['conn']->Execute("
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
	fConnDB();
	
	$result = $GLOBALS['conn']->Execute("
		SELECT ID_UNIDADE, TP_SEXO
		  FROM CON_ATIVOS 
		 WHERE ID = ?
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
		$result = $GLOBALS['conn']->Execute("
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
	fConnDB();
	
	$result = $GLOBALS['conn']->Execute("
		SELECT DISTINCT i.CD AS ID, CONCAT( i.CD, '-', t.DS ) AS DS
		FROM CAD_INSTRUMENTO i
		INNER JOIN TAB_TP_INSTRUMENTO t ON (t.ID = i.ID_TP)
		WHERE i.CD NOT IN (SELECT DISTINCT CD_FANFARRA FROM CAD_ATIVOS WHERE NR_ANO = YEAR(NOW()) AND ID <> ? AND CD_FANFARRA IS NOT NULL)
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
	fConnDB();
	
	$result = $GLOBALS['conn']->Execute("
		SELECT YEAR(DT_NASC) AS ANO, MONTH(DT_NASC) AS MES, YEAR(NOW()) AS HOJE
		  FROM CAD_PESSOA 
		 WHERE ID = ?", Array( $parameters["id"] ) );
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
?>