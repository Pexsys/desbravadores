<?php
function fDataFilters( $parameters ){
	$temFiltro = false;

	$pFilters = $parameters["filters"];
	
	$strFilter  = "<div class=\"col-xs-8\" id=\"divFilters\" filter-to=\"".$parameters["filterTo"]."\">";
	
	$flt = fRequest("flt");
	if (isset($flt) && !empty($flt)):
		$aParam = explode(",",$flt);
		$aOpSel = explode(",",fRequest("op"));
		foreach ($aParam as $k):
			$key = array_search($k, array_column($pFilters, "id"));
			if ($key !== false):
				$temFiltro = true;
				$arrFilter = addFilter( array( "type" => $pFilters[$key]["id"], "desc" => $pFilters[$key]["ds"], "opsl" => $aOpSel ) );
				$strFilter .= $arrFilter["obj"];
				unset($pFilters[$key]);
			endif;
		endforeach;
	endif;
	$strFilter .= "</div>";
	$arr = array_msort( $pFilters, array("ds" => SORT_LOCALE_STRING) );
	$strFilter .= "<div class=\"input-group col-xs-4 pull-right\">";
	$strFilter .= "<select class=\"selectpicker form-control input-sm\" id=\"addFilter\" onchange=\"jsFilter.addFilter(this);\" data-width=\"100%\" title=\"Adicionar filtros\" data-width=\"auto\" data-container=\"body\">";
	foreach ($arr as $key => $value):
		$strFilter .= "<option value=\"".$value["id"]."\"";
		if ( isset($value["icon"]) ):
			$strFilter .= " data-icon=\"".$value["icon"];
			$strFilter .= (isset($value["icon-color"]) ? " ".$value["icon-color"] : " text-muted" );
			$strFilter .= "\"";
		endif;
		$strFilter .= ">".$value["ds"]."</option>";
	endforeach;
	$strFilter .= "</select>";
	$strFilter .= "</div>";
	$strFilter .= "<div class=\"form-group col-xs-12\"><a role=\"button\" class=\"btn btn-info btn-sm\" id=\"applyFilter\" style=\"color:#ffffff".($temFiltro?"":";display:none")."\" onclick=\"jsFilter.apply();\"><i class=\"glyphicon glyphicon-cog\"></i>&nbsp;Aplicar Filtro</a></div>";
	$strFilter .= "<br/>";
	echo $strFilter;
}

function getDomainFilter( $parameters ) {
	$domain = array();

	$type = $parameters["type"];
	
	//SEXO
	if ( $type == "X" ):
		$domain = array(
			array( "id" => "F", "ds" => "FEMININO", "icon" => "fa fa-venus" ),
			array( "id" => "M", "ds" => "MASCULINO", "icon" => "fa fa-mars" )
		);
	
	//SITUACAO
	elseif ( $type == "S" ):
		$domain = array(
			array( "id" => "A", "ds" => "ATIVOS", "icon" => "fa fa-toggle-on" ),
			array( "id" => "I", "ds" => "INATIVOS", "icon" => "fa fa-toggle-off" ),
			array( "id" => "T", "ds" => "TODOS", "icon" => "fa-globe" )
		);
		
	//GRUPO
	elseif ( $type == "G" ):
		$domain = array(
			array( "id" => "1", "ds" => "DESBRAVADORES", "icon" => "fa fa-child" ),
			array( "id" => "2", "ds" => "DIRETORIA GERAL", "icon" => "fa fa-user-secret" ),
			array( "id" => "3", "ds" => "FANFARRA", "icon" => "fa fa-music" ),
			array( "id" => "4", "ds" => "INSTRUTORES", "icon" => "fa fa-graduation-cap" ),
			array( "id" => "5", "ds" => "CONSELHEIROS", "icon" => "fa fa-heart" ),
			array( "id" => "6", "ds" => "CAPITÃES", "icon" => "fa fa-first-order" )
		);
		
	//APRENDIZADO
	elseif ( $type == "HA" ):
		$year = date("Y");
		$domain = array(
			array( "id" => "0", "ds" => "EM ANDAMENTO", "icon" => "fa fa-battery-half" ),
			array( "id" => "1", "ds" => "PENDENTES DE AVALIAÇÃO", "icon" => "fa fa-battery-full" ),
			array( "id" => "2", "ds" => "AVALIADOS EM $year", "icon" => "fa fa-eye" ),
			array( "id" => "3", "ds" => "PENDENTES DE INVESTIDURA", "icon" => "fa fa-graduation-cap" )
		);
		
	//PENDENCIAS CADASTRAIS
	elseif ( $type == "PC" ):
		$domain = array(
			array( "id" => "NC5", "ds" => "NOME COMPLETO INV&Aacute;LIDO", "icon" => "fa fa-address-book" ),
			array( "id" => "SEX", "ds" => "SEXO INVÁLIDO", "icon" => "fa fa-venus-mars" ),
			array( "id" => "DTN", "ds" => "DATA DE NASCIMENTO INVÁLIDA", "icon" => "fa fa-calendar" ),
			array( "id" => "DOC", "ds" => "DOCUMENTO INVÁLIDO", "icon" => "fa fa-id-card-o" ),
			array( "id" => "CPF", "ds" => "CPF INVÁLIDO", "icon" => "fa fa-id-card" ),
			array( "id" => "LOG", "ds" => "LOGRADOURO INVÁLIDO", "icon" => "fa fa-road" ),
			array( "id" => "NLG", "ds" => "NUMERO LOGRADOURO INVÁLIDO", "icon" => "fa fa-map-marker" ),
			array( "id" => "BAI", "ds" => "BAIRRO INVÁLIDO", "icon" => "fa fa-location-arrow" ),
			array( "id" => "CID", "ds" => "CIDADE INVÁLIDA", "icon" => "fa fa-street-view" ),
			array( "id" => "EST", "ds" => "UF INVÁLIDA", "icon" => "fa fa-map" ),
			array( "id" => "CEP", "ds" => "CEP INVÁLIDO", "icon" => "fa fa-map-signs" ),
			array( "id" => "TEL", "ds" => "TELEFONES INVÁLIDOS", "icon" => "fa fa-phone" ),
			array( "id" => "UNI", "ds" => "UNIDADE INVÁLIDA", "icon" => "fa fa-universal-access" ),
			array( "id" => "CAR", "ds" => "CARGO/FUNÇÃO INVÁLIDA", "icon" => "fa fa-user-md" )
		);
		
	//ITENS COMPRADOS
	elseif ( $type == "IC" ):
		$domain = array(
			array( "id" => "0", "ds" => "ITENS COMPRADOS", "icon" => "fa fa-shopping-bag" ),
			array( "id" => "1", "ds" => "ITENS A COMPRAR", "icon" => "fa fa-shopping-basket" ),
			array( "id" => "4", "ds" => "ITENS PREVISTOS", "icon" => "fa fa-cart-plus" ),
			array( "id" => "2", "ds" => "ITENS ENTREGUES", "icon" => "fa fa-cart-arrow-down" ),
			array( "id" => "3", "ds" => "ITENS A ENTREGAR", "icon" => "fa fa-truck" )
		);
		
	//MES ANIVERSARIO
	elseif ( $type == "MA" ):
		$domain = getMesAniversario();

	//HISTORICO
	elseif ( $type == "HH" ):
		$year = date("Y");
		$domain = array(
			array( "id" => "4", "ds" => "INVESTIDOS EM $year", "icon" => "fa fa-trophy" ),
			array( "id" => "5", "ds" => "INVESTIDOS ANTES DE $year", "icon" => "fa fa-shield" )
		);
		
	//TIPO DE MATERIAIS
	elseif ( $type == "HT" ):
		$domain = getTipoMateriais();
		
	//BATIZADO
	elseif ( $type == "B" ):
		$y = date("Y");
		$domain = array(
			array( "id" => "S", "ds" => "SIM", "icon" => "fa fa-thumbs-o-up" ),
			array( "id" => "N", "ds" => "NÃO", "icon" => "fa fa-thumbs-o-down" ),
			array( "id" => $y, "ds" => "EM ". $y, "icon" => "fa fa-pause" ),
			array( "id" => ($y-1), "ds" => "EM ". ($y-1), "icon" => "fa fa-play" ),
			array( "id" => ($y-2), "ds" => "EM ". ($y-2), "icon" => "fa fa-step-backward" ),
			array( "id" => ($y-3), "ds" => "EM ". ($y-3), "icon" => "fa fa-backward" ),
			array( "id" => "A". ($y-3), "ds" => "ANTES DE ". ($y-3), "icon" => "fa fa-fast-backward" )
		);
		
	//TIPO DE OCORRENCIA
	elseif ( $type == "TO" ):
		$domain = array(
			array( "id" => "P", "ds" => "POSITIVA", "icon" => "fa fa-thumbs-o-up" ),
			array( "id" => "N", "ds" => "NEGATIVA", "icon" => "fa fa-thumbs-o-down" )
		);
		
	//UNIDADE
	elseif ( $type == "EV" ):
		$domain = getDomainEventos();
		
	//UNIDADE
	elseif ( $type == "U" ):
		$domain = getDomainUnidades();
	
	//TIPO APRENDIZADO
	elseif ( $type == "Z" ):
		$domain = getTipoAprendizado();
		
	//CLASSE
	elseif ( $type == "C" ):
		$domain = getDomainClasses();

	//MESTRADOS
	elseif ( $type == "M" ):
		$domain = getDomainMestrados();

	//ESPECIALIDADES
	elseif ( $type == "E" ):
		$domain = getDomainEspecialidades();

	//MEMBROS ATIVOS
	elseif ( $type == "T" ):
		$domain = getDomainMembrosAtivos();

	//MEMBROS INATIVOS
	elseif ( $type == "I" ):
		$domain = getDomainMembrosInativos();

	//AREAS
	elseif ( $type == "A" ):
		$domain = getDomainAreasEspecialidades();

	endif;

	return array( "result" => true, "domain" => $domain );
}

function addFilter( $parameters ){
	$filter = getDomainFilter( $parameters );
	
	$value = $parameters["type"];
	$label = $parameters["desc"];
	$icon = (isset($parameters["icon"]) ? $parameters["icon"] : "");
	$opSel = $parameters["opsl"];
	if (is_array($opSel)):
		foreach ( $filter["domain"] as $j => $k ):
			if ( $opSel[0] == "ALL" || in_array( $k["id"], $opSel, true ) ):
				$filter["domain"][$j]["selected"] = "S";
			endif;
		endforeach;
	endif;
	
	$str  = "<div class=\"input-group input-group-sm col-xs-12 col-md-12 col-sm-12 col-lg-12\" id=\"divFilter$value\" style=\"padding-bottom:10px\">";
	$str .= (!empty($icon) ? "<i class=\"pull-left $icon\"></i>&nbsp;" : "" ) ."<label for=\"optFilter$value\" class=\"pull-left\">$label:&nbsp;</label>";
	$str .= "<span class=\"label label-danger pull-right\" style=\"cursor:pointer\" onclick=\"jsFilter.removeFilter(this);\" filter-value=\"$value\" filter-label=\"$label\" filter-icon=\"$icon\"><i class=\"glyphicon glyphicon-remove\"></i>&nbsp;Remover</span>";
	if ( count($filter["domain"]) > 5):
		$str .= "<span class=\"pull-right\"><input type=\"checkbox\" id=\"notFilter$value\">&nbsp;<label for=\"notFilter$value\">N&atilde;o</label>&nbsp;</span>";
	endif;
	$str .= "<select class=\"selectpicker form-control input-sm\" id=\"optFilter$value\" filter-field=\"$value\" multiple data-selected-text-format=\"count > 3\" title=\"Escolha uma ou mais op&ccedil;&otilde;es\" data-width=\"100%\" data-container=\"body\"";
	if ( count($filter["domain"]) > 8):
		$str .= " data-live-search=\"true\"";
		$str .= " data-actions-box=\"true\"";
	endif;
	$str .= ">";
	foreach ( $filter["domain"] as $k ):
		$str .= "<option value=\"".$k["id"]."\"";
		if ($k["selected"] == "S"):
			$str .= " selected";
		endif;
		if (isset($k["sb"])):
			$str .= " data-subtext=\"".$k["sb"]."\"";
		endif;
		if (isset($k["icon"])):
			$str .= " data-icon=\"".$k["icon"];
			$str .= (isset($k["icon-color"]) ? " ".$k["icon-color"] : " text-muted" );
			$str .= "\"";
		endif;
		$str .= ">".$k["ds"]."</option>";
	endforeach;
	$str .= "</select></div>";
	return array( "result" => true, "obj" => $str );
}
?>