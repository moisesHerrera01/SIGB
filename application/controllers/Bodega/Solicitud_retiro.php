<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Solicitud_retiro extends CI_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->helper(array('form', 'paginacion'));
    $this->load->library('table');
    $this->load->model(array('Bodega/Solicitud_retiro_model','Bodega/Detalle_solicitud_producto_model', 'Notificacion_model', 'Bodega/Fuentefondos_model'));
  }

  public function index(){
    if($this->session->userdata('logged_in')){
      $data['title'] = "Solicitudes";
      $data['js'] = "assets/js/validate/retiro_solicitud.js";
      date_default_timezone_set('America/El_Salvador');
      $anyo=20;
      $fecha_actual=date($anyo."y-m-d");
      $msg = array('alert' => $this->uri->segment(4),'fecha'=>$fecha_actual,'controller'=>'solicitud_retiro');
  		$data['body'] = $this->load->view('mensajes', $msg, TRUE) . $this->load->view('Bodega/solicitud_retiro_view',$msg,TRUE) .
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
    //var_dump($this->User_model->obtenerEmpleadosSeccionCorrecta());
    if($USER){
      /*
      * Configuracion de la tabla
      */

      $template = array(
          'table_open' => '<table class="table table-striped table-bordered">'
      );
      $this->table->set_template($template);
      $this->table->set_heading('Id','Número','Fecha', 'Sección', 'Fuente Fondo', 'Estado','Detalle','Liquidar',
      'Editar','Eliminar','Acta');
      /*
      * Filtro a la BD
      */
      /*Obtiene el numero de registros a mostrar por pagina */
      $num = '10';
      $pagination = '';
      $registros;
      if ($this->input->is_ajax_request()) {
        if (!($this->input->post('busca') == "")) {
            $registros = $this->Solicitud_retiro_model->buscarSolicitudes($USER['id'], $this->input->post('busca'));
        } else {
            $registros = $this->Solicitud_retiro_model->obtenerSolicitudesLimit($num, $this->uri->segment(4));
            $pagination = paginacion('index.php/Bodega/Solicitud_retiro/index/', $this->Solicitud_retiro_model->totalSolicitudes()->total,
                          $num, '4');
        }
      } else {
            $registros = $this->Solicitud_retiro_model->obtenerSolicitudesLimit($num, $this->uri->segment(4));
            $pagination = paginacion('index.php/Bodega/Solicitud_retiro/index/', $this->Solicitud_retiro_model->totalSolicitudes()->total,
                          $num, '4');
      }
      /*
      * llena la tabla con los datos consultados
      */

      if (!($registros == FALSE)) {
        $modulo=$this->User_model->obtenerModulo('Bodega/Solicitud_retiro');
        foreach($registros as $sol) {
            $fuente = $this->Fuentefondos_model->obtenerFuente($sol->id_fuentes);
            $seccion = $this->Solicitud_retiro_model->obtenerSeccion($sol->id_seccion);
            $botones='<a class="icono icon-detalle" href="'.base_url('index.php/Bodega/Detalle_retiro/index/'.$sol->id_solicitud.'/'.$modulo).'"></a>';
            $onClick = "llenarFormulario('solicitud', ['id', 'fecha_solicitud','id_usuario','autocomplete',
             'seccion','autocomplete3','autocomplete2', 'id_fuentes'],
                        [$sol->id_solicitud, '$sol->fecha_solicitud','$sol->id_usuario','$sol->nombre_completo',
                         '$sol->id_seccion','$sol->nombre_seccion','$sol->nombre_fuente','$sol->id_fuentes'], 'prioridad', '$sol->prioridad')";
                         if($sol->estado_solicitud=='APROBADA' || $sol->estado_solicitud=='EN DESPACHO' || $sol->estado_solicitud=='INGRESADA' ){
                             $eliminar='<a class="icono icon-eliminar" uri='.base_url('index.php/Bodega/Solicitud_retiro/EliminarDato/'.$sol->id_solicitud).'></a>';
                             $editar='<a class="icono icon-pencil" onClick="'.$onClick.'"></a>';
                             $acta = '<a class="icono icon-lock"></a>';
                             if($this->Solicitud_Model->validarLiquidar($sol->id_solicitud)){
                               $liquidar='<a class="icono icon-liquidar" href="'.base_url('index.php/Bodega/Solicitud_retiro/Liquidar/'.$sol->id_solicitud.'/').'"></a>';
                             }else{
                               $liquidar='<a class="icono icon-lock"></a>';
                             }
                         }elseif ($sol->estado_solicitud=='LIQUIDADA') {
                           $acta = '<a class="icono icon-acta" href="'.base_url('index.php/Bodega/ActaRetiro/index/'.$sol->id_solicitud.'/').'" target="_blank"></a>';
                           $eliminar='<a class="icono icon-denegar"></a>';
                           $editar='<a class="icono icon-denegar"></a>';
                           $liquidar='<a class="icono icon-denegar"></a>';
                         }else{
                           $eliminar='<a class="icono icon-denegar"></a>';
                           $editar='<a class="icono icon-denegar"></a>';
                           $liquidar='<a class="icono icon-denegar"></a>';
                           $acta = '<a class="icono icon-denegar"></a>';
                         }
            $this->table->add_row($sol->id_solicitud, $sol->numero_solicitud,$sol->fecha_solicitud,
             $seccion, $fuente, $sol->estado_solicitud,$botones,$liquidar,$editar,$eliminar,$acta);

        }
      } else {
        $msg = array('data' => "Texto no encontrado", 'colspan' => "11");
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
    $modulo=$this->User_model->obtenerModulo('Bodega/Solicitud_retiro');
    $USER = $this->session->userdata('logged_in');
    $numero= $this->Solicitud_retiro_model->obtenerId();
    $botones;
    if($USER){
      $data = array(
          'fecha_solicitud' => $this->input->post('fecha_solicitud'),
          'id_seccion' => $this->input->post('seccion'),
          'numero_solicitud' => $this->Solicitud_retiro_model->obtenerNumeroFuente($this->input->post('id_fuentes')),
          'prioridad'=> $this->input->post('prioridad'),
          'id_usuario' => $this->input->post('id_usuario'),
          'id_fuentes' => $this->input->post('id_fuentes'),
          'nivel_solicitud' =>3
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
          $data['numero_solicitud']=$this->Solicitud_retiro_model->obtenerSolicitud($this->input->post('id'));
          $rastrea['operacion']='ACTUALIZA';
          $rastrea['id_registro']=$this->input->post('id');
          $this->User_model->insertarRastreabilidad($rastrea);
          $this->Solicitud_retiro_model->actualizarSolicitud($this->input->post('id'),$data);
          redirect('/Bodega/Solicitud_retiro/index/update');
        } else {
          redirect('/Bodega/Solicitud_retiro/index/forbidden');
        }
      }

      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'insert')) {
        $rastrea['operacion']='INSERTA';
        $rastrea['id_registro']=$this->User_model->obtenerSiguienteIdModuloIncrement('sic_solicitud');
        $this->User_model->insertarRastreabilidad($rastrea);
        $this->Solicitud_retiro_model->insertarSolicitud($data);
        redirect('/Bodega/Detalle_retiro/index/'.$numero.'/'.$modulo);
      } else {
        redirect('/Bodega/Solicitud_retiro/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  public function EliminarDato(){
    $modulo=$this->User_model->obtenerModulo('Bodega/Solicitud_retiro');
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
          redirect('/Bodega/Solicitud_retiro/index/existeSol');
        }
        else {
          $this->User_model->insertarRastreabilidad($rastrea);
          $this->Solicitud_retiro_model->eliminarSolicitud($id);
          redirect('/Bodega/Solicitud_retiro/index/delete');
        }
      } else {
        redirect('/Bodega/Solicitud_retiro/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  public function Liquidar(){
    $USER = $this->session->userdata('logged_in');
    $id = $this->uri->segment(4);
    $usuario = $this->Solicitud_Model->obtenerSolicitudUsuario($id);
    $estado=$this->Solicitud_Model->retornarEstado($id);
    if($estado=='APROBADA'){
      $this->Notificacion_model->NotificacionSolicitudBodega($id, $USER, 4);
      $this->Solicitud_Model->liquidar($id);
      redirect('/Bodega/kardex/insertarDescargoSolicitud_retiro?controller=solicitud_retiro&&id='.$id);
    }else{
      redirect('/Bodega/Solicitud_retiro/index/liquidada/'.$id);
    }
  }

  public function AutocompleteUsuarioSeccion(){
    $USER = $this->session->userdata('logged_in');
    $seccion=$this->input->post('seccion');
    if($USER){
      $registros = '';
      if ($this->input->is_ajax_request()) {
        if (!($this->input->post('autocomplete') == "")) {
            $registros = $this->User_model->buscarEmpleadosSeccionCorrecta($this->input->post('autocomplete'),$seccion);
        } else {
            $registros = $this->User_model->obtenerEmpleadosSeccionCorrecta($seccion);
        }
      } else {
            $registros = $this->User_model->obtenerEmpleadosSeccionCorrecta($seccion);
      }
      if ($registros == '') {
        echo '<div id="1" class="suggest-element"><a id="conteo">No se encontraron coincidencias</a></div>';
      }else {
        $i = 1;
        foreach ($registros as $sol) {
          echo '<div id="'.$i.'" class="suggest-element" ida="sol'.$sol->id_usuario.'"><a id="sol'.
          $sol->id_usuario.'" data="'.$sol->id_usuario.'"  data1="'.$sol->nombre_empleado.'">'
          .$sol->nombre_empleado.'</a></div>';
          $i++;
        }
      }
    } else {
      redirect('login');
    }
  }
}
?>
