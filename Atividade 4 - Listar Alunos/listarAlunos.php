<?php
$linhas = [];
if (file_exists("alunos.txt")) {
    $arqAluno = fopen("alunos.txt", "r");
    fgets($arqAluno);
    
    while (!feof($arqAluno)) {
        $linha = fgets($arqAluno);
        if (trim($linha) != "") {
            $linhas[] = explode(";", $linha);
        }
    }
    fclose($arqAluno);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Listar Alunos</title>
</head>
<body>
    <h1>Lista de Alunos</h1>
    <table border="1">
        <tr>
            <th>Matrícula</th>
            <th>Nome</th>
            <th>Email</th>
            <th>Ações</th>
        </tr>
        <?php foreach ($linhas as $dados): ?>
        <tr>
            <td><?php echo trim($dados[2]); ?></td>
            <td><?php echo trim($dados[0]); ?></td>
            <td><?php echo trim($dados[1]); ?></td>
            <td>
                <a href="2_formAlterarAluno.php?mat=<?php echo trim($dados[2]); ?>">
                    <button>Alterar</button>
                </a>
                <a href="4_formExcluirAluno.php?mat=<?php echo trim($dados[2]); ?>">
                    <button>Excluir</button>
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <br>
    <a href="AdicaoAlun.php">Adicionar Novo Aluno</a>
</body>
</html>
