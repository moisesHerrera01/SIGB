<?php
 class Solicitud_disponibilidad_model extends CI_Model{

 	public $id_solicitud_compra;
 	public $fecha;
  public $fecha_ingreso;
 	public $numero_confirmacion;
 	public $observaciones;

 	 function __construct() {
        parent::__construct();
    }

    public function insertarSolicitudDisponibilidad($data){
        $this->id_solicitud_compra = $data['id_solicitud_compra'];
        $this->fecha_ingreso=$data['fecha_ingreso'];
        $this->fecha=$data['fecha'];
        $this->numero_confirmacion=$data['id_solicitud_compra'];
        $this->observaciones=$data['observaciones'];
        $this->db->insert('sic_solicitud_disponibilidad', $this);
    }
     public function obtenerSolicitudesDisponibilidad(){
        $this->db->order_by("id_solicitud_disponibilidad desc");
        $query = $this->db->get('sic_solicitud_disponibilidad');
        if ($query->num_rows() > 0) {
            return  $query->result();
        }
        else {
            return FALSE;
        }
      }

      public function buscarSolicitudes($busca){
      $this->db->like('id_solicitud_disponibilidad', $busca);
      $this->db->or_like('id_solicitud_compra', $busca);
      $this->db->order_by("id_solicitud_disponibilidad desc");
      $query = $this->db->get('sic_solicitud_disponibilidad', 10);
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

     public function actualizarSolicitudDisponibilidad($id, $data){
      $this->db->where('id_solicitud_disponibilidad',$id);
      $this->db->update('sic_solicitud_disponibilidad', $data);
    }


    public function eliminarSolicitudDisponibilidad($id){
      $this->db->delete('sic_solicitud_disponibilidad', array('id_solicitud_disponibilidad' => $id));
    }

    public function totalSolicitudesDisponibilidad(){
      return $this->db->count_all('sic_solicitud_disponibilidad');
    }

     public function obtenerSolicitudesLimit($porpagina, $segmento){
       $this->db->order_by("id_solicitud_disponibilidad desc");
       $query = $this->db->get('sic_solicitud_disponibilidad', $porpagina, $segmento);
       if ($query->num_rows() > 0) {
           return  $query->result();
       }
       else {
           return FALSE;
       }
     }

 public function obtenerSolicitudCompleta($id){
        $this->db->where('id_solicitud_disponibilidad',$id);
        $query = $this->db->get('sic_solicitud_disponibilidad');
        return  $query->row();
    }

      public function buscarSolicitudesDisponibilidadLimit($busca){
       $this->db->select('sd.id_solicitud_disponibilidad,sd.id_solicitud_compra,sd.numero_confirmacion,sd.fecha,
       dp.id_especifico,sec.nombre_seccion,sd.observaciones,sd.fecha_ingreso,sc.estado_solicitud_compra,sc.nivel_solicitud')
                ->from('sic_solicitud_disponibilidad sd')
                ->join('sic_solicitud_compra sc','sd.id_solicitud_compra=sc.id_solicitud_compra')
                ->join('sir_empleado em','em.id_empleado=sc.solicitante')
                ->join('sir_empleado_informacion_laboral e','e.id_empleado=em.id_empleado')
                ->join('org_seccion sec','sec.id_seccion=e.id_seccion')
                ->join('sic_detalle_solicitud_compra dsc','dsc.id_solicitud_compra=sc.id_solicitud_compra')
                ->join('sic_detalle_producto dp','dp.id_detalleproducto=dsc.id_detalleproducto')
                ->where('sc.nivel_solicitud>3')
                ->group_by('sd.id_solicitud_disponibilidad')
                ->order_by('sc.id_solicitud_compra desc')
                ->like('sd.id_solicitud_disponibilidad',$busca)
                ->or_like('sc.id_solicitud_compra',$busca);
       $query = $this->db->get();
       if ($query->num_rows() > 0) {
           return  $query->result();
       }
       else {
           return FALSE;
       }
     }

     public function obtenerSolicitudesDisponibilidadLimit($porpagina,$segmento){
       $this->db->select('sd.id_solicitud_disponibilidad,sd.id_solicitud_compra,sd.numero_confirmacion,sd.fecha,
       dp.id_especifico,sec.nombre_seccion,sd.observaciones,sc.estado_solicitud_compra,sc.nivel_solicitud,sd.fecha_ingreso')
               ->from('sic_solicitud_disponibilidad sd')
               ->join('sic_solicitud_compra sc','sd.id_solicitud_compra=sc.id_solicitud_compra')
               ->join('sir_empleado em','em.id_empleado=sc.solicitante')
               ->join('sir_empleado_informacion_laboral e','e.id_empleado=em.id_empleado')
               ->join('org_seccion sec','sec.id_seccion=e.id_seccion')
               ->join('sic_detalle_solicitud_compra dsc','dsc.id_solicitud_compra=sc.id_solicitud_compra')
               ->join('sic_detalle_producto dp','dp.id_detalleproducto=dsc.id_detalleproducto')
               ->where('sc.nivel_solicitud>3')
               ->group_by('sd.id_solicitud_disponibilidad')
               ->order_by('sc.id_solicitud_compra desc')
               ->limit($porpagina,$segmento);
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerDisponibilidad($id_solicitud_compra){
     $this->db->select('sd.id_solicitud_disponibilidad,sd.id_solicitud_compra,sd.numero_confirmacion,sd.fecha,
     dp.id_especifico,sec.nombre_seccion,sd.observaciones,sc.estado_solicitud_compra,sc.nivel_solicitud,pro.nombre,
     dsc.cantidad,sc.justificacion,sc.precio_estimado,l.linea_trabajo')
              ->from('sic_solicitud_disponibilidad sd')
              ->join('sic_solicitud_compra sc','sd.id_solicitud_compra=sc.id_solicitud_compra')
              ->join('sir_empleado em','em.id_empleado=sc.solicitante')
              ->join('sir_empleado_informacion_laboral e','e.id_empleado=em.id_empleado')
              ->join('org_linea_trabajo l','e.id_linea_trabajo=l.id_linea_trabajo')
              ->join('org_seccion sec','sec.id_seccion=e.id_seccion')
              ->join('sic_detalle_solicitud_compra dsc','dsc.id_solicitud_compra=sc.id_solicitud_compra')
              ->join('sic_detalle_producto dp','dp.id_detalleproducto=dsc.id_detalleproducto')
              ->join('sic_producto pro','pro.id_producto=dp.id_producto')
              ->where('sc.id_solicitud_compra',$id_solicitud_compra)
              ->group_by('dsc.id_detalle_solicitud_compra');
     $query = $this->db->get();
     if ($query->num_rows() > 0) {
         return  $query->result();
     }
     else {
         return FALSE;
     }
   }

     public function existeSolicitudDisponibilidad($id){
      $this->db->where('id_solicitud_disponibilidad',$id);
      $query = $this->db->get('sic_detalle_solicitud_compra');
      if ($query->num_rows()>0){
        return TRUE;
      }
      else {
        return FALSE;
      }
    }

    public function obtenerSolicitudCompraAuto($nivel){
     $this->db->select('sc.id_solicitud_compra,dp.id_especifico,sec.nombre_seccion')
              ->from('sic_solicitud_compra sc')
              ->join('sir_empleado em','em.id_empleado=sc.solicitante')
              ->join('sir_empleado_informacion_laboral e','e.id_empleado=em.id_empleado')
              ->join('org_seccion sec','sec.id_seccion=e.id_seccion')
              ->join('sic_detalle_solicitud_compra dsc','dsc.id_solicitud_compra=sc.id_solicitud_compra')
              ->join('sic_detalle_producto dp','dp.id_detalleproducto=dsc.id_detalleproducto')
              ->group_by('sc.id_solicitud_compra')
              ->order_by('sc.id_solicitud_compra asc')
              ->where('sc.nivel_solicitud',$nivel);
     $query = $this->db->get();
     if ($query->num_rows() > 0) {
         return  $query->result();
     }
     else {
         return FALSE;
     }
   }

   public function buscarSolicitudCompraAuto($nivel,$busca){
    $this->db->select('sc.id_solicitud_compra,l.linea_trabajo,dp.id_especifico,sec.nombre_seccion')
             ->from('sic_solicitud_compra sc')
             ->join('sir_empleado em','em.id_empleado=sc.solicitante')
             ->join('sir_empleado_informacion_laboral e','e.id_empleado=em.id_empleado')
             ->join('org_seccion sec','sec.id_seccion=e.id_seccion')
             ->join('sic_detalle_solicitud_compra dsc','dsc.id_solicitud_compra=sc.id_solicitud_compra')
             ->join('sic_detalle_producto dp','dp.id_detalleproducto=dsc.id_detalleproducto')
             ->group_by('sc.id_solicitud_compra')
             ->order_by('sc.id_solicitud_compra asc')
             ->where('sc.id_solicitud_compra',$busca)
             ->where('sc.nivel_solicitud',$nivel);
    $query = $this->db->get();
    if ($query->num_rows() > 0) {
        return  $query->result();
    }
    else {
        return FALSE;
    }
  }

  public function buscarLineasTrabajo($linea){
    $this->db->select('linea_trabajo,id_linea_trabajo')
             ->from('org_linea_trabajo')
             ->where('id_linea_trabajo',$linea)
             ->order_by('id_linea_trabajo');
    $query=$this->db->get();
    if($query->num_rows()>0){
      return $query->result();
    }else{
      return FALSE;
    }
  }

  public function obtenerLineasTrabajo(){
    $this->db->select('linea_trabajo,id_linea_trabajo')
             ->from('org_linea_trabajo')
             ->order_by('id_linea_trabajo');
    $query=$this->db->get();
    if($query->num_rows()>0){
      return $query->result();
    }else{
      return FALSE;
    }
  }

  public function reporteDisponibilidad($minFecha,$maxFecha,$segmento,$porpagina){
   $this->db->select('sd.id_solicitud_disponibilidad,sd.id_solicitud_compra,dp.id_especifico,
   sec.nombre_seccion,sc.justificacion, sd.fecha, l.linea_trabajo, sc.numero_solicitud_compra,f.nombre_fuente,
   sc.nivel_solicitud,sc.fecha_solicitud_compra')
            ->from('sic_solicitud_disponibilidad sd')
            ->join('sic_solicitud_compra sc','sd.id_solicitud_compra=sc.id_solicitud_compra')
            ->join('sic_orden_compra o','o.id_solicitud_compra=sc.id_solicitud_compra')
            ->join('sic_compromiso_presupuestario cp','cp.id_orden_compra=o.id_orden_compra','left')
            ->join('sic_fuentes_fondo f','f.id_fuentes=cp.id_fuentes','left')
            ->join('sir_empleado_informacion_laboral ei','ei.id_empleado=sc.solicitante')
            ->join('org_linea_trabajo l','l.id_linea_trabajo=ei.id_linea_trabajo')
            ->join('org_seccion sec','sec.id_seccion=sc.id_seccion')
            ->join('sic_detalle_solicitud_compra dsc','dsc.id_solicitud_compra=sc.id_solicitud_compra')
            ->join('sic_detalle_producto dp','dp.id_detalleproducto=dsc.id_detalleproducto')
            ->group_by('sd.id_solicitud_disponibilidad')
            ->limit($segmento,$porpagina)
            ->where("sd.fecha_ingreso BETWEEN '$minFecha' AND '$maxFecha'");
   $query = $this->db->get();
   if ($query->num_rows() > 0) {
       return  $query->result();
   }
   else {
       return FALSE;
   }
 }

 public function reporteDisponibilidadExcel($minFecha,$maxFecha){
   $this->db->select('sd.id_solicitud_disponibilidad,sd.id_solicitud_compra,dp.id_especifico,
   sec.nombre_seccion,sc.justificacion, sd.fecha, l.linea_trabajo, sc.numero_solicitud_compra,f.nombre_fuente,
   sc.nivel_solicitud,sc.fecha_solicitud_compra')
            ->from('sic_solicitud_disponibilidad sd')
            ->join('sic_solicitud_compra sc','sd.id_solicitud_compra=sc.id_solicitud_compra')
            ->join('sic_orden_compra o','o.id_solicitud_compra=sc.id_solicitud_compra')
            ->join('sic_compromiso_presupuestario cp','cp.id_orden_compra=o.id_orden_compra','left')
            ->join('sic_fuentes_fondo f','f.id_fuentes=cp.id_fuentes','left')
           ->join('sir_empleado_informacion_laboral ei','ei.id_empleado=sc.solicitante')
           ->join('org_linea_trabajo l','l.id_linea_trabajo=ei.id_linea_trabajo')
           ->join('org_seccion sec','sec.id_seccion=sc.id_seccion')
           ->join('sic_detalle_solicitud_compra dsc','dsc.id_solicitud_compra=sc.id_solicitud_compra')
           ->join('sic_detalle_producto dp','dp.id_detalleproducto=dsc.id_detalleproducto')
           ->group_by('sd.id_solicitud_disponibilidad')
           ->where("sd.fecha_ingreso BETWEEN '$minFecha' AND '$maxFecha'");
  $query = $this->db->get();
  if ($query->num_rows() > 0) {
      return  $query->result();
  }
  else {
      return FALSE;
  }
}

   public function reporteDisponibilidadTotal($minFecha,$maxFecha){
    $this->db->select('count(*) as total')
             ->from('sic_solicitud_disponibilidad sd')
             ->join('sic_detalle_solicitud_disponibilidad dsd','dsd.id_solicitud_disponibilidad=sd.id_solicitud_disponibilidad')
             ->join('sic_solicitud_compra sc','sd.id_solicitud_compra=sc.id_solicitud_compra')
             ->join('sic_orden_compra o','o.id_solicitud_compra=sc.id_solicitud_compra')
             ->join('sic_compromiso_presupuestario cp','cp.id_orden_compra=o.id_orden_compra','left')
             ->join('sic_fuentes_fondo f','f.id_fuentes=cp.id_fuentes','left')
             ->join('org_seccion sec','sec.id_seccion=sc.id_seccion')
             ->join('sic_detalle_solicitud_compra dsc','dsc.id_solicitud_compra=sc.id_solicitud_compra')
             ->join('sic_detalle_producto dp','dp.id_detalleproducto=dsc.id_detalleproducto')
             ->group_by('sd.id_solicitud_disponibilidad')
             ->where("sd.fecha_ingreso BETWEEN '$minFecha' AND '$maxFecha'");
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
             ->from('sic_solicitud_disponibilidad sd')
             ->join('sic_solicitud_compra s','s.id_solicitud_compra=sd.id_solicitud_compra')
             ->join('sic_detalle_solicitud_compra det','det.id_solicitud_compra=s.id_solicitud_compra')
             ->join('sic_detalle_producto dp','dp.id_detalleproducto=det.id_detalleproducto')
             ->join('sic_producto p','p.id_producto=dp.id_producto')
             ->join('sic_unidad_medida u','u.id_unidad_medida=p.id_unidad_medida')
             ->where('sd.id_solicitud_disponibilidad',$id);
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

  public function obtenerSolicitudesDisponibilidadFiltro($fecha_inicio,$fecha_fin,$id_linea,$segmento,$porpagina){
      $this->db->select('o.id_orden_compra,o.numero_orden_compra,s.numero_solicitud_compra,f.nombre_fuente,o.fecha,
      sec.nombre_seccion,prov.nombre_proveedor,dsd.id_linea_trabajo,dsd.monto_sub_total,lt.linea_trabajo,s.fecha_solicitud_compra')
               ->from('sic_orden_compra o')
               ->join('sic_proveedores prov','prov.id_proveedores=o.id_proveedores')
               ->join('sic_solicitud_compra s','s.id_solicitud_compra=o.id_solicitud_compra')
               ->join('sir_empleado e','e.id_empleado=s.solicitante')
               ->join('sir_empleado_informacion_laboral eil','eil.id_empleado=e.id_empleado')
               ->join('org_seccion sec','sec.id_seccion=eil.id_seccion')
               ->join('sic_compromiso_presupuestario cp','cp.id_orden_compra=o.id_orden_compra')
               ->join('sic_fuentes_fondo f','f.id_fuentes=cp.id_fuentes')
               ->join('sic_detalle_solicitud_disponibilidad dsd','dsd.id_detalle_solicitud_disponibilidad=o.id_detalle_solicitud_disponibilidad')
               ->join('org_linea_trabajo lt','lt.id_linea_trabajo=dsd.id_linea_trabajo')
               ->order_by('o.id_orden_compra','asc')
               ->group_by('o.id_detalle_solicitud_disponibilidad')
               ->limit($segmento,$porpagina)
               ->where('dsd.id_linea_trabajo',$id_linea)
               ->where("o.fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'");
      $query=$this->db->get();
      if ($query->num_rows()>0) {
        return $query->result();
      }else{
        return FALSE;
      }
    }

    public function obtenerSolicitudesDisponibilidadTotal($fecha_inicio,$fecha_fin,$id_linea){
        $this->db->select('count(*) as total')
                 ->from('sic_orden_compra o')
                 ->join('sic_proveedores prov','prov.id_proveedores=o.id_proveedores')
                 ->join('sic_solicitud_compra s','s.id_solicitud_compra=o.id_solicitud_compra')
                 ->join('sir_empleado e','e.id_empleado=s.solicitante')
                 ->join('sir_empleado_informacion_laboral eil','eil.id_empleado=e.id_empleado')
                 ->join('org_seccion sec','sec.id_seccion=eil.id_seccion')
                 ->join('sic_compromiso_presupuestario cp','cp.id_orden_compra=o.id_orden_compra')
                 ->join('sic_fuentes_fondo f','f.id_fuentes=cp.id_fuentes')
                 ->join('sic_detalle_solicitud_disponibilidad dsd','o.id_detalle_solicitud_disponibilidad=dsd.id_detalle_solicitud_disponibilidad')
                 ->join('org_linea_trabajo lt','lt.id_linea_trabajo=dsd.id_linea_trabajo')
                 ->order_by('o.id_orden_compra','asc')
                 ->group_by('o.id_orden_compra')
                 ->where('dsd.id_linea_trabajo',$id_linea)
                 ->where("o.fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'");
        $query=$this->db->get();
        if ($query->num_rows()>0) {
          return $query->row();
        }else{
          return FALSE;
        }
      }

      public function obtenerSolicitudesDisponibilidadExcel($fecha_inicio,$fecha_fin,$id_linea){
          $this->db->select('o.id_orden_compra,o.numero_orden_compra,s.numero_solicitud_compra,f.nombre_fuente,o.fecha,
          sec.nombre_seccion,prov.nombre_proveedor,dsd.monto_sub_total,s.nivel_solicitud,s.fecha_solicitud_compra')
                   ->from('sic_orden_compra o')
                   ->join('sic_proveedores prov','prov.id_proveedores=o.id_proveedores')
                   ->join('sic_solicitud_compra s','s.id_solicitud_compra=o.id_solicitud_compra')
                   ->join('sir_empleado e','e.id_empleado=s.solicitante')
                   ->join('sir_empleado_informacion_laboral eil','eil.id_empleado=e.id_empleado')
                   ->join('org_seccion sec','sec.id_seccion=eil.id_seccion')
                   ->join('sic_compromiso_presupuestario cp','cp.id_orden_compra=o.id_orden_compra')
                   ->join('sic_fuentes_fondo f','f.id_fuentes=cp.id_fuentes')
                   ->join('sic_detalle_solicitud_disponibilidad dsd','o.id_detalle_solicitud_disponibilidad=dsd.id_detalle_solicitud_disponibilidad')
                   ->order_by('o.id_orden_compra','asc')
                   ->group_by('o.id_orden_compra')
                   ->where('dsd.id_linea_trabajo',$id_linea)
                   ->where("o.fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'");
          $query=$this->db->get();
          if ($query->num_rows()>0) {
            return $query->result();
          }else{
            return FALSE;
          }
      }

      public function obtenerDisponibilidadAuto() {
        $this->db->select('a.id_solicitud_compra, b.id_detalle_solicitud_disponibilidad, c.linea_trabajo, b.monto_sub_total')
                 ->from('sic_solicitud_disponibilidad a')
                 ->join('sic_detalle_solicitud_disponibilidad b', 'a.id_solicitud_disponibilidad = b.id_solicitud_disponibilidad')
                 ->join('org_linea_trabajo c', 'b.id_linea_trabajo = c.id_linea_trabajo')
                 ->join('sic_solicitud_compra sc','sc.id_solicitud_compra=a.id_solicitud_compra')
                 ->order_by('a.id_solicitud_compra','desc')
                 ->where('sc.nivel_solicitud',5)
                 ->or_where('sc.nivel_solicitud',6);
        $query = $this->db->get();
        if ($query->num_rows()>0) {
          return $query->result();
        }else{
          return FALSE;
        }
      }

      public function buscarDisponibilidadAuto($busca) {
        $this->db->select('a.id_solicitud_compra, b.id_detalle_solicitud_disponibilidad, c.linea_trabajo, b.monto_sub_total')
                 ->from('sic_solicitud_disponibilidad a')
                 ->join('sic_detalle_solicitud_disponibilidad b', 'a.id_solicitud_disponibilidad = b.id_solicitud_disponibilidad')
                 ->join('org_linea_trabajo c', 'b.id_linea_trabajo = c.id_linea_trabajo')
                 ->like('a.id_solicitud_compra', $busca);

        $query = $this->db->get();
        if ($query->num_rows()>0) {
          return $query->result();
        }else{
          return FALSE;
        }
      }

}
?>
