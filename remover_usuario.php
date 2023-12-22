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

// Obter nome do usuário a ser removido
$nome_usuario = $_GET["nome"];

// Remover mensagens do usuário
$conn->query("DELETE FROM mensagens WHERE nome = '$nome_usuario'");

// Remover usuário
$conn->query("DELETE FROM usuarios WHERE nome = '$nome_usuario'");

// Redirecionar de volta para a página principal
header("Location: index.php");
exit();
?>
