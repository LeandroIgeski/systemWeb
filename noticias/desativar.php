<?php

require_once "../config/auth.php";
require_once "../config/conexao.php";

// Verifica se foi informado um ID válido
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    die("ID da notícia inválido.");
}

$id = (int) $_GET["id"];

// Verifica se o usuário é administrador
$isAdmin = isset($_SESSION["tipo"]) && $_SESSION["tipo"] === "admin";

if ($isAdmin) {

    // Administrador pode desativar qualquer notícia
    $stmt = $conn->prepare("
        UPDATE noticias
        SET ativo = 0,
            desativado_por = 'admin'
        WHERE id = ?
    ");

    $stmt->bind_param("i", $id);

} else {

    // Usuário comum só pode desativar as próprias notícias
    $usuarioId = $_SESSION["id"];

    $stmt = $conn->prepare("
        UPDATE noticias
        SET ativo = 0,
            desativado_por = 'autor'
        WHERE id = ?
          AND usuario_id = ?
    ");

    $stmt->bind_param("ii", $id, $usuarioId);
}

if ($stmt->execute()) {

    if ($stmt->affected_rows > 0) {

        header("Location: listar.php?sucesso=desativada");
        exit();

    } else {

        header("Location: listar.php?erro=permissao");
        exit();

    }

} else {

    header("Location: listar.php?erro=bd");
    exit();

}

$stmt->close();
$conn->close();

?>