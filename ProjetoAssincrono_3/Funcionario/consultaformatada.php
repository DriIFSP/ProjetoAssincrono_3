<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Vendas Assíncrono</title>
    <link rel="icon" type="image/x-icon" href="../assets/favicon.ico">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <div class="container py-4">
        <!-- Botão de voltar -->
        <div class="mb-4">
            <a href="../index.html" class="btn btn-secondary">Voltar</a>
        </div>

        <h2 class="mb-4 text-center">Consultar Vendas Assíncrono (AJAX + JSON)</h2>
        <h3>DADOS JSON REMONTADOS EM UMA TABELA</h3>

        <div class="d-flex flex-column align-items-center">

            <!-- Formulário por funcionário -->
            <div class="mb-5 w-100" style="max-width: 500px;">
                <form id="formFuncionario" class="d-flex flex-column">
                    <label for="funcionario" class="form-label"><strong>Por Funcionário:</strong></label>
                    <select class="form-select mb-3" name="funcionario" id="funcionario" required>
                        <option value=""><<< Escolha um Funcionário >>></option>
                        <option value="todos">Todos</option>
                    </select>
                    <button type="submit" class="btn btn-primary">Consultar</button>
                </form>
            </div>

            <!-- Formulário por ano/mês -->
            <div class="w-100" style="max-width: 500px;">
                <form id="formData">
                    <label class="form-label"><strong>Por Ano/Mês:</strong></label>
                    <div class="d-flex gap-2">
                        <select class="form-select" name="ano" id="ano" required>
                            <option value=""><<< Escolha o Ano >>></option>
                            <option value="todos">Todos</option>
                        </select>

                        <select class="form-select" name="mes" id="mes" required>
                            <option value=""><<< Escolha o Mês >>></option>
                            <option value="todos">Todos</option>
                        </select>

                        <button type="submit" class="btn btn-primary">Consultar</button>
                    </div>
                </form>
            </div>

        </div>

        <!-- Área de resultados em tabela -->
        <div class="mt-5">
            <h4>Resultados:</h4>
            <!-- Aqui vamos inserir a tabela dinamicamente -->
            <div id="resultado" class="table-responsive"></div>
        </div>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const resultado = document.getElementById("resultado");

        // ======================
        // Popula anos dinamicamente
        // ======================
        const anoSelect = document.getElementById("ano");
        for (let i = 2024; i <= 2034; i++) {
            let opt = document.createElement("option");
            opt.value = i;
            opt.textContent = i;
            anoSelect.appendChild(opt);
        }

        // ======================
        // Popula meses dinamicamente
        // ======================
        const mesSelect = document.getElementById("mes");
        for (let i = 1; i <= 12; i++) {
            let opt = document.createElement("option");
            opt.value = i;
            opt.textContent = i;
            mesSelect.appendChild(opt);
        }

        // ======================
        // Carrega funcionários via AJAX
        // ======================
        fetch("consultafuncionario.php?consultar=lista")
            .then(resp => resp.json()) // converte JSON para objeto JS
            .then(data => {
                if (data.status === "ok") {
                    let select = document.getElementById("funcionario");
                    data.funcionarios.forEach(nome => {
                        let opt = document.createElement("option");
                        opt.value = nome;
                        opt.textContent = nome;
                        select.appendChild(opt);
                    });
                } else {
                    alert("Erro: " + data.msg);
                }
            })
            .catch(err => {
                alert("Erro ao carregar funcionários: " + err);
            });

        // ======================
        // Função para criar tabela HTML a partir dos dados JSON
        // ======================
        function criarTabela(dados) {
            if (!dados || dados.length === 0) {
                return "<p>Nenhum registro encontrado.</p>";
            }

            // Cria a tabela
            let html = `<table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Foto</th>
                        <th>Nome</th>
                        <th>Ano</th>
                        <th>Mês</th>
                        <th>Valor Venda</th>
                        <th>Valor Bônus</th>
                        <th>Segmento</th>
                    </tr>
                </thead>
                <tbody>`;

            dados.forEach(item => {
                html += `<tr>
                    <td>
                    <img src="${item.caminhoimg}" 
                        alt="Foto de ${item.nome}" 
                        style="width:50px; height:auto;" 
                        class="img-fluid rounded">
                   </td>
                    <td><strong>${item.nome}</strong></td>
                    <td>${item.ano}</td>
                    <td>${item.mes}</td>
                    <td>R$ ${item.vrvenda}</td>
                    <td>R$ ${item.vrbonus}</td>
                    <td>${item.segmento}</td>
                </tr>`;
            });

            html += "</tbody></table>";
            return html;
        }

        // ======================
        // Formulário por funcionário
        // ======================
        document.getElementById("formFuncionario").addEventListener("submit", e => {
            e.preventDefault();
            const formData = new FormData(e.target);

            fetch("consultafuncionario.php?consultar=nome", {
                method: "POST",
                body: formData
            })
            .then(resp => resp.json())
            .then(data => {
                if (data.status === "ok") {
                    // Insere a tabela no HTML
                    resultado.innerHTML = criarTabela(data.dados);
                } else {
                    resultado.innerHTML = "<p>Erro: " + data.msg + "</p>";
                }
            })
            .catch(err => {
                resultado.innerHTML = "<p>Erro na requisição: " + err + "</p>";
            });
        });

        // ======================
        // Formulário por ano/mês
        // ======================
        document.getElementById("formData").addEventListener("submit", e => {
            e.preventDefault();
            const formData = new FormData(e.target);

            fetch("consultafuncionario.php?consultar=data", {
                method: "POST",
                body: formData
            })
            .then(resp => resp.json())
            .then(data => {
                if (data.status === "ok") {
                    resultado.innerHTML = criarTabela(data.dados);
                } else {
                    resultado.innerHTML = "<p>Erro: " + data.msg + "</p>";
                }
            })
            .catch(err => {
                resultado.innerHTML = "<p>Erro na requisição: " + err + "</p>";
            });
        });

    });
    </script>
</body>
</html>