<?php
@require_once("_virtualpath.php");

$dirImgCliente = "";
$dirImgGenerico = "img/";

$dirImagens = $VirtualDir . $dirImgGenerico . $dirImgCliente;
$dirImgAppl = $dirImgGenerico . $dirImgCliente;

$pattern = array(
	"clubeDS" => "Clube de Desbravadores Pioneiros - IASD Capão Redondo - 6ª Região - APS - UCB - DSA",
	"virtualDir" => "/desbravadores/",
	"bars" => array(
		"CI" => "P",
		"FN" => array(
			"0" => "SIMPLES",
			"A" => "CARTÃO DE CLASSE",
			"B" => "CADERNO DE CLASSE",
			"C" => "PASTA DE CLASSE",
			"D" => "AUTORIZAÇÃO COMUM",
			"E" => "CADERNO DE ESPECIALIDADE",
			"F" => "AUTORIZAÇÃO ESPECIAL"
		),
		"FI" => 2,
		"NI" => 3
	)
);
?>