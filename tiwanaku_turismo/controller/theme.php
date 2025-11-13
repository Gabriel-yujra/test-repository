<?php 

class ThemeController{
	public $page_title;
	public $view;

	public function __construct() {
		$this->view = 'listar_tours';
		$this->page_title = 'Tours';
	}

	/* Toggle theme */
	public function toggle(){
		$tema_actual = isset($_COOKIE['pref_tema']) ? $_COOKIE['pref_tema'] : 'claro';
		$nuevo_tema = $tema_actual === 'oscuro' ? 'claro' : 'oscuro';
		
		$tiempo_expiracion = time() + (86400 * 30); // 30 días
		// Establecer cookie sin HttpOnly para que JavaScript pueda leerla si es necesario
		setcookie('pref_tema', $nuevo_tema, $tiempo_expiracion, "/", "", false, false);
		$_COOKIE['pref_tema'] = $nuevo_tema;
		
		// Redirigir a la página anterior
		if(isset($_GET['redirect']) && !empty($_GET['redirect'])){
			$redirect = urldecode($_GET['redirect']);
			// Limpiar la URL para evitar problemas
			$redirect = ltrim($redirect, '/');
			if(empty($redirect) || $redirect === 'index.php'){
				$redirect = 'index.php?controller=tour&action=list';
			}
		} else {
			// Si hay sesión, ir a tours, sino a login
			if(isset($_SESSION['user_id'])){
				$redirect = 'index.php?controller=tour&action=list';
			} else {
				$redirect = 'index.php?controller=auth&action=login';
			}
		}
		header('Location: ' . $redirect);
		exit();
	}

}

?>

