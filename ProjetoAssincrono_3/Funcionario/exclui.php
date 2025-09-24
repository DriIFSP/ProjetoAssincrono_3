<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Vendas Assíncrono</title>
        <link rel="icon" type="image/x-icon" href="../assets/favicon.ico">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

<div class="container my-4 p-3">
    <!-- Botão de voltar -->
    <div class="mb-4">
        <a href="../index.html" class="btn btn-secondary">Voltar</a>
    </div>

    <!-- Título centralizado -->
    <h2 class="mb-4 text-center">Excluir Vendas Assíncrono</h2>

    <!-- Container flexível para centralizar a tabela -->
    <div class="d-flex flex-column align-items-center w-100">
        <div id="tabela-container" class="table-responsive w-100"></div>
    </div>
</div>

<script>
// ================================
// Espera até que todo o DOM esteja carregado
// ================================
document.addEventListener("DOMContentLoaded", () => {
    const container = document.getElementById("tabela-container");

    // ================================
    // Função para carregar registros via AJAX
    // ================================
    function carregarVendas() {
        // fetch() envia uma requisição HTTP assíncrona para o PHP
        // Aqui usamos GET para obter todos os registros em formato JSON
        fetch("excluifuncionario.php")
            .then(resp => resp.json()) // Converte a resposta do PHP de JSON para objeto JS
            .then(data => {
                // O PHP retorna sempre um objeto JSON com: status, msg e dados
                // data.status indica se a operação foi bem sucedida
                if (data.status === "ok") {
                    criarTabela(data.dados); // Se OK, monta a tabela com os dados recebidos
                } else {
                    // Exibe uma mensagem de alerta caso não haja registros ou ocorra erro
                    container.innerHTML = `<div class="alert alert-warning">${data.msg}</div>`;
                }
            })
            .catch(err => {
                // Caso a requisição falhe (ex: PHP não responde), exibe mensagem de erro
                container.innerHTML = `<div class="alert alert-danger">Erro ao carregar dados: ${err}</div>`;
            });
    }

    // ================================
    // Função para criar tabela HTML a partir dos dados recebidos em JSON
    // ================================
    function criarTabela(dados) {
        let html = `
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Código</th>
                    <th>Nome</th>
                    <th>Valor de Vendas</th>
                    <th>Valor de Bônus</th>
                    <th>Segmento</th>
                    <th>Mês</th>
                    <th>Ano</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
        `;

        // Para cada item no array de dados (JSON convertido para JS), cria uma linha da tabela
        dados.forEach(item => {
            html += `
                <tr id="linha-${item.codigo}">
                    <td>${item.codigo}</td>
                    <td><strong>${item.nome}</strong></td>
                    <td>R$ ${Number(item.vrvenda).toLocaleString('pt-BR', {minimumFractionDigits: 2})}</td>
                    <td>R$ ${Number(item.vrbonus).toLocaleString('pt-BR', {minimumFractionDigits: 2})}</td>
                    <td>${item.segmento}</td>
                    <td>${item.mes}</td>
                    <td>${item.ano}</td>
                    <td>
                        <!-- O botão chama a função excluirVenda passando o código -->
                        <button class="btn btn-danger btn-sm" onclick="excluirVenda(${item.codigo})">Excluir</button>
                    </td>
                </tr>
            `;
        });

        html += `</tbody></table>`;
        // Insere a tabela completa dentro do container
        container.innerHTML = html;
    }

    // ================================
    // Função para excluir registro via AJAX
    // ================================
    // window.excluirVenda é necessário para tornar a função acessível no onclick do botão
    window.excluirVenda = function(codigo) {
        if (!confirm("Tem certeza que deseja excluir este registro?")) return;

        // FormData é usado para enviar dados via POST como se fosse um formulário
        const formData = new FormData();
        formData.append("codigo", codigo);

        // fetch() envia a requisição POST ao PHP, que excluirá o registro
        fetch("excluifuncionario.php", {method: "POST", body: formData})
            .then(resp => resp.json()) // Converte o JSON retornado pelo PHP em objeto JS
            .then(data => {
                if (data.status === "ok") {
                    // Remove a linha correspondente da tabela sem precisar recarregar a página
                    const linha = document.getElementById(`linha-${codigo}`);
                    if (linha) linha.remove();
                } else {
                    // Exibe mensagem de erro caso a exclusão falhe
                    alert(data.msg);
                }
            })
            .catch(err => {
                // Caso a requisição falhe, exibe alerta
                alert("Erro na requisição: " + err);
            });
    }

    // ================================
    // Carrega os registros assim que a página é aberta
    // ================================
    carregarVendas();
});
</script>
</body>
</html>