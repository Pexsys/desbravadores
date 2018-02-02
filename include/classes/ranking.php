<?php
class RANKING {
    //ID - ID_CAD_MEMBRO - TP - ID_ORIGEM - DH_ORIGEM - DH_LOG - ID_CAD_USUARIO - NR_PONTOS

    public function __construct(){
    }

    private function insert($data){
        return ENTITY::instance("LOG_RANKING")->insert($data)->getID();
    }

    public function log(){

    }
}
?>