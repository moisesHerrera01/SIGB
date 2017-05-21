<?php
  class Notificacion_model extends CI_Model{

    public $emisor;
    public $receptor;
    public $mensaje_notificacion;
    public $url_notificacion;
    public $clase_notificacion;

    function __construct() {
        parent::__construct();
        $this->load->model(array('User_model', 'Bodega/Solicitud_Model', 'Compras/Solicitud_Compra_Model', 'User_model'));
    }

    public function insertarNotificacion($data){

        $this->emisor = $data['emisor'];
        $this->receptor = $data['receptor'];
        $this->mensaje_notificacion = $data['mensaje_notificacion'];
        $this->url_notificacion = $data['url_notificacion'];
        $this->clase_notificacion = $data['clase_notificacion'];

        $this->db->insert('sic_notificacion', $this);
        return $this->db->insert_id();
    }

    public function eliminarNotificacion($id) {
      $this->db->delete('sic_notificacion', array('id_notificacion' => $id));
    }

    public function obtenerNotificaciones($id_usuario) {
      $this->db->from('sic_notificacion')
               ->where('receptor', $id_usuario)
               ->order_by("id_notificacion", "asc");
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
        return $query->result_array();
      } else {
        return FALSE;
      }
    }

    public function totalNotificacion($id_usuario) {
      $this->db->from('sic_notificacion')
               ->where('receptor', $id_usuario);
      return $this->db->count_all_results();
    }

    public function EnviarNotificacion($data) {

      if (is_array($data)) {

        $nombre_emisor = $data['nombre_emisor'];
        $correo_emisor = $data['correo_emisor'];
        $id_emisor = $data['id_emisor'];
        $correo_receptor = $data['correo_receptor'];
        $id_receptor = $data['id_receptor'];
        $asunto = $data['asunto'];
        $mensaje = $data['mensaje'];
        $url = $data['url'];

        if ('' != $id_emisor && '' != $id_receptor && '' != $mensaje && '' != $url) {
          $not = array(
            'emisor' => $id_emisor,
            'receptor' => $id_receptor,
            'mensaje_notificacion' => $mensaje,
            'url_notificacion' => $url,
            'clase_notificacion' => 'success',
          );
          $this->insertarNotificacion($not);
        }

        if ('' != $nombre_emisor && '' != $correo_receptor && '' != $asunto && '' != $mensaje) {
          $this->load->library('email');

          $config = array(
            'protocol' => 'sendmail',
            'mailtype' => 'html'
          );

          $this->email->initialize($config);

          $this->email->from($nombre_emisor);
          $this->email->to($correo_receptor);
          $this->email->subject('SICBAF: ' . $asunto);
          $this->email->message('<h2>' . $mensaje . '</h2>');

          $this->email->send();
        }
      }

    }

    // como emisor recibe a $USER de la sesion
    public function NotificacionSolicitudBodega($id_solicitud, $emisor, $nivel) {
      $emisor = $this->User_model->obtenerUsuario($emisor['id']);
      $data = array();

      $roles = $this->User_model->obtenerRolesSistema();

      switch ($nivel) {
        case 1:
          // enviar a jefe unidad
          $receptor = $this->User_model->obtenerCorreoUsuario($roles[4]['id_rol'], $emisor->id_seccion);
          if ($receptor) {

            $data[0]['nombre_emisor'] = $emisor->nombre_completo;
            $data[0]['correo_emisor'] = $emisor->correo;
            $data[0]['id_emisor'] = $emisor->id_usuario;
            $data[0]['correo_receptor'] = $receptor->correo;
            $data[0]['id_receptor'] = $receptor->id_usuario;
            $data[0]['mensaje'] = "Hay una nueva solicitud que require atención con id " . $id_solicitud;
            $data[0]['asunto'] = 'Solicitud Bodega';
            $data[0]['url'] = base_url("index.php/Bodega/Solicitud_control");
          }

          break;
        case 2:
          // enviar a DA
          $receptor = $this->User_model->obtenerCorreoUsuario($roles[1]['id_rol'], 36);
          if ($receptor) {

            $data[0]['nombre_emisor'] = $emisor->nombre_completo;
            $data[0]['correo_emisor'] = $emisor->correo;
            $data[0]['id_emisor'] = $emisor->id_usuario;
            $data[0]['correo_receptor'] = $receptor->correo;
            $data[0]['id_receptor'] = $receptor->id_usuario;
            $data[0]['mensaje'] = "Hay una nueva solicitud que require atención con id " . $id_solicitud;
            $data[0]['asunto'] = 'Solicitud Bodega';
            $data[0]['url'] = base_url("index.php/Bodega/Solicitud_control");
          }

          // solicitante
          $solicitud = $this->Solicitud_Model->obtenerTodaSolicitud($id_solicitud);
          $receptor = $this->User_model->obtenerUsuario($solicitud[0]->id_usuario);
          if ($receptor) {

            $data[1]['nombre_emisor'] = $emisor->nombre_completo;
            $data[1]['correo_emisor'] = $emisor->correo;
            $data[1]['id_emisor'] = $emisor->id_usuario;
            $data[1]['correo_receptor'] = $receptor->correo;
            $data[1]['id_receptor'] = $receptor->id_usuario;
            $data[1]['mensaje'] = "La solicitud id " . $id_solicitud . " ha sido aprobada por su Jefe.";
            $data[1]['asunto'] = 'Solicitud Bodega';
            $data[1]['url'] = base_url("index.php/Bodega/Solicitud_control");
          }
          break;
        case 3:
          # enviar a Bodega
          $receptor = $this->User_model->obtenerCorreoUsuario($roles[3]['id_rol'], 72);
          if ($receptor) {

            $data[0]['nombre_emisor'] = $emisor->nombre_completo;
            $data[0]['correo_emisor'] = $emisor->correo;
            $data[0]['id_emisor'] = $emisor->id_usuario;
            $data[0]['correo_receptor'] = $receptor->correo;
            $data[0]['id_receptor'] = $receptor->id_usuario;
            $data[0]['asunto'] = "Hay una nueva solicitud que require atención con id " . $id_solicitud;
            $data[0]['mensaje'] = 'Solicitud Bodega';
            $data[0]['url'] = base_url("index.php/Bodega/Solicitud_control");
          }

          // solicitante
          $solicitud = $this->Solicitud_Model->obtenerTodaSolicitud($id_solicitud);
          $receptor = $this->User_model->obtenerUsuario($solicitud[0]->id_usuario);
          if ($receptor) {

            $data[1]['nombre_emisor'] = $emisor->nombre_completo;
            $data[1]['correo_emisor'] = $emisor->correo;
            $data[1]['id_emisor'] = $emisor->id_usuario;
            $data[1]['correo_receptor'] = $receptor->correo;
            $data[1]['id_receptor'] = $receptor->id_usuario;
            $data[1]['mensaje'] = "La solicitud id " . $id_solicitud . " ha sido aprobada por Directora Administrativa.";
            $data[1]['asunto'] = 'Solicitud Bodega';
            $data[1]['url'] = base_url("index.php/Bodega/Solicitud_control");
          }
          break;
        case 4:
          # enviar a solicitante
          $solicitud = $this->Solicitud_Model->obtenerTodaSolicitud($id_solicitud);
          $receptor = $this->User_model->obtenerUsuario($solicitud[0]->id_usuario);
          if ($receptor) {

            $data[0]['nombre_emisor'] = $emisor->nombre_completo;
            $data[0]['correo_emisor'] = $emisor->correo;
            $data[0]['id_emisor'] = $emisor->id_usuario;
            $data[0]['correo_receptor'] = $receptor->correo;
            $data[0]['id_receptor'] = $receptor->id_usuario;
            $data[0]['mensaje'] = "La solicitud id " . $id_solicitud . " ha sido liquidada.";
            $data[0]['asunto'] = 'Solicitud Bodega';
            $data[0]['url'] = base_url("index.php/Bodega/Solicitud_control");
          }

          break;
        case 9:
          # enviar a solicitante
          $solicitud = $this->Solicitud_Model->obtenerTodaSolicitud($id_solicitud);
          $receptor = $this->User_model->obtenerUsuario($solicitud[0]->id_usuario);
          if ($receptor) {

            $data[0]['nombre_emisor'] = $emisor->nombre_completo;
            $data[0]['correo_emisor'] = $emisor->correo;
            $data[0]['id_emisor'] = $emisor->id_usuario;
            $data[0]['correo_receptor'] = $receptor->correo;
            $data[0]['id_receptor'] = $receptor->id_usuario;
            $data[0]['mensaje'] = "La solicitud id " . $id_solicitud . " no ha sido aprobada.";
            $data[0]['asunto'] = 'Solicitud Bodega';
            $data[0]['url'] = base_url("index.php/Bodega/Solicitud_control");
          }
          break;
      }

      foreach ($data as $dato) {
        $this->EnviarNotificacion($dato);
      }
    }

    public function NotificacionSolicitudCompra($id_solicitud, $emisor, $nivel){

      $emisor = $this->User_model->obtenerUsuario($emisor['id']);
      $data = array();
      $roles = $this->User_model->obtenerRolesSistema();

      $modulo = $this->User_model->obtenerModulo('Compras/Solicitud_Compra');

      if ($emisor) {
        switch ($nivel) {
          case 1:
            # enviar a jefe unidad
            $receptor = $this->User_model->obtenerCorreoUsuario($roles[4]['id_rol'], $emisor->id_seccion);
            if ($receptor) {

              $data[0]['nombre_emisor'] = $emisor->nombre_completo;
              $data[0]['correo_emisor'] = $emisor->correo;
              $data[0]['id_emisor'] = $emisor->id_usuario;
              $data[0]['correo_receptor'] = $receptor->correo;
              $data[0]['id_receptor'] = $receptor->id_usuario;
              $data[0]['mensaje'] = "Hay un nuevo requerimiento de compra que require atención con id " . $id_solicitud;
              $data[0]['asunto'] = 'Solicitud Compra';
              $data[0]['url'] = base_url("index.php/Compras/Aprobar_Solicitud");
            }
            break;
          case 2:
            # enviar a autorizante
            $solicitud = $this->Solicitud_Compra_Model->obtenerSolicitudCompleta($id_solicitud);
            $receptor = $this->User_model->obtenerUsuarioPorEmpleado($solicitud->autorizante);

            if ($receptor) {
              $data[0]['nombre_emisor'] = $emisor->nombre_completo;
              $data[0]['correo_emisor'] = $emisor->correo;
              $data[0]['id_emisor'] = $emisor->id_usuario;
              $data[0]['correo_receptor'] = $receptor->correo;
              $data[0]['id_receptor'] = $receptor->id_usuario;
              $data[0]['mensaje'] = "Hay un nuevo requerimiento de compra que require atención con id " . $id_solicitud;
              $data[0]['asunto'] = 'Solicitud Compra';
              $data[0]['url'] = base_url("index.php/Compras/Aprobar_Solicitud");
            }

            // enviar a solicitante
            $receptor = $this->User_model->obtenerUsuario($this->User_model->obtenerUsuarioRastreabilidad($id_solicitud, $modulo, 'INSERTA')->id_usuario);

            if ($receptor) {
              $data[0]['nombre_emisor'] = $emisor->nombre_completo;
              $data[0]['correo_emisor'] = $emisor->correo;
              $data[0]['id_emisor'] = $emisor->id_usuario;
              $data[0]['correo_receptor'] = $receptor->correo;
              $data[0]['id_receptor'] = $receptor->id_usuario;
              $data[0]['mensaje'] = "La solicitud con id " . $id_solicitud . " ha sido aprobada por su Jefe.";
              $data[0]['asunto'] = 'Solicitud Compra';
              $data[0]['url'] = base_url("index.php/Compras/Estado_Solicitud/index/".$id_solicitud);
            }
            break;
          case 3:
            # enviar a Compras
            $solicitud = $this->Solicitud_Compra_Model->obtenerSolicitudCompleta($id_solicitud);
            $receptor = $this->User_model->obtenerCorreoUsuario($roles[6]['id_rol'], 121);
            if ($receptor) {
              $data[0]['nombre_emisor'] = $emisor->nombre_completo;
              $data[0]['correo_emisor'] = $emisor->correo;
              $data[0]['id_emisor'] = $emisor->id_usuario;
              $data[0]['correo_receptor'] = $receptor->correo;
              $data[0]['id_receptor'] = $receptor->id_usuario;
              $data[0]['mensaje'] = "Hay un nuevo requerimiento de compra que require atención con id " . $id_solicitud;
              $data[0]['asunto'] = 'Solicitud Compra';
              $data[0]['url'] = base_url("index.php/Compras/Gestionar_Solicitud");
            }

            $receptores[] = $this->User_model->obtenerUsuario($this->User_model->obtenerUsuarioRastreabilidad($id_solicitud, $modulo, 'INSERTA')->id_usuario);
            $receptores[] = $this->User_model->obtenerCorreoUsuario($roles[4]['id_rol'], $emisor->id_seccion);

            if ($receptores) {
              $i = 0;
              foreach ($receptores as $receptor) {
                $data[$i]['nombre_emisor'] = $emisor->nombre_completo;
                $data[$i]['correo_emisor'] = $emisor->correo;
                $data[$i]['id_emisor'] = $emisor->id_usuario;
                $data[$i]['correo_receptor'] = $receptor->correo;
                $data[$i]['id_receptor'] = $receptor->id_usuario;
                $data[$i]['mensaje'] = "La solicitud con id " . $id_solicitud . " ha sido aprobada.";
                $data[$i]['asunto'] = 'Solicitud Compra';
                $data[$i]['url'] = base_url("index.php/Compras/Estado_Solicitud/index/".$id_solicitud);
                $i++;
              }
            }
            break;
          case 4:
            # enviar a solicitante
            $solicitud = $this->Solicitud_Compra_Model->obtenerSolicitudCompleta($id_solicitud);
            $receptores[] = $this->User_model->obtenerUsuario($this->User_model->obtenerUsuarioRastreabilidad($id_solicitud, $modulo, 'INSERTA')->id_usuario);
            $receptores[] = $this->User_model->obtenerUsuarioPorEmpleado($solicitud->solicitante);

            if ($receptores) {
              $i = 0;
              foreach ($receptores as $receptor) {
                $data[$i]['nombre_emisor'] = $emisor->nombre_completo;
                $data[$i]['correo_emisor'] = $emisor->correo;
                $data[$i]['id_emisor'] = $emisor->id_usuario;
                $data[$i]['correo_receptor'] = $receptor->correo;
                $data[$i]['id_receptor'] = $receptor->id_usuario;
                $data[$i]['mensaje'] = "La solicitud con id " . $id_solicitud . " ha sido aprobada por compras.";
                $data[$i]['asunto'] = 'Solicitud Compra';
                $data[$i]['url'] = base_url("index.php/Compras/Estado_Solicitud/index/".$id_solicitud);
                $i++;
              }
            }
            break;
          case 9:
            // enviar a solicitante
            $solicitud = $this->Solicitud_Compra_Model->obtenerSolicitudCompleta($id_solicitud);
            //solicitante
            $receptores[] = $this->User_model->obtenerUsuarioPorEmpleado($solicitud->solicitante);
            //usuario solicitante
            $receptores[] = $this->User_model->obtenerUsuario($this->User_model->obtenerUsuarioRastreabilidad($id_solicitud, $modulo, 'INSERTA')->id_usuario);
            //UACI
            $receptores[] = $this->User_model->obtenerCorreoUsuario($roles[7]['id_rol'], 121);
            //jefa compras
            $receptores[] = $this->User_model->obtenerCorreoUsuario($roles[6]['id_rol'], 121);

            $mensaje = '';
            switch ($solicitud->nivel_anterior) {
              case 2:
                $mensaje = $solicitud->comentario_autorizante;
                break;
              case 3:
                $mensaje = $solicitud->comentario_compras;
                break;
              default:
                $mensaje = "";
                break;
            }

            if ($receptores) {
              $i = 0;
              foreach ($receptores as $receptor) {
                $data[$i]['nombre_emisor'] = $emisor->nombre_completo;
                $data[$i]['correo_emisor'] = $emisor->correo;
                $data[$i]['id_emisor'] = $emisor->id_usuario;
                $data[$i]['correo_receptor'] = $receptor->correo;
                $data[$i]['id_receptor'] = $receptor->id_usuario;
                $data[$i]['mensaje'] = "La solicitud con id " . $id_solicitud . " ha sido denegada. " . $mensaje;
                $data[$i]['asunto'] = 'Solicitud Compra';
                $data[$i]['url'] = base_url("index.php/Compras/Estado_Solicitud/index/".$id_solicitud);
                $i++;
              }
            }
            break;
          case 10:
            // enviar a solicitante
            $solicitud = $this->Solicitud_Compra_Model->obtenerSolicitudCompleta($id_solicitud);
            //solicitante
            $receptores[] = $this->User_model->obtenerUsuarioPorEmpleado($solicitud->solicitante);
            //usuario solicitante
            $receptores[] = $this->User_model->obtenerUsuario($this->User_model->obtenerUsuarioRastreabilidad($id_solicitud, $modulo, 'INSERTA')->id_usuario);
            //UACI
            $receptores[] = $this->User_model->obtenerCorreoUsuario($roles[7]['id_rol'], 121);
            //jefa compras
            $receptores[] = $this->User_model->obtenerCorreoUsuario($roles[6]['id_rol'], 121);

            $mensaje = '<a class="link" href="'.base_url('index.php/Compras/Reportes/Reporte_solicitudes_denegadas/descargarArchivo/'.$id_solicitud.'/').'">Memorandum</a>';

            if ($receptores) {
              $i = 0;
              foreach ($receptores as $receptor) {
                $data[$i]['nombre_emisor'] = $emisor->nombre_completo;
                $data[$i]['correo_emisor'] = $emisor->correo;
                $data[$i]['id_emisor'] = $emisor->id_usuario;
                $data[$i]['correo_receptor'] = $receptor->correo;
                $data[$i]['id_receptor'] = $receptor->id_usuario;
                $data[$i]['mensaje'] = "La solicitud con id " . $id_solicitud . " ha sido denegada. " . $mensaje;
                $data[$i]['asunto'] = 'Solicitud Compra';
                $data[$i]['url'] = base_url("index.php/Compras/Estado_Solicitud/index/".$id_solicitud);
                $i++;
              }
            }
            break;
        }

      }

      foreach ($data as $dato) {
        $this->EnviarNotificacion($dato);
      }
    }

    public function NotificacionProductoActivoFijo($id_detalleproducto) {
      $especificos = array('54101', '54102', '54105', '54108', '54113');

      $this->db->select('id_especifico')
               ->from('sic_detalle_producto')
               ->where('id_detalleproducto', $id_detalleproducto);
      $especifico;
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
       $especifico = $query->row()->id_especifico;
      } else {
       $especifico = FALSE;
      }

      if ($especifico) {
        $indice = array_search($especifico,$especificos,true);
        if ($indice) {
          $not = array(
            'emisor' => 1029,
            'receptor' => 1060,
            'mensaje_notificacion' => "Ha ingresado un nuevo producto a la Bodega",
            'url_notificacion' => base_url("index.php/Bodega/Factura"),
            'clase_notificacion' => 'success',
          );
        }
      }
    }
  }
?>
