<?php
$msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mat = $_POST['mat'];
    
    $arqAluno = fopen("alunos.txt", "r");
    $arqAlunoNovo = fopen("alunos_novo.txt", "w");
    
    $cabecalho = fgets($arqAluno);
    fwrite($arqAlunoNovo, $cabecalho);
    
    while (!feof($arqAluno)) {
        $linha = fgets($arqAluno);
        if (trim($linha) != "") {
            $dados = explode(";", $linha);
            
            if (trim($dados[2]) != $mat) {
                fwrite($arqAlunoNovo, $linha);
            }
        }
    }
    fclose($arqAluno);
    fclose($arqAlunoNovo);
    
    rename("alunos_novo.txt", "alunos.txt");
    $msg = "Aluno excluído com sucesso!!!";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Aluno Excluído</title>
</head>
<body>
    <h1>Status da Exclusão</h1>
    <p><?php echo $msg; ?></p>
    <br>
    <a href="1_listarAlunos.php">Voltar para a Lista</a>
</body>
</html>
