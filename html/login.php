<?php
// ============================================================
// login.php - Inicio de sesión
// ============================================================
require "conexion.php";
require "util.php";
require "auth.php";

if (estaLogueado()) {
    ir("perfil.php");
}

$error = "";
$correo = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $correo = trim($_POST["correo"] ?? "");
    $contrasena = $_POST["contrasena"] ?? "";

    if ($correo === "" || $contrasena === "") {
        $error = "Escribe tu correo y tu contraseña.";
    } else {

        $stmt = mysqli_prepare($con, "SELECT id, nombre, correo, contrasena, rol FROM usuarios WHERE correo = ?");
        mysqli_stmt_bind_param($stmt, "s", $correo);
        mysqli_stmt_execute($stmt);
        $r = mysqli_stmt_get_result($stmt);
        $u = mysqli_fetch_assoc($r);
        mysqli_stmt_close($stmt);

        if ($u && password_verify($contrasena, $u["contrasena"])) {

            // Sesión iniciada hasta que el usuario cierre sesión
            $_SESSION["usuario"] = array(
                "id" => (int) $u["id"],
                "nombre" => $u["nombre"],
                "correo" => $u["correo"],
                "rol" => $u["rol"]
            );

            ir("index.php");

        } else {
            $error = "Correo o contraseña incorrectos.";
        }
    }
}

$titulo = "Iniciar sesión";
$paginaActiva = "login";
include "header.php";
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">

        <h1 class="h2 mb-4">Iniciar sesión</h1>

        <?php if (isset($_GET["creada"])) { ?>
            <div class="alert alert-success">Tu cuenta se creó correctamente. Ya puedes iniciar sesión.</div>
        <?php } ?>

        <?php if ($error !== "") { ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php } ?>

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="post" action="login.php">

                    <div class="mb-3">
                        <label for="correo" class="form-label">Correo electrónico</label>
                        <input type="email" class="form-control" id="correo" name="correo" required
                            value="<?php echo htmlspecialchars($correo); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="contrasena" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="contrasena" name="contrasena" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Entrar</button>
                    <a href="registro.php" class="btn btn-outline-secondary">Crear cuenta</a>

                </form>
            </div>
        </div>

    </div>
</div>

<?php include "footer.php"; ?>
