<?php
// ============================================================
// imagen.php - Sirve la portada de un producto guardada en la BD
// ============================================================
require "conexion.php";

$id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

$stmt = mysqli_prepare($con, "SELECT foto FROM productos WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$r = mysqli_stmt_get_result($stmt);
$p = mysqli_fetch_assoc($r);
mysqli_stmt_close($stmt);

if (!$p || empty($p["foto"])) {
    http_response_code(404);
    exit;
}

$info = @getimagesizefromstring($p["foto"]);
$mime = $info ? $info["mime"] : "image/jpeg";

// ETag: cambia automáticamente cuando la foto cambia
$etag = '"' . md5($p["foto"]) . '"';

header("Content-Type: " . $mime);
header("ETag: " . $etag);
header("Cache-Control: no-cache, must-revalidate");
header("Content-Length: " . strlen($p["foto"]));

// Si el navegador ya tiene la versión actual, respondemos 304 (sin reenviar)
if (isset($_SERVER["HTTP_IF_NONE_MATCH"]) && trim($_SERVER["HTTP_IF_NONE_MATCH"]) === $etag) {
    http_response_code(304);
    exit;
}

echo $p["foto"];
