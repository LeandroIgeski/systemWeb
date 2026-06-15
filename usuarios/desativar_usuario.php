<?php

require_once "../config/admin_auth.php";
require_once "../config/conexao.php";

$id = $_GET["id"];

$sql = "UPDATE usuarios
SET ativo = 0
WHERE id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $id);

$stmt->execute();

header("Location: listar.php");
exit();