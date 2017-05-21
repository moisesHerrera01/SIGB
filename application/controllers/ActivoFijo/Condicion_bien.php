<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Condicion_bien extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('form', 'paginacion'));
    $this->load->library('table');
    $this->load->model('ActivoFijo/Condicion_bien_model');
  }

  public function index(){
    $data['title'] = "Registro de Condiciones de Bienes";
    $data['js'] = "assets/js/validate/condicion_bien.js";

    $msg = array('alert' => $this->uri->segment(4), );

		$data['body'] = $this->load->view('mensajes', $msg, TRUE) . $this->load->view('ActivoFijo/condicion_bien_view', '', TRUE) .
                    "<br><div class='content_table table-responsive'>" .
                    "<div class='limit-content-title'><span class='icono icon-table icon-title'> Condición bien</span></div>".
                    "<div class='limit-content'>" . $this->mostrarTabla() . "</div></div>";
    $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
    $this->load->view('base', $data);

	}

  public function mostrarTabla(){
      /*
      * Configuracion de la tabla
      */
      $USER = $this->session->userdata('logged_in');
      $modulo=$this->User_model->obtenerModulo('ActivoFijo/Condicion_bien');
      if($USER){
        if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'update')) {

          $template = array(
              'table_open' => '<table class="table table-striped table-bordered">'
          );
          $this->table->set_template($template);
          $this->table->set_heading('Id','Nombre Condición','Modificar', 'Eliminar');

          /*
          * Filtro a la BD
          */

          /*Obtiene el numero de registros a mostrar por pagina */
          $num = '15';
          $pagination = '';
          $registros;
          if ($this->input->is_ajax_request()) {
            if (!($this->input->post('busca') == "")) {
                $registros = $this->Condicion_bien_model->buscarCondiciones($this->input->post('busca'));
            } else {
                $registros = $this->Condicion_bien_model->obtenercondicionesLimit($num, $this->uri->segment(4));
                $pagination = paginacion('index.php/ActivoFijo/Condicion_bien/index/', $this->Condicion_bien_model->totalCondiciones(),
                              $num, '4');
            }
          } else {
                $registros = $this->Condicion_bien_model->obtenerCondicionesLimit($num, $this->uri->segment(4));
                $pagination = paginacion('index.php/ActivoFijo/Condicion_bien/index/', $this->Condicion_bien_model->totalCondiciones(),
                              $num, '4');
          }

          /*
          * llena la tabla con los datos consultados
          */

          if (!($registros == FALSE)) {
            foreach($registros as $unidad) {
                $onClick = "llenarFormulario('condicion_bien_model', ['id', 'nombre_condicion_bien'], [$unidad->id_condicion_bien, '$unidad->nombre_condicion_bien'])";

                $this->table->add_row($unidad->id_condicion_bien, $unidad->nombre_condicion_bien,
                                //form_button($btn_act), $form_el,
                                '<a class="icono icon-actualizar" onClick="'.$onClick.'"></a>',
                                '<a class="icono icon-eliminar" uri='.base_url('index.php/ActivoFijo/Condicion_bien/EliminarDato/'.$unidad->id_condicion_bien).'></a>');
            }
          } else {
            $msg = array('data' => "Texto no encontrado", 'colspan' => "6");
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
          redirect('/ActivoFijo/Condicion_bien/index/forbidden');
        }
      } else {
        redirect('login/index/error_no_autenticado');
      }
  }

  /*
  * Actualiza o Registra al sistema
  */
  public function RecibirDatos(){
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Condicion_bien');
    $USER = $this->session->userdata('logged_in');
    if($USER){
        $data = array(
            'nombre_condicion_bien' => $this->input->post('nombre_condicion_bien')
        );

        if (!($this->input->post('id') == '')){
          if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'update')) {
            $this->Condicion_bien_model->actualizarCondicion($this->input->post('id'),$data);
            redirect('/ActivoFijo/Condicion_bien/index/update');
          } else {
            redirect('/ActivoFijo/Condicion_bien/index/forbidden');
          }
        }

        if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'insert')) {
          $this->Condicion_bien_model->insertarCondicion($data);
          redirect('/ActivoFijo/Condicion_bien/index/new');
        } else {
          redirect('/ActivoFijo/Condicion_bien/index/forbidden');
        }
      } else {
        redirect('login/index/error_no_autenticado');
      }
    }

  /*
  * elimina un registro cuando se le pasa por la url el id
  */
  public function EliminarDato(){
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Condicion_bien');
    $USER = $this->session->userdata('logged_in');
    if($USER){
      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'delete')) {
        $id = $this->uri->segment(4);
        $this->Condicion_bien_model->eliminarCondicion($id);
        redirect('/ActivoFijo/Condicion_bien/index/delete');
      } else {
        redirect('/ActivoFijo/Condicion_bien/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }
  //Metodo Autocomplete para la condición de los bienes
  public function Autocomplete(){
    $registros = '';
    if ($this->input->is_ajax_request()) {
      if (!($this->input->post('autocomplete') == "")) {
          $registros = $this->Condicion_bien_model->buscarCondiciones($this->input->post('autocomplete'));
      } else {
          $registros = $this->Condicion_bien_model->obtenerCondiciones();
      }
    } else {
          $registros = $this->Condicion_bien_model->obtenerCondiciones();
    }

    if ($registros == '') {
      echo '<div id="1" class="suggest-element"><a id="conteo">No se encontraron coincidencias</a></div>';
    }else {
      $i = 1;
      foreach ($registros as $unidadMedida) {
        echo '<div id="'.$i.'" class="suggest-element" ida="producto'.$unidadMedida->id_condicion_bien.'"><a id="producto'.
        $unidadMedida->id_condicion_bien.'" data="'.$unidadMedida->id_condicion_bien.'"  data1="'.$unidadMedida->nombre_condicion_bien.'" >'
        .$unidadMedida->nombre_condicion_bien.'</a></div>';
        $i++;
      }
    }
  }
}
?>
