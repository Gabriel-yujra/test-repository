// Gestión de Carrito de Compras en SessionStorage

const CLAVE_CARRITO = 'tiwanaku_cart';

/**
 * Obtener el carrito completo
 */
function obtenerCarrito() {
    try {
        const carrito = sessionStorage.getItem(CLAVE_CARRITO);
        return carrito ? JSON.parse(carrito) : [];
    } catch (error) {
        // Error silencioso - los datos solo son visibles al inspeccionar
        return [];
    }
}

/**
 * Agregar tour al carrito
 */
function agregarAlCarrito(id_tour, datos_tour) {
    try {
        const carrito = obtenerCarrito();
        
        // Verificar si ya existe
        const itemExistente = carrito.find(item => item.id == id_tour);
        if (itemExistente) {
            itemExistente.cantidad += 1;
        } else {
            carrito.push({
                id: id_tour,
                nombre: datos_tour.nombre || 'Tour',
                precio: datos_tour.precio || 0,
                categoria: datos_tour.categoria || '',
                cantidad: 1,
                fecha_agregado: new Date().toISOString()
            });
        }
        
        sessionStorage.setItem(CLAVE_CARRITO, JSON.stringify(carrito));
        actualizarInterfazCarrito();
        return true;
    } catch (error) {
        // Error silencioso - los datos solo son visibles al inspeccionar
        return false;
    }
}

/**
 * Eliminar tour del carrito
 */
function eliminarDelCarrito(id_tour) {
    try {
        const carrito = obtenerCarrito();
        const filtrado = carrito.filter(item => item.id != id_tour);
        sessionStorage.setItem(CLAVE_CARRITO, JSON.stringify(filtrado));
        actualizarInterfazCarrito();
        return true;
    } catch (error) {
        // Error silencioso - los datos solo son visibles al inspeccionar
        return false;
    }
}

/**
 * Actualizar cantidad de un item en el carrito
 */
function actualizarCantidadItemCarrito(id_tour, cantidad) {
    try {
        const carrito = obtenerCarrito();
        const item = carrito.find(item => item.id == id_tour);
        if (item) {
            if (cantidad <= 0) {
                return eliminarDelCarrito(id_tour);
            }
            item.cantidad = cantidad;
            sessionStorage.setItem(CLAVE_CARRITO, JSON.stringify(carrito));
            actualizarInterfazCarrito();
        }
        return true;
    } catch (error) {
        // Error silencioso - los datos solo son visibles al inspeccionar
        return false;
    }
}

/**
 * Limpiar el carrito
 */
function limpiarCarrito() {
    try {
        sessionStorage.removeItem(CLAVE_CARRITO);
        actualizarInterfazCarrito();
        return true;
    } catch (error) {
        // Error silencioso - los datos solo son visibles al inspeccionar
        return false;
    }
}

/**
 * Obtener total del carrito
 */
function obtenerTotalCarrito() {
    const carrito = obtenerCarrito();
    return carrito.reduce((total, item) => total + (item.precio * item.cantidad), 0);
}

/**
 * Obtener cantidad total de items
 */
function obtenerCantidadItemsCarrito() {
    const carrito = obtenerCarrito();
    return carrito.reduce((total, item) => total + item.cantidad, 0);
}

/**
 * Actualizar la interfaz del carrito
 */
function actualizarInterfazCarrito() {
    const carrito = obtenerCarrito();
    const elementoContador = document.getElementById('cartCount');
    const elementoLista = document.getElementById('cartList');
    
    if (elementoContador) {
        elementoContador.textContent = obtenerCantidadItemsCarrito();
    }
    
    if (elementoLista) {
        if (carrito.length === 0) {
            elementoLista.innerHTML = '<li><a class="dropdown-item text-center" href="#">El carrito está vacío</a></li>';
        } else {
            let html = '';
            carrito.forEach(item => {
                html += `<li>
                    <a class="dropdown-item" href="index.php?controller=tour&action=view&id=${item.id}">
                        <strong>${item.nombre}</strong><br>
                        <small>Cantidad: ${item.cantidad} - Bs. ${(item.precio * item.cantidad).toFixed(2)}</small>
                    </a>
                </li>`;
            });
            html += `<li><hr class="dropdown-divider"></li>`;
            html += `<li><a class="dropdown-item text-center"><strong>Total: Bs. ${obtenerTotalCarrito().toFixed(2)}</strong></a></li>`;
            html += `<li><a class="dropdown-item text-center text-danger" href="#" onclick="limpiarCarrito(); return false;">Limpiar carrito</a></li>`;
            elementoLista.innerHTML = html;
        }
    }
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    actualizarInterfazCarrito();
    
    // Manejar clics en botones de agregar al carrito
    document.addEventListener('click', function(e) {
        if (e.target.closest('.add-to-cart-btn')) {
            const btn = e.target.closest('.add-to-cart-btn');
            const id_tour = btn.getAttribute('data-tour-id');
            
            if (id_tour) {
                // Obtener datos del botón
                const nombre = btn.getAttribute('data-tour-nombre') || '';
                const precio = parseFloat(btn.getAttribute('data-tour-precio')) || 0;
                const categoria = btn.getAttribute('data-tour-categoria') || '';
                
                // Si no hay datos en el botón, intentar obtenerlos del card
                let datos_tour;
                if (!nombre) {
                    const tarjetaTour = btn.closest('.tour-card') || btn.closest('.card');
                    if (tarjetaTour) {
                        const elementoNombre = tarjetaTour.querySelector('h3, h5, .card-title');
                        const textoPrecio = tarjetaTour.querySelector('.text-primary, [class*="precio"]')?.textContent || '';
                        datos_tour = {
                            nombre: elementoNombre?.textContent?.trim() || 'Tour',
                            precio: parseFloat(textoPrecio.replace(/[^\d.]/g, '')) || 0,
                            categoria: tarjetaTour.querySelector('.badge')?.textContent?.trim() || ''
                        };
                    } else {
                        datos_tour = { nombre: 'Tour', precio: 0, categoria: '' };
                    }
                } else {
                    datos_tour = { nombre, precio, categoria };
                }
                
                agregarAlCarrito(id_tour, datos_tour);
            }
        }
    });
});
