<?php 

require_once __DIR__ . '/../model/reservation.php';
require_once __DIR__ . '/../model/tour.php';

class ReservationController{
	public $page_title;
	public $view;

	public function __construct() {
		$this->view = 'listar_reservas';
		$this->page_title = 'Reservas';
		$this->objeto_reserva = new Reservation();
	}

	/* List all reservations */
	public function list(){
		$this->page_title = 'Reservas Realizadas';
		return $this->objeto_reserva->getReservations([]);
	}

	/* Load reservation for edit */
	public function edit($id = null){
		$this->page_title = 'Editar Reserva';
		$this->view = 'editar_reserva';
		if(isset($_GET["id"])) $id = $_GET["id"];
		
		$reservation = $this->objeto_reserva->getReservationById($id);
		$objeto_tour = new Tour();
		$tours = $objeto_tour->getTours();
		
		return [
			'reservation' => $reservation,
			'tours' => $tours
		];
	}

	/* Create reservation */
	public function create(){
		$this->page_title = 'Nueva Reserva';
		$this->view = 'crear_reserva';
		$tour_id = isset($_GET["tour_id"]) ? $_GET["tour_id"] : null;
		
		$objeto_tour = new Tour();
		$tour = null;
		if($tour_id){
			$tour = $objeto_tour->getTourById($tour_id);
		}
		$tours = $objeto_tour->getTours();
		
		return [
			'tour' => $tour,
			'tours' => $tours
		];
	}

	/* Save reservation */
	public function save(){
		$this->view = 'editar_reserva';
		$this->page_title = 'Guardar Reserva';
		$id = $this->objeto_reserva->save($_POST);
		
		if($id){
			$result = $this->objeto_reserva->getReservationById($id);
			$objeto_tour = new Tour();
			$tours = $objeto_tour->getTours();
			$_GET["response"] = true;
			return [
				'reservation' => $result,
				'tours' => $tours
			];
		} else {
			$_GET["error"] = true;
			$objeto_tour = new Tour();
			$tours = $objeto_tour->getTours();
			return [
				'reservation' => $_POST,
				'tours' => $tours
			];
		}
	}

	/* Confirm to delete */
	public function confirmDelete(){
		$this->page_title = 'Eliminar Reserva';
		$this->view = 'confirmar_eliminar_reserva';
		return $this->objeto_reserva->getReservationById($_GET["id"]);
	}

	/* Delete */
	public function delete(){
		$this->page_title = 'Listado de Reservas';
		$this->view = 'eliminar_reserva';
		return $this->objeto_reserva->deleteReservationById($_POST["id"]);
	}

	/* Cancel reservation */
	public function cancel(){
		$this->page_title = 'Cancelar Reserva';
		$this->view = 'cancelar_reserva';
		$id = isset($_GET["id"]) ? $_GET["id"] : null;
		$result = $this->objeto_reserva->cancelReservation($id);
		return ['success' => $result];
	}

}

?>

