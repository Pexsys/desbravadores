<link href="<?php echo PATTERNS::getVD();?>include/_core/css/acc-wizard.min.css" rel="stylesheet"/>
<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">Cadastro de Acordo Financeiro</h3>
	</div>
</div>
<div class="row" id="divLista">
    <div class="col-lg-12">
    	<div class="row">
    	<?php FILTERS::Data(
    		array(
    			"filterTo" => "#comDataTable",
    			"filters" =>
    				array(
    					array( "id" => "SA", "ds" => "Status", "icon" => "fa fa-hourglass-start" )
    				)
    		)
    	);?>
    	</div>
    	<div class="row">
    		<table class="compact row-border hover stripe" style="cursor: pointer;" cellspacing="0" width="100%" id="comDataTable">
    			<thead>
    				<tr>
    					<th></th>
    					<th>C&oacute;digo</th>
    					<th>Patrocinador</th>
    					<th>Patrocinado</th>
    					<th>Status</th>
    				</tr>
    			</thead>
    			<tbody/>
    		</table>
    		<br/>
    	</div>
    	<div class="row">
    		<a role="button" class="btn btn-primary pull-left" id="btnNovo"><i class="fa fa-plus"></i>&nbsp;Novo</a>
    	</div>
    </div>
</div>
<div class="row" id="divAcordo">
    <form method="post" id="frmAcordo">
        <div class="col-lg-12">
            <div class="panel panel-primary" aria-expanded="false">
            <div class="panel-heading">
                <button aria-hidden="true" class="close" type="button" id="btnX">&times;</button>
                <h3 class="panel-title">Acordo</h3>
            </div>
            <div class="panel-body">
                <div class="col-lg-12">
                    <div class="row">
                    </div>
                    <div class="row">
                        <hr/>
                        <a role="button" class="btn btn-primary pull-left" id="btnFechar"><i class="fa fa-times"></i>&nbsp;Fechar</a>
                        <a role="button" class="btn btn-success pull-right" id="btnGravar"><i class="fa fa-floppy-o"></i>&nbsp;Salvar</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<hr/>
<a href="#Patrocinador">Patrocinador</a>
<a href="#Gerais">Custos Gerais</a>
<a href="#Saidas">Saídas/Passeios</a>
<a href="#Uniformes">Uniformes</a>
<a href="#Acerto">Anteriores</a>

<div class="col-lg-12"><!-- style="display:none"-->
    <div class="row acc-wizard">
      <div class="col-md-3" style="padding-left: 2em;">
        <ol class="acc-wizard-sidebar">
          <li class="acc-wizard-completed acc-wizard-active"><a href="#prerequisites">Prerequisites</a></li>
          <li class="acc-wizard-completed"><a href="#addwizard">Add Wizard</a></li>
          <li class="acc-wizard-completed"><a href="#adjusthtml">Adjust HTML</a></li>
          <li class="acc-wizard-todo"><a href="#viewpage">Release</a></li>
        </ol>
      </div>
      <div class="col-md-9" style="padding-right: 2em;">
        <div id="accordion-demo" class="panel-group">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h4 class="panel-title">
                <a href="#prerequisites" data-parent="#accordion-demo" data-toggle="collapse" class="">
                  Install Bootstrap and jQuery
                </a>
              </h4>
            </div>
            <div id="prerequisites" class="panel-collapse collapse in" style="height: auto;">
              <div class="panel-body">
                <form id="form-prerequisites">
                  <p>
                    The accordion wizard depends on two other open source packages:
                    </p><ul>
                      <li>The Bootstrap framework, available <a href="http://getbootstrap.com">here</a>.
                      </li><li>The jQuery javascript library, available <a href="http://jquery.com">here</a>.
                    </li></ul>
                    Note that Bootstrap itself depends on jQuery for its interactive
                    components, so if you're using Bootstrap you probably already have
                    jQuery as well.
                  <p></p>
                  <p>
                    You'll include the CSS styles for Bootstrap in the
                    <code>&lt;head&gt;</code> of your HTML file, for example:
                  </p>
                  <pre><!--
                  -->&lt;link href="css/bootstrap.min.css" rel="stylesheet"&gt;<!--
                  --></pre>
                  <p>
                    and you'll include jQuery and Bootstrap javascript files at the
                    end of your <code>&lt;body&gt;</code> section, for example:
                  </p>
                  <pre><!--
                  -->&lt;script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js" type="text/javascript"&gt;&lt;/script&gt;
<!--              -->&lt;script src="js/bootstrap.min.js"&gt;&lt;/script&gt;<!--
                  --></pre>
                <div class="acc-wizard-step"><button class="btn btn-primary" type="submit">Next Step</button></div></form>
              </div> <!--/.panel-body -->
            </div> <!-- /#prerequisites -->
          </div> <!-- /.panel.panel-default -->

          <div class="panel panel-default">
            <div class="panel-heading">
              <h4 class="panel-title">
                <a href="#addwizard" data-parent="#accordion-demo" data-toggle="collapse" class="collapsed">
                  Add Accordion Wizard
                </a>
              </h4>
            </div>
            <div id="addwizard" class="panel-collapse collapse" style="height: 36.4px;">
              <div class="panel-body">
                <form id="form-addwizard">
                  <p>
                    If you haven't already found it, the source code for the
                    accordion wizard is available on github
                    <a href="https://github.com/sathomas/acc-wizard">here</a>.
                    There are two main folders, <code>/src</code> and
                    <code>/release</code>.
                  </p>
                  <p>
                    There are two different ways to add the accordion wizard to
                    your pages. The simplest approach is just to add the CSS and
                    javascript files from the <code>/release</code> folder
                    directly in your HTML without modifying them:
                  </p>
                  <pre><!--
                  -->&lt;link href="css/bootstrap.min.css" rel="stylesheet"&gt;
<!--              -->&lt;link href="css/acc-wizard.min.css" rel="stylesheet"&gt;<!--
                  --></pre>
                  <p>
                    and
                  </p>
                  <pre><!--
                  -->&lt;script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js" type="text/javascript"&gt;&lt;/script&gt;
<!--              -->&lt;script src="js/bootstrap.min.js"&gt;&lt;/script&gt;
<!--              -->&lt;script src="js/acc-wizard.min.js"&gt;&lt;/script&gt;<!--
                  --></pre>
                  <p>
                    The release styles for the accordion wizard are based on
                    Bootstrap's default styles. If you've tweaked the Bootstrap
                    styles (e.g. by changing the link color), you'll want to
                    make corresponding tweaks to <code>acc-wizard.min.css</code>.
                  </p>
                  <p>
                    Alternatively, if you're building custom CSS and javascript,
                    then you might want to start with the files in the <code>/src</code>
                    folder and adapt them to your source code. The <code>/src</code>
                    folder contains a LESS file and uncompressed (and commented)
                    javascript. Note that the <code>acc-wizard.less</code> file
                    depends on variables defined in Bootstrap's <code>variables.less</code>
                    file.
                </p><div class="acc-wizard-step"><button class="btn" type="reset">Go Back</button> <button class="btn btn-primary" type="submit">Next Step</button></div></form>
              </div> <!--/.panel-body -->
            </div> <!-- /#addwizard -->
          </div> <!-- /.panel.panel-default -->

          <div class="panel panel-default">
            <div class="panel-heading">
              <h4 class="panel-title">
                <a href="#adjusthtml" data-parent="#accordion-demo" data-toggle="collapse" class="collapsed">
                  Adjust Your HTML Markup
                </a>
              </h4>
            </div>
            <div id="adjusthtml" class="panel-collapse collapse" style="height: 36.4px;">
              <div class="panel-body">
                <form id="form-adjusthtml">
                  <p>
                    Now you can modify your HTML markup to activate the accordion
                    wizard. There are two parts to the markup—the collapsible
                    accordion itself and the task list. I prefer putting both in
                    the same <code>.row</code> with the task list taking up a
                    <code>.col-md-3</code> and the accordion panels in a <code>.col-md-9</code>,
                    but that's not a requirement.
                  </p>
                  <p>
                    The accordion panel can be exactly as documented in the
                    <a href="http://getbootstrap.com/javascript/#collapse-examples">Bootstrap example</a>,
                    but I think there's a problem with the Bootstrap implementation.
                    Specifically, the Bootstrap example only adds the class
                    <code>.in</code> to one of the accordion panels. That class
                    marks the panel as visible by default. The problem with only
                    having one panel visible by default is that users without
                    javascript will <strong>never</strong> be able to see the other
                    panels. Sure, that's a minority of users, but why make your
                    pages unworkable even for a small minority. Instead, I suggest
                    adding <code>.in</code> to all your <code>.collapse</code>
                    elements and have javascript code select only one to make
                    visible when it runs. The accordion wizard javascript will handle
                    that for you if you choose to use that approach.
                  </p>
                  <p>
                    The sidebar task list is nothing but a standard HTML ordered
                    list. The only required additions are adding the
                    <code>.acc-wizard-sidebar</code> class to the <code>&lt;ol&gt;</code>
                    element and <code>.acc-wizard-todo</code> to the individual list
                    items. If you want to indicate that some steps are already
                    complete, you can instead add the <code>.acc-wizard-completed</code>
                    class to the corresponding <code>&lt;li&gt;</code> elements.
                  </p>
                  <pre><!--
                  -->&lt;ol class="acc-wizard-sidebar"&gt;
<!--              -->  &lt;li class="acc-wizard-todo"&gt;&lt;a href="#prerequisites"&gt;Install Bootstrap and jQuery&lt;/a&gt;&lt;/li&gt;
<!--              -->  &lt;li class="acc-wizard-todo"&gt;&lt;a href="#addwizard"&gt;Add Accordion Wizard&lt;/a&gt;&lt;/li&gt;
<!--              -->  &lt;li class="acc-wizard-todo"&gt;&lt;a href="#adjusthtml"&gt;Adjust Your HTML Markup&lt;/a&gt;&lt;/li&gt;
<!--              -->  &lt;li class="acc-wizard-todo"&gt;&lt;a href="#viewpage"&gt;Test Your Page&lt;/a&gt;&lt;/li&gt;
<!--              -->&lt;/ol&gt;<!--
                  --></pre>
                  <p>
                    Finally, you'll want to active the wizard in your javascript.
                    That's nothing more than simply calling the plugin on an
                    appropriate selection.
                  </p>
                  <pre><!--
                  -->&lt;script&gt;
<!--              -->    $(window).load(function() {
<!--              -->        $(".acc-wizard").accwizard();
<!--              -->    });
<!--              -->&lt;/script&gt;<!--
                 --></pre>
                  <p>
                    The default options are probably fine for most uses, but
                    there are many customizations you can use when you activate
                    the wizard. Check out the documentation on
                    <a href="https://github.com/sathomas/acc-wizard">github</a>
                    for the details.
                  </p>
                <div class="acc-wizard-step"><button class="btn" type="reset">Go Back</button> <button class="btn btn-primary" type="submit">Next Step</button></div></form>
              </div> <!--/.panel-body -->
            </div> <!-- /#adjusthtml -->
          </div> <!-- /.panel.panel-default -->

          <div class="panel panel-default">
            <div class="panel-heading">
              <h4 class="panel-title">
                <a href="#viewpage" data-parent="#accordion-demo" data-toggle="collapse" class="collapsed">
                  Test Your Page
                </a>
              </h4>
            </div>
            <div id="viewpage" class="panel-collapse collapse" style="height: 36.4px;">
              <div class="panel-body">
                <form id="form-viewpage">
                  <p>
                    Naturally, the last thing you'll want to do is test your
                    page with the accordion wizard. Once you've confirmed that
                    it's working as expected, release it on the world. Your
                    users will definitely appreciate the feedback and guidance
                    it gives to multi-step and complex tasks on your web site.
                  </p>
                <div class="acc-wizard-step"><button class="btn" type="reset">Go Back</button></div></form>
              </div> <!--/.panel-body -->
            </div> <!-- /#adjusthtml -->
          </div> <!-- /.panel.panel-default -->

        </div>
      </div>
    </div>
	<script>
	      function onNext(parent, panel) {
	        hash = "#" + panel.id;
	        $(".acc-wizard-sidebar",$(parent))
	            .children("li")
	            .children("a[href='" + hash + "']")
	            .parent("li")
	            .removeClass("acc-wizard-todo")
	            .addClass("acc-wizard-completed");
	      }
	      $(window).load(function() {
	        $(".acc-wizard").accwizard({onNext: onNext});
	      })
	    </script>

</div>
<script src="<?php echo PATTERNS::getVD();?>include/_core/js/acc-wizard.min.js"></script>
<script src="<?php echo PATTERNS::getVD();?>dashboard/js/cadastroAcordo.js<?php echo "?".microtime();?>"></script>
