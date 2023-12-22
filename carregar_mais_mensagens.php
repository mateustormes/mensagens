<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "aplicacao_mensagens";

// Obter dados do POST
$nome_usuario = $_POST["nome_usuario"];
$offset = $_POST["offset"];

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Listar mais 10 mensagens do usuário (com base no offset)
$result = $conn->query("SELECT * FROM mensagens WHERE nome = '$nome_usuario' ORDER BY horario DESC LIMIT 10 OFFSET $offset");

// Exibir as mensagens
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<tr>
                <td>' . $row["id"] . '</td>
                <td>' . $row["texto"] . '</td>
                <td>' . $row["nome"] . '</td>
                <td>' . $row["horario"] . '</td>
              </tr>';
    }
}

// Fechar conexão
$conn->close();
?>
