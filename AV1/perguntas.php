<?php
require_once 'conexao_txt.php';
$arquivo = 'perguntas.txt';
$perguntas = lerArquivo($arquivo);

$mensagem = '';
$detalhePergunta = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir_id'])) {
    $idExcluir = $_POST['excluir_id'];
    $perguntas = array_filter($perguntas, fn($p) => $p['id'] !== $idExcluir);
    salvarArquivo($arquivo, array_values($perguntas));
    $mensagem = "Pergunta excluída com sucesso.";
    $perguntas = lerArquivo($arquivo);
}

if (isset($_GET['ver_id'])) {
    foreach ($perguntas as $p) {
        if ($p['id'] === $_GET['ver_id']) {
            $detalhePergunta = $p;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Listagem de Perguntas</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background: #f4f4f9; }
        .container { max-width: 800px; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #0056b3; color: white; }
        .btn { padding: 5px 10px; text-decoration: none; color: white; border-radius: 3px; font-size: 14px; display: inline-block; }
        .btn-view { background: #17a2b8; }
        .btn-edit { background: #ffc107; color: black; }
        .btn-del { background: #dc3545; border: none; cursor: pointer; }
        .modal { background: #e9ecef; padding: 15px; margin-top: 20px; border-radius: 5px; border-left: 5px solid #17a2b8; }
        .correta { color: green; font-weight: bold; }
        a { color: #007bff; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php">&larr; Voltar ao Menu</a> | <a href="criar_multipla.php">+ Nova Pergunta</a>
        <h2>Perguntas Cadastradas</h2>
        <?php if($mensagem): ?><p style="color: green;"><?= $mensagem ?></p><?php endif; ?>

        <?php if($detalhePergunta): ?>
            <div class="modal">
                <h3>Detalhes da Pergunta</h3>
                <p><strong>Enunciado:</strong> <?= htmlspecialchars($detalhePergunta['enunciado']) ?></p>
                <ul>
                    <?php foreach($detalhePergunta['respostas'] as $r): ?>
                        <li class="<?= $r['correta'] ? 'correta' : '' ?>">
                            <?= htmlspecialchars($r['texto']) ?> <?= $r['correta'] ? '(Correta)' : '' ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <a href="perguntas.php">Fechar Detalhes</a>
            </div>
        <?php endif; ?>

        <table>
            <tr>
                <th>Enunciado</th>
                <th>Ações</th>
            </tr>
            <?php if(empty($perguntas)): ?>
                <tr><td colspan="2">Nenhuma pergunta cadastrada.</td></tr>
            <?php else: ?>
                <?php foreach($perguntas as $p): ?>
                <tr>
                    <td><?= htmlspecialchars(substr($p['enunciado'], 0, 80)) ?>...</td>
                    <td>
                        <a href="perguntas.php?ver_id=<?= $p['id'] ?>" class="btn btn-view">Ver</a>
                        <a href="alterar_multipla.php?id=<?= $p['id'] ?>" class="btn btn-edit">Alterar</a>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="excluir_id" value="<?= $p['id'] ?>">
                            <button type="submit" class="btn btn-del" onclick="return confirm('Confirma a exclusão?')">Excluir</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>
