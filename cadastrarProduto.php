<?php
include "templates/upLogin.inc.php";


//all
function produto($ID = null) {

    //js para esta pagina em especifico
    echo "<script src='js/cadastrarProdutoFormulario.js' type='text/javascript'></script>";
    echo "<script src='js/cadastrarProdutoFiltro.js' type='text/javascript'></script>";

    echo "<table id='gradient-style' class='produto' style='width:auto;'>";

    if ($ID) {
        echo "<form name='formCadastraProduto' method='post' action='cadastrarProduto.php' enctype='multipart/form-data' onSubmit='return filtro();'>";
        echo "<input type='hidden' name='op' value='editar'>";
        echo "<input type='hidden' name='id' value='$ID'>";
        $op="editar";
        echo "<tr>";
        echo "<th colspan='2'>";
        echo "<th valign='middle'>Produto $ID";

        $instrucao = "select * from produto where id='$ID'";
        $sql = query($instrucao);
        extract(mysqli_fetch_assoc($sql));
        //id nome modelo id_categoria id_subcategoria id_marca quantidade_minima
        //descricao rate_click rate_compra valor_compra pis cofins icms ipi outros
        //ml1por ml2por ml3por

    } else {
        echo "<form name='formCadastraProduto' method='post' action='cadastrarProduto.php' enctype='multipart/form-data' onSubmit='return filtro();'>";
        echo "<input type='hidden' name='op' value='novo'>";
        $op = "novo";

        $nome = $busca = $modelo = $cod_barra = $id_categoria = $id_subcategoria = $id_marca = $quantidade_minima = $descricao = $rate_click = $rate_compra =
        $mlpor = $descMaxpor = $id_volume = $qtd_minima = $qtd_estoque = $contabilizar_estoque = $ponto_troca = $hp_dias = $hp_hora_inicio = $hp_hora_final = $status = null;
        $valor_compra = $ponto_valor = "00,00";
        echo "<tr>";
        echo "<th colspan='2'>";
        echo "<th>Cadastrar Produto";
    }
    $msg= "Deixando este campo marcado você está permitindo ao usuário efetuar venda deste produto no orçamento e no PDV. ";
    $msg.= "Desmarcando você bloqueará qualquer tipo de venda e busca deste prouto no orçamento e PDV.";
    echo ajudaTool($msg);

    if(!$busca){
        if($ID){
            checkbox('busca', 0);
        }else{
            checkbox('busca', 1);
        }
    }else{
        checkbox('busca', 1);
    }
    echo "</th>";

    echo "<th colspan='2' align='center' valign='middle'>";
    if($status and $ID){
        $cod = "<span style='float:right;'>";
        $cod .= "<a href='cadastrarProduto.php?op=deletar&id=".base64_encode($id)."' title='Deletar produto'><img src='img/deletar.png'></a>";
        $cod .= "</span>";
    }elseif(!$status and $ID){
        $cod = "<span style='float:right;'>";
        $cod .= "<a href='cadastrarProduto.php?op=inserir&id=".base64_encode($id)."' title='Inserir produto'><img src='img/inserir.png'></a>";
        $cod .= "</span>";
    }else{
        $cod = "";
    }
    echo $cod;
    echo "</th>";
    echo "</tr>";



    echo "<tr>";
    echo "<td colspan='2'>Marca<a href='javascript:void(0)' ".pop("marcaProduto.php")." title='Cadastrar nova marca'><img class='imgHelp' src='img/mais.png'></a><br><select name='marca'>";
    echo opcaoSelect("marca", "1", "Ativo", $id_marca, null, "order by nome");
    echo "</select></td>";
    echo "<td>";
    echo "Categoria<a href='javascript:void(0)' ".pop("categoriaProduto.php")." title='Cadastrar nova categoria'><img class='imgHelp' src='img/mais.png'></a><br>";
    //echo "<select name='categoria' onchange='subCatShow();'>";
    echo "<select name='categoria'>";
    echo opcaoSelect("categoria", "1", "Ativo", $id_categoria, null, "order by nome");
    echo "</select>";
    echo "</td>";
    echo "<td>";
    echo "<input type='hidden' name='subcategoria' value='$id_subcategoria'>";
    echo "<span id='selectSubCat'>";
    echo "Sub Categoria<a href='javascript:void(0)' ".pop("subCategoriaProduto.php")." title='Cadastrar nova sub categoria'><img class='imgHelp' src='img/mais.png'></a><br>";
    $sql = query("select id_categoria as idCategoria from sub_categoria where id_categoria='$id_categoria'");
    if (mysqli_num_rows($sql)>0) {
        echo "<select name='SUBCATEGORIA' onchange='mudaSubCat(this.value)'>";
        echo opcaoSelect("sub_categoria", "2", "Ativo", $id_subcategoria, NULL, "and id_categoria='$id_categoria' order by nome");
    } else {
        echo "<select name='SUBCATEGORIA' onchange='mudaSubCat(this.value)' disabled='yes'>";
    }
    echo "</select>";
    echo "</span>";
    echo "</td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td colspan='2'>";
    $js = "verificaNomeProduto(this.value);";
    echo "Nome<br><input type='text' name='nome' onBlur='$js' value='$nome'><input type='hidden' name='verificaNome' value='true'><span style='display:inline-block;' id='checkNome'></span>";
    echo "</td>";
    echo "<td>";
    echo "Modelo<br><input type='text' name='modelo' value='$modelo'>";
    echo "</td>";
    echo "<td>";
    echo "Código de barras<br><input type='text' name='cod_barra' value='$cod_barra'>";
    echo "</td>";
    echo "</tr>";

    echo "<tr>";
    $msg = "Está descrição será exibida no módulo e-commerce. Maiores informações tratar com o desenvolvedor do sistema.";
    echo "<td colspan='4'>Descrição do Produto".ajudaTool($msg)."<textarea class='ckeditor' name='descricao'>$descricao</textarea></td>";
    echo "</tr>";

    //estoque
    echo "<tr>";
    echo "<th colspan='4'>Estoque ";
    $titulo = "Deixe esta a opção estoque marcada caso deseje contabilizar este produto em estoque.<br>";
    $titulo .= "Por exemplo; Quando uma venda for efetuada (caso está opção esteja marcada) será ";
    $titulo .= "dado baixa deste produto automaticamente em estoque.";
    echo "<a href='javascript:void(0)' title='$titulo'>";
    echo ajudaTool($titulo);
    echo "</a>";
    echo "</th>";
    echo "</tr>";
    echo "<tr>";
    echo "<td colspan='1'>";
    if($contabilizar_estoque){
        checkbox('contabilizar', 1, 'mudaValor(this.value);');
        $class="";
    }else{
        checkbox('contabilizar', 0, 'mudaValor(this.value);');
        $class="class='inputValor'";
    }
    echo "</td>";
    echo "<td colspan='1'>";
    echo "Volume <a href='javascript:void(0)' ".pop("volume.php")." title='Adicionar um volume'><img class='imgHelp' src='img/mais.png'></a><br>";
    echo "<select name='volume' $class>";
    echo opcaoSelect("produto_volume", 2, "Ativo", $id_volume);
    echo "</select>";
    echo "</td>";
    $msg = "Quando o estoque atingir a quantidade mínima será mostrado um aviso para a administração do sistema.";
    echo "<td>Quantidade Mínima em estoque".ajudaTool($msg)."<br><input type='text' name='qtd_minima' $class value='$qtd_minima' ".mascara("Valor2")."></td>";
    echo "<td>Quantidade atual em estoque<br><input type='text' name='qtd_estoque' $class value='$qtd_estoque' ".mascara("Valor2")."></td>";
    echo "</tr>";


    //tributação e tributação
    echo "<tr>";
    echo "<th colspan='4' align='center'>Preço de compra e Tributação</th>";
    echo "</tr>";

    echo "<tr>";
    echo "<td colspan='3'></td>";
    $js = "totalCusto(); ";
    echo "<td>Valor de Compra<br><input type='text' value='".real($valor_compra)."' class='totalValor preco' name='valorCompra' ".mascara("Valor2", null, null, $js, $js, $js)."></td>";
    echo "</tr>";

    $sql = query("select * from produto_tributacao where id_produto='$ID'");
    mysqli_num_rows($sql) == 0 ? $qtdTributacao = 1 : $qtdTributacao = mysqli_num_rows($sql);
    echo "<tr>";
    echo "<td>";
    echo "<a href='#campoItem' class='adicionarCampo' title='Adicionar Tributação'><img src='img/mais.png'></a>";
    echo "<input type='hidden' name='qtdTributacao' id='qtdTributacao' value='$qtdTributacao'>";
    echo "</td>";
    echo "<td>Nome da tributação</td>";
    $msg = "Se for selecionado \"Valor Real\" a tributação será somada com o preço de compra do produto.";
    $msg .= "Se for selecionado \"Porcentagem\" a tributação será calculada em forma de porcentagem em cima do valor do produto.<br>";
    echo "<td>Tipo da tributação".ajudaTool($msg)."</td>";
    echo "<td>Valor da tributação</td>";
    echo "</tr>";

    for($i=1, $tributacao = 0; $i<=99; $i++){
        if(mysqli_num_rows($sql)>=$i){
            extract(mysqli_fetch_assoc($sql));
        }else{
            $nome = $tipo_valor = $valor = null;
        }
        if($i>$qtdTributacao){
            $style="style='display:none;'";
        }else{
            $style="";
        }
        echo "<tr class='tributacao' id='trTributacao_$i' $style>";
        echo "<td>";
        echo "<a href='#campoItem' class='removerCampo' title='Remover Tributação'><img src='img/menos.png' width='30'></a>";
        echo "</td>";
        echo "<td><input type='text' name='tributacao[]' id='tributacao_$i' value='$nome'></td>";
        echo "<td>";
        echo "<select name='tipo_valor[]' id='tipoValor_$i' onchange=\"mudaTributacao(this);\">";
        if($tipo_valor==0){
            echo "<option value='0' selected='yes'>Valor Real</option>";
            echo "<option value='1'>Porcentagem</option>";
        }else{
            echo "<option value='0'>Valor Real</option>";
            echo "<option value='1' selected='yes'>Porcentagem</option>";
        }
        echo "</select>";
        echo "</td>";
        echo "<td><input type='text' name='tributacaoValor[]' ".mascara("Valor2", null, null, $js, $js, $js)." id='tributacaoValor_$i' class='totalValor preco' value='".real($valor)."'>";
        echo "</tr>";
        if($tipo_valor==0){
            $tributacao += $valor;
        }elseif($tipo_valor==1){
            $tributacao += $valor_compra * $valor / 100;
        }
    }

    echo "<tr>";
    echo "<td colspan='3'></td>";
    echo "<td>Tributação<br><input class='totalValor preco inputValor' type='text' value='".real($tributacao)."' name='tributacaoTotal' autocomplete='off'></td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td colspan='3'></td>";
    $valor_custo = $tributacao + $valor_compra;
    echo "<td>Total de Custo<br><input class='totalValor preco inputValor' type='text' value='".real($valor_custo)."' name='valorCusto' autocomplete='off'></td>";
    echo "</tr>";

    echo "<tr>";
    echo "<th colspan='4' align='center'>Preço de venda</th>";
    echo "</tr>";

    echo "<tr>";
    echo "<td colspan='2'></td>";
    $jsPor = "valorReal(\"false\");";
    $jsReal = "valorPor(\"false\");";
    echo "<td>Margem de Lucro de venda<br><input type='text' value='".real($mlpor)."' class='totalValor porcentagem' name='mlpor' ".mascara("Valor2", null, null, $jsPor, $jsPor, $jsPor)."></td>";
    $ml = round(($mlpor / 100 * $valor_custo), 2);
    echo "<td>Valor final sem desconto<br><input type='text' class='totalValor preco' name='ml' value='".real($ml)."' ".mascara("Valor2", null, null, $jsReal, $jsReal, $jsReal)."></td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td colspan='2'></td>";
    $jsPor = "valorReal(\"true\");";
    $jsReal = "valorPor(\"true\");";
    echo "<td>Desconto Max. Permitido para venda<br><input type='text' value='".real($descMaxpor)."' class='totalValor porcentagem' name='descMaxpor' ".mascara("Valor2", null, null, $jsPor, $jsPor, $jsPor)."></td>";
    $descMax = round($ml - ($descMaxpor / 100 * $ml), 2);
    echo "<td>Valor final com desconto<br><input type='text' class='totalValor preco' name='descMax' value='".real($descMax)."' ".mascara("Valor2", null, null, $jsReal, $jsReal, $jsReal)."></td>";
    echo "</tr>";

    $styleHappyHour = "style='display:none;'";
    $sqlAdministrativo = query("select valor from administrativo where taxonomia='happyhour'");
    if(mysqli_num_rows($sqlAdministrativo)){
        extract(mysqli_fetch_assoc($sqlAdministrativo));
        if($valor){
            $styleHappyHour = "";
        }
    }
    $hp_dom = 1;
    echo "<style type='text/css'>
        .hp{
            display:inline-block;
            text-align: center;
            padding-left: 8px;
            padding-right: 8px;
        }
    </style>";

    echo "<tr $styleHappyHour>";
    echo "<td colspan='2' align='right'>Happy hour</td>";
    echo "<td style='vertical-align:middle;'>";
    $hp_dias = explode(',', $hp_dias);
    $dias = array('DOM', 'SEG', 'TER', 'QUA', 'QUI', 'SEX', 'SAB');
    for ($i=0; $i<7; $i++){
        if(in_array($i, $hp_dias)){
            echo "<span class='hp'>".$dias[$i]."<br><input type='checkbox' name='hp_dia[]' value='$i' checked='yes'></span>";
        }else{
            echo "<span class='hp'>".$dias[$i]."<br><input type='checkbox' name='hp_dia[]' value='$i'></span>";
        }
    }
    echo "</td>";
    echo "<td>";
    echo "<span class='hp'>Inicio<br><input type='text' name='hp_hora_inicial' value='$hp_hora_inicio' ".mascara('Hora', '5')." placeholder='inicio do happy hour' onblur='calcularhora();'></span>";
    echo "<span class='hp'>Fim<br><input type='text' name='hp_hora_final' value='$hp_hora_final' ".mascara('Hora', '5')." placeholder='final do happy hour' onblur='calcularhora();'></span>";
    echo "</td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td colspan='2'></td>";
    $msg = "Deixando esta opção marcada você estará permitindo a troca dele por pontos.";
    echo "<td>Peritir troca por pontos ".ajudaTool($msg)."<br><select name='ponto_troca' onchange='mudaPonto(this.value)'>";
    if($ponto_troca){
        echo "<option value='1' selected='yes'>Sim</option>";
        echo "<option value='0'>Não</option>";
        $class='';
    }else{
        echo "<option value='1'>Sim</option>";
        echo "<option value='0' selected='yes'>Não</option>";
        $class='inputValor';
    }
    echo "</select></td>";
    echo "<td>Valor de ponto para troca<br><input type='text' name='ponto_valor' value='".real($ponto_valor)."' class='totalValor ponto $class' ".mascara("Valor2")."</td>";
    echo "</tr>";

    $sql = query("select * from administrativo where taxonomia='controleEstoquePdv' and valor='1'");
    if(mysqli_num_rows($sql)){
        echo "<tr>";
        echo "<th>";
        echo "<a href='#campoItem' class='adicionarCampoItem' title='Adicionar Item'><img src='img/mais.png'></a>";
        echo "</th>";
        echo "<th colspan='4'>Ao produzir este produto, foi necessário utilizar quais outros produtos?</th>";
        echo "</tr>";

        $sql = query("select * from produto_subestoque where id_produto='$ID'");
        $qtdProdutoSubEstoque = mysqli_num_rows($sql);
        echo "<input type='hidden' id='qtdProdutoSubEstoque' name='qtdProdutoSubEstoque' value='$qtdProdutoSubEstoque'>";

        for($i=1; $i<=99; $i++){
            $qtdProdutoSubEstoque>=$i ? extract(mysqli_fetch_assoc($sql)): $id_produto_subestoque = $qtd = null;
            if($i<=$qtdProdutoSubEstoque){
                $style = "";
            }else{
                $style = "style='display:none;'";
            }
            echo "<tr id='trProdutoSubEstoque_$i' $style>";
            echo "<td><a href='#campoItem' class='removerCampoItem' title='Remover Item'><img src='img/menos.png'></a></td>";
            echo "<td colspan='2'>";
            echo "<input type='hidden' name='produtoSubEstoqueId[]' id='produtoSubEstoqueId_$i' value='$id_produto_subestoque'>";
            echo "<input type='text' placeholder='Nome do produto' id='produtoSubEstoqueNome_$i' name='produtoSubEstoque[]' onkeyup='showSubEstSug(this.value, $i)' value='".registro($id_produto_subestoque, 'produto', 'nome')."'>";
            echo "<div class='suggestionsBox' id='produtoSubEstoqueSug_$i' style='display: none;'><span style='float:right;'><input type='button' id='deletar' value='X' onclick=\"lookupOff();\"></span>";
            echo "<div class='suggestionList' id='produtoSubEstoqueSugList_$i'></div></div>";
            echo "</td>";
            echo "<td colspan='2'><input type='text' placeholder='Quantidade' name='produtoSubEstoqueQtd[]' value='$qtd' ".mascara('Valor')."></td>";
            echo "</tr>";
        }



    }


    $sql = query("select * from produto_imagem where id_produto='$ID'");
    $imagensQtd = mysqli_num_rows($sql);

    if($ID and $op=="editar"){
        echo "<tr>";
        echo "<th colspan='5'><a href='javascript:void(0)' ".pop("produto_imagem_upload.php?id=$ID", 600, 700)." title='Adicionar imagens para $nome'><img class='imgHelp' src='img/mais.png'></a> Imagens deste produto</th>";
        echo "<tr>";
    }
    if($imagensQtd>0 and $op =="editar"){
        echo "<tr>";
        echo "<td colspan='5' align='center'>";

        echo showImagemProduto($ID, 8, 0, true);

        echo "</td>";
        echo "</tr>";
    }

    //submit
    echo "<tr id='formE6'>";
    echo "<th colspan='3'></th>";
    echo "<th align='right'><input type='submit' class='btnEnviar' value='Enviar'> <input type='reset' value='Cancelar'></th>";
    echo "</tr>";

    echo "</table>";
    echo "</form>";

}

extract($_POST);
extract($_GET);
//all
$info = "";
##se post for de op novo
//op nome modelo marca categoria subcategoria descricao valorCompra
//pis cofins icms ipi outros ml1por ml2por ml3por
##se post for de op editar
//op id
##se post for de op visualizar
//op id
if(!isset($op)){
	
	produto();
	
}else{
	
	if($op == "novo" or $op == "editar") {
		
		$qtd_minima = turnZero($qtd_minima);
        $qtd_estoque = real2($qtd_estoque);
        $qtd_minima = real2($qtd_minima);
        $valorCompra = real2($valorCompra);
		$mlpor = real2($mlpor);
		$descMaxpor = real2($descMaxpor);
		$ponto_valor = real2($ponto_valor);
        $hp_dia = implode(',',$hp_dia);
		
		if (!isset($id)) {
			$id = "";
		}
		if (!isset($subcategoria)) {
			$subcategoria = "";
		}
	
		$subcategoria = turnZero($subcategoria);
		$volume = turnZero($volume);
		$qtd_minima = turnZero($qtd_minima);
		$qtd_estoque = turnZero($qtd_estoque);
		!isset($contabilizar)? $contabilizar = 0: $contabilizar = turnZero($contabilizar);
		!isset($busca)? $busca = 0: $busca = turnZero($busca);
		
	
		$valida = true;
	
		//filtro das variaveis
		if (mysqli_num_rows(query("select nome from produto where nome='$nome'")) and $op == "novo") {
			echo "Já existe um produto com esse nome: $nome.<br>";
			$valida = false;
		}
		if (mysqli_num_rows(query("select nome from produto where cod_barra='$cod_barra'")) and $op == "novo" and $cod_barra <> "") {
			echo "Já existe um produto com esse Código de Barras.<br>";
			$valida = false;
		}
		if (mysqli_num_rows(query("select nome from produto where nome='$nome' and id<>'$id'")) and $op == "editar") {
			echo "Já existe um produto com esse nome: $nome.<br>";
			$valida = false;
		}
		if (mysqli_num_rows(query("select nome from produto where cod_barra='$cod_barra' and id<>'$id'")) and $op == "editar" and $cod_barra <> "") {
			echo "Já existe um produto com esse Código de Barras.<br>";
			$valida = false;
		}
	}
	
	if ($op == "novo" and $valida) {
	
		//insercao no db do produto
		$instrucao = "insert into produto ";
		$instrucao .= "(nome, busca, modelo, cod_barra, id_categoria, id_subcategoria, id_marca, ";
		$instrucao .= "descricao, valor_compra, mlpor, descMaxpor, qtd_minima, qtd_estoque, id_volume, ";
		$instrucao .= "contabilizar_estoque, ponto_valor, ponto_troca, hp_hora_inicio, hp_hora_final, hp_dias) values ";
		$instrucao .= "('$nome', '$busca', '$modelo', '$cod_barra', '$categoria', '$subcategoria', ";
		$instrucao .= "'$marca', '$descricao', '$valorCompra', '$mlpor', '$descMaxpor', '$qtd_minima', '$qtd_estoque', '$volume', ";
		$instrucao .= "'$contabilizar', '$ponto_valor', '$ponto_troca', '$hp_hora_inicial', '$hp_hora_final', '$hp_dia')";
		$sql = query($instrucao);
		$chave_principal = mysqli_insert_id($conexao);
	
		//inserindo na tributação
		$instrucao = "insert into produto_tributacao ";
		$instrucao .= "(id_produto, nome, tipo_valor, valor) values";
		for ($i = 0; $i < $qtdTributacao; $i++) {
			$i != 0 ? $instrucao .= ", " : false;
			$tributacaoValor[$i] = str_replace(",", ".", $tributacaoValor[$i]);
			$instrucao .= "('$chave_principal', '$tributacao[$i]', '$tipo_valor[$i]', '$tributacaoValor[$i]')";
		}
		$sql = query($instrucao);

        //inserir no estoque sub se houver
        if(isset($qtdProdutoSubEstoque)){
            $sql = query('delete from produto_subestoque where id_produto="$chave_principal"');
            
        }
	
		if (mysqli_affected_rows($conexao) > 0) {
	
			$info = "Cadastro do produto efetuado com sucesso.<br>";
	
			$id_usuario = getIdCookieLogin($_COOKIE["login"]);
			$data = date('Y-m-d H:i:s');
			$acao = "Cadastrou um produto.";
			$tabela_afetada = "produto";
	
			insertHistorico($id_usuario, $data, $acao, $tabela_afetada, $chave_principal);
	
			confirmacaoDB($info, "?op=visualizar&id=".base64_encode($chave_principal));
	
			produto($chave_principal);
			
			
	
		} else {
			include "inc/msgErro.inc";
		}
	} elseif ($op == "editar" and $valida) {

		//insercao no db do produto
		$instrucao = "update produto set ";
		$instrucao .= "nome='$nome', busca='$busca', modelo='$modelo', id_categoria='$categoria', id_subcategoria='$subcategoria', ";
		$instrucao .= "id_marca='$marca', descricao='$descricao', valor_compra='$valorCompra', mlpor='$mlpor', descMaxpor='$descMaxpor', ";
		$instrucao .= "contabilizar_estoque='$contabilizar', cod_barra='$cod_barra', qtd_minima='$qtd_minima', qtd_estoque='$qtd_estoque', ";
		$instrucao .= "id_volume='$volume', ponto_valor='$ponto_valor', ponto_troca='$ponto_troca', hp_hora_inicio='$hp_hora_inicial', hp_hora_final='$hp_hora_final', ";
		$instrucao .= "hp_dias='$hp_dia' where id='$id'";
	
		$sql = query($instrucao);
	
		//inserindo na tributação
		$sql = query("delete from produto_tributacao where id_produto='$id'");
		$instrucao = "insert into produto_tributacao ";
		$instrucao .= "(id_produto, nome, tipo_valor, valor) values";
		for ($i = 0; $i < $qtdTributacao; $i++) {
			$i != 0 ? $instrucao .= ", " : false;
			$tributacaoValor[$i] = str_replace(",", ".", $tributacaoValor[$i]);
			$instrucao .= "('$id', '$tributacao[$i]', '$tipo_valor[$i]', '$tributacaoValor[$i]')";
		}
		$sql = query($instrucao);
	
		if (mysqli_affected_rows($conexao) > 0) {
	
			$info = "Alteração do produto efetuado com sucesso.<br>";
	
			$id_usuario = getIdCookieLogin($_COOKIE["login"]);
			$data = date('Y-m-d H:i:s');
			$acao = "Editou um produto.";
			$tabela_afetada = "produto";
			$chave_principal = $id;
	
			insertHistorico($id_usuario, $data, $acao, $tabela_afetada, $chave_principal);
	
			confirmacaoDB($info, "?op=visualizar&id=".base64_encode($id));
	
			produto($id);
	
		} else {
			include "inc/msgErro.inc";
		}
	
	} elseif ($op == "visualizar") {
	
		$id = base64_decode($id);
	
		produto($id);
	
	} elseif ($op == "deletar") {

		$id = base64_decode($id);

		$sql = query("update produto set status='0' where id='$id'");
		//$sql = query("update produto set busca='0' where id='$id'");

		produto($id);

	} elseif ($op == "inserir") {

		$id = base64_decode($id);

		$sql = query("update produto set status='1' where id='$id'");
		//$sql = query("update produto set busca='1' where id='$id'");
		
		produto($id);

	}
}
	
//end all

include "templates/downLogin.inc.php";
?>