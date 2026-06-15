<?php

require_once "../config/admin_auth.php";
require_once "../config/conexao.php";

$sql = "SELECT * FROM usuarios";
$resultado = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Usuários</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="preconnect" href="https://fonts.googleapis.com">

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
    background:white;
    border:none;
    border-radius:15px;
    padding:20px;
    box-shadow:0 4px 15px rgba(0,0,0,.08);
}

.status-ativo{
    color:#34A853;
    font-weight:bold;
}

.status-inativo{
    color:#EA4335;
    font-weight:bold;
}

</style>

</head>

<body>

<div class="sidebar">

<div class="logo">
SistemaWeb
</div>

<a href="../admin.php">
Dashboard
</a>

<a href="listar_usuario.php">
Usuários
</a>

<a href="../logout.php">
Sair
</a>

</div>

<div class="content">

<div class="card">

<div class="d-flex justify-content-between align-items-center mb-4">

<h2>
Gerenciamento de Usuários
</h2>

<a
href="cadastrar_usuario.php"
class="btn btn-primary">

Novo Usuário

</a>

</div>

<table class="table table-hover">

<thead>

<tr>

<th>ID</th>
<th>Nome</th>
<th>Email</th>
<th>Tipo</th>
<th>Status</th>
<th>Ações</th>

</tr>

</thead>

<tbody>

<?php while($usuario = $resultado->fetch_assoc()) { ?>

<tr>

<td>
<?= $usuario['id'] ?>
</td>

<td>
<?= $usuario['nome'] ?>
</td>

<td>
<?= $usuario['email'] ?>
</td>

<td>

<?php if($usuario['tipo'] == 'admin'){ ?>

<span class="badge bg-success">
Administrador
</span>

<?php } else { ?>

<span class="badge bg-warning text-dark">
Comum
</span>

<?php } ?>

</td>

<td>

<?php if($usuario['ativo']){ ?>

<span class="status-ativo">
Ativo
</span>

<?php } else { ?>

<span class="status-inativo">
Inativo
</span>

<?php } ?>

</td>

<td>

<a
href="editar_usuario.php?id=<?= $usuario['id'] ?>"
class="btn btn-warning btn-sm">

Editar

</a>

<a
href="desativar_usuario.php?id=<?= $usuario['id'] ?>"
class="btn btn-danger btn-sm">

Desativar

</a>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</body>

</html>