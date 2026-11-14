// O JavaScript só envia os dados, recebe o resultado e mostra na tela.
// Nenhum cálculo é feito aqui: tudo é responsabilidade do PHP.

const form = document.getElementById('form-calculo');
const campoResultado = document.getElementById('resultado');

form.addEventListener('submit', async (evento) => {
    evento.preventDefault();

    const dados = {
        numero1: document.getElementById('numero1').value,
        numero2: document.getElementById('numero2').value,
        operacao: document.getElementById('operacao').value
    };

    try {
        const resposta = await fetch('calcular.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(dados)
        });

        const json = await resposta.json();

        if (json.sucesso) {
            mostrar(json.resultado, false);
        } else {
            mostrar(json.erro, true);
        }
    } catch (erro) {
        mostrar('Não foi possível se comunicar com o servidor.', true);
    }
});

function mostrar(texto, ehErro) {
    campoResultado.textContent = ehErro ? texto : 'Resultado: ' + texto;
    campoResultado.classList.toggle('erro', ehErro);
}
