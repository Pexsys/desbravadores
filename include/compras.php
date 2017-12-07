<?php
@require_once('functions.php');
@require_once('_core/lib/tcpdf/tcpdf.php');

class COMPRAS {
	
	public function deleteItemPessoaEntregue( $pessoaID, $itemAprendID ) {
		$GLOBALS['conn']->Execute("
			DELETE FROM CAD_COMPRAS_PESSOA
			 WHERE FG_COMPRA = 'S'
			   AND FG_ENTREGUE = 'S'
			   AND ID_CAD_PESSOA = ?
			   AND ID_TAB_MATERIAIS IN (SELECT ID FROM TAB_MATERIAIS WHERE ID_TAB_APREND = ?) 
		", array( $pessoaID, $itemAprendID ) );
	}
	
	public function deleteByID( $id ) {
		 $GLOBALS['conn']->Execute("DELETE FROM CAD_COMPRAS_PESSOA WHERE ID = ?", array($id) );
	}
	
	public function forceInsert( $arr ){
		$GLOBALS['conn']->Execute("
			INSERT INTO CAD_COMPRAS_PESSOA(
				ID_CAD_PESSOA,
				ID_TAB_MATERIAIS,
				TP,
				COMPL,
				FG_COMPRA,
				FG_PREVISAO
			) VALUES (?,?,?,?,?,?)
		", $arr );
	}
	
	public function insertItemCompra( $cd, $pessoaID, $tp, $compl = null, $previsao = "N" ) {
		$r = $GLOBALS['conn']->Execute("
			SELECT ID
			  FROM TAB_MATERIAIS
			 WHERE CD = ?
		", array($cd) );
		if (!$r->EOF):
			$item = $r->fields["ID"];
			
			$arr = array( $pessoaID, $item );
			if (!is_null($compl)):
				$arr[] = $compl;
			endif;
			$r2 = $GLOBALS['conn']->Execute("
				SELECT 1
				  FROM CAD_COMPRAS_PESSOA
				 WHERE ID_CAD_PESSOA = ?
				   AND ID_TAB_MATERIAIS = ?
				   AND COMPL ". (is_null($compl) ? "IS NULL" : "= ?" ) ."
			", $arr );
			if ($r2->EOF):
				$this->forceInsert(
					array(
						$pessoaID,
						$item,
						$tp,
						$compl,
						"N",
						$previsao
					) 
				);
			endif;
		endif;
	}	

	function processaListaPessoaID( $pessoaID, $tp ) {
		//SELECIONA AS CARACTERISTICAS DA PESSOA
		$r1 = $GLOBALS['conn']->Execute("SELECT * FROM CON_ATIVOS WHERE ID = ?", array($pessoaID) );
		
		$qtItens = max( $r1->fields['QT_UNIFORMES'], 1 );
		$isProxAnoDir = fIdadeAtual($r1->fields['DT_NASC']) >= 15 && date( 'n' ) >= 10;
		$fundo = ( fStrStartWith( $r1->fields['CD_CARGO'], "2-") || $isProxAnoDir ? "BR" : "CQ" );

		$GLOBALS['conn']->Execute("
			DELETE FROM CAD_COMPRAS_PESSOA 
			WHERE FG_COMPRA = ?
			  AND ID_CAD_PESSOA = ?
			  AND TP = ?
		", array("N", $pessoaID, $tp) );

		//SELECIONA OS ITENS DE HISTORICO
		$r1 = $GLOBALS['conn']->Execute("
				SELECT ah.ID, ah.ID_TAB_APREND, ah.DT_AVALIACAO, 
					   ta.TP_ITEM, ta.TP_PARA, ta.CD_ITEM_INTERNO, ta.CD_AREA_INTERNO
				  FROM APR_HISTORICO ah
			INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = ah.ID_TAB_APREND)
				 WHERE ah.ID_CAD_PESSOA = ?
				   AND ah.DT_AVALIACAO IS NOT NULL 
				   AND ah.DT_INVESTIDURA IS NULL
				  ORDER BY ta.CD_ITEM_INTERNO
		", array($pessoaID) );
		foreach ($r1 as $k1 => $l1):
		
			$aprendID = $l1["ID_TAB_APREND"];

			//ainda nao alterei o select acima... - testar quando implementar
			$previsao = (is_null($l1["DT_AVALIACAO"]) ? "S" : "N");

			//SE O ITEM CLASSE
			if ( $l1["TP_ITEM"] == "CL" ):
			
				//RECUPERA AS CLASSES REGULARES CONCLUIDAS
				if ( $l1["CD_AREA_INTERNO"] == "REGULAR" ):
					$qtd = 0;
					$cdMax = "";
					$idMax = "";
					$dtMax = "";
					$concat = "";
					$r2 = $GLOBALS['conn']->Execute("
						SELECT ta.ID, ta.CD_ITEM_INTERNO, ah.DT_INVESTIDURA
						  FROM APR_HISTORICO ah
					INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = ah.ID_TAB_APREND) 
						 WHERE ta.CD_ITEM_INTERNO LIKE  '01%00'
						   AND ah.DT_AVALIACAO IS NOT NULL
						   AND ah.ID_CAD_PESSOA = ?
						ORDER BY ta.CD_ITEM_INTERNO
					", array($pessoaID) );
					if (!$r2->EOF):
						$qtd = $r2->RecordCount();
						foreach($r2 as $k2 => $i2):
							$idMax = $i2["ID"];
							$cdMax = $i2["CD_ITEM_INTERNO"];
							$dtMax = $i2["DT_INVESTIDURA"];
							$concat .= (strlen($concat) > 0 ? "|" : "").$cdMax;
						endforeach;
					endif;
					$a = explode("-", $l1["CD_ITEM_INTERNO"]);
					
					//***************************************************************
					//TRATAR TIRA DE CLASSE REGULAR
					//***************************************************************
					$materialCD = ($fundo == "BR" ? "05-04-" : "05-03-").$a[1];
					$this->insertItemCompra( $materialCD, $pessoaID, $tp, null, $previsao );
					
					//***************************************************************
					//TRATAR DISTINTIVO DE REGULARES
					//***************************************************************
					if ($qtd == 6):
						$materialCD = "04-02-01-07";
						$aprendID = $idMax;
					else:
						$materialCD = "04-02-01-".$a[1];
					endif;
					$this->insertItemCompra( $materialCD, $pessoaID, $tp, null, $previsao );
					
					//***************************************************************
					//TRATAR DIVISA DE CLASSE REGULAR
					//***************************************************************
					$materialCD = "";
					if ($qtd == 6):
						$materialCD = ($fundo == "BR" ? "06-04-06" : "06-02-06");
						$aprendID = $idMax;
					elseif ($concat == "01-01-00|01-02-00|01-03-00|01-04-00|01-05-00"):
						$materialCD = ($fundo == "BR" ? "06-04-05" : "06-02-05");
						$aprendID = $idMax;
					elseif ($concat == "01-01-00|01-02-00|01-03-00|01-04-00"):
						$materialCD = ($fundo == "BR" ? "06-04-04" : "06-02-04");
						$aprendID = $idMax;
					elseif ($concat == "01-01-00|01-02-00|01-03-00"):
						$materialCD = ($fundo == "BR" ? "06-04-03" : "06-02-03");
						$aprendID = $idMax;
					elseif ($concat == "01-01-00|01-02-00"):
						$materialCD = ($fundo == "BR" ? "06-04-02" : "06-02-02");
						$aprendID = $idMax;
					elseif (empty($dtMax)):
						$a = explode("-", $cdMax);
						$materialCD = ($fundo == "BR" ? "06-03-" : "06-01-").$a[1];
						$aprendID = $idMax;
					endif;
					if ( !empty($materialCD) ):
						$compl = null;
						for ($qtd=1;$qtd<=$qtItens;$qtd++):
							if ($qtItens>1):
								$compl = "$qtd/$qtItens";
							endif;
							$this->insertItemCompra( $materialCD, $pessoaID, $tp, $compl, $previsao );
						endfor;
					endif;

				//RECUPERA AS CLASSES AVANCADAS CONCLUIDAS
				else:
					$r2 = $GLOBALS['conn']->Execute("
						SELECT ta.CD_ITEM_INTERNO
						  FROM APR_HISTORICO ah
					INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = ah.ID_TAB_APREND)
						 WHERE ta.CD_ITEM_INTERNO LIKE  '01%01'
						   AND ah.DT_AVALIACAO IS NOT NULL
						   AND ah.ID_CAD_PESSOA = ?
					", array($pessoaID) );
					if (!$r2->EOF):
						$materialCD = "";
						if ($r2->RecordCount() == 6):
							
							//VERIFICAR SE EXISTEM AS 6 REGULARES CONCLUIDAS
							$r3 = $GLOBALS['conn']->Execute("
								SELECT ta.CD_ITEM_INTERNO
								  FROM APR_HISTORICO ah
							INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = ah.ID_TAB_APREND)
								 WHERE ta.CD_ITEM_INTERNO LIKE  '01%00'
								   AND ah.DT_AVALIACAO IS NOT NULL
								   AND ah.ID_CAD_PESSOA = ?
							", array($pessoaID) );
							if (!$r3->EOF && $r3->RecordCount() == 6):
								$materialCD = "04-02-02-07";
								$aprendID = 14;
							endif;
							
						else:
							$a = explode("-", $r1->fields["CD_ITEM_INTERNO"]);
							
							//VERIFICAR SE EXISTE A RESPECTIVA REGULAR CONCLU�DA
							$r3 = $GLOBALS['conn']->Execute("
								SELECT ta.CD_ITEM_INTERNO
								  FROM APR_HISTORICO ah
							INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = ah.ID_TAB_APREND)
								 WHERE ta.CD_ITEM_INTERNO LIKE  '01-".$a[1]."-00'
								   AND ah.DT_AVALIACAO IS NOT NULL
								   AND ah.ID_CAD_PESSOA = ?
							", array($pessoaID) );
							if (!$r3->EOF):
								$materialCD = "04-02-02-".$a[1];
							endif;
						endif;
						
						//***************************************************************
						//INSERE DISTINTIVO DA CLASSE AVANCADA
						//***************************************************************
						if (!empty($materialCD)):
							$compl = null;
							for ($qtd=1;$qtd<=$qtItens;$qtd++):
								if ($qtItens>1):
									$compl = "$qtd/$qtItens";
								endif;
								$this->insertItemCompra( $materialCD, $pessoaID, $tp, $compl, $previsao );
							endfor;
						endif;
					endif;
				endif;

			//SE ITEM ESPECIALIDADE
			elseif ( $l1["TP_ITEM"] == "ES" ):
				$cd = ( $l1["CD_AREA_INTERNO"] == "ME" ? "07-03-" : "07-02-" ) . $l1["CD_ITEM_INTERNO"];
				
				//INSERE INSIGNIA DE ESPECIALIDADE/MESTRADO
				$this->insertItemCompra( $cd, $pessoaID, $tp, null, $previsao );
			endif;
		endforeach;
	}		
}
?>