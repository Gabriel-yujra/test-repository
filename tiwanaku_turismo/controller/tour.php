<?php 

require_once __DIR__ . '/../model/tour.php';

class TourController{
	public $page_title;
	public $view;

	public function __construct() {
		$this->view = 'listar_tours';
		$this->page_title = 'Tours Disponibles';
		$this->objeto_tour = new Tour();
	}

	/* List all tours */
	public function list(){
		$this->page_title = 'Tours Disponibles - Tiwanaku';
		$filtros = [];
		
		// Solo búsqueda general (nombre o descripción)
		if(isset($_GET['search']) && !empty($_GET['search'])){
			$filtros['search'] = $_GET['search'];
		}

		$data = $this->objeto_tour->getTours($filtros);
		
		return [
			'tours' => $data,
			'filters' => $filtros
		];
	}

	/* View tour details */
	public function view(){
		$this->page_title = 'Detalles del Tour';
		$this->view = 'ver_tour';
		$id = isset($_GET["id"]) ? $_GET["id"] : null;
		return $this->objeto_tour->getTourById($id);
	}

	/* Load tour for edit */
	public function edit($id = null){
		$this->page_title = 'Editar Tour';
		$this->view = 'editar_tour';
		/* Id can from get param or method param */
		if(isset($_GET["id"])) $id = $_GET["id"];
		
		$tour = $this->objeto_tour->getTourById($id);
		
		return [
			'tour' => $tour
		];
	}

	/* Create or update tour */
	public function save(){
		$this->view = 'editar_tour';
		$this->page_title = 'Guardar Tour';
		$id = $this->objeto_tour->save($_POST);
		
		if($id){
			$result = $this->objeto_tour->getTourById($id);
			$_GET["response"] = true;
			return [
				'tour' => $result
			];
		} else {
			$_GET["error"] = true;
			return [
				'tour' => $_POST
			];
		}
	}

	/* Confirm to delete */
	public function confirmDelete(){
		$this->page_title = 'Eliminar Tour';
		$this->view = 'confirmar_eliminar_tour';
		return $this->objeto_tour->getTourById($_GET["id"]);
	}

	/* Delete */
	public function delete(){
		$this->page_title = 'Listado de Tours';
		$this->view = 'eliminar_tour';
		return $this->objeto_tour->deleteTourById($_POST["id"]);
	}


}

?>

