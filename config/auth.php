<?php

session_start();

if (!isset($_SESSION["id"])) {
    header("Location: /SistemaWeb/index.php");
    exit();
}
?>