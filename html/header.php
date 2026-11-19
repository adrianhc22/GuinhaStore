<?php
// ============================================================
// header.php - Cabecera y barra de navegación (en todas las páginas)
// ============================================================
require_once __DIR__ . "/auth.php";
require_once __DIR__ . "/conexion.php";

$usuario = usuarioActual();
$paginaActiva = $paginaActiva ?? "";

// Cantidad de artículos en el carrito (contador del menú)
$articulosCarrito = 0;
if ($usuario) {
    $stmtCarrito = mysqli_prepare($con, "SELECT COALESCE(SUM(cantidad), 0) AS total FROM carrito WHERE id_usuario = ?");
    mysqli_stmt_bind_param($stmtCarrito, "i", $usuario["id"]);
    mysqli_stmt_execute($stmtCarrito);
    $resCarrito = mysqli_stmt_get_result($stmtCarrito);
    $filaCarrito = mysqli_fetch_assoc($resCarrito);
    $articulosCarrito = (int) $filaCarrito["total"];
    mysqli_stmt_close($stmtCarrito);
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo isset($titulo) ? htmlspecialchars($titulo) . " | " : ""; ?>GuinhaStore</title>

    <!-- Tipografías -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600;700;800;900&family=Rajdhani:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Tema personalizado de GuinhaStore -->
    <link rel="stylesheet" href="estilos.css">
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">GUINHASTORE</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#menuPrincipal" aria-controls="menuPrincipal"
                aria-expanded="false" aria-label="Mostrar menú">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="menuPrincipal">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $paginaActiva === 'catalogo' ? 'active' : ''; ?>"
                            href="index.php">Catálogo</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $paginaActiva === 'contacto' ? 'active' : ''; ?>"
                            href="contacto.php">Contacto</a>
                    </li>

                    <?php if ($usuario) { ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $paginaActiva === 'perfil' ? 'active' : ''; ?>"
                                href="perfil.php">Mi perfil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $paginaActiva === 'carrito' ? 'active' : ''; ?>"
                                href="carrito.php">Carrito<?php if ($articulosCarrito > 0) { ?><span
                                    class="badge-contador"><?php echo $articulosCarrito; ?></span><?php } ?></a>
                        </li>
                        <?php if ($usuario["rol"] === "admin") { ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $paginaActiva === 'admin' ? 'active' : ''; ?>"
                                    href="admin.php">Administración</a>
                            </li>
                        <?php } ?>
                    <?php } ?>
                </ul>

                <ul class="navbar-nav align-items-lg-center">
                    <?php if ($usuario) { ?>
                        <li class="nav-item">
                            <span class="navbar-text me-3"><?php echo htmlspecialchars($usuario["nombre"]); ?></span>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-outline-secondary btn-sm" href="logout.php">Salir</a>
                        </li>
                    <?php } else { ?>
                        <li class="nav-item me-2">
                            <a class="btn btn-outline-secondary btn-sm" href="login.php">Iniciar sesión</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary btn-sm" href="registro.php">Crear cuenta</a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-4">
