<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class FuenteFondos extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('form', 'paginacion'));
    $this->load->library('table');
    $this->load->model('Bodega/Fuentefondos_model');
  }

  public function index(){
    $data['title'] = "Fuentes de Fondo";
    $data['js'] = "assets/js/validate/fdf.js";

    $msg = array('alert' => $this->uri->segment(4), );

		$data['body'] = $this->load->view('mensajes', $msg, TRUE) . $this->load->view('Bodega/fuenteFondos_view',$msg,TRUE) .
                    "<br><div class='content_table '>" .
                    "<div class='limit-content-title'><span class='icono icon-table icon-title'> Fuente de Fondos</span></div>".
                    "<div class='limit-content'>"  . $this->mostrarTabla() . "</div>";
    $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
    $this->load->view('base', $data);
	}

  public function mostrarTabla(){
    $modulo=$this->User_model->obtenerModulo('Bodega/FuenteFondos');
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
        $this->table->set_heading('#','Nombre', 'Código','Descripción','Modificar', 'Eliminar');

        /*
        * Filtro a la BD
        */
        /*Obtiene el numero de registros a mostrar por pagina */
        $num = '10';
        $pagination = '';
        $registros;
        if ($this->input->is_ajax_request()) {
          if (!($this->input->post('busca') == "")) {
              $registros = $this->Fuentefondos_model->buscarFuentes($this->input->post('busca'));
          } else {
              $registros = $this->Fuentefondos_model->obtenerFuentesLimit($num, $this->uri->segment(4));
              $pagination = paginacion('index.php/Bodega/fuenteFondos/index/', $this->Fuentefondos_model->totalFuentes(),
                            $num, '4');
          }
        } else {
            $registros = $this->Fuentefondos_model->obtenerFuentesLimit($num, $this->uri->segment(4));
            $pagination = paginacion('index.php/Bodega/fuenteFondos/index/', $this->Fuentefondos_model->totalFuentes(),
                          $num, '4');
        }

        /*
        * llena la tabla con los datos consultados
        */

        if (!($registros == FALSE)) {
          foreach($registros as $fdf) {
              $onClick = "llenarFormulario('fuentesdefondo', ['id', 'nombreFuente', 'codigo','descripcion'], [$fdf->id_fuentes, '$fdf->nombre_fuente' , '$fdf->codigo','$fdf->descripcion'])";
              $this->table->add_row($fdf->id_fuentes, $fdf->nombre_fuente, $fdf->codigo, $fdf->descripcion,
                              //form_button($btn_act), $form_el,
                              '<a class="icono icon-actualizar" onClick="'.$onClick.'"></a>',
                              '<a class="icono icon-eliminar" uri='.base_url('index.php/Bodega/fuenteFondos/EliminarDato/'.$fdf->id_fuentes).'></a>');
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
        redirect('/Bodega/fuenteFondos/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  /*
  * Actualiza o Registra al sistema
  */

  public function RecibirDatos(){
    $USER = $this->session->userdata('logged_in');
    $modulo=$this->User_model->obtenerModulo('Bodega/FuenteFondos');
    if($USER){
      $data = array(
          'nombre_fuente' => $this->input->post('nombreFuente'),
          'codigo' => $this->input->post('codigo'),
          'descripcion' => $this->input->post('descripcion'),
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
          $this->Fuentefondos_model->actualizarFuente($this->input->post('id'),$data);
          $rastrea['operacion']='ACTUALIZA';
          $rastrea['id_registro']=$this->input->post('id');
          $this->User_model->insertarRastreabilidad($rastrea);
          redirect('/Bodega/fuenteFondos/index/update');
        } else {
          redirect('/Bodega/fuenteFondos/index/forbidden');
        }
      }

      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'insert')) {
        $rastrea['operacion']='INSERTA';
        $rastrea['id_registro']=$this->User_model->obtenerSiguienteIdModuloIncrement('sic_fuentes_fondo');
        $this->User_model->insertarRastreabilidad($rastrea);
        $this->Fuentefondos_model->insertarFuente($data);
        redirect('/Bodega/fuenteFondos/index/new');
      } else {
        redirect('/Bodega/fuenteFondos/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  /*
  * elimina un registro cuando se le pasa por la url el id
  */

  public function EliminarDato(){
    $modulo=$this->User_model->obtenerModulo('Bodega/FuenteFondos');
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
        $this->Fuentefondos_model->eliminarFuente($id);
        $this->User_model->insertarRastreabilidad($rastrea);
        redirect('/Bodega/fuenteFondos/index/delete');
      } else {
        redirect('/Bodega/fuenteFondos/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  public function Autocomplete(){
    $registros = '';
    if ($this->input->is_ajax_request()) {
      if (!($this->input->post('autocomplete') == "")) {
          $registros = $this->Fuentefondos_model->buscarFuentes($this->input->post('autocomplete'));
      } else {
          $registros = $this->Fuentefondos_model->obtenerFuentes();
      }
    } else {
          $registros = $this->Fuentefondos_model->obtenerFuentes();
    }

    if ($registros == '') {
      echo '<div id="1" class="suggest-element"><a id="conteo">No se encontraron coincidencias</a></div>';
    }else {
      $i = 1;
      foreach ($registros as $fuente) {
        echo '<div id="'.$i.'" class="suggest-element" ida="fuente'.$fuente->id_fuentes.'"><a id="fuente'.
        $fuente->id_fuentes.'" data="'.$fuente->id_fuentes.'"  data1="'.$fuente->nombre_fuente.'" >'
        .$fuente->nombre_fuente.'</a></div>';
        $i++;
      }
    }
  }
}
?>
