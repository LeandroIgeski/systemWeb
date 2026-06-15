<?php
require_once "../config/auth.php";
require_once "../config/conexao.php";

$isAdmin = (isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'admin');

if ($isAdmin) {

    $sql = "
        SELECT n.*, u.nome AS autor
        FROM noticias n
        INNER JOIN usuarios u
            ON u.id = n.usuario_id
        ORDER BY n.data_noticia DESC
    ";

    $resultado = $conn->query($sql);

} else {

    $usuarioId = $_SESSION['id'];

    $sql = "
        SELECT n.*, u.nome AS autor
        FROM noticias n
        INNER JOIN usuarios u
            ON u.id = n.usuario_id
        WHERE n.ativo = 1
           OR n.usuario_id = ?
        ORDER BY n.data_noticia DESC
    ";

    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Erro ao preparar a consulta: " . $conn->error);
    }

    $stmt->bind_param("i", $usuarioId);
    $stmt->execute();

    $resultado = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">
    <title>Gerenciar Notícias</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>

        body{
            background:#f4f6f9;
        }

        .card{
            border:none;
            border-radius:15px;
            box-shadow:0 3px 12px rgba(0,0,0,.08);
        }

        .preview{
            width:90px;
            height:60px;
            object-fit:cover;
            border-radius:8px;
        }

    </style>

</head>

<body>

<div class="container mt-5">

    <?php if (isset($_GET['sucesso'])): ?>

        <div class="alert alert-success alert-dismissible fade show">

            <?php

            switch ($_GET['sucesso']) {

                case "cadastro":
                    echo "Notícia cadastrada com sucesso!";
                    break;

                case "edicao":
                    echo "Notícia atualizada com sucesso!";
                    break;

                case "desativada":
                    echo "Notícia desativada com sucesso!";
                    break;

                case "reativada":
                    echo "Notícia reativada com sucesso!";
                    break;

            }

            ?>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>

        </div>

    <?php endif; ?>

    <?php if (isset($_GET["erro"]) && $_GET["erro"] === "indisponivel"): ?>

        <div class="alert alert-warning alert-dismissible fade show" role="alert">
          Esta notícia não está disponível para visualização.
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
     </div>

        <script>
         setTimeout(function () {
              const alerta = document.querySelector(".alert");
              if (alerta) {
                  alerta.remove();
              }
          }, 3000);
     </script>

    <?php endif; ?>                
    <div class="card">

        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">

            <h3 class="mb-0">
                📰 Gerenciamento de Notícias
            </h3>

            <a
                href="cadastrar.php"
                class="btn btn-light"
            >
                ➕ Nova Notícia
            </a>

        </div>

        <div class="card-body">

            <table class="table table-hover table-striped align-middle">

                <thead class="table-dark">

                    <tr>

                        <th>ID</th>
                        <th>Imagem</th>
                        <th>Título</th>
                        <th>Autor</th>
                        <th>Data</th>
                        <th>Status</th>
                        <th width="220">
                            Ações
                        </th>

                    </tr>

                </thead>

                <tbody>

                <?php if ($resultado->num_rows > 0): ?>

                    <?php while ($noticia = $resultado->fetch_assoc()): ?>

                        <tr>

                            <td>
                                <?= $noticia["id"] ?>
                            </td>

                            <td>

                                <?php if (!empty($noticia["imagem"])): ?>

                                    <img
                                        src="<?= htmlspecialchars($noticia["imagem"]) ?>"
                                        class="preview"
                                    >

                                <?php else: ?>

                                    <span class="text-muted">
                                        Sem imagem
                                    </span>

                                <?php endif; ?>

                            </td>

                            <td>
                                <?= htmlspecialchars($noticia["titulo"]) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($noticia["autor"]) ?>
                            </td>

                            <td>
                                <?= date("d/m/Y", strtotime($noticia["data_noticia"])) ?>
                            </td>

                            <td>

                                <?php if ($noticia["ativo"]): ?>

                                    <span class="badge bg-success">
                                        Ativa
                                    </span>

                                <?php else: ?>

                                    <span class="badge bg-secondary">
                                        Desativada
                                    </span>

                                <?php endif; ?>

                            </td>

                            <td>
                                <a
                                        href="visualizar.php?id=<?= $noticia["id"] ?>"
                                         class="btn btn-info btn-sm"
                                    >
                                         Ver
                                    </a>

                                <?php if (
                                    $isAdmin ||
                                    $noticia["usuario_id"] == $_SESSION["id"]
                                ): ?>
                                    
                                    <a
                                        href="editar.php?id=<?= $noticia["id"] ?>"
                                        class="btn btn-warning btn-sm"
                                    >
                                        Editar
                                    </a>

                                    <?php if ($noticia["ativo"]): ?>

                                        <a href="desativar.php?id=<?= $noticia["id"] ?>"
                                            class="btn btn-danger btn-sm"
                                           onclick="return confirm('Deseja realmente desativar esta notícia?');">
                                            Desativar
                                         </a>

                                    <?php else: ?>

                                     <?php if (
                                      $isAdmin ||
                                         (
                                            $noticia["usuario_id"] == $_SESSION["id"] &&
                                            isset($noticia["desativado_por"]) &&
                                           $noticia["desativado_por"] === "autor"
                                         )
                                     ): ?>

                                     <a href="reativar.php?id=<?= $noticia["id"] ?>"
                                     class="btn btn-success btn-sm"
                                       onclick="return confirm('Deseja realmente reativar esta notícia?');">
                                     Reativar
                                     </a>

                                    <?php endif; ?>

                                <?php endif; ?>

                                <?php else: ?>

                                    <span class="badge bg-info text-dark">
                                        Somente leitura
                                    </span>

                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>

                        <td colspan="7" class="text-center text-muted">

                            Nenhuma notícia encontrada.

                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

    <div class="mt-3">

        <a
            href="../dashboard.php"
            class="btn btn-secondary"
        >
            ← Voltar ao Dashboard
        </a>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>