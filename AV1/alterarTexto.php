<?php
require_once 'conexao.php';
$arquivo = 'perguntas.txt';
$id = $_GET['id'] ?? '';
$perguntas = lerArquivo($arquivo);
$perguntaIndex = null;

foreach ($perguntas as $index => $p) {
    if ($p['id'] === $id) {
        $perguntaIndex = $index;
        $pergunta = $p;
        break;
    }
}

if ($perguntaIndex === null) { die("Pergunta não encontrada."); }

$mensagem = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $perguntas[$perguntaIndex]['enunciado'] = $_POST['enunciado'];
    $perguntas[$perguntaIndex]['gabarito'] = $_POST['gabarito'];

    salvarArquivo($arquivo, $perguntas);
    $mensagem = "Pergunta atualizada com sucesso.";
    $pergunta = $perguntas[$perguntaIndex];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Alterar Pergunta - Texto</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background: #f4f4f9; }
        .container { max-width: 600px; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        textarea { width: 100%; padding: 8px; margin-bottom: 10px; box-sizing: border-box; }
        button { background: #ffc107; border: none; padding: 10px 15px; cursor: pointer; border-radius: 4px; font-weight: bold; }
        a { display: inline-block; margin-bottom: 15px; color: #007bff; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <a href="perguntas.php">&larr; Voltar à Listagem</a>
        <h2>Alterar Pergunta Dissertativa</h2>
        <?php if($mensagem): ?><p style="color: green;"><?= $mensagem ?></p><?php endif; ?>

        <form method="POST">
            <label>Enunciado:</label>
            <textarea name="enunciado" rows="4" required><?= htmlspecialchars($pergunta['enunciado']) ?></textarea>

            <label>Gabarito / Resposta Esperada:</label>
            <textarea name="gabarito" rows="4" required><?= htmlspecialchars($pergunta['gabarito']) ?></textarea>

            <button type="submit">Atualizar</button>
        </form>
    </div>
</body>
</html>