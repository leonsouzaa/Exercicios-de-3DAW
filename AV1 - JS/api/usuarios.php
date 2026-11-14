<?php
// API do CRUD de usuários. Sempre responde JSON.
//
//   GET    usuarios.php           -> lista todos os usuários
//   GET    usuarios.php?id=XXX    -> lista um usuário
//   POST   usuarios.php           -> cria usuário (corpo JSON)
//   PUT    usuarios.php?id=XXX    -> altera usuário (corpo JSON)
//   DELETE usuarios.php?id=XXX    -> exclui usuário

require_once __DIR__ . '/conexao.php';

const ARQUIVO = 'usuarios.txt';
const CARGOS = ['Gestor', 'Administrador'];

function validarUsuario(array $entrada, array $usuarios, ?string $idAtual): array
{
    $nome = trim((string) ($entrada['nome'] ?? ''));
    $email = trim((string) ($entrada['email'] ?? ''));
    $cargo = (string) ($entrada['cargo'] ?? '');

    if ($nome === '') {
        erro('Informe o nome.', 422);
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        erro('Informe um e-mail válido.', 422);
    }
    if (!in_array($cargo, CARGOS, true)) {
        erro('Cargo inválido.', 422);
    }
    foreach ($usuarios as $u) {
        if ($u['id'] !== $idAtual && strcasecmp($u['email'], $email) === 0) {
            erro('Já existe um usuário com esse e-mail.', 422);
        }
    }

    return ['nome' => $nome, 'email' => $email, 'cargo' => $cargo];
}

$metodo = $_SERVER['REQUEST_METHOD'];
$id = $_GET['id'] ?? null;
$usuarios = lerArquivo(ARQUIVO);

switch ($metodo) {
    case 'GET':
        if ($id === null) {
            responder(['sucesso' => true, 'usuarios' => $usuarios]);
        }
        $indice = buscarIndice($usuarios, $id);
        if ($indice === null) {
            erro('Usuário não encontrado.', 404);
        }
        responder(['sucesso' => true, 'usuario' => $usuarios[$indice]]);
        break;

    case 'POST':
        $novo = ['id' => uniqid()] + validarUsuario(corpoJson(), $usuarios, null);
        $usuarios[] = $novo;
        salvarArquivo(ARQUIVO, $usuarios);
        responder(['sucesso' => true, 'mensagem' => 'Usuário cadastrado com sucesso.', 'usuario' => $novo], 201);
        break;

    case 'PUT':
        if ($id === null) {
            erro('Informe o id do usuário.', 400);
        }
        $indice = buscarIndice($usuarios, $id);
        if ($indice === null) {
            erro('Usuário não encontrado.', 404);
        }
        $usuarios[$indice] = ['id' => $id] + validarUsuario(corpoJson(), $usuarios, $id);
        salvarArquivo(ARQUIVO, $usuarios);
        responder(['sucesso' => true, 'mensagem' => 'Usuário atualizado com sucesso.', 'usuario' => $usuarios[$indice]]);
        break;

    case 'DELETE':
        if ($id === null) {
            erro('Informe o id do usuário.', 400);
        }
        $indice = buscarIndice($usuarios, $id);
        if ($indice === null) {
            erro('Usuário não encontrado.', 404);
        }
        array_splice($usuarios, $indice, 1);
        salvarArquivo(ARQUIVO, $usuarios);
        responder(['sucesso' => true, 'mensagem' => 'Usuário excluído com sucesso.']);
        break;

    default:
        erro('Método não permitido.', 405);
}
