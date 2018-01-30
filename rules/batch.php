<?php
@require_once("../include/functions.php");
@require_once("../include/sendmail.php");

$md = date("m-d");

fConnDB();
//******* INICIO DA ROTINA DIARIA

$GLOBALS['conn']->Execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('DIÁRIA','01.00-Iniciando rotina...')");

//******* LOG
$GLOBALS['conn']->Execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('DIÁRIA','01.01.00-Inicio do tratamento de LOGs...')");
$GLOBALS['conn']->Execute("DELETE FROM LOG_BATCH WHERE DH < ( NOW() - INTERVAL 30 DAY )");
$GLOBALS['conn']->Execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('DIÁRIA','01.01.99-Exclusão de LOGs antigos concluída.')");

//******* SECRETARIA
$GLOBALS['conn']->Execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('DIÁRIA','01.02.00-Analisando Secretaria...')");

$rDIR = $GLOBALS['conn']->Execute("SELECT * FROM CON_DIRETOR");
$nomeDiretor = titleCase($rDIR->fields["NOME_DIRETOR"]);

//******* SECRETARIA - FELIZ ANIVERSADIO
$GLOBALS['conn']->Execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('DIÁRIA','01.02.01-Analisando Aniversário...')");
	$rA = $GLOBALS['conn']->Execute("
		SELECT NM, TP_SEXO, Year(NOW())-Year(DT_NASC) AS IDADE_ANO, EMAIL
		  FROM CAD_PESSOA
		 WHERE DT_NASC IS NOT NULL
		  AND EMAIL IS NOT NULL
		  AND MONTH(DT_NASC) = MONTH(NOW())
		  AND DAY(DT_NASC) = DAY(NOW())
		  AND ID != ?
	", array($rDIR->fields["ID_DIRETOR"]) );
	foreach ($rA as $lA => $fA):
		$a = explode(" ",titleCase($fA["NM"]));

		$message = MESSAGE::instance( array( "np" => $a[0], "id" => $fA["IDADE_ANO"], "sx" => $fA["TP_SEXO"], "nd" => $nomeDiretor ) );
		$bm = $message->getBirthday();

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
    	     WHERE car.CD_AREA_INTERNO = 'ME'
    	  ORDER BY car.CD_ITEM_INTERNO
    	");
    	foreach ($rg as $lg => $fg):
            $min = $fg["MIN_AREA"];

            $feitas = 0;
            //LE PARAMETRO MINIMO E HISTORICO PARA A REGRA
    	    $rR = $GLOBALS['conn']->Execute("
                    SELECT tar.ID, tar.QT_MIN, COUNT(*) AS QT_FEITAS
                      FROM TAB_APR_ITEM tar
                INNER JOIN CON_APR_REQ car ON (car.ID_TAB_APR_ITEM = tar.ID AND car.TP_ITEM_RQ = 'ES')
                INNER JOIN APR_HISTORICO ah ON (ah.ID_TAB_APREND = car.ID_RQ AND ah.ID_CAD_PESSOA = ? AND ah.DT_CONCLUSAO IS NOT NULL)
                     WHERE tar.ID_TAB_APREND = ?
                  GROUP BY tar.ID, tar.QT_MIN
        	", array($fA["ID_CAD_PESSOA"], $fg["ID"]) );
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
        	    ", array( $fA["ID_CAD_PESSOA"], $fg["ID"] ) );
        	    if ($rI->EOF || is_null($rI->fields["DT_CONCLUSAO"]) ):

        	        //INSERE NOTIFICAÇOES SE NÃO EXISTIR.
        	        $GLOBALS['conn']->Execute("
        				INSERT INTO LOG_MENSAGEM ( ID_ORIGEM, TP, ID_USUARIO, EMAIL, DH_GERA )
        				SELECT ?, 'M', cu.ID_USUARIO, ca.EMAIL, NOW()
        				  FROM CON_ATIVOS ca
        			INNER JOIN CAD_USUARIOS cu ON (cu.ID_CAD_PESSOA = ca.ID_CAD_PESSOA)
        				 WHERE ca.ID_CAD_PESSOA = ?
        				   AND NOT EXISTS (SELECT 1 FROM LOG_MENSAGEM WHERE ID_ORIGEM = ? AND TP = 'M' AND ID_USUARIO = cu.ID_USUARIO)
        			", array( $fg["ID"], $fA["ID_CAD_PESSOA"], $fg["ID"] ) );

					if (!empty($fA["EMAIL"]) && $fA["ID_CAD_PESSOA"] != $rDIR->fields["ID_DIRETOR"] ):
						$message = new MESSAGE( array( "np" => $a[0], "nm" => $fg["DS_ITEM"], "sx" => $fA["SEXO"], "nd" => $nomeDiretor ) );

            			$GLOBALS['mail']->ClearAllRecipients();
        				$GLOBALS['mail']->AddAddress( $fA["EMAIL"] );
        				$GLOBALS['mail']->Subject = utf8_decode(PATTERNS::getClubeDS( array("cl","nm") ) . " - Aviso de Conclusão");
        				$GLOBALS['mail']->MsgHTML( MESSAGE::getConclusao() );

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

//*******  SECRETARIA - EXCLUSAO DE ACESSOS/PERFIS DE MEMBROS INATIVOS
$GLOBALS['conn']->Execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('DIÁRIA','01.02.03-Excluindo Perfis de Membros Inativos...')");
$result = $GLOBALS['conn']->Execute("
	  SELECT DISTINCT cu.ID_USUARIO
		FROM CAD_PESSOA cp
  INNER JOIN CAD_USUARIOS cu ON (cu.ID_CAD_PESSOA = cp.ID)
  INNER JOIN CAD_USU_PERFIL cup ON (cup.ID_CAD_USUARIOS = cu.ID_USUARIO)
	   WHERE NOT EXISTS (SELECT 1
							FROM CAD_ATIVOS a
					  INNER JOIN CAD_MEMBRO m ON (m.ID = a.ID_CAD_MEMBRO)
					  WHERE m.ID_CAD_PESSOA = cp.ID
						AND a.NR_ANO = YEAR(NOW())
						)
");
foreach($result as $l => $fields):
	PROFILE::deleteAllByUserID( $fields['ID_USUARIO'] );
endforeach;

//*******  SECRETARIA - REORGANIZACAO DA BASE EM 01/JANEIRO
if ($md == "01-01"):

	//BASE DE COMPRAS
	$GLOBALS['conn']->Execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('DIÁRIA','01.02.04-Reorganizando base de compras...')");
	$result = $GLOBALS['conn']->Execute("
		SELECT *
		  FROM CAD_COMPRAS
		 WHERE FG_PREVISAO = 'N'
	  ORDER BY ID_CAD_MEMBRO, ID_TAB_MATERIAIS, COMPL
	");
	$GLOBALS['conn']->Execute("TRUNCATE CAD_COMPRAS");
	foreach($result as $l => $f):
		$GLOBALS['conn']->Execute("
			INSERT INTO CAD_COMPRAS(
				ID_CAD_MEMBRO,
				ID_TAB_MATERIAIS,
				TP,
				COMPL,
				FG_COMPRA,
				FG_ENTREGUE,
				FG_PREVISAO
			) VALUES (?,?,?,?,?,?,?)
		", array(
			$f["ID_CAD_MEMBRO"],
			$f["ID_TAB_MATERIAIS"],
			$f["TP"],
			$f["COMPL"],
			$f["FG_COMPRA"],
			$f["FG_ENTREGUE"],
			$f["FG_PREVISAO"]
		));
	endforeach;

	//REQUISITOS ASSINADOS DE ITENS AINDA NÃO CONCLUÍDOS
	$GLOBALS['conn']->Execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('DIÁRIA','01.02.05-Reorganizando base de requisitos assinados...')");
	$GLOBALS['conn']->Execute("DELETE FROM APR_PESSOA_REQ WHERE ID_HISTORICO IN (SELECT ID FROM APR_HISTORICO WHERE DT_CONCLUSAO IS NOT NULL)");
	$result = $GLOBALS['conn']->Execute("SELECT * FROM APR_PESSOA_REQ ORDER BY ID_HISTORICO, ID_TAB_APR_ITEM");
	$GLOBALS['conn']->Execute("TRUNCATE APR_PESSOA_REQ");
	foreach($result as $l => $f):
		$GLOBALS['conn']->Execute("
			INSERT INTO APR_PESSOA_REQ(
				ID_HISTORICO,
				ID_TAB_APR_ITEM,
				DT_ASSINATURA
			) VALUES (?,?,?)
		", array(
			$f["ID_HISTORICO"],
			$f["ID_TAB_APR_ITEM"],
			$f["DT_ASSINATURA"]
		));
	endforeach;

	//MENSAGEM DE FELIZ ANO NOVO
	$GLOBALS['conn']->Execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('DIÁRIA','01.02.06-Felicitando pelo novo ano...')");
	$rA = $GLOBALS['conn']->Execute("
		SELECT NM, TP_SEXO, EMAIL
		  FROM CAD_PESSOA
		 WHERE EMAIL IS NOT NULL
		   AND ID != ?
	", array($rDIR->fields["ID_DIRETOR"]) );
	foreach ($rA as $lA => $fA):
		$a = explode(" ",titleCase($fA["NM"]));

		$message = MESSAGE::instance( array( "np" => $a[0], "id" => $fA["IDADE_ANO"], "sx" => $fA["TP_SEXO"], "nd" => $nomeDiretor ) );
		$bm = $message->getNewYear();

		$GLOBALS['mail']->ClearAllRecipients();
		$GLOBALS['mail']->AddAddress( $fA["EMAIL"] );
		$GLOBALS['mail']->Subject = utf8_decode( $bm["sub"] );
		$GLOBALS['mail']->MsgHTML( $bm["msg"] );

		if ( $GLOBALS['mail']->Send() ):
			echo "feliz ano novo enviado para ". $fA["EMAIL"]."<br/>";
		else:
			echo "feliz ano novo não enviado para ". $fA["EMAIL"]."<br/>";
		endif;
	endforeach;

//MENSAGEM DE FELIZ NATAL
elseif ($md == "12-25"):

	$GLOBALS['conn']->Execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('DIÁRIA','01.02.07-Felicitando pelo natal...')");
	$rA = $GLOBALS['conn']->Execute("
		SELECT cp.NM, cp.TP_SEXO, EMAIL
			FROM CAD_PESSOA cp
			WHERE EMAIL IS NOT NULL
			AND ID != ?
	", array($rDIR->fields["ID_DIRETOR"]) );
	foreach ($rA as $lA => $fA):
		$a = explode(" ",titleCase($fA["NM"]));

		$message = MESSAGE::instance( array( "np" => $a[0], "id" => $fA["IDADE_ANO"], "sx" => $fA["TP_SEXO"], "nd" => $nomeDiretor ) );
		$bm = message->getXMas();

		$GLOBALS['mail']->ClearAllRecipients();
		$GLOBALS['mail']->AddAddress( $fA["EMAIL"] );
		$GLOBALS['mail']->Subject = utf8_decode( $bm["sub"] );
		$GLOBALS['mail']->MsgHTML( $bm["msg"] );

		if ( $GLOBALS['mail']->Send() ):
			echo "feliz natal enviado para ". $fA["EMAIL"]."<br/>";
		else:
			echo "feliz natal não enviado para ". $fA["EMAIL"]."<br/>";
		endif;
	endforeach;
endif;

$GLOBALS['conn']->Execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('DIÁRIA','01.02.99-Rotina de secretaria finalizada com Sucesso.')");

//******* TESOURARIA
//$GLOBALS['conn']->Execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('DIÁRIA','01.03.00-Analisando Tesouraria...')");
//$GLOBALS['conn']->Execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('DIÁRIA','01.03.99-Rotina de Tesouraria finalizada com Sucesso.')");

//******* FINAL DA ROTINA DIARIA
$GLOBALS['conn']->Execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('DIÁRIA','01.99-Rotina finalizada com sucesso!')");
exit;
?>
