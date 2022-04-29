<?php
@require_once("../include/functions.php");
responseMethod();

/****************************
 * Methods defined for use. *
 ****************************/
function getQueryByFilter( $parameters ) {
	$query = "
        SELECT cu.ID, cu.CD_USUARIO, cu.DS_USUARIO, cu.DH_ATUALIZACAO
        FROM CAD_USUARIO cu
        INNER JOIN CON_ATIVOS ca ON (ca.ID_CAD_PESSOA = cu.ID_CAD_PESSOA)
        ORDER BY DH_ATUALIZACAO DESC
	";
	return CONN::get()->execute( $query );
}

function getUsers( $parameters ) {
	$arr = array();

	$result = getQueryByFilter( $parameters );
	if (!is_null($result)):
		foreach ($result as $k => $fields):
			$arr[] = array(
			    "id" => $fields['ID'],
				"ds" => $fields['DS_USUARIO'],
				"dh" => (empty($fields['DH_ATUALIZACAO']) ? "" : strtotime($fields['DH_ATUALIZACAO']) ),
			);
		endforeach;
	endif;
	
	return array( "result" => true, "users" => $arr );
}

function delete( $parameters ) {
	$ids = $parameters["ids"];
	foreach ($ids as $k => $id):
		CONN::get()->execute("DELETE FROM CAD_USUARIO WHERE ID = ?", array($id) );
	endforeach;
	return array( "result" => true );
}
?>
