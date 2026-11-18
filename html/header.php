<?php
// ============================================================
// header.php - Cabecera y barra de navegación (en todas las páginas)
// ============================================================
require_once __DIR__ . "/auth.php";

$usuario = usuarioActual();
$paginaActiva = $paginaActiva ?? "";
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo isset($titulo) ? htmlspecialchars($titulo) . " | " : ""; ?>GuinhaStore</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">GuinhaStore</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#menuPrincipal" aria-controls="menuPrincipal"
                aria-expanded="false" aria-label="Mostrar menú">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="menuPrincipal">
                <ul class="navbar-nav me-auto">
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
                                href="carrito.php">Carrito</a>
                        </li>
                        <?php if ($usuario["rol"] === "admin") { ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $paginaActiva === 'admin' ? 'active' : ''; ?>"
                                    href="admin.php">Administración</a>
                            </li>
                        <?php } ?>
                    <?php } ?>
                </ul>

                <ul class="navbar-nav">
                    <?php if ($usuario) { ?>
                        <li class="nav-item">
                            <span class="navbar-text me-2">Hola, <?php echo htmlspecialchars($usuario["nombre"]); ?></span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Salir</a>
                        </li>
                    <?php } else { ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $paginaActiva === 'login' ? 'active' : ''; ?>"
                                href="login.php">Iniciar sesión</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $paginaActiva === 'registro' ? 'active' : ''; ?>"
                                href="registro.php">Crear cuenta</a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-4">
