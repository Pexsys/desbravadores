<?php
@require_once("../include/functions.php");
@require_once("../include/profile.php");
@require_once("../include/birthdayMsg.php");
@require_once("sendmailMestrado.php");

fConnDB();
//******* INICIO DA ROTINA DIARIA

$GLOBALS['conn']->Execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('DIÁRIA','01.00-Iniciando rotina...')");

//******* LOG
$GLOBALS['conn']->Execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('DIÁRIA','01.01.00-Inicio do tratamento de LOGs...')");
$GLOBALS['conn']->Execute("DELETE FROM LOG_BATCH WHERE DH < ( NOW() - INTERVAL 30 DAY )");
$GLOBALS['conn']->Execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('DIÁRIA','01.01.99-Exclusão de LOGs antigos concluída.')");

//******* SECRETARIA
$GLOBALS['conn']->Execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('DIÁRIA','01.02.00-Analisando Secretaria...')");

$rA = $GLOBALS['conn']->Execute("SELECT * FROM CON_DIRETOR");
$nomeDiretor = titleCase($rA->fields["NOME_DIRETOR"]);

//******* SECRETARIA - FELIZ ANIVERSADIO
$GLOBALS['conn']->Execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('DIÁRIA','01.02.01-Analisando Aniversário...')");
	$rA = $GLOBALS['conn']->Execute("
		SELECT cp.NM, cp.TP_SEXO, Year(NOW())-Year(cp.DT_NASC) AS IDADE_ANO, EMAIL
		  FROM CAD_PESSOA cp
		 WHERE cp.DT_NASC IS NOT NULL
		  AND cp.EMAIL IS NOT NULL
		  AND MONTH(cp.DT_NASC) = MONTH(NOW())
		  AND DAY(cp.DT_NASC) = DAY(NOW())
	");
	foreach ($rA as $lA => $fA):
		$a = explode(" ",titleCase($fA["NM"]));

		$bm = getBirthdayMessage( array( "np" => $a[0], "id" => $fA["IDADE_ANO"], "sx" => $fA["TP_SEXO"], "nd" => $nomeDiretor ) );

		$GLOBALS['mail']->ClearAllRecipients();
		$GLOBALS['mail']->AddAddress( $fA["EMAIL"] );
		$GLOBALS['mail']->Subject = utf8_decode( $bm["sub"] );
		$GLOBALS['mail']->MsgHTML( $bm["msg"] );
			
		if ( $GLOBALS['mail']->Send() ):
			echo "parabens enviado para ". $fA["EMAIL"]."<br/>";
		else:
			echo "parabens não enviado para ". $fA["EMAIL"]."<br/>";
		endif;
	endforeach;

//******* SECRETARIA - MESTRADOS
$GLOBALS['conn']->Execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('DIÁRIA','01.02.02-Analisando Mestrados Completados...')");

    $rA = $GLOBALS['conn']->Execute("SELECT * FROM CON_ATIVOS");
    foreach ($rA as $lA => $fA):
        
        $a = explode(" ",titleCase($fA["NM"]));

        //LE REGRAS
    	$rg = $GLOBALS['conn']->Execute("
    	    SELECT DISTINCT car.ID, car.CD_ITEM_INTERNO, car.CD_AREA_INTERNO, car.DS_ITEM, car.TP_ITEM, car.MIN_AREA
    	      FROM CON_APR_REQ car
    	     WHERE car.CD_AREA_INTERNO = ?
    	  ORDER BY car.CD_ITEM_INTERNO
    	", array("ME") );
    	foreach ($rg as $lg => $fg):
            $min = $fg["MIN_AREA"];
    
            $feitas = 0;
            //LE PARAMETRO MINIMO E HISTORICO PARA A REGRA
    	    $rR = $GLOBALS['conn']->Execute("
                    SELECT tar.ID, tar.QT_MIN, COUNT(*) AS QT_FEITAS
                      FROM TAB_APR_REQ tar
                INNER JOIN CON_APR_REQ car ON (car.ID_TAB_APR_REQ = tar.ID AND car.TP_ITEM_RQ = ?)
                INNER JOIN APR_HISTORICO ah ON (ah.ID_TAB_APREND = car.ID_RQ AND ah.ID_CAD_PESSOA = ? AND ah.DT_CONCLUSAO IS NOT NULL)
                     WHERE tar.ID_TAB_APREND = ?
                  GROUP BY tar.ID, tar.QT_MIN
        	", array( "ES", $fA["ID"], $fg["ID"] ) );
    	    foreach($rR as $lR => $fR):
                $feitas += min( $fR["QT_MIN"], $fR["QT_FEITAS"] );
            endforeach;
    	    
    		$pct = floor( ( $feitas / $min ) * 100 );
    		
    		//VERIFICA SE COMPLETADO, MAS AINDA NÃO CONCLUIDO/AVALIADO/INVESTIDO
    		if ( $pct >= 100 ):
        	    $rI = $GLOBALS['conn']->Execute("
                    SELECT DT_CONCLUSAO
                    FROM APR_HISTORICO 
                    WHERE ID_CAD_PESSOA = ?
                      AND ID_TAB_APREND = ?
        	    ", array( $fA["ID"], $fg["ID"] ) );	
        	    if ($rI->EOF || is_null($rI->fields["DT_CONCLUSAO"]) ):
        	        
        	        //INSERE NOTIFICAÇOES SE NÃO EXISTIR.
        	        $GLOBALS['conn']->Execute("
        				INSERT INTO LOG_MENSAGEM ( ID_ORIGEM, TP, ID_USUARIO, EMAIL, DH_GERA )
        				SELECT ?, 'M', cu.ID_USUARIO, ca.EMAIL, NOW()
        				  FROM CON_ATIVOS ca
        			INNER JOIN CAD_USUARIOS cu ON (cu.ID_CAD_PESSOA = ca.ID)
        				 WHERE ca.ID = ?
        				   AND NOT EXISTS (SELECT 1 FROM LOG_MENSAGEM WHERE ID_ORIGEM = ? AND TP = 'M' AND ID_USUARIO = cu.ID_USUARIO)
        			", array( $fg["ID"], $fA["ID"], $fg["ID"] ) );
        			
					if (!empty($fA["EMAIL"])):
            			$GLOBALS['mail']->ClearAllRecipients();
        				$GLOBALS['mail']->AddAddress( $fA["EMAIL"] );
        				$GLOBALS['mail']->Subject = utf8_decode("Clube Pioneiros - Aviso de Conclusão");
        				$GLOBALS['mail']->MsgHTML( getConclusaoMsg( array( "np" => $a[0], "nm" => $fg["DS_ITEM"], "sx" => $fA["SEXO"], "nd" => $nomeDiretor ) ) );
        					
        				if ( $GLOBALS['mail']->Send() ):
        					$nrEnviados++;
        					//ATUALIZA ENVIO
        					$GLOBALS['conn']->Execute("
        						UPDATE LOG_MENSAGEM
        						   SET DH_SEND = NOW()
        						 WHERE ID = ?
        						   AND TP = ?
        					", array( $l1["ID"], "M" ) );
        					echo "email mestrado enviado para ". $fA["EMAIL"]."<br/>";
        				else:
        					echo "email mestrado não enviado para ". $fA["EMAIL"]."<br/>";
        				endif;
        			endif;
        	    endif;
    		endif;
    	endforeach;
	endforeach;

//******* EXCLUSAO DE ACESSOS DE MEMBROS INATIVOS
$GLOBALS['conn']->Execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('DIÁRIA','01.02.03-Excluindo Perfis de Membros Inativos...')");
$profile = new PROFILE();
$result = $GLOBALS['conn']->Execute("
		SELECT DISTINCT cu.ID_USUARIO
		FROM CAD_PESSOA cp 
  INNER JOIN CAD_USUARIOS cu ON (cu.ID_CAD_PESSOA = cp.ID)
  INNER JOIN CAD_USU_PERFIL cup ON (cup.ID_CAD_USUARIOS = cu.ID_USUARIO)
		WHERE NOT EXISTS (SELECT 1 FROM CAD_ATIVOS WHERE ID = cp.ID AND NR_ANO = YEAR(NOW())) 
");
foreach($result as $l => $fields):
	$profile->deleteAllByUserID( $fields['ID_USUARIO'] );
endforeach;

$GLOBALS['conn']->Execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('DIÁRIA','01.02.99-Rotina de secretaria finalizada com Sucesso.')");

//******* TESOURARIA
//$GLOBALS['conn']->Execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('DIÁRIA','01.03.00-Analisando Tesouraria...')");
//$GLOBALS['conn']->Execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('DIÁRIA','01.03.99-Rotina de Tesouraria finalizada com Sucesso.')");

//******* FINAL DA ROTINA DIARIA
$GLOBALS['conn']->Execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('DIÁRIA','01.99-Rotina finalizada com sucesso!')");
exit;
?>