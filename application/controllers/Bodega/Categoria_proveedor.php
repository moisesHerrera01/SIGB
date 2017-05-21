<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Categoria_proveedor extends CI_Controller {

  public function __construct() {
    parent::__construct();

    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }

    $this->load->helper(array('form', 'paginacion'));
    $this->load->library(array('table'));
    $this->load->model('Bodega/Categoria_proveedor_model');
  }

  public function index(){
    $data['title'] = "Categoria Proveedor";
    //$data['js'] = "assets/js/validate/um.js";

    $msg = array('alert' => $this->uri->segment(4), );

		$data['body'] = $this->load->view('mensajes', $msg, TRUE) . $this->load->view('Bodega/Categoria_proveedor_view', '', TRUE) .
                    "<br><div class='content_table'>" .
                    "<div class='limit-content-title'><span class='icono icon-table icon-title'> Categoria Proveedor</span></div>".
                    "<div class='limit-content'>" . $this->mostrarTabla() . "</div></div>";
    $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
    $this->load->view('base', $data);
	}

  public function mostrarTabla(){
    $modulo=$this->User_model->obtenerModulo('Bodega/Categoria_proveedor');
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
        $this->table->set_heading('#','Nombre', 'Tipo de Servicio','Tipo de empresa','Modificar', 'Eliminar');

        /*
        * Filtro a la BD
        */

        /*Obtiene el numero de registros a mostrar por pagina */
        $num = '10';
        $pagination = '';
        $registros;
        if ($this->input->is_ajax_request()) {
          if (!($this->input->post('busca') == "")) {
              $registros = $this->Categoria_proveedor_model->buscarCategorias($this->input->post('busca'));
          } else {
              $registros = $this->Categoria_proveedor_model->obtenerCategoriasLimit($num, $this->uri->segment(4));
              $pagination = paginacion('index.php/Bodega/Categoria_proveedor/index/', $this->Categoria_proveedor_model->totalCategorias(),
                            $num, '4');
          }
        } else {
              $registros = $this->Categoria_proveedor_model->obtenerCategoriasLimit($num, $this->uri->segment(4));
              $pagination = paginacion('index.php/Bodega/Categoria_proveedor/index/', $this->Categoria_proveedor_model->totalCategorias(),
                            $num, '4');
        }

        /*
        * llena la tabla con los datos consultados
        */

        if (!($registros == FALSE)) {
          foreach($registros as $cat) {
              $onClick = "llenarFormulario('Categoria_proveedor', ['id','nombre'],[$cat->id_categoria_proveedor, '$cat->nombre_categoria'],
              ['rubro_empresa','tipo_empresa'],['$cat->rubro','$cat->tipo_empresa'])";

              $this->table->add_row($cat->id_categoria_proveedor, $cat->nombre_categoria, $cat->rubro,$cat->tipo_empresa,
                              '<a class="icono icon-actualizar" onClick="'.$onClick.'"></a>',
                              '<a class="icono icon-eliminar" uri='.base_url('index.php/Bodega/Categoria_proveedor/EliminarDato/'.$cat->id_categoria_proveedor).'></a>');
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
        redirect('/Bodega/Categoria_proveedor/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  /*
  * Actualiza o Registra al sistema
  */
  public function RecibirDatos(){
    $modulo=$this->User_model->obtenerModulo('Bodega/Categoria_proveedor');
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $data = array(
          'nombre_categoria' => $this->input->post('nombre'),
          'rubro' => $this->input->post('rubro_empresa'),
          'tipo_empresa' => $this->input->post('tipo_empresa')
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
          $this->Categoria_proveedor_model->actualizarCategoria($this->input->post('id'),$data);
          $rastrea['operacion']='ACTUALIZA';
          $rastrea['id_registro']=$this->input->post('id');
          $this->User_model->insertarRastreabilidad($rastrea);
          redirect('/Bodega/Categoria_proveedor/index/update');
        } else {
          redirect('/Bodega/Categoria_proveedor/index/forbidden');
        }
      }
      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'insert')) {
        $this->Categoria_proveedor_model->insertarCategoria($data);
        $rastrea['operacion']='INSERTA';
        $rastrea['id_registro']=$this->User_model->obtenerSiguienteIdModuloIncrement('sic_categoria_proveedor')-1;
        $this->User_model->insertarRastreabilidad($rastrea);
        redirect('/Bodega/Categoria_proveedor/index/new');
      } else {
        redirect('/Bodega/Categoria_proveedor/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  /*
  * elimina un registro cuando se le pasa por la url el id
  */
  public function EliminarDato(){
    $modulo=$this->User_model->obtenerModulo('Bodega/Categoria_proveedor');
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
        $this->Categoria_proveedor_model->eliminarCategoria($id);
        $this->User_model->insertarRastreabilidad($rastrea);
        redirect('/Bodega/Categoria_proveedor/index/delete');
      } else {
        redirect('/Bodega/Categoria_proveedor/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  public function Autocomplete(){
    $registros = '';
    if ($this->input->is_ajax_request()) {
      if (!($this->input->post('autocomplete') == "")) {
          $registros = $this->Categoria_proveedor_model->buscarCategorias($this->input->post('autocomplete'));
      } else {
          $registros = $this->Categoria_proveedor_model->obtenerCategorias();
      }
    } else {
          $registros = $this->Categoria_proveedor_model->obtenerCategorias();
    }

    if ($registros == '') {
      echo '<div id="1" class="suggest-element"><a id="conteo">No se encontraron coincidencias</a></div>';
    }else {
      $i = 1;
      foreach ($registros as $cat) {
        echo '<div id="'.$i.'" class="suggest-element" ida="producto'.$cat->id_categoria_proveedor.'"><a id="producto'.
        $cat->id_categoria_proveedor.'" data="'.$cat->id_categoria_proveedor.'"  data1="'.$cat->nombre_categoria.' - '.$cat->rubro.' - '.$cat->tipo_empresa.'" >'
        .$cat->nombre_categoria.' - '.$cat->rubro.' - '.$cat->tipo_empresa.'</a></div>';
        $i++;
      }
    }
  }
}//esto es un comentario
?>
