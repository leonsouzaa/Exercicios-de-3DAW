<?php
$msg = ""; 

if ($_SERVER['REQUEST_METHOD'] == 'POST')  {
    $codigo_excluir = $_POST["codigo"];
    $arquivo = "disciplinas.txt";
    $excluido = false;

    if (file_exists($arquivo)) {
        
        $linhas = file($arquivo);
        
        $arqDisciplina = fopen($arquivo, "w") or die("Erro ao abrir arquivo");
        
        foreach ($linhas as $linha) {
            
            $dados = explode(";", $linha);
            
            if (trim($linha) == "nome;codigo" || (isset($dados[1]) && trim($dados[1]) != $codigo_excluir)) {
                fwrite($arqDisciplina, $linha);
            } else {
                
                $excluido = true;
            }
        }
        
        fclose($arqDisciplina);
        
        if ($excluido) {
            $msg = "Disciplina excluída com sucesso!!!";
        } else {
            $msg = "Disciplina não encontrada.";
        }
    } else {
        $msg = "O arquivo de disciplinas ainda não existe.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Excluir Disciplina</title>
</head>
<body>
    <h1>Excluir Disciplina</h1>
    
     <form action="ExcluirDisciplina.php" method="POST">
        Código da Disciplina para excluir: <input type="text" name="codigo" required>
        <br><br>
        <input type="submit" value="Excluir Disciplina">
    </form>
    
    <p><strong><?php echo $msg; ?></strong></p>
    <br>
</body>
</html>