<?php
$cadMembroID = $_SESSION['USER']['ID_CAD_MEMBRO'];
$pessoaID = $_SESSION['USER']['ID_CAD_PESSOA'];

function getClass( $pct ){
	if ($pct < 51):
		return "bg-danger";
	elseif ($pct < 85):
		return "bg-warning";
	elseif ($pct < 100):
		return "bg-primary";
	else:
		return "bg-green";
	endif;
}

function drawBoxesArea($title,$result,$boxClass = NULL){
	if (!$result->EOF):
		?>
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="card">
					<div class="header bg-deep-orange">
						<h2><?php echo $title;?></h2>
					</div>
					<div class="body">
						<div class="row">
							<?php
							//TOTAL DE REQUISITOS POR CLASSE.
							foreach ($result as $k => $fields):
								$tp = $fields["TP_ITEM"];
								$value = ( $tp == "ES" ? $fields["CD_ITEM_INTERNO"] : "" );
								$icon = getIconAprendizado( $fields["TP_ITEM"], $fields["CD_AREA_INTERNO"], "fa-4x" );
								$area = getMacroArea( $fields["TP_ITEM"], $fields["CD_AREA_INTERNO"] );
								$class = (isset($boxClass) ? $boxClass : (empty($fields["BOX_CLASS"]) ? "bg-default" : $fields["BOX_CLASS"]));
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
					</div>
				</div>
			</div>
		</div>
		<?php
	endif;
}
?>
<div class="block-header">
	<h1>Meu Painel</h1>
</div>
<?php
$result = CONN::get()->Execute("
SELECT TP, CM, DS, CMPL, FG_IM, DS_ITEM, FUNDO
  FROM CON_COMPRAS cc
 WHERE cc.TP_INCL = 'M'
   AND cc.FG_COMPRA = 'S'
   AND cc.FG_ENTREGUE = 'N'
   AND cc.ID_CAD_MEMBRO = ?
", array($cadMembroID) );
if (!$result->EOF):
	?>
	<div class="block-header">
		<h5>Itens a retirar na secretaria</h5>
	</div>
	<div class="row">
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
				"classPanel" => "bg-red",
				"leftIcon" => $icon,
				"value" => $area,
				"title" => titleCase( $ds, array(" "), array("OU", "COU", "APS", "P", "PP", "G", "GG", "M", "GGX", "3G") )
			));
		endforeach;
		?>
	</div>
	<?php
endif;

$result = CONN::get()->Execute("
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
	<div class="block-header">
		<h5>Itens solicitados/encomendados</h5>
	</div>
	<div class="row">
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
				"classPanel" => "bg-orange",
				"leftIcon" => $icon,
				"value" => $area,
				"title" => titleCase( $ds, array(" "), array("OU", "COU", "APS", "P", "PP", "G", "GG", "M", "GGX", "3G") )
			));
		endforeach;
		?>
	</div>
	<?php
endif;

$result = CONN::get()->Execute("
	SELECT ID_CAD_PESSOA, ID_TAB_APREND, TP_ITEM, CD_AREA_INTERNO, CD_ITEM_INTERNO, DS_ITEM, DT_INICIO, COUNT(*) AS QT_REQ
	FROM CON_APR_PESSOA
	WHERE ID_CAD_PESSOA = ? AND DT_CONCLUSAO IS NULL
	GROUP BY ID_CAD_PESSOA, ID_TAB_APREND, TP_ITEM, CD_AREA_INTERNO, CD_ITEM_INTERNO, DS_ITEM, DT_INICIO
	ORDER BY TP_ITEM, CD_ITEM_INTERNO
", array($pessoaID) );
if (!$result->EOF):
	?>
	<div class="block-header">
		<h5>Itens em andamento em <?echo date("Y");?></h5>
	</div>
	<div class="row">
		<?php
		//TOTAL DE REQUISITOS POR CLASSE.
		foreach ($result as $k => $fields):
			$icon = getIconAprendizado( $fields["TP_ITEM"], $fields["CD_AREA_INTERNO"], "fa-4x" );
			$area = getMacroArea( $fields["TP_ITEM"], $fields["CD_AREA_INTERNO"] );

			if ($fields["TP_ITEM"] == "ES"):
				echo fItemAprendizado(array(
					"classPanel" => "bg-pink",
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
				$rs = CONN::get()->Execute("
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

$result = CONN::get()->Execute("
   SELECT ta.TP_ITEM, ta.CD_ITEM_INTERNO, ta.DS_ITEM, ta.CD_AREA_INTERNO, ah.DT_CONCLUSAO AS DT
	 FROM APR_HISTORICO ah
INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = ah.ID_TAB_APREND)
	WHERE ah.ID_CAD_PESSOA = ?
	  AND ah.DT_CONCLUSAO IS NOT NULL
	  AND ah.DT_AVALIACAO IS NULL
	ORDER BY ta.TP_ITEM, ta.CD_ITEM_INTERNO
", array($pessoaID) );
drawBoxesArea("Itens não avaliados pelo regional",$result,"bg-yellow");

$result = CONN::get()->Execute("
   SELECT DISTINCT ta.TP_ITEM, ta.CD_ITEM_INTERNO, ta.DS_ITEM, ta.CD_AREA_INTERNO, ah.DT_AVALIACAO AS DT,
			 IF(cc.FG_COMPRA = 'S' OR ccag.ID IS NOT NULL,'panel-success','bg-amber') AS BOX_CLASS,
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

$result = CONN::get()->Execute("
   SELECT ta.TP_ITEM, ta.CD_ITEM_INTERNO, ta.DS_ITEM, ta.CD_AREA_INTERNO, ah.DT_INVESTIDURA AS DT
	 FROM APR_HISTORICO ah
INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = ah.ID_TAB_APREND)
	WHERE ah.ID_CAD_PESSOA = ?
	  AND YEAR(ah.DT_INVESTIDURA) = Year(NOW())
	ORDER BY ta.TP_ITEM, ta.CD_ITEM_INTERNO
", array($pessoaID) );
drawBoxesArea("Itens recebidos em ".date("Y"),$result,"bg-green");

$matAnteriores = CONN::get()->Execute("
	SELECT COMPL, DT_ENTREGA, TP, DS, FUNDO, CMPL, FG_ALMOX, FG_IM
	FROM CON_MAT_HISTORICO
	WHERE ID_CAD_MEMBRO = ?
	  AND YEAR(DT_ENTREGA) < YEAR(NOW())
	ORDER BY TP, DT_ENTREGA DESC
", array($cadMembroID) );

$matAno = CONN::get()->Execute("
	SELECT COMPL, DT_ENTREGA, TP, DS, FUNDO, CMPL, FG_ALMOX, FG_IM
	FROM CON_MAT_HISTORICO
	WHERE ID_CAD_MEMBRO = ?
	AND YEAR(DT_ENTREGA) = YEAR(NOW())
	ORDER BY TP, DT_ENTREGA DESC
", array($cadMembroID) );

if (!$matAnteriores->EOF || !$matAno->EOF):
?>
<div class="row">
	<?php if (!$matAno->EOF):?>
	<div class="col-md-12 col-xs-12 col-sm-12 col-lg-6 col-xl-6">
		<div class="card">
			<div class="header bg-green">
				<h2>Materiais recebidos em <?php echo date("Y");?></h2>
			</div>
			<div class="body">
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
	<div class="col-md-12 col-xs-12 col-sm-12 col-lg-6 col-xl-6">
		<div class="card">
			<div class="header bg-light-green">
				<h2>Materiais recebidos anteriormente</h2>
			</div>
			<div class="body">
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
	</div>
	<?php endif;?>
</div>
<?php
endif;
?>
<script src="<?php echo PATTERNS::getVD();?>js/aprendizadoFunctions.js<?php echo "?".time();?>"></script>
<script src="<?php echo PATTERNS::getVD();?>admin/view/screens/dashboard/meu/index.js<?php echo "?".time();?>"></script>
