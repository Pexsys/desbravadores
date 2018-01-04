<?php
@require_once("../include/filters.php");
@require_once("../include/domains.php");

$batismo = getDomainFilter( array( "type" => "B" ) );
$eventos = getDomainFilter( array( "type" => "EV" ) );
$unidades = getDomainFilter( array( "type" => "U" ) );
$classes = getDomainFilter( array( "type" => "C" ) );
?>
<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">Imprimir Listagens</h3>
	</div>
</div>
<div class="row">
	<div class="col-lg-8">
		<select name="cbListagem" id="cbListagem" class="selectpicker form-control input-sm" title="Escolha uma Listagem" data-width="100%" data-container="body" data-actions-box="true">
			<option data-icon="fa fa-history" show="divRegistro" value="geraRegistro.php?filter=">REGISTRO DE HISTÓRICO DE MEMBROS</option>
			<option data-divider="true"></option>
			<option data-icon="fa fa-address-card-o" value="geraListaAtivos.php?">LISTAGEM DE MEMBROS</option>
			<option data-icon="fa fa-bath" show="divBatismo" value="geraListaBatismos.php?filter=">LISTAGEM DE MEMBROS - SITUACAO DE BATISMO</option>
			<option data-icon="fa fa-graduation-cap" show="divClasses" value="geraListaClasse.php?filter=">LISTAGEM DE MEMBROS - CLASSE</option>
			<option data-icon="fa fa-universal-access" show="divUnidades" value="geraListaUnidade.php?filter=">LISTAGEM DE MEMBROS - UNIDADE</option>
			<option data-icon="fa fa-music" show="divFanfarra" value="geraListaFanfarra.php?filter=">LISTAGEM DE MEMBROS - FANFARRA</option>
			<option data-icon="fa fa-user-circle-o" show="divUniformes" value="geraListaUniformes.php?filter=">LISTAGEM DE MEMBROS - UNIFORMES</option>
			<option data-divider="true"></option>
			<option data-icon="fa fa-hand-o-up" value="geraListaPresencaPais.php?">LISTAGEM DE PRESEN&Ccedil;A - REUNI&Atilde;O DE PAIS</option>
			<option data-divider="true"></option>
			<option data-icon="fa fa-star" value="geraListaEstrelas.php?">REQUISI&Ccedil;&Atilde;O DE ESTRELAS DE TEMPO DE SERVI&Ccedil;O</option>
			<option data-divider="true"></option>
			<option data-icon="fa fa-sort-alpha-asc" show="divEventos" value="geraListaEvento.php?eve=">LISTAGEM DE SA&Iacute;DA - ALFAB&Eacute;TICA</option>
			<option data-icon="fa fa-list" show="divEventos" value="geraListaEventoAutoriz.php?eve=">LISTAGEM DE SA&Iacute;DA - CONTROLE DE AUTORIZA&Ccedil;&Otilde;ES - GERAL</option>
			<option data-icon="fa fa-venus-mars" show="divEventos" value="geraListaEventoAutorizGen.php?eve=">LISTAGEM DE SA&Iacute;DA - CONTROLE DE AUTORIZA&Ccedil;&Otilde;ES - G&Ecirc;NERO</option>
			<option data-icon="fa fa-leanpub" show="divEventos" value="geraListaDispensaEscola.php?eve=">LISTAGEM DE SA&Iacute;DA - DISPENSA ESCOLAR</option>
			<option data-icon="glyphicon glyphicon-tent" show="divEventos" value="geraListaEventoTent.php?eve=">LISTAGEM DE SA&Iacute;DA - BARRACAS</option>
			<option data-icon="fa fa-cutlery" show="divEventos" value="geraListaEventoKitchen.php?eve=">LISTAGEM DE SA&Iacute;DA - COZINHA</option>
			<option data-icon="fa fa-bus" show="divEventos" value="geraListaEventoBus.php?eve=">LISTAGEM DE SA&Iacute;DA - PASSAGEIROS</option>
			<option data-icon="fa fa fa-user" show="divEventos" value="geraListaUniformes.php?filter=C&eve=">LISTAGEM DE SA&Iacute;DA - CAMISETAS - GERAL</option>
			<option data-icon="fa fa-male" show="divEventos" value="geraListaUniformes.php?filter=A&eve=">LISTAGEM DE SA&Iacute;DA - AGASALHOS - GERAL</option>
		</select>
	</div>
</div>
<br/>
<div class="row">
	<div class="col-lg-8" name="rowFilter" id="divUniformes" style="display:none;">
	    	<label for="cbUniformes" class="control-label">Uniforme:</label>
	    	<select name="cbUniformes" id="cbUniformes" class="selectpicker form-control input-sm" title="Escolha o tipo de Uniforme" data-width="100%" data-container="body" data-actions-box="false">
	    		<option value="A">AGASALHOS</option>
	    		<option value="C" selected>CAMISETAS</option>
	    	</select>
    </div>
	<div class="col-lg-8" name="rowFilter" id="divClasses" style="display:none;">
	    	<label for="cbClasses" class="control-label">Classes:</label>
	    	<select name="cbClasses" id="cbClasses" class="selectpicker form-control input-sm" title="Escolha uma ou mais classes"  multiple data-selected-text-format="count > 4" data-width="100%" data-container="body" data-actions-box="true">
	    	    <?php
	    	    foreach ($classes["domain"] as $k => $o):
	    	    		echo "<option value=\"". $o["id"] ."\" selected>". $o["ds"] ."</option>";
	    	    endforeach;
	    	    ?>
	    	</select>
    </div>
	<div class="col-lg-8" name="rowFilter" id="divUnidades" style="display:none;">
    		<label for="cbUnidades" class="control-label">Unidades:</label>
	    	<select name="cbUnidades" id="cbUnidades" class="selectpicker form-control input-sm" title="Escolha uma ou mais unidades"  multiple data-selected-text-format="count > 4" data-width="100%" data-container="body" data-actions-box="true">
	    	    <?php
	    	    foreach ($unidades["domain"] as $k => $o):
	    	    		echo "<option value=\"". $o["id"] ."\" selected>". $o["ds"] ."</option>";
	    	    endforeach;
	    	    ?>
	    	</select>
    </div>
	<div class="col-lg-8" name="rowFilter" id="divFanfarra" style="display:none;">
    		<label for="cbFanfarra" class="control-label">Exibição:</label>
	    	<select name="cbFanfarra" id="cbFanfarra" class="selectpicker form-control input-sm"  title="Escolha o tipo de Exibição" data-width="100%" data-container="body">
	    		<option value="A">ALFABÉTICA</option>
	    		<option value="I" selected>POR INSTRUMENTO</option>
	    	</select>
    </div>
	<div class="col-lg-8" name="rowFilter" id="divBatismo" style="display:none;">
    		<label for="cbBatismo" class="control-label">Batizado:</label>
		<select name="cbBatismo" id="cbBatismo" class="selectpicker form-control input-sm" title="Escolha a situação de batismo" data-width="100%" data-container="body" data-actions-box="false">
    	    <?php
    	    $s = false;
		foreach ($batismo["domain"] as $k => $o):
			echo "<option value=\"". $o["id"] ."\"". ($o["id"]=="S"?" selected":"") .">". $o["ds"] ."</option>";
		endforeach;
    	    ?>
		</select>
    </div>
	<div class="col-lg-8" name="rowFilter" id="divEventos" style="display:none;">
		<label for="cbEventos" class="control-label">Evento:</label>
		<select name="cbEventos" id="cbEventos" class="selectpicker form-control input-sm" title="Escolha o Evento" data-live-search="true" data-width="100%" data-show-subtext="true" data-container="body" data-actions-box="false">
		<option></option>
		<?php
    	   	$s = false;
			foreach ($eventos["domain"] as $k => $o):
				$id = fStrZero($o["id"],3);
    	        echo "<option value=\"". $o["id"] ."\"". (!$s ? " selected":"") ." data-subtext=\"$id\">". $o["ds"] ."</option>";
    	        $s = true;
			endforeach;
		?>
		</select>
    </div>
 	<div class="col-lg-8" name="rowFilter" id="divRegistro" style="display:none;">
		<label for="cbMembros" class="control-label">Membros:</label>
		<select name="cbMembros" id="cbMembros" class="selectpicker form-control input-sm" title="Escolha um ou mais membros" data-live-search="true" multiple data-selected-text-format="count > 2" data-width="100%" data-container="body" data-actions-box="true">
		<?php
		$qtdZeros = zeroSizeID();
        	$result = $GLOBALS['conn']->Execute("
				SELECT DISTINCT cm.ID, cp.NM
				FROM CAD_MEMBRO cm
				INNER JOIN CAD_PESSOA cp ON (cp.ID = cm.ID_CAD_PESSOA)
			WHERE EXISTS (SELECT 1 FROM APR_HISTORICO WHERE ID_CAD_PESSOA = cm.ID_CAD_PESSOA)
				OR EXISTS (SELECT 1 FROM EVE_SAIDA_MEMBRO WHERE ID_CAD_MEMBRO = cm.ID)
			ORDER BY cp.NM
            ");
        	foreach($result as $l => $fields):
        		$id = fStrZero($fields['ID'], $qtdZeros);
        		echo "<option value=\"". $fields['ID'] ."\" data-subtext=\"$id\">".$fields['NM']."</option>";
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
<script src="<?php echo $GLOBALS['pattern']->getVD();?>dashboard/js/printListagens.js<?php echo "?".microtime();?>"></script>