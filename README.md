# GuinhaStore — Proyecto Final

**Tienda en línea de videojuegos de PlayStation 5**
Materia: Programación para Internet (SIS3410) · Universidad Anáhuac México Norte
Alumno: Adrian Huelga Correa

**Repositorio:** (se agrega al subir a GitHub)

---

## Descripción

GuinhaStore es una tienda en línea completa hecha con **PHP, MySQL (MariaDB) y Bootstrap 5**,
que corre dentro de **Docker**. Permite explorar un catálogo de **32 juegos de PS5**, crear una
cuenta, iniciar sesión, armar un carrito de compras y finalizar la compra, además de una página
de administración con inventario, alta/edición de productos e historial de compras.

## Cómo ejecutarla

Con **Docker Desktop abierto**:

```bash
cd ~/Downloads/ProgParaInternet/Proyecto_Final
docker compose up -d --build
```

Luego abre en el navegador:

- **Tienda:** http://localhost:8080/index.php
- **phpMyAdmin:** http://localhost:8081/ (usuario `root`, contraseña `root_password`)

Para detenerla:

```bash
docker compose down
```

> La primera vez tarda unos minutos (compila la imagen de PHP y descarga MariaDB).
> La base de datos y el catálogo de 32 juegos **se crean automáticamente** al abrir la tienda.

## Usuarios de prueba

| Tipo | Correo | Contraseña |
|---|---|---|
| **Administrador** | admin@guinahstore.com | admin123 |
| **Cliente** | cliente@guinahstore.com | cliente123 |

También puedes crear tu propia cuenta desde **Crear cuenta**.

## Secciones del sitio

| Página | Descripción |
|---|---|
| `index.php` | Catálogo de los 32 juegos con portada, descripción, precio y stock |
| `producto.php` | Ficha del juego con detalle y botón para agregar al carrito |
| `carrito.php` | Carrito: modificar cantidad, eliminar y finalizar compra |
| `registro.php` | Creación de cuenta nueva (todos los datos del usuario) |
| `login.php` / `logout.php` | Inicio y cierre de sesión (sesión persistente) |
| `perfil.php` | Información del usuario y su historial de compras |
| `admin.php` | Inventario, alta/edición de productos e historial de compras |
| `contacto.php` | Información de contacto y del sitio |

La barra de navegación está presente en **todas** las páginas.

## Base de datos

Base de datos **guinahstore** con cuatro tablas:

```
productos    id, nombre, descripcion, foto (BLOB), precio, stock, fabricante, origen
usuarios     id, nombre, correo (único), contrasena (hash), fecha_nacimiento,
             num_tarjeta, direccion_postal, rol (cliente|admin)
carrito      id, id_usuario, id_producto, cantidad
historial    id, id_orden, id_usuario, id_producto, cantidad, precio_unitario, fecha
```

El diseño completo está en `diagrama_er.png` y el script de creación en `init.sql`.

## Flujo de compra

1. El usuario agrega juegos al carrito (se guardan en la tabla `carrito`).
2. Puede modificar cantidades o eliminar productos.
3. Al **finalizar la compra**:
   - se guarda cada artículo en el **historial** (`id_orden`, usuario, producto, cantidad, precio, fecha),
   - se **descuenta el stock** de cada producto,
   - se **vacía el carrito**.

## Tecnologías

- **PHP 8.2** con Apache (contenedor Docker) — mysqli y gd
- **MariaDB 11** (contenedor Docker)
- **Bootstrap 5** (CDN) para todos los elementos
- **Docker Compose** para levantar todo
- **Git / GitHub** para el control de versiones

## Estructura

```
Proyecto_Final/
├── Dockerfile
├── docker-compose.yml
├── init.sql              # crea las 4 tablas
├── diagrama_er.png       # diagrama E-R (phpMyAdmin)
├── reporte.pdf           # reporte escrito con pantallazos
├── volcado.sql           # volcado de la base de datos
├── covers/               # portadas originales de los juegos
└── html/
    ├── conexion.php      auth.php     util.php     seed.php    datos_juegos.php
    ├── header.php        footer.php   imagen.php
    ├── index.php         producto.php carrito.php
    ├── registro.php      login.php    logout.php   perfil.php
    ├── admin.php         contacto.php
```
