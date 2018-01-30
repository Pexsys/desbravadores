<script type="text/javascript" src="<?php echo PATTERNS::getVD();?>js/jquery.sha1.js"></script>
<script type="text/javascript" src="<?php echo PATTERNS::getVD();?>js/login.js?<?php echo microtime();?>"></script>
<div class="modal fade" id="myLoginModal">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="login-box">
				<div class="card">
					<div class="header bg-light-blue">
                        <span class="glyphicon glyphicon-lock"></span> Login
                    </div>
					<div class="body">
						<form id="login-form" method="POST" autocomplete="off">
							<div class="form-group form-float">
								<div class="form-line">
									<input type="text" class="form-control" name="usr" id="usr" autofocus style="text-transform:uppercase"/>
									<label class="form-label" for="usr"><i class="fa fa-barcode"></i>&nbsp;Usu&aacute;rio</label>
								</div>
							</div>
							<div class="form-group form-float">
								<div class="form-line">
									<input type="password" class="form-control" name="psw" id="psw"/>
									<label class="form-label" for="usr"><i class="fa fa-lock"></i>&nbsp;Senha</label>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-7 p-t-5"></div>
								<div class="col-xs-5">
									<button type="submit" class="btn btn-success waves-effect">
	                                    <i class="material-icons">vpn_key</i>
	                                    <span>Entrar</span>
	                                </button>
								</div>
							</div>
		                </form>
		            </div>
		        </div>
			</div>
		</div>
	</div>
</div>
