<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"];
    $mensagem = $_POST["mensagem"];

    function criptografarMensagem($mensagem, $chave) {
        // Gerar um vetor de inicialização aleatório
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    
        // Criptografar a mensagem usando AES-256-CBC
        $mensagemCriptografada = openssl_encrypt($mensagem, 'aes-256-cbc', $chave, 0, $iv);
    
        // Retornar a mensagem criptografada e o vetor de inicialização
        return base64_encode($iv . $mensagemCriptografada);
    }
    
    $chaveSecreta = "chave_secreta";
    
    $mensagemCriptografada = criptografarMensagem($mensagem, $chaveSecreta);
    

    // Inserir mensagem no banco de dados
    $conn = new mysqli("br892.hostgator.com.br", "engine66_mateus", "Mub62021*", "engine66_aplicacao_mensagens");

    if ($conn->connect_error) {
        die("Conexão falhou: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO mensagens (texto, nome) VALUES (?, ?)");
    $stmt->bind_param("ss", $mensagemCriptografada, $nome);
    $stmt->execute();

    $stmt->close();
    $conn->close();

    // Redirecionar de volta para a página de mensagens
    header("Location: mensagens.php?nome=$nome");
    exit();
}
?>
