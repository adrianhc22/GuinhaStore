<?php
// ============================================================
// perfil.php - Información del usuario y su historial de compras
// ============================================================
require "conexion.php";
require "util.php";
require "auth.php";

requiereLogin();

$usuario = usuarioActual();

// Datos completos del usuario
$stmt = mysqli_prepare($con, "SELECT nombre, correo, fecha_nacimiento, num_tarjeta, direccion_postal, rol
                              FROM usuarios WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $usuario["id"]);
mysqli_stmt_execute($stmt);
$r = mysqli_stmt_get_result($stmt);
$datos = mysqli_fetch_assoc($r);
mysqli_stmt_close($stmt);

// Historial de compras
$stmt = mysqli_prepare($con, "SELECT h.id_orden, h.cantidad, h.precio_unitario, h.fecha, p.nombre
                              FROM historial h JOIN productos p ON h.id_producto = p.id
                              WHERE h.id_usuario = ?
                              ORDER BY h.id_orden DESC, h.id ASC");
mysqli_stmt_bind_param($stmt, "i", $usuario["id"]);
mysqli_stmt_execute($stmt);
$r = mysqli_stmt_get_result($stmt);
$compras = mysqli_fetch_all($r, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

// Agrupamos por orden
$ordenes = array();
foreach ($compras as $c) {
    if (!isset($ordenes[$c["id_orden"]])) {
        $ordenes[$c["id_orden"]] = array("fecha" => $c["fecha"], "items" => array(), "total" => 0);
    }
    $ordenes[$c["id_orden"]]["items"][] = $c;
    $ordenes[$c["id_orden"]]["total"] += $c["cantidad"] * $c["precio_unitario"];
}

$titulo = "Mi perfil";
$paginaActiva = "perfil";
include "header.php";
?>

<?php if (isset($_GET["compra"])) { ?>
    <div class="alert alert-success">
        ¡Compra realizada con éxito! Tu número de orden es <strong><?php echo (int) $_GET["compra"]; ?></strong>.
    </div>
<?php } ?>

<h1 class="h2 mb-4">Mi perfil</h1>

<div class="row g-4">

    <div class="col-lg-5">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">Datos personales</div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>Nombre:</strong> <?php echo htmlspecialchars($datos["nombre"]); ?></li>
                <li class="list-group-item"><strong>Correo:</strong> <?php echo htmlspecialchars($datos["correo"]); ?></li>
                <li class="list-group-item"><strong>Fecha de nacimiento:</strong>
                    <?php echo htmlspecialchars($datos["fecha_nacimiento"]); ?></li>
                <li class="list-group-item"><strong>Tarjeta:</strong>
                    **** <?php echo htmlspecialchars(substr($datos["num_tarjeta"], -4)); ?></li>
                <li class="list-group-item"><strong>Dirección postal:</strong>
                    <?php echo htmlspecialchars($datos["direccion_postal"]); ?></li>
                <li class="list-group-item"><strong>Tipo de cuenta:</strong>
                    <span class="badge bg-<?php echo $datos["rol"] === "admin" ? "danger" : "secondary"; ?>">
                        <?php echo htmlspecialchars($datos["rol"]); ?>
                    </span>
                </li>
            </ul>
        </div>

        <a href="carrito.php" class="btn btn-primary mt-3">Ver mi carrito</a>
        <a href="licencia.php" class="d-none"></a>
    </div>

    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">Historial de compras</div>
            <div class="card-body">

                <?php if (count($ordenes) === 0) { ?>
                    <p class="text-muted mb-0">Todavía no has realizado compras. <a href="index.php">Ir al catálogo</a>.</p>
                <?php } else { ?>

                    <?php foreach ($ordenes as $idOrden => $orden) { ?>
                        <div class="mb-4">
                            <h2 class="h6">
                                Orden #<?php echo $idOrden; ?>
                                <small class="text-muted">— <?php echo htmlspecialchars($orden["fecha"]); ?></small>
                            </h2>

                            <ul class="list-group mb-1">
                                <?php foreach ($orden["items"] as $it) { ?>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>
                                            <?php echo (int) $it["cantidad"]; ?> x
                                            <?php echo htmlspecialchars($it["nombre"]); ?>
                                        </span>
                                        <span><?php echo precio($it["cantidad"] * $it["precio_unitario"]); ?></span>
                                    </li>
                                <?php } ?>
                            </ul>

                            <p class="text-end fw-bold mb-0">Total: <?php echo precio($orden["total"]); ?></p>
                        </div>
                    <?php } ?>

                <?php } ?>

            </div>
        </div>
    </div>

</div>

<?php include "footer.php"; ?>
