<?php
// ============================================================
// contacto.php - Información de contacto y del sitio
// ============================================================
require "conexion.php";
require "util.php";

$titulo = "Contacto";
$paginaActiva = "contacto";
include "header.php";
?>

<h1 class="h2 mb-4">Contacto e información</h1>

<div class="row g-4">

    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-dark text-white">Datos de contacto</div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>Correo:</strong> contacto@guinahstore.com</li>
                <li class="list-group-item"><strong>Teléfono:</strong> (55) 1234 5678</li>
                <li class="list-group-item"><strong>Dirección:</strong> Av. Universidad 100, CDMX</li>
                <li class="list-group-item"><strong>Horario:</strong> Lunes a viernes, 9:00 a 18:00</li>
            </ul>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-dark text-white">Acerca del sitio</div>
            <div class="card-body">
                <p>
                    <strong>GuinhaStore</strong> es una tienda en línea de videojuegos de PlayStation 5.
                    Puedes explorar el catálogo, agregar juegos a tu carrito y completar tu compra.
                </p>
                <p class="mb-0">
                    Proyecto final de la materia <strong>Programación para Internet</strong>,
                    desarrollado con PHP, MySQL y Bootstrap 5.
                </p>
            </div>
        </div>
    </div>

</div>

<div class="card shadow-sm mt-4">
    <div class="card-header bg-dark text-white">Preguntas frecuentes</div>
    <div class="card-body">
        <div class="accordion" id="faq">

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#faq1">¿Necesito crear una cuenta para comprar?</button>
                </h2>
                <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faq">
                    <div class="accordion-body">
                        Sí. Para agregar juegos al carrito y finalizar la compra debes iniciar sesión con tu cuenta.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#faq2">¿Qué pasa con el inventario al comprar?</button>
                </h2>
                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faq">
                    <div class="accordion-body">
                        Al finalizar la compra, el sistema guarda el registro en tu historial y descuenta las
                        piezas del inventario automáticamente.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#faq3">¿Puedo modificar mi carrito?</button>
                </h2>
                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faq">
                    <div class="accordion-body">
                        Sí, en la página del carrito puedes cambiar la cantidad o eliminar cualquier juego
                        antes de pagar.
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include "footer.php"; ?>
