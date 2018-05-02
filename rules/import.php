<?php
@require_once("../include/functions.php");
@require_once("../include/acompanhamento.php");


//ANALISE/IMPORTACAO DE HISTORICO DSA/UCB
$i = 0;
$u = 0;
$result = CONN::get()->Execute("
    SELECT t.ID, t.ID_CAD_PESSOA, t.TP_ITEM, t.CD_AREA_INTERNO, t.CD_ITEM_INTERNO, a.DS_ITEM, t.DT_INICIO, t.DT_CONCLUSAO, t.DT_AVALIACAO, t.DT_INVESTIDURA, a.ID AS ID_TAB_APREND
      FROM TMP_HIST_DSA t
INNER JOIN TAB_APRENDIZADO a ON (a.CD_ITEM_INTERNO = t.CD_ITEM_INTERNO)
     WHERE t.FG_INTEGRADO = 'N'
  ORDER BY t.ID
");
foreach ($result as $k => $ls):
	$aprendID = $ls['ID_TAB_APREND'];
	$pessoaID = $ls['ID_CAD_PESSOA'];
	$internoCD = $ls['CD_ITEM_INTERNO'];

	$uh = updateHistorico( 
		$pessoaID, 
		$aprendID, 
		array( 
			"dt_inicio" => $ls['DT_INICIO'], 
			"dt_conclusao" => $ls['DT_CONCLUSAO'], 
			"dt_avaliacao" => $ls['DT_AVALIACAO'], 
			"dt_investidura" => $ls['DT_INVESTIDURA'] 
		),
		null 
	);
	
	echo "ITEM[$internoCD],PESSOA[$pessoaID]";
	if ($uh["op"] == "INSERT"):
		++$i;
		echo ",INSERTED";
		
	//SE EXISTE, ATUALIZA DATAS.
	else:
		++$u;
		echo ",UPDATED";
	endif;
	CONN::get()->Execute("COMMIT");
	
	CONN::get()->Execute("UPDATE TMP_HIST_DSA SET FG_INTEGRADO = 'S' WHERE ID = ?", array($ls["ID"]) );
	echo ",INTEGRATED<br/>";
endforeach;
echo "Terminou: $i registros inseridos.<br/>";
echo "Terminou: $u registros alterados.<br/>";
echo "<br/>";
echo "<br/>";

//ANALISE/IMPORTACAO DE APONTAMENTOS DE CLASSE.
$l = 0;
$x = 0;
$esp = 0;
$result = CONN::get()->Execute("SELECT * FROM TMP");
while (!$result->EOF):
	++$l;
	$req			= str_replace("--","-",$result->fields['REQ']);
	$dtAssinat		= $result->fields['DT_ASSINAT'];

	$barDecode	= PATTERNS::getBars()->decode($result->fields['BAR']);
	
	$mr = marcaRequisito( $barDecode["ni"], $barDecode["fi"], $req, $dtAssinat );
	if (!is_null($mr["idreq"])):
		//ATUALIZADA TEMPORARIO COM PESSOA RECUPERADA + APRENDIZADO.
		CONN::get()->Execute("
			UPDATE TMP SET 
				ID_CAD_PESSOA = ?, 
				ID_TAB_APREND = ? 
			WHERE ID = ?
		", array( $pessoaID, $mr["idreq"], $result->fields['ID']) );
		CONN::get()->Execute("COMMIT");
	endif;
	
	//ANALISAR ESPECIALIDADES E INSERIR NO HISTORICO
	$aEspec = explode("/", $result->fields['ESPEC']);
	if ( count($aEspec) > 0 ):
		foreach ($aEspec as $value):
			if (!empty($value)):
				$rs = CONN::get()->Execute("SELECT ID_TAB_APREND FROM TAB_APRENDIZADO WHERE CD_ITEM_INTERNO = ?", array( $value ) );
				echo "ESPEC[$value],PESSOA[".$barDecode["ni"]."],ASSINAT[$dtAssinat]";
				
				if (!$rs->EOF):
					$uh = updateHistorico( 
						$pessoaID, 
						$rs->fields["ID_TAB_APREND"], 
						array( 
							"dt_inicio" => $dtAssinat, 
							"dt_conclusao" => $dtAssinat, 
							"dt_avaliacao" => null,
							"dt_investidura" => null
						),
						null 
					);

					if ($uh["op"]== "INSERT"):
						++$esp;
						echo ",INSERTED";
					else:
						echo ",NOT INSERTED";
					endif;
				else:
					echo ",NOT INSERTED";
				endif;
				echo "<br/>";
			endif;
			
		endforeach;
	endif;

	$result->MoveNext();
endwhile;

CONN::get()->Execute("
INSERT INTO TMP_ORIG (BAR,REQ,DT_ASSINAT,ESPEC,ID_TAB_APREND,ID_CAD_PESSOA)
SELECT BAR,REQ,DT_ASSINAT,ESPEC,ID_TAB_APREND,ID_CAD_PESSOA FROM TMP WHERE ID_TAB_APREND IS NOT NULL AND ID_CAD_PESSOA IS NOT NULL
");
CONN::get()->Execute("DELETE FROM TMP WHERE ID_TAB_APREND IS NOT NULL AND ID_CAD_PESSOA IS NOT NULL");
CONN::get()->Execute("COMMIT");

echo "Terminou: $x/$l registros alterados.<br/>";
echo "Terminou: $esp especialidades inseridas.<br/>";
echo "<br/>";
echo "<br/>";

//ANALISE DE HISTORICO
$analisados = 0;
$incluidos = 0;
$alterados = 0;
$deletados = 0;
$rs = CONN::get()->Execute("
	SELECT DISTINCT at.NM, at.ID
	  FROM APR_HISTORICO ah
    INNER JOIN CON_ATIVOS at ON (at.ID_CAD_PESSOA = ah.ID_CAD_PESSOA)
    INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = ah.ID_TAB_APREND)
    	 WHERE ah.DT_CONCLUSAO IS NULL
      ORDER BY at.NM
");
if (!$rs->EOF):
	echo "Calculando historico...<br/>";

	foreach ($rs as $ks => $ls):
		$analisados++;
		$ah = analiseHistoricoPessoa( $ls['ID'] );
		foreach ($ah["op"] as $st):
			echo "$st<br/>";
		endforeach;
		$deletados += $ah["del"];
		$alterados += $ah["upd"];
	endforeach;
	echo "Historico: analisados[$analisados], incluidos[$incluidos], alterados[$alterados], deletados[$deletados]<br/>";
endif;
?>