<?php
function fDashBoard( $aOpt ) {
	session_start();
	$mostra = true;
	$qtd = 0;

	if (isset($aOpt["query"])):
		$result = CONN::get()->Execute($aOpt["query"]);
		if (!$result->EOF):
			$qtd = $result->fields['qtd'];
		endif;
	endif;
	if ($qtd == 0 && (!array_key_exists("showzero",$aOpt) || !$aOpt["showzero"])):
		$mostra = false;
	endif;

	if ($mostra):
		echo "<div class=\"col-lg-3 col-md-4 col-sm-6 col-xs-12\">
			<div class=\"info-box {$aOpt["class"]} hover-zoom-effect\">
				<div class=\"icon\">
					<i class=\"{$aOpt["icon"]}\"></i>
				</div>
				<div class=\"content\">
					<div class=\"text\">{$aOpt["title"]}</div>
					<div class=\"number\">$qtd</div>
				</div>
			</div>
		</div>";
	endif;
}
?>
<div class="block-header">
	<h1>O Clube</h1>
</div>
<div class="row">
	<?php
		insDocs();
		fDashBoard(array(
			"class" => "bg-red",
			"icon" => "fa fa-thumbs-down fa-4x",
			"query" =>
				"SELECT COUNT(*) AS qtd FROM (
					SELECT 'NOME COMPLETO' AS INCONSIST FROM CON_ATIVOS WHERE LENGTH(NM)<=5
					UNION ALL
					SELECT 'SEXO' AS INCONSIST FROM CON_ATIVOS WHERE TP_SEXO NOT IN ('M','F')
					UNION ALL
					SELECT 'DT.NASC' AS INCONSIST FROM CON_ATIVOS WHERE DT_NASC IS NULL OR LENGTH(DT_NASC) = 0
					UNION ALL
					SELECT 'DOCUMENTO' AS INCONSIST FROM CON_ATIVOS WHERE NR_DOC IS NULL OR LENGTH(NR_DOC) < 7 OR INSTR(TRIM(NR_DOC),' ') < 3
					UNION ALL
					SELECT 'CPF' AS INCONSIST FROM CON_ATIVOS WHERE (NR_CPF IS NULL OR LENGTH(NR_CPF) = 0) AND NR_CPF_RESP IS NULL
					UNION ALL
					SELECT 'LOGRADOURO' AS INCONSIST FROM CON_ATIVOS WHERE LOGRADOURO IS NULL OR LENGTH(LOGRADOURO) = 0
					UNION ALL
					SELECT 'NÚM.LOGR.' AS INCONSIST FROM CON_ATIVOS WHERE NR_LOGR IS NULL OR LENGTH(NR_LOGR) = 0
					UNION ALL
					SELECT 'BAIRRO' AS INCONSIST FROM CON_ATIVOS WHERE BAIRRO IS NULL OR LENGTH(BAIRRO) = 0
					UNION ALL
					SELECT 'CIDADE' AS INCONSIST FROM CON_ATIVOS WHERE CIDADE IS NULL OR LENGTH(CIDADE) = 0
					UNION ALL
					SELECT 'ESTADO' AS INCONSIST FROM CON_ATIVOS WHERE UF IS NULL OR LENGTH(UF) = 0
					UNION ALL
					SELECT 'CEP' AS INCONSIST FROM CON_ATIVOS WHERE CEP IS NULL OR LENGTH(CEP) = 0
					UNION ALL
					SELECT 'TELEFONE' AS INCONSIST FROM CON_ATIVOS WHERE (FONE_RES IS NULL AND FONE_CEL IS NULL) OR LENGTH(CONCAT(FONE_RES,FONE_CEL)) = 0
					UNION ALL
					SELECT 'UNIDADE' AS INCONSIST FROM CON_ATIVOS WHERE ID_UNIDADE IS NULL OR ID_UNIDADE = 0
					UNION ALL
					SELECT 'CARGO/FUNÇÃO' AS INCONSIST FROM CON_ATIVOS WHERE CD_CARGO IS NULL OR LENGTH(CD_CARGO)<=1
				) c",
			"title" => "Pend&ecirc;ncias",
			"url" => PATTERNS::getVD()."dashboard/index.php?id=5&flt=PC&op=ALL"
		));

		fDashBoard(array(
			"class" => "bg-green",
			"icon" => "fa fa-toggle-on fa-4x",
			"query" => "SELECT COUNT(*) AS qtd FROM CON_ATIVOS",
			"title" => "Ativos",
			"url" => PATTERNS::getVD()."dashboard/index.php?id=5"
		));

		fDashBoard(array(
			"class" => "bg-pink",
			"icon" => "fa fa-check-circle fa-4x",
			"query" => "SELECT COUNT(*) AS qtd FROM CON_ATIVOS WHERE dt_bat IS NULL",
			"title" => "N&atilde;o Batizados",
			"url" => PATTERNS::getVD()."dashboard/index.php?id=5&flt=B&op=N"
		));

		fDashBoard(array(
			"class" => "bg-cyan",
			"icon" => "fa fa-bath fa-4x",
			"query" => "SELECT COUNT(*) AS qtd FROM CON_ATIVOS WHERE dt_bat IS NOT NULL",
			"title" => "Batizados",
			"url" => PATTERNS::getVD()."dashboard/index.php?id=5&flt=B&op=S"
		));

		fDashBoard(array(
			"class" => "bg-blue",
			"icon" => "fa fa-info-circle fa-4x",
			"query" => "SELECT COUNT(*) AS qtd FROM CAD_MEMBRO",
			"title" => "Cadastrados",
			"url" => PATTERNS::getVD()."dashboard/index.php?id=5&flt=ALL"
		));
	?>
</div>
