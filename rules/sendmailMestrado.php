<?php
@require_once("../include/sendmail.php");

function sendMestradoByID($mestradoID){
	$rs = $GLOBALS['conn']->Execute("
			SELECT DISTINCT ta.*
			  FROM LOG_MENSAGEM lc
		INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = lc.ID_ORIGEM AND lc.TP = 'M')
			 WHERE ta.ID = ?
 			   AND ta.FG_PEND = 'N'
			   AND lc.DH_SEND IS NULL
			   AND lc.EMAIL IS NOT NULL			
		  ORDER BY 1
	", array($mestradoID) );
	if (!$rs->EOF):
		$GLOBALS['mail']->Subject = "Clube Pioneiros - Aviso de Conclusão!";
		
		$rs1 = $GLOBALS['conn']->Execute("
			SELECT *
			  FROM LOG_MENSAGEM
			 WHERE DH_SEND IS NULL
			   AND EMAIL IS NOT NULL
			   AND ID_ORIGEM = ?
			   AND TP = ?
	      ORDER BY ID
		", array($l["ID"], "M") );
		
		if ( !$rs1->EOF ):
			foreach($rs1 as $l1):
				$GLOBALS['mail']->ClearAllRecipients();
				$GLOBALS['mail']->AddAddress( $l1["EMAIL"] );
				$GLOBALS['mail']->MsgHTML("");
					
				if ( $GLOBALS['mail']->Send() ):
					$nrEnviados++;
					//ATUALIZA ENVIO
					$GLOBALS['conn']->Execute("
						UPDATE LOG_MENSAGEM
						   SET DH_SEND = NOW()
						 WHERE ID = ?
						   AND TP = ?
					", array($l1["ID"], "M") );
				else:
					echo "email não enviado para ". $l1["EMAIL"];
				endif;
			
			endforeach;
		endif;
	endif;
}
?>