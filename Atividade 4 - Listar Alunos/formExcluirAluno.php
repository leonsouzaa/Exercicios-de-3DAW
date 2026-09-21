<?php
$mat_url = "";
$nome = "";
$email = "";
$cpf = "";

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['mat'])) {
    $mat_url = $_GET['mat'];
    $arqAluno = fopen("alunos.txt", "r");
    fgets($arqAluno);
    
    while (!feof($arqAluno)) {
        $linha = fgets($arqAluno);
        if (trim($linha) != "") {
            $dados = explode(";", $linha);
            if (trim($dados[2]) == $mat_url) {
                $nome = trim($dados[0]);
                $email = trim($dados[1]);
                $cpf = trim($dados[3]);
                break;
            }
        }
    }
    fclose($arqAluno);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Confirmar Exclusão</title>
</head>
<body>
    <h1>Deseja realmente excluir este aluno?</h1>
    <p><strong>Nome:</strong> <?php echo $nome; ?></p>
    <p><strong>Email:</strong> <?php echo $email; ?></p>
    <p><strong>Matrícula:</strong> <?php echo $mat_url; ?></p>
    <p><strong>CPF:</strong> <?php echo $cpf; ?></p>
    
    <form action="5_processaExcluirAluno.php" method="POST">
        <input type="hidden" name="mat" value="<?php echo $mat_url; ?>">
        <input type="submit" value="Confirmar Exclusão">
    </form>
    <br>
    <a href="1_listarAlunos.php">Cancelar e Voltar</a>
</body>
</html>
