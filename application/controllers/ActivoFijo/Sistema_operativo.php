<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sistema_operativo extends CI_Controller {

  function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('Login/index/error_no_autenticado');
    }
    $this->load->helper(array('form', 'paginacion'));
    $this->load->library('table');
    $this->load->model(array('ActivoFijo/Sistema_operativo_model'));
  }

  public function index(){

    $data['title'] = "Sistema operativo";
    $data['js'] = "assets/js/validate/so.js";

    $msg = array('alert' => $this->uri->segment(4));
		$data['body'] = $this->load->view('mensajes', $msg, TRUE) . $this->load->view('ActivoFijo/Sistema_operativo_view',$msg,TRUE) .
                    "<br><div class='content_table '>" .
                    "<div class='limit-content-title'><span class='icono icon-table icon-title'> Catalogo de Sistemas operativos</span></div>".
                    "<div class='limit-content'>"  . $this->mostrarTabla() . "</div></div>";
    $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
    $this->load->view('base', $data);
	}

  public function mostrarTabla(){
    $USER = $this->session->userdata('logged_in');
    if($USER){
      /*
      * Configuracion de la tabla
      */

      $template = array(
          'table_open' => '<table class="table table-striped table-bordered">'
      );
      $this->table->set_template($template);
      $this->table->set_heading('#','Id', 'Versión de sistema operativo','Editar','Eliminar');

      /*
      * Filtro a la BD
      */
      /*Obtiene el numero de registros a mostrar por pagina */
      $num = '10';
      $pagination = '';
      $registros;
      if ($this->input->is_ajax_request()) {
        if (!($this->input->post('busca') == "")) {
            $registros = $this->Sistema_operativo_model->buscarSistemas_operativos($this->input->post('busca'));
        } else {
            $registros = $this->Sistema_operativo_model->obtenerSistemas_operativosLimit($num, $this->uri->segment(4));
            $pagination = paginacion('index.php/ActivoFijo/Sistema_operativo/index/', $this->Sistema_operativo_model->totalSistemas_operativos(),
                          $num, '4');
        }
      } else {
            $registros = $this->Sistema_operativo_model->obtenerSistemas_operativosLimit($num, $this->uri->segment(4));
            $pagination = paginacion('index.php/ActivoFijo/Sistema_operativo/index/', $this->Sistema_operativo_model->totalSistemas_operativos(),
                          $num, '4');
      }

      /*
      * llena la tabla con los datos consultados
      */

      if (!($registros == FALSE)) {
        $i=1;
        foreach($registros as $so) {
            $onClick = "llenarFormulario('Sistema_operativo', ['id','version_sistema_operativo'],[$so->id_sistema_operativo, '$so->version_sistema_operativo'])";
            $this->table->add_row($i,$so->id_sistema_operativo, $so->version_sistema_operativo,
                            '<a class="icono icon-actualizar" onClick="'.$onClick.'"></a>',
                            '<a class="icono icon-eliminar" uri='.base_url('index.php/ActivoFijo/Sistema_operativo/EliminarDato/'.$so->id_sistema_operativo).'></a>');
                            $i++;
        }
      } else {
        $msg = array('data' => "Texto no encontrado", 'colspan' => "3");
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
      redirect('Login/index/error_no_autenticado');
    }
  }

  /*
  * Actualiza o Registra al sistema
  */
  public function RecibirDatos(){
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Sistema_operativo');
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $data = array(
          'version_sistema_operativo' => $this->input->post('version_sistema_operativo')
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
          $rastrea['operacion']='ACTUALIZA';
          $rastrea['id_registro']=$this->input->post('id');
          $this->User_model->insertarRastreabilidad($rastrea);
          $this->Sistema_operativo_model->actualizarSistema_operativo($this->input->post('id'),$data);
          redirect('/ActivoFijo/Sistema_operativo/index/update');
        } else {
          redirect('/ActivoFijo/Sistema_operativo/index/forbidden');
        }
      }

      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'insert')) {
        $rastrea['operacion']='INSERTA';
        $rastrea['id_registro']=$this->User_model->obtenerSiguienteIdModuloIncrement('sic_sistema_operativo');
        $this->User_model->insertarRastreabilidad($rastrea);
        $this->Sistema_operativo_model->insertarSistema_operativo($data);
        redirect('/ActivoFijo/Sistema_operativo/index/new');
      } else {
        redirect('/ActivoFijo/Sistema_operativo/index/forbidden');
      }
    } else {
      redirect('Login/index/error_no_autenticado');
    }
  }

  /*
  * elimina un registro cuando se le pasa por la url el id
  */
  public function EliminarDato(){
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Sistema_operativo');
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
        $this->Sistema_operativo_model->eliminarSistema_operativo($id);
        $this->User_model->insertarRastreabilidad($rastrea);
        redirect('/ActivoFijo/Sistema_operativo/index/delete');
      } else {
        redirect('/ActivoFijo/Sistema_operativo/index/forbidden');
      }
    } else {
      redirect('Login/index/error_no_autenticado');
    }
  }

  public function Autocomplete(){
    $registros = '';
    if ($this->input->is_ajax_request()) {
      if (!($this->input->post('autocomplete') == "")) {
          $registros = $this->Sistema_operativo_model->buscarSistemas_operativos($this->input->post('autocomplete'));
      } else {
          $registros = $this->Sistema_operativo_model->obtenerSistemas_operativos();
      }
    } else {
          $registros = $this->Sistema_operativo_model->obtenerSistemas_operativos();
    }

    if ($registros == '') {
      echo '<div id="1" class="suggest-element"><a id="conteo">No se encontraron coincidencias</a></div>';
    }else {
      $i = 1;
      foreach ($registros as $os) {
        echo '<div id="'.$i.'" class="suggest-element" ida="producto'.$os->id_sistema_operativo.'"><a id="producto'.
        $os->id_sistema_operativo.'" data="'.$os->id_sistema_operativo.'"  data1="'.$os->version_sistema_operativo.'" >'
        .$os->version_sistema_operativo.'</a></div>';
        $i++;
      }
    }
  }
}
?>
