<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <!-- Define o conjunto de caracteres como UTF-8, suportando acentos e cedilha -->
    <meta charset="UTF-8">
    <!-- Responsivo: ajusta o layout ao tamanho da tela -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Vendas Assíncrono</title>
    <!-- Ícone da aba do navegador -->
    <link rel="icon" type="image/x-icon" href="../assets/favicon.ico">
    <!-- Bootstrap: framework CSS para estilização rápida e responsiva -->
    <link rel="stylesheet" href="bootstrap/css/bootstrap.css">
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <!-- Container principal com padding vertical -->
    <div class="container py-4">
        <!-- Botão de voltar -->
        <div class="mb-4">
            <a href="../index.html" class="btn btn-secondary">Voltar</a>
        </div>

        <!-- Título centralizado -->
        <h2 class="mb-4 text-center">Consultar Vendas Assíncrono (AJAX + JSON)</h2>

        <!-- Container flexível para centralizar formulários -->
        <div class="d-flex flex-column align-items-center">

            <!-- ======================
                 Formulário de consulta por funcionário
                 ====================== -->
            <div class="mb-5 w-100" style="max-width: 500px;">
                <form id="formFuncionario" class="d-flex flex-column">
                    <label for="funcionario" class="form-label"><strong>Por Funcionário:</strong></label>
                    <select class="form-select mb-3" name="funcionario" id="funcionario" required>
                        <!-- Opção padrão -->
                        <option value=""><<< Escolha um Funcionário >>></option>
                        <!-- Opção para todos os funcionários -->
                        <option value="todos">Todos</option>
                    </select>
                    <!-- Botão de envio -->
                    <button type="submit" class="btn btn-primary">Consultar</button>
                </form>
            </div>

            <!-- ======================
                 Formulário de consulta por ano/mês
                 ====================== -->
            <div class="w-100" style="max-width: 500px;">
                <form id="formData">
                    <label class="form-label"><strong>Por Ano/Mês:</strong></label>
                    <div class="d-flex gap-2">
                        <!-- Select de anos -->
                        <select class="form-select" name="ano" id="ano" required>
                            <option value=""><<< Escolha o Ano >>></option>
                            <option value="todos">Todos</option>
                        </select>

                        <!-- Select de meses -->
                        <select class="form-select" name="mes" id="mes" required>
                            <option value=""><<< Escolha o Mês >>></option>
                            <option value="todos">Todos</option>
                        </select>

                        <!-- Botão de envio -->
                        <button type="submit" class="btn btn-primary">Consultar</button>
                    </div>
                </form>
            </div>

        </div>

        <!-- ======================
             Área de resultados
             ====================== -->
        <div class="mt-5">
            <h4>Resultados:</h4>
            <!-- <pre> mantém a formatação do JSON -->
            <pre id="resultado" class="border p-3 bg-light"></pre>
        </div>
    </div>

    <!-- ======================
         Script JavaScript
         ====================== -->
    <script>
    // Espera o DOM carregar completamente antes de manipular elementos
    document.addEventListener("DOMContentLoaded", () => {
        // Elemento onde os resultados da consulta serão exibidos
        const resultado = document.getElementById("resultado");

        // ======================
        // Popula dinamicamente o select de anos
        // ======================
        const anoSelect = document.getElementById("ano");
        for (let i = 2024; i <= 2034; i++) {
            let opt = document.createElement("option"); // cria a option
            opt.value = i; // valor enviado ao backend
            opt.textContent = i; // texto visível ao usuário
            anoSelect.appendChild(opt); // adiciona ao select
        }

        // ======================
        // Popula dinamicamente o select de meses
        // ======================
        const mesSelect = document.getElementById("mes");
        for (let i = 1; i <= 12; i++) {
            let opt = document.createElement("option");
            opt.value = i;
            opt.textContent = i;
            mesSelect.appendChild(opt);
        }

        // ======================
        // AJAX para carregar funcionários
        // ======================
        // fetch() é usado para fazer requisições assíncronas (AJAX) ao servidor sem recarregar a página
        fetch("consultafuncionario.php?consultar=lista") // envia requisição GET para o PHP
            .then(resp => resp.json()) // converte a resposta JSON em objeto JS
            .then(data => {
                if (data.status === "ok") {
                    // Se a resposta for positiva, adiciona cada funcionário ao select
                    let select = document.getElementById("funcionario");
                    data.funcionarios.forEach(nome => {
                        let opt = document.createElement("option");
                        opt.value = nome; // valor que será enviado ao backend
                        opt.textContent = nome; // texto exibido
                        select.appendChild(opt);
                    });
                } else {
                    // Caso haja algum erro enviado pelo PHP
                    alert("Erro: " + data.msg);
                }
            })
            .catch(err => {
                // Captura erros de rede ou falha do fetch
                alert("Erro ao carregar funcionários: " + err);
            });

        // ======================
        // Formulário de consulta por funcionário
        // ======================
        document.getElementById("formFuncionario").addEventListener("submit", e => {
            e.preventDefault(); // impede reload da página
            const formData = new FormData(e.target); // coleta os dados do formulário

            // Envia requisição POST de forma assíncrona
            fetch("consultafuncionario.php?consultar=nome", {
                method: "POST", // método HTTP
                body: formData   // corpo da requisição
            })
            .then(resp => resp.json()) // converte resposta JSON
            .then(data => {
                if (data.status === "ok") {
                    // Mostra os dados no <pre> com identação
                    resultado.textContent = JSON.stringify(data.dados, null, 2);
                } else {
                    // Mostra erro retornado pelo PHP
                    resultado.textContent = "Erro: " + data.msg;
                }
            })
            .catch(err => {
                // Captura falhas de rede ou erro no fetch
                resultado.textContent = "Erro na requisição: " + err;
            });
        });

        // ======================
        // Formulário de consulta por ano/mês
        // ======================
        document.getElementById("formData").addEventListener("submit", e => {
            e.preventDefault(); // evita reload
            const formData = new FormData(e.target); // coleta valores de ano e mês

            // Requisição assíncrona para o PHP
            fetch("consultafuncionario.php?consultar=data", {
                method: "POST",
                body: formData
            })
            .then(resp => resp.json()) // converte para objeto JS
            .then(data => {
                if (data.status === "ok") {
                    // Exibe resultados formatados
                    resultado.textContent = JSON.stringify(data.dados, null, 2);
                } else {
                    resultado.textContent = "Erro: " + data.msg;
                }
            })
            .catch(err => {
                // Captura erros de rede ou execução do fetch
                resultado.textContent = "Erro na requisição: " + err;
            });
        });
    });
    </script>
</body>
</html>