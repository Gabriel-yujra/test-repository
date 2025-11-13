# Sistema de Gestión de Turismo Tiwanaku

Sistema web desarrollado en PHP utilizando el patrón MVC (Modelo-Vista-Controlador) para la gestión de tours turísticos y reservas en Tiwanaku, Bolivia.

## 🎯 Características Principales

### Requerimientos CRUD Implementados (15 Requerimientos)

1. **Gestión de Tours (6 requerimientos)**
   - ✅ Listar tours disponibles
   - ✅ Crear nuevo tour
   - ✅ Ver detalles de un tour
   - ✅ Editar tour existente
   - ✅ Eliminar tour
   - ✅ Buscar tours por nombre/descripción

2. **Gestión de Reservas (5 requerimientos)**
   - ✅ Listar reservas
   - ✅ Crear nueva reserva
   - ✅ Editar reserva existente
   - ✅ Eliminar reserva
   - ✅ Cancelar reserva (con restauración de cupos)

3. **Sistema de Autenticación (2 requerimientos)**
   - ✅ Login de usuarios
   - ✅ Registro de usuarios

4. **Funcionalidades Adicionales (2 requerimientos)**
   - ✅ Carrito de compras (SessionStorage)
   - ✅ Tema claro/oscuro (Cookies)

**Total: 15 Requerimientos**

## 🏗️ Arquitectura MVC

### Separación de Responsabilidades

#### **Modelo (Model)**
- `model/db.php`: Clase para conexión a base de datos
- `model/tour.php`: Lógica de negocio para tours
- `model/reservation.php`: Lógica de negocio para reservas
- `model/user.php`: Lógica de negocio para usuarios

**Responsabilidades:**
- Acceso exclusivo a datos
- Validaciones de datos
- Operaciones CRUD
- Lógica de negocio (validación de cupos, cálculo de precios, autenticación, etc.)

#### **Vista (View)**
- `view/template/header.php`: Cabecera común
- `view/template/footer.php`: Pie de página común
- `view/list_tour.php`: Listado de tours
- `view/view_tour.php`: Detalles de tour
- `view/edit_tour.php`: Formulario de edición/creación
- `view/confirm_delete_tour.php`: Confirmación de eliminación
- `view/delete_tour.php`: Mensaje de eliminación
- `view/list_reservation.php`: Listado de reservas
- `view/create_reservation.php`: Formulario de creación de reserva
- `view/edit_reservation.php`: Formulario de edición
- `view/confirm_delete_reservation.php`: Confirmación de eliminación
- `view/delete_reservation.php`: Mensaje de eliminación
- `view/cancel_reservation.php`: Cancelación de reserva
- `view/login.php`: Página de login
- `view/register.php`: Página de registro

**Responsabilidades:**
- Presentación de datos
- Interfaz de usuario
- Formularios
- Sin lógica de negocio
- Sin acceso directo a datos

#### **Controlador (Controller)**
- `controller/tour.php`: Controlador de tours
- `controller/reservation.php`: Controlador de reservas
- `controller/auth.php`: Controlador de autenticación
- `controller/theme.php`: Controlador de tema

**Responsabilidades:**
- Recibir peticiones del usuario
- Seleccionar modelo apropiado
- Procesar datos
- Seleccionar vista para presentación
- Gestionar flujo de la aplicación
- Sin lógica de negocio (la lógica está en los modelos)

## 🍪 Almacenamiento Web

### Cookies
- **pref_tema**: Preferencia de tema (claro, oscuro)
  - Duración: 30 días
  - Path: / (aplicable a todo el sitio)
  - SameSite: Lax
  - **Las cookies solo son visibles al inspeccionar la página (F12 > Application > Cookies)**
  - **No se muestran datos de cookies en la interfaz**
  - Se establece mediante icono de luna/sol en el header
  - Aplica tema oscuro/claro en toda la aplicación

**Atributos de Seguridad de Cookies:**
- **SameSite: Lax**: Previene ataques CSRF (Cross-Site Request Forgery) al restringir el envío de cookies en solicitudes cross-site
- **Path: /**: La cookie es accesible en todo el sitio web
- **Duración: 30 días**: Tiempo de expiración apropiado para preferencias de usuario
- **Sin HttpOnly**: Permite acceso desde JavaScript cuando es necesario (para el cambio de tema)
- **Sin Secure**: En desarrollo local (en producción con HTTPS se debe usar Secure: true)

### SessionStorage
- **tiwanaku_cart**: Carrito de compras con tours seleccionados
  - Persistencia: Solo durante la sesión del navegador
  - Formato: JSON
  - Funcionalidades: Agregar tours, eliminar tours, ver total, actualizar cantidad
  - **Solo es visible al inspeccionar la página (F12 > Application > Session Storage)**
  - **No se muestran datos de SessionStorage en la interfaz**
  - Se actualiza al agregar tours al carrito
  - Se muestra en el dropdown del navbar
  - Se mantiene durante la sesión activa
  - Se limpia al cerrar el navegador

**Características de SessionStorage:**
- **Persistencia de Sesión**: Los datos se mantienen solo durante la sesión del navegador
- **Serialización JSON**: Los datos se almacenan como JSON para facilitar el manejo
- **Gestión de Errores**: Incluye manejo de errores al leer/escribir datos
- **Privacidad**: Los datos no se envían al servidor automáticamente
- **Limitación por Dominio**: Solo accesible desde el mismo dominio y protocolo

## 📁 Estructura del Proyecto

```
tiwanaku_turismo/
├── config/
│   └── config.php          # Configuración de la aplicación
├── controller/
│   ├── tour.php            # Controlador de tours
│   ├── reservation.php     # Controlador de reservas
│   ├── auth.php            # Controlador de autenticación
│   └── theme.php           # Controlador de tema
├── model/
│   ├── db.php              # Conexión a base de datos
│   ├── tour.php            # Modelo de tours
│   ├── reservation.php     # Modelo de reservas
│   └── user.php            # Modelo de usuarios
├── view/
│   ├── template/
│   │   ├── header.php      # Cabecera común
│   │   └── footer.php      # Pie de página común
│   ├── list_tour.php       # Listado de tours
│   ├── view_tour.php       # Detalles de tour
│   ├── edit_tour.php       # Editar/crear tour
│   ├── confirm_delete_tour.php
│   ├── delete_tour.php
│   ├── list_reservation.php
│   ├── create_reservation.php
│   ├── edit_reservation.php
│   ├── confirm_delete_reservation.php
│   ├── delete_reservation.php
│   ├── cancel_reservation.php
│   ├── login.php           # Página de login
│   └── register.php        # Página de registro
├── assets/
│   ├── css/
│   │   ├── style.css       # Estilos principales
│   │   └── dark-theme.css  # Tema oscuro
│   └── js/
│       ├── sessionStorage.js # Gestión de carrito de compras
│       ├── cookies.js      # Gestión de tema oscuro
│       └── main.js         # Script principal
├── index.php               # Punto de entrada
├── database.sql            # Script de base de datos
└── README.md               # Este archivo
```

## 🚀 Instalación

### Requisitos
- PHP 7.4 o superior
- MySQL 5.7 o superior
- Servidor web (Apache/Nginx) o XAMPP
- Navegador web moderno

### Pasos de Instalación

1. **Clonar o copiar el proyecto** en la carpeta `htdocs` de XAMPP:
   ```
   C:\xampp\htdocs\tiwanaku_turismo
   ```

2. **Crear la base de datos**:
   - Abrir phpMyAdmin (http://localhost/phpmyadmin)
   - Importar el archivo `database.sql`
   - O ejecutar el script SQL manualmente

3. **Configurar la conexión**:
   - Editar `config/config.php` si es necesario
   - Verificar credenciales de base de datos:
     ```php
     define("DB_HOST", "localhost");
     define("DB", "tiwanaku_turismo");
     define("DB_USER", "root");
     define("DB_PASS", "");
     ```

4. **Iniciar el servidor**:
   - Iniciar Apache y MySQL en XAMPP
   - Acceder a: `http://localhost/tiwanaku_turismo`

5. **Credenciales de acceso**:
   - Usuario: `admin`
   - Contraseña: `admin123`

## 📊 Base de Datos

### Tablas

#### `users`
- id (PK)
- username (UNIQUE)
- email (UNIQUE)
- password (hash bcrypt)
- nombre_completo
- rol (admin, usuario)
- fecha_registro
- ultimo_acceso
- activo

#### `tours`
- id (PK)
- nombre
- descripcion
- categoria
- precio
- duracion
- cupos_disponibles
- fecha_creacion
- fecha_actualizacion

#### `reservations`
- id (PK)
- tour_id (FK)
- cliente_nombre
- cliente_email
- cliente_telefono
- cantidad_personas
- fecha_tour
- estado (pendiente, confirmada, cancelada)
- fecha_reserva
- fecha_actualizacion

## 🎨 Diseño

- **Framework CSS**: Bootstrap 5.3
- **Iconos**: Bootstrap Icons
- **Temas**: Claro y Oscuro (cambio mediante icono en header)
- **Responsive**: Diseño adaptable a móviles y tablets
- **UX**: Interfaz intuitiva y moderna

## 🔒 Seguridad

### Prepared Statements
- Todas las consultas SQL usan prepared statements con PDO
- Prevención de inyección SQL mediante parámetros enlazados
- Ejemplo: `$stmt->execute([$id, $nombre, $precio])`

### Protección XSS
- Uso de `htmlspecialchars()` en todas las salidas de datos
- Escapado de caracteres especiales HTML
- Prevención de ejecución de scripts maliciosos

### Atributos de Seguridad de Cookies

#### SameSite: Lax
- **Propósito**: Previene ataques CSRF (Cross-Site Request Forgery)
- **Funcionamiento**: La cookie solo se envía en solicitudes del mismo sitio o en navegaciones de nivel superior (GET)
- **Protección**: Impide que sitios maliciosos envíen solicitudes con las cookies del usuario
- **Implementación**: Configurado en `setcookie()` con el parámetro `SameSite=Lax`

#### Path: /
- **Propósito**: Define el alcance de la cookie
- **Funcionamiento**: La cookie es accesible en todas las rutas del dominio
- **Seguridad**: Limita la cookie al dominio específico

#### Duración: 30 días
- **Propósito**: Balance entre persistencia y seguridad
- **Funcionamiento**: La cookie expira después de 30 días
- **Seguridad**: Reduce el riesgo de cookies permanentes comprometidas

#### Sin HttpOnly (en este caso)
- **Razón**: Se necesita acceso desde JavaScript para el cambio de tema
- **Consideración**: En producción, si no se necesita acceso desde JS, se recomienda HttpOnly: true
- **Alternativa**: Para mayor seguridad, se podría usar HttpOnly y manejar el tema solo desde el servidor

#### Sin Secure (en desarrollo)
- **Razón**: Desarrollo local sin HTTPS
- **Producción**: En producción con HTTPS, se debe usar Secure: true
- **Protección**: En HTTPS, Secure previene el envío de cookies por conexiones no seguras

### Autenticación
- Sistema de login con hash de contraseñas (bcrypt)
- Protección de sesión en todas las páginas (excepto login/register)
- Validación de credenciales en el servidor
- Sesiones PHP para mantener el estado de autenticación

### Validación
- Validación tanto en cliente (HTML5, JavaScript) como en servidor (PHP)
- Validación de tipos de datos
- Validación de rangos (precios, cantidades, fechas)
- Sanitización de entrada de datos

### SQL Injection
- Prevenido mediante PDO y prepared statements
- Parámetros enlazados en todas las consultas
- Sin concatenación directa de variables en consultas SQL

## 📝 Criterios de Evaluación Cumplidos

### I. Modelo-Vista-Controlador (MVC)

✅ **Separación de Responsabilidades (2 puntos)**
- Separación clara y estricta entre Modelo, Vista y Controlador
- No hay lógica de negocio en las Vistas
- Las Vistas solo presentan datos
- Los Controladores no contienen lógica de negocio

✅ **Implementación del Modelo (3 puntos)**
- Gestión exclusiva de datos
- Validaciones incluidas (validación de cupos, autenticación, etc.)
- Lógica de negocio en los modelos
- Notificación al Controlador sobre cambios

✅ **Implementación del Controlador (3 puntos)**
- Recibe entrada del usuario
- Selecciona modelo apropiado
- Elige vista para presentación
- Sin lógica de negocio (toda la lógica está en los modelos)

✅ **Implementación de la Vista (3 puntos)**
- Solo muestra interfaz y datos
- Sin lógica de negocio
- Sin acceso directo a datos
- Solo presenta información recibida del controlador

### II. Uso de Almacenamiento Web

✅ **SessionStorage (2 puntos)**
- Guardado eficiente de datos en SessionStorage
- Serialización/deserialización JSON
- Gestión de errores
- Funcionalidad: Carrito de compras (tiwanaku_cart)
- Operaciones: Agregar, eliminar, actualizar cantidad, ver total
- **Solo visible al inspeccionar (F12 > Application > Session Storage)**
- **No se muestran datos de SessionStorage en la interfaz**

✅ **Cookies (2 puntos)**
- Gestión correcta de cookies
- Atributos de seguridad (SameSite: Lax)
- Fechas de expiración apropiadas (30 días)
- Uso para preferencias de tema (pref_tema)
- **Las cookies solo son visibles al inspeccionar (F12 > Application > Cookies)**
- **No se muestran datos de cookies en la interfaz**

## 🎯 Requerimientos Implementados (15 Requerimientos)

### CRUD de Tours (6 requerimientos)
1. Listar tours
2. Crear tour
3. Ver detalles de tour
4. Editar tour
5. Eliminar tour
6. Buscar tours por nombre/descripción

### CRUD de Reservas (5 requerimientos)
7. Listar reservas
8. Crear reserva
9. Editar reserva
10. Eliminar reserva
11. Cancelar reserva

### Sistema de Autenticación (2 requerimientos)
12. Login de usuarios
13. Registro de usuarios

### Funcionalidades Adicionales (2 requerimientos)
14. Carrito de compras (SessionStorage)
15. Tema claro/oscuro (Cookies)

## 👥 Autor

Desarrollado para el curso de Desarrollo Web
Tema: Sistema de Turismo en Tiwanaku

## 📄 Licencia

Este proyecto es educativo y está destinado para fines académicos.
