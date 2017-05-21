<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bienes_Inmuebles extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('url', 'form', 'paginacion'));
    $this->load->library('table');
    $this->load->model(array('ActivoFijo/Datos_Comunes_Model','ActivoFijo/Condicion_bien_model','ActivoFijo/Bienes_inmuebles_model',
    'ActivoFijo/Condicion_bien_model','ActivoFijo/Bienes_Muebles_Model'));
  }

  public function index(){

    $data['title'] = "Inmuebles";
    $data['js'] = "assets/js/validate/bienes_inmuebles.js";
    $msg = array('alert' => $this->uri->segment(4),'controller'=>'bienes_inmuebles' );

    $data['body'] = $this->load->view('mensajes', $msg, TRUE) . $this->load->view('ActivoFijo/bienes_inmuebles_view', '', TRUE) .
                    "<br><div class='content_table table-responsive'>" .
                    "<div class='limit-content-title'><span class='icono icon-table icon-title'>Bienes Inmuebles</span></div>".
                    "<div class='limit-content'>" . $this->mostrarTabla() . "</div></div>";
    $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
    $this->load->view('base', $data);
	}

  public function mostrarTabla(){
    /*
    * Configuracion de la tabla
    */
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Bienes_Inmuebles');
    $USER = $this->session->userdata('logged_in');
    if($USER){
      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'update')) {
        $template = array(
            'table_open' => '<table class="table table-striped table-bordered">'
        );
        $this->table->set_template($template);
        $this->table->set_heading('Id','Descripción','Código','Código anterior','Tipo_Inmueble','Extensión','Zona','Dirección','Editar','Eliminar');

        /*
        * Filtro a la BD
        */

        /*Obtiene el numero de registros a mostrar por pagina */
        $num = '10';
        $pagination = '';
        $registros;
        if ($this->input->is_ajax_request()) {
          if (!($this->input->post('busca') == "")) {
              $registros = $this->Bienes_inmuebles_model->buscarBienesInmuebles($this->input->post('busca'));
          } else {
              $registros = $this->Bienes_inmuebles_model->obtenerBienesInmueblesLimit($num, $this->uri->segment(4));
              $pagination = paginacion('index.php/ActivoFijo/Bienes_Inmuebles/index/', $this->Bienes_inmuebles_model->totalBienesInmuebles()->total,
                            $num, '4');
          }
        } else {
              $registros = $this->Bienes_inmuebles_model->obtenerBienesInmueblesLimit($num, $this->uri->segment(4));
              $pagination = paginacion('index.php/ActivoFijo/Bienes_Inmuebles/index/', $this->Bienes_inmuebles_model->totalBienesInmuebles()->total,
                            $num, '4');
        }

        /*
        * llena la tabla con los datos consultados
        */

        if (!($registros == FALSE)) {
          foreach($registros as $dat) {
              if($dat->terreno_zona!=NULL && $dat->tipo_inmueble!=NULL){
                $onClick = "llenarFormulario('Bienes_Inmuebles', ['id', 'dato_comun','autocomplete1','codigo_anterior','extension','matricula',
                'direccion','condicion_bien','autocomplete2','fines','precio','observacion'],
                [$dat->id_bien, '$dat->id_dato_comun','$dat->descripcion','$dat->codigo_anterior','$dat->terreno_extension',
                '$dat->matricula','$dat->terreno_direccion','$dat->id_condicion_bien','$dat->nombre_condicion_bien',
                '$dat->terreno_fines','$dat->terreno_precio_adquisicion','$dat->observacion'],['tipo_inmueble','zona_bien'],
                ['$dat->tipo_inmueble', '$dat->terreno_zona'])";

                $this->table->add_row($dat->id_bien,$dat->descripcion, $dat->codigo,$dat->codigo_anterior,$dat->tipo_inmueble,$dat->terreno_extension,
                $dat->terreno_zona,$dat->terreno_direccion,'<a class="icono icon-actualizar" onClick="'.$onClick.'"></a>',
                '<a class="icono icon-eliminar" uri='.base_url('index.php/ActivoFijo/Bienes_Inmuebles/EliminarDato/'.$dat->id_bien).'></a>');
              }
          }
        } else {
          $msg = array('data' => "Texto no encontrado", 'colspan' => "10");
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
        redirect('/ActivoFijo/Bienes_Inmuebles/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  /*
  * Actualiza o Registra al sistema
  */
  public function RecibirDatos(){
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Bienes_Inmuebles');
    $USER = $this->session->userdata('logged_in');
    $id_dato_comun=$this->input->post('dato_comun');
    $cor=$this->Bienes_inmuebles_model->obtenerCorrelativo();
    $cat=$this->Bienes_inmuebles_model->calcularCodigo($id_dato_comun);
    $codigo=$cat->numero_categoria.'.'.sprintf("%'06d", $cor).'.'.$cat->numero_subcategoria;
    $id_condicion_bien=$this->Condicion_bien_model->obtenercondicionId('EN USO');
    if($USER){
        $data = array(
            'id_dato_comun' => $id_dato_comun,
            'codigo_anterior' => $this->input->post('codigo_anterior'),
            'tipo_inmueble' => $this->input->post('tipo_inmueble'),
            'terreno_extension' => $this->input->post('extension'),
            'matricula' => $this->input->post('matricula'),
            'terreno_direccion' => $this->input->post('direccion'),
            'terreno_zona' => $this->input->post('zona_bien'),
            'id_condicion_bien' => $id_condicion_bien,
            'terreno_fines' => $this->input->post('fines'),
            'terreno_precio_adquisicion' => $this->input->post('precio'),
            'observacion' => $this->input->post('observacion'),
        );

        if (!($this->input->post('id') == '')){
            if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'update')) {
                $this->Bienes_inmuebles_model->actualizarBienesInmuebles($this->input->post('id'),$data);
                redirect('/ActivoFijo/Bienes_Inmuebles/index/update');
              } else {
                redirect('/ActivoFijo/Bienes_Inmuebles/index/forbidden');
              }
        }
      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'insert')) {
        $data['codigo']=$codigo;
        $this->Bienes_inmuebles_model->insertarBienesInmuebles($data);
        redirect('/ActivoFijo/Bienes_Inmuebles/index/new');
      } else {
        redirect('/ActivoFijo/Bienes_Inmuebles/index/forbidden');
      }
      } else {
        redirect('login/index/error_no_autenticado');
      }
  }

  /*
  * elimina un registro cuando se le pasa por la url el id
  */
  public function EliminarDato(){
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Bienes_Inmuebles');
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $id = $this->uri->segment(4);
      if ($this->Bienes_Muebles_Model->contieneDetalleMovimiento($id)->asociados>0){
        if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'delete')) {
          redirect('/ActivoFijo/Bienes_Inmuebles/index/no_delete');
        } else {
          redirect('/ActivoFijo/Bienes_Inmuebles/index/forbidden');
        }
      }
      else {
        if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'delete')) {
          $this->Bienes_Muebles_Model->eliminarBien_mueble($id);
          redirect('/ActivoFijo/Bienes_Inmuebles/index/delete');
        } else {
          redirect('/ActivoFijo/Bienes_Inmuebles/index/forbidden');
        }
      }
      }else {
        redirect('login/index/error_no_autenticado');
      }
  }

  public function AutocompleteDatosComunes(){
    $registros = '';
    if ($this->input->is_ajax_request()) {
      if (!($this->input->post('autocomplete') == "")) {
          $registros = $this->Datos_Comunes_Model->buscarDatosComunes($this->input->post('autocomplete'));
      } else {
          $registros = $this->Datos_Comunes_Model->obtenerDatosComunes();
      }
    } else {
          $registros = $this->Datos_Comunes_Model->obtenerDatosComunes();
    }

    if ($registros == '') {
      echo '<div id="1" class="suggest-element"><a id="conteo">No se encontraron coincidencias</a></div>';
    }else {
      $i = 1;
      foreach ($registros as $al) {
        echo '<div id="'.$i.'" class="suggest-element" ida="dato'.$al->id_dato_comun.'"><a id="dato'.
        $al->id_dato_comun.'" data="'.$al->id_dato_comun.'"  data1="'.$al->id_dato_comun.'-'.$al->descripcion.'" >'
        .$al->id_dato_comun.'-'.$al->descripcion.'</a></div>';
        $i++;
      }
    }
  }

  public function AutocompleteCondicion(){
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
      foreach ($registros as $al) {
        echo '<div id="'.$i.'" class="suggest-element" ida="condicion'.$al->id_condicion_bien.'"><a id="condicion'.
        $al->id_condicion_bien.'" data="'.$al->id_condicion_bien.'"  data1="'.$al->nombre_condicion_bien.'" >'
        .$al->nombre_condicion_bien.'</a></div>';
        $i++;
      }
    }
  }
}
?>
