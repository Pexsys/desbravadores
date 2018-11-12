<?php
$mail = MAIL::get();
function sendOcorrenciaByID($ocorrenciaID, $nrEnviados){
	$rs = CONN::get()->Execute("
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
    $mail = MAIL::get();
    $mail->Subject = PATTERNS::getClubeDS( array("cl","nm") ) . " - Ocorrencia #".$l["CD"]." [".strftime("%d/%m/%Y",strtotime($l["DH"])) ."]";
    
		$rs1 = CONN::get()->Execute("
			SELECT *
			  FROM LOG_MENSAGEM
			 WHERE DH_SEND IS NULL
			   AND EMAIL IS NOT NULL
			   AND ID_ORIGEM = ?
			   AND TP = ?
	      ORDER BY ID
		", array($ocorrenciaID, "O") );
		if ( !$rs1->EOF ):
			foreach($rs1 as $k1 => $l1):
				$mail->ClearAllRecipients();
				$mail->AddAddress($l1["EMAIL"]);
				$mail->MsgHTML($l["TXT"]);
					
        if ( $mail->Send() ):
          ++$nrEnviados;
					CONN::get()->Execute("
						UPDATE LOG_MENSAGEM
						   SET DH_SEND = NOW()
						 WHERE ID = ?
					", array($l1["ID"]) );
				else:
					echo "email não enviado para ". $l1["EMAIL"]."<br/>";
				endif;
			
			endforeach;
		endif;
  endif;
  return $nrEnviados;
}
?>