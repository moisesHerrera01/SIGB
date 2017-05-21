<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Solicitud_control extends CI_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->helper(array('form', 'paginacion'));
    $this->load->library('table');
    $this->load->model(array('Bodega/Solicitud_Model','Bodega/Detalle_solicitud_producto_model', 'Bodega/Fuentefondos_model', 'Notificacion_model'));
  }

  public function index(){
    if($this->session->userdata('logged_in')){
      $data['title'] = "Control Solicitudes";
      $data['js'] = "assets/js/validate/solc.js";
      $pri=$this->Solicitud_Model->obtenerId();
      $USER = $this->session->userdata('logged_in');
      $id_seccion=$USER['id_seccion'];
      $sec=$this->Solicitud_Model->obtenerSeccion($id_seccion);
      //$data['js'] = "assets/js/validate/factura.js";
      date_default_timezone_set('America/El_Salvador');
      $anyo=20;
      $fecha_actual=date($anyo."y-m-d");
      $msg = array('alert' => $this->uri->segment(4),'fecha'=>$fecha_actual,'id'=>$pri,'controller'=>'solicitud',
      'seccion'=>$sec);

  		$data['body'] = $this->load->view('mensajes', $msg, TRUE) . $this->load->view('Bodega/solicitud_control_view',$msg,TRUE) .
                      "<br><div class='content_table '>" .
                      "<div class='limit-content-title'><span class='icono icon-table icon-title'> Control de Solicitudes</span></div>".
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
      $this->table->set_heading('Id', 'Número', 'Sección', 'Fuente Fondo', 'Fecha', 'Comentario','Estado','Aprobar','Denegar', 'Detalle', 'Editar');

      $nivel = array();
      if ($USER['rol'] == 'DIRECTOR ADMINISTRATIVO') {
        $nivel[] = 2;
        $nivel[] = 3;
        $nivel[] = 4;
        $nivel[] = 9;
        $seccion = 0;
      } elseif ($USER['rol'] == 'JEFE UNIDAD' || $USER['rol'] == 'JEFE BODEGA' || $USER['rol'] == 'COLABORADOR BODEGA' || $USER['rol'] == 'JEFE COMPRAS' || $USER['rol'] == 'COLABORADOR COMPRAS'
                || $USER['rol'] == 'JEFE AF' || $USER['rol'] == 'COLABORADOR AF' || $USER['rol'] == 'JEFE UACI' || $USER['rol'] == 'COLABORADOR UACI') {
        $nivel[] = 1;
        $nivel[] = 2;
        $nivel[] = 3;
        $nivel[] = 4;
        $nivel[] = 9;
        $seccion = $USER['id_seccion'];
      } elseif ($USER['rol'] == 'ADMINISTRADOR SICBAF') {
        $nivel[] = 0;
        $nivel[] = 1;
        $nivel[] = 2;
        $nivel[] = 3;
        $nivel[] = 4;
        $nivel[] = 9;
        $seccion = 108;
        $this->table->set_heading('Id', 'Número', 'Sección', 'Fuente Fondo', 'Fecha', 'Comentario', 'Estado', 'Nivel','Aprobar','Denegar', 'Detalle', 'Editar');
      }
      $num = '10';
      $pagination = '';
      $registros;
      if ($this->input->is_ajax_request()) {
        if (!($this->input->post('busca') == "")) {
            $registros = $this->Solicitud_Model->buscarSolicitudes($this->input->post('busca'));
        } else {
            $registros = $this->Solicitud_Model->obtenerSolicitudesEstadoLimit($nivel, $seccion, $num, $this->uri->segment(4));
            $pagination = paginacion('index.php/Bodega/Solicitud_control/index/', $this->Solicitud_Model->totalSolicitudes($USER['id_seccion'])->total,
                          $num, '4');
        }
      } else {
            $registros = $this->Solicitud_Model->obtenerSolicitudesEstadoLimit($nivel, $seccion, $num, $this->uri->segment(4));
            $pagination = paginacion('index.php/Bodega/Solicitud_control/index/', $this->Solicitud_Model->totalSolicitudes($USER['id_seccion'])->total,
                          $num, '4');
      }

      /*
      * llena la tabla con los datos consultados
      */
      if (!($registros == FALSE)) {
        foreach($registros as $sol) {
          $id_rol = $USER['rol'];
          $comentario='';
          if ($USER['rol'] == 'JEFE UNIDAD' || $USER['rol'] == 'JEFE BODEGA'  || $USER['rol'] == 'COLABORADOR BODEGA'  || $USER['rol'] == 'JEFE COMPRAS' || $USER['rol'] == 'COLABORADOR COMPRAS' ||
              $USER['rol'] == 'JEFE AF'|| $USER['rol'] == 'COLABORADOR AF' || $USER['rol'] == 'JEFE UACI' || $USER['rol'] == 'COLABORADOR UACI') {
            $comentario=$sol->comentario_jefe;
          }elseif ($USER['rol'] == 'DIRECTOR ADMINISTRATIVO') {
            $comentario=$sol->comentario_admin;
          }
            $seccion = $this->Solicitud_Model->obtenerSeccion($sol->id_seccion);
            $fuente = $this->Fuentefondos_model->obtenerFuente($sol->id_fuentes);

            if ($USER['rol'] == 'ADMINISTRADOR SICBAF') {
              if ($sol->nivel_solicitud == 0 ||  $sol->nivel_solicitud == 3 || $sol->nivel_solicitud == 4 || $sol->nivel_solicitud == 9){
                $onClick = "llenarFormulario('solicitud', ['id', 'fecha_solicitud', 'id_seccion', 'numero_solicitud', 'nivel'],
                            [$sol->id_solicitud, '$sol->fecha_solicitud', '$seccion', '$sol->numero_solicitud',
                            '$sol->nivel_solicitud'],false,false,false,'comentario_solicitud','$sol->comentario_jefe')";

                            $botones='<a class="icono icon-detalle" href="'.base_url('index.php/Bodega/Detalle_Solicitud_Control/index/'.$sol->id_solicitud.'/').'"></a>';
                            //$eliminar='<a class="icono icon-denegar"></a>';
                            $actualizar = '<a class="icono icon-denegar"></a>';
                            $aprobar = '<a class="icono icon-denegar"></a>';
                            $denegar = '<a class="icono icon-denegar"></a>';
                            $estado = $sol->estado_solicitud;

                            $this->table->add_row($sol->id_solicitud, $sol->numero_solicitud, $seccion, $fuente, $sol->fecha_solicitud, $comentario,
                            $estado,$sol->nivel_solicitud, $aprobar,$denegar,$botones,$actualizar);

              } else {
                $onClick = "llenarFormulario('solicitud', ['id', 'fecha_solicitud', 'id_seccion', 'numero_solicitud', 'nivel'],
                            [$sol->id_solicitud, '$sol->fecha_solicitud', '$seccion', '$sol->numero_solicitud',
                            '$sol->nivel_solicitud'],false,false,false,'comentario_solicitud','$sol->comentario_jefe')";

                            $botones='<a class="icono icon-detalle" href="'.base_url('index.php/Bodega/Detalle_Solicitud_Control/index/'.$sol->id_solicitud.'/').'"></a>';
                            //$eliminar='<a class="icono icon-eliminar" uri='.base_url('index.php/Bodega/solicitud/EliminarDato/'.$sol->id_solicitud).'></a>';
                            $actualizar = '<a class="icono icon-actualizar" title="Actualizar" onClick="'.$onClick.'"></a>';
                            $aprobar = '<a class="icono icon-liquidar" href="'.base_url('index.php/Bodega/Solicitud_control/Aprobar/'.$sol->id_solicitud.'/').'"></a>';
                            $denegar = '<a class="icono icon-cross" href="'.base_url('index.php/Bodega/Solicitud_control/Denegar/'.$sol->id_solicitud.'/').'"></a>';
                            $estado = $sol->estado_solicitud;

                            $this->table->add_row($sol->id_solicitud, $sol->numero_solicitud, $seccion, $fuente, $sol->fecha_solicitud, $comentario
                            ,$estado,$sol->nivel_solicitud, $aprobar,$denegar,$botones,$actualizar);
              }
            }
              if ($USER['rol'] == 'JEFE UNIDAD' || $USER['rol'] == 'JEFE BODEGA' || $USER['rol'] == 'JEFE COMPRAS' || $USER['rol'] == 'JEFE AF' || $USER['rol'] == 'JEFE UACI'){
                if ($sol->nivel_solicitud == 1){
                  $onClick = "llenarFormulario('solicitud', ['id', 'fecha_solicitud', 'id_seccion', 'numero_solicitud', 'nivel'],
                              [$sol->id_solicitud, '$sol->fecha_solicitud', '$seccion', '$sol->numero_solicitud',
                               '$sol->nivel_solicitud'],false,false,false,'comentario_solicitud','$sol->comentario_jefe')";

                              $botones='<a class="icono icon-detalle" href="'.base_url('index.php/Bodega/Detalle_Solicitud_Control/index/'.$sol->id_solicitud.'/'.$id_rol.'/').'"></a>';
                              //$eliminar='<a class="icono icon-eliminar" uri='.base_url('index.php/Bodega/solicitud/EliminarDato/'.$sol->id_solicitud).'></a>';
                              $actualizar = '<a class="icono icon-actualizar" title="Actualizar" onClick="'.$onClick.'"></a>';
                              $aprobar = '<a class="icono icon-liquidar" href="'.base_url('index.php/Bodega/Solicitud_control/Aprobar/'.$sol->id_solicitud.'/').'"></a>';
                              $denegar = '<a class="icono icon-cross" href="'.base_url('index.php/Bodega/Solicitud_control/Denegar/'.$sol->id_solicitud.'/').'"></a>';
                              $estado = 'POR APROBAR';

                              $this->table->add_row($sol->id_solicitud, $sol->numero_solicitud, $seccion, $fuente, $sol->fecha_solicitud, $comentario,
                              $estado,$aprobar,$denegar,$botones,$actualizar);

                } elseif ($sol->nivel_solicitud == 2){
                  $onClick = "llenarFormulario('solicitud', ['id', 'fecha_solicitud', 'id_seccion', 'numero_solicitud', 'nivel'],
                              [$sol->id_solicitud, '$sol->fecha_solicitud', '$seccion', '$sol->numero_solicitud', '$sol->nivel_solicitud'],false,false,false,'comentario_solicitud','$sol->comentario_jefe')";

                              $botones='<a class="icono icon-detalle" href="'.base_url('index.php/Bodega/Detalle_Solicitud_Control/index/'.$sol->id_solicitud.'/'.$id_rol.'/').'"></a>';
                              //$eliminar='<a class="icono icon-denegar"></a>';
                              $actualizar = '<a class="icono icon-actualizar" title="Actualizar" onClick="'.$onClick.'"></a>';
                              $aprobar = '<a class="icono icon-denegar"></a>';
                              $denegar = '<a class="icono icon-cross" href="'.base_url('index.php/Bodega/Solicitud_control/Denegar/'.$sol->id_solicitud.'/').'"></a>';
                              $estado = 'APROBADA';

                              $this->table->add_row($sol->id_solicitud, $sol->numero_solicitud, $seccion, $fuente, $sol->fecha_solicitud, $comentario,
                              $estado,$aprobar,$denegar,$botones,$actualizar);

                } elseif ($sol->nivel_solicitud == 3 || $sol->nivel_solicitud == 4){
                  $onClick = "llenarFormulario('solicitud', ['id', 'fecha_solicitud', 'id_seccion', 'numero_solicitud', 'comentario_solicitud', 'nivel'],
                              [$sol->id_solicitud, '$sol->fecha_solicitud', '$seccion', '$sol->numero_solicitud', '$comentario', '$sol->nivel_solicitud'])";

                              $botones='<a class="icono icon-detalle" href="'.base_url('index.php/Bodega/Detalle_Solicitud_Control/index/'.$sol->id_solicitud.'/'.$id_rol.'/').'"></a>';
                              //$eliminar='<a class="icono icon-denegar"></a>';
                              $actualizar = '<a class="icono icon-denegar"></a>';
                              $aprobar = '<a class="icono icon-denegar"></a>';
                              $denegar = '<a class="icono icon-denegar"></a>';
                              $estado = 'APROBADA';

                              $this->table->add_row($sol->id_solicitud, $sol->numero_solicitud, $seccion, $fuente, $sol->fecha_solicitud, $comentario,
                              $estado,$aprobar,$denegar,$botones,$actualizar);

                } elseif ($sol->nivel_solicitud == 9) {
                  if ($sol->nivel_anterior == 1){
                    $estado = $sol->estado_solicitud;
                  }
                  if ($sol->nivel_anterior == 2){
                    $estado = 'APROBADA';
                  }

                    $onClick = "llenarFormulario('solicitud', ['id', 'fecha_solicitud', 'id_seccion', 'numero_solicitud', 'comentario_solicitud', 'nivel'],
                                [$sol->id_solicitud, '$sol->fecha_solicitud', '$seccion', '$sol->numero_solicitud', '$sol->comentario', '$sol->nivel_solicitud'])";

                                $botones='<a class="icono icon-detalle" href="'.base_url('index.php/Bodega/Detalle_Solicitud_Control/index/'.$sol->id_solicitud.'/'.$id_rol.'/').'"></a>';
                                //$eliminar='<a class="icono icon-denegar"></a>';
                                $actualizar = '<a class="icono icon-denegar"></a>';
                                $aprobar = '<a class="icono icon-denegar"></a>';
                                $denegar = '<a class="icono icon-denegar"></a>';

                                $this->table->add_row($sol->id_solicitud, $sol->numero_solicitud, $seccion, $fuente, $sol->fecha_solicitud, $comentario,
                                $estado,$aprobar,$denegar,$botones,$actualizar);
                  }

                }
                //control de permisos de colaboradores

                if ($USER['rol'] == 'COLABORADOR BODEGA' || $USER['rol'] == 'COLABORADOR COMPRAS' || $USER['rol'] == 'COLABORADOR AF' || $USER['rol'] == 'COLABORADOR UACI'){
                  if ($sol->nivel_solicitud == 1){
                    $onClick = "llenarFormulario('solicitud', ['id', 'fecha_solicitud', 'id_seccion', 'numero_solicitud', 'nivel'],
                                [$sol->id_solicitud, '$sol->fecha_solicitud', '$seccion', '$sol->numero_solicitud',
                                 '$sol->nivel_solicitud'],false,false,false,'comentario_solicitud','$sol->comentario_jefe')";

                                $botones='<a class="icono icon-detalle" href="'.base_url('index.php/Bodega/Detalle_Solicitud_Control/index/'.$sol->id_solicitud.'/'.$id_rol.'/').'"></a>';
                                //$eliminar='<a class="icono icon-eliminar" uri='.base_url('index.php/Bodega/solicitud/EliminarDato/'.$sol->id_solicitud).'></a>';
                                $actualizar = '<a class="icono icon-actualizar" title="Actualizar" onClick="'.$onClick.'"></a>';
                                $aprobar = '<a class="icono icon-denegar"></a>';
                                $denegar = '<a class="icono icon-denegar"></a>';
                                $estado = 'POR APROBAR';

                                $this->table->add_row($sol->id_solicitud, $sol->numero_solicitud, $seccion, $fuente, $sol->fecha_solicitud, $comentario,
                                $estado,$aprobar,$denegar,$botones,$actualizar);

                  } elseif ($sol->nivel_solicitud == 2){
                    $onClick = "llenarFormulario('solicitud', ['id', 'fecha_solicitud', 'id_seccion', 'numero_solicitud', 'nivel'],
                                [$sol->id_solicitud, '$sol->fecha_solicitud', '$seccion', '$sol->numero_solicitud', '$sol->nivel_solicitud'],false,false,false,'comentario_solicitud','$sol->comentario_jefe')";

                                $botones='<a class="icono icon-detalle" href="'.base_url('index.php/Bodega/Detalle_Solicitud_Control/index/'.$sol->id_solicitud.'/'.$id_rol.'/').'"></a>';
                                //$eliminar='<a class="icono icon-denegar"></a>';
                                $actualizar = '<a class="icono icon-actualizar" title="Actualizar" onClick="'.$onClick.'"></a>';
                                $aprobar = '<a class="icono icon-denegar"></a>';
                                $denegar = '<a class="icono icon-denegar"></a>';
                                $estado = 'APROBADA';

                                $this->table->add_row($sol->id_solicitud, $sol->numero_solicitud, $seccion, $fuente, $sol->fecha_solicitud, $comentario,
                                $estado,$aprobar,$denegar,$botones,$actualizar);

                  } elseif ($sol->nivel_solicitud == 3 || $sol->nivel_solicitud == 4){
                    $onClick = "llenarFormulario('solicitud', ['id', 'fecha_solicitud', 'id_seccion', 'numero_solicitud', 'comentario_solicitud', 'nivel'],
                                [$sol->id_solicitud, '$sol->fecha_solicitud', '$seccion', '$sol->numero_solicitud', '$comentario', '$sol->nivel_solicitud'])";

                                $botones='<a class="icono icon-detalle" href="'.base_url('index.php/Bodega/Detalle_Solicitud_Control/index/'.$sol->id_solicitud.'/'.$id_rol.'/').'"></a>';
                                //$eliminar='<a class="icono icon-denegar"></a>';
                                $actualizar = '<a class="icono icon-denegar"></a>';
                                $aprobar = '<a class="icono icon-denegar"></a>';
                                $denegar = '<a class="icono icon-denegar"></a>';
                                $estado = 'APROBADA';

                                $this->table->add_row($sol->id_solicitud, $sol->numero_solicitud, $seccion, $fuente, $sol->fecha_solicitud, $comentario,
                                $estado,$aprobar,$denegar,$botones,$actualizar);

                  } elseif ($sol->nivel_solicitud == 9) {
                    if ($sol->nivel_anterior == 1){
                      $estado = $sol->estado_solicitud;
                    }
                    if ($sol->nivel_anterior == 2){
                      $estado = 'APROBADA';
                    }

                      $onClick = "llenarFormulario('solicitud', ['id', 'fecha_solicitud', 'id_seccion', 'numero_solicitud', 'comentario_solicitud', 'nivel'],
                                  [$sol->id_solicitud, '$sol->fecha_solicitud', '$seccion', '$sol->numero_solicitud', '$sol->comentario', '$sol->nivel_solicitud'])";

                                  $botones='<a class="icono icon-detalle" href="'.base_url('index.php/Bodega/Detalle_Solicitud_Control/index/'.$sol->id_solicitud.'/'.$id_rol.'/').'"></a>';
                                  //$eliminar='<a class="icono icon-denegar"></a>';
                                  $actualizar = '<a class="icono icon-denegar"></a>';
                                  $aprobar = '<a class="icono icon-denegar"></a>';
                                  $denegar = '<a class="icono icon-denegar"></a>';

                                  $this->table->add_row($sol->id_solicitud, $sol->numero_solicitud, $seccion, $fuente, $sol->fecha_solicitud, $comentario,
                                  $estado,$aprobar,$denegar,$botones,$actualizar);
                    }

                  }


              if ($USER['rol'] == 'DIRECTOR ADMINISTRATIVO'){
                if ($sol->nivel_solicitud == 2){
                  $onClick = "llenarFormulario('solicitud', ['id', 'fecha_solicitud', 'id_seccion', 'numero_solicitud', 'nivel'],
                              [$sol->id_solicitud, '$sol->fecha_solicitud', '$seccion', '$sol->numero_solicitud', '$sol->nivel_solicitud'],
                              false,false,false,'comentario_solicitud','$sol->comentario_admin')";

                              $botones='<a class="icono icon-detalle" href="'.base_url('index.php/Bodega/Detalle_Solicitud_Control/index/'.$sol->id_solicitud.'/'.$id_rol.'/').'"></a>';
                              //$eliminar='<a class="icono icon-eliminar" uri='.base_url('index.php/Bodega/solicitud/EliminarDato/'.$sol->id_solicitud).'></a>';
                              $actualizar = '<a class="icono icon-actualizar" title="Actualizar" onClick="'.$onClick.'"></a>';
                              $aprobar = '<a class="icono icon-liquidar" href="'.base_url('index.php/Bodega/Solicitud_control/Aprobar/'.$sol->id_solicitud.'/').'"></a>';
                              $denegar = '<a class="icono icon-cross" href="'.base_url('index.php/Bodega/Solicitud_control/Denegar/'.$sol->id_solicitud.'/').'"></a>';
                              $estado = 'POR APROBAR';

                              $this->table->add_row($sol->id_solicitud, $sol->numero_solicitud, $seccion, $fuente, $sol->fecha_solicitud, $comentario,
                              $estado,$aprobar,$denegar,$botones,$actualizar);
                }
                if  ($sol->nivel_solicitud == 3){
                  $onClick = "llenarFormulario('solicitud', ['id', 'fecha_solicitud', 'id_seccion', 'numero_solicitud', 'nivel'],
                              [$sol->id_solicitud, '$sol->fecha_solicitud', '$seccion', '$sol->numero_solicitud', '$sol->nivel_solicitud'],
                              false,false,false,'comentario_solicitud','$sol->comentario_admin')";

                              $botones='<a class="icono icon-detalle" href="'.base_url('index.php/Bodega/Detalle_Solicitud_Control/index/'.$sol->id_solicitud.'/'.$id_rol.'/').'"></a>';
                              //$eliminar='<a class="icono icon-denegar"></a>';
                              $actualizar = '<a class="icono icon-actualizar" title="Actualizar" onClick="'.$onClick.'"></a>';
                              $aprobar = '<a class="icono icon-denegar"></a>';
                              $denegar = '<a class="icono icon-cross" href="'.base_url('index.php/Bodega/Solicitud_control/Denegar/'.$sol->id_solicitud.'/').'"></a>';
                              $estado = 'APROBADA';

                              $this->table->add_row($sol->id_solicitud, $sol->numero_solicitud, $seccion, $fuente, $sol->fecha_solicitud, $comentario,
                              $estado,$aprobar,$denegar,$botones,$actualizar);

                }if  ($sol->nivel_solicitud == 4){
                  $onClick = "llenarFormulario('solicitud', ['id', 'fecha_solicitud', 'id_seccion', 'numero_solicitud', 'comentario_solicitud', 'nivel'],
                              [$sol->id_solicitud, '$sol->fecha_solicitud', '$seccion', '$sol->numero_solicitud', '$comentario', '$sol->nivel_solicitud'])";

                              $botones='<a class="icono icon-detalle" href="'.base_url('index.php/Bodega/Detalle_Solicitud_Control/index/'.$sol->id_solicitud.'/'.$id_rol.'/').'"></a>';
                              //$eliminar='<a class="icono icon-denegar"></a>';
                              $actualizar = '<a class="icono icon-denegar"></a>';
                              $aprobar = '<a class="icono icon-denegar"></a>';
                              $denegar = '<a class="icono icon-denegar"></a>';
                              $estado = 'APROBADA';

                              $this->table->add_row($sol->id_solicitud, $sol->numero_solicitud, $seccion, $fuente, $sol->fecha_solicitud, $comentario,
                              $estado,$aprobar,$denegar,$botones,$actualizar);

                }
                if ($sol->nivel_solicitud == 9 && $sol->nivel_anterior == 2) {


                    $onClick = "llenarFormulario('solicitud', ['id', 'fecha_solicitud', 'id_seccion', 'numero_solicitud', 'comentario_solicitud', 'nivel'],
                                [$sol->id_solicitud, '$sol->fecha_solicitud', '$seccion', '$sol->numero_solicitud', '$sol->comentario', '$sol->nivel_solicitud'])";

                                $botones='<a class="icono icon-detalle" href="'.base_url('index.php/Bodega/Detalle_Solicitud_Control/index/'.$sol->id_solicitud.'/').'"></a>';
                                //$eliminar='<a class="icono icon-denegar"></a>';
                                $actualizar = '<a class="icono icon-denegar"></a>';
                                $aprobar = '<a class="icono icon-denegar"></a>';
                                $denegar = '<a class="icono icon-denegar"></a>';
                                $estado = $sol->estado_solicitud;

                                $this->table->add_row($sol->id_solicitud, $sol->numero_solicitud, $seccion, $fuente, $sol->fecha_solicitud, $comentario,
                                $estado,$aprobar,$denegar,$botones,$actualizar);
                  }

                }
              }

    } else {
        $msg = array('data' => "Texto no encontrado", 'colspan' => "9");
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
      $estado = $this->Solicitud_Model->retornarEstado($id);
      $nivel = $this->Solicitud_Model->retornarNivel($id);
      if ($estado == 'ENVIADA' || $estado == 'APROBADA'){
        $data = array(
            'estado_solicitud' => 'APROBADA',
            'nivel_solicitud' => $nivel + 1
        );
      }
      $this->Solicitud_Model->actualizarSolicitud($id,$data);
      $this->Notificacion_model->NotificacionSolicitudBodega($id, $USER, $nivel + 1);
      redirect('/Bodega/Solicitud_control/index/update');
    } else {
      redirect('login/index/error_no_autenticado');
    }

  }

  public function Denegar() {
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $id = $this->uri->segment(4);
      $nivel = $this->Solicitud_Model->retornarNivel($id);
      $data = array(
          'estado_solicitud' => 'DENEGADA',
          'nivel_anterior' => $nivel,
          'nivel_solicitud' => 9
      );
      $this->Solicitud_Model->actualizarSolicitud($id,$data);
      $this->Notificacion_Model->NotificacionSolicitudBodega($id, $USER, 9);
      redirect('/Bodega/Solicitud_control/index/update');
    } else {
      redirect('login/index/error_no_autenticado');
    }

  }

  public function ActualizarDatos() {
    $modulo=$this->User_model->obtenerModulo('Bodega/Solicitud_control');
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
    $nivel_solicitud = $this->Solicitud_Model->retornarNivel($this->input->post('id'));
    $comentario='';
    if ($USER['rol'] == 'JEFE UNIDAD' || $USER['rol'] == 'JEFE BODEGA' || $USER['rol'] == 'JEFE COMPRAS' || $USER['rol'] == 'JEFE UACI' || $USER['rol'] == 'JEFE AF'
        || $USER['rol'] == 'COLABORADOR BODEGA' || $USER['rol'] == 'COLABORADOR COMPRAS' || $USER['rol'] == 'COLABORADOR UACI' || $USER['rol'] == 'COLABORADOR AF') {                   //verificar act para colaboradores
      $comentario='comentario_jefe';
    }elseif ($USER['rol'] == 'DIRECTOR ADMINISTRATIVO') {
      $comentario='comentario_admin';
    }
    if($USER){
      $data = array(
          $comentario => $this->input->post('comentario_solicitud'),
      );

      if (!($this->input->post('id') == '')){
        if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'update')) {
          $rastrea['operacion']='ACTUALIZA';
          $rastrea['id_registro']=$this->input->post('id');
          $this->User_model->insertarRastreabilidad($rastrea);
          $this->Solicitud_Model->actualizarSolicitud($this->input->post('id'),$data);
          redirect('/Bodega/Solicitud_control/index/update');
        } else {
          redirect('/Bodega/Solicitud_control/index/forbidden');
        }
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }
}
?>
