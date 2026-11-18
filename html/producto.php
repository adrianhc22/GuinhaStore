<?php
// ============================================================
// producto.php - Detalle de un juego
// ============================================================
require "conexion.php";
require "util.php";

$id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

$stmt = mysqli_prepare($con, "SELECT id, nombre, descripcion, foto, precio, stock, fabricante, origen
                              FROM productos WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$p = mysqli_fetch_assoc($resultado);
mysqli_stmt_close($stmt);

$titulo = $p ? $p["nombre"] : "Producto no encontrado";
$paginaActiva = "catalogo";
include "header.php";

if (!$p) {
    echo '<div class="alert alert-warning">El producto que buscas no existe. <a href="index.php">Volver al catálogo</a>.</div>';
    include "footer.php";
    exit;
}
?>

<a href="index.php" class="btn btn-sm btn-outline-secondary mb-3">&larr; Volver al catálogo</a>

<div class="row g-4">

    <div class="col-md-5 col-lg-4">
        <?php $src = imagenUrl($p["id"]); ?>
        <?php if ($src) { ?>
            <img src="<?php echo $src; ?>" class="img-fluid rounded shadow-sm"
                alt="Portada de <?php echo htmlspecialchars($p["nombre"]); ?>">
        <?php } ?>
    </div>

    <div class="col-md-7 col-lg-8">
        <h1 class="h2"><?php echo htmlspecialchars($p["nombre"]); ?></h1>

        <p class="text-muted mb-3"><?php echo htmlspecialchars($p["descripcion"]); ?></p>

        <ul class="list-group list-group-flush mb-3">
            <li class="list-group-item d-flex justify-content-between">
                <span class="text-muted">Fabricante</span>
                <span class="fw-semibold"><?php echo htmlspecialchars($p["fabricante"]); ?></span>
            </li>
            <li class="list-group-item d-flex justify-content-between">
                <span class="text-muted">Origen</span>
                <span class="fw-semibold"><?php echo htmlspecialchars($p["origen"]); ?></span>
            </li>
            <li class="list-group-item d-flex justify-content-between">
                <span class="text-muted">Existencias</span>
                <span class="fw-semibold"><?php echo (int) $p["stock"]; ?> piezas</span>
            </li>
        </ul>

        <p class="fs-3 fw-bold text-primary mb-3"><?php echo precio($p["precio"]); ?></p>

        <form method="post" action="carrito.php" class="d-flex gap-2 align-items-center">
            <input type="hidden" name="accion" value="agregar">
            <input type="hidden" name="id_producto" value="<?php echo $p["id"]; ?>">

            <label for="cantidad" class="form-label mb-0">Cantidad:</label>
            <input type="number" id="cantidad" name="cantidad" class="form-control" style="max-width: 100px;"
                value="1" min="1" max="<?php echo max(1, (int) $p["stock"]); ?>">

            <button type="submit" class="btn btn-primary"
                <?php echo $p["stock"] <= 0 ? "disabled" : ""; ?>>
                Agregar al carrito
            </button>
        </form>

        <?php if ($p["stock"] <= 0) { ?>
            <p class="text-danger mt-2 mb-0">Producto agotado.</p>
        <?php } ?>
    </div>

</div>

<?php include "footer.php"; ?>
