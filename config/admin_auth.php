<?php

require_once "auth.php";

if ($_SESSION["tipo"] != "admin") {

    echo "
    <h2>Acesso negado!</h2>
    <p>Você não possui permissão para acessar esta área.</p>
    ";

    exit();
}
?>