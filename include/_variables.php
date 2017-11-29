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
		"CI" => array( "size" => 1, "fixed" => "P" ),
		"FN" => array( 
            "size" => 1, 
            "variables" => array(
                "0" => "SIMPLES",
                "A" => "CARTÃO DE CLASSE",
                "B" => "CADERNO DE CLASSE",
                "C" => "PASTA DE CLASSE",
                "D" => "AUTORIZAÇÃO COMUM",
                "E" => "CADERNO DE ESPECIALIDADE",
                "F" => "AUTORIZAÇÃO ESPECIAL"
            )
		),
		"FI" => array( "size" => 2 ),
		"NI" => array( "size" => 3 )
	)
);
?>