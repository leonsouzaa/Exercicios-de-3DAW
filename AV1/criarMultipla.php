<?php
require_once 'conexao_txt.php';
$arquivo = 'perguntas.txt';
$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $perguntas = lerArquivo($arquivo);
    
    $respostas = [];
    foreach ($_POST['resposta_texto'] as $index => $texto) {
        if (trim($texto) !== '') {
            $respostas[] = [
                'texto' => $texto,
                'correta' => (isset($_POST['correta']) && $_POST['correta'] == $index)
            ];
        }
    }

    $novaPergunta = [
        'id' => uniqid(),
        'enunciado' => $_POST['enunciado'],
        'respostas' => $respostas
    ];

    $perguntas[] = $novaPergunta;
    salvarArquivo($arquivo, $perguntas);
    $mensagem = "Pergunta cadastrada com sucesso.";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Nova Pergunta</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background: #f4f4f9; }
        .container { max-width: 600px; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        textarea, input[type="text"] { width: 100%; padding: 8px; margin-bottom: 10px; box-sizing: border-box; }
        .opcao-group { display: flex; align-items: center; margin-bottom: 5px; }
        .opcao-group input[type="text"] { margin-bottom: 0; margin-right: 10px; }
        button { background: #007bff; color: white; border: none; padding: 10px 15px; cursor: pointer; border-radius: 4px; }
        button:hover { background: #0056b3; }
        a { display: inline-block; margin-bottom: 15px; color: #007bff; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php">&larr; Voltar ao Menu</a> | <a href="perguntas.php">Ver Listagem</a>
        <h2>Nova Pergunta de Múltipla Escolha</h2>
        <?php if($mensagem): ?><p style="color: green;"><?= $mensagem ?></p><?php endif; ?>

        <form method="POST">
            <label>Enunciado:</label>
            <textarea name="enunciado" rows="4" required></textarea>

            <label>Alternativas (Selecione a correta):</label>
            <?php for($i = 0; $i < 4; $i++): ?>
                <div class="opcao-group">
                    <input type="text" name="resposta_texto[]" placeholder="Opção <?= $i+1 ?>" required>
                    <label><input type="radio" name="correta" value="<?= $i ?>" required> Correta</label>
                </div>
            <?php endfor; ?>

            <button type="submit" style="margin-top: 15px;">Salvar</button>
        </form>
    </div>
</body>
</html>
