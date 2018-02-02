<?php
function fDashBoard( $panelClass, $iconLeft, $query, $showZero, $titulo, $urlDetalhes ) {
	session_start();
	$mostra = true;
	$qtd = 0;
	
	$result = $GLOBALS['conn']->Execute($query);
	if (!$result->EOF):
		$qtd = $result->fields['qtd'];
	endif;
	
	if ( $qtd == 0 && !$showZero ):
		$mostra = false;
	endif;
	
	if ($mostra):
		echo "
		<div class=\"col-md-4 col-sm-6 col-lg-3\">
			<div class=\"panel $panelClass\">
				<div class=\"panel-heading\">
					<div class=\"row\">
						<div class=\"col-xs-3\">
							<i class=\"$iconLeft\"></i>
						</div>
						<div class=\"col-xs-9 text-right\">
							<div class=\"huge\">$qtd</div>
							<div>$titulo</div>
						</div>
					</div>
				</div>";
	
		
		$rs = $GLOBALS['conn']->Execute("
			SELECT 1
			  FROM CAD_USUARIO cu
		  INNER JOIN CON_ATIVOS cp ON (cp.ID_CAD_PESSOA = cu.ID_CAD_PESSOA)
		  INNER JOIN CAD_USU_PERFIL cuf ON (cuf.ID_CAD_USUARIO = cu.ID
		  INNER JOIN TAB_PERFIL_ITEM tpi ON (tpi.ID_TAB_PERFIL = cuf.ID_PERFIL AND tpi.ID_TAB_DASHBOARD = 5)
			 WHERE cu.ID = ?
		",array( $_SESSION['USER']['id'] ) );
		if (!$rs->EOF):
			echo 	"<a href=\"$urlDetalhes\">
						<div class=\"panel-footer\">
							<span class=\"pull-left\">Detalhes</span>
							<span class=\"pull-right\"><i class=\"fa fa-arrow-circle-right\"></i></span>
							<div class=\"clearfix\"></div>
						</div>
					</a>";
		endif;
		echo "
			</div>
		</div>";
	endif;
}
?>
<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">O Clube</h3>
	</div>
</div>
<div class="row">
	<?php
		insDocs();
		fDashBoard( "panel-red", "fa fa-thumbs-down fa-4x", 
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
			false, "Pend&ecirc;ncias", $GLOBALS['pattern']->getVD()."dashboard/index.php?id=5&flt=PC&op=ALL" );
			
		fDashBoard( "panel-green", "fa fa-toggle-on fa-4x", 
			"SELECT COUNT(*) AS qtd FROM CON_ATIVOS", 
			true, "Ativos", $GLOBALS['pattern']->getVD()."dashboard/index.php?id=5" );

		fDashBoard( "panel-yellow", "fa fa-check-circle fa-4x", 
			"SELECT COUNT(*) AS qtd FROM CON_ATIVOS WHERE dt_bat IS NULL",
			false, "N&atilde;o Batizados", $GLOBALS['pattern']->getVD()."dashboard/index.php?id=5&flt=B&op=N" );

		fDashBoard( "panel-primary", "fa fa-bath fa-4x", 
			"SELECT COUNT(*) AS qtd FROM CON_ATIVOS WHERE dt_bat IS NOT NULL",
			false, "Batizados", $GLOBALS['pattern']->getVD()."dashboard/index.php?id=5&flt=B&op=S" );
			
		fDashBoard( "panel-info", "fa fa-info-circle fa-4x", 
			"SELECT COUNT(*) AS qtd FROM CAD_MEMBRO",
			true, "Cadastrados", $GLOBALS['pattern']->getVD()."dashboard/index.php?id=5&flt=ALL" );
	?>
</div>