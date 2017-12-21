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
	$strFilter .= "<div class=\"input-group col-xs-4 pull-right\">";
	$strFilter .= "<select class=\"selectpicker form-control input-sm\" id=\"addFilter\" onchange=\"jsFilter.addFilter(this);\" data-width=\"100%\" title=\"Adicionar filtros\" data-width=\"auto\" data-container=\"body\">";
	$arr = array_msort( $pFilters, array('label' => SORT_ASC) );
	foreach ($arr as $key => $value):
		$strFilter .= "<option value=\"".$value["id"]."\"";
		if ( isset($value["icon"]) ):
			$strFilter .= " data-icon=\"".$value["icon"]."\"";
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
			array( "id" => "F", "ds" => "FEMININO" ),
			array( "id" => "M", "ds" => "MASCULINO" )
		);
	
	//SITUACAO
	elseif ( $type == "S" ):
		$domain = array(
			array( "id" => "A", "ds" => "ATIVOS" ),
			array( "id" => "I", "ds" => "INATIVOS" ),
			array( "id" => "T", "ds" => "TODOS" )
		);
		
	//GRUPO
	elseif ( $type == "G" ):
		$domain = array(
			array( "id" => "1", "ds" => "DESBRAVADORES" ),
			array( "id" => "2", "ds" => "DIRETORIA GERAL" ),
			array( "id" => "3", "ds" => "FANFARRA" ),
			array( "id" => "4", "ds" => "INSTRUTORES" ),
			array( "id" => "5", "ds" => "CONSELHEIROS" ),
			array( "id" => "6", "ds" => "CAPITÃES" )
		);
		
	//APRENDIZADO
	elseif ( $type == "HA" ):
		$year = date("Y");
		$domain = array(
			array( "id" => "0", "ds" => "EM ANDAMENTO" ),
			array( "id" => "1", "ds" => "PENDENTES DE AVALIA&Ccedil;&Atilde;O" ),
			array( "id" => "2", "ds" => "AVALIADOS EM $year" ),
			array( "id" => "3", "ds" => "PENDENTES DE INVESTIDURA" )
		);
		
	//PENDENCIAS CADASTRAIS
	elseif ( $type == "PC" ):
		$domain = array(
			array( "id" => "NC5", "ds" => "NOME COMPLETO INV&Aacute;LIDO" ),
			array( "id" => "SEX", "ds" => "SEXO INV&Aacute;LIDO" ),
			array( "id" => "DTN", "ds" => "DATA DE NASCIMENTO INV&Aacute;LIDA" ),
			array( "id" => "DOC", "ds" => "DOCUMENTO INV&Aacute;LIDO" ),
			array( "id" => "CPF", "ds" => "CPF INV&Aacute;LIDO" ),
			array( "id" => "LOG", "ds" => "LOGRADOURO INV&Aacute;LIDO" ),
			array( "id" => "NLG", "ds" => "NUMERO LOGRADOURO INV&Aacute;LIDO" ),
			array( "id" => "BAI", "ds" => "BAIRRO INV&Aacute;LIDO" ),
			array( "id" => "CID", "ds" => "CIDADE INV&Aacute;LIDA" ),
			array( "id" => "EST", "ds" => "UF INV&Aacute;LIDA" ),
			array( "id" => "CEP", "ds" => "CEP INV&Aacute;LIDO" ),
			array( "id" => "TEL", "ds" => "TELEFONES INV&Aacute;LIDOS" ),
			array( "id" => "UNI", "ds" => "UNIDADE INV&Aacute;LIDA" ),
			array( "id" => "CAR", "ds" => "CARGO/FUN&Ccedil;&Atilde;O INV&Aacute;LIDA" )
		);

		
	//ITENS COMPRADOS
	elseif ( $type == "IC" ):
		$domain = array(
			array( "id" => "0", "ds" => "ITENS COMPRADOS" ),
			array( "id" => "1", "ds" => "ITENS A COMPRAR" ),
			array( "id" => "4", "ds" => "ITENS PREVISTOS" ),
			array( "id" => "2", "ds" => "ITENS ENTREGUES" ),
			array( "id" => "3", "ds" => "ITENS A ENTREGAR" )
		);
		
	//MES ANIVERSARIO
	elseif ( $type == "MA" ):
		$domain = getMesAniversario();

	//HISTORICO
	elseif ( $type == "HH" ):
		$year = date("Y");
		$domain = array(
			array( "id" => "4", "ds" => "INVESTIDOS EM $year" ),
			array( "id" => "5", "ds" => "INVESTIDOS ANTES DE $year" )
		);
		
	//TIPO DE MATERIAIS
	elseif ( $type == "HT" ):
		$domain = getTipoMateriais();
		
	//BATIZADO
	elseif ( $type == "B" ):
		$domain = array(
			array( "id" => "S", "ds" => "SIM" ),
			array( "id" => "N", "ds" => "N&Atilde;O" )
		);
		$y = 0;
		for ($i=0;$i<=2;$i++):
			$y = date("Y") - $i;
			$domain[] = array( "id" => $y, "ds" => "EM ". $y);
		endfor;
		$domain[] = array( "id" => "A". $y, "ds" => "ANTES DE ". $y);
		
	//TIPO DE OCORRENCIA
	elseif ( $type == "TO" ):
		$domain = array(
			array( "id" => "P", "ds" => "POSITIVA" ),
			array( "id" => "N", "ds" => "NEGATIVA" )
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
		$str .= ">".$k["ds"]."</option>";
	endforeach;
	$str .= "</select></div>";
	return array( "result" => true, "obj" => $str );
}
?>