<?php
require_once "../config/auth.php";
require_once "../config/conexao.php";

$isAdmin = isset($_SESSION['tipo']) && $_SESSION['tipo'] === "admin";
$usuarioId = $_SESSION['id'];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Notícia inválida.");
}

$id = (int) $_GET['id'];

if ($isAdmin) {
    $stmt = $conn->prepare("SELECT * FROM noticias WHERE id = ?");
    $stmt->bind_param("i", $id);
} else {
    $stmt = $conn->prepare("SELECT * FROM noticias WHERE id = ? AND usuario_id = ?");
    $stmt->bind_param("ii", $id, $usuarioId);
}

$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows == 0) {
    die("Você não possui permissão para editar esta notícia.");
}

$noticia = $resultado->fetch_assoc();

$mensagem = "";
$erro = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $titulo = trim($_POST["titulo"]);
    $data = $_POST["data_noticia"];
    $texto = trim($_POST["texto"]);

    $imagemAtual = $noticia["imagem"];

    if (empty($titulo) || empty($data) || empty($texto)) {
        $erro = "Preencha todos os campos obrigatórios.";
    } else {

        if (
            isset($_FILES["imagem"]) &&
            $_FILES["imagem"]["error"] != UPLOAD_ERR_NO_FILE
        ) {

            $permitidas = ["jpg", "jpeg", "png", "webp"];

            $ext = strtolower(pathinfo(
                $_FILES["imagem"]["name"],
                PATHINFO_EXTENSION
            ));

            if (!in_array($ext, $permitidas)) {

                $erro = "Formato de imagem inválido.";

            } elseif ($_FILES["imagem"]["size"] > 2 * 1024 * 1024) {

                $erro = "A imagem deve possuir no máximo 2 MB.";

            } else {

                if (!is_dir("uploads")) {
                    mkdir("uploads", 0777, true);
                }

                $novoNome = uniqid("noticia_", true) . "." . $ext;
                $destino = "uploads/" . $novoNome;

                if (move_uploaded_file($_FILES["imagem"]["tmp_name"], $destino)) {

                    if (!empty($imagemAtual) && file_exists($imagemAtual)) {
                        @unlink($imagemAtual);
                    }

                    $imagemAtual = $destino;

                } else {

                    $erro = "Erro ao salvar a nova imagem.";

                }
            }
        }

        if (empty($erro)) {

            if ($isAdmin) {

                $update = $conn->prepare("
                    UPDATE noticias
                    SET titulo=?, data_noticia=?, texto=?, imagem=?
                    WHERE id=?
                ");

                $update->bind_param(
                    "ssssi",
                    $titulo,
                    $data,
                    $texto,
                    $imagemAtual,
                    $id
                );

            } else {

                $update = $conn->prepare("
                    UPDATE noticias
                    SET titulo=?, data_noticia=?, texto=?, imagem=?
                    WHERE id=? AND usuario_id=?
                ");

                $update->bind_param(
                    "ssssii",
                    $titulo,
                    $data,
                    $texto,
                    $imagemAtual,
                    $id,
                    $usuarioId
                );

            }

            if ($update->execute()) {

                $mensagem = "Notícia atualizada com sucesso.";

                $noticia["titulo"] = $titulo;
                $noticia["data_noticia"] = $data;
                $noticia["texto"] = $texto;
                $noticia["imagem"] = $imagemAtual;

            } else {

                $erro = "Erro ao atualizar a notícia.";

            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

<meta charset="UTF-8">

<title>Editar Notícia</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-lg-8">

<div class="card shadow">

<div class="card-header bg-warning">

<h3 class="mb-0 text-dark">
Editar Notícia
</h3>

</div>

<div class="card-body">

<?php if (!empty($mensagem)): ?>
<div class="alert alert-success">
<?= htmlspecialchars($mensagem) ?>
</div>
<?php endif; ?>

<?php if (!empty($erro)): ?>
<div class="alert alert-danger">
<?= htmlspecialchars($erro) ?>
</div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">

<div class="mb-3">

<label class="form-label">
Título
</label>

<input
type="text"
name="titulo"
class="form-control"
required
value="<?= htmlspecialchars($noticia['titulo']) ?>"
>

</div>

<div class="mb-3">

<label class="form-label">
Data
</label>

<input
type="date"
name="data_noticia"
class="form-control"
required
value="<?= htmlspecialchars($noticia['data_noticia']) ?>"
>

</div>

<div class="mb-3">

<label class="form-label">
Texto
</label>

<textarea
name="texto"
rows="8"
class="form-control"
required
><?= htmlspecialchars($noticia['texto']) ?></textarea>

</div>

<?php if (!empty($noticia["imagem"])): ?>

<div class="mb-3">

<label class="form-label">
Imagem Atual
</label>

<br>

<img
src="<?= htmlspecialchars($noticia["imagem"]) ?>"
class="img-fluid rounded"
style="max-height:250px;"
>

</div>

<?php endif; ?>

<div class="mb-3">

<label class="form-label">
Trocar imagem
</label>

<input
type="file"
name="imagem"
class="form-control"
accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
>

<small class="text-muted">
Caso não selecione outra imagem, a atual será mantida.
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
class="btn btn-warning"
>
Salvar Alterações
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