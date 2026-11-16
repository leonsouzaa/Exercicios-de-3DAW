const API = {
    perguntas: 'api/perguntas.php',
    usuarios: 'api/usuarios.php'
};

// Envia a requisição ao PHP e devolve o JSON. Lança erro com a mensagem do servidor.
async function requisitar(url, metodo = 'GET', corpo = null) {
    const opcoes = { method: metodo, headers: {} };
    if (corpo !== null) {
        opcoes.headers['Content-Type'] = 'application/json';
        opcoes.body = JSON.stringify(corpo);
    }

    let json;
    try {
        const resposta = await fetch(url, opcoes);
        json = await resposta.json();
    } catch (e) {
        throw new Error('Não foi possível se comunicar com o servidor.');
    }

    if (!json.sucesso) {
        throw new Error(json.erro || 'Erro desconhecido.');
    }
    return json;
}

function criar(tag, texto = '', classe = '') {
    const elemento = document.createElement(tag);
    if (texto) elemento.textContent = texto;
    if (classe) elemento.className = classe;
    return elemento;
}

function botao(texto, classe, aoClicar) {
    const b = criar('button', texto, classe);
    b.type = 'button';
    b.addEventListener('click', aoClicar);
    return b;
}

function linhaVazia(tbody, colunas, texto) {
    const tr = criar('tr');
    const td = criar('td', texto, 'vazio');
    td.colSpan = colunas;
    tr.appendChild(td);
    tbody.appendChild(tr);
}

const campoMensagem = document.getElementById('mensagem');

function mostrarMensagem(texto, ehErro = false) {
    campoMensagem.textContent = texto;
    campoMensagem.classList.toggle('erro', ehErro);
    campoMensagem.hidden = false;
}

// Executa uma ação e mostra o erro do servidor (se houver)
async function tentar(acao) {
    try {
        await acao();
    } catch (e) {
        mostrarMensagem(e.message, true);
    }
}

/* ================================ ABAS ================================ */

document.querySelectorAll('.abas button').forEach((b) => {
    b.addEventListener('click', () => {
        document.querySelectorAll('.abas button').forEach((x) => x.classList.toggle('ativa', x === b));
        document.getElementById('aba-perguntas').hidden = b.dataset.aba !== 'perguntas';
        document.getElementById('aba-usuarios').hidden = b.dataset.aba !== 'usuarios';
        campoMensagem.hidden = true;
    });
});

/* ============================== PERGUNTAS ============================== */

const tabelaPerguntas = document.getElementById('tabela-perguntas');
const modalPergunta = document.getElementById('modal-pergunta');
const modalVer = document.getElementById('modal-ver');
let gabaritos = []; // lista só para exibir na tela enquanto o formulário está aberto

async function carregarPerguntas() {
    const json = await requisitar(API.perguntas);
    tabelaPerguntas.innerHTML = '';

    if (json.perguntas.length === 0) {
        linhaVazia(tabelaPerguntas, 4, 'Nenhuma pergunta cadastrada ainda.');
        return;
    }

    json.perguntas.forEach((p, i) => {
        const tr = criar('tr');
        tr.appendChild(criar('td', String(i + 1)));
        tr.appendChild(criar('td', p.enunciado));
        tr.appendChild(criar('td', p.tipo === 'texto' ? 'Dissertativa' : 'Múltipla'));

        const acoes = criar('div', '', 'acoes');
        acoes.appendChild(botao('Ver', 'azul-claro', () => tentar(() => verPergunta(p.id))));
        acoes.appendChild(botao('Alterar', 'amarelo', () => tentar(() => editarPergunta(p.id))));
        acoes.appendChild(botao('Excluir', 'vermelho', () => tentar(() => excluirPergunta(p.id))));

        const td = criar('td');
        td.appendChild(acoes);
        tr.appendChild(td);
        tabelaPerguntas.appendChild(tr);
    });
}

/* ---- Listar uma pergunta ---- */
async function verPergunta(id) {
    const { pergunta } = await requisitar(`${API.perguntas}?id=${encodeURIComponent(id)}`);
    const lista = document.getElementById('ver-respostas');
    lista.innerHTML = '';

    document.getElementById('ver-enunciado').textContent = pergunta.enunciado;

    if (pergunta.tipo === 'texto') {
        document.getElementById('ver-titulo').textContent = 'Pergunta dissertativa';
        document.getElementById('ver-rotulo').textContent = 'Respostas aceitas:';
        pergunta.gabaritos.forEach((g) => lista.appendChild(criar('li', g)));
    } else {
        document.getElementById('ver-titulo').textContent = 'Pergunta de múltipla escolha';
        document.getElementById('ver-rotulo').textContent = 'Alternativas:';
        pergunta.respostas.forEach((r) => {
            lista.appendChild(criar('li', r.texto + (r.correta ? ' (correta)' : ''), r.correta ? 'correta' : ''));
        });
    }
    modalVer.showModal();
}

/* ---- Formulário de criar / alterar ---- */
function abrirFormPergunta(tipo, pergunta = null) {
    document.getElementById('form-pergunta').reset();
    document.getElementById('pergunta-id').value = pergunta ? pergunta.id : '';
    document.getElementById('pergunta-tipo').value = tipo;
    document.getElementById('pergunta-enunciado').value = pergunta ? pergunta.enunciado : '';

    const nomeTipo = tipo === 'texto' ? 'dissertativa' : 'de múltipla escolha';
    document.getElementById('titulo-form-pergunta').textContent =
        (pergunta ? 'Alterar pergunta ' : 'Nova pergunta ') + nomeTipo;

    document.getElementById('campos-multipla').hidden = tipo !== 'multipla';
    document.getElementById('campos-texto').hidden = tipo !== 'texto';

    if (tipo === 'multipla') {
        document.querySelectorAll('.alt').forEach((linha, i) => {
            const resposta = pergunta ? pergunta.respostas[i] : null;
            linha.querySelector('.alt-texto').value = resposta ? resposta.texto : '';
            linha.querySelector('.alt-correta').checked = resposta ? resposta.correta : false;
        });
    } else {
        gabaritos = pergunta ? [...pergunta.gabaritos] : [];
        desenharGabaritos();
    }
    modalPergunta.showModal();
}

async function editarPergunta(id) {
    const { pergunta } = await requisitar(`${API.perguntas}?id=${encodeURIComponent(id)}`);
    abrirFormPergunta(pergunta.tipo, pergunta);
}

function desenharGabaritos() {
    const ul = document.getElementById('lista-gabaritos');
    ul.innerHTML = '';
    gabaritos.forEach((g, i) => {
        const li = criar('li');
        li.appendChild(criar('span', `${i + 1}. ${g}`));
        li.appendChild(botao('Remover', 'vermelho', () => {
            gabaritos.splice(i, 1);
            desenharGabaritos();
        }));
        ul.appendChild(li);
    });
}

function adicionarGabarito() {
    const campo = document.getElementById('novo-gabarito');
    if (campo.value.trim() === '') return;
    gabaritos.push(campo.value);
    campo.value = '';
    desenharGabaritos();
}

document.getElementById('btn-add-gabarito').addEventListener('click', adicionarGabarito);
document.getElementById('novo-gabarito').addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
        e.preventDefault();
        adicionarGabarito();
    }
});

document.getElementById('btn-nova-multipla').addEventListener('click', () => abrirFormPergunta('multipla'));
document.getElementById('btn-nova-texto').addEventListener('click', () => abrirFormPergunta('texto'));
document.getElementById('btn-fechar-pergunta').addEventListener('click', () => modalPergunta.close());
document.getElementById('btn-fechar-ver').addEventListener('click', () => modalVer.close());

/* ---- Salvar (criar = POST, alterar = PUT) ---- */
document.getElementById('form-pergunta').addEventListener('submit', (evento) => {
    evento.preventDefault();

    const id = document.getElementById('pergunta-id').value;
    const tipo = document.getElementById('pergunta-tipo').value;
    const dados = {
        tipo: tipo,
        enunciado: document.getElementById('pergunta-enunciado').value
    };

    if (tipo === 'multipla') {
        dados.respostas = [...document.querySelectorAll('.alt')].map((linha) => ({
            texto: linha.querySelector('.alt-texto').value,
            correta: linha.querySelector('.alt-correta').checked
        }));
    } else {
        dados.gabaritos = gabaritos;
    }

    tentar(async () => {
        const json = id
            ? await requisitar(`${API.perguntas}?id=${encodeURIComponent(id)}`, 'PUT', dados)
            : await requisitar(API.perguntas, 'POST', dados);
        modalPergunta.close();
        mostrarMensagem(json.mensagem);
        await carregarPerguntas();
    });
});

/* ---- Excluir ---- */
async function excluirPergunta(id) {
    if (!confirm('Tem certeza que deseja excluir esta pergunta e suas respostas?')) return;
    const json = await requisitar(`${API.perguntas}?id=${encodeURIComponent(id)}`, 'DELETE');
    mostrarMensagem(json.mensagem);
    await carregarPerguntas();
}

/* =============================== USUÁRIOS =============================== */

const tabelaUsuarios = document.getElementById('tabela-usuarios');
const formUsuario = document.getElementById('form-usuario');

async function carregarUsuarios() {
    const json = await requisitar(API.usuarios);
    tabelaUsuarios.innerHTML = '';

    if (json.usuarios.length === 0) {
        linhaVazia(tabelaUsuarios, 4, 'Nenhum usuário cadastrado ainda.');
        return;
    }

    json.usuarios.forEach((u) => {
        const tr = criar('tr');
        tr.appendChild(criar('td', u.nome));
        tr.appendChild(criar('td', u.email));
        tr.appendChild(criar('td', u.cargo));

        const acoes = criar('div', '', 'acoes');
        acoes.appendChild(botao('Alterar', 'amarelo', () => tentar(() => editarUsuario(u.id))));
        acoes.appendChild(botao('Excluir', 'vermelho', () => tentar(() => excluirUsuario(u.id))));

        const td = criar('td');
        td.appendChild(acoes);
        tr.appendChild(td);
        tabelaUsuarios.appendChild(tr);
    });
}

function limparFormUsuario() {
    formUsuario.reset();
    document.getElementById('usuario-id').value = '';
    document.getElementById('titulo-form-usuario').textContent = 'Novo usuário';
    document.getElementById('btn-cancelar-usuario').hidden = true;
}

async function editarUsuario(id) {
    const { usuario } = await requisitar(`${API.usuarios}?id=${encodeURIComponent(id)}`);
    document.getElementById('usuario-id').value = usuario.id;
    document.getElementById('usuario-nome').value = usuario.nome;
    document.getElementById('usuario-email').value = usuario.email;
    document.getElementById('usuario-cargo').value = usuario.cargo;
    document.getElementById('titulo-form-usuario').textContent = 'Alterar usuário';
    document.getElementById('btn-cancelar-usuario').hidden = false;
    formUsuario.scrollIntoView({ behavior: 'smooth' });
}

async function excluirUsuario(id) {
    if (!confirm('Confirma a exclusão deste usuário?')) return;
    const json = await requisitar(`${API.usuarios}?id=${encodeURIComponent(id)}`, 'DELETE');
    mostrarMensagem(json.mensagem);
    await carregarUsuarios();
}

document.getElementById('btn-cancelar-usuario').addEventListener('click', limparFormUsuario);

formUsuario.addEventListener('submit', (evento) => {
    evento.preventDefault();

    const id = document.getElementById('usuario-id').value;
    const dados = {
        nome: document.getElementById('usuario-nome').value,
        email: document.getElementById('usuario-email').value,
        cargo: document.getElementById('usuario-cargo').value
    };

    tentar(async () => {
        const json = id
            ? await requisitar(`${API.usuarios}?id=${encodeURIComponent(id)}`, 'PUT', dados)
            : await requisitar(API.usuarios, 'POST', dados);
        limparFormUsuario();
        mostrarMensagem(json.mensagem);
        await carregarUsuarios();
    });
});

/* ============================== INICIALIZAÇÃO ============================== */

tentar(async () => {
    await carregarPerguntas();
    await carregarUsuarios();
});
