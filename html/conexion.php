<?php
// ============================================================
// conexion.php - Conexión a la base de datos
// ============================================================

$con = mysqli_connect("db", "usuario", "123456", "guinahstore");

if (mysqli_connect_errno()) {
    die("Error al conectar con la base de datos: " . mysqli_connect_error());
}

mysqli_set_charset($con, "utf8mb4");
