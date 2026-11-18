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

header("Content-Type: " . $mime);
header("Cache-Control: public, max-age=86400");
header("Content-Length: " . strlen($p["foto"]));

echo $p["foto"];
