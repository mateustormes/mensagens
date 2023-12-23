<?php
$servername = "br892.hostgator.com.br";
$username = "engine66_mateus";
$password = "Mub62021*";
$dbname = "engine66_aplicacao_mensagens";

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Obter nome do usuário da URL
$nome_usuario = $_GET["nome"];

// Listar mensagens do usuário (apenas 10 mais recentes)
$result = $conn->query("SELECT * FROM mensagens WHERE nome = '$nome_usuario' ORDER BY horario DESC LIMIT 10");
function descriptografarMensagem($mensagemCriptografada, $chave) {
    // Decodificar a mensagem criptografada
    $dadosDecodificados = base64_decode($mensagemCriptografada);

    // Extrair o vetor de inicialização e a mensagem criptografada
    $ivLength = openssl_cipher_iv_length('aes-256-cbc');
    $iv = substr($dadosDecodificados, 0, $ivLength);
    $mensagemCriptografada = substr($dadosDecodificados, $ivLength);

    // Descriptografar a mensagem usando AES-256-CBC
    $mensagemOriginal = openssl_decrypt($mensagemCriptografada, 'aes-256-cbc', $chave, 0, $iv);

    // Retornar a mensagem original
    return $mensagemOriginal;
}
$chaveSecreta = "chave_secreta";
// Listar todas as mensagens para contagem
$total_mensagens = $conn->query("SELECT COUNT(*) AS total FROM mensagens WHERE nome = '$nome_usuario'")->fetch_assoc()['total'];

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <title>Mensagens de <?php echo $nome_usuario; ?></title>
    <!-- Incluir o arquivo clipboard.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.8/clipboard.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <a href="index.php" class="btn btn-success">Voltar</a>
        <!-- Botão para apagar todas as conversas -->
        <button style="align:right" type="button" class="btn btn-danger" data-toggle="modal" data-target="#modalApagarConversas">
            Apagar Todas as Conversas
        </button>
        <!-- Formulário para enviar mensagem (movido para cima) -->
        <form action="enviar_mensagem.php" method="post">
            <div class="form-group">
                <label for="mensagem">Enviar Mensagem:</label>
                <input type="text" class="form-control" id="mensagem" name="mensagem" required>
            </div>
            <input type="hidden" name="nome" value="<?php echo $nome_usuario; ?>">
            <button type="submit" class="btn btn-primary">Enviar Mensagem</button>
        </form>

        <h2>Mensagens de <?php echo $nome_usuario; ?></h2>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Ações</th>
                        <th>Texto</th>
                        <th>Nome</th>
                        <th>Horário</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $mensagemDescriptografada = descriptografarMensagem($row["texto"], $chaveSecreta);
                            echo '<tr>
                                    <td>
                                        <button class="btn btn-primary copiar" data-clipboard-text="' . $mensagemDescriptografada . '">Copiar</button>
                                    </td>
                                    <td>' . $row["texto"]. '</td>
                                    <td>' . $row["nome"] . '</td>
                                    <td>' . $row["horario"] . '</td>
                                </tr>';
                        }

                        // Adicionar funcionalidade "Mostrar Mais"
                        if ($total_mensagens > 10) {
                            echo '<tr>
                                    <td colspan="5">
                                        <button type="button" class="btn btn-primary" id="mostrarMais">Mostrar Mais</button>
                                    </td>
                                </tr>';
                        }
                    } else {
                        echo '<tr>
                                <td colspan="5">Não há mensagens para este usuário.</td>
                            </tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Modal para confirmar exclusão de conversas -->
        <div class="modal fade" id="modalApagarConversas" tabindex="-1" role="dialog" aria-labelledby="modalApagarConversasLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalApagarConversasLabel">Confirmar Exclusão</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Tem certeza de que deseja apagar todas as conversas de <?php echo $nome_usuario; ?>?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <a href="apagar_conversas.php?nome=<?php echo $nome_usuario; ?>" class="btn btn-danger">Apagar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

    <script>
        // Adicionar funcionalidade "Mostrar Mais"
        $(document).ready(function(){
            var offset = 10;
            var totalMensagens = <?php echo $total_mensagens; ?>;

            $("#mostrarMais").click(function(){
                $.ajax({
                    url: "carregar_mais_mensagens.php",
                    type: "POST",
                    data: {nome_usuario: "<?php echo $nome_usuario; ?>", offset: offset},
                    success: function(data){
                        $("tbody").append(data);
                        offset += 10;

                        // Ocultar o botão "Mostrar Mais" se todas as mensagens foram carregadas
                        if (offset >= totalMensagens) {
                            $("#mostrarMais").hide();
                        }
                    }
                });
            });

            // Inicializar a biblioteca clipboard.js
            new ClipboardJS('.copiar');
        });
    </script>
</body>
</html>

<?php
// Fechar conexão
$conn->close();
?>
