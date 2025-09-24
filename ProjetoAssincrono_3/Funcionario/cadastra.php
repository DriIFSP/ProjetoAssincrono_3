<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro Assíncrono</title>

    <link rel="icon" type="image/x-icon" href="../assets/favicon.ico">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="css/estilo.css">
</head>

<body>
    <div class="container my-4 p-3">

        <a href="../index.html" class="btn btn-secondary mb-3">Voltar</a>
        <h2 class="mb-4">Cadastro Assíncrono</h2>

        <form id="form1" enctype="multipart/form-data">
            <div class="row">
                <!-- Coluna da imagem -->
                <div class="col-lg-5 mb-4 text-center">
                    <img src="img/moldura.png" id="moldura" class="img-fluid rounded mb-3" alt="Bônus" style="max-width: 60%; height:auto;">
                    <input type="file" name="foto" id="foto" accept=".png,.jpg" class="form-control" required>
                </div>

                <!-- Coluna do formulário -->
                <div class="col-lg-7">
                    <div class="mb-3">
                        <label for="func" class="form-label d-block"><b>Escolha o Funcionário:</b></label>
                        <select class="form-select w-50" id="func" name="func" required>
                            <option value=""><<< Escolha um Funcionário >>></option>
                            <option value="Ana Andrade">Ana Andrade</option>
                            <option value="Bruna Costa">Bruna Costa</option>
                            <option value="Carlos Montreal">Carlos Montreal</option>
                            <option value="João Freitas">João Freitas</option>
                            <option value="Paulo Santos">Paulo Santos</option>
                            <option value="Rita Passaros">Rita Passaros</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="valor" class="form-label d-block"><b>Digite o valor de Vendas do Mês:</b></label>
                        <input type="text" class="form-control w-25" id="valor" name="valor" placeholder="Valor Vendas" maxlength="10" required>
                    </div>

                    <div class="mb-3">
                        <label for="segmento" class="form-label d-block"><b>Escolha o Segmento:</b></label>
                        <select class="form-select w-50" id="segmento" name="segmento" required>
                            <option value=""><<< Escolha um Segmento >>></option>
                            <option value="Automóveis">Automóveis</option>
                            <option value="Imóveis">Imóveis</option>
                            <option value="Seguro Residencial">Seguro Residencial</option>
                            <option value="Seguro Automóvel">Seguro Automóvel</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg">Cadastrar</button>
                </div>
            </div>
        </form>

        <div id="resultado" class="mt-3"></div>
    </div>

    <!-- JavaScript -->
    <script>
        // Pré-visualização da imagem
        document.getElementById('foto').addEventListener('change', function () {
            const campo = this;
            if (campo.files && campo.files[0]) {
                const file = new FileReader();
                file.onload = function (e) {
                    document.getElementById("moldura").src = e.target.result;
                };
                file.readAsDataURL(campo.files[0]);
            }
        });

        // Máscara de moeda
        function mascara(o, f) {
            setTimeout(() => { o.value = f(o.value); }, 1);
        }

        function moeda(v) {
            v = v.replace(/\D/g, ""); // apenas números
            v = v.replace(/(\d{1})(\d{1,2})$/, "$1.$2"); // ponto antes dos 2 últimos dígitos
            return v;
        }

      // Pega o formulário pelo id 'form1'
const form = document.getElementById('form1');

// Adiciona um "ouvinte de evento" para o submit do formulário
form.addEventListener('submit', function (e) {
    // Impede que o formulário seja enviado da forma tradicional (recarregando a página)
    e.preventDefault();

    // Captura os valores preenchidos nos campos do formulário
    const func = document.getElementById("func").value;          // Nome do funcionário
    const valor = document.getElementById("valor").value;        // Valor de vendas
    const segmento = document.getElementById("segmento").value;  // Segmento escolhido
    const foto = document.getElementById("foto").files[0];       // Foto enviada (pega o arquivo)

    // Validações básicas antes do envio
    if (!func) { alert('Escolha o Funcionário'); return; }
    if (!segmento) { alert('Escolha o Segmento'); return; }
    if (!valor) { alert('Digite o valor'); return; }
    if (!foto) { alert('É obrigatório anexar uma foto'); return; }

    // Cria um objeto FormData para enviar os dados (inclusive arquivos) via AJAX
    const formData = new FormData();
    formData.append('func', func);
    formData.append('valor', valor);
    formData.append('segmento', segmento);
    formData.append('foto', foto);

    // Faz a requisição assíncrona usando fetch para o arquivo PHP
    fetch('cadastrafuncionario.php', {
        method: 'POST',     // Método HTTP
        body: formData      // Corpo da requisição (com os dados do formulário)
    })
        // Converte a resposta do servidor para JSON
        .then(response => response.json())
        // Manipula o retorno do servidor
        .then(data => {
            const resultado = document.getElementById('resultado');
            if (data.success) {
                // Se o servidor retornou sucesso
                resultado.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                form.reset(); // Limpa os campos do formulário
                document.getElementById('moldura').src = 'img/moldura.png'; // Volta a moldura para a imagem padrão
            } else {
                // Se o servidor retornou erro
                resultado.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
            }
        })
        // Caso ocorra algum erro na comunicação (ex: servidor fora do ar)
        .catch(error => {
            console.error('Erro:', error);
            document.getElementById('resultado').innerHTML = `<div class="alert alert-danger">Erro ao enviar os dados</div>`;
        });
    });

        // Aplicar máscara ao digitar no campo valor
        document.getElementById('valor').addEventListener('keypress', function () {
            mascara(this, moeda);
        });
    </script>
</body>

</html>