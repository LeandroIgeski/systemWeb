<?php

require_once "../config/auth.php";
require_once "../config/conexao.php";

// Verifica se o ID foi informado
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: listar.php");
    exit();
}

$id = (int) $_GET["id"];

// Verifica se é administrador
$isAdmin = ($_SESSION["tipo"] === "admin");

if ($isAdmin) {

    // Administrador pode reativar qualquer notícia
    $stmt = $conn->prepare("
        UPDATE noticias
        SET ativo = 1,
            desativado_por = NULL
        WHERE id = ?
    ");

    $stmt->bind_param("i", $id);

} else {

    // Usuário comum só pode reativar
    // se a notícia for dele E ele mesmo a tiver desativado
    $usuarioId = $_SESSION["id"];

    $stmt = $conn->prepare("
        UPDATE noticias
        SET ativo = 1,
            desativado_por = NULL
        WHERE id = ?
          AND usuario_id = ?
          AND desativado_por = 'autor'
    ");

    $stmt->bind_param("ii", $id, $usuarioId);
}

if ($stmt->execute()) {

    header("Location: listar.php?sucesso=reativada");
    exit();

} else {

    header("Location: listar.php?erro=reativacao");
    exit();

}

$stmt->close();
$conn->close();

?>