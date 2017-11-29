<?php
class PATTERNS {

    private $virtualDir;
    private $clubeDS;

    function __construct() {

        //VIRTUALDIR
        $this->virtualDir = "/desbravadores/";

        //DESCRICAO DO CLUBE
        $this->$clubeDS = "Clube de Desbravadores Pioneiros - IASD Capão Redondo - 6ª Região - APS - UCB - DSA";

        /*
        array(
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
        */

    }

    //RETORNA VIRTUAL DIR
    public function getVD(){
        return $this->virtualDir;
    }

    //RETORNA DESCRICAO DO CLUBE
    public function getCDS(){
        return $this->clubeDS;
    }
    
}
$GLOBALS['pattern'] = new PATTERNS();

$dirImgCliente = "";
$dirImgGenerico = "img/";

$dirImagens = $GLOBALS['pattern']->getVD() . $dirImgGenerico . $dirImgCliente;
$dirImgAppl = $dirImgGenerico . $dirImgCliente;
?>