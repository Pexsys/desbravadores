<?php
@require_once("../include/sendmail.php");

function sendOcorrenciaByID($ocorrenciaID){
	$rs = $GLOBALS['conn']->Execute("
			SELECT DISTINCT co.ID, co.CD, co.DH, co.TXT
			  FROM LOG_MENSAGEM lc
		INNER JOIN CAD_OCORRENCIA co ON (co.ID = lc.ID_ORIGEM AND lc.TP = 'O')
			 WHERE co.ID = ?
 			   AND co.FG_PEND = 'N'
			   AND lc.DH_SEND IS NULL
			   AND lc.EMAIL IS NOT NULL			
		  ORDER BY 1
	", array($ocorrenciaID) );
	if (!$rs->EOF):
		$GLOBALS['mail']->Subject = "Clube Pioneiros - Ocorrencia #".$l["CD"]." [".strftime("%d/%m/%Y",strtotime($l["DH"])) ."]";
		
		$rs1 = $GLOBALS['conn']->Execute("
			SELECT *
			  FROM LOG_MENSAGEM
			 WHERE DH_SEND IS NULL
			   AND EMAIL IS NOT NULL
			   AND ID_ORIGEM = ?
			   AND TP = ?
	      ORDER BY ID
		", array($l["ID"], "O") );
		
		if ( !$rs1->EOF ):
			foreach($rs1 as $l1):
				$GLOBALS['mail']->ClearAllRecipients();
				$GLOBALS['mail']->AddAddress( $l1["EMAIL"] );
				$GLOBALS['mail']->MsgHTML($l["TXT"]);
					
				if ( $GLOBALS['mail']->Send() ):
					$nrEnviados++;
					//ATUALIZA ENVIO
					$GLOBALS['conn']->Execute("
						UPDATE LOG_MENSAGEM
						   SET DH_SEND = NOW()
						 WHERE ID = ?
						   AND TP = ?
					", array($l1["ID"], "O") );
				else:
					echo "email não enviado para ". $l1["EMAIL"];
				endif;
			
			endforeach;
		endif;
	endif;
}
?>