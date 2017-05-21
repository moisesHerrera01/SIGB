<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UnidadMedidas extends CI_Controller {

  public function __construct() {
    parent::__construct();

    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }

    $this->load->helper(array('form', 'paginacion'));
    $this->load->library(array('table'));
    $this->load->model('Bodega/UnidadMedida');
  }

  public function index(){
    $data['title'] = "Unidad Medida";
    $data['js'] = "assets/js/validate/um.js";

    $msg = array('alert' => $this->uri->segment(4), );

		$data['body'] = $this->load->view('mensajes', $msg, TRUE) . $this->load->view('Bodega/unidadMedida', '', TRUE) .
                    "<br><div class='content_table'>" .
                    "<div class='limit-content-title'><span class='icono icon-table icon-title'> Unidad Medida</span></div>".
                    "<div class='limit-content'>" . $this->mostrarTabla() . "</div></div>";
    $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
    $this->load->view('base', $data);
	}

  public function mostrarTabla(){
    $modulo=$this->User_model->obtenerModulo('Bodega/UnidadMedidas');
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
        $this->table->set_heading('#','Nombre', 'Abreviatura','Modificar', 'Eliminar');

        /*
        * Filtro a la BD
        */

        /*Obtiene el numero de registros a mostrar por pagina */
        $num = '10';
        $pagination = '';
        $registros;
        if ($this->input->is_ajax_request()) {
          if (!($this->input->post('busca') == "")) {
              $registros = $this->UnidadMedida->buscarUnidades($this->input->post('busca'));
          } else {
              $registros = $this->UnidadMedida->obtenerUnidadesLimit($num, $this->uri->segment(4));
              $pagination = paginacion('index.php/Bodega/UnidadMedidas/index/', $this->UnidadMedida->totalUnidades(),
                            $num, '4');
          }
        } else {
              $registros = $this->UnidadMedida->obtenerUnidadesLimit($num, $this->uri->segment(4));
              $pagination = paginacion('index.php/Bodega/UnidadMedidas/index/', $this->UnidadMedida->totalUnidades(),
                            $num, '4');
        }

        /*
        * llena la tabla con los datos consultados
        */

        if (!($registros == FALSE)) {
          foreach($registros as $unidad) {
              $onClick = "llenarFormulario('UnidadMedida', ['id', 'nombre', 'abreviatura'], [$unidad->id_unidad_medida, '$unidad->nombre' , '$unidad->abreviatura'])";

              $this->table->add_row($unidad->id_unidad_medida, $unidad->nombre, $unidad->abreviatura,
                              //form_button($btn_act), $form_el,
                              '<a class="icono icon-actualizar" onClick="'.$onClick.'"></a>',
                              '<a class="icono icon-eliminar" uri='.base_url('index.php/Bodega/UnidadMedidas/EliminarDato/'.$unidad->id_unidad_medida).'></a>');
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
        redirect('/Bodega/UnidadMedidas/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  /*
  * Actualiza o Registra al sistema
  */
  public function RecibirDatos(){
    $modulo=$this->User_model->obtenerModulo('Bodega/UnidadMedidas');
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $data = array(
          'nombre' => $this->input->post('nombre'),
          'abreviatura' => $this->input->post('abreviatura')
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
          $this->UnidadMedida->actualizarUnidad($this->input->post('id'),$data);
          $rastrea['operacion']='ACTUALIZA';
          $rastrea['id_registro']=$this->input->post('id');
          $this->User_model->insertarRastreabilidad($rastrea);
          redirect('/Bodega/UnidadMedidas/index/update');
        } else {
          redirect('/Bodega/UnidadMedidas/index/forbidden');
        }
      }
      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'insert')) {
        $this->UnidadMedida->insertarUnidad($data);
        $rastrea['operacion']='INSERTA';
        $rastrea['id_registro']=$this->User_model->obtenerSiguienteIdModuloIncrement('sic_unidad_medida')-1;
        $this->User_model->insertarRastreabilidad($rastrea);
        redirect('/Bodega/UnidadMedidas/index/new');
      } else {
        redirect('/Bodega/UnidadMedidas/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  /*
  * elimina un registro cuando se le pasa por la url el id
  */
  public function EliminarDato(){
    $modulo=$this->User_model->obtenerModulo('Bodega/UnidadMedidas');
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
        $this->UnidadMedida->eliminarUnidad($id);
        $this->User_model->insertarRastreabilidad($rastrea);
        redirect('/Bodega/UnidadMedidas/index/delete');
      } else {
        redirect('/Bodega/UnidadMedidas/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  public function Autocomplete(){
    $registros = '';
    if ($this->input->is_ajax_request()) {
      if (!($this->input->post('autocomplete') == "")) {
          $registros = $this->UnidadMedida->buscarUnidades($this->input->post('autocomplete'));
      } else {
          $registros = $this->UnidadMedida->obtenerUnidades();
      }
    } else {
          $registros = $this->UnidadMedida->obtenerUnidades();
    }

    if ($registros == '') {
      echo '<div id="1" class="suggest-element"><a id="conteo">No se encontraron coincidencias</a></div>';
    }else {
      $i = 1;
      foreach ($registros as $unidadMedida) {
        echo '<div id="'.$i.'" class="suggest-element" ida="producto'.$unidadMedida->id_unidad_medida.'"><a id="producto'.
        $unidadMedida->id_unidad_medida.'" data="'.$unidadMedida->id_unidad_medida.'"  data1="'.$unidadMedida->nombre.'" >'
        .$unidadMedida->nombre.'</a></div>';
        $i++;
      }
    }
  }
}//esto es un comentario
?>
