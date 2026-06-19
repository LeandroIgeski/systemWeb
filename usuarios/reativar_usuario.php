<?php

require_once "../config/admin_auth.php";
require_once "../config/conexao.php";

if (!isset($_GET["id"])) {
    header("Location: listar_usuario.php");
    exit();
}

$id = intval($_GET["id"]);

$sql = $conn->prepare("
    UPDATE usuarios
    SET ativo = 1
    WHERE id = ?
");

$sql->bind_param("i", $id);
$sql->execute();

header("Location: listar_usuario.php");
exit();

?>