<?php

require_once "../config/auth.php";
require_once "../config/conexao.php";

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    die("Notícia inválida.");
}

$id = (int) $_GET["id"];

$isAdmin = isset($_SESSION["tipo"]) && $_SESSION["tipo"] === "admin";

if ($isAdmin) {

    $stmt = $conn->prepare("
        SELECT n.*, u.nome AS autor
        FROM noticias n
        INNER JOIN usuarios u
            ON u.id = n.usuario_id
        WHERE n.id = ?
    ");

    $stmt->bind_param("i", $id);

} else {

    $stmt = $conn->prepare("
        SELECT n.*, u.nome AS autor
        FROM noticias n
        INNER JOIN usuarios u
            ON u.id = n.usuario_id
        WHERE n.id = ?
          AND n.ativo = 1
    ");

    $stmt->bind_param("i", $id);

}

$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows == 0) {
    header("Location: listar.php?erro=indisponivel");
    exit();
}

$noticia = $resultado->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <title><?= htmlspecialchars($noticia["titulo"]) ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>

        body{
            background:#f4f6f9;
        }

        .card{
            border:none;
            border-radius:15px;
            box-shadow:0 3px 12px rgba(0,0,0,.10);
        }

        .imagem-noticia{
            width:100%;
            max-height:500px;
            object-fit:cover;
            border-radius:10px;
        }

        .texto{
            white-space:pre-wrap;
            text-align:justify;
            font-size:1.05rem;
            line-height:1.8;
        }

    </style>

</head>

<body>

<div class="container mt-5 mb-5">

    <div class="card">

        <div class="card-body p-4">

            <h1 class="mb-3">
                <?= htmlspecialchars($noticia["titulo"]) ?>
            </h1>

            <p class="text-muted">

                <strong>Autor:</strong>
                <?= htmlspecialchars($noticia["autor"]) ?>

                |

                <strong>Data:</strong>
                <?= date("d/m/Y", strtotime($noticia["data_noticia"])) ?>

            </p>

            <?php if (!empty($noticia["imagem"])): ?>

                <img
                    src="<?= htmlspecialchars($noticia["imagem"]) ?>"
                    class="imagem-noticia mb-4"
                    alt="Imagem da notícia"
                >

            <?php endif; ?>

            <div class="texto">

                <?= nl2br(htmlspecialchars($noticia["texto"])) ?>

            </div>

            <hr>

            <a href="listar.php" class="btn btn-secondary">
                ← Voltar para a lista
            </a>

        </div>

    </div>

</div>

</body>

</html>