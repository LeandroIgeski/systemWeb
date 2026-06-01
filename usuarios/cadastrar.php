<?php

require_once "../config/admin_auth.php";
require_once "../config/conexao.php";

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nome = trim($_POST["nome"]);
    $email = trim($_POST["email"]);
    $senha = $_POST["senha"];
    $tipo = $_POST["tipo"];

    $verifica = $conn->prepare(
        "SELECT id FROM usuarios WHERE email = ?"
    );

    $verifica->bind_param("s", $email);
    $verifica->execute();

    if ($verifica->get_result()->num_rows > 0) {

        $msg = "Este e-mail já está cadastrado.";

    } else {

        $senhaHash = password_hash(
            $senha,
            PASSWORD_DEFAULT
        );

        $sql = $conn->prepare(
            "INSERT INTO usuarios
            (nome,email,senha,tipo,ativo)
            VALUES
            (?, ?, ?, ?, 1)"
        );

        $sql->bind_param(
            "ssss",
            $nome,
            $email,
            $senhaHash,
            $tipo
        );

        if ($sql->execute()) {

            header("Location: listar.php");
            exit();

        } else {

            $msg = "Erro ao cadastrar usuário.";

        }
    }
}

?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<title>Cadastrar Usuário</title>

</head>

<body>

<h1>Cadastrar Usuário</h1>

<form method="POST">

<label>Nome</label>
<br>
<input
type="text"
name="nome"
required>
<br><br>

<label>Email</label>
<br>
<input
type="email"
name="email"
required>
<br><br>

<label>Senha</label>
<br>
<input
type="password"
name="senha"
required>
<br><br>

<label>Tipo</label>
<br>

<select name="tipo">

<option value="comum">
Usuário Comum
</option>

<option value="admin">
Administrador
</option>

</select>

<br><br>

<button type="submit">
Cadastrar
</button>

</form>

<br>

<p style="color:red;">
<?= $msg ?>
</p>

<a href="listar.php">
Voltar
</a>

</body>

</html>