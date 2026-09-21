<?php
$msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mat = $_POST['mat'];
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $cpf = $_POST['cpf'];
    
    $arqAluno = fopen("alunos.txt", "r");
    $arqAlunoNovo = fopen("alunos_novo.txt", "w");
    
    $cabecalho = fgets($arqAluno);
    fwrite($arqAlunoNovo, $cabecalho);
    
    while (!feof($arqAluno)) {
        $linha = fgets($arqAluno);
        if (trim($linha) != "") {
            $dados = explode(";", $linha);
            
            if (trim($dados[2]) == $mat) {
                $novaLinha = $nome . ";" . $email . ";" . $mat . ";" . $cpf . "\n";
                fwrite($arqAlunoNovo, $novaLinha);
            } else {
                fwrite($arqAlunoNovo, $linha);
            }
        }
    }
    fclose($arqAluno);
    fclose($arqAlunoNovo);
    
    rename("alunos_novo.txt", "alunos.txt");
    $msg = "Aluno alterado com sucesso!!!";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Aluno Alterado</title>
</head>
<body>
    <h1>Status da Alteração</h1>
    <p><?php echo $msg; ?></p>
    <br>
    <a href="1_listarAlunos.php">Voltar para a Lista</a>
</body>
</html>
