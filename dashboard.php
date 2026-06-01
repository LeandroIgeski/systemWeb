<?php

require_once "config/auth.php";

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

<meta charset="UTF-8">

<title>Painel do Usuário</title>

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
    transition:.3s;
}

.card:hover{
    transform:translateY(-5px);
}

</style>

</head>

<body>

<div class="sidebar">

<div class="logo">
<i class="bi bi-grid-3x3-gap-fill"></i>
SistemaWeb
</div>

<a href="dashboard.php">
<i class="bi bi-speedometer2"></i>
Dashboard
</a>

<a href="logout.php">
<i class="bi bi-box-arrow-right"></i>
Sair
</a>

</div>

<div class="content">

<h1>
Olá,
<span style="color:#4285F4">
<?= $_SESSION["nome"] ?>
</span>
</h1>

<p class="text-muted">
Bem-vindo ao Sistema de Notícias.
</p>

<br>

<div class="row">

<div class="col-md-6">

<div class="card p-4">

<h5>
<i class="bi bi-person-circle"></i>
Dados do Usuário
</h5>

<hr>

<p>
<strong>Nome:</strong>
<?= $_SESSION["nome"] ?>
</p>

<p>
<strong>Tipo:</strong>
<?= ucfirst($_SESSION["tipo"]) ?>
</p>

</div>

</div>

<div class="col-md-6">

<div class="card p-4">

<h5>
<i class="bi bi-shield-check"></i>
Permissões
</h5>

<hr>

<p>
Você está autenticado no sistema.
</p>

<p>
Seu perfil possui acesso de
<strong>
<?= ucfirst($_SESSION["tipo"]) ?>
</strong>.
</p>

</div>

</div>

</div>

</body>

</html>