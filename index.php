<?php
session_start();
require_once("config/conexao.php");

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST["email"];
    $senha = $_POST["senha"];

    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $resultado = $stmt->get_result();

    if($resultado->num_rows > 0){

        $usuario = $resultado->fetch_assoc();

        if(!$usuario["ativo"]){
            $msg = "Usuário desativado.";
        }
        else if(password_verify($senha, $usuario["senha"])){

            $_SESSION["id"] = $usuario["id"];
            $_SESSION["nome"] = $usuario["nome"];
            $_SESSION["tipo"] = $usuario["tipo"];

            if($usuario["tipo"] == "admin"){
                header("Location: admin.php");
            }else{
                header("Location: dashboard.php");
            }

            exit();
        }else{
            $msg = "Senha inválida.";
        }

    }else{
        $msg = "Email não encontrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>SistemaWeb</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>

body{
    font-family:'Poppins',sans-serif;
    background:linear-gradient(
        135deg,
        #4285F4,
        #34A853
    );
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
}

.login-card{
    background:white;
    width:420px;
    padding:40px;
    border-radius:20px;
    box-shadow:0 10px 30px rgba(0,0,0,.15);
}

.logo{
    text-align:center;
    margin-bottom:25px;
}

.logo h1{
    font-weight:600;
    color:#4285F4;
}

.form-control{
    border-radius:10px;
    padding:12px;
}

.btn-google{
    background:#4285F4;
    color:white;
    border:none;
    border-radius:10px;
    padding:12px;
    width:100%;
    font-weight:500;
}

.btn-google:hover{
    background:#3367D6;
    color:white;
}

.error{
    color:#EA4335;
    text-align:center;
    margin-top:15px;
}

.subtitle{
    text-align:center;
    color:#666;
    margin-bottom:25px;
}

</style>

</head>

<body>

<div class="login-card">

<div class="logo">

<h1>SistemaWeb</h1>

<p class="subtitle">
Controle de Usuários e Notícias
</p>

</div>

<form method="POST">

<div class="mb-3">

<label class="form-label">
Email
</label>

<input
type="email"
name="email"
class="form-control"
required>

</div>

<div class="mb-3">

<label class="form-label">
Senha
</label>

<input
type="password"
name="senha"
class="form-control"
required>

</div>

<button
type="submit"
class="btn-google">

Entrar

</button>

</form>

<?php if(!empty($msg)){ ?>

<div class="error">

<?= $msg ?>

</div>

<?php } ?>

</div>

</body>

</html>