<?php
require_once "config/auth.php";
require_once "config/conexao.php";

// dados do admin (só se for admin)
$totalUsuarios = 0;
$totalAtivos = 0;
$totalAdmins = 0;
if ($_SESSION["tipo"] === "admin") {

    $totalUsuarios = $conn->query("SELECT COUNT(*) AS total FROM usuarios")->fetch_assoc()['total'];

    $totalAtivos = $conn->query("SELECT COUNT(*) AS total FROM usuarios WHERE ativo = 1")->fetch_assoc()['total'];

    $totalAdmins = $conn->query("SELECT COUNT(*) AS total FROM usuarios WHERE tipo = 'admin'")->fetch_assoc()['total'];
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
<meta charset="UTF-8">

<title>Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
body{
    font-family:'Poppins',sans-serif;
    background:#f5f7fb;
}

.sidebar{
    width:250px;
    height:100vh;
    position:fixed;
    background:white;
    box-shadow:0 0 15px rgba(0,0,0,.1);
    padding:20px;
}

.logo{
    font-size:24px;
    font-weight:600;
    color:#4285F4;
    margin-bottom:30px;
}

.sidebar a{
    display:block;
    text-decoration:none;
    color:#444;
    padding:12px;
    border-radius:10px;
    margin-bottom:10px;
}

.sidebar a:hover{
    background:#4285F4;
    color:white;
}

.content{
    margin-left:270px;
    padding:30px;
}

.card{
    border:none;
    border-radius:15px;
    box-shadow:0 4px 15px rgba(0,0,0,.08);
}
</style>

</head>

<body>

<div class="sidebar">

    <div class="logo">SistemaWeb</div>

    <a href="dashboard.php">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>

    <a href="noticias/listar.php">
        <i class="bi bi-newspaper"></i> Notícias
    </a>

    <!-- 🔥 só admin vê -->
    <?php if ($_SESSION["tipo"] === "admin"): ?>
        <a href="usuarios/listar_usuario.php">
            <i class="bi bi-people"></i> Usuários
        </a>
    <?php endif; ?>

    <a href="logout.php">
        <i class="bi bi-box-arrow-right"></i> Sair
    </a>

</div>

<div class="content">

    <h2>Olá, <?= htmlspecialchars($_SESSION["nome"]) ?></h2>

    <p class="text-muted">
        Bem-vindo ao sistema de notícias.
    </p>

    <!-- 🔥 PAINEL ADMIN -->
    <?php if ($_SESSION["tipo"] === "admin"): ?>

        <div class="row">

            <div class="col-md-4">
                <div class="card p-3">
                    <h5>Total Usuários</h5>
                    <h2><?= $totalUsuarios ?></h2>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-3">
                    <h5>Usuários Ativos</h5>
                    <h2><?= $totalAtivos ?></h2>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-3">
                    <h5>Admins</h5>
                    <h2><?= $totalAdmins ?></h2>
                </div>
            </div>

        </div>

    <?php else: ?>

        <!-- 🔥 PAINEL USUÁRIO -->
        <div class="card p-4 mt-4">
            <h5>Área do Usuário</h5>
            <p>Você pode cadastrar e visualizar notícias.</p>
        </div>

    <?php endif; ?>

</div>

</body>
</html>