# Guía de Instalación Rápida - Turismo Tiwanaku

## Pasos para Instalar

### 1. Requisitos Previos
- XAMPP instalado y funcionando
- PHP 7.4 o superior
- MySQL 5.7 o superior
- Navegador web moderno

### 2. Instalación de la Base de Datos

1. Abrir phpMyAdmin: `http://localhost/phpmyadmin`
2. Crear una nueva base de datos llamada `tiwanaku_turismo`
3. Seleccionar la base de datos
4. Ir a la pestaña "Importar"
5. Seleccionar el archivo `database.sql` del proyecto
6. Hacer clic en "Continuar"

**O ejecutar el SQL manualmente:**
```sql
CREATE DATABASE tiwanaku_turismo;
USE tiwanaku_turismo;
-- Copiar y pegar el contenido de database.sql
```

### 3. Configuración

1. Verificar que el proyecto esté en: `C:\xampp\htdocs\tiwanaku_turismo`
2. Editar `config/config.php` si es necesario:
   ```php
   define("DB_HOST", "localhost");
   define("DB", "tiwanaku_turismo");
   define("DB_USER", "root");
   define("DB_PASS", ""); // Cambiar si tiene contraseña
   ```

### 4. Iniciar el Servidor

1. Abrir el Panel de Control de XAMPP
2. Iniciar Apache
3. Iniciar MySQL
4. Abrir el navegador y acceder a: `http://localhost/tiwanaku_turismo`

### 5. Verificar la Instalación

- Debería ver la página de listado de tours
- Debería haber 10 tours de ejemplo cargados
- Debería poder crear, editar y eliminar tours
- Debería poder crear reservas

## Solución de Problemas

### Error de Conexión a la Base de Datos
- Verificar que MySQL esté corriendo
- Verificar las credenciales en `config/config.php`
- Verificar que la base de datos exista

### Error 404
- Verificar que Apache esté corriendo
- Verificar la ruta del proyecto
- Verificar permisos de archivos

### Errores de PHP
- Verificar la versión de PHP (debe ser 7.4 o superior)
- Verificar que las extensiones PDO y PDO_MySQL estén habilitadas

## Características a Probar

1. **Gestión de Tours:**
   - Listar tours
   - Crear nuevo tour
   - Editar tour
   - Eliminar tour
   - Buscar tours
   - Filtrar por categoría

2. **Gestión de Reservas:**
   - Crear reserva
   - Listar reservas
   - Editar reserva
   - Cancelar reserva
   - Eliminar reserva

3. **Almacenamiento Web:**
   - Carrito de compras (SessionStorage) - visible en F12 > Application > Session Storage
   - Cambiar tema (Cookies) - icono de luna/sol en el header
   - Las cookies solo son visibles al inspeccionar (F12 > Application > Cookies)

4. **Estadísticas:**
   - Ver estadísticas de tours
   - Ver tours por categoría

## Datos de Prueba

El script SQL incluye:
- 10 tours de ejemplo
- 4 reservas de ejemplo
- Categorías: Arqueológico, Cultural, Histórico, Religioso, Aventura

## Soporte

Para más información, consultar el archivo `README.md`

