<?php
// Funções comuns da API: leitura/gravação nos arquivos txt e resposta em JSON.
// Cada linha do .txt guarda um registro em formato JSON.

const DIR_DADOS = __DIR__ . '/../dados/';

function responder(array $dados, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($dados, JSON_UNESCAPED_UNICODE);
    exit;
}

function erro(string $mensagem, int $status = 400): void
{
    responder(['sucesso' => false, 'erro' => $mensagem], $status);
}

function corpoJson(): array
{
    $dados = json_decode(file_get_contents('php://input'), true);
    if (!is_array($dados)) {
        erro('Requisição inválida: envie um JSON.', 400);
    }
    return $dados;
}

function lerArquivo(string $nome): array
{
    if (!is_dir(DIR_DADOS)) {
        mkdir(DIR_DADOS, 0777, true);
    }
    $caminho = DIR_DADOS . $nome;
    if (!file_exists($caminho)) {
        file_put_contents($caminho, '');
    }

    $dados = [];
    $linhas = file($caminho, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($linhas as $linha) {
        $item = json_decode($linha, true);
        if (is_array($item)) {
            $dados[] = $item;
        }
    }
    return $dados;
}

function salvarArquivo(string $nome, array $dados): void
{
    $conteudo = '';
    foreach ($dados as $item) {
        $conteudo .= json_encode($item, JSON_UNESCAPED_UNICODE) . "\n";
    }
    file_put_contents(DIR_DADOS . $nome, $conteudo, LOCK_EX);
}

function buscarIndice(array $lista, string $id): ?int
{
    foreach ($lista as $indice => $item) {
        if (($item['id'] ?? null) === $id) {
            return $indice;
        }
    }
    return null;
}
