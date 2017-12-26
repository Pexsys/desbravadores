<?php
@include_once("_bars.php");

class PATTERNS {

    private $virtualDir;
    private $bars;
    private $email;

    function __construct() {

        //VIRTUALDIR
        $this->virtualDir = "/desbravadores/";

        //EMAIL
        $this->email = "desbravadores@iasd-capaoredondo.com.br";
        
        //DEFINICOES DO BARCODE
        $this->bars = new BARS();
    }

    public function getClubeDS($p){
        $str  = in_array("cl",$p) ? "Clube " : "";
        $str .= in_array("cj",$p) && in_array("db",$p) && !empty($str) ? "de " : "";
        $str .= in_array("db",$p) ? "Desbravadores " : "";
        $str .= in_array("nm",$p) ? "Pioneiros " : "";
        $str .= in_array("sp",$p) && in_array("af",$p) && !empty($str) ? "- " : "";
        $str .= in_array("af",$p) ? "1959 " : "";
        $str .= in_array("sp",$p) && in_array("ig",$p) && !empty($str) ? "- " : "";
        $str .= in_array("ig",$p) ? "IASD Capão Redondo " : "";
        $str .= in_array("sp",$p) && in_array("rg",$p) && !empty($str) ? "- " : "";
        $str .= in_array("rg",$p) ? "6ª Região " : "";
        $str .= in_array("sp",$p) && in_array("as",$p) && !empty($str) ? "- " : "";
        $str .= in_array("as",$p) ? "APS " : "";
        $str .= in_array("sp",$p) && in_array("un",$p) && !empty($str) ? "- " : "";
        $str .= in_array("un",$p) ? "UCB " : "";
        $str .= in_array("sp",$p) && in_array("dv",$p) && !empty($str) ? "- " : "";
        $str .= in_array("dv",$p) ? "DSA" : "";
        return trim($str);
    }

    //RETORNA DESCRICAO DO CLUBE
    public function getCDS(){
        return $this->getClubeDS( array( "cl", "cj", "db", "nm", "sp", "ig", "rg", "as", "un", "dv" ) );
    }

    public function getMail(){
        return $this->email;
    }

    public function getBars(){
        return $this->bars;
    }

    //RETORNA VIRTUAL DIR
    public function getVD(){
        return $this->virtualDir;
    }
    
}
$GLOBALS['pattern'] = new PATTERNS();
?>