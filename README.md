# Sistema de Gestión de Biblioteca - FESC

Sistema web desarrollado en **PHP** y **SQL Server** para la administración integral de una biblioteca. Permite gestionar el catálogo de libros, autores, editoriales, categorías, préstamos, devoluciones y usuarios del sistema.

## Características Principales

*   **Autenticación y Roles:** Inicio de sesión seguro con contraseñas hasheadas y control de acceso basado en roles (Administrador, Bibliotecario, etc.).
*   **Catálogo de Libros:** Registro, edición y baja lógica de libros. Gestión de disponibilidad y ejemplares.
*   **Gestión de Autores y Categorías:** Módulos independientes para dar de alta autores, nacionalidades, categorías y descripciones.
*   **Préstamos y Devoluciones:** Registro de préstamos con validación automática de disponibilidad, fechas límite y actualización de stock en tiempo real mediante transacciones SQL.
*   **Panel de Control (Dashboard):** KPIs en tiempo real (libros activos, lectores, préstamos activos y vencidos).
*   **Reportes Dinámicos:** Visualización de préstamos vencidos, préstamos activos y libros con baja disponibilidad.
*   **Gestión de Lectores:** Registro de alumnos con datos como número de cuenta, carrera, semestre y contacto.

## Tecnologías Utilizadas

*   **Backend:** PHP 8+ (PDO para consultas preparadas y transacciones).
*   **Base de Datos:** Microsoft SQL Server (T-SQL, Vistas, Procedimientos, Triggers).
*   **Frontend:** HTML5, CSS3 (Diseño responsivo y personalizado).
*   **Seguridad:** `password_hash()` y `password_verify()` para contraseñas.

## Estructura del Proyecto

| Archivo | Descripción |
| :--- | :--- |
| `index.php` | Redirecciona al dashboard si hay sesión activa, o al login si no. |
| `login.php` | Pantalla de acceso y validación de credenciales. |
| `logout.php` | Cierra la sesión del usuario actual. |
| `dashboard.php` | Panel principal con estadísticas y accesos rápidos. |
| `libros.php` | CRUD de libros (registro, edición, baja lógica, disponibilidad). |
| `autores.php` | CRUD de autores. |
| `categorias.php` | CRUD de categorías de libros. |
| `lectores.php` | CRUD de lectores (alumnos). |
| `prestamos.php` | Registro de préstamos y devoluciones con control de stock. |
| `reportes.php` | Reportes de vencidos, activos y baja disponibilidad. |
| `usuarios.php` | Gestión de usuarios del sistema (solo administradores). |
| `includes/header.php` | Cabecera HTML, navegación y conexión a la base de datos. |
| `includes/footer.php` | Pie de página y cierre de etiquetas HTML. |
| `config/conexion.php` | Configuración de la conexión PDO y funciones globales. |

## Requisitos de Instalación

1.  **Servidor Web:** Apache o Nginx (XAMPP, WAMP, Laragon o similar).
2.  **PHP:** Versión 8.0 o superior.
3.  **Extensiones PHP:** `pdo_sqlsrv` (para conexión con SQL Server).
4.  **Base de Datos:** Microsoft SQL Server (2016 o superior).

## Instalación y Configuración

1.  **Clonar el repositorio:**
    ```bash
    git clone https://github.com/tu-usuario/biblioteca-fesc.git
    cd biblioteca-fesc
