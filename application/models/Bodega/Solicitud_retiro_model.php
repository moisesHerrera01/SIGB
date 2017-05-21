<?php
  class Solicitud_retiro_model extends CI_Model{

    public $fecha_solicitud;
    public $id_seccion;
    public $numero_solicitud;
    public $estado_solicitud;
    public $fecha_salida;
    public $id_usuario;
    public $prioridad;
    public $id_fuentes;
    public $nivel_solicitud;
    public $comentario_jefe;
    public $comentario_admin;
    public $comentario;

    function __construct() {
        parent::__construct();
    }

    public function insertarSolicitud($data){

        $this->fecha_solicitud = $data['fecha_solicitud'];
        $this->id_seccion = $data['id_seccion'];
        $this->numero_solicitud = $data['numero_solicitud'];
        $this->estado_solicitud = 'APROBADA';
        $this->fecha_salida = 'NULL';
        $this->id_usuario = $data['id_usuario'];
        $this->prioridad = $data['prioridad'];
        $this->id_fuentes = $data['id_fuentes'];
        $this->nivel_solicitud= $data['nivel_solicitud'];
        $this->comentario_jefe= 'Ingreso y descargo directamente';
        $this->comentario_admin= 'Ingreso y descargo directamente';
        $this->comentario= 'Ingreso y descargo directamente';

        $this->db->insert('sic_solicitud', $this);
    }

      public function obtenerSolicitudes(){
        $this->db->order_by("prioridad desc, id_solicitud desc");
        $query = $this->db->get('sic_solicitud');
        if ($query->num_rows() > 0) {
            return  $query->result();
        }
        else {
            return FALSE;
        }
      }

      public function obtenerSolicitud($id){
          $this->db->where('id_solicitud',$id);
          $query = $this->db->get('sic_solicitud');
          if ($query->num_rows() > 0) {
            $nombre;
            foreach ($query->result() as $fact) {
              $nombre = $fact->numero_solicitud;
            }
            return  $nombre;
          }
          else {
              return FALSE;
          }
      }

    public function buscarSolicitudes($busca){
      $this->db->select('*')
               ->from('sic_solicitud s')
               ->join('org_usuario u','u.id_usuario=s.id_usuario')
               ->join('org_seccion sec','sec.id_seccion=s.id_seccion')
               ->join('sic_fuentes_fondo f','f.id_fuentes=s.id_fuentes')
               ->order_by("prioridad desc, id_solicitud desc")
               ->like('s.numero_solicitud',$busca)
               ->or_like('s.fecha_solicitud',$busca);
     $query=$this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerSolicitudesLimit($porpagina, $segmento){
      $this->db->select('*')
               ->from('sic_solicitud s')
               ->join('org_usuario u','u.id_usuario=s.id_usuario')
               ->join('org_seccion sec','sec.id_seccion=s.id_seccion')
               ->join('sic_fuentes_fondo f','f.id_fuentes=s.id_fuentes')
               ->order_by("prioridad desc, id_solicitud desc")
               ->limit($porpagina, $segmento);
     $query=$this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function buscarSolicitudesUser($id_user, $busca){
      $this->db->where('id_usuario', $id_user);
      $this->db->like('numero_solicitud', $busca);
      //$this->db->order_by("prioridad", "desc");
      $this->db->order_by("prioridad desc, id_solicitud desc");
      $query = $this->db->get('sic_solicitud', 10);
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function actualizarSolicitud($id, $data){
      $this->db->where('id_solicitud',$id);
      $this->db->update('sic_solicitud', $data);
    }

    public function liquidar($id){
      date_default_timezone_set('America/El_Salvador');
      $anyo=20;
      $fecha_actual=date($anyo."y-m-d");
      $dat = array(
        'estado_solicitud' => 'LIQUIDADA',
        'fecha_salida'=>$fecha_actual,
        'nivel_solicitud' => 4,
        'nivel_anterior' => 3
      );
      $this->db->where('id_solicitud', $id);
      $this->db->update('sic_solicitud', $dat);
    }

    public function aprobar($id) {
      $this->db->where('id_solicitud', $id);
      $this->db->update('sic_solicitud', array('estado_solicitud' => 'APROBADA'));
    }

    public function enviarSolicitud($id) {
      $data = array('estado_solicitud' => 'ENVIADA', 'nivel_solicitud' => 1);
      $this->db->where('id_solicitud', $id);
      $this->db->update('sic_solicitud', $data);
    }

    public function eliminarSolicitud($id){
      $this->db->delete('sic_solicitud', array('id_solicitud' => $id));
    }

    public function totalSolicitudes(){
      $this->db->select("count(*) as total")
                ->from('sic_solicitud');
      $query=$this->db->get();
      return $query->row();
    }



    public function totalSolicitudesRetiro(){
      $this->db->select("count(id_solicitud) as total")
                ->from('sic_solicitud')
                ->where('nivel_solicitud', 3)
                ->or_where('nivel_solicitud', 4);
      $query=$this->db->get();
      return $query->row();
    }

     public function obtenerSolicitudesEstadoLimit($nivel, $seccion, $porpagina, $segmento){
       for ($i=0; $i < count($nivel); $i++) {
         if ($i == 0) {
           $this->db->where('nivel_solicitud', $nivel[$i]);
         } else {
           $this->db->or_where('nivel_solicitud', $nivel[$i]);
         }
       }
       if ($seccion != 0) {
         $this->db->where('id_seccion', $seccion);
       }
       $this->db->order_by("prioridad desc, id_solicitud desc");
       $query = $this->db->get('sic_solicitud', $porpagina, $segmento);
       if ($query->num_rows() > 0) {
           return  $query->result();
       }
       else {
           return FALSE;
       }
     }

     public function obtenerSolicitudesUserLimit($id_user ,$porpagina, $segmento){
       $this->db->where('id_usuario', $id_user);
       $this->db->order_by("prioridad desc, id_solicitud desc");
       $query = $this->db->get('sic_solicitud', $porpagina, $segmento);
       if ($query->num_rows() > 0) {
           return  $query->result();
       }
       else {
           return FALSE;
       }
     }

     public function obtenerSeccion($id){
       $this->db->where('id_seccion',$id);
       $query = $this->db->get('org_seccion');
       if ($query->num_rows() > 0) {
         $nombre;
         foreach ($query->result() as $sec) {
           $nombre = $sec->nombre_seccion;
         }
         return  $nombre;
       }
       else {
           return FALSE;
       }
     }

     public function obtenerSecciones(){
       $this->db->order_by("id_seccion", "asc");
       $query = $this->db->get('org_seccion');
       if ($query->num_rows() > 0) {
           return  $query->result();
       }
       else {
           return FALSE;
       }
     }

     public function buscarSecciones($busca){
       $this->db->like('nombre_seccion', $busca);
       $query = $this->db->get('org_seccion', 10);
       if ($query->num_rows() > 0) {
           return  $query->result();
       }
       else {
           return FALSE;
       }
     }


      public function obtenerId(){
       $this->db->select('DATABASE() as nombre');
       $query=$this->db->get();
       $base=$query->row()->nombre;
       $this->db->select('AUTO_INCREMENT as var');
       $this->db->where('TABLE_SCHEMA',$base);
       $this->db->where('TABLE_NAME','sic_solicitud');
       $query = $this->db->get('information_schema.TABLES');
       if ($query->num_rows() > 0) {
         $nombre;
         foreach ($query->result() as $fact) {
           $nombre = $fact->var;
         }
         return  $nombre;
       }
       else {
           return FALSE;
       }
     }

      public function obtenerNumeroFuente($fuente) {
        $this->db->select('numero_solicitud')
                 ->from('sic_solicitud')
                 ->where('id_fuentes', $fuente)
                 ->limit(1)
                 ->order_by('id_solicitud', 'DESC');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
          return $query->row()->numero_solicitud + 1;
        } else {
          return 1;
        }
      }

     public function retornarEstado($id){
         $this->db->where('id_solicitud', $id);
         $query = $this->db->get('sic_solicitud');
         if ($query->num_rows() > 0) {
           $estado;
           foreach ($query->result() as $sol) {
             $estado = $sol->estado_solicitud;
           }
           return  $estado;
         }
         else {
             return FALSE;
         }
     }

     public function retornarNivel($id){
         $this->db->where('id_solicitud',$id);
         $query = $this->db->get('sic_solicitud');
         if ($query->num_rows() > 0) {
           $nivel;
           foreach ($query->result() as $fact) {
             $nivel = $fact->nivel_solicitud;
           }
           return  $nivel;
         }
         else {
             return FALSE;
         }
     }



     public function obtenerTodaSolicitud($id){
         $this->db->where('id_solicitud',$id);
         $query = $this->db->get('sic_solicitud');
         if ($query->num_rows() > 0) {
           return  $query->result();
         }
         else {
             return FALSE;
         }
     }

     public function obtenerSolicitudUsuario($id) {
        $this->db->select('b.usuario')
                 ->from('sic_solicitud a')
                 ->join('org_usuario b', 'a.id_usuario = b.id_usuario')
                 ->where('a.id_solicitud', $id);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
          return $query->row();
        } else {
          return FALSE;
        }
     }

     public function obtenerSolicitudCompleta($id){
       $this->db->where('id_solicitud',$id);
       $query=$this->db->get('sic_solicitud');
       return $query->row();
     }

     public function obtenerSolicitudKardex($fecha, $detalle_producto, $cantidad, $precio){
       $this->db->select("a.id_solicitud")
                ->from("sic_solicitud a")
                ->join("sic_detalle_solicitud_producto b", "a.id_solicitud = b.id_solicitud")
                ->where("a.fecha_salida", $fecha)
                ->where("a.estado_solicitud", "LIQUIDADA")
                ->where("b.id_detalleproducto", $detalle_producto)
                ->where("b.cantidad", $cantidad)
                ->where("b.precio", $precio)
                ->group_by("a.id_solicitud");

         $query = $this->db->get();
         if ($query->num_rows() > 0) {
           return  $query->row();
         }
         else {
             return FALSE;
         }
     }

     public function obtenerLiquidadas($anio = 0) {
       $this->db->select("*")
                ->from("sic_solicitud")
                ->where("estado_solicitud", "LIQUIDADA");

        if ($anio != 0) {
            $this->db->where("fecha_salida BETWEEN '$anio-01-01' AND '$anio-12-31'");
        }

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
          return  $query->result();
        }
        else {
            return FALSE;
        }
     }

     public function obtenerSolicitudesUserFecha($id_user, $anio = 0){
       $this->db->from('sic_solicitud')
                ->where('id_usuario', $id_user);
       if ($anio != 0) {
           $this->db->where("fecha_salida BETWEEN '$anio-01-01' AND '$anio-12-31'");
       }
       $query = $this->db->get();

       if ($query->num_rows() > 0) {
           return  $query->result();
       }
       else {
           return FALSE;
       }
     }

     public function obtenerSolicitudesSeccionFecha($seccion, $anio = 0){
       $this->db->from('sic_solicitud')
                ->where('id_seccion', $seccion);
       if ($anio != 0) {
           $this->db->where("fecha_salida BETWEEN '$anio-01-01' AND '$anio-12-31'");
       }
       $query = $this->db->get();

       if ($query->num_rows() > 0) {
           return  $query->result();
       }
       else {
           return FALSE;
       }
     }

     public function obtenerGastosRetiros($seccion, $minFecha, $maxFecha) {
       $this->db->select('SUM(b.total) total')
                ->from("sic_solicitud a")
                ->join("sic_detalle_solicitud_producto b", "a.id_solicitud = b.id_solicitud")
                ->where("a.estado_solicitud", "LIQUIDADA")
                ->where("a.id_seccion", $seccion)
                ->where("a.fecha_salida BETWEEN '$minFecha' AND '$maxFecha'");

       $query = $this->db->get();
       if ($query->num_rows() > 0) {
          return  $query->row();
       }
       else {
          return FALSE;
       }
     }

     public function obtenerSolicitudesAprobadasSeccion($seccion, $anio) {
       $this->db->select("count(*) cantidad")
                ->from('sic_solicitud')
                ->where('id_seccion', $seccion)
                ->where("nivel_solicitud >=", 3)
                ->where("fecha_salida BETWEEN '$anio-01-01' AND '$anio-12-31'");

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
           return  $query->row();
        }
        else {
           return FALSE;
        }
     }

     public function obtenerSolicitudesNoAprobadasSeccion($seccion, $anio) {
       $this->db->select("count(*) cantidad")
                ->from('sic_solicitud')
                ->where('id_seccion', $seccion)
                ->where("nivel_solicitud <", 3)
                ->where("fecha_salida BETWEEN '$anio-01-01' AND '$anio-12-31'");

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
           return  $query->row();
        }
        else {
           return FALSE;
        }
     }

     public function obtenerGastoTotalBodegaMes($mes, $anio, $u_dia = 31) {
       $this->db->select('SUM(b.total) total')
                ->from("sic_solicitud a")
                ->join("sic_detalle_solicitud_producto b", "a.id_solicitud = b.id_solicitud")
                ->where("a.estado_solicitud", "LIQUIDADA")
                ->where("fecha_salida BETWEEN '$anio-$mes-01' AND '$anio-$mes-$u_dia'");

       $query = $this->db->get();
       if ($query->num_rows() > 0) {
          return  $query->row();
       }
       else {
          return FALSE;
       }
     }

     public function validarLiquidar($id_solicitud){
       $contador=0;
       $this->db->select('ds.estado_solicitud_producto')
                ->from('sic_solicitud s')
                ->join('sic_detalle_solicitud_producto ds','ds.id_solicitud=s.id_solicitud')
                ->where('s.id_solicitud',$id_solicitud);
        $query=$this->db->get();
        $sol=$query->result();

        foreach ($sol as $soli) {
          if($soli->estado_solicitud_producto=='DESCARGADO'){
            $contador++;
          }
        }
        if ($contador>0) {
          return TRUE;
        }else {
          return FALSE;
        }
     }

     public function validarDescargar($id_detalle_solicitud){
       $this->db->select('ds.precio')
                ->from('sic_detalle_solicitud_producto ds')
                ->where('ds.id_detalle_solicitud_producto',$id_detalle_solicitud);
        $query=$this->db->get();
        $soli=$query->row();
          if($soli->precio<=0){
            return TRUE;
          }else {
            return FALSE;
          }
     }

     public function obtenerIdSeccion($nombre){
       $this->db->select('*')
                ->from('org_seccion')
                ->where('nombre_seccion',$nombre);
       $query=$this->db->get();
       return $query->row();
     }
}
?>
