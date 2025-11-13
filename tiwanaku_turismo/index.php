<?php 
session_start();

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/model/db.php';

// Protección de sesión - excepto para auth
if(!isset($_GET["controller"])) $_GET["controller"] = constant("DEFAULT_CONTROLLER");
if(!isset($_GET["action"])) $_GET["action"] = constant("DEFAULT_ACTION");

// Verificar si necesita autenticación (excepto login, register y theme)
if($_GET["controller"] != "auth" && $_GET["controller"] != "theme" && !isset($_SESSION['user_id'])){
	header('Location: index.php?controller=auth&action=login');
	exit();
}

$ruta_controlador = __DIR__ . '/controller/'.$_GET["controller"].'.php';

/* Check if controller exists */
if(!file_exists($ruta_controlador)) $ruta_controlador = __DIR__ . '/controller/'.constant("DEFAULT_CONTROLLER").'.php';

/* Load controller */
require_once $ruta_controlador;
$nombre_controlador = $_GET["controller"].'Controller';
$controlador = new $nombre_controlador();

/* Check if method is defined */
$datos_a_vista["data"] = array();
if(method_exists($controlador,$_GET["action"])) $datos_a_vista["data"] = $controlador->{$_GET["action"]}();


/* Load views */
require_once __DIR__ . '/view/template/header.php';
require_once __DIR__ . '/view/'.$controlador->view.'.php';
require_once __DIR__ . '/view/template/footer.php';

?>

