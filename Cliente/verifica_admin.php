<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$nivel_sessao = strtolower(trim((string) ($_SESSION["nivel_acesso"] ?? "")));
$tipo_sessao  = strtolower(trim((string) ($_SESSION["tipo_usuario"] ?? "")));

$e_admin_valido = in_array($nivel_sessao, ["admin", "administrador", "2"], true)
    && $tipo_sessao === "administrador";

if (!$e_admin_valido) {
    $_SESSION = [];
    header("Location: login.php");
    exit;
}