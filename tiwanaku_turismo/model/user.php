<?php 

require_once __DIR__ . '/db.php';

class User {

	private $table = 'users';
	private $conection;

	public function __construct() {
		
	}

	/* Set conection */
	public function getConection(){
		$objeto_db = new Db();
		$this->conection = $objeto_db->conection;
	}

	/* Get user by username */
	public function getUserByUsername($username){
		if(empty($username)) return false;
		$this->getConection();
		$sql = "SELECT * FROM ".$this->table. " WHERE username = ? AND activo = 1";
		$stmt = $this->conection->prepare($sql);
		$stmt->execute([$username]);
		return $stmt->fetch();
	}

	/* Get user by email */
	public function getUserByEmail($email){
		if(empty($email)) return false;
		$this->getConection();
		$sql = "SELECT * FROM ".$this->table. " WHERE email = ? AND activo = 1";
		$stmt = $this->conection->prepare($sql);
		$stmt->execute([$email]);
		return $stmt->fetch();
	}

	/* Get user by id */
	public function getUserById($id){
		if(empty($id)) return false;
		$this->getConection();
		$sql = "SELECT id, username, email, nombre_completo, rol, fecha_registro FROM ".$this->table. " WHERE id = ? AND activo = 1";
		$stmt = $this->conection->prepare($sql);
		$stmt->execute([$id]);
		return $stmt->fetch();
	}

	/* Verify password */
	public function verifyPassword($password, $hash){
		return password_verify($password, $hash);
	}

	/* Create user */
	public function create($param){
		$this->getConection();

		$username = isset($param["username"]) ? trim($param["username"]) : "";
		$email = isset($param["email"]) ? trim($param["email"]) : "";
		$password = isset($param["password"]) ? $param["password"] : "";
		$nombre_completo = isset($param["nombre_completo"]) ? trim($param["nombre_completo"]) : "";
		$rol = isset($param["rol"]) ? trim($param["rol"]) : "usuario";

		// Validations
		if(empty($username) || empty($email) || empty($password) || empty($nombre_completo)){
			return false;
		}

		// Check if username or email exists
		if($this->getUserByUsername($username) || $this->getUserByEmail($email)){
			return false;
		}

		// Hash password
		$passwordHash = password_hash($password, PASSWORD_DEFAULT);

		// Insert user
		$sql = "INSERT INTO ".$this->table. " (username, email, password, nombre_completo, rol, fecha_registro) values(?, ?, ?, ?, ?, NOW())";
		$stmt = $this->conection->prepare($sql);
		$stmt->execute([$username, $email, $passwordHash, $nombre_completo, $rol]);
		
		return $this->conection->lastInsertId();
	}

	/* Update last access */
	public function updateLastAccess($id){
		$this->getConection();
		$sql = "UPDATE ".$this->table. " SET ultimo_acceso = NOW() WHERE id = ?";
		$stmt = $this->conection->prepare($sql);
		return $stmt->execute([$id]);
	}

	/* Login */
	public function login($username, $password){
		$user = $this->getUserByUsername($username);
		if(!$user){
			return false;
		}

		if($this->verifyPassword($password, $user['password'])){
			$this->updateLastAccess($user['id']);
			// Remove password from returned data
			unset($user['password']);
			return $user;
		}

		return false;
	}

}

?>

