<?php
// ============================================================
// index.php - Catálogo de productos (GuinhaStore)
// ============================================================
require "conexion.php";
require "seed.php";
require "util.php";

// Llena la base de datos la primera vez
asegurarDatos($con);

$resultado = mysqli_query($con, "SELECT id, nombre, descripcion, foto, precio, stock, fabricante, origen
                                 FROM productos ORDER BY id");

$productos = $resultado ? mysqli_fetch_all($resultado, MYSQLI_ASSOC) : array();

$titulo = "Catálogo";
$paginaActiva = "catalogo";
include "header.php";
?>

<!-- ============ HERO ============ -->
<section class="hero animar">
    <span class="etiqueta">PlayStation 5 · Tienda en línea</span>
    <h1>GUINHASTORE</h1>
    <p>Los mejores juegos de PS5 al mejor precio. Explora el catálogo, arma tu carrito y vive la experiencia.</p>
</section>

<!-- ============ CATÁLOGO ============ -->
<div class="seccion-titulo">
    <h2>Catálogo de juegos</h2>
    <span class="badge bg-primary ms-auto"><?php echo count($productos); ?> juegos</span>
</div>

<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">

    <?php $i = 0;
    foreach ($productos as $p) {
        $i++; ?>
        <div class="col animar" style="animation-delay: <?php echo ($i % 8) * 0.05; ?>s;">
            <div class="tarjeta-juego">

                <div class="contenedor-portada">
                    <?php if ($p["stock"] > 0) { ?>
                        <span class="insignia-flotante insignia-ok">Disponible</span>
                    <?php } else { ?>
                        <span class="insignia-flotante insignia-agotado">Agotado</span>
                    <?php } ?>

                    <a href="producto.php?id=<?php echo $p["id"]; ?>">
                        <img src="<?php echo imagenUrl($p["id"]); ?>"
                            alt="Portada de <?php echo htmlspecialchars($p["nombre"]); ?>">
                    </a>
                </div>

                <div class="cuerpo-juego">
                    <h3><?php echo htmlspecialchars($p["nombre"]); ?></h3>
                    <p class="desc"><?php echo htmlspecialchars($p["descripcion"]); ?></p>

                    <div class="fila-precio">
                        <span class="precio"><?php echo precio($p["precio"]); ?></span>
                        <span class="fabricante-chip"><?php echo htmlspecialchars($p["fabricante"]); ?></span>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="producto.php?id=<?php echo $p["id"]; ?>"
                            class="btn btn-outline-primary btn-sm flex-fill">Ver</a>

                        <form method="post" action="carrito.php" class="flex-fill">
                            <input type="hidden" name="accion" value="agregar">
                            <input type="hidden" name="id_producto" value="<?php echo $p["id"]; ?>">
                            <button type="submit" class="btn btn-primary btn-sm w-100"
                                <?php echo $p["stock"] <= 0 ? "disabled" : ""; ?>>
                                Agregar
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    <?php } ?>

</div>

<?php include "footer.php"; ?>
