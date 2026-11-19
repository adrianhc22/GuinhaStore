<?php
// ============================================================
// index.php - Catálogo de productos con búsqueda y filtros
// ============================================================
require "conexion.php";
require "seed.php";
require "util.php";

// Llena la base de datos la primera vez
asegurarDatos($con);

// ---------------- Filtros ----------------
$q = trim($_GET["q"] ?? "");
$fab = trim($_GET["fab"] ?? "");
$orden = $_GET["orden"] ?? "recientes";

$sql = "SELECT id, nombre, descripcion, foto, precio, stock, fabricante, origen, fecha_lanzamiento FROM productos WHERE 1=1";
$tipos = "";
$params = array();

if ($q !== "") {
    $sql .= " AND (nombre LIKE ? OR descripcion LIKE ? OR fabricante LIKE ? OR origen LIKE ?)";
    $like = "%" . $q . "%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $tipos .= "ssss";
}

if ($fab !== "") {
    $sql .= " AND fabricante = ?";
    $params[] = $fab;
    $tipos .= "s";
}

switch ($orden) {
    case "precio_asc":
        $sql .= " ORDER BY precio ASC";
        break;
    case "precio_desc":
        $sql .= " ORDER BY precio DESC";
        break;
    case "nombre":
        $sql .= " ORDER BY nombre ASC";
        break;
    default:
        $sql .= " ORDER BY fecha_lanzamiento DESC";
}

$stmt = mysqli_prepare($con, $sql);
if ($tipos !== "") {
    mysqli_stmt_bind_param($stmt, $tipos, ...$params);
}
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$productos = $resultado ? mysqli_fetch_all($resultado, MYSQLI_ASSOC) : array();
mysqli_stmt_close($stmt);

// Lista de fabricantes para el filtro
$fabricantes = array();
$rf = mysqli_query($con, "SELECT DISTINCT fabricante FROM productos WHERE fabricante <> '' ORDER BY fabricante");
if ($rf) {
    while ($f = mysqli_fetch_assoc($rf)) {
        $fabricantes[] = $f["fabricante"];
    }
}

$hayFiltros = ($q !== "" || $fab !== "" || $orden !== "recientes");

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

<!-- ============ BUSCADOR Y FILTROS ============ -->
<form class="barra-filtros" method="get" action="index.php">
    <div class="row g-2 align-items-end">

        <div class="col-12 col-lg-4">
            <label for="q" class="form-label">Buscar</label>
            <input type="text" class="form-control" id="q" name="q"
                placeholder="Juego, fabricante u origen…" value="<?php echo htmlspecialchars($q); ?>">
        </div>

        <div class="col-6 col-lg-3">
            <label for="fab" class="form-label">Fabricante</label>
            <select class="form-select" id="fab" name="fab">
                <option value="">Todos</option>
                <?php foreach ($fabricantes as $f) { ?>
                    <option value="<?php echo htmlspecialchars($f); ?>"
                        <?php echo $fab === $f ? "selected" : ""; ?>>
                        <?php echo htmlspecialchars($f); ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="col-6 col-lg-3">
            <label for="orden" class="form-label">Ordenar por</label>
            <select class="form-select" id="orden" name="orden">
                <option value="recientes" <?php echo $orden === "recientes" ? "selected" : ""; ?>>Más recientes</option>
                <option value="nombre" <?php echo $orden === "nombre" ? "selected" : ""; ?>>Nombre (A-Z)</option>
                <option value="precio_asc" <?php echo $orden === "precio_asc" ? "selected" : ""; ?>>Precio: menor a mayor</option>
                <option value="precio_desc" <?php echo $orden === "precio_desc" ? "selected" : ""; ?>>Precio: mayor a menor</option>
            </select>
        </div>

        <div class="col-12 col-lg-2">
            <button type="submit" class="btn btn-primary w-100">Buscar</button>
        </div>

    </div>

    <?php if ($hayFiltros) { ?>
        <div class="mt-3">
            <a href="index.php" class="btn btn-sm btn-outline-secondary">Limpiar filtros</a>
        </div>
    <?php } ?>
</form>

<!-- ============ CATÁLOGO ============ -->
<div class="seccion-titulo">
    <h2><?php echo $hayFiltros ? "Resultados" : "Catálogo de juegos"; ?></h2>
    <span class="badge bg-primary ms-auto"><?php echo count($productos); ?> juegos</span>
</div>

<?php if (count($productos) === 0) { ?>
    <div class="alert alert-info">
        No se encontraron juegos con esos criterios. <a href="index.php" class="alert-link">Ver todo el catálogo</a>.
    </div>
<?php } ?>

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
                    <?php if (!empty($p["fecha_lanzamiento"])) { ?>
                        <p class="fecha-juego">&#128197; <?php echo date("d/m/Y", strtotime($p["fecha_lanzamiento"])); ?></p>
                    <?php } ?>
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
