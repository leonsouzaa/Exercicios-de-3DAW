<?php

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Sistema AV1</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f4f4f9; color: #333; }
        .container { max-width: 600px; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1 { color: #0056b3; }
        ul { list-style: none; padding: 0; }
        li { margin-bottom: 12px; }
        a { text-decoration: none; color: #007bff; font-weight: bold; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Painel de Controle</h1>
        <p>Gerenciamento de Perguntas</p>
        <ul>
            <li><a href="usuarios.php">Gerenciar Usuários</a></li>
            <li><a href="criarMultipla.php">Cadastrar Pergunta de Múltipla Escolha</a></li>
            <li><a href="criarTexto.php">Cadastrar Pergunta Dissertativa</a></li>
            <li><a href="perguntas.php">Listar e Gerenciar Perguntas</a></li>
        </ul>
    </div>
</body>
</html>