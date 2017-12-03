<?php
@include_once("_bars.php");

class PATTERNS {

    private $virtualDir;
    private $clubeDS;
    private $bars;

    function __construct() {

        //VIRTUALDIR
        $this->virtualDir = "/desbravadores/";

        //DESCRICAO DO CLUBE
        $this->clubeDS = "Clube de Desbravadores Pioneiros - IASD Capão Redondo - 6ª Região - APS - UCB - DSA";

        //DEFINICOES DO BARCODE
        $this->bars = new BARS();
    }

    public function getBars(){
        return $this->bars;
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