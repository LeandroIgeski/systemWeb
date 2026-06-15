<?php

require_once "config/admin_auth.php";

require_once "config/conexao.php";

$sqlUsuarios = "SELECT COUNT(*) AS total FROM usuarios";
$resultUsuarios = $conn->query($sqlUsuarios);
$totalUsuarios = $resultUsuarios->fetch_assoc()['total'];

$sqlAtivos = "SELECT COUNT(*) AS total FROM usuarios WHERE ativo = 1";
$resultAtivos = $conn->query($sqlAtivos);
$totalAtivos = $resultAtivos->fetch_assoc()['total'];

$sqlAdmins = "SELECT COUNT(*) AS total FROM usuarios WHERE tipo = 'admin'";
$resultAdmins = $conn->query($sqlAdmins);
$totalAdmins = $resultAdmins->fetch_assoc()['total'];

?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<title>Painel Administrativo</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="assets/css/style.css">

</head>

<body>

<div class="sidebar">

<div class="logo">
SistemaWeb
</div>

<a href="admin.php">
Dashboard
</a>

<a href="noticias/listar.php">
    <i class="bi bi-newspaper"></i>
    Gerenciar Notícias
</a>

<a href="usuarios/listar_usuario.php">
Usuários
</a>

<a href="logout.php">
Sair
</a>

</div>

<div class="content">

<h1>
Bem-vindo,
<?= $_SESSION["nome"] ?>
</h1>

<br>

<div class="row">

<div class="col-md-4">

<div class="card p-4">

<h5>Total Usuários</h5>

<h2>
<?= $totalUsuarios ?>
</h2>

</div>

</div>

<div class="col-md-4">

<div class="card p-4">

<h5>Usuários Ativos</h5>

<h2>
<?= $totalAtivos ?>
</h2>

</div>

</div>

<div class="col-md-4">

<div class="card p-4">

<h5>Administradores</h5>

<h2>
<?= $totalAdmins ?>
</h2>

</div>

</div>

</div>

<h5>Tipo de Conta</h5>

<h2>Admin</h2>

</div>

</div>

</div>

</div>

</body>

</html>