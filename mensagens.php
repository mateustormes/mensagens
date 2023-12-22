<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "aplicacao_mensagens";

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

       

        <h2>Mensagens de <?php echo $nome_usuario; ?></h2>
        <?php
        if ($result->num_rows > 0) {
            echo '<table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Texto</th>
                            <th>Nome</th>
                            <th>Horário</th>
                        </tr>
                    </thead>
                    <tbody>';
            while ($row = $result->fetch_assoc()) {
                echo '<tr>
                        <td>' . $row["id"] . '</td>
                        <td>' . $row["texto"] . '</td>
                        <td>' . $row["nome"] . '</td>
                        <td>' . $row["horario"] . '</td>
                    </tr>';
            }
            echo '</tbody></table>';

            // Adicionar funcionalidade "Mostrar Mais"
            if ($total_mensagens > 10) {
                echo '<button type="button" class="btn btn-primary" id="mostrarMais">Mostrar Mais</button>';
            }
        } else {
            echo '<p>Não há mensagens para este usuário.</p>';
        }
        ?>
        
        <!-- Modal para confirmar exclusão de conversas -->
        <div class="modal fade" id="modalApagarConversas" tabindex="-1" role="dialog" aria-labelledby="modalApagarConversasLabel" aria-hidden="true">
            <!-- Conteúdo do modal aqui -->
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
    });
</script>


</body>
</html>

<?php
// Fechar conexão
$conn->close();
?>
