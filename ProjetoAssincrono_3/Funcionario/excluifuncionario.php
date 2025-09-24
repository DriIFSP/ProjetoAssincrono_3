<?php
// ==============================================
// excluir.php - Lista e exclui vendas via AJAX
// ==============================================

// Inclui conexão com o banco de dados
include("conecta.inc");

// Define que a resposta será JSON para requisições AJAX
header('Content-Type: application/json');

// -----------------------------
// 1) Processa exclusão se for POST com 'codigo'
// -----------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['codigo'])) {
    $codigo = (int) $_POST['codigo']; // converte para inteiro (segurança)
    
    $sql = "DELETE FROM tbfuncmes WHERE codigo = $codigo";
    
    if (mysqli_query($con, $sql)) {
        echo json_encode(["status" => "ok", "msg" => "Registro excluído com sucesso!"]);
    } else {
        echo json_encode(["status" => "erro", "msg" => "Erro ao excluir: " . mysqli_error($con)]);
    }
    exit; // finaliza para não enviar o HTML abaixo
}

// -----------------------------
// 2) Se não for POST, retorna todos os registros em JSON
// -----------------------------
$res = mysqli_query($con, "SELECT codigo, nome, vrvenda, vrbonus, segmento, mes, ano FROM tbfuncmes");

$dados = [];
if ($res && mysqli_num_rows($res) > 0) {
    while ($row = mysqli_fetch_assoc($res)) {
        $dados[] = $row;
    }
    echo json_encode(["status" => "ok", "dados" => $dados]);
} else {
    echo json_encode(["status" => "erro", "msg" => "Nenhum registro encontrado"]);
}

mysqli_close($con);
exit;
?>