<?php
    $sigla = "";
    $msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST')  {
    $sigla = $_POST["sigla"];
    $msg = "";
    
    $arqDisc = fopen("disciplinas.txt","r") or die("erro ao abrir arquivo");
    $arqDiscNovo = fopen("disciplinas_novo.txt","w") or die("erro ao criar arquivo");
    
    $linha = fgets($arqDisc);
    fwrite($arqDiscNovo, $linha);

    while(!feof($arqDisc)) {
        $linha = fgets($arqDisc);
        
        if ($linha) {
            $colunaDados = explode(";", $linha);
            
            if (trim($colunaDados[1]) != $sigla) {
                fwrite($arqDiscNovo, $linha);
            }
        }
     }
    fclose($arqDisc);
    fclose($arqDiscNovo);
    
    rename("disciplinas_novo.txt", "disciplinas.txt");
    
    $msg = "Deu tudo certo!!!";
}
?>
<!DOCTYPE html>
<html>
<head>
</head>
<body>
<h1>Excluir Disciplina</h1>
<br>
<ul>
    <li><a href="ex03_IncluirDisciplina.php">Incluir Disciplina</a></li>
    <li><a href="ex04_listarTodasDisciplinas.php">Listar Disciplina</a></li>
    <li><a href="ex05_pedeQuemAlterar.php">Alterar Disciplina</a></li>
</ul>

<form action="ex06_ExcluirDisciplina.php" method="POST">
    Sigla da disciplina para excluir: <input type="text" name="sigla">
    <br><br>
    <input type="submit" value="Excluir Disciplina">
</form>

<p><?php echo $msg ?></p>
<br>
</body>
</html>
