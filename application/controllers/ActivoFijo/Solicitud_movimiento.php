<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Solicitud_movimiento extends CI_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->helper(array('form', 'paginacion'));
    $this->load->library('table');
    $this->load->model(array('ActivoFijo/Movimiento_Model','User_model'));
  }

  public function index(){
    if($this->session->userdata('logged_in')){
      $data['title'] = "Solicitude Movimiento";
      //$data['js'] = "assets/js/validate/sol.js";
      //$pri=$this->Solicitud_Model->obtenerId();
      $USER = $this->session->userdata('logged_in');
      //$id_seccion=$USER['id_seccion'];
      $solicitante=$USER['nombre_empleado'];
      //$sec=$this->Solicitud_Model->obtenerSeccion($id_seccion);
      date_default_timezone_set('America/El_Salvador');
      $anyo=20;
      $fecha_actual=date($anyo."y-m-d");
      $msg = array('alert' => $this->uri->segment(4),'fecha'=>$fecha_actual,'solicitante'=>$solicitante,'controller'=>'Solicitud_movimiento');

  		$data['body'] = $this->load->view('mensajes', $msg, TRUE) . $this->load->view('ActivoFijo/Solicitud_movimiento_view',$msg,TRUE) .
                      "<br><div class='content_table '>" .
                      "<div class='limit-content-title'><span class='icono icon-table icon-title'> Historial de Solicitudes de movimiento</span></div>".
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
      $this->table->set_heading('Id', 'Oficina Entrega', 'Oficina Recibe', 'Empleado', 'Tipo Movimiento', 'Fecha','Enviar','Detalle','Eliminar');

      /*
      * Filtro a la BD
      */
      /*Obtiene el numero de registros a mostrar por pagina */
      $num = '10';
      $pagination = '';
      $registros;
      if ($this->input->is_ajax_request()) {
        if (!($this->input->post('busca') == "")) {
            $registros = $this->Movimiento_Model->buscarMovimientos($this->input->post('busca'));
        } else {
            $registros = $this->Movimiento_Model->obtenerSolMovimientosLimit($num, $this->uri->segment(4));
            $pagination = paginacion('index.php/ActivoFijo/Solicitud_movimiento/index/', $this->Movimiento_Model->totalMovimientos(),$num, '4');
        }
      } else {
            $registros = $this->Movimiento_Model->obtenerSolMovimientosLimit($num, $this->uri->segment(4));
            $pagination = paginacion('index.php/ActivoFijo/Solicitud_movimiento/index/', $this->Movimiento_Model->totalMovimientos(),$num, '4');
      }
      //var_dump($registros);
      /*
      * llena la tabla con los datos consultados
      */

      if (!($registros == FALSE)) {
        foreach($registros as $sol) {
          $emp=$this->Movimiento_Model->obtenerNombreEmpleado($sol->id_empleado);
          $ent=$this->Movimiento_Model->obtenerNombreOficinas($sol->id_oficina_entrega);
          $rec=$this->Movimiento_Model->obtenerNombreOficinas($sol->id_oficina_recibe);
            //$fuente = $this->Fuentefondos_model->obtenerFuente($sol->id_fuentes);
            //$seccion = $this->Solicitud_Model->obtenerSeccion($sol->id_seccion);
            if($sol->estado_solicitud=='INGRESADA' || $sol->estado_solicitud=='ENVIADA'){
            $botones='<a class="icono icon-detalle" href="'.base_url('index.php/ActivoFijo/Detalle_solicitud_movimiento/index/'.$sol->id_movimiento.'/').'"></a>';
            if($sol->estado_solicitud=='INGRESADA'){
                $eliminar='<a class="icono icon-eliminar" uri='.base_url('index.php/ActivoFijo/Solicitud_movimiento/EliminarDato/'.$sol->id_movimiento).'></a>';
                $enviar = '<a class="icono icon-rocket" href='.base_url('index.php/ActivoFijo/Solicitud_movimiento/EnviarDato/'.$sol->id_movimiento).' title="Enviar"></a>';
            }elseif ($sol->estado_solicitud=='ENVIADA'){
              $eliminar='<a class="icono icon-eliminar" uri='.base_url('index.php/ActivoFijo/Solicitud_movimiento/EliminarDato/'.$sol->id_movimiento).'></a>';
              $enviar='<a class="icono icon-denegar"></a>';
            } else {
              $eliminar='<a class="icono icon-denegar"></a>';
              $enviar='<a class="icono icon-denegar"></a>';
            }

            $onClick = "llenarFormulario('solicitud', ['id', 'fecha_solicitud', 'autocomplete3', 'oficina_entrega',
            'autocomplete4','empleado','autocomplete5','tipo_movimiento','usuario_externo','entregado_por','recibido_por','autorizado_por','visto_bueno_por','observacion','comentario'],
                        [$sol->id_movimiento, '$sol->fecha_guarda', '$sol->id_oficina_entrega','$sol->id_empleado',
                        $sol->primer_nombre, $sol->id_tipo_movimiento,$sol->nombre_movimiento,$sol->usuario_externo,'$sol->recibido_por','$sol->entregado_por','$sol->autorizado_por','$sol->visto_bueno_por',$sol->observacion])";

            $this->table->add_row($sol->id_movimiento, $ent->nombre_oficina.', '.$ent->nombre_seccion.', '.$ent->nombre_almacen,$rec->nombre_oficina.', '.$rec->nombre_seccion.', '.$rec->nombre_almacen,
            $emp->nombre_completo,$sol->nombre_movimiento, $sol->fecha_guarda, $enviar,$botones,$eliminar);
              }
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
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Solicitud_movimiento');
    $USER = $this->session->userdata('logged_in');
    $numero= $this->User_model->obtenerSiguienteIdModuloIncrement('sic_movimiento');
    $botones;
    if($USER){
      $data = array(
          'id_oficina_entrega' => $this->input->post('oficina_entrega'),
          'id_oficina_recibe' => $this->input->post('oficina_recibe'),
          'id_empleado' => $this->input->post('empleado'),
          'id_tipo_movimiento' => $this->input->post('tipo_movimiento'),
          'usuario_externo' => $this->input->post('usuario_externo'),
          'entregado_por' => $this->input->post('entregado_por'),
          'recibido_por' => $this->input->post('recibido_por'),
          'autorizado_por' => $this->input->post('autorizado_por'),
          'visto_bueno_por' => $this->input->post('visto_bueno_por'),
          'fecha_guarda' => $this->input->post('fecha_solicitud'),
          'nivel_solicitud' => 0,
          'estado_solicitud' => 'INGRESADA',
          'estado_movimiento' => 'ABIERTO',
          'observacion' => $this->input->post('observacion')
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
          $this->Movimiento_Model->actualizarSolicitud($this->input->post('id'),$data);
          redirect('/ActivoFijo/Solicitud_movimiento/index/update');
        } else {
          redirect('/ActivoFijo/Solicitud_movimiento/index/forbidden');
        }
      }

      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'insert')) {
        $rastrea['operacion']='INSERTA';
        $rastrea['id_registro']=$this->User_model->obtenerSiguienteIdModuloIncrement('sic_solicitud');
        $this->User_model->insertarRastreabilidad($rastrea);
        $this->Movimiento_Model->insertarSolicitud($data);
        redirect('/ActivoFijo/Detalle_solicitud_movimiento/index/'.$numero);
      } else {
        redirect('/ActivoFijo/Solicitud_movimiento/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  public function EliminarDato(){
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Solicitud_movimiento');
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
        if ($this->Movimiento_Model->contieneDetalleMovimiento($id)->asociados>0){
          redirect('/ActivoFijo/Solicitud_movimiento/index/existeSol');
        }
        else {
          $this->User_model->insertarRastreabilidad($rastrea);
          $this->Movimiento_Model->eliminarMovimiento($id);
          redirect('/ActivoFijo/Solicitud_movimiento/index/delete');
        }
      } else {
        redirect('/ActivoFijo/Solicitud_movimiento/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  public function EnviarDato() {
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Solicitud_movimiento');
    $USER = $this->session->userdata('logged_in');
    if($USER){
      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'delete')) {
        $id = $this->uri->segment(4);
        if ($this->Movimiento_Model->contieneDetalleMovimiento($id)->asociados>0){
          $this->Movimiento_Model->enviarSolicitud($id);
          //$this->Notificacion_model->NotificacionSolicitudBodega($id, $USER, 1);
          redirect('/ActivoFijo/Solicitud_movimiento/index/send');
        }
        else {
          redirect('/ActivoFijo/Solicitud_movimiento/index/noexisteSol');
        }
      } else {
        redirect('/ActivoFijo/Solicitud_movimiento/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }
}
?>
