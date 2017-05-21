<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Detalle_gestionar_movimiento extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('form', 'paginacion'));
    $this->load->library('table');
    $this->load->model(array('ActivoFijo/Detalle_movimiento_model','ActivoFijo/Bienes_inmuebles_model'));
  }

  public function index(){
    $data['title'] = "Detalle Solicitud Movimiento";
    $id_mov = $this->uri->segment(4);
    $nombre = $this->Detalle_movimiento_model->obtenerMovimiento($id_mov);
    $msg = array('alert' => $this->uri->segment(5),'nombre'=> $nombre->observacion,'id_mov' => $id_mov,'controller'=>'detalle_movimiento');
		$data['body'] = $this->load->view('mensajes', $msg, TRUE) . $this->load->view('ActivoFijo/Detalle_gestionar_movimiento_view',$msg,TRUE) .
                    "<br><div class='content_table table-responsive'>" .
                    "<div class='limit-content-title'><span class='icono icon-table icon-title'> Detalle Solicitud Movimientos</span></div>".
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
    $this->table->set_heading('Id','Descripción','Marca', 'Modelo','Color','Serie','Código','Código anterior','Eliminar');

    /*
    * Filtro a la BD
    */

    /*Obtiene el numero de registros a mostrar por pagina */
    $num = '10';
    $pagination = '';
    $registros;
    if ($this->input->is_ajax_request()) {
      if (!($this->input->post('busca') == "")) {
          $registros = $this->Detalle_movimiento_model->buscarDetalleMovimiento($this->input->post('busca'),$this->uri->segment(4));
      } else {
          $registros = $this->Detalle_movimiento_model->obtenerDetalleMovimientosLimit($num, $this->uri->segment(5),$this->uri->segment(4));
          $pagination = paginacion('index.php/ActivoFijo/Detalle_gestionar_movimiento/index/'.$this->uri->segment(4), $this->Detalle_movimiento_model->totalDetalleMovimientos($this->uri->segment(4)),
                        $num, '5');
      }
    } else {
          $registros = $this->Detalle_movimiento_model->obtenerDetalleMovimientosLimit($num, $this->uri->segment(5),$this->uri->segment(4));
          $pagination = paginacion('index.php/ActivoFijo/Detalle_gestionar_movimiento/index/'.$this->uri->segment(4), $this->Detalle_movimiento_model->totalDetalleMovimientos($this->uri->segment(4)),
                        $num, '5');
    }

    /*
    * llena la tabla con los datos consultados
    */

    if (!($registros == FALSE)) {
      foreach($registros as $det) {
          $onClick = "llenarFormulario('detalle', ['id_detalle_movimiento','autocomplete','bien'],
          ['$det->id_detalle_movimiento','$det->codigo','$det->id_bien'])";
          $this->table->add_row($det->id_detalle_movimiento, $det->descripcion,$det->nombre_marca,$det->modelo,$det->color,
          $det->serie,$det->codigo,$det->codigo_anterior,
          '<a class="icono icon-eliminar" uri="'.base_url('index.php/ActivoFijo/Detalle_gestionar_movimiento/EliminarDato/'.$det->id_movimiento.'/'.$det->id_detalle_movimiento.'/').'"></a>');
      }
    } else {
      $msg = array('data' => "Texto no encontrado", 'colspan' => "9");
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

  /*
  * Actualiza o Registra al sistema
  */
  public function RecibirDatos(){
    $mov=$this->Detalle_movimiento_model->obtenerTodoMovimiento($this->input->post('movimiento'));
    $data = array(
        'id_detalle_movimiento'=>$this->input->post('id_detalle_movimiento'),
        'id_movimiento' => $this->input->post('movimiento'),
        'id_bien' => $this->input->post('bien')
    );
    $data2=array(
      'id_oficina'=>$mov->id_oficina_recibe,
      'id_empleado'=>$mov->id_empleado
    );
      if (!($this->input->post('id_detalle_movimiento') == '')){
        $this->Detalle_movimiento_model->actualizarDetalleMovimiento($this->input->post('id_detalle_movimiento'),$data);
        redirect('/ActivoFijo/Detalle_gestionar_movimiento/index/'.$data['id_movimiento'].'/update');
      }
      else{
        $this->Detalle_movimiento_model->insertarDetalleMovimiento($data);
        $this->Bienes_inmuebles_model->actualizarBienesInmuebles($data['id_bien'],$data2);
        redirect('/ActivoFijo/Detalle_gestionar_movimiento/index/'.$data['id_movimiento'].'/new');
      }
  }

  public function EliminarDato(){
    $detmov=$this->Detalle_movimiento_model->obtenerTodoDetalleMovimiento($this->uri->segment(5));
    $id_mov=$this->uri->segment(4);
    $this->Detalle_movimiento_model->eliminarDetalleMovimiento($this->uri->segment(5));
    $data2=array(
      'id_oficina'=>'0',
      'id_empleado'=>'0'
    );
    $this->Bienes_inmuebles_model->actualizarBienesInmuebles($detmov->id_bien,$data2);
    redirect('/ActivoFijo/Detalle_gestionar_movimiento/index/'.$id_mov.'/delete');
  }

  public function AutocompleteBienes(){
    $registros = '';
    if ($this->input->is_ajax_request()) {
      if (!($this->input->post('autocomplete') == "")) {
          $registros = $this->Bienes_inmuebles_model->buscarBienesInmuebles($this->input->post('autocomplete'));
      } else {
          $registros = $this->Bienes_inmuebles_model->obtenerBienesInmuebles();
      }
    } else {
          $registros = $this->Bienes_inmuebles_model->obtenerBienesInmuebles();
    }
    if ($registros == '') {
      echo '<div id="1" class="suggest-element"><a id="conteo">No se encontraron coincidencias</a></div>';
    }else {
      $i = 1;
      foreach ($registros as $al) {
        echo '<div id="'.$i.'" class="suggest-element" ida="bien'.$al->id_bien.'"><a id="bien'.
        $al->id_bien.'" data="'.$al->id_bien.'"  data1="Bien: '.$al->id_bien.' - Código: '.$al->codigo.'" >
        Bien: '.$al->descripcion.' - Código: '.$al->codigo.'</a></div>';
        $i++;
      }
    }
  }
}
?>
