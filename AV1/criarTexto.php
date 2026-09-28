<?php
require_once 'conexao.php';

$mensagem = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $enunciado = trim($_POST['enunciado'] ?? '');
    $respostas =$_POST['respostas'] ?? []; 

    if (!empty($enunciado) && !empty($respostas)) {
        
        $gabarito_serializado = implode(' || ', array_filter($respostas));$linha = "TEXTO;" . $enunciado . ";" . $gabarito_serializado . "\n";
        
        
        file_put_contents('perguntas.txt', $linha, FILE_APPEND);$mensagem = "Pergunta salva com sucesso!";
    } else {
        $mensagem = "Preencha o enunciado e adicione pelo menos uma resposta/gabarito.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Nova Pergunta Dissertativa</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f4f4f9; color: #333; }
        .container { max-width: 600px; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); margin: auto; }
        h2 { color: #0056b3; }
        textarea, input[type="text"] { width: 100%; padding: 8px; margin-top: 5px; margin-bottom: 15px; box-sizing: border-box; }
        button { background-color: #007bff; color: white; border: none; padding: 10px 15px; cursor: pointer; border-radius: 4px; }
        button:hover { background-color: #0056b3; }
        .btn-remover { background-color: #dc3545; padding: 5px 10px; font-size: 12px; margin-left: 10px; }
        .btn-remover:hover { background-color: #bd2130; }
        ul#lista-respostas { list-style-type: none; padding: 0; margin-bottom: 15px; }
        ul#lista-respostas li { background: #e9ecef; padding: 8px 12px; margin-bottom: 5px; display: flex; justify-content: space-between; align-items: center; border-radius: 4px; }
        .msg { color: green; font-weight: bold; margin-bottom: 15px; }
        a { text-decoration: none; color: #007bff; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <p><a href="index.php">← Voltar ao Menu</a> | <a href="perguntas.php">Ver Listagem</a></p>
        
        <h2>Nova Pergunta Dissertativa (Texto)</h2>

        <?php if (!empty($mensagem)): ?>
            <p class="msg"><?php echo $mensagem; ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>Enunciado:</label>
            <textarea name="enunciado" rows="3" required placeholder="Ex: Quem descobriu o Brasil?"></textarea>

            <label>Adicionar Resposta / Gabarito Aceito:</label>
            <div style="display: flex; gap: 10px;">
                <input type="text" id="nova_resposta" placeholder="Ex: Pedro Álvares Cabral">
                <button type="button" onclick="adicionarResposta()">Adicionar</button>
            </div>

            <p style="font-size: 14px; color: #666; margin-top: 5px;">Respostas adicionadas (você pode incluir variações aceitas):</p>
            <ul id="lista-respostas">  
            </ul>
            <div id="container-inputs-ocultos"></div>

            <button type="submit" style="width: 100%; margin-top: 10px;">Salvar Pergunta</button>
        </form>
    </div>

    <script>
        let respostasArray = [];

        function adicionarResposta() {
            const input = document.getElementById('nova_resposta');
            const valor = input.value.trim();

            if (valor === "") {
                alert("Digite uma resposta antes de adicionar!");
                return;
            }

            respostasArray.push(valor);
            input.value = "";
            atualizarTela();
        }

        function removerResposta(index) {
            respostasArray.splice(index, 1);
            atualizarTela();
        }

        function atualizarTela() {
            const ul = document.getElementById('lista-respostas');
            const containerOcultos = document.getElementById('container-inputs-ocultos');
            
            ul.innerHTML = "";
            containerOcultos.innerHTML = "";

            respostasArray.forEach((resp, index) => {
                
                const li = document.createElement('li');
                li.innerHTML = `<span>${index + 1}. ${resp}</span> <button type="button" class="btn-remover" onclick="removerResposta(${index})">Remover</button>`;
                ul.appendChild(li);

               
                const inputHidden = document.createElement('input');
                inputHidden.type = 'hidden';
                inputHidden.name = 'respostas[]';
                inputHidden.value = resp;
                containerOcultos.appendChild(inputHidden);
            });
        }
    </script>
</body>
</html>