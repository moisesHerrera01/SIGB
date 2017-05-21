<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Disco_Duro extends CI_Controller {

  public function __construct() {
    parent::__construct();

    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }

    $this->load->helper(array('form', 'paginacion'));
    $this->load->library(array('table'));
    $this->load->model('ActivoFijo/Disco_Duro_Model');
  }

  public function index(){
    $data['title'] = "Disco Duro";
    $data['js'] = "assets/js/validate/hdd.js";

    $msg = array('alert' => $this->uri->segment(4), );

		$data['body'] = $this->load->view('mensajes', $msg, TRUE) . $this->load->view('ActivoFijo/disco_duro_view', '', TRUE) .
                    "<br><div class='content_table'>" .
                    "<div class='limit-content-title'><span class='icono icon-table icon-title'> Discos Duros</span></div>".
                    "<div class='limit-content'>" . $this->mostrarTabla() . "</div></div>";
    $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
    $this->load->view('base', $data);
	}

  public function mostrarTabla(){
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Disco_Duro');
    $USER = $this->session->userdata('logged_in');
    if($USER){
      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'select')) {
        /*
        * Configuracion de la tabla
        */

        $template = array(
            'table_open' => '<table class="table table-striped table-bordered">'
        );
        $this->table->set_template($template);
        $this->table->set_heading('#', 'Capacidad', 'Modificar', 'Eliminar');

        /*
        * Filtro a la BD
        */

        /*Obtiene el numero de registros a mostrar por pagina */
        $num = '10';
        $pagination = '';
        $registros;
        if ($this->input->is_ajax_request()) {
          if (!($this->input->post('busca') == "")) {
              $registros = $this->Disco_Duro_Model->buscarDiscos($this->input->post('busca'));
          } else {
              $registros = $this->Disco_Duro_Model->obtenerDiscosDurosLimit($num, $this->uri->segment(4));
              $pagination = paginacion('index.php/ActivoFijo/Disco_Duro/index/', $this->Disco_Duro_Model->totalDiscosDuros(),
                            $num, '4');
          }
        } else {
              $registros = $this->Disco_Duro_Model->obtenerDiscosDurosLimit($num, $this->uri->segment(4));
              $pagination = paginacion('index.php/ActivoFijo/Disco_Duro/index/', $this->Disco_Duro_Model->totalDiscosDuros(),
                            $num, '4');
        }

        /*
        * llena la tabla con los datos consultados
        */

        if (!($registros == FALSE)) {
          foreach($registros as $disco) {
              $cantidad = substr($disco->capacidad, 0, -3);
              $unidad = substr($disco->capacidad, -3);
              $onClick = "llenarFormulario('Disco_Duro', ['id', 'capacidad'], [$disco->id_disco_duro, $cantidad], ['unidad'], ['$unidad'])";

              $this->table->add_row($disco->id_disco_duro, $disco->capacidad,
                              //form_button($btn_act), $form_el,
                              '<a class="icono icon-actualizar" onClick="'.$onClick.'"></a>',
                              '<a class="icono icon-eliminar" uri='.base_url('index.php/ActivoFijo/Disco_Duro/EliminarDato/'.$disco->id_disco_duro).'></a>');
          }
        } else {
          $msg = array('data' => "Texto no encontrado", 'colspan' => "6");
          $this->table->add_row($msg);
        }

        /*
        * vuelve a verificar para mostrar los datos
        */
        if ($this->input->is_ajax_request()) {
          echo "<div class='table-responsive'>" . $this->table->generate() . "</div>" . $pagination;
        } else {
          return "<div class='table-responsive'>" . $this->table->generate() . "</div>" . $pagination;
        }
      } else {
        redirect('/ActivoFijo/Disco_Duro/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  /*
  * Actualiza o Registra al sistema
  */
  public function RecibirDatos(){
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Disco_Duro');
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $data = array(
          'capacidad' => $this->input->post('capacidad') . " " . $this->input->post('unidad'),
      );
      date_default_timezone_set('America/El_Salvador');
      $anyo=20;
      $fecha_actual=date($anyo."y-m-d");
      $hora=date("H:i:s");
      $rastrea = array(
        'id_usuario' =>$USER['id'],
        'id_modulo' =>$modulo,
        'fecha' =>$fecha_actual,
        'hora' =>$hora,
      );
      if (!($this->input->post('id') == '')){
        if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'update')) {
          $this->Disco_Duro_Model->actualizarDiscoDuro($this->input->post('id'),$data);
          $rastrea['operacion']='ACTUALIZA';
          $rastrea['id_registro']=$this->input->post('id');
          $this->User_model->insertarRastreabilidad($rastrea);
          redirect('/ActivoFijo/Disco_Duro/index/update');
        } else {
          redirect('/ActivoFijo/Disco_Duro/index/forbidden');
        }
      }
      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'insert')) {
        $this->Disco_Duro_Model->insertarOficina($data);
        $rastrea['operacion']='INSERTA';
        $rastrea['id_registro']=$this->User_model->obtenerSiguienteIdModuloIncrement('sic_disco_duro')-1;
        $this->User_model->insertarRastreabilidad($rastrea);
        redirect('/ActivoFijo/Disco_Duro/index/new');
      } else {
        redirect('/ActivoFijo/Disco_Duro/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  /*
  * elimina un registro cuando se le pasa por la url el id
  */
  public function EliminarDato(){
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Disco_Duro');
    $USER = $this->session->userdata('logged_in');
    date_default_timezone_set('America/El_Salvador');
    $anyo=20;
    $fecha_actual=date($anyo."y-m-d");
    $hora=date("H:i:s");
    $rastrea = array(
      'id_usuario' =>$USER['id'],
      'id_modulo' =>$modulo,
      'fecha' =>$fecha_actual,
      'hora' =>$hora,
      'operacion' =>'ELIMINA',
      'id_registro' =>$this->uri->segment(4),
    );
    if($USER){
      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'delete')) {
        $id = $this->uri->segment(4);
        $this->Disco_Duro_Model->eliminarDiscoDuro($id);
        $this->User_model->insertarRastreabilidad($rastrea);
        redirect('/ActivoFijo/Disco_Duro/index/delete');
      } else {
        redirect('/ActivoFijo/Disco_Duro/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  public function Autocomplete(){
    $registros = '';
    if ($this->input->is_ajax_request()) {
      if (!($this->input->post('autocomplete') == "")) {
          $registros = $this->Disco_Duro_Model->buscarDiscos($this->input->post('autocomplete'));
      } else {
          $registros = $this->Disco_Duro_Model->obtenerDiscosDuros();
      }
    } else {
          $registros = $this->Disco_Duro_Model->obtenerDiscosDuros();
    }

    if ($registros == '') {
      echo '<div id="1" class="suggest-element"><a id="conteo">No se encontraron coincidencias</a></div>';
    }else {
      $i = 1;
      foreach ($registros as $discos) {
        echo '<div id="'.$i.'" class="suggest-element" ida="disco'.$discos->id_disco_duro.'"><a id="producto'.
        $discos->id_disco_duro.'" data="'.$discos->id_disco_duro.'"  data1="'.$discos->capacidad.'" >'
        .$discos->capacidad.'</a></div>';
        $i++;
      }
    }
  }
}
?>
