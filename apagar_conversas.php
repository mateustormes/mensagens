<?php
$servername = "br892.hostgator.com.br";
$username = "engine66_mateus";
$password = "Mub62021*";
$dbname = "engine66_aplicacao_mensagens";

// Obtém o nome do usuário da URL
$nome_usuario = $_GET["nome"];

// Cria conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Exclui todas as mensagens do usuário
$conn->query("DELETE FROM mensagens WHERE nome = '$nome_usuario'");

// Fecha a conexão
$conn->close();

// Redireciona de volta para a página de mensagens
header("Location: mensagens.php?nome=$nome_usuario");
exit();
?>
