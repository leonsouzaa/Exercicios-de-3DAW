<?php
require_once 'conexao.php';

if (isset($_GET['acao']) &&$_GET['acao'] == 'excluir') {
    $idExcluir = intval($_GET['id']);
    if (file_exists('perguntas.txt')) {
        $linhas = file('perguntas.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (isset($linhas[$idExcluir])) {
            unset($linhas[$idExcluir]);
            file_put_contents('perguntas.txt', implode("\n", $linhas) . (count($linhas) > 0 ? "\n" : ""));
        }
    }
    header("Location: perguntas.php");
    exit;
}

$perguntas = [];
if (file_exists('perguntas.txt')) {
    $perguntas = file('perguntas.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Listar e Gerenciar Perguntas</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f4f4f9; color: #333; }
        .container { max-width: 900px; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); margin: auto; }
        h2 { color: #0056b3; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #007bff; color: white; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .btn { padding: 6px 12px; text-decoration: none; border-radius: 4px; color: white; font-size: 14px; margin-right: 4px; display: inline-block; }
        .btn-ver { background-color: #17a2b8; }
        .btn-alterar { background-color: #ffc107; color: #333; }
        .btn-excluir { background-color: #dc3545; }
        a { text-decoration: none; color: #007bff; }
        a:hover { text-decoration: underline; }
        
    
        .modal { display: none; position: fixed; z-index: 1; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4); }
        .modal-content { background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 50%; border-radius: 8px; }
        .close { color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; }
        .close:hover { color: black; }
    </style>
</head>
<body>
    <div class="container">
        <p><a href="index.php">← Voltar ao Menu</a> | <a href="criarMultipla.php">+ Nova Múltipla Escolha</a> | <a href="criarTexto.php">+ Nova Dissertativa</a></p>
        
        <h2>Gerenciamento de Perguntas</h2>

        <table>
            <thead>
                <tr>
                    <th style="width: 10%;">ID</th>
                    <th style="width: 55%;">Enunciado</th>
                    <th style="width: 15%;">Tipo</th>
                    <th style="width: 20%;">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($perguntas)): ?>
                    <tr>
                        <td colspan="4" style="text-align: center;">Nenhuma pergunta cadastrada ainda.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($perguntas as $index =>$linha): 
                        $partes = explode(';',$linha);
                        $tipo =$partes[0] ?? '';
                        $enunciado =$partes[1] ?? 'Sem enunciado';
                    ?>
                        <tr>
                            <td><?php echo $index; ?></td>
                            <td><?php echo htmlspecialchars($enunciado); ?></td>
                            <td><?php echo ($tipo == 'TEXTO') ? 'Dissertativa' : 'Múltipla'; ?></td>
                            <td>
                                <button class="btn btn-ver" onclick="abrirModal('<?php echo htmlspecialchars(addslashes($linha)); ?>')">Ver</button>
                                
                                <?php if ($tipo == 'TEXTO'): ?>
                                    <a href="alterarTexto.php?id=<?php echo $index; ?>" class="btn btn-alterar">Alterar</a>
                                <?php else: ?>
                                    <a href="alterarMultipla.php?id=<?php echo $index; ?>" class="btn btn-alterar">Alterar</a>
                                <?php endif; ?>

                                <a href="perguntas.php?acao=excluir&id=<?php echo $index; ?>" class="btn btn-excluir" onclick="return confirm('Tem certeza que deseja excluir esta pergunta?');">Excluir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div id="modalDetalhes" class="modal">
        <div class="modal-content">
            <span class="close" onclick="fecharModal()">&times;</span>
            <h3 id="modalTipo" style="color: #0056b3; margin-top: 0;"></h3>
            <p><strong>Enunciado:</strong></p>
            <p id="modalEnunciado" style="background: #e9ecef; padding: 10px; border-radius: 4px;"></p>
            <p><strong>Detalhes / Respostas:</strong></p>
            <div id="modalDetalhesConteudo" style="background: #e9ecef; padding: 10px; border-radius: 4px;"></div>
        </div>
    </div>

    <script>
        function abrirModal(linhaDados) {
            const partes = linhaDados.split(';');
            const tipo = partes[0];
            const enunciado = partes[1];
            
            document.getElementById('modalEnunciado').innerText = enunciado;
            
            let conteudoHTML = "";
            if (tipo === 'TEXTO') {
                document.getElementById('modalTipo').innerText = "Detalhes da Pergunta Dissertativa";
                const respostas = partes[2] ? partes[2].split(' || ') : [];
                conteudoHTML = "<ul>";
                respostas.forEach((r, i) => {
                    conteudoHTML += `<li>Gabarito ${i + 1}: ${r}</li>`;
                });
                conteudoHTML += "</ul>";
            } else {
                document.getElementById('modalTipo').innerText = "Detalhes da Pergunta de Múltipla Escolha";
                conteudoHTML = "<ul>";
                
                for (let i = 2; i < partes.length; i++) {
                    conteudoHTML += `<li>${partes[i]}</li>`;
                }
                conteudoHTML += "</ul>";
            }
            
            document.getElementById('modalDetalhesConteudo').innerHTML = conteudoHTML;
            document.getElementById('modalDetalhes').style.display = "block";
        }

        function fecharModal() {
            document.getElementById('modalDetalhes').style.display = "none";
        }

        window.onclick = function(event) {
            const modal = document.getElementById('modalDetalhes');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>