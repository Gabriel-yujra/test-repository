<?php 

require_once __DIR__ . '/db.php';

class Reservation {

	private $table = 'reservations';
	private $conection;

	public function __construct() {
		
	}

	/* Set conection */
	public function getConection(){
		$dbObj = new Db();
		$this->conection = $dbObj->conection;
	}

	/* Get all reservations */
	public function getReservations($filters = []){
		$this->getConection();
		$sql = "SELECT r.*, t.nombre as tour_nombre, t.precio as tour_precio 
				FROM ".$this->table." r 
				INNER JOIN tours t ON r.tour_id = t.id 
				ORDER BY r.fecha_reserva DESC";

		$stmt = $this->conection->prepare($sql);
		$stmt->execute();

		return $stmt->fetchAll();
	}

	/* Get reservation by id */
	public function getReservationById($id){
		if(is_null($id)) return false;
		$this->getConection();
		$sql = "SELECT r.*, t.nombre as tour_nombre, t.precio as tour_precio, t.categoria 
				FROM ".$this->table." r 
				INNER JOIN tours t ON r.tour_id = t.id 
				WHERE r.id = ?";
		$stmt = $this->conection->prepare($sql);
		$stmt->execute([$id]);

		return $stmt->fetch();
	}

	/* Save reservation */
	public function save($param){
		$this->getConection();

		$tour_id = $cliente_nombre = $cliente_email = $cliente_telefono = $cantidad_personas = $fecha_tour = "";
		$id = null;
		$estado = "pendiente";

		/* Check if exists */
		$exists = false;
		if(isset($param["id"]) && $param["id"] != ''){
			$reserva_actual = $this->getReservationById($param["id"]);
			if(isset($reserva_actual["id"])){
				$exists = true;	
				$id = $param["id"];
				$tour_id = $reserva_actual["tour_id"];
				$cliente_nombre = $reserva_actual["cliente_nombre"];
				$cliente_email = $reserva_actual["cliente_email"];
				$cliente_telefono = $reserva_actual["cliente_telefono"];
				$cantidad_personas = $reserva_actual["cantidad_personas"];
				$fecha_tour = $reserva_actual["fecha_tour"];
				$estado = $reserva_actual["estado"];
			}
		}

		/* Received values */
		if(isset($param["tour_id"])) $tour_id = intval($param["tour_id"]);
		if(isset($param["cliente_nombre"])) $cliente_nombre = trim($param["cliente_nombre"]);
		if(isset($param["cliente_email"])) $cliente_email = trim($param["cliente_email"]);
		if(isset($param["cliente_telefono"])) $cliente_telefono = trim($param["cliente_telefono"]);
		if(isset($param["cantidad_personas"])) $cantidad_personas = intval($param["cantidad_personas"]);
		if(isset($param["fecha_tour"])) $fecha_tour = trim($param["fecha_tour"]);
		if(isset($param["estado"])) $estado = trim($param["estado"]);

		/* Validations */
		if(empty($tour_id) || empty($cliente_nombre) || empty($cliente_email) || $cantidad_personas <= 0 || empty($fecha_tour)){
			return false;
		}

		/* Database operations */
		if($exists){
			$sql = "UPDATE ".$this->table. " SET tour_id=?, cliente_nombre=?, cliente_email=?, cliente_telefono=?, cantidad_personas=?, fecha_tour=?, estado=? WHERE id=?";
			$stmt = $this->conection->prepare($sql);
			$res = $stmt->execute([$tour_id, $cliente_nombre, $cliente_email, $cliente_telefono, $cantidad_personas, $fecha_tour, $estado, $id]);
		}else{
			// Verificar disponibilidad de cupos
			require_once __DIR__ . '/tour.php';
			$modelo_tour = new Tour();
			$tour = $modelo_tour->getTourById($tour_id);
			if(!$tour || $tour['cupos_disponibles'] < $cantidad_personas){
				return false;
			}

			$sql = "INSERT INTO ".$this->table. " (tour_id, cliente_nombre, cliente_email, cliente_telefono, cantidad_personas, fecha_tour, estado, fecha_reserva) values(?, ?, ?, ?, ?, ?, ?, NOW())";
			$stmt = $this->conection->prepare($sql);
			$stmt->execute([$tour_id, $cliente_nombre, $cliente_email, $cliente_telefono, $cantidad_personas, $fecha_tour, $estado]);
			$id = $this->conection->lastInsertId();

			// Actualizar cupos disponibles
			if($id){
				$modelo_tour->updateAvailableSlots($tour_id, $cantidad_personas);
			}
		}	

		return $id;	

	}

	/* Delete reservation by id */
	public function deleteReservationById($id){
		$this->getConection();
		
		// Obtener la reserva para restaurar cupos
		$reservation = $this->getReservationById($id);
		if($reservation && $reservation['estado'] != 'cancelada'){
			require_once __DIR__ . '/tour.php';
			$modelo_tour = new Tour();
			// Restaurar cupos
			$this->getConection();
			$sql = "UPDATE tours SET cupos_disponibles = cupos_disponibles + ? WHERE id = ?";
			$stmt = $this->conection->prepare($sql);
			$stmt->execute([$reservation['cantidad_personas'], $reservation['tour_id']]);
		}

		$sql = "DELETE FROM ".$this->table. " WHERE id = ?";
		$stmt = $this->conection->prepare($sql);
		return $stmt->execute([$id]);
	}

	/* Cancel reservation */
	public function cancelReservation($id){
		$this->getConection();
		
		$reservation = $this->getReservationById($id);
		if(!$reservation || $reservation['estado'] == 'cancelada'){
			return false;
		}

		// Restaurar cupos
		require_once __DIR__ . '/tour.php';
		$modelo_tour = new Tour();
		$this->getConection();
		$sql = "UPDATE tours SET cupos_disponibles = cupos_disponibles + ? WHERE id = ?";
		$stmt = $this->conection->prepare($sql);
		$stmt->execute([$reservation['cantidad_personas'], $reservation['tour_id']]);

		// Marcar como cancelada
		$sql = "UPDATE ".$this->table. " SET estado = 'cancelada' WHERE id = ?";
		$stmt = $this->conection->prepare($sql);
		return $stmt->execute([$id]);
	}

}

?>

