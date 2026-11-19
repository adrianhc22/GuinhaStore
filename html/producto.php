<?php
// ============================================================
// producto.php - Detalle de un juego (con trailer incrustado)
// ============================================================
require "conexion.php";
require "util.php";

$id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

$stmt = mysqli_prepare($con, "SELECT id, nombre, descripcion, foto, precio, stock, fabricante, origen, trailer, fecha_lanzamiento
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
    echo '<div class="alert alert-warning">El producto que buscas no existe. <a href="index.php" class="alert-link">Volver al catálogo</a>.</div>';
    include "footer.php";
    exit;
}
?>

<a href="index.php" class="btn btn-sm btn-outline-secondary mb-5">&larr; Volver al catálogo</a>

<div class="row g-4 align-items-start animar">

    <!-- Portada -->
    <div class="col-md-5 col-lg-4">
        <div class="contenedor-portada"
            style="border-radius:20px;border:1px solid rgba(120,170,255,.2);box-shadow:0 25px 60px rgba(0,0,0,.6),0 0 40px rgba(59,130,246,.25);">
            <img src="<?php echo imagenUrl($p["id"]); ?>"
                alt="Portada de <?php echo htmlspecialchars($p["nombre"]); ?>">
        </div>
    </div>

    <!-- Información -->
    <div class="col-md-7 col-lg-8">

        <span class="badge bg-secondary mb-3"><?php echo htmlspecialchars($p["origen"]); ?></span>
        <?php if (!empty($p["fecha_lanzamiento"])) { ?>
            <span class="badge bg-dark mb-3">Lanzamiento: <?php echo date("d/m/Y", strtotime($p["fecha_lanzamiento"])); ?></span>
        <?php } ?>

        <h1 class="display-6 mb-2" style="color:#fff;"><?php echo htmlspecialchars($p["nombre"]); ?></h1>

        <p style="color:#a9bcda;font-size:1.08rem;"><?php echo htmlspecialchars($p["descripcion"]); ?></p>

        <div class="precio mb-3" style="font-size:2.4rem;"><?php echo precio($p["precio"]); ?></div>

        <div class="row g-3 mb-4">
            <div class="col-sm-4">
                <div class="card h-100">
                    <div class="card-body py-3">
                        <div class="small text-uppercase" style="letter-spacing:1.5px;color:#7dd3fc;">Fabricante</div>
                        <div class="fw-bold"><?php echo htmlspecialchars($p["fabricante"]); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card h-100">
                    <div class="card-body py-3">
                        <div class="small text-uppercase" style="letter-spacing:1.5px;color:#7dd3fc;">Origen</div>
                        <div class="fw-bold"><?php echo htmlspecialchars($p["origen"]); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card h-100">
                    <div class="card-body py-3">
                        <div class="small text-uppercase" style="letter-spacing:1.5px;color:#7dd3fc;">Existencias</div>
                        <div class="fw-bold"><?php echo (int) $p["stock"]; ?> piezas</div>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($p["stock"] > 0) { ?>
            <form method="post" action="carrito.php" class="d-flex gap-2 align-items-center flex-wrap">
                <input type="hidden" name="accion" value="agregar">
                <input type="hidden" name="id_producto" value="<?php echo $p["id"]; ?>">

                <label for="cantidad" class="form-label mb-0">Cantidad:</label>
                <input type="number" id="cantidad" name="cantidad" class="form-control"
                    style="max-width: 110px;" value="1" min="1" max="<?php echo (int) $p["stock"]; ?>">

                <button type="submit" class="btn btn-primary btn-lg px-4">Agregar al carrito</button>
            </form>
        <?php } else { ?>
            <div class="alert alert-warning mb-0">Producto agotado por el momento.</div>
        <?php } ?>

    </div>

</div>

<!-- ============ TRAILER ============ -->
<?php if (!empty($p["trailer"])) { ?>
    <section class="mt-5 animar">
        <div class="seccion-titulo">
            <h2>Trailer</h2>
        </div>

        <div class="ratio ratio-16x9 trailer-caja">
            <iframe src="https://www.youtube.com/embed/<?php echo htmlspecialchars($p["trailer"]); ?>?rel=0"
                title="Trailer de <?php echo htmlspecialchars($p["nombre"]); ?>"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                allowfullscreen></iframe>
        </div>
    </section>
<?php } ?>

<?php include "footer.php"; ?>
