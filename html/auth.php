<?php
// ============================================================
// auth.php - Manejo de sesión e inicio de sesión
// ============================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Devuelve los datos del usuario logueado o null
function usuarioActual()
{
    return $_SESSION["usuario"] ?? null;
}

function estaLogueado()
{
    return isset($_SESSION["usuario"]);
}

function esAdmin()
{
    return estaLogueado() && $_SESSION["usuario"]["rol"] === "admin";
}

// Si no hay sesión, manda al login
function requiereLogin()
{
    if (!estaLogueado()) {
        header("Location: login.php");
        exit;
    }
}

// Si no es admin, manda al catálogo
function requiereAdmin()
{
    if (!esAdmin()) {
        header("Location: index.php");
        exit;
    }
}
