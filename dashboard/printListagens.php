<?php
@require_once("../include/filters.php");
?>
<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">Imprimir Listagens</h3>
	</div>
</div>
<div class="row">
	<div class="col-lg-10">
		<label for="cbListagem" class="control-label">Listagem:</label>
		<select name="cbListagem" id="cbListagem" class="selectpicker form-control input-sm" title="Escolha uma Listagem" data-width="100%" data-container="body" data-actions-box="true">
			<option value="LST_ATIVOS">LISTAGEM DE MEMBROS ATIVOS</option>
			<option value="LST_NAO_BATIZADOS">LISTAGEM DE MEMBROS ATIVOS NÃO BATIZADOS</option>
			<option value="LST_CLASSE">LISTAGEM DE MEMBROS ATIVOS POR CLASSE</option>
			<option value="LST_CAMISETAS">LISTAGEM DE MATERIAIS/CAMISETAS</option>
			<option value="LST_ESTRELATS">REQUISI&Ccedil;&Atilde;O DE ESTRELAS DE TEMPO DE SERVI&Ccedil;O</option>
		</select>
	</div>
</div>
<br/>
<div class="row">
	<div class="col-lg-5">
		<button id="btnGerar" class="btn btn-success pull-right"><i class="fa fa-print"></i>&nbsp;Gerar</button>
	</div>
</div>
<script src="<?php echo $GLOBALS['VirtualDir'];?>dashboard/js/printListagens.js<?php echo "?".microtime();?>"></script>