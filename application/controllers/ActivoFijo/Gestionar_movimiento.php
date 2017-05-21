<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Gestionar_movimiento extends CI_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->helper(array('form', 'paginacion'));
    $this->load->library('table');
    $this->load->model(array('ActivoFijo/Movimiento_Model','User_model'));
  }

  public function index(){
    if($this->session->userdata('logged_in')){
      $data['title'] = "Gestionar Movimiento";
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

  		$data['body'] = $this->load->view('mensajes', $msg, TRUE) . $this->load->view('ActivoFijo/Gestionar_movimiento_view',$msg,TRUE) .
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
      $this->table->set_heading('Id', 'Oficina Recibe', 'Oficina Entrega', 'Empleado', 'Tipo Movimiento', 'Fecha','Aprobar','Denegar','Cerrar','Imprimir','Detalle','Editar','Eliminar');

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
            $pagination = paginacion('index.php/ActivoFijo/Gestionar_movimiento/index/', $this->Movimiento_Model->totalMovimientos(),$num, '4');
        }
      } else {
            $registros = $this->Movimiento_Model->obtenerSolMovimientosLimit($num, $this->uri->segment(4));
            $pagination = paginacion('index.php/ActivoFijo/Gestionar_movimiento/index/', $this->Movimiento_Model->totalMovimientos(),$num, '4');
      }
      //var_dump($registros);
      /*
      * llena la tabla con los datos consultados
      */

      if (!($registros == FALSE)) {
        foreach($registros as $sol) {
            //$fuente = $this->Fuentefondos_model->obtenerFuente($sol->id_fuentes);
            //$seccion = $this->Solicitud_Model->obtenerSeccion($sol->id_seccion);
            $onClick = "llenarFormulario('solicitud', ['id', 'fecha_solicitud','tipo','entregado_por','recibido_por','autorizado_por','visto_bueno_por'],
                        [$sol->id_movimiento, '$sol->fecha_guarda','$sol->nombre_movimiento','$sol->recibido_por','$sol->entregado_por','$sol->autorizado_por','$sol->visto_bueno_por'],false,false,false,'observacion','$sol->observacion')";

            $botones='<a class="icono icon-detalle" href="'.base_url('index.php/ActivoFijo/Detalle_gestionar_movimiento/index/'.$sol->id_movimiento.'/').'"></a>';

            if($sol->nivel_solicitud==2){
                $actualizar = '<a class="icono icon-actualizar" title="Actualizar" onClick="'.$onClick.'"></a>';
                $eliminar='<a class="icono icon-eliminar" uri='.base_url('index.php/ActivoFijo/Gestionar_movimiento/EliminarDato/'.$sol->id_movimiento).'></a>';
                $aprobar = '<a class="icono icon-liquidar" href="'.base_url('index.php/ActivoFijo/Gestionar_movimiento/Aprobar/'.$sol->id_movimiento.'/').'"></a>';
                $denegar = '<a class="icono icon-cross" href="'.base_url('index.php/ActivoFijo/Gestionar_movimiento/Denegar/'.$sol->id_movimiento.'/').'"></a>';
                $cerrar='<a class="icono icon-denegar"></a>';
                $imprimir = '<a class="icono icon-denegar"></a>';
                $this->table->add_row($sol->id_movimiento, $sol->id_oficina_recibe,$sol->id_oficina_entrega, $sol->primer_nombre.$sol->primer_apellido,
                                      $sol->nombre_movimiento, $sol->fecha_guarda,$aprobar,$denegar,$cerrar,$botones,$actualizar,$eliminar);
            }elseif ($sol->nivel_solicitud==3){
              $actualizar ='<a class="icono icon-denegar"></a>';
              $eliminar='<a class="icono icon-denegar"></a>';
              $aprobar = '<a class="icono icon-denegar"></a>';
              $denegar = '<a class="icono icon-denegar"></a>';
              $imprimir = '<a class="icono icon-acta" target="_blank" href="'.base_url('index.php/ActivoFijo/Movimiento_imp/index/'.$sol->id_movimiento.'/').'"></a>';
              $cerrar='<a class="icono icon-lock" href="'.base_url('index.php/ActivoFijo/Gestionar_movimiento/cerrar/'.$sol->id_movimiento.'/').'"></a>';
              $this->table->add_row($sol->id_movimiento, $sol->id_oficina_recibe,$sol->id_oficina_entrega, $sol->primer_nombre.$sol->primer_apellido,
                                    $sol->nombre_movimiento, $sol->fecha_guarda,$aprobar,$denegar,$cerrar,$imprimir,$botones,$actualizar,$eliminar);
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

  public function Aprobar() {
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $id = $this->uri->segment(4);
      //$estado = $this->Solicitud_Model->retornarEstado($id);
      //$nivel = $this->Solicitud_Model->retornarNivel($id);
      //if ($estado == 'ENVIADA' || $estado == 'APROBADA'){
        $data = array(
            'estado_solicitud' => 'APROBADA',
            'nivel_solicitud' => 3
        );
      //}
      $this->Movimiento_Model->actualizarMovimiento($id,$data);
      //$this->Notificacion_model->NotificacionSolicitudBodega($id, $USER, $nivel + 1);
      redirect('/ActivoFijo/Gestionar_movimiento/index/update');
    } else {
      redirect('login/index/error_no_autenticado');
    }

  }

  public function Denegar() {
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $id = $this->uri->segment(4);
      //$nivel = $this->Solicitud_Model->retornarNivel($id);
      $data = array(
          'estado_solicitud' => 'DENEGADA',
          'nivel_solicitud' => 9
      );
      $this->Movimiento_Model->actualizarMovimiento($id,$data);
    //  $this->Notificacion_Model->NotificacionSolicitudBodega($id, $USER, 9);
      redirect('/ActivoFijo/Gestionar_movimiento/index/update');
    } else {
      redirect('login/index/error_no_autenticado');
    }

  }

  public function ActualizarDatos() {
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Gestionar_movimiento');
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
    );
    //$id_seccion=$USER['id_seccion'];
    //$botones;
    //$nivel_solicitud = $this->Solicitud_Model->retornarNivel($this->input->post('id'));
    //$observacion='observacion';
    /*if ($USER['rol'] == 'JEFE UNIDAD' || $USER['rol'] == 'JEFE BODEGA' || $USER['rol'] == 'JEFE COMPRAS' || $USER['rol'] == 'JEFE UACI' || $USER['rol'] == 'JEFE AF'
        || $USER['rol'] == 'COLABORADOR BODEGA' || $USER['rol'] == 'COLABORADOR COMPRAS' || $USER['rol'] == 'COLABORADOR UACI' || $USER['rol'] == 'COLABORADOR AF') {                   //verificar act para colaboradores
      $comentario='comentario_jefe';
    }elseif ($USER['rol'] == 'DIRECTOR ADMINISTRATIVO') {
      $comentario='comentario_admin';
    }*/
    if($USER){
      $data = array(
          'entregado_por' => $this->input->post('entregado_por'),
          'recibido_por' => $this->input->post('recibido_por'),
          'autorizado_por' => $this->input->post('autorizado_por'),
          'visto_bueno_por' => $this->input->post('visto_bueno_por'),
          'observacion' => $this->input->post('observacion')

      );

      if (!($this->input->post('id') == '')){
        if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'update')) {
          $rastrea['operacion']='ACTUALIZA';
          $rastrea['id_registro']=$this->input->post('id');
          $this->User_model->insertarRastreabilidad($rastrea);
          $this->Movimiento_Model->actualizarMovimiento($this->input->post('id'),$data);
          redirect('/ActivoFijo/Gestionar_movimiento/index/update');
        } else {
          redirect('/ActivoFijo/Gestionar_movimiento/index/forbidden');
        }
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  public function cerrar(){
      $this->Movimiento_Model->cerrar($this->uri->segment(4));
      redirect('/ActivoFijo/Gestionar_movimiento/index/update');
  }

  public function EliminarDato(){
    $modulo=$this->User_model->obtenerModulo('ActivoFijo/Gestionar_movimiento');
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
          redirect('/ActivoFijo/Gestionar_movimiento/index/existeSol');
        }
        else {
          $this->User_model->insertarRastreabilidad($rastrea);
          $this->Movimiento_Model->eliminarMovimiento($id);
          redirect('/ActivoFijo/Gestionar_movimiento/index/delete');
        }
      } else {
        redirect('/ActivoFijo/Gestionar_movimiento/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }
}
?>
