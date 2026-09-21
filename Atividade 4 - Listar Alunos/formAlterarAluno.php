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
    <title>Alterar Aluno</title>
</head>
<body>
    <h1>Alterar Dados do Aluno</h1>
    <form action="3_processaAlterarAluno.php" method="POST">
        Matrícula (não alterável): <input type="text" name="mat" value="<?php echo $mat_url; ?>" readonly>
        <br><br>
        Nome: <input type="text" name="nome" value="<?php echo $nome; ?>">
        <br><br>
        Email: <input type="text" name="email" value="<?php echo $email; ?>">
        <br><br>
        CPF: <input type="text" name="cpf" value="<?php echo $cpf; ?>">
        <br><br>
        <input type="submit" value="Salvar Alterações">
    </form>
    <br>
    <a href="1_listarAlunos.php">Voltar para a Lista</a>
</body>
</html>
