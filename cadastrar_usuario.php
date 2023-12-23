<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"];
    $senha = $_POST["senha"];

    // Validar tamanho do nome
    if (strlen($nome) > 50 || strlen($senha) > 20) {
        header("Location: index.php?erro=Erro: O nome não pode ter mais de 50 caracteres e a senha não pode ter mais de 20 caracteres.");
        exit();
    }

    // Conectar ao banco de dados
    $conn = new mysqli("br892.hostgator.com.br", "engine66_mateus", "Mub62021*", "engine66_aplicacao_mensagens");

    // Verificar a conexão
    if ($conn->connect_error) {
        header("Location: index.php?erro=Conexão falhou: " . $conn->connect_error);
        exit();
    }

    // Verificar se o usuário já existe
    $check_user_query = $conn->prepare("SELECT id FROM usuarios WHERE nome = ?");
    $check_user_query->bind_param("s", $nome);
    $check_user_query->execute();
    $check_user_query->store_result();

    // Se já existir um usuário com esse nome, retorne um erro
    if ($check_user_query->num_rows > 0) {
        header("Location: index.php?erro=Erro: Já existe um usuário cadastrado com esse nome.");
        exit();
    }

    // Preparar a consulta SQL para inserir o usuário com senha
    $stmt = $conn->prepare("INSERT INTO usuarios (nome, senha) VALUES (?, ?)");
    $stmt->bind_param("ss", $nome, $senha); // "ss" indica dois parâmetros de string
    $stmt->execute();

    $stmt->close();
    $conn->close();

    // Redirecionar de volta para a página inicial
    header("Location: index.php");
    exit(); // Garante que o script não continue após o redirecionamento
}
?>
