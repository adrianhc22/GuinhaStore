<?php
// ============================================================
// gracias.php - Pantalla de compra exitosa
// ============================================================
require "conexion.php";
require "util.php";
require "auth.php";

requiereLogin();

$usuario = usuarioActual();
$idOrden = (int) ($_GET["orden"] ?? 0);

// Detalle de la orden
$stmt = mysqli_prepare($con, "SELECT h.cantidad, h.precio_unitario, p.nombre
                              FROM historial h JOIN productos p ON h.id_producto = p.id
                              WHERE h.id_orden = ? AND h.id_usuario = ?
                              ORDER BY h.id ASC");
mysqli_stmt_bind_param($stmt, "ii", $idOrden, $usuario["id"]);
mysqli_stmt_execute($stmt);
$r = mysqli_stmt_get_result($stmt);
$items = mysqli_fetch_all($r, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

$total = 0;
foreach ($items as $it) {
    $total += $it["cantidad"] * $it["precio_unitario"];
}

$titulo = "Compra exitosa";
$paginaActiva = "catalogo";
include "header.php";
?>

<div class="gracias animar">

    <?php
    // Confeti animado (sin librerías, solo CSS)
    $colores = array("#22d3ee", "#3b82f6", "#8b5cf6", "#ec4899", "#facc15", "#22c55e");
    for ($i = 0; $i < 28; $i++) {
        $left = (int) round(fmod($i * 3.6 + 2, 100));
        $delay = ($i % 9) * 0.35;
        $dur = 2.6 + ($i % 5) * 0.6;
        $color = $colores[$i % count($colores)];
        echo '<span class="confeti" style="left:' . $left . '%;background:' . $color
            . ';animation-delay:' . $delay . 's;animation-duration:' . $dur . 's;"></span>';
    }
    ?>

    <div class="icono-ok">&#10003;</div>

    <h1 class="display-6 mb-2" style="color:#fff;">¡Gracias por tu compra!</h1>
    <p style="color:#a9bcda;">Tu pedido se registró correctamente.</p>

    <p class="mb-4">
        <span class="badge bg-dark fs-6">Orden #<?php echo $idOrden; ?></span>
    </p>

    <?php if (count($items) > 0) { ?>
        <div class="card mx-auto" style="max-width: 620px;">
            <div class="card-header">Resumen del pedido</div>
            <ul class="list-group list-group-flush">
                <?php foreach ($items as $it) { ?>
                    <li class="list-group-item d-flex justify-content-between">
                        <span><?php echo (int) $it["cantidad"]; ?> × <?php echo htmlspecialchars($it["nombre"]); ?></span>
                        <span><?php echo precio($it["cantidad"] * $it["precio_unitario"]); ?></span>
                    </li>
                <?php } ?>
                <li class="list-group-item d-flex justify-content-between fw-bold">
                    <span>Total pagado</span>
                    <span class="precio"><?php echo precio($total); ?></span>
                </li>
            </ul>
        </div>
    <?php } ?>

    <div class="d-flex gap-2 justify-content-center mt-4 flex-wrap">
        <a href="index.php" class="btn btn-primary">Seguir comprando</a>
        <a href="perfil.php" class="btn btn-outline-secondary">Ver mi historial</a>
    </div>

</div>

<?php include "footer.php"; ?>
