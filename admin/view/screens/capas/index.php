<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">Gerar Capas de Especialidades</h3>
	</div>
</div>
<div class="row">
	<div class="col-lg-12">
		<div class="row">
			<form class="form-horizontal" method="post" id="capas-form">
				<div class="col-xs-12 col-md-12">
					<div class="row form-group" style="margin-bottom:20px">
						<table class="compact row-border hover stripe" style="cursor:pointer" cellspacing="0" width="100%" id="simpledatatable">
							<thead>
								<tr>
									<th>C&oacute;d.</th>
									<th>Especialidade</th>
									<th>&Aacute;rea</th>
								</tr>
							</thead>
						<tbody/>
						</table>
					</div>
				</div>
				<div class="col-xs-12 col-md-12">
					<div class="row form-group">
						<label for="nmMembro" class="control-label">Para quem?</label>
						<select name="nmMembro" id="nmMembro" class="selectpicker form-control input-sm" opt-value="id" multiple opt-label="ds" opt-search="id" opt-links="fg" opt-subtext="sb" opt-link-class="S=minhaCapa" opt-selected="fg" data-live-search="true" title="Escolha um ou mais nomes" data-selected-text-format="count > 5" data-width="100%" data-container="body" data-actions-box="true"></select>
					</div>
				</div>
				<div class="col-xs-12 col-md-12">
					<div class="row form-group form-group-sm">
						<input type="button" id="clearSelection" class="btn btn-warning  pull-left" value="Limpar Sele&ccedil;&atilde;o"/>
						<input type="submit" class="btn btn-success pull-right" value="Gerar Selecionadas"/>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<script src="<?php echo PATTERNS::getVD();?>admin/view/screens/capas/index.js<?php echo "?".time();?>"></script>
