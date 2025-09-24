<?php
// Define que a resposta do servidor será em formato JSON (útil para o AJAX interpretar).
header('Content-Type: application/json');

// Inclui o arquivo de conexão com o banco de dados (conecta.inc contém $con).
include 'conecta.inc';


// Define a pasta onde as fotos enviadas serão armazenadas
$dir = "img/"; 


// Captura os dados enviados pelo formulário.
// O operador "??" evita erro caso o campo não tenha sido enviado, definindo valor padrão.
$nome = $_POST["func"] ?? '';
$valor = $_POST["valor"] ?? '';
$segmento = $_POST["segmento"] ?? '';
$arquivo = $_FILES["foto"] ?? null;


// Verifica se todos os campos obrigatórios foram preenchidos.
// Caso falte algum, retorna mensagem de erro em JSON e encerra o código.
if(!$nome || !$valor || !$segmento || !$arquivo){
    echo json_encode(['success'=>false, 'message'=>'Preencha todos os campos']);
    exit;
}


// Tratamento do valor: troca vírgula por ponto (para padrão numérico).
// Exemplo: "1.500,50" → "1500.50"
$valor = str_replace(',', '.', $valor);

// Converte o valor para número decimal (float) para permitir cálculos.
$valor_float = floatval($valor);


// Cálculo do bônus com base no valor da venda.
// Cada faixa de venda tem uma porcentagem diferente.
if($valor_float < 500){
    $bonus = $valor_float * 0.01; // 1%
} elseif($valor_float < 1001){
    $bonus = $valor_float * 0.05; // 5%
} elseif($valor_float < 3001){
    $bonus = $valor_float * 0.10; // 10%
} else {
    $bonus = $valor_float * 0.15; // 15%
}


// Cria um objeto de data com a data atual
$data = new DateTime();

// Extrai o ano atual (ex: 2025)
$ano = $data->format("Y");

// Extrai o mês atual (número de 1 a 12)
$mes = $data->format("n");


// Verifica se já existe um registro para esse funcionário, nesse segmento, mês e ano.
// Isso evita que o mesmo funcionário seja cadastrado duas vezes no mesmo período.
$stmt = $con->prepare("SELECT * FROM tbfuncmes WHERE nome=? AND segmento=? AND mes=? AND ano=?");

// Faz a ligação dos parâmetros à consulta (seguro contra SQL Injection).
$stmt->bind_param("ssii", $nome, $segmento, $mes, $ano);

// Executa a consulta
$stmt->execute();

// Pega o resultado da consulta
$resul = $stmt->get_result();


// Se já existe pelo menos um registro igual, retorna erro e encerra.
if($resul->num_rows > 0){
    echo json_encode(['success'=>false, 'message'=>'Já foi cadastrado as vendas deste funcionário para esse segmento, mês e ano']);
    exit;
}


// Preparação para salvar a foto no servidor.
// Descobre a extensão do arquivo (ex: "jpg", "png").
$ext = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));

// Cria um nome novo e único para a foto, juntando nome do funcionário e data/hora.
$novo_nome = $nome . "_" . date("Ymd_His") . "." . $ext;

// Define o caminho completo onde a foto será salva.
$destino = $dir . $novo_nome;


// Move o arquivo temporário para a pasta "img/" com o novo nome.
// Se der erro no upload, retorna mensagem de erro e encerra.
if(!move_uploaded_file($arquivo["tmp_name"], $destino)){
    echo json_encode(['success'=>false, 'message'=>'Erro ao carregar a foto']);
    exit;
}


// Cria o comando SQL para inserir os dados de forma segura (Prepared Statement).
$insert = $con->prepare("INSERT INTO tbfuncmes (nome, vrvenda, vrbonus, caminhoimg, segmento, mes, ano) VALUES (?, ?, ?, ?, ?, ?, ?)");

// Faz a ligação dos valores reais aos parâmetros do SQL.
// Tipos: s (string), d (double), i (inteiro).
$insert->bind_param("sddssii", $nome, $valor_float, $bonus, $destino, $segmento, $mes, $ano);


// Executa o comando SQL de inserção no banco de dados.
if($insert->execute()){
    // Caso a inserção dê certo, retorna mensagem de sucesso em JSON.
    echo json_encode(['success'=>true, 'message'=>'Venda do Funcionário incluída com sucesso neste mês e ano!']);
} else {
    // Caso haja falha no banco, retorna mensagem de erro.
    echo json_encode(['success'=>false, 'message'=>'Erro ao salvar no banco']);
}


// Fecha os comandos SQL e a conexão para liberar recursos.
$insert->close();
$stmt->close();
$con->close();
?>