<div class="row">
	<div class="col-lg-12">
		<div class="page-header">
			<div class="pull-right form-inline">
				<div class="btn-group">
					<button data-calendar-nav="prev" class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-chevron-left"></i></button>
					<button data-calendar-nav="today" class="btn btn-info btn-xs"><i class="glyphicon glyphicon-header"></i></button>
					<button data-calendar-nav="next" class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-chevron-right"></i></button>
				</div>
				<div class="btn-group">
					<button data-calendar-view="year" class="btn btn-warning btn-xs"><i class="glyphicon glyphicon-th-large"></i></button>
					<button data-calendar-view="month" class="btn btn-warning btn-xs active"><i class="glyphicon glyphicon-calendar"></i></button>
					<button data-calendar-view="week" class="btn btn-warning btn-xs"><i class="glyphicon glyphicon-th-list"></i></button>
					<button data-calendar-view="day" class="btn btn-warning btn-xs"><i class="glyphicon glyphicon-list"></i></button>
				</div>
				<?php if (true): //PODE INSERIR?>
				<div class="btn-group">
					<button class="btn btn-default btn-xs" id="addEvent"><i class="glyphicon glyphicon-plus"></i></button>
				</div>
				<?php endif;?>
			</div>
			<h4></h4>
		</div>
		<div id="calendar"></div>
		<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="events-modal" class="modal fade">
			<div class="modal-dialog" style="width:450px;">
				<div class="modal-content">
					<div class="modal-header">
						<button aria-hidden="true" data-dismiss="modal" class="close" type="button" id="btnX">&times;</button>
						<h3 class="modal-title" id="hTitle">Evento</h3>
					</div>
					<div class="modal-body" style="height:400px;overflow-y:auto;">
						<form method="post" id="myCalendarForm">
							<input type="hidden" id="eventID" field="id"/>
							<div class="row">
								<div class="col-lg-12">
									<div class="panel panel-red" aria-expanded="false">
										<div class="panel-heading">
											<h5 class="panel-title">Data e hor&aacute;rio</h5>
										</div>
										<div class="panel-body">
											<div class="col-lg-6" style="padding-left:0px">
												<div class="input-group date" id="datetimepickerini" datatype="datetimepicker">
													<input type="text" name="dh_ini" id="dh_ini" field="dh_ini" class="form-control input-sm" placeholder="Data/Hora In&iacute;cio do evento"/>
													<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
												</div>
											</div>
											<div class="col-lg-6" style="padding-right:0px">
												<div class="input-group date" id="datetimepickerfim" datatype="datetimepicker">
													<input type="text" name="dh_fim" id="dh_fim" field="dh_fim" class="form-control input-sm" placeholder="Data/Hora Final do evento"/>
													<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div id="divInfo" class="row">
								<div class="col-lg-12" style="margin-bottom:-10px">
									<div class="panel panel-warning" aria-expanded="false">
										<div class="panel-heading">
											<h5 class="panel-title" data-toggle="collapse" href="#cAbout">
												Sobre o Evento (Descri&ccedil;&atilde;o/Local/Endere&ccedil;o)
												<i class="pull-right glyphicon glyphicon-chevron-down"></i>
											</h5>
										</div>
										<div id="cAbout" class="panel-body panel-collapse collapse">
											<div class="row">
												<div class="col-lg-12" style="margin-bottom:-10px">
													<div class="form-group">
														<textarea field="ds_info" name="dsInfo" id="dsInfo" rows="2" class="form-control input-sm" placeholder="Informa&ccedil;&otilde;es adicionais do evento" default-value="Reuni&atilde;o"></textarea>
													</div>
													<div class="form-group">
														<input type="text" name="dsLocal" id="dsLocal" field="ds_local" class="form-control input-sm" placeholder="Local do evento" default-value="IASD Cap&atilde;o Redondo"/>
													</div>

													<div class="panel panel-default" aria-expanded="false">
														<div class="panel-heading">
															<h5 class="panel-title" data-toggle="collapse" href="#cEndereco">
																Endere&ccedil;o
																<i class="pull-right glyphicon glyphicon-chevron-down"></i>
															</h5>
														</div>
														<div id="cEndereco" class="panel-body panel-collapse collapse" style="padding:0px;">
															<div class="row">
																<div class="col-lg-11">
																	<div class="form-group col-xs-9">
																		<input type="text" name="dsLogra" id="dsLogra" field="ds_logra" class="form-control input-sm" placeholder="Logradouro"/>
																	</div>
																	<div class="form-group col-xs-2">
																		<input type="text" name="nrLog" id="nrLog" field="nr_logra" class="form-control input-sm" placeholder="N&ordm;"/>
																	</div>
																</div>
															</div>
															<div class="row">
																<div class="col-lg-11">
																	<div class="form-group col-xs-5">
																		<input type="text" name="dsComp" id="dsComp" field="ds_cmpl" class="form-control input-sm" placeholder="Complemento"/>
																	</div>
																	<div class="form-group col-xs-6">
																		<input type="text" name="dsBai" id="dsBai" field="ds_bai" class="form-control input-sm" placeholder="Bairro"/>
																	</div>
																</div>
															</div>
															<div class="row">
																<div class="col-lg-11">
																	<div class="form-group col-xs-9">
																		<input type="text" name="dsCid" id="dsCid" field="ds_cid" class="form-control input-sm" placeholder="Cidade"/>
																	</div>
																	<div class="form-group col-xs-2">
																		<select field="cd_uf" name="cmUF" id="cmUF" class="form-control input-sm" placeholder="UF"></select>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-lg-12" style="margin-bottom:-10px">
									<div class="panel panel-warning" aria-expanded="false">
										<div class="panel-heading">
											<h5 class="panel-title" data-toggle="collapse" href="#cRegraChamada">
												Regras da Chamada e Instru&ccedil;&atilde;o
												<i class="pull-right glyphicon glyphicon-chevron-down"></i>
											</h5>
										</div>
										<div id="cRegraChamada" class="panel-body panel-collapse collapse">
											<div class="row">
												<div class="col-lg-12">
													<div class="form-group col-xs-12">
														<label for="cmGrupo" class="control-label">Grupo</label>
														<select field="tp_grupo" name="cmGrupo" id="cmGrupo" class="form-control input-sm" placeholder="Grupo" default-value="T">
															<option></option>
															<?php
															foreach (fTipoAlvo() as $key => $value):
																echo "<option value=\"$key\">".($value)."</option>";
															endforeach;
															?>
														</select>
													</div>
													<div class="form-group col-xs-12">
														<label for="cmRegra" class="control-label">Regra</label>
														<select field="id_regra" name="cmRegra" id="cmRegra" class="form-control input-sm" placeholder="Regra">
															<option></option>
															<?php
															fConnDB();
															$result = $GLOBALS['conn']->Execute("SELECT id, ds FROM TAB_RGR_CHAMADA ORDER BY ds");
															foreach ($result as $ln):
																echo "<option value=\"".$ln["id"]."\">".($ln["ds"])."</option>";
															endforeach;
															?>
														</select>
													</div>
													<div class="form-group col-xs-12">
														<label for="cmUniforme" class="control-label">Uniforme</label>
														<select field="id_uniforme" name="cmUniforme" id="cmUniforme" class="form-control input-sm" placeholder="Uniforme">
															<option></option>
															<?php
															fConnDB();
															$result = $GLOBALS['conn']->Execute("SELECT id, ds FROM TAB_TP_UNIFORME ORDER BY ds");
															foreach ($result as $ln):
																echo "<option value=\"".$ln["id"]."\">".($ln["ds"])."</option>";
															endforeach;
															?>
														</select>
													</div>
													<div class="form-group col-xs-12">
														<label for="cmInstrucao" class="control-label">Pode assinar requisitos</label>
														<select field="fg_instrucao" name="cmInstrucao" id="cmInstrucao" class="form-control input-sm" placeholder="Instru&ccedil;&atilde;o">
															<option value="S">SIM</option><option value="N">N&Atilde;O</option>
														</select>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-lg-12" style="margin-bottom:-10px">
									<div class="panel panel-warning" aria-expanded="true">
										<div class="panel-heading">
											<h5 class="panel-title" data-toggle="collapse" href="#cInfo">
												Publica&ccedil;&atilde;o
												<i class="pull-right glyphicon glyphicon-chevron-down"></i>
											</h5>
										</div>
										<div id="cInfo" class="panel-body panel-collapse collapse">
											<div class="form-group col-xs-5" style="padding-left:0px">
												<select field="fg_publ" name="cmPub" id="cmPub" class="form-control input-sm" placeholder="Publicar" default-value="S">
													<option value="S">PUBLICAR</option>
													<option value="N">RASCUNHO</option>
												</select>
											</div>
											<div class="form-group col-xs-7" style="padding-right:0px">
												<select field="tp_eve" name="cmTPEve" id="cmTPEve" class="form-control input-sm" placeholder="Tipo do Evento" default-value="DEFAULT">
													<option value="REGIAO">6&ordf; REGI&Atilde;O</option>
													<option value="APS">APS</option>
													<option value="EGW">COL&Eacute;GIO EGW</option>
													<option value="DEFAULT">CLUBE</option>
													<option value="IASD">IASD Cap&atilde;o Redondo</option>
													<option value="SPECIAL">OUTROS</option>
												</select>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-lg-12">
									<?php if (true): //PODE EXCLUIR?>
									<a role="button" class="btn btn-warning pull-left" data-toggle="modal" id="btnDelete"><i class="glyphicon glyphicon-trash"></i>&nbsp;Excluir</a>
									<?php endif;?>
									<?php if (true): //PODE INSERIR/ALTERAR?>
									<button type="submit" class="btn btn-success pull-right"><i class="glyphicon glyphicon-floppy-save"></i>&nbsp;Gravar</button>
									<?php endif;?>
								</div>
							</div>
							<br/>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script src="<?php echo $GLOBALS['VirtualDir'];?>dashboard/js/agenda.js<?php echo "?".microtime();?>"></script>