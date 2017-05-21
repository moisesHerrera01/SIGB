<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Historial_movimientos extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('form', 'paginacion'));
    $this->load->library('table');
    $this->load->model(array('ActivoFijo/Movimiento_Model'));
  }

  public function index(){
    $data['title'] = "Detalle Movimiento";
    $msg = array('alert' => $this->uri->segment(5),'controller'=>'Historial_movimientos');
		$data['body'] = $this->load->view('mensajes', $msg, TRUE) . $this->load->view('ActivoFijo/Reportes/Historial_movimientos_view',$msg,TRUE) .
                    "<br><div class='content_table table-responsive'>" .
                    "<div class='limit-content-title'><span class='icono icon-table icon-title'>Historial Movimientos</span></div>".
                    "<div class='limit-content'>" . $this->mostrarTabla()."</div>";
    $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
    $this->load->view('base', $data);
	}

  public function mostrarTabla(){
    /*
    * Configuracion de la tabla
    */

    $template = array(
        'table_open' => '<table class="table table-striped table-bordered">'
    );
    $this->table->set_template($template);
    $this->table->set_heading('Id movimiento','Almacen','Sección', 'Oficina','Empleado','Fecha','Tipo de movimiento');

    /*
    * Filtro a la BD
    */

    /*Obtiene el numero de registros a mostrar por pagina */
    $num = '10';
    $pagination = '';
    $registros;
    if ($this->input->is_ajax_request()) {
          $registros = $this->Movimiento_Model->obtenerMovimientosLimitId($num, $this->uri->segment(6),$this->uri->segment(5));
          $pagination = paginacion('index.php/ActivoFijo/Reportes/Historial_movimientos/index/'.$this->uri->segment(5), $this->Movimiento_Model->totalMovimientos($this->uri->segment(4)),
                        $num, '5');
    } else {
          $registros = $this->Movimiento_Model->obtenerMovimientosLimitId($num, $this->uri->segment(6),$this->uri->segment(5));
          $pagination = paginacion('index.php/ActivoFijo/Reportes/Historial_movimientos/index/'.$this->uri->segment(5), $this->Movimiento_Model->totalMovimientos($this->uri->segment(4)),
                        $num, '5');
    }

    /*
    * llena la tabla con los datos consultados
    */

    if (!($registros == FALSE)) {
      foreach($registros as $det) {
          $this->table->add_row($det->id_movimiento, $det->nombre_almacen,$det->nombre_seccion,$det->nombre_oficina,
          $det->primer_nombre.' '.$det->segundo_nombre.' '.$det->primer_apellido.' '.$det->segundo_apellido,
          $det->fecha_guarda,$det->nombre_movimiento);
      }
    } else {
      $msg = array('data' => "Texto no encontrado", 'colspan' => "7");
      $this->table->add_row($msg);
    }
    /*
    * vuelve a verificar para mostrar los datos
    */
    if ($this->input->is_ajax_request()) {
      echo $this->table->generate() . $pagination;
    } else {
      return $this->table->generate() . $pagination;
    }
  }
}
?>
