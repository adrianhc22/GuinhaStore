<?php
// ============================================================
// admin.php - Página del administrador
//   · Reporte de inventario
//   · Agregar y modificar productos
//   · Historial de compras
// ============================================================
require "conexion.php";
require "util.php";
require "auth.php";

requiereAdmin();

$seccion = $_GET["seccion"] ?? "inventario";
$aviso = "";
$error = "";

// ------------------------------------------------------------
// Procesa y redimensiona la foto subida
// ------------------------------------------------------------
function procesarFoto($tmp)
{
    ini_set("memory_limit", "512M");

    $info = getimagesize($tmp);
    if ($info === false) {
        return null;
    }

    $funciones = array(
        IMAGETYPE_JPEG => "imagecreatefromjpeg",
        IMAGETYPE_PNG => "imagecreatefrompng",
        IMAGETYPE_WEBP => "imagecreatefromwebp"
    );

    if (!isset($funciones[$info[2]]) || !function_exists($funciones[$info[2]])) {
        return null;
    }

    $img = $funciones[$info[2]]($tmp);
    if ($img === false) {
        return null;
    }

    $ancho = imagesx($img);
    $alto = imagesy($img);
    $max = 600;

    if ($ancho > $max || $alto > $max) {
        $ratio = $max / max($ancho, $alto);
        $nuevoAncho = (int) ($ancho * $ratio);
        $nuevoAlto = (int) ($alto * $ratio);
        $nueva = imagecreatetruecolor($nuevoAncho, $nuevoAlto);
        imagecopyresampled($nueva, $img, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);
        $img = $nueva;
    }

    ob_start();
    imagejpeg($img, null, 80);
    return ob_get_clean();
}

// ------------------------------------------------------------
// AGREGAR producto
// ------------------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["accion"] ?? "") === "agregar") {

    $nombre = trim($_POST["nombre"] ?? "");
    $descripcion = trim($_POST["descripcion"] ?? "");
    $fabricante = trim($_POST["fabricante"] ?? "");
    $origen = trim($_POST["origen"] ?? "");
    $precio = (float) ($_POST["precio"] ?? 0);
    $stock = (int) ($_POST["stock"] ?? 0);

    if ($nombre === "" || $descripcion === "" || $precio <= 0) {
        $error = "Completa el nombre, la descripción y un precio mayor a cero.";
    } elseif (!isset($_FILES["foto"]) || $_FILES["foto"]["error"] !== UPLOAD_ERR_OK) {
        $error = "Selecciona una imagen de portada.";
    } else {
        $foto = procesarFoto($_FILES["foto"]["tmp_name"]);

        if ($foto === null) {
            $error = "La imagen no es válida (usa JPG, PNG o WEBP).";
        } else {
            $sql = "INSERT INTO productos (nombre, descripcion, foto, precio, stock, fabricante, origen)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($con, $sql);

            $nulo = null;
            mysqli_stmt_bind_param($stmt, "ssbdiss", $nombre, $descripcion, $nulo, $precio, $stock, $fabricante, $origen);
            mysqli_stmt_send_long_data($stmt, 2, $foto);

            if (mysqli_stmt_execute($stmt)) {
                $aviso = "El producto se agregó correctamente.";
            } else {
                $error = "No se pudo agregar el producto.";
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// ------------------------------------------------------------
// MODIFICAR producto
// ------------------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["accion"] ?? "") === "editar") {

    $id = (int) ($_POST["id"] ?? 0);
    $nombre = trim($_POST["nombre"] ?? "");
    $descripcion = trim($_POST["descripcion"] ?? "");
    $fabricante = trim($_POST["fabricante"] ?? "");
    $origen = trim($_POST["origen"] ?? "");
    $precio = (float) ($_POST["precio"] ?? 0);
    $stock = (int) ($_POST["stock"] ?? 0);

    if ($id <= 0 || $nombre === "" || $precio < 0) {
        $error = "Datos incompletos para modificar el producto.";
    } else {

        $hayFoto = isset($_FILES["foto"]) && $_FILES["foto"]["error"] === UPLOAD_ERR_OK;

        if ($hayFoto) {
            $foto = procesarFoto($_FILES["foto"]["tmp_name"]);

            if ($foto === null) {
                $error = "La imagen nueva no es válida.";
            } else {
                $sql = "UPDATE productos SET nombre=?, descripcion=?, foto=?, precio=?, stock=?, fabricante=?, origen=? WHERE id=?";
                $stmt = mysqli_prepare($con, $sql);
                $nulo = null;
                mysqli_stmt_bind_param($stmt, "ssbdissi", $nombre, $descripcion, $nulo, $precio, $stock, $fabricante, $origen, $id);
                mysqli_stmt_send_long_data($stmt, 2, $foto);
                $ok = mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        } else {
            $sql = "UPDATE productos SET nombre=?, descripcion=?, precio=?, stock=?, fabricante=?, origen=? WHERE id=?";
            $stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($stmt, "ssdissi", $nombre, $descripcion, $precio, $stock, $fabricante, $origen, $id);
            $ok = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        if ($error === "") {
            if ($ok) {
                $aviso = "Producto actualizado correctamente.";
            } else {
                $error = "No se pudo actualizar el producto.";
            }
        }
    }
}

$titulo = "Administración";
$paginaActiva = "admin";
include "header.php";
?>

<h1 class="h2 mb-3">Panel de administración</h1>

<?php if ($aviso !== "") { ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($aviso); ?></div>
<?php } ?>
<?php if ($error !== "") { ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
<?php } ?>

<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link <?php echo $seccion === 'inventario' ? 'active' : ''; ?>"
            href="admin.php?seccion=inventario">Inventario</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo $seccion === 'agregar' ? 'active' : ''; ?>"
            href="admin.php?seccion=agregar">Agregar producto</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo $seccion === 'historial' ? 'active' : ''; ?>"
            href="admin.php?seccion=historial">Historial de compras</a>
    </li>
</ul>

<?php if ($seccion === "inventario") { ?>

    <?php
    $res = mysqli_query($con, "SELECT id, nombre, precio, stock, fabricante, origen FROM productos ORDER BY id");
    $productos = $res ? mysqli_fetch_all($res, MYSQLI_ASSOC) : array();

    $totalStock = 0;
    $valorTotal = 0;
    foreach ($productos as $p) {
        $totalStock += (int) $p["stock"];
        $valorTotal += (int) $p["stock"] * (float) $p["precio"];
    }
    ?>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card stat-azul text-white">
                <div class="card-body">
                    <div class="small">Productos registrados</div>
                    <div class="fs-3 fw-bold"><?php echo count($productos); ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card stat-verde text-white">
                <div class="card-body">
                    <div class="small">Piezas en inventario</div>
                    <div class="fs-3 fw-bold"><?php echo $totalStock; ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card stat-oscuro text-white">
                <div class="card-body">
                    <div class="small">Valor del inventario</div>
                    <div class="fs-3 fw-bold"><?php echo precio($valorTotal); ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Juego</th>
                    <th>Fabricante</th>
                    <th>Origen</th>
                    <th class="text-end">Precio</th>
                    <th class="text-center">Stock</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $p) { ?>
                    <tr>
                        <td><?php echo $p["id"]; ?></td>
                        <td><?php echo htmlspecialchars($p["nombre"]); ?></td>
                        <td><?php echo htmlspecialchars($p["fabricante"]); ?></td>
                        <td><?php echo htmlspecialchars($p["origen"]); ?></td>
                        <td class="text-end"><?php echo precio($p["precio"]); ?></td>
                        <td class="text-center">
                            <?php if ((int) $p["stock"] > 0) { ?>
                                <span class="badge bg-success"><?php echo (int) $p["stock"]; ?></span>
                            <?php } else { ?>
                                <span class="badge bg-danger">0</span>
                            <?php } ?>
                        </td>
                        <td>
                            <a href="admin.php?seccion=editar&id=<?php echo $p["id"]; ?>"
                                class="btn btn-sm btn-outline-primary">Modificar</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

<?php } elseif ($seccion === "agregar") { ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="post" action="admin.php?seccion=agregar" enctype="multipart/form-data">
                <input type="hidden" name="accion" value="agregar">

                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label for="nombre" class="form-label">Nombre del juego</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="precio" class="form-label">Precio</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="precio" name="precio" required>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="stock" class="form-label">Stock</label>
                        <input type="number" min="0" class="form-control" id="stock" name="stock" value="0" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="fabricante" class="form-label">Fabricante</label>
                        <input type="text" class="form-control" id="fabricante" name="fabricante">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="origen" class="form-label">Origen</label>
                        <input type="text" class="form-control" id="origen" name="origen">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="foto" class="form-label">Portada</label>
                        <input type="file" class="form-control" id="foto" name="foto"
                            accept="image/jpeg,image/png,image/webp" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Guardar producto</button>
            </form>
        </div>
    </div>

<?php } elseif ($seccion === "editar") { ?>

    <?php
    $id = (int) ($_GET["id"] ?? 0);
    $stmt = mysqli_prepare($con, "SELECT id, nombre, descripcion, precio, stock, fabricante, origen FROM productos WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $r = mysqli_stmt_get_result($stmt);
    $p = mysqli_fetch_assoc($r);
    mysqli_stmt_close($stmt);
    ?>

    <?php if (!$p) { ?>
        <div class="alert alert-warning">El producto no existe.</div>
    <?php } else { ?>

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="post" action="admin.php?seccion=editar" enctype="multipart/form-data">
                    <input type="hidden" name="accion" value="editar">
                    <input type="hidden" name="id" value="<?php echo $p["id"]; ?>">

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="nombre" class="form-label">Nombre del juego</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required
                                value="<?php echo htmlspecialchars($p["nombre"]); ?>">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="precio" class="form-label">Precio</label>
                            <input type="number" step="0.01" min="0" class="form-control" id="precio" name="precio"
                                required value="<?php echo $p["precio"]; ?>">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="stock" class="form-label">Stock</label>
                            <input type="number" min="0" class="form-control" id="stock" name="stock"
                                value="<?php echo $p["stock"]; ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion"
                            rows="3"><?php echo htmlspecialchars($p["descripcion"]); ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="fabricante" class="form-label">Fabricante</label>
                            <input type="text" class="form-control" id="fabricante" name="fabricante"
                                value="<?php echo htmlspecialchars($p["fabricante"]); ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="origen" class="form-label">Origen</label>
                            <input type="text" class="form-control" id="origen" name="origen"
                                value="<?php echo htmlspecialchars($p["origen"]); ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="foto" class="form-label">Nueva portada (opcional)</label>
                            <input type="file" class="form-control" id="foto" name="foto"
                                accept="image/jpeg,image/png,image/webp">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    <a href="admin.php?seccion=inventario" class="btn btn-outline-secondary">Volver al inventario</a>
                </form>
            </div>
        </div>

    <?php } ?>

<?php } elseif ($seccion === "historial") { ?>

    <?php
    $res = mysqli_query($con, "SELECT h.id_orden, h.cantidad, h.precio_unitario, h.fecha,
                                      u.nombre AS usuario, u.correo, p.nombre AS producto
                               FROM historial h
                               JOIN usuarios u ON h.id_usuario = u.id
                               JOIN productos p ON h.id_producto = p.id
                               ORDER BY h.id_orden DESC, h.id ASC");
    $compras = $res ? mysqli_fetch_all($res, MYSQLI_ASSOC) : array();
    ?>

    <?php if (count($compras) === 0) { ?>
        <div class="alert alert-info">Todavía no hay compras registradas.</div>
    <?php } else { ?>

        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Orden</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Correo</th>
                        <th>Juego</th>
                        <th class="text-center">Cant.</th>
                        <th class="text-end">Precio unit.</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($compras as $c) { ?>
                        <tr>
                            <td><?php echo $c["id_orden"]; ?></td>
                            <td><?php echo htmlspecialchars($c["fecha"]); ?></td>
                            <td><?php echo htmlspecialchars($c["usuario"]); ?></td>
                            <td><?php echo htmlspecialchars($c["correo"]); ?></td>
                            <td><?php echo htmlspecialchars($c["producto"]); ?></td>
                            <td class="text-center"><?php echo (int) $c["cantidad"]; ?></td>
                            <td class="text-end"><?php echo precio($c["precio_unitario"]); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

    <?php } ?>

<?php } ?>

<?php include "footer.php"; ?>
