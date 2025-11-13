<?php 

require_once __DIR__ . '/../model/user.php';

class AuthController{
	public $page_title;
	public $view;

	public function __construct() {
		$this->view = 'iniciar_sesion';
		$this->page_title = 'Iniciar Sesión';
	}

	/* Show login page */
	public function login(){
		$this->page_title = 'Iniciar Sesión';
		$this->view = 'iniciar_sesion';
		
		// Si ya está logueado, redirigir
		if(isset($_SESSION['user_id'])){
			header('Location: index.php?controller=tour&action=list');
			exit();
		}
		
		return [];
	}

	/* Process login */
	public function doLogin(){
		$this->view = 'iniciar_sesion';
		$this->page_title = 'Iniciar Sesión';
		
		if(!isset($_POST['username']) || !isset($_POST['password'])){
			$_GET['error'] = 'Ingrese usuario y contraseña';
			return [];
		}

		$objeto_usuario = new User();
		$user = $objeto_usuario->login($_POST['username'], $_POST['password']);

		if($user){
			// Iniciar sesión
			session_start();
			$_SESSION['user_id'] = $user['id'];
			$_SESSION['username'] = $user['username'];
			$_SESSION['nombre_completo'] = $user['nombre_completo'];
			$_SESSION['rol'] = $user['rol'];
			
			header('Location: index.php?controller=tour&action=list');
			exit();
		} else {
			$_GET['error'] = 'Usuario o contraseña incorrectos';
			return [];
		}
	}

	/* Logout */
	public function logout(){
		session_start();
		session_destroy();
		header('Location: index.php?controller=auth&action=login');
		exit();
	}

	/* Show register page */
	public function register(){
		$this->page_title = 'Registrarse';
		$this->view = 'registro';
		return [];
	}

	/* Process register */
	public function doRegister(){
		$this->view = 'registro';
		$this->page_title = 'Registrarse';
		
		if(!isset($_POST['username']) || !isset($_POST['email']) || !isset($_POST['password']) || !isset($_POST['nombre_completo'])){
			$_GET['error'] = 'Complete todos los campos';
			return [];
		}

		if($_POST['password'] !== $_POST['password_confirm']){
			$_GET['error'] = 'Las contraseñas no coinciden';
			return [];
		}

		$objeto_usuario = new User();
		$id = $objeto_usuario->create($_POST);

		if($id){
			$_GET['success'] = 'Usuario registrado correctamente. Puede iniciar sesión.';
		} else {
			$_GET['error'] = 'Error al registrar usuario. El usuario o email ya existe.';
		}

		return [];
	}

}

?>

