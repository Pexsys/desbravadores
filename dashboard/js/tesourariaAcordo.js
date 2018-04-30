var comDataTable = undefined; 
var rowSelected = undefined; 
 
$(document).ready(function(){ 
 
	comDataTable = $('#comDataTable').DataTable({
		lengthChange: false,
		ordering: true,
		paging: false,
		scrollY: 300,
		searching: true,
		processing: true,
		language: {
			info: "_END_ acordos",
			search: "",
			searchPlaceholder: "Procurar...",
			infoFiltered: " de _MAX_",
			loadingRecords: "Aguarde - carregando...",
			zeroRecords: "Dados indispon&iacute;veis para esta sele&ccedil;&atilde;o",
			infoEmpty: "0 encontrados"
		},
		ajax: {
			type  : "GET",
			url  : jsLIB.rootDir+"rules/acordos.php",
			data  : function (d) {
				d.MethodName = "getAcordos",
					d.data = {
						filtro: 'T',
						filters: jsFilter.jSON()
					}
			},
			dataSrc: "source"
		},
		columns: [ 
			{  data: "id", 
				visible: false 
			},
			{  data: 'cd',
				sortable: true,
				width: "10%"
			},
			{  data: 'pt',
				type: 'ptbr-string',
				sortable: true,
				width: "40%"
			},
			{  data: 'bn',
				type: 'ptbr-string',
				sortable: true,
				width: "40%"
			},
			{  data: "tp",
				type: 'ptbr-string',
				width: "10%",
				render: function (data, type, row) {
					if (data == 'P'){
						return "PENDENTE";
					} else if (data == 'L'){
						return "LIBERADO";
					} else {
						return "CONCLUÍDO";
					} 
				} 
			} 
		], 
		fnRowCallback: function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) { 
			if (aData.tp == "P") {
				$(nRow.cells[3]).css('color', '#ff6464' ).css('font-weight', 'bold');
			} else if ( aData.tp == "L" ) {
				$(nRow.cells[3]).css('color', '#b0ffb3' );
			} else {
				$(nRow.cells[3]).css('color', '#b0ffb3' );
			}
		}
	}).order( [ 4, 'asc' ] ); 
 
    $("#btnNovo").on("click", function(event){ 
        alternaFormularios(true); 
    }); 
 
    $("#btnFechar").on("click", function(event){ 
        telaInicial();
    }); 
 
    $("#btnGravar").on("click", function(event){ 
        telaInicial();
	}); 
});

function telaInicial(){
	refreshDataTable();
	alternaFormularios(false);
	
}
 
function alternaFormularios(exibe){ 
    $("#divLista").visible(!exibe); 
    $("#divAcordo").visible(exibe); 
} 
 
function refreshDataTable(){
    comDataTable.ajax.reload( function(){
	}); 
}


/*
	TOPICOS - (*) Obrigatórios

	--------------------------------------------------------------------------------------
	1 - IDENTIFICAR POR CPF O PATROCINADOR (*)
	--------------------------------------------------------------------------------------
		1.1 - Se não existir, inserir em CAD_PESSOA (NM, NR_CPF, FONE_CEL, EMAIL, DT_NASC), e recuperar ID_CAD_PESSOA que será utilizado como patrocinador.
		1.2 - Se existir, recuperar ID_CAD_PESSOA que será utilizado como patrocinador.
	
	--------------------------------------------------------------------------------------
	2 - IDENTIFICAR POR CPF OU NOME O BENEFICIÁRIOS (pode ser nenhum ou diversos).
	--------------------------------------------------------------------------------------
		2.1 - Caso não exista patrocinados nominais, atribuir internamente o benefício para o CPF 000.000.000-00 (Clube), ID_PESSOA = 0.
		2.2 - Caso não encontre uma pessoa que receberá o benefício, inserir em CAD_PESSOA (NM), e recuperar ID_CAD_PESSOA que será utilizado como beneficiário.
		2.3 - Se existir, recuperar ID_CAD_PESSOA que será utilizado como beneficiário.

	--------------------------------------------------------------------------------------
	3 - ESCOLHA DOS ITENS A SEREM ACORDADOS
	--------------------------------------------------------------------------------------
		3.1 - Por padrão, todos os itens totais ou parciais (ainda não acordados para o período) deverão vir marcados para acordo.
		3.2 - Listar agrupadamente os itens a serem acordados, agrupados e sumarizados por seus respectivos grupos/subgrupos + beneficiario ou agrupados por beneficiário + grupo/subgrupo.
		3.3 - Para cada item selecionado, o sistema deve fazer dinamicamente a soma dos itens, exibir nos grupos/subgrupos e respectiva árvore o que já foi selecionado, mostrando ainda o que falta a ser pago, no grupo do total.
		3.4 - Os itens poderão ser escolhidos e acordados de maneira geral
		3.5 - Os itens marcados com parcelamento/valores específicos não entrarão na regra de acordo/parcelamento genérico.
		3.6 - Assim que firmado o acordo, os galores serão gravados tanto os genéricos quanto os específicos respeitando o respectivo acordo.

	--------------------------------------------------------------------------------------
	4 - ESCOLHA DA FORMA DE PAGAMENTO
	--------------------------------------------------------------------------------------
		4.1 - Os valores sempre poderão ser negociados respeitando as datas-limite de parcelamento de cada item escolhido. 
		4.2 - Caso haja intenção de parcelamento, indicar as parcelas por meio de spinner, de acordo com o item 4.1.
		4.3 - Para facilitar a vida do negociador, oferecer o cálculo do item em valor nominal ou em porcentagem.
		4.4 - As datas de parcelamento serão sujeridas pelo sistema, mas poderão ser alteradas conforme a necessidade do patrocinador, de acordo com o item 4.1.
		4.5 - Ao gravar o acordo:
			4.5.1 - Os valores deverão ser agendados de modo sumarizado e também de modo separado para que o clube possa controlar e visualizar todas as contas contábeis relativas ao custo de cada item.
			4.5.2 - O sistema deverá disponibilizar cópia impressa do respectivo acordo, para que o patrocinador possa assinar, e também levar para relembrar.
			4.5.3 - No dia do vencimento da parcela (não sendo sábado), o sistema fará automaticamente respectivos lançamentos.
			4.5.4 - Embora os valores possam ser escolhidos e negociados com porcentagem, sempre deverão ser gravados de maneira nominal.

	--------------------------------------------------------------------------------------
	5 - PAGAMENTO
	--------------------------------------------------------------------------------------
		5.1 - O pagamento poderá ser feito diretamente no clube, em dinheiro, cheque, cartões de crédito/débito.
		5.2 - Caso o pagamento seja feito em depósito na conta da igreja, o patrocinador deverá enviar o comprovante para a tesouraria do clube que fará a baixa das parcelas/valores.
		5.3 - Caso o pagamento seja feito abaixo do valor da parcela, e o sistema sempre abaterá do inicio para o final. Logo, uma parcela somente será considerada quitada se o valor integral da mesma for pago.
		5.4 - Caso o pagamento seja feito acima do valor da parcela, o saldo remanescente pago será abatido da próxima parcela.

	--------------------------------------------------------------------------------------
	6 - EFETIVAÇÃO DO ACORDO
	--------------------------------------------------------------------------------------
		6.1 - Um acordo somente será efetivado, quando houver o lançamento manual ou automático de um recibo de pagamento.
		6.2 - Todo pagamento que entrar no sistema, será contabilizado por meio de recibo. Os recibos poderão ser automáticos, ou lançados de forma manual pela tesouraria.
		6.3 - Todo recibo também poderá ser reimpresso, tanto pela tesouraria, quanto pelo patrocinador.

	--------------------------------------------------------------------------------------
	7 - NOVO ACORDO
	--------------------------------------------------------------------------------------
		7.1 - Novos acordos podem ser feitos, sempre levando em conta os valores em atraso ou atuais ainda não acordados.
		7.2 - Caso um novo acordo seja feito, os acordos anteriores deverão ser considerados quitados/lançados de forma integral e o novo acordo deve contar as novas condições.

	--------------------------------------------------------------------------------------
	REGRA CONTÁBIL
	--------------------------------------------------------------------------------------
	C/P-C - CONTA Promessa do CLUBE - Armazena as promessas de pagamento (visão do clube).
	C/P-P - CONTA Promessa da PESSOA - Armazena as promessas de pagamento (visão da pessoa).
	C/C-C - CONTA Caixa do CLUBE - Armazena as movimentações financeiras reais do clube.
	C/C-P - CONTA Caixa da PESSOA - Armazena as movimentações financeiras reais da pessoa.

	1 - Ao inserir uma promessa de pagamento.....:	(+) C/P-C | (-) C/P-P (1*)
	2 - No dia do vencimento da parcela (batch)..:	(+) C/P-P | (-) C/C-P (2*)
	3 - No pagamento da parcela (recibo).........:	(+) C/C-C | (-) C/P-C (3*)
		3.1 - Caso a parcela tenha algum valor de pagamento antecipado, realizar a operação (2*) para o respectivo valor excedente, antes de realizar a operação (3*).

*/