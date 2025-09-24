<?php
// ==============================
// CONFIGURAÇÃO DE ERROS (APENAS PARA TESTE)
// ==============================
// Mostra erros diretamente na tela. Útil em desenvolvimento, mas não deve ser usado em produção.
ini_set('display_errors', 1);
error_reporting(E_ALL);

// ==============================
// DEFINIÇÃO DO CABEÇALHO JSON
// ==============================
// Informa ao navegador que a resposta será no formato JSON.
// Isso permite que o JavaScript (fetch ou AJAX) interprete corretamente os dados.
header('Content-Type: application/json');

// ==============================
// CONEXÃO COM O BANCO DE DADOS
// ==============================
// Inclui o arquivo de conexão (deve conter mysqli_connect)
// $con será usado para executar consultas.
include("conecta.inc");

// ==============================
// CAPTURA DA AÇÃO
// ==============================
// O parâmetro 'consultar' na URL define qual operação será executada: 
// 'lista' = retorna lista de funcionários
// 'nome'  = consulta por nome de funcionário
// 'data'  = consulta por ano/mês
$acao = $_GET['consultar'] ?? '';

// ==============================
// LISTA DE FUNCIONÁRIOS
// ==============================
if ($acao === 'lista') {

    // Consulta SQL para obter nomes distintos de funcionários
    $res = mysqli_query($con, "SELECT DISTINCT nome FROM tbfuncmes");

    // Verifica se a consulta retornou resultados
    if (!$res || mysqli_num_rows($res) < 1) {
        // Se não houver resultados, envia JSON informando erro
        echo json_encode(["status" => "erro", "msg" => "Nenhum funcionário encontrado."]);
        exit; // Encerra a execução
    }

    // Array para armazenar os nomes
    $funcionarios = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $funcionarios[] = $row['nome']; // Adiciona cada nome ao array
    }

    // Retorna os nomes no formato JSON
    echo json_encode(["status" => "ok", "funcionarios" => $funcionarios]);
    exit;
}

// ==============================
// CONSULTA POR NOME
// ==============================
if ($acao === 'nome' && $_SERVER["REQUEST_METHOD"] === "POST") {

    // Captura o nome enviado pelo formulário (POST)
    if (isset($_POST['funcionario'])) 
        $nome = trim($_POST['funcionario']); // remove espaços extras
    else 
        $nome = '';

    // Se não foi informado nenhum funcionário, retorna erro
    if ($nome === '') {
        echo json_encode(["status" => "erro", "msg" => "Funcionário não informado"]);
        exit;
    }

    // Monta a consulta SQL
    // Se for "todos", retorna todos os registros
    if ($nome === "todos") 
        $sql = "SELECT * FROM tbfuncmes";
    else 
        // Se for um nome específico, filtra pelo nome
        $sql = "SELECT * FROM tbfuncmes WHERE nome='$nome'"; // Atenção: pode ser melhor usar prepared statements para segurança

    // Executa a consulta
    $res = mysqli_query($con, $sql);
    if (!$res) {
        // Se houver erro na consulta, retorna mensagem do MySQL
        echo json_encode(["status" => "erro", "msg" => mysqli_error($con)]);
        exit;
    }

    // Armazena os resultados em um array
    $dados = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $dados[] = $row;
    }

    // Retorna os dados em JSON
    echo json_encode(["status" => "ok", "dados" => $dados]);
    exit;
}

// ==============================
// CONSULTA POR ANO/MÊS
// ==============================
if ($acao === 'data' && $_SERVER["REQUEST_METHOD"] === "POST") {

    // Captura ano e mês enviados pelo formulário
    $ano = $_POST['ano'] ?? 'todos';
    $mes = $_POST['mes'] ?? 'todos';

    // Converte para inteiro se não for "todos"
    if ($ano !== 'todos') 
        $ano = intval($ano);
    if ($mes !== 'todos') 
        $mes = intval($mes);

    // Monta a consulta de acordo com os filtros
    if ($ano === 'todos' && $mes === 'todos') {
        // Sem filtros
        $sql = "SELECT * FROM tbfuncmes";
    } elseif ($ano !== 'todos' && $mes === 'todos') {
        // Filtra apenas pelo ano
        $sql = "SELECT * FROM tbfuncmes WHERE ano=$ano";
    } elseif ($ano === 'todos' && $mes !== 'todos') {
        // Filtra apenas pelo mês
        $sql = "SELECT * FROM tbfuncmes WHERE mes=$mes";
    } else {
        // Filtra ano e mês juntos
        $sql = "SELECT *  FROM tbfuncmes WHERE ano=$ano AND mes=$mes";
    }

    // Executa a consulta
    $res = mysqli_query($con, $sql);
    if (!$res) {
        // Retorna erro caso a consulta falhe
        echo json_encode(["status" => "erro", "msg" => mysqli_error($con)]);
        exit;
    }

    // Armazena resultados
    $dados = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $dados[] = $row;
    }

    // Retorna os dados em JSON
    echo json_encode(["status" => "ok", "dados" => $dados]);
    exit;
}

// ==============================
// AÇÃO INVÁLIDA
// ==============================
// Se nenhum parâmetro válido for enviado, retorna erro genérico
echo json_encode(["status" => "erro", "msg" => "Ação inválida"]);
exit;
?>