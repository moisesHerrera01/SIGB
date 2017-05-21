<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Seleccion_Subcategoria extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('url', 'form', 'paginacion'));
    $this->load->library('table');
    $this->load->model(array('ActivoFijo/Equipo_informatico_Model','ActivoFijo/Categoria_model',
    'ActivoFijo/Subcategoria_Model','ActivoFijo/Seleccion_subcategoria_model'));
  }

  public function index(){
    $id = $this->uri->segment(4);
    $data['title'] = "Equipo Informático";
    //$data['js'] = "assets/js/validate/subcategoria.js";
    $msg = array('alert' => $this->uri->segment(4),'controller'=>'Seleccion_Subcategoria');
		$data['body'] = $this->load->view('mensajes', $msg, TRUE) . $this->load->view('ActivoFijo/seleccion_subcategoria_view',$msg, TRUE) .
                    "<br><div class='content_table table-responsive'>" .
                    "<div class='limit-content-title'><span class='icono icon-table icon-title'> Equipos Informáticos</span></div>".
                    "<div class='limit-content'>" . $this->mostrarTabla() . "</div></div>";
    $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
    $this->load->view('base', $data);
	}

  public function mostrarTabla(){
    /*
    * Configuracion de la tabla
    */
    $USER = $this->session->userdata('logged_in');
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Seleccion_Subcategoria');
    if($USER){
      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'select')) {
        $template = array(
            'table_open' => '<table class="table table-striped table-bordered">'
        );
        $this->table->set_template($template);
        $this->table->set_heading('#','Id','Tipo','Procesador','Disco duro','Memoria','Sistema','Office','Dirección ip','Punto','Editar','Eliminar');

        /*
        * Filtro a la BD
        */

        /*Obtiene el numero de registros a mostrar por pagina */
        $num = '10';
        $pagination = '';
        $registros;
        if ($this->input->is_ajax_request()) {
          if (!($this->input->post('busca') == "")) {
              $registros = $this->Seleccion_subcategoria_model->buscarEquipo($this->input->post('busca'));
          } else {
              $registros = $this->Seleccion_subcategoria_model->obtenerEquipoLimit($num, $this->uri->segment(5));
              $pagination = paginacion('index.php/ActivoFijo/Seleccion_Subcategoria/index/'.
              $this->uri->segment(4), $this->Seleccion_subcategoria_model->totalEquipo()->total,
              $num, '5');
          }
        } else {
              $registros = $this->Seleccion_subcategoria_model->obtenerEquipoLimit($num,$this->uri->segment(5));
              $pagination = paginacion('index.php/ActivoFijo/Seleccion_Subcategoria/index/',
              $this->Seleccion_subcategoria_model->totalEquipo()->total,
                            $num, '5');
        }
        /*
        * llena la tabla con los datos consultados
        */
        $i=1;
        if (!($registros == FALSE)) {

          foreach($registros as $sub) {
                  $onClick = "llenarFormulario('equipo', ['id','bien','autocomplete'],
                  [$sub->id_equipo_informatico,'$sub->id_bien','$sub->descripcion'], ['tipo_computadora'],
                  ['$sub->tipo_computadora'])";
              $this->table->add_row($i,$sub->id_equipo_informatico,$sub->tipo_computadora,
              '<a class="icono icon-detalle" href="'.base_url('index.php/ActivoFijo/Equipo_informatico/index/'.$sub->id_equipo_informatico.'/'.$this->Seleccion_subcategoria_model->obtenerIdSubcategoria('PROCESADOR').'/').'"></a>',
              '<a class="icono icon-detalle" href="'.base_url('index.php/ActivoFijo/Equipo_informatico/index/'.$sub->id_equipo_informatico.'/'.$this->Seleccion_subcategoria_model->obtenerIdSubcategoria('DISCO DURO').'/').'"></a>',
              '<a class="icono icon-detalle" href="'.base_url('index.php/ActivoFijo/Equipo_informatico/index/'.$sub->id_equipo_informatico.'/'.$this->Seleccion_subcategoria_model->obtenerIdSubcategoria('MEMORIA').'/').'"></a>',
              '<a class="icono icon-detalle" href="'.base_url('index.php/ActivoFijo/Equipo_informatico/index/'.$sub->id_equipo_informatico.'/'.$this->Seleccion_subcategoria_model->obtenerIdSubcategoria('SISTEMA OPERATIVO').'/').'"></a>',
              '<a class="icono icon-detalle" href="'.base_url('index.php/ActivoFijo/Equipo_informatico/index/'.$sub->id_equipo_informatico.'/'.$this->Seleccion_subcategoria_model->obtenerIdSubcategoria('OFFICE').'/').'"></a>',
              '<a class="icono icon-detalle" href="'.base_url('index.php/ActivoFijo/Equipo_informatico/index/'.$sub->id_equipo_informatico.'/'.$this->Seleccion_subcategoria_model->obtenerIdSubcategoria('IP').'/').'"></a>',
              '<a class="icono icon-detalle" href="'.base_url('index.php/ActivoFijo/Equipo_informatico/index/'.$sub->id_equipo_informatico.'/'.$this->Seleccion_subcategoria_model->obtenerIdSubcategoria('PUNTO DE RED').'/').'"></a>',
              '<a class="icono icon-actualizar" onClick="'.$onClick.'"></a>',
              '<a class="icono icon-eliminar" uri='.base_url('index.php/ActivoFijo/Seleccion_Subcategoria/EliminarDato/'.$sub->id_equipo_informatico).'></a>');
              $i++;
          }
        } else {
          $msg = array('data' => "Texto no encontrado", 'colspan' => "12");
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
        redirect('/ActivoFijo/Seleccion_Subcategoria/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  /*
  * Actualiza o Registra al sistema
  */
  public function RecibirDatos(){
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Seleccion_Subcategoria');
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $data = array(
          'id_bien' => $this->input->post('bien'),
          'tipo_computadora' => $this->input->post('tipo_computadora')
      );

      if (!($this->input->post('id') == '')){
        if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'update')) {
          $this->Seleccion_subcategoria_model->actualizarEquipo($this->input->post('id'),$data);
          redirect('/ActivoFijo/Seleccion_Subcategoria/index/update');
        } else {
          redirect('/ActivoFijo/Seleccion_Subcategoria/index/forbidden');
        }
      }
      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'insert')) {
        $this->Seleccion_subcategoria_model->insertarEquipo($data);
        redirect('/ActivoFijo/Seleccion_Subcategoria/index/new');
      } else {
        redirect('/ActivoFijo/Seleccion_Subcategoria/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  /*
  * elimina un registro cuando se le pasa por la url el id
  */
  public function EliminarDato(){
      $modulo=$this->User_model->obtenerModulo('ActivoFijo/Seleccion_Subcategoria');
    $USER = $this->session->userdata('logged_in');
    if($USER){
      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'delete')) {
        $id = $this->uri->segment(4);
        if ($this->Seleccion_subcategoria_model->validarDetalleEquipo($id)) {
          redirect('/ActivoFijo/Seleccion_Subcategoria/index/existeequipo');
        }else {
          $this->Seleccion_subcategoria_model->eliminarEquipo($id);
          redirect('/ActivoFijo/Seleccion_Subcategoria/index/delete');
        }
      } else {
        redirect('/ActivoFijo/Seleccion_Subcategoria/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }
}
?>
