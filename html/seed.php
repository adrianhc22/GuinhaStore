<?php
// ============================================================
// seed.php - Llena la base de datos la primera vez
//   · 32 juegos de PS5 con su portada (guardada como BLOB)
//   · Un usuario administrador y un cliente de prueba
// ============================================================

require_once __DIR__ . "/datos_juegos.php";

// Devuelve los bytes de la portada del juego ("" si no existe)
function leerPortada($numero)
{
    $base = "/var/www/covers/" . $numero;

    if (file_exists($base . ".jpg")) {
        return file_get_contents($base . ".jpg");
    }
    if (file_exists($base . ".png")) {
        return file_get_contents($base . ".png");
    }
    return null;
}

// Inserta los datos iniciales solo si la tabla de productos está vacía
function asegurarDatos($con)
{
    $res = mysqli_query($con, "SELECT COUNT(*) AS total FROM productos");
    $fila = mysqli_fetch_assoc($res);

    if ((int) $fila["total"] > 0) {
        return false;
    }

    // ---------------- Productos ----------------
    foreach (datosJuegos() as $juego) {

        $foto = leerPortada($juego["foto"]);
        if ($foto === null) {
            continue;
        }

        $sql = "INSERT INTO productos (nombre, descripcion, foto, precio, stock, fabricante, origen, trailer, fecha_lanzamiento)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($con, $sql);
        if ($stmt === false) {
            continue;
        }

        $nombre = $juego["nombre"];
        $descripcion = $juego["descripcion"];
        $nulo = null;
        $precio = $juego["precio"];
        $stock = $juego["stock"];
        $fabricante = $juego["fabricante"];
        $origen = $juego["origen"];
        $trailer = $juego["trailer"] ?? "";
        $fechaLanz = $juego["fecha"] ?? null;

        mysqli_stmt_bind_param($stmt, "ssbdissss", $nombre, $descripcion, $nulo, $precio, $stock, $fabricante, $origen, $trailer, $fechaLanz);
        mysqli_stmt_send_long_data($stmt, 2, $foto);   // 3er parámetro = foto
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    // ---------------- Usuarios demo ----------------
    $usuarios = array(
        array("Administrador", "admin@guinahstore.com", "admin123", "1990-01-01", "4111111111111111", "Av. Universidad 100, CDMX", "admin"),
        array("Cliente Demo", "cliente@guinahstore.com", "cliente123", "2000-05-15", "4000123456789010", "Calle Falsa 123, CDMX", "cliente")
    );

    foreach ($usuarios as $u) {

        $hash = password_hash($u[2], PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios (nombre, correo, contrasena, fecha_nacimiento, num_tarjeta, direccion_postal, rol)
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($con, $sql);
        if ($stmt === false) {
            continue;
        }

        $nombre = $u[0];
        $correo = $u[1];
        $fecha = $u[3];
        $tarjeta = $u[4];
        $direccion = $u[5];
        $rol = $u[6];

        mysqli_stmt_bind_param($stmt, "sssssss", $nombre, $correo, $hash, $fecha, $tarjeta, $direccion, $rol);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    return true;
}
