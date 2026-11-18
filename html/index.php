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

<!-- Encabezado -->
<div class="p-5 mb-4 bg-dark text-white rounded-3">
    <h1 class="display-6 fw-bold">GuinhaStore</h1>
    <p class="lead mb-0">La mejor tienda de videojuegos de PlayStation 5.</p>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="h4 mb-0">Catálogo de juegos</h2>
    <span class="badge bg-primary fs-6"><?php echo count($productos); ?> juegos</span>
</div>

<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">

    <?php foreach ($productos as $p) { ?>
        <div class="col">
            <div class="card h-100 shadow-sm">

                <?php $src = imagenUrl($p["id"]); ?>
                <?php if ($src) { ?>
                    <img src="<?php echo $src; ?>" class="card-img-top" alt="Portada de <?php echo htmlspecialchars($p["nombre"]); ?>"
                        style="height: 300px; object-fit: cover;">
                <?php } else { ?>
                    <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 300px;">
                        Sin portada
                    </div>
                <?php } ?>

                <div class="card-body d-flex flex-column">
                    <h3 class="h6 card-title mb-1"><?php echo htmlspecialchars($p["nombre"]); ?></h3>
                    <p class="card-text small text-muted flex-grow-1"><?php echo htmlspecialchars($p["descripcion"]); ?></p>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold text-primary"><?php echo precio($p["precio"]); ?></span>
                        <?php if ($p["stock"] > 0) { ?>
                            <span class="badge bg-success">Disponible</span>
                        <?php } else { ?>
                            <span class="badge bg-danger">Agotado</span>
                        <?php } ?>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="producto.php?id=<?php echo $p["id"]; ?>"
                            class="btn btn-outline-dark btn-sm flex-fill">Ver</a>

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
