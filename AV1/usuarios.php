<?php
require_once 'conexao_txt.php';
$arquivo = 'usuarios.txt';
$usuarios = lerArquivo($arquivo);

$mensagem = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    
    if ($acao === 'criar') {
        $novo = [
            'id' => uniqid(),
            'nome' => $_POST['nome'],
            'email' => $_POST['email'],
            'cargo' => $_POST['cargo']
        ];
        $usuarios[] = $novo;
        salvarArquivo($arquivo, $usuarios);
        $mensagem = "Usuário cadastrado com sucesso.";
        $usuarios = lerArquivo($arquivo);
    } elseif ($acao === 'excluir') {
        $idExcluir = $_POST['id'];
        $usuarios = array_filter($usuarios, fn($u) => $u['id'] !== $idExcluir);
        salvarArquivo($arquivo, array_values($usuarios));
        $mensagem = "Usuário excluído com sucesso.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciamento de Usuários</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background: #f4f4f9; }
        .container { max-width: 700px; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #0056b3; color: white; }
        input, select { padding: 8px; width: 100%; margin-bottom: 10px; box-sizing: border-box; }
        button { background: #28a745; color: white; border: none; padding: 10px 15px; cursor: pointer; border-radius: 4px; }
        button:hover { background: #218838; }
        .btn-danger { background: #dc3545; padding: 5px 10px; }
        .btn-danger:hover { background: #c82333; }
        a { display: inline-block; margin-bottom: 15px; color: #007bff; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php">&larr; Voltar ao Menu</a>
        <h2>Cadastro de Usuários</h2>
        <?php if($mensagem): ?><p style="color: green;"><?= $mensagem ?></p><?php endif; ?>

        <form method="POST">
            <input type="hidden" name="acao" value="criar">
            <label>Nome:</label>
            <input type="text" name="nome" required>
            <label>E-mail:</label>
            <input type="email" name="email" required>
            <label>Cargo:</label>
            <select name="cargo">
                <option value="Gestor">Gestor</option>
                <option value="Administrador">Administrador</option>
            </select>
            <button type="submit">Cadastrar</button>
        </form>

        <h3>Usuários Cadastrados</h3>
        <table>
            <tr>
                <th>Nome</th>
                <th>E-mail</th>
                <th>Cargo</th>
                <th>Ações</th>
            </tr>
            <?php foreach($usuarios as $u): ?>
            <tr>
                <td><?= htmlspecialchars($u['nome']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><?= htmlspecialchars($u['cargo']) ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="acao" value="excluir">
                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                        <button type="submit" class="btn-danger" onclick="return confirm('Confirma a exclusão?')">Excluir</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
