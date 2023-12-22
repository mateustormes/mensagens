<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"];

    // Validar tamanho do nome
    if (strlen($nome) > 50) {
        die("Erro: O nome não pode ter mais de 50 caracteres.");
    }

    // Inserir usuário no banco de dados
    $conn = new mysqli("localhost", "root", "", "aplicacao_mensagens");

    if ($conn->connect_error) {
        die("Conexão falhou: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO usuarios (nome) VALUES (?)");
    $stmt->bind_param("s", $nome);
    $stmt->execute();

    $stmt->close();
    $conn->close();

    // Redirecionar de volta para a página inicial
    header("Location: index.php");
    exit();
}
?>
