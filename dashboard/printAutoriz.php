<?php
$membroID = $_SESSION['USER']['id_cad_pessoa'];

function drawBoxesArea($title,$result,$boxClass){
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
				echo fItemAprendizado(array(
					"classPanel" => $boxClass,
					"leftIcon" => $icon, 
					"value" => $value, 
					"title" => titleCase( $fields["DS_ITEM"] ), 
					"strBL" => titleCase( $area )
				));
			endforeach;
			?>
		</div>
		<?php
	endif;
}

//MEMBRO LOGADO
$result = $GLOBALS['conn']->Execute("
	SELECT es.ID, es.DS, esp.ID_CAD_PESSOA, es.DH_S 
	  FROM EVE_SAIDA_PESSOA esp 
INNER JOIN EVE_SAIDA es ON (es.ID = esp.ID_EVE_SAIDA AND es.DH_R > NOW() AND es.FG_IMPRIMIR = ?)
     WHERE esp.FG_AUTORIZ = ?
       AND esp.ID_CAD_PESSOA = ?
  ORDER BY es.DH_S DESC
", array( "S", "S", $membroID ) );
if ( !$result->EOF ):
    ?>
    <div class="row" id="minhasAutoriz">
    	<div class="col-lg-12">
    		<h3 class="page-header">Reimprimir Minha Autoriza&ccedil;&atilde;o</h3>
    	</div>
    </div>
	<?php
	foreach ($result as $k => $fields):
	    	echo "<div class=\"col-md-8 col-xs-12 col-sm-12 col-lg-4\">";
			echo "  <div class=\"panel panel-success\" name=\"reprint\" cad-id=\"".$fields["ID_CAD_PESSOA"]."\" aut-id=\"".$fields["ID"]."\">";
			echo "	<div class=\"panel-heading\" style=\"cursor:pointer;\">";
	        echo "	    <div class=\"row\">
    					<div class=\"col-xs-3\"><i class=\"fa fa-id-card-o fa-4x\"></i></div>
    					<div class=\"col-xs-9 text-right\">
    						<div class=\"huge\">".strftime("%Y",strtotime($fields["DH_S"]))."-".fStrZero($fields["ID"],3)."</div>
    					</div>
    					<div class=\"col-xs-12 text-right\">".strtoupper($fields["DS"])."</div>
    				</div>
			    </div>	
	    		</div>
	    	</div>";
	endforeach;
endif;
?>
<div id="outrasAutoriz" style="display:none">
	<div class="row">
		<div class="col-lg-12">
			<h3 class="page-header">Reimprimir Outras Autoriza&ccedil;&otilde;es</h3>
		</div>
	</div>
	<form class="form-horizontal" method="post" id="capas-form">
		<div class="col-xs-12 col-md-12">
			<div class="row form-group">
				<label for="nmMembro" class="control-label">Para quem?</label>
				<select name="nmMembro" id="nmMembro" class="selectpicker form-control input-sm" opt-value="id" multiple opt-label="ds" opt-subtext="sb" data-live-search="true" title="Escolha um ou mais nomes" data-selected-text-format="count > 5" data-width="100%" data-container="body" data-actions-box="true"></select>
			</div>
		</div>
		<div class="col-xs-12 col-md-12">
			<div class="row form-group form-group-sm">
				<input type="submit" class="btn btn-success pull-right" value="Imprimir Selecionadas"/>
			</div>
		</div>
	</form>
</div>
<div id="alertAutoriz" style="display:none">
	<div class="row">
		<div class="col-lg-12">
			<h3 class="page-header">Você não está autorizado a imprimir sua autorização. Procure a secretaria do clube.</h3>
		</div>
	</div>
</div>
<script src="<?php echo $GLOBALS['pattern']->getVD();?>dashboard/js/printAutoriz.js<?php //echo "?".microtime();?>"></script>