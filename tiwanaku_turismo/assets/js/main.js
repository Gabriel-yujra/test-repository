// Script principal de la aplicación

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar componentes
    inicializarAplicacion();
});

/**
 * Inicializar la aplicación
 */
function inicializarAplicacion() {
    // Aplicar tema (si existe la función)
    if (typeof aplicarTema === 'function') {
        aplicarTema();
    }
    
    // Mostrar mensajes de éxito/error
    mostrarMensajesFlash();
    
    // Inicializar tooltips de Bootstrap
    inicializarTooltips();
}

/**
 * Mostrar mensajes flash
 */
function mostrarMensajesFlash() {
    // Los mensajes se muestran sin auto-ocultar
    // Solo se ocultan manualmente o al recargar la página
}

/**
 * Inicializar tooltips de Bootstrap
 */
function inicializarTooltips() {
    const listaTooltips = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    listaTooltips.map(function (elementoTooltip) {
        return new bootstrap.Tooltip(elementoTooltip);
    });
}


/**
 * Formatear fecha
 */
function formatearFecha(cadenaFecha) {
    const fecha = new Date(cadenaFecha);
    return fecha.toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

/**
 * Formatear precio
 */
function formatearPrecio(precio) {
    return new Intl.NumberFormat('es-BO', {
        style: 'currency',
        currency: 'BOB',
        minimumFractionDigits: 2
    }).format(precio);
}

/**
 * Validar formularios
 */
function validarFormulario(idFormulario) {
    const formulario = document.getElementById(idFormulario);
    if (!formulario) return false;
    
    const entradas = formulario.querySelectorAll('input[required], select[required], textarea[required]');
    let esValido = true;
    
    entradas.forEach(entrada => {
        if (!entrada.value.trim()) {
            entrada.classList.add('is-invalid');
            esValido = false;
        } else {
            entrada.classList.remove('is-invalid');
        }
    });
    
    return esValido;
}

// Manejar envío de formularios
document.addEventListener('submit', function(e) {
    const formulario = e.target;
    if (formulario.tagName === 'FORM' && formulario.hasAttribute('data-validate')) {
        if (!validarFormulario(formulario.id)) {
            e.preventDefault();
            alert('Por favor, complete todos los campos requeridos');
        }
    }
});

// Confirmación antes de eliminar (solo en enlaces de confirmación, no en delete directo)
document.addEventListener('click', function(e) {
    if (e.target.closest('a[href*="confirmDelete"]')) {
        // La confirmación se maneja en la vista de confirmación
    }
});

