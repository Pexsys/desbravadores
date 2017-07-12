<?php
@require_once("../include/filters.php");
@require_once("../include/domains.php");

$batismo = getDomainFilter( array( "type" => "B" ) );
$eventos = getDomainFilter( array( "type" => "EV" ) );
?>
<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">Imprimir Listagens</h3>
	</div>
</div>
<div class="row">
	<div class="col-lg-8">
		<select name="cbListagem" id="cbListagem" class="selectpicker form-control input-sm" title="Escolha uma Listagem" data-width="100%" data-container="body" data-actions-box="true">
			<option value="LST_ATIVOS">LISTAGEM DE MEMBROS</option>
			<option value="LST_BATISMO">LISTAGEM DE MEMBROS - SITUACAO DE BATISMO</option>
			<option value="LST_CLASSE">LISTAGEM DE MEMBROS - CLASSE</option>
			<option value="LST_UNIFORMES">LISTAGEM DE MEMBROS - UNIFORMES</option>
			<option data-divider="true"></option>
			<option value="LST_PRESPAIS">LISTAGEM DE PRESEN&Ccedil;A - REUNI&Atilde;O DE PAIS</option>
			<option data-divider="true"></option>
			<option value="LST_ESTRELATS">REQUISI&Ccedil;&Atilde;O DE ESTRELAS DE TEMPO DE SERVI&Ccedil;O</option>
			<option data-divider="true"></option>
			<option value="LST_EVE_ALFA">LISTAGEM DE SA&Iacute;DA - ALFAB&Eacute;TICA</option>
			<option value="LST_EVE_CTRL">LISTAGEM DE SA&Iacute;DA - CONTROLE DE AUTORIZA&Ccedil;&Otilde;ES - GERAL</option>
			<option value="LST_EVE_CTRL_GEN">LISTAGEM DE SA&Iacute;DA - CONTROLE DE AUTORIZA&Ccedil;&Otilde;ES - G&Ecirc;NERO</option>
			<option value="LST_EVE_TENT">LISTAGEM DE SA&Iacute;DA - BARRACAS</option>
			<option value="LST_EVE_KITCHEN">LISTAGEM DE SA&Iacute;DA - COZINHA</option>
			<option value="LST_EVE_PASS">LISTAGEM DE SA&Iacute;DA - PASSAGEIROS</option>
			<option value="LST_EVE_MAT_C">LISTAGEM DE SA&Iacute;DA - CAMISETAS - GERAL</option>
			<option value="LST_EVE_MAT_A">LISTAGEM DE SA&Iacute;DA - AGASALHOS - GERAL</option>
		</select>
	</div>
</div>
<br/>
<div class="row">
	<div class="col-lg-8" id="uniformes" style="display:none;">
    	<label for="cbUniformes" class="control-label">Uniformes:</label>
    	<select name="cbUniformes" id="cbUniformes" class="selectpicker form-control input-sm" title="Escolha o tipo de Uniforme" data-width="100%" data-container="body" data-actions-box="false">
    		<option value="A">AGASALHOS</option>
    		<option value="C" selected>CAMISETAS</option>
    	</select>
    </div>
	<div class="col-lg-8" id="batismo" style="display:none;">
    	<label for="cbBatismo" class="control-label">Batizados:</label>
    	<select name="cbBatismo" id="cbBatismo" class="selectpicker form-control input-sm" title="Escolha a situação de batismo" data-width="100%" data-container="body" data-actions-box="false">
    	    <?php
    	        $s = false;
                foreach ($batismo["domain"] as $k => $o):
                    echo "<option value=\"". $o["value"] ."\"". ($o["value"]=="S"?" selected":"") .">". $o["label"] ."</option>";
                endforeach;
    	    ?>
    	</select>
    </div>
	<div class="col-lg-8" id="eventos" style="display:none;">
    	<label for="cbEventos" class="control-label">Eventos:</label>
    	<select name="cbEventos" id="cbEventos" class="selectpicker form-control input-sm" title="Escolha o Evento" data-width="100%" data-container="body" data-actions-box="false">
    		<option></option>
    	    <?php
    	   		$s = false;
    	        foreach ($eventos["domain"] as $k => $o):
    	        	echo "<option value=\"". $o["value"] ."\"". (!$s ? " selected":"") .">". $o["label"] ."</option>";
    	        	$s = true;
                endforeach;
    	    ?>
    	</select>
    </div>
</div>
<br/>
<div class="row">
	<div class="col-lg-8">
		<button id="btnGerar" class="btn btn-success pull-right"><i class="fa fa-print"></i>&nbsp;Gerar</button>
	</div>
</div>
<script src="<?php echo $GLOBALS['VirtualDir'];?>dashboard/js/printListagens.js<?php echo "?".microtime();?>"></script>