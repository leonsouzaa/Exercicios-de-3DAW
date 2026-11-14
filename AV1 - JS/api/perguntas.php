<?php
// API de perguntas e respostas (múltipla escolha e texto). Sempre responde JSON.
//
//   GET    perguntas.php           -> lista todas as perguntas
//   GET    perguntas.php?id=XXX    -> lista uma pergunta
//   POST   perguntas.php           -> cria pergunta (corpo JSON)
//   PUT    perguntas.php?id=XXX    -> altera pergunta (corpo JSON)
//   DELETE perguntas.php?id=XXX    -> exclui pergunta e respostas

require_once __DIR__ . '/conexao.php';

const ARQUIVO = 'perguntas.txt';

function validarPergunta(array $entrada): array
{
    $tipo = $entrada['tipo'] ?? '';
    $enunciado = trim((string) ($entrada['enunciado'] ?? ''));

    if ($enunciado === '') {
        erro('Informe o enunciado.', 422);
    }

    if ($tipo === 'multipla') {
        $respostas = [];
        $lista = is_array($entrada['respostas'] ?? null) ? $entrada['respostas'] : [];
        foreach ($lista as $r) {
            $texto = trim((string) ($r['texto'] ?? ''));
            if ($texto !== '') {
                $respostas[] = ['texto' => $texto, 'correta' => !empty($r['correta'])];
            }
        }
        if (count($respostas) < 2) {
            erro('Informe pelo menos duas alternativas.', 422);
        }
        $corretas = count(array_filter($respostas, fn($r) => $r['correta']));
        if ($corretas !== 1) {
            erro('Marque exatamente uma alternativa correta.', 422);
        }
        return ['tipo' => 'multipla', 'enunciado' => $enunciado, 'respostas' => $respostas];
    }

    if ($tipo === 'texto') {
        $lista = is_array($entrada['gabaritos'] ?? null) ? $entrada['gabaritos'] : [];
        $gabaritos = [];
        foreach ($lista as $g) {
            $g = trim((string) $g);
            if ($g !== '') {
                $gabaritos[] = $g;
            }
        }
        if (!$gabaritos) {
            erro('Informe pelo menos uma resposta (gabarito).', 422);
        }
        return ['tipo' => 'texto', 'enunciado' => $enunciado, 'gabaritos' => $gabaritos];
    }

    erro('Tipo de pergunta inválido.', 422);
}

$metodo = $_SERVER['REQUEST_METHOD'];
$id = $_GET['id'] ?? null;
$perguntas = lerArquivo(ARQUIVO);

switch ($metodo) {
    case 'GET':
        if ($id === null) {
            responder(['sucesso' => true, 'perguntas' => $perguntas]);
        }
        $indice = buscarIndice($perguntas, $id);
        if ($indice === null) {
            erro('Pergunta não encontrada.', 404);
        }
        responder(['sucesso' => true, 'pergunta' => $perguntas[$indice]]);
        break;

    case 'POST':
        $nova = ['id' => uniqid()] + validarPergunta(corpoJson());
        $perguntas[] = $nova;
        salvarArquivo(ARQUIVO, $perguntas);
        responder(['sucesso' => true, 'mensagem' => 'Pergunta cadastrada com sucesso.', 'pergunta' => $nova], 201);
        break;

    case 'PUT':
        if ($id === null) {
            erro('Informe o id da pergunta.', 400);
        }
        $indice = buscarIndice($perguntas, $id);
        if ($indice === null) {
            erro('Pergunta não encontrada.', 404);
        }
        $dados = validarPergunta(corpoJson());
        if ($dados['tipo'] !== $perguntas[$indice]['tipo']) {
            erro('Não é possível mudar o tipo da pergunta.', 422);
        }
        $perguntas[$indice] = ['id' => $id] + $dados;
        salvarArquivo(ARQUIVO, $perguntas);
        responder(['sucesso' => true, 'mensagem' => 'Pergunta atualizada com sucesso.', 'pergunta' => $perguntas[$indice]]);
        break;

    case 'DELETE':
        if ($id === null) {
            erro('Informe o id da pergunta.', 400);
        }
        $indice = buscarIndice($perguntas, $id);
        if ($indice === null) {
            erro('Pergunta não encontrada.', 404);
        }
        array_splice($perguntas, $indice, 1);
        salvarArquivo(ARQUIVO, $perguntas);
        responder(['sucesso' => true, 'mensagem' => 'Pergunta e respostas excluídas com sucesso.']);
        break;

    default:
        erro('Método não permitido.', 405);
}
