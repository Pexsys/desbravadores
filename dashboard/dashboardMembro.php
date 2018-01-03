<?php
$cadMembroID = $_SESSION['USER']['id_cad_membro'];
$pessoaID = $_SESSION['USER']['id_cad_pessoa'];

function getClass( $pct ){
	if ($pct < 51):
		return "panel-danger";
	elseif ($pct < 85):
		return "panel-warning";
	elseif ($pct < 100):
		return "panel-primary";
	else:
		return "panel-green";
	endif;
}

function drawBoxesArea($title,$result,$boxClass = NULL){
	if (!$result->EOF):
		?>
		<div class="row">
			<div class="col-lg-12">
				<h4 class="page-header"><?php echo $title;?></h4>
			</div>
			<?php
			//TOTAL DE REQUISITOS POR CLASSE.
			foreach ($result as $k => $fields):
				$tp = $fields["TP_ITEM"];
				$value = ( $tp == "ES" ? $fields["CD_ITEM_INTERNO"] : "" );
				$icon = getIconAprendizado( $fields["TP_ITEM"], $fields["CD_AREA_INTERNO"], "fa-4x" );
				$area = getMacroArea( $fields["TP_ITEM"], $fields["CD_AREA_INTERNO"] );
				$class = (isset($boxClass) ? $boxClass : (empty($fields["BOX_CLASS"]) ? "panel-default" : $fields["BOX_CLASS"]));
				echo fItemAprendizado(array(
					"classPanel" => $class,
					"leftIcon" => $icon, 
					"value" => $value, 
					"title" => titleCase( $fields["DS_ITEM"], array(" "), array("OU", "COU", "APS") ), 
					"strBL" => titleCase( $area ), 
					"strBR" => strftime("%d/%m/%Y",strtotime($fields["DT"])), 
					"hint" => $fields["BOX_HINT"]
				));
			endforeach;
			?>
		</div>
		<?php
	endif;
}
?>
<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">Meu Painel</h3>
	</div>
</div>
<?php
$result = $GLOBALS['conn']->Execute("
SELECT TP, CM, DS, CMPL, FG_IM, DS_ITEM, FUNDO
  FROM CON_COMPRAS cc 
 WHERE cc.TP_INCL = 'M'
   AND cc.FG_COMPRA = 'S'
   AND cc.FG_ENTREGUE = 'N'
   AND cc.ID_CAD_MEMBRO = ?
", array($cadMembroID) );
if (!$result->EOF):
	?>
	<div class="row">
		<div class="col-lg-12">
			<h4 class="page-header">Itens a retirar na secretaria</h4>
		</div>
		<?php
		//TOTAL DE REQUISITOS POR CLASSE.
		foreach ($result as $k => $fields):
			$ds = $fields['DS'];
			
			if ( $fields['CMPL'] == "S" && $fields['FG_IM'] == 'N'):
				$ds .= " - ". $fields['DS_ITEM'];
			endif;
			
			if ( !empty($fields['FUNDO']) ):
				$ds .= " - FUNDO ". ($fields['FUNDO'] == "BR" ?  "BRANCO" : "CAQUI");
			endif;
	
			if ( !empty($fields['CM']) ):
				$ds .= " [ ".$fields['CM']." ]";
			endif;

			$icon = getIconAprendizado( $fields["TP"], "", "fa-4x" );
			$area = getMacroArea( $fields["TP"], "" );
			echo fItemAprendizado(array(
				"classPanel" => "panel-red",
				"leftIcon" => $icon, 
				"value" => $area, 
				"title" => titleCase( $ds, array(" "), array("OU", "COU", "APS") )
			));
		endforeach;
		?>
	</div>
	<?php
endif;

$result = $GLOBALS['conn']->Execute("
	SELECT TP, CM, DS, CMPL, FG_IM, DS_ITEM, FUNDO
	FROM CON_COMPRAS cc 
	WHERE cc.TP_INCL = 'M'
	AND cc.FG_COMPRA = 'N'
	AND cc.FG_ENTREGUE = 'N'
	AND cc.FG_ALMOX = 'N'
	AND cc.ID_CAD_MEMBRO = ?
	", array($cadMembroID) );
if (!$result->EOF):
	?>
	<div class="row">
		<div class="col-lg-12">
			<h4 class="page-header">Itens solicitados/encomendados</h4>
		</div>
		<?php
		//TOTAL DE REQUISITOS POR CLASSE.
		foreach ($result as $k => $fields):
			$ds = $fields['DS'];
			
			if ( $fields['CMPL'] == "S" && $fields['FG_IM'] == 'N'):
				$ds .= " - ". $fields['DS_ITEM'];
			endif;
			
			if ( !empty($fields['FUNDO']) ):
				$ds .= " - FUNDO ". ($fields['FUNDO'] == "BR" ?  "BRANCO" : "CAQUI");
			endif;
	
			if ( !empty($fields['CM']) ):
				$ds .= " [ ".$fields['CM']." ]";
			endif;

			$icon = getIconAprendizado( $fields["TP"], "", "fa-4x" );
			$area = getMacroArea( $fields["TP"], "" );
			echo fItemAprendizado(array(
				"classPanel" => "panel-warning",
				"leftIcon" => $icon, 
				"value" => $area, 
				"title" => titleCase( $ds, array(" "), array("OU", "COU", "APS") )
			));
		endforeach;
		?>
	</div>
	<?php
endif;

$result = $GLOBALS['conn']->Execute("
	SELECT ID_CAD_PESSOA, ID_TAB_APREND, TP_ITEM, CD_AREA_INTERNO, CD_ITEM_INTERNO, DS_ITEM, DT_INICIO, COUNT(*) AS QT_REQ
	FROM CON_APR_PESSOA
	WHERE ID_CAD_PESSOA = ? AND DT_CONCLUSAO IS NULL
	GROUP BY ID_CAD_PESSOA, ID_TAB_APREND, TP_ITEM, CD_AREA_INTERNO, CD_ITEM_INTERNO, DS_ITEM, DT_INICIO
	ORDER BY TP_ITEM, CD_ITEM_INTERNO
", array($pessoaID) );
if (!$result->EOF):
	?>
	<div class="row">
		<div class="col-lg-12">
			<h4 class="page-header">Itens em andamento em <?echo date("Y");?></h4>
		</div>
		<?php
		//TOTAL DE REQUISITOS POR CLASSE.
		foreach ($result as $k => $fields):
			$icon = getIconAprendizado( $fields["TP_ITEM"], $fields["CD_AREA_INTERNO"], "fa-4x" );
			$area = getMacroArea( $fields["TP_ITEM"], $fields["CD_AREA_INTERNO"] );
			
			if ($fields["TP_ITEM"] == "ES"):
				echo fItemAprendizado(array(
					"classPanel" => "panel-danger",
					"leftIcon" => $icon, 
					"value" => $fields["CD_ITEM_INTERNO"], 
					"title" => titleCase( $fields["DS_ITEM"] ), 
					"strBL" => titleCase( $area ), 
					"strBR" => strftime("%d/%m/%Y",strtotime($fields["DT_INICIO"]))
				));
			else:
				$qtdReq =  $fields["QT_REQ"];
				$tabAprID =  $fields["ID_TAB_APREND"];
				
				$pct = 0;
				$qtd = 0;
				$rs = $GLOBALS['conn']->Execute("
					SELECT COUNT(*) AS QT_COMPL
					FROM CON_APR_PESSOA
					WHERE ID_CAD_PESSOA = ?
					AND ID_TAB_APREND = ?
					AND DT_ASSINATURA IS NOT NULL
					AND DT_CONCLUSAO IS NULL
				", array($pessoaID, $tabAprID) );
				if (!$rs->EOF):
					$qtd = $rs->fields["QT_COMPL"];
				endif;
				
				$pct = floor( ( $qtd / $qtdReq ) * 100);
				echo fItemAprendizado(array(
					"classPanel" => getClass( $pct ),
					"classSize" => "col-md-12 col-xs-12 col-sm-12 col-lg-6 col-xl-3",
					"leftIcon" => $icon, 
					"value" => "$pct%", 
					"title" => titleCase( $fields["DS_ITEM"] ), 
					"strBL" => titleCase( $area ), 
					"strBR" => "$qtd / $qtdReq", 
					"fields" => array( 
						"name" => "progress",
						"cad-id" => $fields["ID_CAD_PESSOA"],
						"req-id" => $fields["ID_TAB_APREND"]
					)
				));
			endif;
		endforeach;
		?>
	</div>
	<?php
endif;

$result = $GLOBALS['conn']->Execute("
   SELECT ta.TP_ITEM, ta.CD_ITEM_INTERNO, ta.DS_ITEM, ta.CD_AREA_INTERNO, ah.DT_CONCLUSAO AS DT
	 FROM APR_HISTORICO ah
INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = ah.ID_TAB_APREND)
	WHERE ah.ID_CAD_PESSOA = ?
	  AND ah.DT_CONCLUSAO IS NOT NULL
	  AND ah.DT_AVALIACAO IS NULL
	ORDER BY ta.TP_ITEM, ta.CD_ITEM_INTERNO
", array($pessoaID) );
drawBoxesArea("Itens não avaliados pelo regional",$result,"panel-yellow");

$result = $GLOBALS['conn']->Execute("
   SELECT DISTINCT ta.TP_ITEM, ta.CD_ITEM_INTERNO, ta.DS_ITEM, ta.CD_AREA_INTERNO, ah.DT_AVALIACAO AS DT, 
			 IF(cc.FG_COMPRA = 'S' OR ccag.ID IS NOT NULL,'panel-success','panel-warning') AS BOX_CLASS, 
			 IF(cc.FG_COMPRA = 'S' OR ccag.ID IS NOT NULL,'Item comprado','Item ainda não comprado') AS BOX_HINT
	 FROM APR_HISTORICO ah
INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = ah.ID_TAB_APREND)
INNER JOIN TAB_MATERIAIS tm ON (tm.ID_TAB_APREND = ta.ID)
 LEFT JOIN CON_COMPRAS cc ON (cc.ID_CAD_PESSOA = ah.ID_CAD_PESSOA AND cc.FG_COMPRA = 'S' AND cc.ID_TAB_APREND = ah.ID_TAB_APREND)
 LEFT JOIN CON_COMPRAS ccag ON (ccag.ID_CAD_PESSOA = ah.ID_CAD_PESSOA AND ccag.FG_COMPRA = 'S' AND ccag.ID_TAB_APREND = tm.ID_AGRUPADA)
	WHERE ah.ID_CAD_PESSOA = ?
	  AND ah.DT_CONCLUSAO IS NOT NULL
	  AND ah.DT_AVALIACAO IS NOT NULL
	  AND ah.DT_INVESTIDURA IS NULL
	ORDER BY ta.TP_ITEM, ta.CD_ITEM_INTERNO
", array($pessoaID) );
drawBoxesArea("Itens a receber na próxima investidura",$result);

$result = $GLOBALS['conn']->Execute("
   SELECT ta.TP_ITEM, ta.CD_ITEM_INTERNO, ta.DS_ITEM, ta.CD_AREA_INTERNO, ah.DT_INVESTIDURA AS DT
	 FROM APR_HISTORICO ah
INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = ah.ID_TAB_APREND)
	WHERE ah.ID_CAD_PESSOA = ?
	  AND YEAR(ah.DT_INVESTIDURA) = Year(NOW())
	ORDER BY ta.TP_ITEM, ta.CD_ITEM_INTERNO
", array($pessoaID) );
drawBoxesArea("Itens recebidos em ".date("Y"),$result,"panel-green");

$matAnteriores = $GLOBALS['conn']->Execute("
	SELECT COMPL, DT_ENTREGA, TP, DS, FUNDO, CMPL, FG_ALMOX, FG_IM
	FROM CON_MAT_HISTORICO
	WHERE ID_CAD_MEMBRO = ?
	  AND YEAR(DT_ENTREGA) < YEAR(NOW())
	ORDER BY TP, DT_ENTREGA DESC
", array($cadMembroID) );

$matAno = $GLOBALS['conn']->Execute("
	SELECT COMPL, DT_ENTREGA, TP, DS, FUNDO, CMPL, FG_ALMOX, FG_IM
	FROM CON_MAT_HISTORICO
	WHERE ID_CAD_MEMBRO = ?
	AND YEAR(DT_ENTREGA) = YEAR(NOW())
	ORDER BY TP, DT_ENTREGA DESC
", array($cadMembroID) );

if (!$matAnteriores->EOF || !$matAno->EOF):
?>
<div class="row">
	<div class="col-md-12 col-xs-12 col-sm-12 col-lg-12 col-xl-6">
		<h4 class="page-header">Materiais recebidos</h4>
	</div>
	<?php if (!$matAno->EOF):?>
	<div class="col-md-12 col-xs-12 col-sm-12 col-lg-12 col-xl-6">
		<div class="panel panel-green">
			<div class="panel-heading">
				<label>Em <?php echo date("Y");?></label>
			</div>
			<div class="panel-body">
				<?php
				foreach ($matAno as $key => $line):
					$ds = $line['DS'];
					
					if ( $line['CMPL'] == "S" && $line['FG_IM'] == 'N'):
						$ds .= " - ". $line['DS_ITEM'];
					endif;
					
					if ( !empty($fields['FUNDO']) ):
						$ds .= " - FUNDO ". ($line['FUNDO'] == "BR" ?  "BRANCO" : "CAQUI");
					endif;
			
					if ( !empty($line['COMPL']) ):
						$ds .= " [ ".$line['COMPL']." ]";
					endif;
			
					$icon = getIconAprendizado( $line["TP"], "", "fa-4x" );
					$area = getMacroArea( $line["TP"], "" );
					echo fItemAprendizado(array(
						"leftIcon" => $icon, 
						"value" => $area, 
						"title" => titleCase( $ds, array(" "), array("OU", "COU", "APS") )
					));
				endforeach;
				?>
			</div>
		</div>
	</div>
	<?php endif;?>
	<?php if (!$matAnteriores->EOF):?>
	<div class="col-md-12 col-xs-12 col-sm-12 col-lg-12 col-xl-6">
		<div class="panel panel-success">
			<div class="panel-heading">
				<label>Anteriores</label>
			</div>
			<div class="panel-body" style="height:300px;overflow-y:scroll;">
				<div class="table-responsive">
					<table class="table">
						<thead>
							<tr>
								<th style="width:200px">Tipo</th>
								<th>Descri&ccedil;&atilde;o</th>
								<th style="width:100px">Data</th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ($matAnteriores as $key => $line):
								$ds = $line['DS'];
								
								if ( $line['CMPL'] == "S" && $line['FG_IM'] == 'N'):
									$ds .= " - ". $line['DS_ITEM'];
								endif;
								
								if ( !empty($fields['FUNDO']) ):
									$ds .= " - FUNDO ". ($line['FUNDO'] == "BR" ?  "BRANCO" : "CAQUI");
								endif;
						
								if ( !empty($line['COMPL']) ):
									$ds .= " [ ".$line['COMPL']." ]";
								endif;
						
								echo "<tr>
									<td><i class=\"".getIconAprendizado( $line["TP"], "" )."\"></i>&nbsp;".$line["TP"]."</td>
									<td>$ds</td>
									<td>".date( 'd/m/Y', strtotime($line["DT_ENTREGA"]) )."</td>
								</tr>";
							endforeach;
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<?php endif;?>
</div>
<?php
endif;
?>
<script src="<?php echo $GLOBALS['pattern']->getVD();?>dashboard/js/aprendizadoFunctions.js<?php echo "?".microtime();?>"></script>
<script src="<?php echo $GLOBALS['pattern']->getVD();?>dashboard/js/dashboardMembro.js<?php echo "?".microtime();?>"></script>