<?php 

require_once __DIR__ . '/../config/config.php';

class Db {

	private $host;
	private $db;
	private $user;
	private $pass;
	public $conection;

	public function __construct() {		

		$this->host = constant('DB_HOST');
		$this->db = constant('DB');
		$this->user = constant('DB_USER');
		$this->pass = constant('DB_PASS');

		try {
           $this->conection = new PDO('mysql:host='.$this->host.';dbname='.$this->db, $this->user, $this->pass);
           $this->conection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
           $this->conection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error de conexión: " . $e->getMessage();
            exit();
        }

	}

}

?>

