<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Solicitud extends CI_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->helper(array('form', 'paginacion'));
    $this->load->library('table');
    $this->load->model(array('Bodega/Solicitud_Model','Bodega/Detalle_solicitud_producto_model', 'Notificacion_model', 'Bodega/Fuentefondos_model'));
  }

  public function index(){
    if($this->session->userdata('logged_in')){
      $data['title'] = "Solicitudes";
      $data['js'] = "assets/js/validate/sol.js";
      $pri=$this->Solicitud_Model->obtenerId();
      $USER = $this->session->userdata('logged_in');
      $id_seccion=$USER['id_seccion'];
      $solicitante=$USER['nombre_empleado'];
      $sec=$this->Solicitud_Model->obtenerSeccion($id_seccion);
      date_default_timezone_set('America/El_Salvador');
      $anyo=20;
      $fecha_actual=date($anyo."y-m-d");
      $msg = array('alert' => $this->uri->segment(4),'fecha'=>$fecha_actual,'solicitante'=>$solicitante,'controller'=>'solicitud',
      'seccion'=>$sec,'id_seccion'=>$id_seccion);

  		$data['body'] = $this->load->view('mensajes', $msg, TRUE) . $this->load->view('Bodega/solicitud_view',$msg,TRUE) .
                      "<br><div class='content_table '>" .
                      "<div class='limit-content-title'><span class='icono icon-table icon-title'> Historial de Solicitudes a Bodega</span></div>".
                      "<div class='limit-content'>" . $this->mostrarTabla() . "</div></div>";
      $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
      $this->load->view('base', $data);
    } else {
      redirect('login/index/error_no_autenticado');
    }
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
      $this->table->set_heading('Id', 'Número', 'Fecha', 'Sección', 'Fuente Fondo', 'Justificación','Estado', 'Enviar','Detalle','Eliminar');

      /*
      * Filtro a la BD
      */
      /*Obtiene el numero de registros a mostrar por pagina */
      $num = '10';
      $pagination = '';
      $registros;
      if ($this->input->is_ajax_request()) {
        if (!($this->input->post('busca') == "")) {
            $registros = $this->Solicitud_Model->buscarSolicitudesUser($USER['id_seccion'], $this->input->post('busca'));
        } else {
            $registros = $this->Solicitud_Model->obtenerSolicitudesUserLimit($USER['id_seccion'], $num, $this->uri->segment(4));
            $pagination = paginacion('index.php/Bodega/Solicitud/index/', $this->Solicitud_Model->totalSolicitudes($USER['id_seccion'])->total,
                          $num, '4');
        }
      } else {
            $registros = $this->Solicitud_Model->obtenerSolicitudesUserLimit($USER['id_seccion'] ,$num, $this->uri->segment(4));
            $pagination = paginacion('index.php/Bodega/Solicitud/index/', $this->Solicitud_Model->totalSolicitudes($USER['id_seccion'])->total,
                          $num, '4');
      }

      /*
      * llena la tabla con los datos consultados
      */

      if (!($registros == FALSE)) {
        foreach($registros as $sol) {
            $fuente = $this->Fuentefondos_model->obtenerFuente($sol->id_fuentes);
            $seccion = $this->Solicitud_Model->obtenerSeccion($sol->id_seccion);
            $botones='<a class="icono icon-detalle" href="'.base_url('index.php/Bodega/Detalle_solicitud_producto/index/'.$sol->id_solicitud.'/').'"></a>';
            if($sol->estado_solicitud=='INGRESADA'){
                $eliminar='<a class="icono icon-eliminar" uri='.base_url('index.php/Bodega/Solicitud/EliminarDato/'.$sol->id_solicitud).'></a>';
                $enviar = '<a class="icono icon-rocket" href='.base_url('index.php/Bodega/Solicitud/EnviarDato/'.$sol->id_solicitud).' title="Enviar"></a>';
            }elseif ($sol->estado_solicitud=='ENVIADA'){
              $eliminar='<a class="icono icon-eliminar" uri='.base_url('index.php/Bodega/Solicitud/EliminarDato/'.$sol->id_solicitud).'></a>';
              $enviar='<a class="icono icon-denegar"></a>';
            } else {
              $eliminar='<a class="icono icon-denegar"></a>';
              $enviar='<a class="icono icon-denegar"></a>';
            }

            $onClick = "llenarFormulario('solicitud', ['id', 'fecha_solicitud', 'id_seccion', 'numero_solicitud', 'fuente', 'autocomplete2','comentario'],
                        [$sol->id_solicitud, '$sol->fecha_solicitud', '$seccion','$sol->numero_solicitud', $sol->id_fuentes, $fuente,$sol->comentario])";

            $this->table->add_row($sol->id_solicitud, $sol->numero_solicitud,$sol->fecha_solicitud, $seccion, $fuente, $sol->comentario, $sol->estado_solicitud, $enviar,$botones,$eliminar);

        }
      } else {
        $msg = array('data' => "Texto no encontrado", 'colspan' => "8");
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
      redirect('login/index/error_no_autenticado');
    }
  }

  /*
  * Actualiza o Registra al sistema
  */
  public function RecibirDatos(){
    $modulo=$this->User_model->obtenerModulo('Bodega/Solicitud');
    $USER = $this->session->userdata('logged_in');
    $numero= $this->Solicitud_Model->obtenerId();
    $botones;
    if($USER){
      $data = array(
          'fecha_solicitud' => $this->input->post('fecha_solicitud'),
          'id_seccion' => $this->input->post('seccion'),
          'numero_solicitud' => $this->Solicitud_Model->obtenerNumeroFuente($this->input->post('fuente')),
          'prioridad'=>'NORMAL',
          'id_usuario' => $USER['id'],
          'fuente' => $this->input->post('fuente'),
          'comentario' => $this->input->post('comentario')
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
          $this->Solicitud_Model->actualizarSolicitud($this->input->post('id'),$data);
          redirect('/Bodega/Solicitud/index/update');
        } else {
          redirect('/Bodega/Solicitud/index/forbidden');
        }
      }

      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'insert')) {
        $rastrea['operacion']='INSERTA';
        $rastrea['id_registro']=$this->User_model->obtenerSiguienteIdModuloIncrement('sic_solicitud');
        $this->User_model->insertarRastreabilidad($rastrea);
        $this->Solicitud_Model->insertarSolicitud($data);
        redirect('/Bodega/Detalle_solicitud_producto/index/'.$numero);
      } else {
        redirect('/Bodega/Solicitud/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  public function EliminarDato(){
    $modulo=$this->User_model->obtenerModulo('Bodega/Solicitud');
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
        if ($this->Detalle_solicitud_producto_model->existeSolicitud($id)){
          redirect('/Bodega/Solicitud/index/existeSol');
        }
        else {
          $this->User_model->insertarRastreabilidad($rastrea);
          $this->Solicitud_Model->eliminarSolicitud($id);
          redirect('/Bodega/Solicitud/index/delete');
        }
      } else {
        redirect('/Bodega/Solicitud/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  public function EnviarDato() {
    $modulo=$this->User_model->obtenerModulo('Bodega/Solicitud');
    $USER = $this->session->userdata('logged_in');
    if($USER){
      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'delete')) {
        $id = $this->uri->segment(4);
        if ($this->Detalle_solicitud_producto_model->existeSolicitud($id)){
          $this->Solicitud_Model->enviarSolicitud($id);
          $this->Notificacion_model->NotificacionSolicitudBodega($id, $USER, 1);
          redirect('/Bodega/Solicitud/index/send');
        }
        else {
          redirect('/Bodega/Solicitud/index/noexisteSol');
        }
      } else {
        redirect('/Bodega/Solicitud/index/forbidden');
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
            $registros = $this->Solicitud_Model->buscarSecciones($this->input->post('autocomplete'));
        } else {
            $registros = $this->Solicitud_Model->obtenerSecciones();
        }
      } else {
            $registros = $this->Solicitud_Model->obtenerSecciones();
      }

      if ($registros == '') {
        echo '<div id="1" class="suggest-element"><a id="conteo">No se encontraron coincidencias</a></div>';
      }else {
        $i = 1;
        foreach ($registros as $sec) {
          echo '<div id="'.$i.'" class="suggest-element" ida="seccion'.$sec->id_seccion.'"><a id="seccion'.
          $sec->id_seccion.'" data="'.$sec->id_seccion.'"  data1="'.$sec->nombre_seccion.'" >'
          .$sec->nombre_seccion.'</a></div>';
          $i++;
        }
      }
    } else {
      redirect('login');
    }
  }
}
?>
