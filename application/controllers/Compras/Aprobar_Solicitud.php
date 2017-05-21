<?php
//welcome to the jungle
defined('BASEPATH') OR exit('No direct script access allowed');

class Aprobar_solicitud extends CI_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->helper(array('form', 'paginacion'));
    $this->load->library('table');
    $this->load->model(array('Compras/Solicitud_Compra_Model','Compras/Detalle_solicitud_compra_model',
    'Bodega/Solicitud_Model', 'Notificacion_model'));
  }

  public function index(){
    if($this->session->userdata('logged_in')){
      $data['title'] = "Aprobar Solicitudes";
      $data['js'] = "assets/js/validate/asol.js";
      $pri=$this->Solicitud_Compra_Model->obtenerIdSolicitudCompra();
      $USER = $this->session->userdata('logged_in');
      $solicitante=$USER['nombre_empleado'];
      $id_seccion=$USER['id_seccion'];
      date_default_timezone_set('America/El_Salvador');
      $anyo=20;
      $fecha_actual=date($anyo."y-m-d");
      $msg = array('alert' => $this->uri->segment(4),'fecha'=>$fecha_actual,'id'=>$pri,'controller'=>'Aprobar_Solicitud',
      'solicitante'=>$solicitante);

      $data['body'] = $this->load->view('mensajes', $msg, TRUE) . $this->load->view('Compras/aprobar_solicitud_view',$msg,TRUE) .
                      "<br><div class='content_table '>" .
                      "<div class='limit-content-title'><span class='icono icon-table icon-title'> Historial de Solicitudes de Compra</span></div>".
                      "<div class='limit-content'>" . $this->mostrarTabla() . "</div></div>";
      $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
      $this->load->view('base', $data);
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  public function mostrarTabla() {
    $USER = $this->session->userdata('logged_in');
    if($USER){
      /*
      * Configuracion de la tabla
      */
      $seccion;
      $template = array(
          'table_open' => '<table class="table table-striped table-bordered">'
      );
      $this->table->set_template($template);
      //se retiro eliminar del encabezado
      $this->table->set_heading('Id', 'Solicitante', 'Fecha', 'Comentario', 'Estado','Aprobar','Denegar', 'Detalle', 'Editar', 'Adjunto');
      $nivel = array();

      if($this->Solicitud_Compra_Model->esAutorizante($USER['id_seccion'],$USER['id_empleado'])) {
        $nivel[] = 2;
        $nivel[] = 3;
        $nivel[] = 4;
        $nivel[] = 5;
        $nivel[] = 6;
        $nivel[] = 7;
        $nivel[] = 8;
        $nivel[] = 9;
        $seccion = $USER['id_seccion'];
      }elseif($USER['rol'] == 'JEFE UNIDAD' || $USER['rol'] == 'JEFE BODEGA' || $USER['rol']=='JEFE AF'
              || $USER['rol'] == 'JEFE COMPRAS' || $USER['rol'] == 'JEFE UACI'
              || $USER['rol'] == 'COLABORADOR BODEGA' || $USER['rol'] == 'COLABORADOR COMPRAS' ||
                 $USER['rol'] == 'COLABORADOR AF' || $USER['rol'] == 'COLABORADOR UACI') {
        $nivel[] = 1;
        $nivel[] = 2;
        $nivel[] = 3;
        $nivel[] = 4;
        $nivel[] = 5;
        $nivel[] = 6;
        $nivel[] = 7;
        $nivel[] = 8;
        $nivel[] = 9;
        $seccion = $USER['id_seccion'];
      } elseif ($USER['rol'] == 'ADMINISTRADOR SICBAF') {
        $nivel[] = 0;
        $nivel[] = 1;
        $nivel[] = 2;
        $nivel[] = 3;
        $nivel[] = 4;
        $nivel[] = 5;
        $nivel[] = 6;
        $nivel[] = 7;
        $nivel[] = 8;
        $nivel[] = 9;
        $seccion = 0;
        $this->table->set_heading('Id', 'Solicitante', 'Fecha', 'Comentario', 'Estado', 'Nivel','Aprobar','Denegar','Detalle', 'Editar', 'Adjuntos');
      }
      $num = '10';
      $pagination = '';
      $registros;
      if ($this->input->is_ajax_request()) {
        if (!($this->input->post('busca') == "")) {
            $registros = $this->Solicitud_Compra_Model->buscarSolicitudes($this->input->post('busca'));
        } else {
            if ($this->Solicitud_Compra_Model->esAutorizante($USER['id_seccion'],$USER['id_empleado'])) {
              $registros = $this->Solicitud_Compra_Model->obtenerSolicitudesAutorizante($nivel, $USER['id_empleado'], $num, $this->uri->segment(4));
              $pagination = paginacion('index.php/Compras/Aprobar_Solicitud/index/', count($this->Solicitud_Compra_Model->totalSolicitudesAutorizante($nivel,$USER['id_empleado'])),
                            $num, '4');
            }else{
              $registros = $this->Solicitud_Compra_Model->obtenerSolicitudesEstadoLimit($nivel, $seccion, $num, $this->uri->segment(4));
              $pagination = paginacion('index.php/Compras/Aprobar_Solicitud/index/', $this->Solicitud_Compra_Model->totalSolicitudesCompra($seccion)->cantidad,
                            $num, '4');
            }
        }
      } else {
        if ($this->Solicitud_Compra_Model->esAutorizante($USER['id_seccion'],$USER['id_empleado'])) {
          $registros = $this->Solicitud_Compra_Model->obtenerSolicitudesAutorizante($nivel, $USER['id_empleado'], $num, $this->uri->segment(4));
          $pagination = paginacion('index.php/Compras/Aprobar_Solicitud/index/', count($this->Solicitud_Compra_Model->totalSolicitudesAutorizante($nivel,$USER['id_empleado'])),
                        $num, '4');

        } else {
          $registros = $this->Solicitud_Compra_Model->obtenerSolicitudesEstadoLimit($nivel, $seccion, $num, $this->uri->segment(4));
          $pagination = paginacion('index.php/Compras/Aprobar_Solicitud/index/', $this->Solicitud_Compra_Model->totalSolicitudesCompra($seccion)->cantidad,
                        $num, '4');
        }
      }
      /*
      * llena la tabla con los datos consultados
      */
      if (!($registros == FALSE)) {
        $modulo=$this->User_model->obtenerModulo('Compras/Aprobar_Solicitud');
        foreach($registros as $sol) {
            $id_rol = $USER['id_rol'];
            $comentario='';
            if ($USER['rol'] == 'JEFE UNIDAD' || $USER['rol'] == 'JEFE BODEGA' || $USER['rol']=='JEFE AF'
             || $USER['rol'] == 'JEFE COMPRAS' || $USER['rol'] == 'JEFE UACI'
             || $USER['rol'] == 'COLABORADOR BODEGA' || $USER['rol'] == 'COLABORADOR COMPRAS'
             || $USER['rol'] == 'COLABORADOR AF' || $USER['rol'] == 'COLABORADOR UACI') {
                  if ($sol->nivel_solicitud==1 || $sol->nivel_solicitud==2 || ($sol->nivel_solicitud==9 && $sol->nivel_anterior == 1)){
                  $comentario=$sol->comentario_jefe;
                } elseif ($sol->nivel_solicitud>=3) {
                    $comentario=$sol->comentario_autorizante;
                  }
                }
                if ($this->Solicitud_Compra_Model->esAutorizante($USER['id_seccion'],$USER['id_empleado'])) {
                  if($sol->comentario_autorizante!=''){
                  $comentario=$sol->comentario_autorizante;
                }else {
                  $comentario=$sol->comentario_jefe;
                }
            }
            $seccion = $this->Solicitud_Model->obtenerSeccion($USER['id_seccion']);
              $solicitante=$this->Solicitud_Compra_Model->obtenerSolicitante($sol->solicitante);
            $descargar_archivo= '<a class="icono icon-descargar" href="'.base_url('index.php/Compras/Aprobar_Solicitud/descargar_archivo/'.$sol->id_solicitud_compra.'/').'"></a>';
            if ($USER['rol'] == 'ADMINISTRADOR SICBAF') {
              if ($sol->nivel_solicitud == 0 || $sol->nivel_solicitud == 3 ||  $sol->nivel_solicitud == 4 ||
               $sol->nivel_solicitud == 5 || $sol->nivel_solicitud == 6 || $sol->nivel_solicitud == 7 ||
                $sol->nivel_solicitud == 8 || $sol->nivel_solicitud == 9){
                $onClick = "llenarFormulario('solicitud', ['id', 'fecha_solicitud', 'numero'],
                [$sol->id_solicitud_compra, '$sol->fecha_solicitud_compra', '$sol->numero_solicitud_compra'], false, false, false, 'comentario', '$comentario')";
                $botones='<a class="icono icon-detalle" href="'.base_url('index.php/Compras/Detalle_Solicitud_Compra/index/'.$sol->id_solicitud_compra.'/'.$modulo.'/'.$id_rol.'/').'"></a>';
                $actualizar = '<a class="icono icon-denegar"></a>';
                $aprobar = '<a class="icono icon-denegar"></a>';
                $denegar = '<a class="icono icon-denegar"></a>';
                $estado = $sol->estado_solicitud_compra;
                if ($sol->nivel_solicitud==2){
                    $comentario=$sol->comentario_jefe;
                } if ($sol->nivel_solicitud>=3) {
                    $comentario=$sol->comentario_autorizante;
                } if ($sol->nivel_solicitud==4){
                    $comentario=$sol->comentario_compras;
                } if ($sol->nivel_solicitud==9 && $sol->nivel_anterior == 1){
                  $comentario=$sol->comentario_jefe;
                } if ($sol->nivel_solicitud==9 && $sol->nivel_anterior == 2){
                  $comentario=$sol->comentario_autorizante;
                } if ($sol->nivel_solicitud==9 && $sol->nivel_anterior == 3){
                  $comentario=$sol->comentario_compras;
                }
                $this->table->add_row($sol->id_solicitud_compra, $solicitante,
                 $sol->fecha_solicitud_compra, $comentario, $estado, $sol->nivel_solicitud,$aprobar,
                 $denegar,$botones,$actualizar, $descargar_archivo);
              } else {
                $onClick = "llenarFormulario('solicitud', ['id', 'fecha_solicitud', 'numero'],
                [$sol->id_solicitud_compra, '$sol->fecha_solicitud_compra', '$sol->numero_solicitud_compra'], false, false, false, 'comentario', '$comentario')";

                $botones='<a class="icono icon-detalle" href="'.base_url('index.php/Compras/Detalle_Solicitud_Compra/index/'.$sol->id_solicitud_compra.'/'.$modulo.'/'.$id_rol.'/').'"></a>';
                $actualizar = '<a class="icono icon-pencil" title="Actualizar" onClick="'.$onClick.'"></a>';
                $aprobar = '<a class="icono icon-liquidar aprobar_compra" data-url="index.php/Compras/Aprobar_Solicitud/Aprobar/'.$sol->id_solicitud_compra.'/" data-url-table="index.php/Compras/Aprobar_Solicitud/mostrarTabla"></a>';
                $denegar = '<a class="icono icon-cross denegar_compra" data-url="index.php/Compras/Aprobar_Solicitud/Denegar/'.$sol->id_solicitud_compra.'/" data-url-table="index.php/Compras/Aprobar_Solicitud/mostrarTabla"></a>';
                $estado = $sol->estado_solicitud_compra;

                if ($sol->nivel_solicitud==1 || $sol->nivel_solicitud==2){
                    $comentario=$sol->comentario_jefe;
                } elseif ($sol->nivel_solicitud>=3) {
                    $comentario=$sol->comentario_autorizante;
                } elseif ($sol->nivel_solicitud==9 && $sol->nivel_anterior == 1){
                  $comentario=$sol->comentario_jefe;
                } elseif ($sol->nivel_solicitud==9 && $sol->nivel_anterior == 2){
                  $comentario=$sol->comentario_autorizante;
                } elseif ($sol->nivel_solicitud==9 && $sol->nivel_anterior == 3){
                  $comentario=$sol->comentario_compras;
                }

                $this->table->add_row($sol->id_solicitud_compra, $solicitante,
                 $sol->fecha_solicitud_compra, $comentario, $estado, $sol->nivel_solicitud,$aprobar,
                 $denegar,$botones,$actualizar, $descargar_archivo);
                }
              }elseif ($sol->autorizante == $USER['id_empleado']) {
                    if ($sol->nivel_solicitud == 2 || $sol->nivel_solicitud == 3){
                      $onClick = "llenarFormulario('solicitud', ['id', 'fecha_solicitud', 'numero'],
                      [$sol->id_solicitud_compra, '$sol->fecha_solicitud_compra', '$sol->numero_solicitud_compra'], false, false, false, 'comentario', '$comentario')";

                      if ($sol->nivel_solicitud == 2){
                      $botones='<a class="icono icon-detalle" href="'.base_url('index.php/Compras/Detalle_Solicitud_Compra/index/'.$sol->id_solicitud_compra.'/'.$modulo.'/'.'111'.'/').'"></a>';
                      $actualizar = '<a class="icono icon-pencil" title="Actualizar" onClick="'.$onClick.'"></a>';
                      $aprobar = '<a class="icono icon-liquidar aprobar_compra" data-url="index.php/Compras/Aprobar_Solicitud/Aprobar/'.$sol->id_solicitud_compra.'/" data-url-table="index.php/Compras/Aprobar_Solicitud/mostrarTabla"></a>';
                      $denegar = '<a class="icono icon-cross denegar_compra" data-url="index.php/Compras/Aprobar_Solicitud/Denegar/'.$sol->id_solicitud_compra.'/" data-url-table="index.php/Compras/Aprobar_Solicitud/mostrarTabla"></a>';
                      $estado = 'POR APROBAR';}

                      if ($sol->nivel_solicitud == 3){
                      $botones='<a class="icono icon-detalle" href="'.base_url('index.php/Compras/Detalle_Solicitud_Compra/index/'.$sol->id_solicitud_compra.'/'.$modulo.'/'.'111'.'/').'"></a>';
                      $actualizar = '<a class="icono icon-pencil" title="Actualizar" onClick="'.$onClick.'"></a>';
                      $aprobar = '<a class="icono icon-denegar"></a>';
                      $denegar = '<a class="icono icon-cross denegar_compra" data-url="index.php/Compras/Aprobar_Solicitud/Denegar/'.$sol->id_solicitud_compra.'/"></a>';
                      $estado = 'APROBADA AUTORIZANTE';}

                      $this->table->add_row($sol->id_solicitud_compra, $solicitante,
                       $sol->fecha_solicitud_compra, $comentario, $estado,$aprobar,
                       $denegar,$botones,$actualizar, $descargar_archivo);

                    } elseif ($sol->nivel_solicitud == 4 || $sol->nivel_solicitud == 5 || $sol->nivel_solicitud == 6 || $sol->nivel_solicitud == 7 || $sol->nivel_solicitud == 8) {
                      $onClick = "llenarFormulario('solicitud', ['id', 'fecha_solicitud', 'numero'],
                      [$sol->id_solicitud_compra, '$sol->fecha_solicitud_compra', '$sol->numero_solicitud_compra'], false, false, false, 'comentario', '$comentario')";

                      $botones='<a class="icono icon-detalle" href="'.base_url('index.php/Compras/Detalle_Solicitud_Compra/index/'.$sol->id_solicitud_compra.'/'.$modulo.'/'.'111'.'/').'"></a>';
                      $actualizar = '<a class="icono icon-denegar"></a>';
                      $aprobar = '<a class="icono icon-denegar"></a>';
                      $denegar = '<a class="icono icon-denegar"></a>';
                      $estado = 'APROBADA';

                      $this->table->add_row($sol->id_solicitud_compra, $solicitante,
                       $sol->fecha_solicitud_compra, $comentario, $estado,$aprobar,
                       $denegar,$botones,$actualizar,$descargar_archivo);

                    } elseif ($sol->nivel_solicitud == 9){
                      if ($sol->nivel_anterior == 2){
                        $estado = $sol->estado_solicitud_compra;
                      }
                      if ($sol->nivel_anterior == 3 || $sol->nivel_anterior == 4){
                        $estado = 'APROBADA';
                      }
                      $onClick = "llenarFormulario('solicitud', ['id', 'fecha_solicitud', 'numero'],
                      [$sol->id_solicitud_compra, '$sol->fecha_solicitud_compra', '$sol->numero_solicitud_compra'], false, false, false, 'comentario', '$comentario')";

                      $botones='<a class="icono icon-detalle" href="'.base_url('index.php/Compras/Detalle_Solicitud_Compra/index/'.$sol->id_solicitud_compra.'/'.$modulo.'/'.'111'.'/').'"></a>';
                      $actualizar = '<a class="icono icon-denegar"></a>';
                      $aprobar = '<a class="icono icon-denegar"></a>';
                      $denegar = '<a class="icono icon-denegar"></a>';

                      $this->table->add_row($sol->id_solicitud_compra, $solicitante,
                       $sol->fecha_solicitud_compra, $comentario, $estado,$aprobar,
                       $denegar,$botones,$actualizar, $descargar_archivo);
                    }
                }elseif ($sol->solicitante == $USER['id_empleado']) {
                if ($sol->nivel_solicitud == 1 || $sol->nivel_solicitud == 2){
                  $onClick = "llenarFormulario('solicitud', ['id', 'fecha_solicitud', 'numero'],
                  [$sol->id_solicitud_compra, '$sol->fecha_solicitud_compra', '$sol->numero_solicitud_compra'], false, false, false, 'comentario', '$comentario')";
                  if ($sol->nivel_solicitud == 1){
                    $botones='<a class="icono icon-detalle" href="'.base_url('index.php/Compras/Detalle_Solicitud_Compra/index/'.$sol->id_solicitud_compra.'/'.$modulo.'/'.$id_rol.'/').'"></a>';
                    $actualizar = '<a class="icono icon-pencil" title="Actualizar" onClick="'.$onClick.'"></a>';
                    $aprobar = '<a class="icono icon-liquidar aprobar_compra" data-url="index.php/Compras/Aprobar_Solicitud/Aprobar/'.$sol->id_solicitud_compra.'/" data-url-table="index.php/Compras/Aprobar_Solicitud/mostrarTabla"></a>';
                    $denegar = '<a class="icono icon-cross denegar_compra" data-url="index.php/Compras/Aprobar_Solicitud/Denegar/'.$sol->id_solicitud_compra.'/" data-url-table="index.php/Compras/Aprobar_Solicitud/mostrarTabla"></a>';
                    $estado = 'POR APROBAR';
                  }

                  if ($sol->nivel_solicitud == 2){
                  $botones='<a class="icono icon-detalle" href="'.base_url('index.php/Compras/Detalle_Solicitud_Compra/index/'.$sol->id_solicitud_compra.'/'.$modulo.'/'.$id_rol.'/').'"></a>';
                //  $eliminar='<a class="icono icon-denegar"></a>';
                  $actualizar = '<a class="icono icon-pencil" title="Actualizar" onClick="'.$onClick.'"></a>';
                  $aprobar = '<a class="icono icon-denegar"></a>';
                  //$denegar = '<a class="icono icon-denegar"></a>';
                  $denegar = '<a class="icono icon-cross denegar_compra" data-url="index.php/Compras/Aprobar_Solicitud/Denegar/'.$sol->id_solicitud_compra.'/" data-url-table="index.php/Compras/Aprobar_Solicitud/mostrarTabla"></a>';
                  $estado = 'APROBADA JEFATURA';}


                  $this->table->add_row($sol->id_solicitud_compra, $solicitante,
                   $sol->fecha_solicitud_compra, $comentario, $estado,$aprobar,
                   $denegar,$botones,$actualizar, $descargar_archivo);

                } elseif ($sol->nivel_solicitud == 3 || $sol->nivel_solicitud == 4 || $sol->nivel_solicitud == 5 || $sol->nivel_solicitud == 6 || $sol->nivel_solicitud == 7 || $sol->nivel_solicitud == 8) {
                  $onClick = "llenarFormulario('solicitud', ['id', 'fecha_solicitud', 'numero'],
                  [$sol->id_solicitud_compra, '$sol->fecha_solicitud_compra', '$sol->numero_solicitud_compra'], false, false, false, 'comentario', '$comentario')";

                  $botones='<a class="icono icon-detalle" href="'.base_url('index.php/Compras/Detalle_Solicitud_Compra/index/'.$sol->id_solicitud_compra.'/'.$modulo.'/'.$id_rol.'/').'"></a>';
                  //$eliminar= '<a class="icono icon-denegar"></a>';
                  $actualizar = '<a class="icono icon-denegar"></a>';
                  $aprobar = '<a class="icono icon-denegar"></a>';
                  $denegar = '<a class="icono icon-denegar"></a>';
                  $estado = 'APROBADA';

                  $this->table->add_row($sol->id_solicitud_compra,$solicitante, $sol->fecha_solicitud_compra, $comentario, $estado,$aprobar,
                   $denegar,$botones,$actualizar, $descargar_archivo);

                } elseif ($sol->nivel_solicitud == 9){
                  if ($sol->nivel_anterior == 1){
                    $estado = $sol->estado_solicitud_compra;
                  }
                  if ($sol->nivel_anterior == 2 || $sol->nivel_anterior == 3){
                    $estado = 'APROBADA';
                  }
                  $onClick = "llenarFormulario('solicitud', ['id', 'fecha_solicitud', 'numero'],
                  [$sol->id_solicitud_compra, '$sol->fecha_solicitud_compra', '$sol->numero_solicitud_compra'], false, false, false, 'comentario', '$comentario')";

                  $botones='<a class="icono icon-detalle" href="'.base_url('index.php/Compras/Detalle_Solicitud_Compra/index/'.$sol->id_solicitud_compra.'/'.$modulo.'/'.$id_rol.'/').'"></a>';
                  //$eliminar= '<a class="icono icon-denegar"></a>';
                  $actualizar = '<a class="icono icon-denegar"></a>';
                  $aprobar = '<a class="icono icon-denegar"></a>';
                  $denegar = '<a class="icono icon-denegar"></a>';

                  $this->table->add_row($sol->id_solicitud_compra, $solicitante,
                   $sol->fecha_solicitud_compra, $comentario, $estado,$aprobar,
                   $denegar,$botones,$actualizar, $descargar_archivo);

                }
              }
  //CONTROL DE PERMISOS DE COLABORADORES
              elseif ($USER['rol'] == 'COLABORADOR AF' || $USER['rol'] == 'COLABORADOR BODEGA' || $USER['rol'] == 'COLABORADOR COMPRAS' || $USER['rol'] == 'COLABORADOR UACI') {
                if ($sol->nivel_solicitud == 1 || $sol->nivel_solicitud == 2){
                  $onClick = "llenarFormulario('solicitud', ['id', 'fecha_solicitud', 'numero'],
                  [$sol->id_solicitud_compra, '$sol->fecha_solicitud_compra', '$sol->numero_solicitud_compra'], false, false, false, 'comentario', '$comentario')";

                  if ($sol->nivel_solicitud == 1){
                  $botones='<a class="icono icon-detalle" href="'.base_url('index.php/Compras/Detalle_Solicitud_Compra/index/'.$sol->id_solicitud_compra.'/'.$modulo.'/'.$id_rol.'/').'"></a>';
                  //$eliminar='<a class="icono icon-eliminar" uri='.base_url('index.php/Compras/Aprobar_Solicitud/EliminarDato/'.$sol->id_solicitud_compra).'></a>';
                  $actualizar = '<a class="icono icon-pencil" title="Actualizar" onClick="'.$onClick.'"></a>';
                  $aprobar = '<a class="icono icon-denegar"></a>';
                  $denegar = '<a class="icono icon-denegar"></a>';
                  $estado = 'POR APROBAR';}

                  if ($sol->nivel_solicitud == 2){
                  $botones='<a class="icono icon-detalle" href="'.base_url('index.php/Compras/Detalle_Solicitud_Compra/index/'.$sol->id_solicitud_compra.'/'.$modulo.'/'.$id_rol.'/').'"></a>';
                  //$eliminar='<a class="icono icon-denegar"></a>';
                  $actualizar = '<a class="icono icon-pencil" title="Actualizar" onClick="'.$onClick.'"></a>';
                  $aprobar = '<a class="icono icon-denegar"></a>';
                  $denegar = '<a class="icono icon-denegar"></a>';
                  //$denegar = '<a class="icono icon-cross" href="'.base_url('index.php/Compras/Aprobar_Solicitud/Denegar/'.$sol->id_solicitud_compra.'/').'"></a>';
                  $estado = 'APROBADA JEFATURA';}


                  $this->table->add_row($sol->id_solicitud_compra, $solicitante,
                   $sol->fecha_solicitud_compra, $comentario, $estado,$aprobar,
                   $denegar,$botones,$actualizar, $descargar_archivo);

                } elseif ($sol->nivel_solicitud == 3 || $sol->nivel_solicitud == 4 || $sol->nivel_solicitud == 5 || $sol->nivel_solicitud == 6 || $sol->nivel_solicitud == 7 || $sol->nivel_solicitud == 8) {
                  $onClick = "llenarFormulario('solicitud', ['id', 'fecha_solicitud', 'numero'],
                  [$sol->id_solicitud_compra, '$sol->fecha_solicitud_compra', '$sol->numero_solicitud_compra'], false, false, false, 'comentario', '$comentario')";

                  $botones='<a class="icono icon-detalle" href="'.base_url('index.php/Compras/Detalle_Solicitud_Compra/index/'.$sol->id_solicitud_compra.'/'.$modulo.'/'.$id_rol.'/').'"></a>';
                  //$eliminar= '<a class="icono icon-denegar"></a>';
                  $actualizar = '<a class="icono icon-denegar"></a>';
                  $aprobar = '<a class="icono icon-denegar"></a>';
                  $denegar = '<a class="icono icon-denegar"></a>';
                  $estado = 'APROBADA';

                  $this->table->add_row($sol->id_solicitud_compra, $solicitante,
                   $sol->fecha_solicitud_compra, $comentario, $estado,$aprobar,
                   $denegar,$botones,$actualizar, $descargar_archivo);

                } elseif ($sol->nivel_solicitud == 9){
                  if ($sol->nivel_anterior == 1){
                    $estado = $sol->estado_solicitud_compra;
                  }
                  if ($sol->nivel_anterior == 2 || $sol->nivel_anterior == 3){
                    $estado = 'APROBADA';
                  }
                  $onClick = "llenarFormulario('solicitud', ['id', 'fecha_solicitud', 'numero'],
                  [$sol->id_solicitud_compra, '$sol->fecha_solicitud_compra', '$sol->numero_solicitud_compra'], false, false, false, 'comentario', '$comentario')";

                  $botones='<a class="icono icon-detalle" href="'.base_url('index.php/Compras/Detalle_Solicitud_Compra/index/'.$sol->id_solicitud_compra.'/'.$modulo.'/'.$id_rol.'/').'"></a>';
                  //$eliminar= '<a class="icono icon-denegar"></a>';
                  $actualizar = '<a class="icono icon-denegar"></a>';
                  $aprobar = '<a class="icono icon-denegar"></a>';
                  $denegar = '<a class="icono icon-denegar"></a>';

                  $this->table->add_row($sol->id_solicitud_compra,$solicitante, $sol->fecha_solicitud_compra, $comentario, $estado,$aprobar,
                   $denegar,$botones,$actualizar, $descargar_archivo);
                }
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
        echo "<div class='table-responsive'>" . $this->table->generate() . "</div>" . $pagination;
      } else {
        return "<div class='table-responsive'>" . $this->table->generate() . "</div>" . $pagination;
      }

    } else {
      redirect('login/index/error_no_autenticado');
    }
  }
  public function Aprobar() {
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $id = $this->uri->segment(4);
      $nivel = $this->Solicitud_Compra_Model->obtenerNivelSolicitud($id);
      if ($nivel == 1){
        $data = array(
            'estado_solicitud_compra' => 'APROBADA JEFATURA',
            'nivel_solicitud' => $nivel + 1,
            'comentario_jefe' => strtoupper($this->input->post('comentario'))
        );
      }
      elseif ($nivel == 2){
        $data = array(
            'estado_solicitud_compra' => 'APROBADA AUTORIZANTE',
            'nivel_solicitud' => $nivel + 1,
            'comentario_autorizante' => strtoupper($this->input->post('comentario'))
        );
      }
      $this->Solicitud_Compra_Model->actualizarSolicitudCompra($id,$data);
      $this->Notificacion_model->NotificacionSolicitudCompra($id, $USER, $nivel+1);
      //redirect('/Compras/Aprobar_Solicitud/index/update');
    } else {
      redirect('login/index/error_no_autenticado');
    }

  }


  public function Denegar() {
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $id = $this->uri->segment(4);
      //$estado = $this->Solicitud_Compra_Model->obtenerEstadoSolicitud($id);
      $nivel = $this->Solicitud_Compra_Model->obtenerNivelSolicitud($id);
      if ($this->Solicitud_Compra_Model->esAutorizante($USER['id_seccion'],$USER['id_empleado'])) {
        $data = array(
            'estado_solicitud_compra' => 'DENEGADA',
            'nivel_anterior' => $nivel,
            'nivel_solicitud' => 9,
            'comentario_autorizante' => strtoupper($this->input->post('comentario'))
            );
      }elseif($USER['rol'] == 'ADMINISTRADOR SICBAF' || $USER['rol'] == 'JEFE UNIDAD' ||
       $USER['rol'] == 'JEFE BODEGA' ||  $USER['rol'] == 'JEFE COMPRAS' ||  $USER['rol'] == 'JEFE AF'){
      $data = array(
          'estado_solicitud_compra' => 'DENEGADA',
          'nivel_anterior' => $nivel,
          'nivel_solicitud' => 9,
          'comentario_jefe' => strtoupper($this->input->post('comentario'))
          );
      }
      $this->Solicitud_Compra_Model->actualizarSolicitudCompra($id,$data);
      $this->Notificacion_model->NotificacionSolicitudCompra($id, $USER, 9);
      //redirect('/Compras/Aprobar_Solicitud/index/update');
    } else {
      redirect('login/index/error_no_autenticado');
    }

  }


  public function ActualizarDatos() {
    $modulo=$this->User_model->obtenerModulo('Compras/Aprobar_Solicitud');
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
    $id_seccion=$USER['id_seccion'];
    $botones;
    $id=$this->Solicitud_Compra_Model->obtenerIdSolicitudCompra();
    $sol=$this->Solicitud_Compra_Model->obtenerSolicitudCompleta($this->input->post('id'));
    $comentario='';
    if ($this->Solicitud_Compra_Model->esAutorizante($USER['id_seccion'],$USER['id_empleado'])) {
      $comentario='comentario_autorizante';
    }elseif ($USER['rol'] == 'ADMINISTRADOR SICBAF' || $USER['rol'] == 'JEFE UNIDAD' || $USER['rol'] == 'JEFE BODEGA' || $USER['rol']=='JEFE AF' || $USER['rol'] == 'JEFE COMPRAS' || $USER['rol'] == 'JEFE UACI'
            || $USER['rol'] == 'COLABORADOR BODEGA' || $USER['rol'] == 'COLABORADOR COMPRAS' || $USER['rol'] == 'COLABORADOR AF' || $USER['rol'] == 'COLABORADOR UACI') {
          $comentario='comentario_jefe';
    }
    if($USER){
      $data = array(
          $comentario => $this->input->post('comentario'),
      );

      if (!($this->input->post('id') == '')){
        if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'update')) {

          $this->Solicitud_Compra_Model->actualizarSolicitudCompra($this->input->post('id'),$data);
          $rastrea['operacion']='ACTUALIZA';
          $rastrea['id_registro']=$this->input->post('id');
          $this->User_model->insertarRastreabilidad($rastrea);
          redirect('/Compras/Aprobar_Solicitud/index/update');
        } else {
          redirect('/Compras/Aprobar_Solicitud/index/forbidden');
        }
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  public function EliminarDato(){
    $modulo=$this->User_model->obtenerModulo('Compras/Aprobar_Solicitud');
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
        if ($this->Solicitud_Compra_Model->existeSolicitudCompra($id)){
          redirect('/Compras/Aprobar_Solicitud/index/existeSol');
        }
        else {
          $this->Solicitud_Compra_Model->eliminarSolicitudCompra($id);
          $this->User_model->insertarRastreabilidad($rastrea);
          redirect('/Compras/Aprobar_Solicitud/index/delete');
        }
      } else {
        redirect('/Compras/Aprobar_Solicitud/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  public function descargar_archivo($id) {
    $this->load->helper('download');
    $sol=$this->Solicitud_Compra_Model->obtenerSolicitudCompleta($id);
    $name=$sol->documento_especificaciones;
    force_download("uploads/$name", NULL);
    redirect('/Compras/Aprobar_Solicitud/index/descargado');
  }
}
?>
