<?php 

require_once __DIR__ . '/db.php';

class Tour {

	private $table = 'tours';
	private $conection;

	public function __construct() {
		
	}

	/* Set conection */
	public function getConection(){
		$dbObj = new Db();
		$this->conection = $dbObj->conection;
	}

	/* Get all tours */
	public function getTours($filters = []){
		$this->getConection();
		$sql = "SELECT * FROM ".$this->table." WHERE 1=1";
		$parametros = [];

		// Filtro por búsqueda (nombre o descripción)
		if(isset($filters['search']) && !empty($filters['search'])){
			$sql .= " AND (nombre LIKE ? OR descripcion LIKE ?)";
			$termino_busqueda = "%".$filters['search']."%";
			$parametros[] = $termino_busqueda;
			$parametros[] = $termino_busqueda;
		}

		$sql .= " ORDER BY fecha_creacion DESC";

		$stmt = $this->conection->prepare($sql);
		$stmt->execute($parametros);

		return $stmt->fetchAll();
	}

	/* Get tour by id */
	public function getTourById($id){
		if(is_null($id)) return false;
		$this->getConection();
		$sql = "SELECT * FROM ".$this->table. " WHERE id = ?";
		$stmt = $this->conection->prepare($sql);
		$stmt->execute([$id]);

		return $stmt->fetch();
	}

	/* Save tour */
	public function save($param){
		$this->getConection();

		/* Set default values */
		$nombre = $descripcion = $categoria = $precio = $duracion = $cupos_disponibles = "";
		$id = null;

		/* Check if exists */
		$exists = false;
		if(isset($param["id"]) && $param["id"] != ''){
			$tour_actual = $this->getTourById($param["id"]);
			if(isset($tour_actual["id"])){
				$exists = true;	
				$id = $param["id"];
				$nombre = $tour_actual["nombre"];
				$descripcion = $tour_actual["descripcion"];
				$categoria = $tour_actual["categoria"];
				$precio = $tour_actual["precio"];
				$duracion = $tour_actual["duracion"];
				$cupos_disponibles = $tour_actual["cupos_disponibles"];
			}
		}

		/* Received values */
		if(isset($param["nombre"])) $nombre = trim($param["nombre"]);
		if(isset($param["descripcion"])) $descripcion = trim($param["descripcion"]);
		if(isset($param["categoria"])) $categoria = trim($param["categoria"]);
		if(isset($param["precio"])) $precio = floatval($param["precio"]);
		if(isset($param["duracion"])) $duracion = intval($param["duracion"]);
		if(isset($param["cupos_disponibles"])) $cupos_disponibles = intval($param["cupos_disponibles"]);

		/* Validations */
		if(empty($nombre) || empty($descripcion) || empty($categoria) || $precio <= 0 || $duracion <= 0){
			return false;
		}

		/* Database operations */
		if($exists){
			$sql = "UPDATE ".$this->table. " SET nombre=?, descripcion=?, categoria=?, precio=?, duracion=?, cupos_disponibles=? WHERE id=?";
			$stmt = $this->conection->prepare($sql);
			$res = $stmt->execute([$nombre, $descripcion, $categoria, $precio, $duracion, $cupos_disponibles, $id]);
		}else{
			$sql = "INSERT INTO ".$this->table. " (nombre, descripcion, categoria, precio, duracion, cupos_disponibles, fecha_creacion) values(?, ?, ?, ?, ?, ?, NOW())";
			$stmt = $this->conection->prepare($sql);
			$stmt->execute([$nombre, $descripcion, $categoria, $precio, $duracion, $cupos_disponibles]);
			$id = $this->conection->lastInsertId();
		}	

		return $id;	

	}

	/* Delete tour by id */
	public function deleteTourById($id){
		$this->getConection();
		$sql = "DELETE FROM ".$this->table. " WHERE id = ?";
		$stmt = $this->conection->prepare($sql);
		return $stmt->execute([$id]);
	}

	/* Update available slots */
	public function updateAvailableSlots($id, $quantity){
		$this->getConection();
		$sql = "UPDATE ".$this->table. " SET cupos_disponibles = cupos_disponibles - ? WHERE id = ? AND cupos_disponibles >= ?";
		$stmt = $this->conection->prepare($sql);
		return $stmt->execute([$quantity, $id, $quantity]);
	}

}

?>

