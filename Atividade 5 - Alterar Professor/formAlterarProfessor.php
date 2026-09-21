<?php
    $matricula = "";
    $nome = "";
    $cpf = "";
    $endereco = "";

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET["matricula"]))  {
    $matricula = $_GET["matricula"];
    
    if (file_exists("professores.txt")) {
        $arqProf = fopen("professores.txt", "r") or die("erro ao abrir arquivo");
        fgets($arqProf);

        while (!feof($arqProf)) {
            $linha = fgets($arqProf);
            if (trim($linha) != "") {
                $colunaDados = explode(";", $linha);
                if (isset($colunaDados[0]) && trim($colunaDados[0]) == $matricula) {
                    $nome = trim($colunaDados[1]);
                    $cpf = trim($colunaDados[2]);
                    $endereco = trim($colunaDados[3]);
                    break;
                }
            }
        }
        fclose($arqProf);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Alterar Professor</title>
</head>
<body>
<h1>Alterar Professor</h1>

<form action="formAlterarProfessor.php" method="GET">
    Matrícula do Professor: <input type="text" name="matricula" value="<?php echo $matricula; ?>">
    <input type="submit" value="Buscar">
</form>
<br><hr><br>

<form action="processaAlterarProfessor.php" method="POST">
    Matrícula (não alterável): <input type="text" name="matricula" value="<?php echo $matricula; ?>" readonly>
    <br><br>
    Nome: <input type="text" name="nome" value="<?php echo $nome; ?>">
    <br><br>
    CPF: <input type="text" name="cpf" value="<?php echo $cpf; ?>">
    <br><br>
    Endereço: <input type="text" name="endereco" value="<?php echo $endereco; ?>">
    <br><br>
    <input type="submit" value="Salvar Alterações">
</form>

</body>
</html>
