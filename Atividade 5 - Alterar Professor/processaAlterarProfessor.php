<?php
    $msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST')  {
    $cpf = $_POST["cpf"];
    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $titulacao = $_POST["titulacao"];
    $msg = "";
    
    if (file_exists("professores.txt")) {
        $arqProf = fopen("professores.txt", "r") or die("erro ao abrir arquivo");
        $arqProfNovo = fopen("professores_novo.txt", "w") or die("erro ao criar arquivo");
        
        $cabecalho = fgets($arqProf);
        fwrite($arqProfNovo, $cabecalho);

        while (!feof($arqProf)) {
            $linha = fgets($arqProf);
            
            if (trim($linha) != "") {
                $colunaDados = explode(";", $linha);
                
                if (isset($colunaDados[2]) && trim($colunaDados[2]) == $cpf) {
                    $novaLinha = $nome . ";" . $email . ";" . $cpf . ";" . $titulacao . "\n";
                    fwrite($arqProfNovo, $novaLinha);
                } else {
                    fwrite($arqProfNovo, $linha);
                }
            }
        }
        fclose($arqProf);
        fclose($arqProfNovo);
        
        rename("professores_novo.txt", "professores.txt");
        $msg = "Professor alterado com sucesso!!!";
    } else {
        $msg = "Arquivo de professores não encontrado.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Status da Alteração</title>
</head>
<body>
<h1>Alterar Professor</h1>
<p><?php echo $msg; ?></p>
<br>
<a href="formAlterarProfessor.php">Voltar</a>
</body>
</html>