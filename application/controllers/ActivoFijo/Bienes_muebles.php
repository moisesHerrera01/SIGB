<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bienes_muebles extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('url', 'form', 'paginacion'));
    $this->load->library('table');
    $this->load->model(array('ActivoFijo/Bienes_Muebles_Model','ActivoFijo/Bienes_inmuebles_model',
                            'ActivoFijo/Datos_Comunes_Model','ActivoFijo/Condicion_bien_model'));
  }

  public function index(){

    $data['title'] = "Bienes Muebles";
    //$data['js'] = "assets/js/validate/um.js";
    $msg = array('alert' => $this->uri->segment(4),'controller'=>'bienes_muebles');

		$data['body'] = $this->load->view('mensajes', $msg, TRUE) . $this->load->view('ActivoFijo/bienes_muebles_view','', TRUE) .
                    "<br><div class='content_table table-responsive'>" .
                    "<div class='limit-content-title'><span class='icono icon-table icon-title'> Bienes Muebles</span></div>".
                    "<div class='limit-content'>" . $this->mostrarTabla() . "</div></div>";
    $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
    $this->load->view('base', $data);
    //$this->load->view('menu_bodega_view');
	}

  public function mostrarTabla(){
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Bienes_muebles');
    /*
    * Configuracion de la tabla
    */
    $USER = $this->session->userdata('logged_in');
    if($USER){
      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'update')) {

        $template = array(
            'table_open' => '<table class="table table-striped table-bordered">'
        );
        $this->table->set_template($template);
        $this->table->set_heading('Id Bien','Descripción', 'Código','Código Anterior','Marca','Modelo',
        'Serie','Precio Unitario','Oficina','Empleado','Editar','Eliminar');

        /*
        * Filtro a la BD
        */

        /*Obtiene el numero de registros a mostrar por pagina */
        $num = '8';
        $pagination = '';
        $registros;
        if ($this->input->is_ajax_request()) {
          if (!($this->input->post('busca') == "")) {
              $registros = $this->Bienes_Muebles_Model->buscarBienes_muebles($this->input->post('busca'));
          } else {
              $registros = $this->Bienes_Muebles_Model->obtenerBienes_mueblesLimit($num, $this->uri->segment(4));
              $pagination = paginacion('index.php/ActivoFijo/Bienes_muebles/index/', $this->Bienes_Muebles_Model->totalBienes_muebles()->total,
                            $num, '4');
          }
        } else {
              $registros = $this->Bienes_Muebles_Model->obtenerBienes_mueblesLimit($num, $this->uri->segment(4));
              $pagination = paginacion('index.php/ActivoFijo/Bienes_muebles/index/', $this->Bienes_Muebles_Model->totalBienes_muebles()->total,
                            $num, '4');
        }

        /*
        * llena la tabla con los datos consultados
        */

        if (!($registros == FALSE)) {
          foreach($registros as $bien) {
              $onClick = "llenarFormulario('bienes', ['id','dato_comun', 'autocomplete1','codigo_anterior','serie',
              'numero_motor','numero_placa','matricula','observacion','oficina','autocomplete3',
            'empleado','autocomplete4'],
              [$bien->id_bien,'$bien->id_dato_comun', '$bien->descripcion','$bien->codigo_anterior','$bien->serie','$bien->numero_motor',
              '$bien->numero_placa','$bien->matricula','$bien->observacion','$bien->id_oficina','$bien->nombre_oficina','$bien->id_empleado',
              '$bien->primer_nombre $bien->primer_apellido'])";

              $this->table->add_row($bien->id_bien,$bien->descripcion, $bien->codigo,$bien->codigo_anterior,
              $bien->nombre_marca,$bien->modelo,$bien->serie,$bien->precio_unitario,$bien->nombre_oficina,
              $bien->primer_nombre.' '.$bien->primer_apellido,'<a class="icono icon-actualizar" onClick="'.$onClick.'"></a>',
              '<a class="icono icon-eliminar" uri='.base_url('index.php/ActivoFijo/Bienes_muebles/EliminarDato/'.$bien->id_bien).'></a>');
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
        redirect('/ActivoFijo/Bienes_muebles/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  /*
  * Actualiza o Registra al sistema
  */
  public function RecibirDatos(){
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Bienes_muebles');
    $USER = $this->session->userdata('logged_in');
    $id_dato_comun=$this->input->post('dato_comun');
    $cor=$this->Bienes_inmuebles_model->obtenerCorrelativo();
    $cat=$this->Bienes_inmuebles_model->calcularCodigo($id_dato_comun);
    $codigo=$cat->numero_categoria.'.'.sprintf("%'06d", $cor).'.'.$cat->numero_subcategoria;
    $id_condicion_bien=$this->Condicion_bien_model->obtenercondicionId('EN USO');
    if($USER){
      $data = array(
          'id_dato_comun' => $this->input->post('dato_comun'),
          'codigo_anterior' => $this->input->post('codigo_anterior'),
          'serie' => $this->input->post('serie'),
          'numero_motor' => $this->input->post('numero_motor'),
          'numero_placa' => $this->input->post('numero_placa'),
          'matricula' => $this->input->post('matricula'),
          'numero_motor' => $this->input->post('numero_motor'),
          'id_condicion_bien' => $id_condicion_bien,
          'numero_motor' => $this->input->post('numero_motor'),
          'observacion' => $this->input->post('observacion'),
          'id_oficina' => $this->input->post('oficina'),
          'id_empleado' => $this->input->post('empleado'),
          'correlativo' => $cor,
          'codigo' => $codigo
      );

      $data2 = array(
          'id_dato_comun' => $this->input->post('dato_comun'),
          'codigo_anterior' => $this->input->post('codigo_anterior'),
          'serie' => $this->input->post('serie'),
          'numero_motor' => $this->input->post('numero_motor'),
          'numero_placa' => $this->input->post('numero_placa'),
          'matricula' => $this->input->post('matricula'),
          'numero_motor' => $this->input->post('numero_motor'),
          'id_condicion_bien' => $id_condicion_bien,
          'numero_motor' => $this->input->post('numero_motor'),
          'observacion' => $this->input->post('observacion'),
          'id_oficina' => $this->input->post('oficina'),
          'id_empleado' => $this->input->post('empleado')
      );

      if (!($this->input->post('id') == '')){
        if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'update')) {
          $this->Bienes_Muebles_Model->actualizarBien_mueble($this->input->post('id'),$data2);
          redirect('/ActivoFijo/Bienes_muebles/index/update');
        } else {
          redirect('/ActivoFijo/Bienes_muebles/index/forbidden');
        }
      }

      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'insert')) {
        $this->Bienes_Muebles_Model->insertarBienes_muebles($data);
        redirect('/ActivoFijo/Bienes_muebles/index/new');
      } else {
        redirect('/ActivoFijo/Bienes_muebles/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  /*
  * elimina un registro cuando se le pasa por la url el id
  */
  public function EliminarDato(){
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Bienes_muebles');
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $id = $this->uri->segment(4);
      if ($this->Bienes_Muebles_Model->contieneDetalleMovimiento($id)->asociados>0){
        if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'delete')) {
          redirect('/ActivoFijo/Bienes_muebles/index/no_delete');
        } else {
          redirect('/ActivoFijo/Bienes_muebles/index/forbidden');
        }
      }
      else {
        if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'delete')) {
          $this->Bienes_Muebles_Model->eliminarBien_mueble($id);
          redirect('/ActivoFijo/Bienes_muebles/index/delete');
        } else {
          redirect('/ActivoFijo/Bienes_muebles/index/forbidden');
        }
      }
      }else {
        redirect('login/index/error_no_autenticado');
      }
  }

  public function AutocompleteOficina(){
    $registros = '';
    if ($this->input->is_ajax_request()) {
      if (!($this->input->post('autocomplete') == "")) {
          $registros = $this->Bienes_Muebles_Model->buscarOficinas($this->input->post('autocomplete'));
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
        $ofi->id_oficina.'" data="'.$ofi->id_oficina.'"  data1="'.$ofi->nombre_oficina.','.$ofi->nombre_seccion.','.$ofi->nombre_almacen.'" >'
        .$ofi->nombre_oficina.','.$ofi->nombre_seccion.','.$ofi->nombre_almacen.'</a></div>';
        $i++;
      }
    }
  }

  public function AutocompleteEmpleado(){
    $registros = '';
    if ($this->input->is_ajax_request()) {
      if (!($this->input->post('autocomplete') == "")) {
          $registros = $this->Bienes_Muebles_Model->buscarEmpleados($this->input->post('autocomplete'));
      } else {
          $registros = $this->Bienes_Muebles_Model->obtenerEmpleados();
      }
    } else {
          $registros = $this->Bienes_Muebles_Model->obtenerEmpleados();
    }

    if ($registros == '') {
      echo '<div id="1" class="suggest-element"><a id="conteo">No se encontraron coincidencias</a></div>';
    } else {
      $i = 1;
      foreach ($registros as $emp) {
        echo '<div id="'.$i.'" class="suggest-element" ida="empleado'.$emp->id_empleado.'"><a id="empleado'.
        $emp->id_empleado.'" data="'.$emp->id_empleado.'"  data1="'.$emp->nombre_completo.'" >'
        .$emp->nombre_completo.'</a></div>';
        $i++;
      }
    }
  }
}
?>
