<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"];
    $mensagem = $_POST["mensagem"];

   

    // Inserir mensagem no banco de dados
    $conn = new mysqli("localhost", "root", "", "aplicacao_mensagens");

    if ($conn->connect_error) {
        die("Conexão falhou: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO mensagens (texto, nome) VALUES (?, ?)");
    $stmt->bind_param("ss", $mensagem, $nome);
    $stmt->execute();

    $stmt->close();
    $conn->close();

    // Redirecionar de volta para a página de mensagens
    header("Location: mensagens.php?nome=$nome");
    exit();
}
?>
