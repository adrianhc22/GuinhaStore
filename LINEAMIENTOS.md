# Lineamientos — Proyecto Final

**Materia:** Programación para Internet (SIS3410)
**Alumno:** Adrian Huelga Correa
**Universidad:** Universidad Anáhuac México Norte

---

## Descripción

El proyecto final consistirá en desarrollar una **tienda en línea**.

La página deberá tener las siguientes características:

- Deberá tener un **catálogo de productos** con una descripción y una **foto responsiva** por producto.
  **Al menos 30 productos** en stock. Los productos y toda la información acerca de ellos deberá
  estar almacenada en la **base de datos** y debe permitir **añadir productos nuevos**.
- Un **menú de navegación superior o lateral izquierdo**. El menú debe estar disponible en
  **todas las páginas** del sistema para que le permita al usuario navegar en las diferentes
  secciones del sitio.
- El sitio debe tener las siguientes secciones:
  - **Catálogo de productos.**
  - **Página de información del usuario.**
  - **Creación de una nueva cuenta de usuario:** se debe pedir toda la información del usuario.
  - **Carrito de compras del usuario:** deberá mostrar los artículos que el usuario seleccionó
    del catálogo. Deberá permitir modificar su selección y finalizar la compra.
  - **Página del administrador:** permite ver un reporte de los productos en inventario,
    agregar productos nuevos y modificar los existentes. Deberá mostrar el historial de compras.
  - **Información de contacto e información adicional** acerca del sitio.
- Un **sistema de inicio de sesión** validando correo electrónico y contraseña. Debe permanecer
  iniciada la sesión hasta que el usuario la finalice.
- Sólo se permite usar **un framework: Bootstrap**.
- La página debe ser **responsiva** en todos sus elementos.
- La página debe permitir al usuario elegir productos y agregarlos a su carrito de compras.
  Una vez que el usuario haya terminado de seleccionar sus artículos, el sistema debe completar
  la venta del producto actualizando las tablas correspondientes en la base de datos:
  la **cantidad de productos en almacén** y el **historial de compras**.

## Base de datos (mínimo)

**Productos**
- ID Producto
- Nombre
- Descripción
- Fotos
- Precio
- Cantidad en almacén
- Fabricante
- Origen

**Usuarios**
- ID usuario
- Nombre del usuario
- Correo electrónico
- Contraseña
- Fecha de Nacimiento
- Número de tarjeta bancaria
- Dirección Postal

**Historial de compras de los usuarios**
- ID compra
- Usuario
- Producto que ha comprado el usuario

**Carrito de compras**
- ID producto en carrito
- Usuario
- Producto que está por comprar el usuario

## Evaluación

La evaluación del proyecto final se determinará mediante una rúbrica que se encuentra adjunta a
la actividad en BrightSpace.

## Lista de cotejo

### Catálogo de Productos
- ¿Existen al menos 30 productos?
- ¿Los productos son mostrados con detalle e imagen?

### Menú de Navegación
- ¿Se encuentra en todas las páginas?
- ¿Permite navegar las diferentes secciones de la página?

### Sesión de usuario
- ¿Funciona?
- ¿Se mantiene abierta?
- ¿Se puede cerrar y abrir sin problemas?

### Carrito de Compras
- ¿Permite agregar productos?
- ¿Permite modificar productos en el carrito?
- ¿Permite eliminar productos en el carrito?

### Página de Administración
- ¿Existe?
- ¿Permite modificar productos existentes?
- ¿Permite agregar nuevos productos?
- ¿Muestra el historial?

### Sitio Responsivo
- ¿Todas las páginas y elementos son responsivos?

### Base de Datos
- ¿Existe?
- ¿Tiene las tablas y campos requeridos?
- ¿El sistema permite realizar consultas y modificaciones?

### Creación de Cuentas de Usuario
- ¿Se puede hacer una cuenta nueva desde la página?
- Una vez hecha, ¿se puede iniciar sesión?

### Finalizar Compra
- ¿Se guarda la compra en el historial?
- ¿Se actualiza el inventario de productos?
- ¿Se borra el carrito de compras?

## Reporte Escrito

Adicional al proyecto, deberán entregar un documento escrito que deberá contener lo siguiente:

- Portada
- Introducción
- Objetivos
- Diagrama de la Base de Datos
- Código SQL
- Descripción de las principales páginas del proyecto
- Enlace hacia su repositorio Git
- Conclusiones
