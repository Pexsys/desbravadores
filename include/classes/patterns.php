<?php
class BARS {

	private $bars;

	function __construct() {
		$this->bars = array(
			//CLUBE ID
			"CI" => 1,

			//CLUBE PREFIX
			"CP" => "P",

			//FUNCTION ID
			"ID" => array(
				"length" => 1,
				"types" => array(
					//ID ETIQUETA			//FUNCTION          //IMPR.ETIQ     //OBR.CLASSE    //MODELO FORM   //DESCRICAO
					array(  "id" => "0",	"fn" => "BS_NM",    "tg" => "S",    "cl" => "N",    "md" => "1",    "ds" => "0-BÁSICA/NOME" ),
					array(  "id" => "1",	"fn" => "AV_CL",    "tg" => "S",    "cl" => "S",    "md" => "3",    "ds" => "1-CAPA DA PASTA DE AVALIAÇÃO" ),
					array(  "id" => "2",	"fn" => "CL_BL",    "tg" => "S",    "cl" => "S",    "md" => "3",    "ds" => "2-CAPA DE LEITURA BÍBLICA" ),
					array(  "id" => "A",	"fn" => "CT_CL",    "tg" => "S",    "cl" => "S",    "md" => "1",    "ds" => "A-CARTÃO DE CLASSE" ),
					array(  "id" => "B",	"fn" => "CD_CL",    "tg" => "S",    "cl" => "S",    "md" => "1",    "ds" => "B-CADERNO DE ATIVIDADES" ),
					array(  "id" => "C",	"fn" => "PT_CL",    "tg" => "S",    "cl" => "S",    "md" => "2",    "ds" => "C-PASTA DE CLASSE" ),
					array(  "id" => "D",	"fn" => "AT_CM",    "tg" => "N",    "cl" => "N",                    "ds" => "D-AUTORIZAÇÃO DE SAÍDA" ),
					array(  "id" => "E",	"fn" => "CT_ES",    "tg" => "S",    "cl" => "N",    "md" => "1",    "ds" => "E-CARTÃO / ESPECIALIDADES" ),
					array(  "id" => "F",	"fn" => "AT_ES",    "tg" => "N",    "cl" => "N",                    "ds" => "F-AUTORIZAÇÃO ESPECIAL" )
				)
			),

			//FUNCTION ID PARAM
			"FI" => 2,

			//PEOPLE ID - FROM PARAM
			"NI" => 3
		);
	}

	public function getPattern($ids){

		//GRUPO 1 - CI
		$pattern = "(";
		foreach (str_split($this->getClubePrefix()) as $s):
			$pattern .= "[".strtoupper($s).strtolower($s)."]{1}";
		endforeach;
		$pattern .= ")";

		//GRUPO 2 - ID
		$colchetes = array_filter( str_split($ids), function($e){
			return $this->has("id",$e);
		});
		$pattern .= "(". (strlen($ids) > 1 || count($colchetes) > 0 ? "[" : "");
		foreach (str_split($ids) as $s):
			$pattern .= (count($colchetes) > 0 ? strtoupper($s).strtolower($s) : $s);
		endforeach;
		$pattern .= (strlen($ids) > 1 || count($colchetes) > 0 ? "]" : "") . "{".$this->getIDLength()."})";

		//GRUPO 3 - FI
		$pattern .= "([a-zA-Z0-9]{". $this->getFILength()."})";

		//GRUPO 4 - NI
		$pattern .= "([a-zA-Z0-9]{". $this->getNILength()."})";

		return "^$pattern$";
	}

	public function getLength(){
		return
		strlen($this->getClubePrefix()) +
		$this->getIDLength()+
		$this->getFILength()+
		$this->getNILength()
		;
	}

	private function getNILength(){
		return $this->bars["NI"];
	}

	private function getFILength(){
		return $this->bars["FI"];
	}

	private function getIDLength(){
		return $this->bars["ID"]["length"];
	}

	public function getClubePrefix(){
		return $this->bars["CP"];
	}

	public function getClubeID(){
		return $this->bars["CI"];
	}

	public function split($s){
		$patternCode =
			"(.{".strlen($this->getClubePrefix())."})".
			"(.{".$this->getIDLength()."})".
			"(.{".$this->getFILength()."})".
			"(.{".$this->getNILength()."})"
		;

		$a = preg_split("/$patternCode/i", $s, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE );
		return array(
				"ci" => $this->getClubeID(),
				"cp" => $a[0],
				"id" => $a[1],
				"fi" => $a[2],
				"ni" => $a[3]
		);
    }

	public function decode($s){
		$a = $this->split($s);
		return array(
				"lg" => strlen($s),
				"split" => $a,
				"ci" => $a["ci"],
				"cp" => $a["cp"],
				"id" => PATTERNS::fromConvert($a["id"]),
				"fi" => PATTERNS::fromConvert($a["fi"]),
				"ni" => PATTERNS::fromConvert($a["ni"]),
		);
    }

	public function encode($a){
		$fn = "0";
		if (isset($a["id"])):
			$fn = $a["id"];
		elseif (isset($a["fn"])):
			$aux = $this->getFirstTag("fn",$a["fn"]);
			$fn = $aux["id"];
		endif;

		$fi = fStrZero(0,$this->getFILength());
		if (isset($a["fi"])):
			$fi = fStrZero(PATTERNS::toConvert($a["fi"]),$this->getFILength());
		endif;

		$ni = fStrZero(0,$this->getNILength());
		if (isset($a["ni"])):
			$ni = fStrZero(PATTERNS::toConvert($a["ni"]),$this->getNILength());
		endif;

		return mb_strtoupper($this->getClubePrefix() . $fn . $fi . $ni);
	}

	public function getAllTags(){
		return $this->bars["ID"]["types"];
	}

	public function getTagsTipo($tg,$vl){
		return array_filter( $this->getAllTags(), function($e) use($tg,$vl){
			return $e[$tg] == $vl;
		});
	}

	public function getFirstTag($tg,$vl){
		$arr = $this->getTagsTipo($tg,$vl);
		reset($arr);
		return current($arr);
	}

	public function getTagByID($id){
		return $this->getFirstTag("id",$id);
	}

	public function has($tg,$vl){
		$arr = $this->getTagsTipo($tg,$vl);
		return (count($arr) > 0);
	}
}

class PATTERNS {

    public static function getClubeDS($p){
      $str  = in_array("cl",$p) ? "Clube " : "";
      $str .= in_array("cj",$p) && in_array("db",$p) && !empty($str) ? "de " : "";
      $str .= in_array("db",$p) ? "Desbravadores " : "";
      $str .= in_array("nm",$p) ? "Pioneiros " : "";
      $str .= in_array("sp",$p) && in_array("af",$p) && !empty($str) ? "- " : "";
      $str .= in_array("af",$p) ? "1959 " : "";
      $str .= in_array("sp",$p) && in_array("ibd",$p) && !empty($str) ? "- " : "";
      $str .= in_array("ibd",$p) ? "IASD Capão Redondo " : "";
      $str .= in_array("sp",$p) && in_array("dst",$p) && !empty($str) ? "- " : "";
      $str .= in_array("dst",$p) ? "Capão Redondo " : "";
      $str .= in_array("sp",$p) && in_array("ig",$p) && !empty($str) ? "- " : "";
      $str .= in_array("ig",$p) ? "Igreja Adventista do Sétimo Dia " : "";
      $str .= in_array("sp",$p) && in_array("add",$p) && !empty($str) ? "- " : "";
      $str .= in_array("add",$p) ? "Av. Ellis Maas, 520 " : "";
      $str .= in_array("sp",$p) && in_array("cid",$p) && !empty($str) ? "- " : "";
      $str .= in_array("cid",$p) ? "São Paulo - SP " : "";
      $str .= in_array("sp",$p) && in_array("cep",$p) && !empty($str) ? "- " : "";
      $str .= in_array("cep",$p) ? "CEP 05859-000 " : "";
      $str .= in_array("sp",$p) && in_array("cnpj",$p) && !empty($str) ? "- " : "";
      $str .= in_array("cnpj",$p) ? "CNPJ 43.586.122/0121-20 " : "";  
      $str .= in_array("sp",$p) && in_array("rg",$p) && !empty($str) ? "- " : "";
      $str .= in_array("rg",$p) ? "6ª Região " : "";
      $str .= in_array("sp",$p) && in_array("as",$p) && !empty($str) ? "- " : "";
      $str .= in_array("as",$p) ? "Associação Paulista Sul " : "";
      $str .= in_array("sp",$p) && in_array("ab",$p) && !empty($str) ? "- " : "";
      $str .= in_array("ab",$p) ? "APS " : "";
      $str .= in_array("sp",$p) && in_array("un",$p) && !empty($str) ? "- " : "";
      $str .= in_array("un",$p) ? "UCB " : "";
      $str .= in_array("sp",$p) && in_array("dv",$p) && !empty($str) ? "- " : "";
      $str .= in_array("dv",$p) ? "DSA" : "";
      return trim($str);
    }

    //RETORNA DESCRICAO DO CLUBE
    public static function getCDS(){
        return PATTERNS::getClubeDS( array( "cl", "cj", "db", "nm", "sp", "ibd", "rg", "ab", "un", "dv" ) );
    }

    public static function getMail(){
        return "desbravadores@iasdcapao.com.br";
    }

    public static function getBars(){
        return new BARS();
    }

    //RETORNA VIRTUAL DIR
    public static function getVD(){
        return "/desbravadores/";
    }

    public static function toConvert($n){
        return base_convert($n,10,36);
    }

    public static function fromConvert($s){
        return base_convert($s,36,10);
    }
}
?>
