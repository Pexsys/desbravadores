<?php
function fMontaMenu( $perfil ) {
	$retorno = array();
	foreach ( $perfil as $key => $value ):
		$opt = $value["opt"];
		$ico = $value["ico"];
		$url = $value["url"];
		
		$urlEmpty = empty($url);
		$class = "";
		if ( $value["active"] == "S" ):
			$class = " class=\"". ( $urlEmpty ? "open " : "" ) ."active\"";
			if (!$urlEmpty):
				$retorno =  array( "opt" => $opt, "url" => $url );
			endif;
		endif;
		echo "<li$class>";
		if ( !$urlEmpty ):
			echo "<a href=\"".$GLOBALS['pattern']->getVD()."dashboard/index.php?id=$key\"$class>";
		else:
			echo "<a href=\"#\">";
		endif;
		if ( !empty($ico) ):
			echo "<i class=\"$ico\"></i> ";
		endif;
		echo "$opt";
		if ( count($value["child"]) > 0 ):
			echo "<span class=\"fa arrow\"></span>";
		endif;
		echo "</a>";
		if ( count($value["child"]) > 0 ):
			echo "<ul class=\"nav\" style=\"padding-left:20px;\">";
			$ax = fMontaMenu( $value["child"] );
			if ( count($retorno) == 0 ):
				$retorno = $ax;
			endif;
			echo "</ul>";
		endif;
		echo "</li>";
	endforeach;
	return $retorno;
}

function fSetActive( $perfil, $id = NULL ) {
	foreach ( $perfil as $key => $value ):
		if ( count( $value["child"] ) == 0 && ( is_null($id) || empty($id) || $key == $id ) ):
			$perfil[$key]["active"] = "S";
			return $perfil;
		elseif ( count( $value["child"] ) > 0 ):
			$active = false;
			$aux = fSetActive( $value["child"], $id );
			foreach ( $aux as $k => $v ):
				if ( $aux[$k]["active"] == "S" ):
					$perfil[$key]["active"] = "S";
					$active = true;
					break;
				endif;
			endforeach;
			if ($active):
			    $perfil[$key]["child"] = $aux;
				return $perfil;
		    endif;
		endif;
	endforeach;
	return $perfil;
}

$perfil = fSetActive( $profile->fGetPerfil(), fRequest("id") );
?>
<div class="navbar-default sidebar" role="navigation">
	<div class="sidebar-nav navbar-collapse">
		<ul class="nav" id="side-menu">
			<li class="sidebar-search">
				<div class="input-group custom-search-form">
					<input type="text" class="form-control" placeholder="Pesquisar...">
					<span class="input-group-btn">
					<button class="btn btn-default" type="button">
						<i class="fa fa-search"></i>
					</button>
				</span>
				</div>
			</li>
			<?php $activeOpt = fMontaMenu( $perfil );?>
		</ul>
	</div>
</div>