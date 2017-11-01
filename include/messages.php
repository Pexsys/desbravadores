<?php
function getConclusaoMsg( $p ){
	return str_replace( array("&lt;", "&gt;"), array("<", ">"), htmlentities("<p>Olá ".$p["np"].",<br/>
		<br/>
		Em nome do Clube Pioneiros, quero lhe agradecer pelo seu esforço e por mais esta etapa concluída.<br/>
		<br/>
		No intuito de melhorar cada dia mais os registros da secretaria, nosso sistema detectou automaticamente que você concluiu o <b>".$p["nm"]."</b>.<br/>
		<br/>
		Entre no sistema do clube (www.iasd-capaoredondo.com.br/desbravadores) e confira na opção <i>Minha Página / Meu Aprendizado</i>. Caso não consiga ou não tenha acesso, procure seu conselheiro(a), instrutor(a) ou a secretaria do clube.<br/>
		<br/>
		Fiquei orgulhoso ao saber que se tornou um".($p["sx"] == "F"?"a":"")." especialista nessa área. Isso é bom pra você e também para o clube. Meus Parabéns!<br/>
		<br/>
		<br/>
		MARANATA!
		<br/>
		<br/>
		Com carinho,<br/>
		<br/>
		".$p["nd"]."<br/>
		<small>Clube Pioneiros - IASD Capão Redondo<small>
		</p>", ENT_NOQUOTES, 'UTF-8', false));
}
//echo getConclusaoMsg( array( "np" => "Ricardo", "nm" => "MESTRADO", "sx" => "M", "nd" => "Ricardo Jonadabs C&eacute;sar" )  );

function getBirthdayMessage( $aP ){
    $_np = $aP["np"];
    $_id = $aP["id"];

    if ( $aP["sx"] == "F" ):
        $_oa = "a";
        $_mm = "inha";
        $_ii = "irmã";
        $_uu = "uma";
        $_bb = "a";
    else:
        $_oa = "o";
        $_mm = "eu";
        $_ii = "irmão";
        $_uu = "um";
        $_bb = "m";
    endif;

    $aB = array( 
        array(
            "sub" => "Feliz Aniversário!",
            "msg" => "Querid$_oa amig$_oa $_np,<br/>
			<br/>
			Feliz aniversário!<br/>
			<br/>
			Desejo que a cada dia você possa continuar animad$_oa, permitindo que o Espírito Santo te guie, te ilumine, te unja com Seu óleo santo, lhe dando sabedoria.<br/>
			<br/>
			Hoje faz $_id anos que o mundo se tornou mais feliz desde que você chegou... e não importa a idade, seja sempre amável, amig$_oa, prestativ$_oa e cuidados$_oa com as coisas de Deus.<br/>
			<br/>
			Muitas felicidades!"
        )
        ,array(
            "sub" => "Que Deus Te Abençoe Amigo",
            "msg" => "Que os anos que se somam<br/>
                        à sua vida não sejam um peso,<br/>
                        mas que façam parte de uma<br/>
                        infinita conta de novas experiências,<br/>
                        que te façam crescer e aprender<br/>
                        a viver cada vez melhor.<br/>
                        <br/>
                        Que neste dia, a felicidade<br/>
                        se faça presente, m$_mm amig$_oa $_np!<br/>
                        Que as bênçãos do céu<br/>
                        esteja sobre a sua vida,<br/>
                        e que a paz que te desejo hoje<br/>
                        te acompanhe sempre!<br/>
                        Saiba que vou estar<br/>
                        sempre ao seu lado,<br/>
                        hoje e sempre.<br/>
                        <br/>
                        Feliz Aniversário!" 
        )
        ,array(
            "sub" => "Confie em Deus",
            "msg" => "Quando você chorar,<br/>
                    Ele secará as suas lágrimas.<br/>
                    Quando você dá risadas, Ele sorri.<br/>
                    Sempre que você ora,<br/>
                    Ele escuta cada palavra.<br/>
                    Quando você deseja andar<br/>
                    No Seu caminho,<br/>
                    Ele sempre guiará você.<br/>
                    Quando as circunstâncias<br/>
                    Lhe dizem que é impossível,<br/>
                    Deus lhe garante que é possível.<br/>
                    <br/>
                    Confie sempre em Deus<br/>
                    Como seu melhor amigo e<br/>
                    Ele nunca desamparará você.<br/>
                    <br/>
                    Feliz aniversário $_np!" 
        )
        ,array(
            "sub" => "Não Há Limites para Deus",
            "msg" => "Que o nosso Deus tem o poder de fazer o impossível, nós já sabemos, mas é sempre bom relembrar que quando o momento certo chega, não existem limites para Ele atuar.<br/>
            <br/>
            O amor de Deus é o nosso combustível que nos mantém vivos, é o que nos protege do mal e faz a felicidade entre com todas as forças nas nossas vidas.<br/>
            <br/>
            Não há problema grande o suficiente neste mundo que não possa ser solucionado por Ele, quando o medo se abrigar em seu coração, lembre-se de tudo que Ele pode fazer e rapidamente todas as aflições irão se desfazer.<br/>
            <br/>
            Espero que você continue seguindo os ensinamentos do Senhor, pois com Ele ao seu lado, sua vida será preenchida de muita alegria e felicidade. Que as orações sejam atendidas e qualquer batalha que enfrente seja totalmente vencida.<br/>
            <br/>
            Você é muito querid$_oa e certamente Deus tem um plano cheio de bênçãos para a sua vida. Que jamais lhe falte saúde e todos os seus sonhos possam virar realidade.<br/>
            <br/>
            Espero que tenha um dia abençoado! Desejo a você muitos anos de vida e um feliz aniversário!<br/>
            <br/>
            Parabéns $_np!" 
        )
        ,array(
            "sub" => "Parabéns, meu irmão de fé!",
            "msg" => "Deus está sempre colocando as pessoas certas no nosso caminho, são os anjos da guarda que Deus envia para a terra e que chama de amigos. Você foi um anjo enviado por Deus para fazer parte da vida de muitas pessoas e trazer alegria, paz e sabedoria para nós.<br/>
            <br/>
            Você é uma pessoa abençoada e iluminada, $_uu bo$_bb filh$_oa de Deus, $_uu grande $_ii de fé. Sou feliz por ter alguém como você presente em minha existência. Neste dia muito especial que é o dia do seu aniversário, eu desejo que Deus derrame sobre você a Sua glória e amor, que lhe proteja e lhe dê muitas felicidades e paz.<br/>
            <br/>
            Você é uma pessoa especial e merece tudo de bom que a vida tem para lhe dar. Parabéns e muitas felicidades. Que a paz do Senhor esteja sempre com você.<br/>
            <br/>
            Parabéns $_np!" 
        )
        ,array(
            "sub" => "Celebre com alegria e gratidão, amigo",
            "msg" => "Feliz aniversário, m$_mm amig$_oa $_np!<br/>
            <br/>
            Espero que celebre este dia com muita alegria e ao lado dos que mais ama. E também que agradeça ao Senhor por mais um ano de vida, e por todas as bênçãos que recebe diariamente.<br/>
            <br/>
            Eu todos os dias agradeço por Deus ter colocado no meu caminho $_uu amig$_oa tão maravilhos$_oa quanto você. E espero que o Senhor continue iluminando seu caminho e o abençoe hoje e sempre!" 
        )
        ,array(
            "sub" => "Você é um presente de Deus ao mundo",
            "msg" => "M$_mm amig$_oa $_np, a sua existência é apenas mais um exemplo de como Deus é bom e generoso, pois deu de presente ao mundo uma pessoa tão especial quanto você. Feliz aniversário!<br/>
            <br/>
            Sua amizade é para mim uma preciosidade que quero estimar pela vida toda. Saber que posso contar com você é um conforto e uma alegria, pois gosto, admiro e respeito muito você.<br/>
            <br/>
            Que o Senhor o abençoe com muitos anos de vida, e que por todos eles estejamos sempre juntos. Que o seu caminho seja sempre iluminado por muita felicidade. Que hoje e sempre sua fé se fortaleça e a Palavra de Deus seja seu guia.<br/>
            <br/>
            Parabéns, amig$_oa!" 
        )
        ,array(
            "sub" => "Um Coração de Ouro, M$_mm Amig$_oa",
            "msg" => "Feliz Aniversário, amig$_oa!<br/>
            <br/>
            Você é uma pessoa maravilhosa. Tem um coração de ouro e um jeito de encarar a vida e de enxergar as pessoas que é muito revelador. Sua bondade é evidente porque você vive seguindo a palavra do Senhor e isso é a maior dádiva da existência.<br/>
            <br/>
            Desejo que você conheça toda felicidade e paz no dia de hoje. Gosto muito de você, m$_mm bo$_bb amig$_oa $_np!" 
        )
        ,array(
            "sub" => "Teu Aniversário É...",
            "msg" => "...um dia muito especial, pois foi o dia que Deus escolheu para você conhecer este mundo, e nos presenteou com uma pessoa tão positiva e contagiante.<br/>
            <br/>
            ...sempre um momento de alegria, pois quem está ao seu lado neste dia tem o privilégio de dar boas risadas e a chance de viver muitos instantes de felicidade.<br/>
            <br/>
            ...uma ótima oportunidade de agradecer a Deus por mais um ano de vida e por tudo que alcançou como todo seu esforço e energia.<br/>
            <br/>
            ...um dos meus dias preferidos para dizer como amo você, e principalmente para lhe desejar ainda mais felicidade, saúde e sonhos realizados nesta vida!<br/>
            <br/>
            Parabéns $_np!" 
        )
        ,array(
            "sub" => "Aniversário Abençoado",
            "msg" => "Neste aniversário e durante todo o ano,<br/>
            Que a força do Senhor possa guiá-lo,<br/>
            O poder do Senhor o defenda,<br/>
            A sabedoria do Senhor o ensine,<br/>
            A Bíblia o encoraje e alimente,<br/>
            O olho do Senhor esteja sobre você,<br/>
            O ouvido do Senhor possa ouvi-lo,<br/>
            A palavra do Senhor lhe dê a fala,<br/>
            A mão do Senhor lhe proteja,<br/>
            O caminho do Senhor esteja <br/>
            Sempre debaixo dos seus pés.<br/>
            Parabéns $_np!" 
        )
        ,array(
            "sub" => "Deus Nunca te abandona",
            "msg" => "Deus é louco por você, sabia disso? Você não é um acidente de percurso. Você foi planejado com muito cuidado e amor, nos mínimos detalhes. Você é uma obra de arte divina, assinada por Deus.<br/>
            <br/>
            O amor dele por você é tão grande que foi capaz de levar Jesus à cruz para morrer em seu lugar. Muitas vezes, especialmente nos momentos mais difíceis da vida - quando surge uma doença, um acidente, alguém querido morre - é muito difícil perceber o cuidado de Deus conosco.<br/>
            <br/>
            Mas Deus está com você em qualquer lugar, em todos os momentos, sob quaisquer circunstâncias. Ele jamais abandona você. Nunca esqueça disso!<br/>
            <br/>
            Feliz Aniversário $_np!" 
        )
        ,array(
            "sub" => "Que Deus continue guiando seus passos",
            "msg" => "Feliz aniversário! Deus em toda sua bondade concedeu a você mais um ano de vida. E com certeza foi um ano de aprendizagens, de momentos bons e outros nem tanto.<br/>
            <br/>
            Mas todos contam, pois ajudaram a fazer de você uma pessoa mais sábia e forte. E por todos esses momentos você deve agradecer ao Senhor.<br/>
            <br/>
            Que Ele continue guiando seus passos pelos bons caminhos, e a Palavra de Deus continue sendo sua força e inspiração. Que o Senhor cubra você de bênçãos e lhe dê uma vida longa e muito feliz. Parabéns!" 
        )
        ,array(
            "sub" => "Deus lhe concedeu mais um ano de vida",
            "msg" => "Alegre-se, pois Deus lhe concedeu<br/>
            A bênção de mais um ano de vida.<br/>
            Agradeça por tudo, pois o Senhor<br/>
            É seu maior amigo e nunca<br/>
            Abandonará você. Celebre este dia<br/>
            Na paz de Cristo. Feliz aniversário $_np!<br/>
            Que Deus lhe dê saúde, paz e<br/>
            Mais anos de vida abençoados." 
        )
    );

    $ass ="<br/>
            <br/>
            <br/>
            MARANATA!
            <br/>
            <br/>
            Com carinho,<br/>
            <br/>"
            . ( isset($aP["nd"]) ? $aP["nd"]."<br/>" : "" ) .
            "<small>Clube Pioneiros - IASD Capão Redondo</small>";

    $i = rand(0, count($aB)-1 );

    return array( "sub" => $aB[$i]["sub"], "msg"=> 
        str_replace( array("&lt;", "&gt;"), array("<", ">"), htmlentities($aB[$i]["msg"].$ass ."</p>", ENT_NOQUOTES, 'UTF-8', false) )
    );
}
//$r = getBirthdayMessage( array( "np" => "Ricardo", "id" => 44, "sx" => "M", "nd" => "Ricardo Jonadabs César" ) );
//echo $r["msg"];
?>