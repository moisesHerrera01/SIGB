<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Especificos extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('form', 'paginacion'));
    $this->load->library('table');
    $this->load->model(array('Bodega/Especifico','Bodega/DetalleProducto_model'));
  }

  public function index(){

    $data['title'] = "Objeto Especifico";
    $data['js'] = "assets/js/validate/oe.js";
    $msg = array('alert' => $this->uri->segment(4),'controller'=>'especifico');

		$data['body'] = $this->load->view('mensajes', $msg, TRUE) . $this->load->view('Bodega/Especifico',$msg,TRUE) .
                    "<br><div class='content_table '>" .
                    "<div class='limit-content-title'><span class='icono icon-table icon-title'> Especificos</span></div>".
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
      $this->table->set_heading('#','Nombre','Proceso','Detalle Producto','Modificar', 'Eliminar');

      /*
      * Filtro a la BD
      */

      /*Obtiene el numero de registros a mostrar por pagina */
      $num = '10';
      $pagination = '';
      $registros;
      if ($this->input->is_ajax_request()) {
        if (!($this->input->post('busca') == "")) {
            $registros = $this->Especifico->buscarEspecificos($this->input->post('busca'));
        } else {
            $registros = $this->Especifico->obtenerEspecificosLimit($num, $this->uri->segment(4));
            $pagination = paginacion('index.php/Bodega/Especificos/index/', $this->Especifico->totalEspecificos(),
                          $num, '4');
        }
      } else {
            $registros = $this->Especifico->obtenerEspecificosLimit($num, $this->uri->segment(4));
            $pagination = paginacion('index.php/Bodega/Especificos/index/', $this->Especifico->totalEspecificos(),
                          $num, '4');
      }

      /*
      * llena la tabla con los datos consultados
      */

      if (!($registros == FALSE)) {
        foreach($registros as $espe) {
            $onClick = "llenarFormulario('Especifico', ['id_especifico', 'nombre'], [$espe->id_especifico, '$espe->nombre_especifico'],['proces'],['$espe->proceso'])";

            $this->table->add_row($espe->id_especifico, $espe->nombre_especifico,$espe->proceso,
                            //form_button($btn_act), $form_el,
                            '<a class="icono icon-detalle" href="'.base_url('index.php/Bodega/Detalleproductos/index/'.$espe->id_especifico.'/').'"></a>',
                            '<a class="icono icon-actualizar" onClick="'.$onClick.'"></a>',
                            '<a class="icono icon-eliminar" uri='.base_url('index.php/Bodega/Especificos/EliminarDato/'.$espe->id_especifico).'></a>');
        }
      } else {
        $msg = array('data' => "Texto no encontrado", 'colspan' => "6");
        $this->table->add_row($msg);
      }

      /*
      * vuelve a verificar para mostrar los datos
      */
      if ($this->input->is_ajax_request()) {
        echo  "<div class='table-responsive'>" . $this->table->generate() . "</div>" . $pagination;
      } else {
        return "<div class='table-responsive'>" . $this->table->generate() . "</div>" . $pagination;
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  /*
  * Actualiza o Registra al sistema
  */
  public function RecibirDatos(){
    $modulo=$this->User_model->obtenerModulo('Bodega/Especificos');
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $data = array(
          'id_especifico' => $this->input->post('id_especifico'),
          'nombre_especifico' => $this->input->post('nombre'),
          'proceso' => $this->input->post('proces')
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
      if (!($this->Especifico->obtenerEspecifico($data['id_especifico']) == '')){
        if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'update')) {
          $rastrea['operacion']='ACTUALIZA';
          $rastrea['id_registro']=$this->input->post('id_especifico');
          $this->User_model->insertarRastreabilidad($rastrea);
          $this->Especifico->actualizarEspecifico($this->input->post('id_especifico'),$data);
          redirect('/Bodega/Especificos/index/update');
        } else {
          redirect('/Bodega/UnidadMedidas/index/forbidden');
        }
      }
      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'insert')) {
        $this->Especifico->insertarEspecifico($data);
        $rastrea['operacion']='INSERTA';
        $rastrea['id_registro']=$data['id_especifico'];
        $this->User_model->insertarRastreabilidad($rastrea);
        redirect('/Bodega/Especificos/index/new');
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
    $modulo=$this->User_model->obtenerModulo('Bodega/Especificos');
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
        if ($this->DetalleProducto_model->existeEspecifico($id)){
          redirect('/Bodega/Especificos/index/existe');
        }
        else {
          $this->Especifico->eliminarEspecifico($id);
          $this->User_model->insertarRastreabilidad($rastrea);
          redirect('/Bodega/Especificos/index/delete');
        }
      } else {
        redirect('/Bodega/UnidadMedidas/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  public function Autocomplete(){
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $registros = '';
      if ($this->input->is_ajax_request()) {
        if (!($this->input->post('autocomplete') == "")) {
            $registros = $this->Especifico->buscarEspecificos($this->input->post('autocomplete'));
        } else {
            $registros = $this->Especifico->obtenerEspecificos();
        }
      } else {
            $registros = $this->Especifico->obtenerEspecificos();
      }

      if ($registros == '') {
        echo '<div id="1" class="suggest-element"><a id="conteo">No se encontraron coincidencias</a></div>';
      } else {
        $i = 1;
        foreach ($registros as $esp) {
          echo '<div id="'.$i.'" class="suggest-element" ida="especifico'.$esp->id_especifico.'"><a id="especifico'.
          $esp->id_especifico.'" data="'.$esp->id_especifico.'"  data1="'.$esp->nombre_especifico.'" >'
          .$esp->id_especifico.' - '.$esp->nombre_especifico.'</a></div>';
          $i++;
        }
      }
    } else {
      redirect('login');
    }
  }

  public function AutocompletePorProducto(){
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $registros = '';
      if ($this->input->is_ajax_request()) {
        if (!($this->input->post('autocomplete') == "")) {
            $registros = $this->Especifico->buscarEspecificosProducto($this->uri->segment(4), $this->input->post('autocomplete'));
        } else {
            $registros = $this->Especifico->obtenerEspecificosProducto($this->uri->segment(4));
        }
      } else {
            $registros = $this->Especifico->obtenerEspecificosProducto($this->uri->segment(4));
      }

      if ($registros == '') {
        echo '<div id="1" class="suggest-element"><a id="conteo">No se encontraron coincidencias</a></div>';
      } else {
        foreach ($registros as $esp) {
          echo '<div id="'.$i.'" class="suggest-element" ida="especifico'.$esp->id_especifico.'"><a id="especifico'.
          $esp->id_especifico.'" data="'.$esp->id_especifico.'"  data1="'.$esp->nombre_especifico.'" >'
          .$esp->id_especifico.' - '.$esp->nombre_especifico.'</a></div>';
        }
      }
    } else {
      redirect('login');
    }
  }
}
?>
