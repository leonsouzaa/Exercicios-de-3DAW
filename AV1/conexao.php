<?php

function lerArquivo($caminho) {
    if (!file_exists($caminho)) {
        file_put_contents($caminho, '');
    }
    $linhas = file($caminho, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $dados = [];
    foreach ($linhas as $linha) {
        $dados[] = json_decode($linha, true);
    }
    return $dados;
}

function salvarArquivo($caminho, $dados) {
    $conteudo = '';
    foreach ($dados as $item) {
        $conteudo .= json_encode($item) . "\n";
    }
    file_put_contents($caminho, $conteudo);
}
?>
