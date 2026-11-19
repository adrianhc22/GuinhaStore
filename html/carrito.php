<?php
// ============================================================
// carrito.php - Carrito de compras y finalización de la compra
// ============================================================
require "conexion.php";
require "util.php";
require "auth.php";

requiereLogin();

$usuario = usuarioActual();
$idUsuario = (int) $usuario["id"];
$aviso = "";

$accion = $_POST["accion"] ?? "";

// ------------------------------------------------------------
// AGREGAR un producto
// ------------------------------------------------------------
if ($accion === "agregar") {

    $idProducto = (int) ($_POST["id_producto"] ?? 0);
    $cantidad = max(1, (int) ($_POST["cantidad"] ?? 1));

    // Revisamos que exista y tenga stock
    $stmt = mysqli_prepare($con, "SELECT stock FROM productos WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $idProducto);
    mysqli_stmt_execute($stmt);
    $r = mysqli_stmt_get_result($stmt);
    $prod = mysqli_fetch_assoc($r);
    mysqli_stmt_close($stmt);

    if ($prod && (int) $prod["stock"] > 0) {

        // ¿Ya está en el carrito?
        $stmt = mysqli_prepare($con, "SELECT id, cantidad FROM carrito WHERE id_usuario = ? AND id_producto = ?");
        mysqli_stmt_bind_param($stmt, "ii", $idUsuario, $idProducto);
        mysqli_stmt_execute($stmt);
        $r = mysqli_stmt_get_result($stmt);
        $item = mysqli_fetch_assoc($r);
        mysqli_stmt_close($stmt);

        if ($item) {
            $nueva = $item["cantidad"] + $cantidad;
            if ($nueva > (int) $prod["stock"]) {
                $nueva = (int) $prod["stock"];
            }
            $stmt = mysqli_prepare($con, "UPDATE carrito SET cantidad = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "ii", $nueva, $item["id"]);
        } else {
            $stmt = mysqli_prepare($con, "INSERT INTO carrito (id_usuario, id_producto, cantidad) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "iii", $idUsuario, $idProducto, $cantidad);
        }
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    ir("carrito.php");
}

// ------------------------------------------------------------
// ACTUALIZAR cantidad
// ------------------------------------------------------------
if ($accion === "actualizar") {

    $idCarrito = (int) ($_POST["id_carrito"] ?? 0);
    $cantidad = (int) ($_POST["cantidad"] ?? 1);

    if ($cantidad < 1) {
        $cantidad = 1;
    }

    // No permitir más de lo que hay en stock
    $stmt = mysqli_prepare($con, "SELECT c.id, p.stock
                                  FROM carrito c JOIN productos p ON c.id_producto = p.id
                                  WHERE c.id = ? AND c.id_usuario = ?");
    mysqli_stmt_bind_param($stmt, "ii", $idCarrito, $idUsuario);
    mysqli_stmt_execute($stmt);
    $r = mysqli_stmt_get_result($stmt);
    $item = mysqli_fetch_assoc($r);
    mysqli_stmt_close($stmt);

    if ($item) {
        if ($cantidad > (int) $item["stock"]) {
            $cantidad = (int) $item["stock"];
        }
        $stmt = mysqli_prepare($con, "UPDATE carrito SET cantidad = ? WHERE id = ? AND id_usuario = ?");
        mysqli_stmt_bind_param($stmt, "iii", $cantidad, $idCarrito, $idUsuario);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    ir("carrito.php");
}

// ------------------------------------------------------------
// ELIMINAR un producto del carrito
// ------------------------------------------------------------
if ($accion === "eliminar") {

    $idCarrito = (int) ($_POST["id_carrito"] ?? 0);

    $stmt = mysqli_prepare($con, "DELETE FROM carrito WHERE id = ? AND id_usuario = ?");
    mysqli_stmt_bind_param($stmt, "ii", $idCarrito, $idUsuario);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    ir("carrito.php");
}

// ------------------------------------------------------------
// FINALIZAR LA COMPRA
// ------------------------------------------------------------
if ($accion === "finalizar") {

    // Traemos el carrito con los datos del producto
    $stmt = mysqli_prepare($con, "SELECT c.id, c.id_producto, c.cantidad, p.precio, p.stock, p.nombre
                                  FROM carrito c JOIN productos p ON c.id_producto = p.id
                                  WHERE c.id_usuario = ?");
    mysqli_stmt_bind_param($stmt, "i", $idUsuario);
    mysqli_stmt_execute($stmt);
    $r = mysqli_stmt_get_result($stmt);
    $items = mysqli_fetch_all($r, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);

    if (count($items) === 0) {
        $aviso = "Tu carrito está vacío.";
    } else {

        $idOrden = time();   // número de orden
        $errores = array();

        mysqli_begin_transaction($con);

        foreach ($items as $item) {

            if ((int) $item["cantidad"] > (int) $item["stock"]) {
                $errores[] = "No hay suficientes existencias de \"" . $item["nombre"] . "\".";
                continue;
            }

            // 1) Guardar en el historial
            $stmt = mysqli_prepare($con, "INSERT INTO historial (id_orden, id_usuario, id_producto, cantidad, precio_unitario)
                                          VALUES (?, ?, ?, ?, ?)");
            if ($stmt) {
                $cantidad = (int) $item["cantidad"];
                $precioUnit = (float) $item["precio"];
                $idProducto = (int) $item["id_producto"];
                mysqli_stmt_bind_param($stmt, "iiiid", $idOrden, $idUsuario, $idProducto, $cantidad, $precioUnit);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }

            // 2) Descontar del inventario
            $stmt = mysqli_prepare($con, "UPDATE productos SET stock = stock - ? WHERE id = ? AND stock >= ?");
            if ($stmt) {
                $cantidad = (int) $item["cantidad"];
                $idProducto = (int) $item["id_producto"];
                mysqli_stmt_bind_param($stmt, "iii", $cantidad, $idProducto, $cantidad);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }

        // 3) Vaciar el carrito
        $stmt = mysqli_prepare($con, "DELETE FROM carrito WHERE id_usuario = ?");
        mysqli_stmt_bind_param($stmt, "i", $idUsuario);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        mysqli_commit($con);

        ir("gracias.php?orden=" . $idOrden);
    }
}

// ------------------------------------------------------------
// Datos del carrito para mostrar
// ------------------------------------------------------------
$stmt = mysqli_prepare($con, "SELECT c.id, c.cantidad, p.id AS id_producto, p.nombre, p.foto,
                                     p.precio, p.stock, p.fabricante
                              FROM carrito c JOIN productos p ON c.id_producto = p.id
                              WHERE c.id_usuario = ? ORDER BY c.id");

mysqli_stmt_bind_param($stmt, "i", $idUsuario);
mysqli_stmt_execute($stmt);
$r = mysqli_stmt_get_result($stmt);
$items = mysqli_fetch_all($r, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

$total = 0;
foreach ($items as $it) {
    $total += $it["cantidad"] * $it["precio"];
}

$titulo = "Carrito";
$paginaActiva = "carrito";
include "header.php";
?>

<h1 class="h2 mb-4">Mi carrito</h1>

<?php if ($aviso !== "") { ?>
    <div class="alert alert-info"><?php echo htmlspecialchars($aviso); ?></div>
<?php } ?>

<?php if (count($items) === 0) { ?>

    <div class="alert alert-warning">
        Tu carrito está vacío. <a href="index.php" class="alert-link">Ir al catálogo</a>.
    </div>

<?php } else { ?>

    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Juego</th>
                    <th>Precio</th>
                    <th style="width: 160px;">Cantidad</th>
                    <th>Subtotal</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $it) { ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <?php $src = imagenUrl($it["id_producto"]); ?>
                                <?php if ($src) { ?>
                                    <img src="<?php echo $src; ?>" alt="" width="45" height="60"
                                        style="object-fit: cover;" class="rounded">
                                <?php } ?>
                                <span><?php echo htmlspecialchars($it["nombre"]); ?></span>
                            </div>
                        </td>
                        <td><?php echo precio($it["precio"]); ?></td>
                        <td>
                            <form method="post" action="carrito.php" class="d-flex gap-1">
                                <input type="hidden" name="accion" value="actualizar">
                                <input type="hidden" name="id_carrito" value="<?php echo $it["id"]; ?>">
                                <input type="number" name="cantidad" class="form-control form-control-sm"
                                    value="<?php echo $it["cantidad"]; ?>" min="1"
                                    max="<?php echo (int) $it["stock"]; ?>">
                                <button type="submit" class="btn btn-sm btn-outline-dark">OK</button>
                            </form>
                        </td>
                        <td class="fw-semibold"><?php echo precio($it["cantidad"] * $it["precio"]); ?></td>
                        <td>
                            <form method="post" action="carrito.php">
                                <input type="hidden" name="accion" value="eliminar">
                                <input type="hidden" name="id_carrito" value="<?php echo $it["id"]; ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-end">Total</th>
                    <th colspan="2" class="fs-5 text-primary"><?php echo precio($total); ?></th>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="d-flex justify-content-between">
        <a href="index.php" class="btn btn-outline-secondary">Seguir comprando</a>

        <form method="post" action="carrito.php">
            <input type="hidden" name="accion" value="finalizar">
            <button type="submit" class="btn btn-success btn-lg">Finalizar compra</button>
        </form>
    </div>

<?php } ?>

<?php include "footer.php"; ?>
