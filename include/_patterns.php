<?php
@include_once("_bars.php");

class PATTERNS {

    private $virtualDir;
    private $bars;

    function __construct() {

        //VIRTUALDIR
        $this->virtualDir = "/desbravadores/";

        //DEFINICOES DO BARCODE
        $this->bars = new BARS();
    }

    public function getClubeDS( $p ){
        $str  = isset($p["cl"]) ? "Clube " : "";
        $str .= isset($p["cj"]) && isset($p["db"]) ? "de " : "";
        $str .= isset($p["db"]) ? "Desbravadores " : "";
        $str .= isset($p["nm"]) ? "Pioneiros " : "";
        $str .= isset($p["sp"]) && isset($p["af"]) ? "- " : "";
        $str .= isset($p["af"]) ? "1959 " : "";
        $str .= isset($p["sp"]) && isset($p["ig"]) ? "- " : "";
        $str .= isset($p["ig"]) ? "IASD Capão Redondo " : "";
        $str .= isset($p["sp"]) && isset($p["rg"]) ? "- " : "";
        $str .= isset($p["rg"]) ? "6ª Região " : "";
        $str .= isset($p["sp"]) && isset($p["as"]) ? "- " : "";
        $str .= isset($p["as"]) ? "APS " : "";
        $str .= isset($p["sp"]) && isset($p["un"]) ? "- " : "";
        $str .= isset($p["un"]) ? "UCB " : "";
        $str .= isset($p["sp"]) && isset($p["dv"]) ? "- " : "";
        $str .= isset($p["dv"]) ? "DSA" : "";
        return trim($str);
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
        return $this->getClubeDS( 
            array( 
                "cl" => true,
                "cj" => true,
                "db" => true,
                "nm" => true,
                "sp" => true,
                "ig" => true,
                "rg" => true,
                "as" => true,
                "un" => true,
                "dv" => true
            )
        );
    }
    
}
$GLOBALS['pattern'] = new PATTERNS();
?>