<?php
require_once "../config/auth.php";
require_once "../config/conexao.php";

$mensagem = "";
$erro = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $titulo = trim($_POST["titulo"] ?? "");
    $data = $_POST["data_noticia"] ?? "";
    $texto = trim($_POST["texto"] ?? "");

    if (empty($titulo) || empty($data) || empty($texto)) {
        $erro = "Preencha todos os campos obrigatórios.";
    } else {

        $caminhoImagem = null;

        // Upload da imagem (opcional)
        if (isset($_FILES["imagem"]) && $_FILES["imagem"]["error"] !== UPLOAD_ERR_NO_FILE) {

            if ($_FILES["imagem"]["error"] !== UPLOAD_ERR_OK) {
                $erro = "Erro ao enviar a imagem.";
            } else {

                $permitidas = ["jpg", "jpeg", "png", "webp"];

                $extensao = strtolower(pathinfo(
                    $_FILES["imagem"]["name"],
                    PATHINFO_EXTENSION
                ));

                if (!in_array($extensao, $permitidas)) {

                    $erro = "Formato de imagem inválido.";

                } elseif ($_FILES["imagem"]["size"] > 2 * 1024 * 1024) {

                    $erro = "A imagem deve ter no máximo 2 MB.";

                } else {

                    if (!is_dir("uploads")) {
                        mkdir("uploads", 0777, true);
                    }

                    $novoNome = uniqid("noticia_", true) . "." . $extensao;

                    $destino = "uploads/" . $novoNome;

                    if (move_uploaded_file($_FILES["imagem"]["tmp_name"], $destino)) {

                        $caminhoImagem = $destino;

                    } else {

                        $erro = "Não foi possível salvar a imagem.";

                    }
                }
            }
        }

        if (empty($erro)) {

            $usuarioId = $_SESSION["id"];

            $stmt = $conn->prepare("
                INSERT INTO noticias
                (titulo, data_noticia, texto, imagem, usuario_id, ativo)
                VALUES (?, ?, ?, ?, ?, 1)
            ");

            $stmt->bind_param(
                "ssssi",
                $titulo,
                $data,
                $texto,
                $caminhoImagem,
                $usuarioId
            );

            if ($stmt->execute()) {

                $mensagem = "Notícia cadastrada com sucesso!";

            } else {

                $erro = "Erro ao cadastrar a notícia.";

            }

            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <title>Cadastrar Notícia</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-lg-8">

            <div class="card shadow">

                <div class="card-header bg-primary text-white">

                    <h3 class="mb-0">
                        Cadastro de Notícia
                    </h3>

                </div>

                <div class="card-body">

                    <?php if (!empty($mensagem)) : ?>

                        <div class="alert alert-success">
                            <?= htmlspecialchars($mensagem) ?>
                        </div>

                    <?php endif; ?>

                    <?php if (!empty($erro)) : ?>

                        <div class="alert alert-danger">
                            <?= htmlspecialchars($erro) ?>
                        </div>

                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data">

                        <div class="mb-3">

                            <label class="form-label">
                                Título *
                            </label>

                            <input
                                type="text"
                                name="titulo"
                                class="form-control"
                                maxlength="255"
                                required
                            >

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Data da notícia *
                            </label>

                            <input
                                type="date"
                                name="data_noticia"
                                class="form-control"
                                required
                            >

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Conteúdo *
                            </label>

                            <textarea
                                name="texto"
                                rows="8"
                                class="form-control"
                                required
                            ></textarea>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Imagem
                            </label>

                            <input
                                type="file"
                                name="imagem"
                                class="form-control"
                                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                            >

                            <small class="text-muted">
                                Formatos permitidos: JPG, JPEG, PNG e WEBP (máximo 2 MB).
                            </small>

                        </div>

                        <div class="d-flex justify-content-between">

                            <a
                                href="listar.php"
                                class="btn btn-secondary"
                            >
                                Voltar
                            </a>

                            <button
                                type="submit"
                                class="btn btn-success"
                            >
                                Salvar Notícia
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

</body>

</html>