// Gestión de tema oscuro mediante cookies
// Las cookies solo son visibles al inspeccionar la página (F12 > Application > Cookies)

/**
 * Obtener el valor de una cookie
 */
function obtenerCookie(nombre) {
    const nombreIgual = nombre + "=";
    const cookies = document.cookie.split(';');
    for (let i = 0; i < cookies.length; i++) {
        let cookie = cookies[i];
        while (cookie.charAt(0) === ' ') cookie = cookie.substring(1, cookie.length);
        if (cookie.indexOf(nombreIgual) === 0) return cookie.substring(nombreIgual.length, cookie.length);
    }
    return null;
}

/**
 * Aplicar tema según cookie
 */
function aplicarTema() {
    const tema = obtenerCookie('pref_tema') || 'claro';
    const cuerpo = document.body;
    const html = document.documentElement;
    const iconoTema = document.getElementById('themeIcon');
    
    if (tema === 'oscuro') {
        cuerpo.classList.add('dark-theme');
        cuerpo.classList.remove('light-theme');
        html.setAttribute('data-theme', 'oscuro');
        
        // Cambiar icono a sol si existe
        if (iconoTema) {
            iconoTema.className = 'bi bi-sun-fill';
        }
        
        // Cargar CSS del tema oscuro si no está ya cargado
        if (!document.querySelector('link[href*="dark-theme.css"]')) {
            const enlace = document.createElement('link');
            enlace.rel = 'stylesheet';
            enlace.href = 'assets/css/dark-theme.css';
            document.head.appendChild(enlace);
        }
    } else {
        cuerpo.classList.add('light-theme');
        cuerpo.classList.remove('dark-theme');
        html.setAttribute('data-theme', 'claro');
        
        // Cambiar icono a luna si existe
        if (iconoTema) {
            iconoTema.className = 'bi bi-moon-fill';
        }
    }
}

/**
 * Cambiar tema - Redirige al controlador PHP que maneja la cookie
 */
function cambiarTema() {
    // Obtener la URL actual para redirigir después del cambio
    const urlActual = window.location.href;
    const urlBase = window.location.origin + window.location.pathname;
    let urlRedireccion = urlActual.replace(urlBase, '');
    
    // Si no hay parámetros, usar la página por defecto
    if (!urlRedireccion || urlRedireccion === '/' || urlRedireccion === '') {
        urlRedireccion = 'index.php?controller=tour&action=list';
    }
    
    // Redirigir al controlador que establece la cookie y aplica el tema
    window.location.href = 'index.php?controller=theme&action=toggle&redirect=' + encodeURIComponent(urlRedireccion);
}

// Aplicar tema al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    aplicarTema();
    
    // Agregar evento al botón de tema
    const botonCambiarTema = document.getElementById('themeToggle');
    if (botonCambiarTema) {
        botonCambiarTema.addEventListener('click', function(e) {
            e.preventDefault();
            cambiarTema();
        });
    }
});
