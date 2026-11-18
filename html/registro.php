<?php
// ============================================================
// registro.php - Creación de una cuenta de usuario
// ============================================================
require "conexion.php";
require "util.php";
require "auth.php";

if (estaLogueado()) {
    ir("perfil.php");
}

$error = "";
$valores = array("nombre" => "", "correo" => "", "fecha_nacimiento" => "", "num_tarjeta" => "", "direccion_postal" => "");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    foreach ($valores as $campo => $v) {
        $valores[$campo] = trim($_POST[$campo] ?? "");
    }
    $contrasena = $_POST["contrasena"] ?? "";

    // ---------- Validaciones ----------
    if (
        $valores["nombre"] === "" || $valores["correo"] === "" || $contrasena === "" ||
        $valores["fecha_nacimiento"] === "" || $valores["num_tarjeta"] === "" || $valores["direccion_postal"] === ""
    ) {
        $error = "Completa todos los campos.";
    } elseif (!filter_var($valores["correo"], FILTER_VALIDATE_EMAIL)) {
        $error = "El correo electrónico no es válido.";
    } elseif (strlen($contrasena) < 6) {
        $error = "La contraseña debe tener al menos 6 caracteres.";
    } else {

        // ¿El correo ya existe?
        $stmt = mysqli_prepare($con, "SELECT id FROM usuarios WHERE correo = ?");
        mysqli_stmt_bind_param($stmt, "s", $valores["correo"]);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $error = "Ya existe una cuenta con ese correo.";
            mysqli_stmt_close($stmt);
        } else {
            mysqli_stmt_close($stmt);

            $hash = password_hash($contrasena, PASSWORD_DEFAULT);

            $sql = "INSERT INTO usuarios (nombre, correo, contrasena, fecha_nacimiento, num_tarjeta, direccion_postal, rol)
                    VALUES (?, ?, ?, ?, ?, ?, 'cliente')";
            $stmt = mysqli_prepare($con, $sql);

            if ($stmt === false) {
                $error = "Error al crear la cuenta.";
            } else {
                mysqli_stmt_bind_param(
                    $stmt,
                    "ssssss",
                    $valores["nombre"],
                    $valores["correo"],
                    $hash,
                    $valores["fecha_nacimiento"],
                    $valores["num_tarjeta"],
                    $valores["direccion_postal"]
                );

                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_close($stmt);
                    ir("login.php?creada=1");
                } else {
                    $error = "No se pudo crear la cuenta.";
                    mysqli_stmt_close($stmt);
                }
            }
        }
    }
}

$titulo = "Crear cuenta";
$paginaActiva = "registro";
include "header.php";
?>

<div class="row justify-content-center">
    <div class="col-lg-7">

        <h1 class="h2 mb-4">Crear una cuenta</h1>

        <?php if ($error !== "") { ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php } ?>

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="post" action="registro.php">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nombre" class="form-label">Nombre completo</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required
                                value="<?php echo htmlspecialchars($valores["nombre"]); ?>">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="correo" class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control" id="correo" name="correo" required
                                value="<?php echo htmlspecialchars($valores["correo"]); ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="contrasena" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="contrasena" name="contrasena"
                                required minlength="6">
                            <div class="form-text">Mínimo 6 caracteres.</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="fecha_nacimiento" class="form-label">Fecha de nacimiento</label>
                            <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento"
                                required value="<?php echo htmlspecialchars($valores["fecha_nacimiento"]); ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="num_tarjeta" class="form-label">Número de tarjeta bancaria</label>
                            <input type="text" class="form-control" id="num_tarjeta" name="num_tarjeta" required
                                maxlength="16" inputmode="numeric"
                                value="<?php echo htmlspecialchars($valores["num_tarjeta"]); ?>">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="direccion_postal" class="form-label">Dirección postal</label>
                            <input type="text" class="form-control" id="direccion_postal" name="direccion_postal"
                                required value="<?php echo htmlspecialchars($valores["direccion_postal"]); ?>">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Crear cuenta</button>
                    <a href="login.php" class="btn btn-outline-secondary">Ya tengo cuenta</a>

                </form>
            </div>
        </div>

    </div>
</div>

<?php include "footer.php"; ?>
