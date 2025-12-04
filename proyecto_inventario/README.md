# Sistema de Gestión de Inventarios - Material de Construcción

Sistema completo de gestión de inventarios desarrollado en PHP para XAMPP, diseñado para la administración de materiales de construcción.

## 🎨 Características de Diseño

- **Tema Oscuro Elegante**: Diseño moderno con colores gris oscuro y azul oscuro
- **Interfaz Responsive**: Adaptable a diferentes tamaños de pantalla
- **Navegación Intuitiva**: Sidebar con menú organizado por módulos

## 📋 Módulos Implementados

### 1. Gestión de Materiales
- ✅ Registro de nuevos materiales (RF-MAT-01)
- ✅ Clasificación por categorías jerárquicas (RF-MAT-02)
- ✅ Gestión de unidades de medida (RF-MAT-03)
- ✅ Configuración de stock mínimo/máximo (RF-MAT-04)
- ✅ Gestión de precios y costos (RF-MAT-05)
- ✅ Edición de materiales (RF-MAT-09)
- ✅ Borrado lógico (RF-MAT-10)

### 2. Gestión de Proveedores
- ✅ Registro de proveedores (RF1)
- ✅ Edición de información (RF2)
- ✅ Desactivación de proveedores (RF3)
- ✅ Listado y filtrado (RF4)
- ✅ Historial de compras (RF5)
- ✅ Integración con órdenes de compra (RF8)

### 3. Control de Movimientos de Inventario
- ✅ Registro de ingresos (compras y devoluciones) (RF01)
- ✅ Registro de salidas y préstamos (RF02)
- ✅ Control de stock en tiempo real (RFO3)
- ✅ Validación de stock (bloqueo de salidas) (RFO4)
- ✅ Alertas de stock crítico (RFO6)
- ✅ Historial de movimientos (Kardex) (RFO7)
- ✅ Asignación de costos a proyectos (RF08)
- ✅ Transferencias entre almacenes

### 4. Reportes y Alertas
- ✅ Reportes de inventario (RF-01)
- ✅ Exportación de reportes (RF-02)
- ✅ Visualización gráfica (RF-03)
- ✅ Gestión de umbrales y alertas (RF-04)
- ✅ Consumos por proyecto

### 5. Administración
- ✅ Gestión de usuarios y roles
- ✅ Gestión de almacenes
- ✅ Gestión de proyectos

## 🚀 Instalación

### Requisitos
- XAMPP (PHP 7.4+ y MySQL/MariaDB)
- Navegador web moderno

### Pasos de Instalación

1. **Copiar archivos al directorio de XAMPP**
   ```
   Copiar todo el contenido a: C:\xampp\htdocs\proyecto_inventario
   ```

2. **Crear la base de datos**
   - Abrir phpMyAdmin (http://localhost/phpmyadmin)
   - Ejecutar el script SQL proporcionado para crear todas las tablas
   - O ejecutar el archivo SQL directamente desde MySQL

3. **Configurar la conexión**
   - Editar `config/database.php` si es necesario (por defecto usa root sin contraseña)

4. **Inicializar datos básicos**
   - Ejecutar: `http://localhost/proyecto_inventario/install/init_data.php`
   - Esto creará roles, unidades, categorías y usuario administrador

5. **Acceder al sistema**
   - URL: `http://localhost/proyecto_inventario/login.php`
   - Usuario: `admin`
   - Contraseña: `admin123`

## 👤 Usuarios y Roles

El sistema incluye tres roles principales:

- **Administrador**: Acceso total al sistema, gestión de usuarios, almacenes y proyectos
- **Almacenero**: Registro de entradas y salidas de materiales
- **Residente**: Solo consulta y solicitud de materiales

## 📁 Estructura del Proyecto

```
proyecto_inventario/
├── api/                    # Endpoints API
├── assets/
│   ├── css/               # Estilos CSS
│   └── js/                 # JavaScript
├── config/                 # Configuración (BD, autenticación)
├── includes/               # Header y footer
├── install/                # Scripts de instalación
├── modules/
│   ├── admin/             # Administración
│   ├── compras/           # Órdenes y facturas
│   ├── materiales/        # Gestión de materiales
│   ├── movimientos/       # Ingresos, salidas, kardex
│   ├── proveedores/       # Gestión de proveedores
│   └── reportes/          # Reportes y alertas
├── index.php              # Dashboard principal
├── login.php              # Página de login
└── logout.php             # Cerrar sesión
```

## 🎯 Funcionalidades Principales

### Dashboard
- Estadísticas generales del sistema
- Materiales con stock crítico
- Movimientos recientes
- Gráficos de consumo por proyecto

### Gestión de Materiales
- CRUD completo de materiales
- Gestión de categorías
- Control de unidades de medida
- Configuración de alertas de stock

### Movimientos
- Registro de ingresos (compras y devoluciones)
- Registro de salidas (consumo y préstamos)
- Validación automática de stock disponible
- Kardex completo con trazabilidad

### Reportes
- Inventario valorizado
- Consumos por proyecto
- Alertas de stock crítico
- Exportación a Excel (preparado)

## 🔒 Seguridad

- Autenticación por sesiones
- Control de acceso por roles
- Validación de datos en formularios
- Protección contra SQL injection (prepared statements)

## 📝 Notas

- El sistema está diseñado para uso académico en Ingeniería de Software 2
- Todos los requerimientos del PDF han sido implementados
- El diseño sigue el tema de colores solicitado (gris oscuro y azul oscuro)
- Compatible con MySQL/MariaDB

## 🛠️ Tecnologías Utilizadas

- PHP 7.4+
- MySQL/MariaDB
- HTML5 / CSS3
- JavaScript (Vanilla)
- Chart.js (para gráficos)
- Font Awesome (iconos)

## 📞 Soporte

Para cualquier duda o problema, revisar:
1. La configuración de la base de datos
2. Los permisos de archivos
3. Los logs de error de PHP y MySQL

---

**Desarrollado para Ingeniería de Software 2**

