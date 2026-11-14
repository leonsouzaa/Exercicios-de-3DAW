<?php

header('Content-Type: application/json; charset=utf-8');

function responder(array $dados, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($dados, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(['sucesso' => false, 'erro' => 'Método não permitido. Use POST.'], 405);
}

$entrada = json_decode(file_get_contents('php://input'), true);

if (!is_array($entrada)) {
    responder(['sucesso' => false, 'erro' => 'Requisição inválida.'], 400);
}

$numero1  = $entrada['numero1'] ?? null;
$numero2  = $entrada['numero2'] ?? null;
$operacao = $entrada['operacao'] ?? null;

if (!is_numeric($numero1) || !is_numeric($numero2)) {
    responder(['sucesso' => false, 'erro' => 'Informe dois números válidos.'], 422);
}

$a = (float) $numero1;
$b = (float) $numero2;

switch ($operacao) {
    case 'soma':
        $resultado = $a + $b;
        break;
    case 'subtracao':
        $resultado = $a - $b;
        break;
    case 'multiplicacao':
        $resultado = $a * $b;
        break;
    case 'divisao':
        if ($b == 0) {
            responder(['sucesso' => false, 'erro' => 'Não é possível dividir por zero.'], 422);
        }
        $resultado = $a / $b;
        break;
    case 'potencia':
        $resultado = $a ** $b;
        break;
    case 'resto':
        if ($b == 0) {
            responder(['sucesso' => false, 'erro' => 'Não é possível calcular o resto com divisor zero.'], 422);
        }
        $resultado = fmod($a, $b);
        break;
    default:
        responder(['sucesso' => false, 'erro' => 'Operação inválida.'], 422);
}

if (is_nan($resultado) || is_infinite($resultado)) {
    responder(['sucesso' => false, 'erro' => 'O resultado não é um número válido.'], 422);
}

responder([
    'sucesso'   => true,
    'resultado' => round($resultado, 10)
]);
