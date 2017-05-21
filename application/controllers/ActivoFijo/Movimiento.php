<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Movimiento extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('url', 'form', 'paginacion'));
    $this->load->library('table');
    $this->load->model(array('ActivoFijo/Bienes_Muebles_Model','ActivoFijo/Movimiento_Model',
                              'ActivoFijo/Tipo_movimiento_model'));
  }

  public function index(){

    $data['title'] = "Movimientos";
    $data['js'] = "assets/js/validate/movimiento.js";
    $msg = array('alert' => $this->uri->segment(4),'controller'=>'movimiento');

		$data['body'] = $this->load->view('mensajes', $msg, TRUE) . $this->load->view('ActivoFijo/movimiento_view','', TRUE) .
                    "<br><div class='content_table table-responsive'>" .
                    "<div class='limit-content-title'><span class='icono icon-table icon-title'> Movimientos</span></div>".
                    "<div class='limit-content'>" . $this->mostrarTabla() . "</div></div>";
    $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
    $this->load->view('base', $data);
	}

  public function mostrarTabla(){
    /*
    * Configuracion de la tabla
    */
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Movimiento');
    $USER = $this->session->userdata('logged_in');
    if($USER){
      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'update')) {

        $template = array(
            'table_open' => '<table class="table table-striped table-bordered">'
        );
        $this->table->set_template($template);
        $this->table->set_heading('Id','Oficina Recibe', 'Empleado','NR','Elabora','Editar','Eliminar','Detalle','Imprimir','Cerrar');

        /*
        * Filtro a la BD
        */

        /*Obtiene el numero de registros a mostrar por pagina */
        $num = '10';
        $pagination = '';
        $registros;
        if ($this->input->is_ajax_request()) {
          if (!($this->input->post('busca') == "")) {
              $registros = $this->Movimiento_Model->buscarMovimientos($this->input->post('busca'));
          } else {
              $registros = $this->Movimiento_Model->obtenerMovimientosLimit($num, $this->uri->segment(4));
              $pagination = paginacion('index.php/ActivoFijo/Movimiento/index/', $this->Movimiento_Model->totalMovimientos(),
                            $num, '4');
          }
        } else {
              $registros = $this->Movimiento_Model->obtenerMovimientosLimit($num, $this->uri->segment(4));
              $pagination = paginacion('index.php/ActivoFijo/Movimiento/index/', $this->Movimiento_Model->totalMovimientos(),
                            $num, '4');
        }

        /*
        * llena la tabla con los datos consultados
        */

        if (!($registros == FALSE)) {
          foreach($registros as $mov) {
              $nom_ofi_recibe = $this->Bienes_Muebles_Model->obtenerOficina($mov->id_oficina_recibe);
              $onClick = "llenarFormulario('movimiento', ['id','oficina_entrega', 'autocomplete3','oficina_recibe',
              'autocomplete','empleado','autocomplete4','tipo_movimiento','autocomplete5','usuario_externo',
              'entregado_por','recibido_por','autorizado_por','visto_bueno_por'],
              [$mov->id_movimiento,'$mov->id_oficina_entrega', '$mov->nombre_oficina','$mov->id_oficina_recibe',
              '$nom_ofi_recibe','$mov->id_empleado','$mov->primer_nombre $mov->primer_apellido',
              '$mov->id_tipo_movimiento','$mov->nombre_movimiento','$mov->usuario_externo',
              '$mov->entregado_por','$mov->recibido_por','$mov->autorizado_por','$mov->visto_bueno_por'],
              false,false,false, 'observacion', '$mov->observacion')";

              $this->table->add_row($mov->id_movimiento,$nom_ofi_recibe,$mov->primer_nombre.' '.$mov->primer_apellido,$mov->nr,$mov->nombre_completo,
              '<a class="icono icon-actualizar" onClick="'.$onClick.'"></a>',
              '<a class="icono icon-eliminar" uri='.base_url('index.php/ActivoFijo/Movimiento/EliminarDato/'.$mov->id_movimiento).'></a>',
              '<a class="icono icon-detalle" href="'.base_url('index.php/ActivoFijo/Detalle_Movimiento/index/'.$mov->id_movimiento.'/').'"></a>',
              '<a class="icono icon-acta" target="_blank" href="'.base_url('index.php/ActivoFijo/Movimiento_imp/index/'.$mov->id_movimiento.'/').'"></a>',
              '<a class="icono icon-lock" href="'.base_url('index.php/ActivoFijo/Movimiento/cerrar/'.$mov->id_movimiento.'/').'"></a>');
          }
        } else {
          $msg = array('data' => "Texto no encontrado", 'colspan' => "10");
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
      } else {
        redirect('/ActivoFijo/Movimiento/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  /*
  * Actualiza o Registra al sistema
  */
  public function RecibirDatos(){
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Movimiento');
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $data = array(
          'id_oficina_entrega' => $this->input->post('oficina_entrega'),
          'id_oficina_recibe' => $this->input->post('oficina_recibe'),
          'id_empleado' => $this->input->post('empleado'),
          'id_tipo_movimiento' => $this->input->post('tipo_movimiento'),
          'usuario_externo' => $this->input->post('usuario_externo'),
          'entregado_por' => $this->input->post('entregado_por'),
          'recibido_por' => $this->input->post('recibido_por'),
          'autorizado_por' => $this->input->post('autorizado_por'),
          'visto_bueno_por' => $this->input->post('visto_bueno_por'),
          'observacion' => $this->input->post('observacion')
      );
      if (!($this->input->post('id') == '')){
        if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'update')) {
          $this->Movimiento_Model->actualizarMovimiento($this->input->post('id'),$data);
          redirect('/ActivoFijo/Movimiento/index/update');
        } else {
          redirect('/ActivoFijo/Movimiento/index/forbidden');
        }
      }

      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'insert')) {
        $this->Movimiento_Model->insertarMovimiento($data);
        redirect('/ActivoFijo/Movimiento/index/new');
      } else {
        redirect('/ActivoFijo/Movimiento/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  /*
  * elimina un registro cuando se le pasa por la url el id
  */
  public function EliminarDato(){
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Movimiento');
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $id = $this->uri->segment(4);
      if ($this->Movimiento_Model->contieneDetalleMovimiento($id)->asociados > 0){
        if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'delete')) {
          redirect('/ActivoFijo/Movimiento/index/no_delete');
        } else {
          redirect('/ActivoFijo/Movimiento/index/forbidden');
        }
      } else {
        if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'delete')) {
          $id = $this->uri->segment(4);
          $this->Movimiento_Model->eliminarMovimiento($id);
          redirect('/ActivoFijo/Movimiento/index/delete');
        } else {
          redirect('/ActivoFijo/Movimiento/index/forbidden');
        }
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  public function AutocompleteOficina(){
    $registros = '';
    if ($this->input->is_ajax_request()) {
      if (!($this->input->post('autocomplete3') == "")) {
          $registros = $this->Bienes_Muebles_Model->buscarOficinas($this->input->post('autocomplete3'));
      } else {
          $registros = $this->Bienes_Muebles_Model->obtenerOficinas();
      }
    } else {
          $registros = $this->Bienes_Muebles_Model->obtenerOficinas();
    }

    if ($registros == '') {
      echo '<div id="1" class="suggest-element"><a id="conteo">No se encontraron coincidencias</a></div>';
    } else {
      $i = 1;
      foreach ($registros as $ofi) {
        echo '<div id="'.$i.'" class="suggest-element" ida="oficina'.$ofi->id_oficina.'"><a id="oficina'.
        $ofi->id_oficina.'" data="'.$ofi->id_oficina.'"  data1="'.$ofi->nombre_oficina.'" >'
        .$ofi->nombre_oficina.'</a></div>';
        $i++;
      }
    }
  }
//Metodo autocomplete para el tipo de movimiento
  public function AutocompleteTipo_movimiento(){
    $registros = '';
    if ($this->input->is_ajax_request()) {
      if (!($this->input->post('autocomplete5') == "")) {
          $registros = $this->Tipo_movimiento_model->buscarMovimientos($this->input->post('autocomplete5'));
      } else {
          $registros = $this->Tipo_movimiento_model->obtenerMovimientos();
      }
    } else {
          $registros = $this->Tipo_movimiento_model->obtenerMovimientos();
    }

    if ($registros == '') {
      echo '<div id="1" class="suggest-element"><a id="conteo">No se encontraron coincidencias</a></div>';
    } else {
      $i = 1;
      foreach ($registros as $tip) {
        echo '<div id="'.$i.'" class="suggest-element" ida="tipo_movimiento'.$tip->id_tipo_movimiento.'"><a id="tipo_movimiento'.
        $tip->id_tipo_movimiento.'" data="'.$tip->id_tipo_movimiento.'"  data1="'.$tip->nombre_movimiento.'" >'
        .$tip->nombre_movimiento.'</a></div>';
        $i++;
      }
    }
  }

  public function cerrar(){
      $this->Movimiento_Model->cerrar($this->uri->segment(4));
      redirect('/ActivoFijo/Movimiento/index/cerrar_estado');
  }
}
?>
