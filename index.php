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
// Verificar se há mensagem de erro
$erro = isset($_GET['erro']) ? $_GET['erro'] : '';
// Listar usuários
$result = $conn->query("SELECT * FROM usuarios");

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <title>Lista de Usuários</title>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>

</head>
<body>
    <div class="container mt-5">
        <h2>Lista de Usuários</h2>
        <?php
        if (!empty($erro)) {
            // Exibir modal de erro
            echo '<script>
                    $(document).ready(function(){
                        $("#modalErro").modal("show");
                    });
                  </script>';
            }
        if ($result->num_rows > 0) {
            echo '<table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>';
                    while ($row = $result->fetch_assoc()) {
                        echo '<tr>
                                <td>' . $row["id"] . '</td>
                                <td>' . $row["nome"] . '</td>
                                <td>
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalSenha" data-nome="' . $row["nome"] . '">
                                        Entrar
                                    </button>
                                    <a href="remover_usuario.php?nome=' . $row["nome"] . '" class="btn btn-danger">Remover Usuário</a>
                                </td>
                              </tr>';
                    }
                    
            echo '</tbody></table>';
        } else {
            echo '<p>Não há usuários cadastrados.</p>';
        }
        ?>
        <!-- Botão para abrir modal -->
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalCadastrar">
            Cadastrar Usuário
        </button>
    </div>

   <!-- Modal para cadastrar usuário -->
    <div class="modal fade" id="modalCadastrar" tabindex="-1" role="dialog" aria-labelledby="modalCadastrarLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="cadastrar_usuario.php" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCadastrarLabel">Cadastrar Usuário</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="nome">Seu Nome:</label>
                            <input type="text" class="form-control" id="nome" name="nome" maxlength="50" required>
                        </div>
                        <div class="form-group">
                            <label for="senha">Sua Senha:</label>
                            <input type="password" class="form-control" id="senha" name="senha" maxlength="20" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-primary">Cadastrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal para verificar a senha -->
    <div class="modal fade" id="modalSenha" tabindex="-1" role="dialog" aria-labelledby="modalSenhaLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="verificar_senha.php" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalSenhaLabel">Verificar Senha</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="senha">Digite sua Senha:</label>
                            <input type="password" class="form-control" id="senha" name="senha" maxlength="20" required>
                            <input type="hidden" id="modalSenhaNome" name="nome" value="">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-primary">Entrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal de erro -->
    <div class="modal fade" id="modalErro" tabindex="-1" role="dialog" aria-labelledby="modalErroLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalErroLabel">Erro</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?php echo $erro; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script> -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

    <script>
        $('#modalSenha').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var recipient = button.data('nome');
            var modal = $(this);
            modal.find('#modalSenhaNome').val(recipient);
        });
    </script>
</body>
</html>

<?php
// Fechar conexão
$conn->close();
?>
