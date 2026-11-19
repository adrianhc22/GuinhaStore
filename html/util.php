<?php
// ============================================================
// util.php - Funciones de ayuda
// ============================================================

// Convierte la foto (BLOB) en una URL de datos para el <img>
function fotoSrc($blob)
{
    if ($blob === null || $blob === "" || strlen($blob) === 0) {
        return null;
    }

    $info = @getimagesizefromstring($blob);
    $mime = $info ? $info["mime"] : "image/jpeg";

    return "data:" . $mime . ";base64," . base64_encode($blob);
}

// Formatea un precio en pesos
function precio($valor)
{
    return "$" . number_format((float) $valor, 2);
}

// Versión de los recursos: súbela cuando cambien las portadas
define("ASSET_VERSION", "4");

// URL para mostrar la portada desde la base de datos
function imagenUrl($id)
{
    return "imagen.php?id=" . (int) $id . "&v=" . ASSET_VERSION;
}

// Redirige y termina
function ir($url)
{
    header("Location: " . $url);
    exit;
}
