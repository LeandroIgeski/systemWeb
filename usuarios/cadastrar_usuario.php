<?php

require_once "../config/admin_auth.php";
require_once "../config/conexao.php";

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nome = trim($_POST["nome"]);
    $email = trim($_POST["email"]);
    $senha = $_POST["senha"];
    $tipo = $_POST["tipo"];

    $verifica = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $verifica->bind_param("s", $email);
    $verifica->execute();

    if ($verifica->get_result()->num_rows > 0) {

        $msg = "Este e-mail já está cadastrado.";

    } else {

        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        $sql = $conn->prepare("
            INSERT INTO usuarios
            (nome,email,senha,tipo,ativo)
            VALUES
            (?, ?, ?, ?, 1)
        ");

        $sql->bind_param(
            "ssss",
            $nome,
            $email,
            $senhaHash,
            $tipo
        );

        if ($sql->execute()) {

            header("Location: listar_usuario.php");
            exit();

        } else {

            $msg = "Erro ao cadastrar usuário.";

        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Cadastrar Usuário</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    background:#f5f7fb;
}

.card{
    border:none;
    border-radius:15px;
    box-shadow:0 5px 20px rgba(0,0,0,.1);
}

</style>

</head>

<body>

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-7">

            <div class="card">

                <div class="card-header bg-primary text-white">

                    <h3 class="mb-0">
                        Cadastrar Usuário
                    </h3>

                </div>

                <div class="card-body">

                    <?php if($msg != ""){ ?>

                        <div class="alert alert-danger">

                            <?= $msg ?>

                        </div>

                    <?php } ?>

                    <form method="POST">

                        <div class="mb-3">

                            <label class="form-label">
                                Nome
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                name="nome"
                                required>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Email
                            </label>

                            <input
                                type="email"
                                class="form-control"
                                name="email"
                                required>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Senha
                            </label>

                            <input
                                type="password"
                                class="form-control"
                                name="senha"
                                required>

                        </div>

                        <div class="mb-4">

                            <label class="form-label">
                                Tipo de usuário
                            </label>

                            <select
                                name="tipo"
                                class="form-select">

                                <option value="comum">
                                    Usuário Comum
                                </option>

                                <option value="admin">
                                    Administrador
                                </option>

                            </select>

                        </div>

                        <div class="d-flex justify-content-between">

                            <a href="listar_usuario.php" class="btn btn-secondary">
                                Voltar
                            </a>

                            <button
                                type="submit"
                                class="btn btn-primary">

                                Cadastrar Usuário

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>