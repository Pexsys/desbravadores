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
			$key = array_search($k, array_column($pFilters, "value"));
			if ($key !== false):
				$temFiltro = true;
				$arrFilter = addFilter( array( "type" => $pFilters[$key]["value"], "desc" => $pFilters[$key]["label"], "opsl" => $aOpSel ) );
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
		$strFilter .= "<option value=\"".$value["value"]."\">";
		//if ( isset($value["icon"]) ):
		//	$strFilter .= " <i class=\"".$value["icon"]."\"></i>&nbsp;";
		//endif;
		$strFilter .= $value["label"]."</option>";
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
			array( "value" => "F", "label" => "FEMININO" ),
			array( "value" => "M", "label" => "MASCULINO" )
		);
	
	//SITUACAO
	elseif ( $type == "S" ):
		$domain = array(
			array( "value" => "A", "label" => "ATIVOS" ),
			array( "value" => "I", "label" => "INATIVOS" ),
			array( "value" => "T", "label" => "TODOS" )
		);
		
	//GRUPO
	elseif ( $type == "G" ):
		$domain = array(
			array( "value" => "1", "label" => "DESBRAVADORES" ),
			array( "value" => "2", "label" => "DIRETORIA GERAL" ),
			array( "value" => "3", "label" => "FANFARRA" ),
			array( "value" => "4", "label" => "INSTRUTORES" ),
			array( "value" => "5", "label" => "CONSELHEIROS" ),
			array( "value" => "6", "label" => "CAPITÃES" )
		);
		
	//APRENDIZADO
	elseif ( $type == "HA" ):
		$year = date("Y");
		$domain = array(
			array( "value" => "0", "label" => "EM ANDAMENTO" ),
			array( "value" => "1", "label" => "PENDENTES DE AVALIA&Ccedil;&Atilde;O" ),
			array( "value" => "2", "label" => "AVALIADOS EM $year" ),
			array( "value" => "3", "label" => "PENDENTES DE INVESTIDURA" )
		);
		
	//PENDENCIAS CADASTRAIS
	elseif ( $type == "PC" ):
		$domain = array(
			array( "value" => "NC5", "label" => "NOME COMPLETO INV&Aacute;LIDO" ),
			array( "value" => "SEX", "label" => "SEXO INV&Aacute;LIDO" ),
			array( "value" => "DTN", "label" => "DATA DE NASCIMENTO INV&Aacute;LIDA" ),
			array( "value" => "DOC", "label" => "DOCUMENTO INV&Aacute;LIDO" ),
			array( "value" => "CPF", "label" => "CPF INV&Aacute;LIDO" ),
			array( "value" => "LOG", "label" => "LOGRADOURO INV&Aacute;LIDO" ),
			array( "value" => "NLG", "label" => "NUMERO LOGRADOURO INV&Aacute;LIDO" ),
			array( "value" => "BAI", "label" => "BAIRRO INV&Aacute;LIDO" ),
			array( "value" => "CID", "label" => "CIDADE INV&Aacute;LIDA" ),
			array( "value" => "EST", "label" => "UF INV&Aacute;LIDA" ),
			array( "value" => "CEP", "label" => "CEP INV&Aacute;LIDO" ),
			array( "value" => "TEL", "label" => "TELEFONES INV&Aacute;LIDOS" ),
			array( "value" => "UNI", "label" => "UNIDADE INV&Aacute;LIDA" ),
			array( "value" => "CAR", "label" => "CARGO/FUN&Ccedil;&Atilde;O INV&Aacute;LIDA" )
		);

		
	//ITENS COMPRADOS
	elseif ( $type == "IC" ):
		$domain = array(
			array( "value" => "0", "label" => "ITENS COMPRADOS" ),
			array( "value" => "1", "label" => "ITENS A COMPRAR" ),
			array( "value" => "2", "label" => "ITENS ENTREGUES" ),
			array( "value" => "3", "label" => "ITENS A ENTREGAR" )
		);
		
	//MES ANIVERSARIO
	elseif ( $type == "MA" ):
		$domain = getMesAniversario();

		//HISTORICO
	elseif ( $type == "HH" ):
		$year = date("Y");
		$domain = array(
			array( "value" => "4", "label" => "INVESTIDOS EM $year" ),
			array( "value" => "5", "label" => "INVESTIDOS ANTES DE $year" )
		);
		
	//BATIZADO
	elseif ( $type == "B" ):
		$domain = array(
			array( "value" => "S", "label" => "SIM" ),
			array( "value" => "N", "label" => "N&Atilde;O" )
		);
		$y = 0;
		for ($i=0;$i<=2;$i++):
			$y = date("Y") - $i;
			$domain[] = array( "value" => $y, "label" => "EM ". $y);
		endfor;
		$domain[] = array( "value" => "A". $y, "label" => "ANTES DE ". $y);
		
		
	//TIPO DE OCORRENCIA
	elseif ( $type == "TO" ):
		$domain = array(
			array( "value" => "P", "label" => "POSITIVA" ),
			array( "value" => "N", "label" => "NEGATIVA" )
		);
		
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
	$opSel = $parameters["opsl"];
	if (is_array($opSel)):
		foreach ( $filter["domain"] as $j => $k ):
			if ( $opSel[0] == "ALL" || in_array( $k["value"], $opSel, true ) ):
				$filter["domain"][$j]["selected"] = "S";
			endif;
		endforeach;
	endif;
	
	$str  = "<div class=\"input-group input-group-sm col-xs-12 col-md-12 col-sm-12 col-lg-12\" id=\"divFilter$value\" style=\"padding-bottom:10px\">";
	$str .= "<label for=\"optFilter$value\" class=\"pull-left\">$label:&nbsp;</label>";
	$str .= "<span class=\"label label-danger pull-right\" style=\"cursor:pointer\" onclick=\"jsFilter.removeFilter(this);\" filter-value=\"$value\" filter-label=\"$label\"><i class=\"glyphicon glyphicon-remove\"></i>&nbsp;Remover</span>";
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
		$str .= "<option value=\"".$k["value"]."\"";
		if ($k["selected"] == "S"):
			$str .= " selected";
		endif;
		$str .= ">".$k["label"]."</option>";
	endforeach;
	$str .= "</select></div>";
	return array( "result" => true, "obj" => $str );
}
?>