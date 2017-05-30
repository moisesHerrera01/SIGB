<?php
  class Solicitud_compra_model extends CI_Model{

    public $numero_solicitud_compra;
    public $fecha_solicitud_compra;
    public $solicitante;
    public $justificacion;
    public $documento_especificaciones;
    public $precio_estimado;
    public $estado_solicitud_compra;
    public $autorizante;
    public $forma_entrega;
    public $lugar_entrega;
    public $otras_condiciones;
    public $propuesta_administrador;
    public $id_seccion;
    public $comentario;

    function __construct() {
        parent::__construct();
    }

    public function insertarSolicitudCompra($data){
        $USER = $this->session->userdata('logged_in');
        $this->numero_solicitud_compra = $data['numero_solicitud_compra'];
        $this->fecha_solicitud_compra = $data['fecha_solicitud_compra'];
        $this->solicitante = $data['solicitante'];
        $this->autorizante = $data['autorizante'];
        $this->propuesta_administrador = $data['propuesta_administrador'];
        $this->justificacion = $data['justificacion'];
        $this->documento_especificaciones = $data['documento_especificaciones'];
        $this->precio_estimado = $data['precio_estimado'];
        $this->estado_solicitud_compra = 'INGRESADA';
        $this->id_seccion=$USER['id_seccion'];
        $this->documento_especificaciones=$data['documento_especificaciones'];
        $this->forma_entrega=$data['forma_entrega'];
        $this->lugar_entrega=$data['lugar_entrega'];
        $this->otras_condiciones=$data['otras_condiciones'];
        //$this->comentario_jefe=$data['comentario_jefe'];
        //$this->comentario_autorizante=$data['comentario_autorizante'];
        $this->comentario=$data['comentario'];
        $this->db->insert('sic_solicitud_compra', $this);
    }

      public function obtenerSolicitudesCompra(){
        $this->db->order_by("id_solicitud_compra desc");
        $query = $this->db->get('sic_solicitud_compra');
        if ($query->num_rows() > 0) {
            return  $query->result();
        }
        else {
            return FALSE;
        }
      }

    public function buscarSolicitudes($busca){
      $this->db->like('numero_solicitud_compra', $busca);
      $this->db->or_like('fecha_solicitud_compra', $busca);
      $this->db->order_by("id_solicitud_compra desc");
      $query = $this->db->get('sic_solicitud_compra', 10);
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function actualizarSolicitudCompra($id, $data){
      $this->db->where('id_solicitud_compra',$id);
      $this->db->update('sic_solicitud_compra', $data);
    }


    public function eliminarSolicitudCompra($id){
      $this->db->delete('sic_solicitud_compra', array('id_solicitud_compra' => $id));
    }

    public function totalSolicitudesCompra($seccion){
      if ($seccion==0) {
        $this->db->select('count(s.id_solicitud_compra) as cantidad')
                 ->from('sic_solicitud_compra s');
        $query = $this->db->get();
      } else {
        $this->db->select('count(s.id_solicitud_compra) as cantidad')
                 ->from('sic_solicitud_compra s')
                 ->join('sir_empleado e','s.solicitante=e.id_empleado')
                 ->join('org_usuario u','u.nr=e.nr')
                 ->order_by('id_solicitud_compra desc')
                 ->where('s.id_seccion', $seccion);
        $query = $this->db->get();
      }
      if ($query->num_rows() > 0) {
          return  $query->row();
      }
      else {
          return FALSE;
      }
    }

     public function obtenerSolicitudesLimit($porpagina, $segmento){
       $this->db->order_by("id_solicitud_compra desc");
       $query = $this->db->get('sic_solicitud_compra', $porpagina, $segmento);
       if ($query->num_rows() > 0) {
           return  $query->result();
       }
       else {
           return FALSE;
       }
     }

     public function buscarSolicitudesCompraUser($id_seccion ,$busca){
       if ($id_seccion==0){
         $this->db->select('s.id_solicitud_compra,s.fecha_solicitud_compra,s.numero_solicitud_compra,e.primer_nombre,e.segundo_nombre,
         s.justificacion,s.documento_especificaciones,s.precio_estimado,s.estado_solicitud_compra,s.propuesta_administrador,
         s.forma_entrega,s.lugar_entrega,s.otras_condiciones,s.comentario,s.solicitante,s.autorizante')
                  ->from('sic_solicitud_compra s')
                  ->join('sir_empleado e','s.solicitante=e.id_empleado')
                  ->join('org_usuario u','u.nr=e.nr')
                  ->order_by('s.fecha_solicitud_compra desc')
                  ->like('s.id_solicitud_compra',$busca);
                  //->where('s.id_seccion', $id_seccion);
         $query = $this->db->get();

       } else {
         $this->db->select('s.id_solicitud_compra,s.fecha_solicitud_compra,s.numero_solicitud_compra,e.primer_nombre,e.segundo_nombre,
         s.justificacion,s.documento_especificaciones,s.precio_estimado,s.estado_solicitud_compra,s.propuesta_administrador,
         s.forma_entrega,s.lugar_entrega,s.otras_condiciones,s.comentario,s.solicitante,s.autorizante')
                  ->from('sic_solicitud_compra s')
                  ->join('sir_empleado e','s.solicitante=e.id_empleado')
                  ->join('org_usuario u','u.nr=e.nr')
                  ->order_by('s.fecha_solicitud_compra desc')
                  ->like('numero_solicitud_compra',$busca)
                  ->where('s.id_solicitud_compra', $id_seccion);
         $query = $this->db->get();
       }

       if ($query->num_rows() > 0) {
           return  $query->result();
       }
       else {
           return FALSE;
       }
     }

     public function obtenerSolicitudesCompraUserLimit($id_seccion ,$porpagina, $segmento){
       if($id_seccion==0){
         $this->db->select('s.id_solicitud_compra,s.fecha_solicitud_compra,s.numero_solicitud_compra, s.justificacion,s.solicitante,s.autorizante,
         s.documento_especificaciones,s.precio_estimado,s.estado_solicitud_compra,s.propuesta_administrador,s.forma_entrega,s.lugar_entrega,s.otras_condiciones,s.comentario')
                  ->from('sic_solicitud_compra s')
                  ->join('sir_empleado e','s.solicitante=e.id_empleado')
                  ->join('org_usuario u','u.nr=e.nr')
                  ->limit($porpagina,$segmento)
                  ->order_by('s.fecha_solicitud_compra desc');
         $query = $this->db->get();

       } else {
         $this->db->select('s.id_solicitud_compra,s.fecha_solicitud_compra,s.numero_solicitud_compra, s.justificacion,s.solicitante,s.autorizante,
         s.documento_especificaciones,s.precio_estimado,s.estado_solicitud_compra,s.propuesta_administrador,s.forma_entrega,s.lugar_entrega,s.otras_condiciones,s.comentario')
                  ->from('sic_solicitud_compra s')
                  ->join('sir_empleado e','s.solicitante=e.id_empleado')
                  ->join('org_usuario u','u.nr=e.nr')
                  ->limit($porpagina,$segmento)
                  ->order_by('s.fecha_solicitud_compra desc')
                  ->where('s.id_seccion', $id_seccion);
         $query = $this->db->get();
       }
       if ($query->num_rows() > 0) {
           return  $query->result();
       }
       else {
           return FALSE;
       }
     }

     public function obtenerIdSolicitudCompra(){
      $this->db->select('DATABASE() as nombre');
      $query=$this->db->get();
      $base=$query->row()->nombre;
      $this->db->select('AUTO_INCREMENT as var');
      $this->db->where('TABLE_SCHEMA',$base);
      $this->db->where('TABLE_NAME','sic_solicitud_compra');
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

    public function existeSolicitudCompra($id){
      $this->db->where('id_solicitud_compra',$id);
      $query = $this->db->get('sic_detalle_solicitud_compra');
      if ($query->num_rows()>0){
        return TRUE;
      }
      else {
        return FALSE;
      }
    }

    public function enviarSolicitudCompra($id) {
      $data = array('estado_solicitud_compra' => 'ENVIADA', 'nivel_solicitud' => 1);
      $this->db->where('id_solicitud_compra', $id);
      $this->db->update('sic_solicitud_compra', $data);
    }

    public function obtenerSolicitudCompra($id){
        $this->db->where('id_solicitud_compra',$id);
        $query = $this->db->get('sic_solicitud_compra');
        if ($query->num_rows() > 0) {
          $nombre;
          foreach ($query->result() as $fact) {
            $nombre = $fact->numero_solicitud_compra;
          }
          return  $nombre;
        }
        else {
            return FALSE;
        }
    }

    public function obtenerEstadoSolicitud($id){
        $this->db->where('id_solicitud_compra',$id);
        $query = $this->db->get('sic_solicitud_compra');
        if ($query->num_rows() > 0) {
          $estado;
          foreach ($query->result() as $fact) {
            $estado = $fact->estado_solicitud_compra;
          }
          return  $estado;
        }
        else {
            return FALSE;
        }
    }

    public function obtenerNivelSolicitud($id){
        $this->db->where('id_solicitud_compra',$id);
        $query = $this->db->get('sic_solicitud_compra');
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

    public function obtenerSolicitudCompleta($id){
        $this->db->where('id_solicitud_compra',$id);
        $query = $this->db->get('sic_solicitud_compra');
        return  $query->row();
    }

    public function obtenerSolicitante($id){
        $this->db->where('id_empleado',$id);
        $query = $this->db->get('sir_empleado');
        if ($query->num_rows() > 0) {
          $nombre;
          foreach ($query->result() as $fact) {
            $nombre = $fact->primer_nombre.' '.$fact->segundo_nombre;
          }
          return  $nombre;
        }
        else {
            return FALSE;
        }
    }

    public function obtenerSolicitudesEstadoLimit($nivel, $seccion, $porpagina, $segmento){
      if ($seccion==0) {
        $this->db->order_by("id_solicitud_compra desc");
        $query = $this->db->get('sic_solicitud_compra', $porpagina, $segmento);
      }else{

        for ($i=0; $i < count($nivel); $i++) {
          if ($i == 0) {
            $this->db->where('nivel_solicitud', $nivel[$i]);
          } else {
            $this->db->or_where('nivel_solicitud', $nivel[$i]);
          }
          if ($seccion != 0) {
            $this->db->where('id_seccion', $seccion);
          }
        }

        $this->db->order_by("id_solicitud_compra desc");
        $query = $this->db->get('sic_solicitud_compra', $porpagina, $segmento);
      }
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerDatosSolicitud($id){
      $this->db->select('s.numero_solicitud_compra,s.fecha_solicitud_compra,e.nombre_empleado,
                e.cargo_funcional,e.seccion,s.justificacion,dp.id_especifico,ds.cantidad,u.nombre,p.nombre as nombre_producto,s.precio_estimado,
                ds.especificaciones,s.forma_entrega,s.lugar_entrega,s.comentario_jefe,s.comentario_autorizante,s.comentario_compras, s.autorizante,
                s.comentario, s.otras_condiciones, e.seccion_padre')
               ->from('sic_solicitud_compra s')
               ->join('sic_detalle_solicitud_compra ds','s.id_solicitud_compra = ds.id_solicitud_compra')
               ->join('sic_detalle_producto dp','ds.id_detalleproducto = dp.id_detalleproducto')
               ->join('sic_producto p','dp.id_producto = p.id_producto')
               ->join('sic_unidad_medida u','p.id_unidad_medida = u.id_unidad_medida')
               ->join('Lista_empleados_estado e','s.solicitante=e.id_empleado')
               ->where('s.id_solicitud_compra', $id)
               ->group_by('ds.id_detalle_solicitud_compra');
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerAutorizante_AdminOC($id_solicitud,$bandera) {
      $this->db->select("lee.nombre_empleado,lee.seccion,lee.cargo_funcional,lee.seccion_padre")
               ->from("sic_solicitud_compra s");
               if ($bandera==1) {
                 $this->db->join("Lista_empleados_estado lee", "s.autorizante = lee.id_empleado");
               }else {
                 $this->db->join("Lista_empleados_estado lee", "s.propuesta_administrador = lee.id_empleado");
               }
               $this->db->where("s.id_solicitud_compra", $id_solicitud);
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
         return $query->row();
      }
      else {
         return FALSE;
      }
    }

// metodo de autocomplete solicitante en Solicitud_Compra
    /*public function obtenerSolicitantes($id_seccion){
      $this->db->select('lee.id_empleado,lee.cargo_funcional,lee.nombre_empleado,lee.seccion_padre')
               ->from('Lista_empleados_estado lee')
               ->where('lee.estado',1)
               ->where('lee.id_seccion',$id_seccion)
               ->where("lee.nivel_funcional BETWEEN '1' AND '2'");
       $query=$this->db->get();
       if ($query->num_rows() > 0) {
           return  $query->result();
       }
       else {
           return FALSE;
       }
    }*/

    public function obtenerSolicitantes(){
      $this->db->select('lee.nombre_empleado,lee.id_empleado,lee.cargo_funcional,lee.seccion_padre')
                ->from('Lista_empleados_estado lee')
                ->join('sir_empleado_informacion_laboral i','lee.id_empleado=i.id_empleado')
                ->where('lee.estado',1)
                ->group_by('lee.id_empleado')
                //->where("lee.nivel_funcional BETWEEN '1' AND '2'")
                ->order_by('lee.id_empleado')
                ->limit(10);
        $query2 = $this->db->get();
      if ($query2->num_rows() > 0) {
          return  $query2->result();
      }
      else {
          return FALSE;
      }
    }

    // buscar de autocomplete de solicitante en solicitud_compra
    /*public function buscarSolicitantes($id_seccion,$busca){
      $this->db->select('lee.id_empleado,lee.cargo_funcional,lee.nombre_empleado,lee.seccion_padre')
               ->from('Lista_empleados_estado lee')
               ->where('lee.estado',1)
               ->like('lee.nombre_empleado',$busca)
               ->where('lee.id_seccion',$id_seccion)
               ->where("lee.nivel_funcional BETWEEN '1' AND '2'");
       $query=$this->db->get();
       if ($query->num_rows() > 0) {
           return  $query->result();
       }
       else {
           return FALSE;
       }
    }*/

      public function buscarSolicitantes($busca){
      $this->db->select("e.nombre_empleado, e.id_empleado,f.funcional as cargo_funcional,s.depende as seccion_padre")
                ->from("(select CONCAT_WS(' ',primer_nombre,segundo_nombre,primer_apellido,
                segundo_apellido) as nombre_empleado, id_empleado,id_estado from sir_empleado) as e")
                ->join('sir_empleado_informacion_laboral ei','e.id_empleado=ei.id_empleado')
                ->join('sir_cargo_funcional f','f.id_cargo_funcional=ei.id_cargo_funcional')
                ->join('org_seccion s','s.id_seccion=ei.id_seccion')
                ->join('tcm_empleado_informacion_laboral eil','eil.id_empleado=e.id_empleado')
                ->join('tcm_empleado_informacion_laboral eil2','eil.fecha_inicio=ei.fecha_inicio')
                ->where('e.id_estado',1)
                ->like('e.nombre_empleado',$busca)
                ->group_by('e.id_empleado')
                ->limit(10);
        $query2 = $this->db->get();

        foreach ($query2->result() as $empleado) {
          $this->db->select('nombre_seccion')
                   ->from('org_seccion')
                   ->where('id_seccion',$empleado->seccion_padre);
          $query3 = $this->db->get();
          $seccion =$query3->row();
          $empleado->seccion_padre=$seccion->nombre_seccion;
        }
      if ($query2->num_rows() > 0) {
          return  $query2->result();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerEmpleadoSecciones($id_empleado){
      $this->db->select('lee.seccion, lee.seccion_padre,lee.id_seccion,lee.id_seccion_padre')
                ->from('Lista_empleados_estado lee')
                ->where('lee.estado',1)
                ->where('lee.id_empleado',$id_empleado);
        $query2 = $this->db->get();
      if ($query2->num_rows() > 0) {
          return  $query2->row();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerEmpleadoDatos(){
      $this->db->select("CONCAT_WS(' ',e.primer_nombre,e.segundo_nombre,e.primer_apellido,e.segundo_apellido) as nombre_empleado,
      e.id_empleado,f.funcional as cargo_funcional,s.depende as seccion_padre")
                ->from('sir_empleado e')
                ->join('org_usuario u','e.nr=u.nr')
                ->join('sir_empleado_informacion_laboral ei','e.id_empleado=ei.id_empleado')
                ->join('sir_cargo_funcional f','f.id_cargo_funcional=ei.id_cargo_funcional')
                ->join('org_seccion s','s.id_seccion=ei.id_seccion')
                ->join('tcm_empleado_informacion_laboral eil','eil.id_empleado=e.id_empleado')
                ->join('tcm_empleado_informacion_laboral eil2','eil.fecha_inicio=ei.fecha_inicio')
                ->where('e.id_estado',1)
                ->where("f.id_nivel BETWEEN '1' AND '2'")
                ->group_by('e.id_empleado')
                ->limit(10);
        $query2 = $this->db->get();

        foreach ($query2->result() as $empleado) {
          $this->db->select('nombre_seccion')
                   ->from('org_seccion')
                   ->where('id_seccion',$empleado->seccion_padre);
          $query3 = $this->db->get();
          $seccion =$query3->row();
          $empleado->seccion_padre=$seccion->nombre_seccion;
        }
      if ($query2->num_rows() > 0) {
          return  $query2->result();
      }
      else {
          return FALSE;
      }
    }

    public function buscarEmpleadoDatos($busca){
      $this->db->select("e.nombre_empleado, e.id_empleado,f.funcional as cargo_funcional,s.depende as seccion_padre")
                ->from("(select CONCAT_WS(' ',primer_nombre,segundo_nombre,primer_apellido,
                segundo_apellido) as nombre_empleado, id_empleado,id_estado from sir_empleado) as e")
                ->join('sir_empleado_informacion_laboral ei','e.id_empleado=ei.id_empleado')
                ->join('sir_cargo_funcional f','f.id_cargo_funcional=ei.id_cargo_funcional')
                ->join('org_seccion s','s.id_seccion=ei.id_seccion')
                ->join('tcm_empleado_informacion_laboral eil','eil.id_empleado=e.id_empleado')
                ->join('tcm_empleado_informacion_laboral eil2','eil.fecha_inicio=ei.fecha_inicio')
                ->where('e.id_estado',1)
                ->where("f.id_nivel BETWEEN '1' AND '2'")
                ->like('e.nombre_empleado',$busca)
                ->group_by('e.id_empleado')
                ->limit(10);
        $query2 = $this->db->get();

        foreach ($query2->result() as $empleado) {
          $this->db->select('nombre_seccion')
                   ->from('org_seccion')
                   ->where('id_seccion',$empleado->seccion_padre);
          $query3 = $this->db->get();
          $seccion =$query3->row();
          $empleado->seccion_padre=$seccion->nombre_seccion;
        }
      if ($query2->num_rows() > 0) {
          return  $query2->result();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerAutorizante($solicitante){
      $this->db->select('seccion')
               ->from('Lista_empleados_estado')
               ->where('id_seccion',$solicitante)
               ->group_by('id_seccion');
      $query=$this->db->get();
      $nom='';
        if ($query->num_rows() > 0) {
          $nom=$query->row()->seccion;
        }else {
            $nom='N/A';
        }
        return  $nom;
    }

    public function esAutorizante($id_seccion,$id_empleado){
        $this->db->where('id_seccion_padre',$id_seccion);
        $query = $this->db->get('Lista_empleados_estado');
        $autorizante=FALSE;
        if ($query->num_rows() > 0) {
          $autorizante=TRUE;
        }elseif ($id_empleado==1000107) {
          $autorizante=TRUE;
        }
        return $autorizante;
    }

    public function retornarIdAutorizante($id){
        $this->db->where('solicitante',$id);
        $query = $this->db->get('sic_autoriza_solicita');
        if ($query->num_rows() > 0) {
          $idAuto;
          foreach ($query->result() as $auto) {
            $idAuto = $auto->autorizante;
          }
          return  $idAuto;
        }
        else {
            return FALSE;
        }
    }

    public function obtenerSolicitudesCompraUserDHB($id_seccion, $anio){
      $this->db->select('s.id_solicitud_compra,s.fecha_solicitud_compra,s.numero_solicitud_compra,e.primer_nombre,e.segundo_nombre,
      s.justificacion,s.documento_especificaciones,s.precio_estimado,s.estado_solicitud_compra')
               ->from('sic_solicitud_compra s')
               ->join('sir_empleado e','s.solicitante=e.id_empleado')
               ->join('org_usuario u','u.nr=e.nr')
               ->order_by('id_solicitud_compra desc')
               ->where('u.id_seccion', $id_seccion)
               ->where("s.fecha_solicitud_compra BETWEEN '$anio-01-01' AND '$anio-12-31'");
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }


    public function obtenerSolicitudesAprobadasSeccionFecha($seccion, $anio) {
      $this->db->select('count(*) cantidad')
               ->from('sic_solicitud_compra a')
               ->where('a.nivel_solicitud > 3')
               ->like('a.estado_solicitud_compra', 'APROBADA')
               ->where('a.id_seccion', $seccion)
               ->where("a.fecha_solicitud_compra BETWEEN '$anio-01-01' AND '$anio-12-31'");
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->row();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerSolicitudesNoAprobadasSeccionFecha($seccion, $anio) {
      $this->db->select('count(*) cantidad')
               ->from('sic_solicitud_compra a')
               ->where('a.nivel_solicitud > 3')
               ->like('a.estado_solicitud_compra', 'DENEGADA')
               ->where('a.id_seccion', $seccion)
               ->where("a.fecha_solicitud_compra BETWEEN '$anio-01-01' AND '$anio-12-31'");
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->row();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerSolicitudesAprJefe($anio) {
      $this->db->select('count(*) cantidad')
               ->from('sic_solicitud_compra a')
               ->where('a.nivel_solicitud BETWEEN 3 AND 6')
               ->where("a.fecha_solicitud_compra BETWEEN '$anio-01-01' AND '$anio-12-31'");
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->row();
      }
      else {
          return FALSE;
      }
    }



    public function reporteDenegadas($minFecha,$maxFecha,$segmento,$porpagina){
     $this->db->select('sc.id_solicitud_compra,sc.fecha_solicitud_compra,dp.id_especifico,
     sec.nombre_seccion,sc.comentario_jefe,sc.comentario_autorizante,sc.comentario_compras,sc.memorandum')
              ->from('sic_solicitud_compra sc')
              ->join('sic_detalle_solicitud_compra dsc','dsc.id_solicitud_compra=sc.id_solicitud_compra')
              ->join('sic_detalle_producto dp','dp.id_detalleproducto=dsc.id_detalleproducto')
              ->join('org_seccion sec','sec.id_seccion=sc.id_seccion')
              ->group_by('sc.id_solicitud_compra')
              ->limit($segmento,$porpagina)
              ->where('sc.estado_solicitud_compra','DENEGADA');
     $query = $this->db->get();
     if ($query->num_rows() > 0) {
         return  $query->result();
     }
     else {
         return FALSE;
     }
   }

   public function reporteDenegadasExcel($minFecha,$maxFecha){
     $this->db->select('sc.id_solicitud_compra,sc.fecha_solicitud_compra,dp.id_especifico,
     sec.nombre_seccion,sc.comentario_jefe,sc.comentario_autorizante,sc.comentario_compras')
              ->from('sic_solicitud_compra sc')
              ->join('sic_detalle_solicitud_compra dsc','dsc.id_solicitud_compra=sc.id_solicitud_compra')
              ->join('sic_detalle_producto dp','dp.id_detalleproducto=dsc.id_detalleproducto')
              ->join('org_seccion sec','sec.id_seccion=sc.id_seccion')
              ->group_by('sc.id_solicitud_compra')
              ->where('sc.estado_solicitud_compra','DENEGADA');
     $query = $this->db->get();
     if ($query->num_rows() > 0) {
         return  $query->result();
     }
     else {
         return FALSE;
     }
  }

     public function reporteDenegadasTotal($minFecha,$maxFecha){
      $this->db->select('count(*) as total')
              ->from('sic_solicitud_compra sc')
              ->join('sic_detalle_solicitud_compra dsc','dsc.id_solicitud_compra=sc.id_solicitud_compra')
              ->join('sic_detalle_producto dp','dp.id_detalleproducto=dsc.id_detalleproducto')
              ->join('org_seccion sec','sec.id_seccion=sc.id_seccion')
              ->group_by('sc.id_solicitud_compra')
              ->where('sc.estado_solicitud_compra','DENEGADA');
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->row();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerDescripcionProductos($id){
      $this->db->select('p.nombre,det.cantidad,u.nombre as unidad')
               ->from('sic_solicitud_compra s')
               ->join('sic_detalle_solicitud_compra det','det.id_solicitud_compra=s.id_solicitud_compra')
               ->join('sic_detalle_producto dp','dp.id_detalleproducto=det.id_detalleproducto')
               ->join('sic_producto p','p.id_producto=dp.id_producto')
               ->join('sic_unidad_medida u','u.id_unidad_medida=p.id_unidad_medida')
               ->where('s.id_solicitud_compra',$id);
      $query=$this->db->get();
      $descripcion='';
      $count=0;
      foreach ($query->result() as $prod) {
        if ($count<3) {
          $descripcion.=$prod->cantidad.' '.$prod->nombre.'/'.$prod->unidad.', ';
        }
        $count++;
      }if($count<=3){
          $descripcion= substr($descripcion,0,-2).'.';
        }else{
          $descripcion= $descripcion .'ENTRE OTROS.';
        }
      return $descripcion;
    }
    public function obtenerSolicitudesAutorizante($nivel,$autorizante,$porpagina,$segmento){
      $this->db->select('s.id_solicitud_compra,s.numero_solicitud_compra,s.fecha_solicitud_compra,s.solicitante,
                        s.justificacion,s.documento_especificaciones,s.precio_estimado,s.comentario_jefe,s.comentario_autorizante,
                        s.comentario_compras,s.comentario,s.autorizante,s.forma_entrega,s.lugar_entrega,s.otras_condiciones,
                        s.propuesta_administrador,s.nivel_solicitud,s.nivel_anterior,s.id_seccion')
               ->from('sic_solicitud_compra s');
               for ($i=0; $i < count($nivel); $i++) {
                   $this->db->or_where('s.nivel_solicitud', $nivel[$i])
                            ->where('s.autorizante', $autorizante)
                            ->or_where('s.solicitante',$autorizante);
               }
      $this->db->order_by('s.id_solicitud_compra','desc')
               ->group_by('s.id_solicitud_compra');
      $this->db->limit($porpagina,$segmento);
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function totalSolicitudesAutorizante($nivel,$autorizante){
      $this->db->select('*')
               ->from('sic_solicitud_compra s');
               for ($i=0; $i < count($nivel); $i++) {
                 if ($i == 0) {
                   $this->db->where('s.nivel_solicitud', $nivel[$i]);
                 } else {
                   $this->db->or_where('s.nivel_solicitud', $nivel[$i]);
                 }
                 $this->db->where('s.autorizante', $autorizante);
               }
      $this->db->order_by('s.id_solicitud_compra','desc');
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerEmpleadoDatosId($id_empleado){
      $this->db->select("CONCAT_WS(' ',e.primer_nombre,e.segundo_nombre,e.primer_apellido,e.segundo_apellido) as nombre_empleado,
      e.id_empleado,f.funcional as cargo_funcional,s.depende as seccion_padre")
                ->from('sir_empleado e')
                ->join('org_usuario u','e.nr=u.nr')
                ->join('sir_empleado_informacion_laboral ei','e.id_empleado=ei.id_empleado')
                ->join('sir_cargo_funcional f','f.id_cargo_funcional=ei.id_cargo_funcional')
                ->join('org_seccion s','s.id_seccion=ei.id_seccion')
                ->join('tcm_empleado_informacion_laboral eil','eil.id_empleado=e.id_empleado')
                ->join('tcm_empleado_informacion_laboral eil2','eil.fecha_inicio=ei.fecha_inicio')
                ->where('e.id_estado',1)
                ->where('e.id_empleado',$id_empleado)
                ->where("f.id_nivel BETWEEN '1' AND '2'")
                ->group_by('e.id_empleado')
                ->limit(10);
        $query2 = $this->db->get();

        foreach ($query2->result  () as $empleado) {
          $this->db->select('nombre_seccion')
                   ->from('org_seccion')
                   ->where('id_seccion',$empleado->seccion_padre);
          $query3 = $this->db->get();
          $seccion =$query3->row();
          $empleado->seccion_padre=$seccion->nombre_seccion;
        }
      if ($query2->num_rows() > 0) {
          return  $query2->row();
      }
      else {
          return FALSE;
      }
    }
}
?>
