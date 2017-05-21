<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Memoria extends CI_Controller {

  function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('Login/index/error_no_autenticado');
    }
    $this->load->helper(array('form', 'paginacion'));
    $this->load->library('table');
    $this->load->model(array('ActivoFijo/Memoria_model'));
  }

  public function index(){

    $data['title'] = "Memoria";
    $data['js'] = "assets/js/validate/mem.js";

    $msg = array('alert' => $this->uri->segment(4));
		$data['body'] = $this->load->view('mensajes', $msg, TRUE) . $this->load->view('ActivoFijo/Memoria_view',$msg,TRUE) .
                    "<br><div class='content_table '>" .
                    "<div class='limit-content-title'><span class='icono icon-table icon-title'> Catalogo de Memorias</span></div>".
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
      $this->table->set_heading('#','Id', 'Tipo de Memoria','Editar','Eliminar');

      /*
      * Filtro a la BD
      */
      /*Obtiene el numero de registros a mostrar por pagina */
      $num = '10';
      $pagination = '';
      $registros;
      if ($this->input->is_ajax_request()) {
        if (!($this->input->post('busca') == "")) {
            $registros = $this->Memoria_model->buscarMemorias($this->input->post('busca'));
        } else {
            $registros = $this->Memoria_model->obtenerMemoriasLimit($num, $this->uri->segment(4));
            $pagination = paginacion('index.php/ActivoFijo/Memoria/index/', $this->Memoria_model->totalMemorias(),
                          $num, '4');
        }
      } else {
            $registros = $this->Memoria_model->obtenerMemoriasLimit($num, $this->uri->segment(4));
            $pagination = paginacion('index.php/ActivoFijo/Memoria/index/', $this->Memoria_model->totalMemorias(),
                          $num, '4');
      }

      /*
      * llena la tabla con los datos consultados
      */

      if (!($registros == FALSE)) {
        $i=1;
        foreach($registros as $mem) {
            $onClick = "llenarFormulario('Memoria', ['id','tipo_memoria'],[$mem->id_memoria, '$mem->tipo_memoria'])";
            $this->table->add_row($i,$mem->id_memoria, $mem->tipo_memoria,
                            '<a class="icono icon-actualizar" onClick="'.$onClick.'"></a>',
                            '<a class="icono icon-eliminar" uri='.base_url('index.php/ActivoFijo/Memoria/EliminarDato/'.$mem->id_memoria).'></a>');
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
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Memoria');
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $data = array(
          'tipo_memoria' => $this->input->post('tipo_memoria')
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
          $this->Memoria_model->actualizarMemoria($this->input->post('id'),$data);
          redirect('/ActivoFijo/Memoria/index/update');
        } else {
          redirect('/ActivoFijo/Memoria/index/forbidden');
        }
      }

      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'insert')) {
        $rastrea['operacion']='INSERTA';
        $rastrea['id_registro']=$this->User_model->obtenerSiguienteIdModuloIncrement('sic_memoria');
        $this->User_model->insertarRastreabilidad($rastrea);
        $this->Memoria_model->insertarMemoria($data);
        redirect('/ActivoFijo/Memoria/index/new');
      } else {
        redirect('/ActivoFijo/Memoria/index/forbidden');
      }
    } else {
      redirect('Login/index/error_no_autenticado');
    }
  }

  /*
  * elimina un registro cuando se le pasa por la url el id
  */
  public function EliminarDato(){
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Memoria');
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
        $this->Memoria_model->eliminarMemoria($id);
        $this->User_model->insertarRastreabilidad($rastrea);
        redirect('/ActivoFijo/Memoria/index/delete');
      } else {
        redirect('/ActivoFijo/Memoria/index/forbidden');
      }
    } else {
      redirect('Login/index/error_no_autenticado');
    }
  }
}
?>
