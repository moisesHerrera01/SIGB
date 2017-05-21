<?php
  class Orden_compra_model extends CI_Model{
    public $fecha;
    public $id_proveedores;
    public $id_solicitud_compra;
    public $observacion;
    public $lugar_notificaciones;
    public $estado_orden;
    public $numero_orden_compra;
    public $id_detalle_solicitud_disponibilidad;
    public $monto_total_oc;

    function __construct() {
        parent::__construct();
    }

    public function insertarOrdenCompra($data){
        $this->numero_orden_compra = $data['numero_orden_compra'];
        $this->fecha = $data['fecha'];
        $this->id_proveedores = $data['id_proveedores'];
        $this->id_solicitud_compra = $data['id_solicitud_compra'];
        $this->observacion = $data['observacion'];
        $this->lugar_notificaciones = $data['lugar_notificaciones'];
        $this->estado_orden = 'INGRESADA';
        $this->id_detalle_solicitud_disponibilidad = $data['id_detalle_solicitud_disponibilidad'];
        $this->monto_total_oc= $data['monto_total_oc'];
        $this->db->insert('sic_orden_compra', $this);
    }

    public function obtenerOrdenCompra($id) {
      $this->db->where("id_orden_compra", $id);
      $query = $this->db->get('sic_orden_compra');
      if ($query->num_rows() > 0) {
          return  $query->row();
      }
      else {
          return FALSE;
      }
    }

    public function actualizarOrdenCompra($id, $data){
      $this->db->where('id_orden_compra',$id);
      $this->db->update('sic_orden_compra', $data);
    }

    public function eliminarOrdenCompra($id){
      $this->db->delete('sic_orden_compra', array('id_orden_compra' => $id));
    }

    public function obtenerOrdenComprasLimit($porpagina, $segment) {
      $this->db->select('a.id_orden_compra, a.numero_orden_compra, a.fecha, a.observacion, a.lugar_notificaciones, b.id_proveedores, b.nombre_proveedor,
       c.numero_solicitud_compra,c.estado_solicitud_compra,c.nivel_solicitud,sd.monto_sub_total,c.id_solicitud_compra,
       sd.id_solicitud_disponibilidad,a.fecha,a.id_detalle_solicitud_disponibilidad,l.linea_trabajo,a.monto_total_oc')
               ->from('sic_orden_compra a')
               ->join('sic_proveedores b', 'a.id_proveedores = b.id_proveedores')
               ->join('sic_solicitud_compra c', 'a.id_solicitud_compra = c.id_solicitud_compra')
               ->join('sic_detalle_solicitud_disponibilidad sd','a.id_detalle_solicitud_disponibilidad=sd.id_detalle_solicitud_disponibilidad')
               ->join('org_linea_trabajo l','l.id_linea_trabajo=sd.id_linea_trabajo')
               ->limit($segment, $porpagina)
               ->order_by('id_orden_compra desc');
       $query = $this->db->get();
       if ($query->num_rows() > 0) {
           return  $query->result();
       }
       else {
           return FALSE;
       }
    }

    function totalOrdenes(){
      return $this->db->count_all('sic_orden_compra');
    }

    public function obtenerProductos($id_orden_compra){
      $this->db->select('e.id_producto, c.cantidad')
               ->from('sic_orden_compra a')
               ->join('sic_solicitud_compra b', 'a.id_solicitud_compra = b.id_solicitud_compra')
               ->join('sic_detalle_solicitud_compra c', 'c.id_solicitud_compra = b.id_solicitud_compra')
               ->join('sic_detalle_producto d', 'd.id_detalleproducto = c.id_detalleproducto')
               ->join('sic_producto e', 'e.id_producto = d.id_producto')
               ->where('a.id_orden_compra = ' . $id_orden_compra);
       $query = $this->db->get();
       if ($query->num_rows() > 0) {
           return  $query->result();
       }
       else {
           return FALSE;
       }
    }

    public function obtenerEspecificoProductos($id_orden_compra) {
      $productos = $this->obtenerProductos($id_orden_compra);
      $detalleproductos = array();
      foreach ($productos as $esp_pro) {
        $this->db->select('b.id_especifico, a.nombre AS nombre_producto, b.id_detalleproducto')
                 ->from('sic_producto a')
                 ->join('sic_detalle_producto b', 'a.id_producto = b.id_producto')
                 ->where('a.id_producto = '.$esp_pro->id_producto);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
           foreach ($query->result() as $value) {
             $value->cantidad = $esp_pro->cantidad;
             $detalleproductos[] = $value;
           }
        }
      }
      return $detalleproductos;
    }

    public function buscarOrdenesAutocomplete($busca){
      $this->db->select('o.id_orden_compra,o.fecha,o.numero_orden_compra')
               ->from('sic_orden_compra o')
               ->join('sic_solicitud_compra s','o.id_solicitud_compra=s.id_solicitud_compra')
               ->where('s.nivel_solicitud',6)
               ->order_by("o.id_orden_compra", "asc")
               ->like('o.id_orden_compra', $busca)
               ->or_like('s.id_solicitud_compra', $busca);
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerOrdenesAutocomplete(){
      $this->db->select('o.id_orden_compra,o.numero_orden_compra,o.fecha')
               ->from('sic_orden_compra o')
               ->join('sic_solicitud_compra s','o.id_solicitud_compra=s.id_solicitud_compra')
               ->where('s.nivel_solicitud',6)
               ->order_by("o.id_orden_compra", "asc");
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

  public function obtenerOrdenesFiltro($tipo,$fecha_inicio,$fecha_fin,$segmento,$porpagina){

               if($tipo==NULL){
                 $this->db->select('o.id_orden_compra,o.numero_orden_compra,s.numero_solicitud_compra,f.nombre_fuente,
                 o.fecha,sec.nombre_seccion,p.nombre,prov.nombre_proveedor,o.monto_total_oc,det.cantidad,catp.tipo_empresa,
                 s.nivel_solicitud,s.fecha_solicitud_compra')
                          ->from('sic_orden_compra o')
                          ->join('sic_proveedores prov','prov.id_proveedores=o.id_proveedores')
                          ->join('sic_categoria_proveedor catp','catp.id_categoria_proveedor=prov.id_categoria_proveedor')
                          ->join('sic_solicitud_compra s','s.id_solicitud_compra=o.id_solicitud_compra')
                          ->join('sic_detalle_solicitud_compra det','det.id_solicitud_compra=s.id_solicitud_compra')
                          ->join('sic_detalle_producto dp','dp.id_detalleproducto=det.id_detalleproducto')
                          ->join('sic_producto p','p.id_producto=dp.id_producto')
                          ->join('sir_empleado e','e.id_empleado=s.solicitante')
                          ->join('sir_empleado_informacion_laboral eil','eil.id_empleado=e.id_empleado')
                          ->join('org_seccion sec','sec.id_seccion=eil.id_seccion')
                          ->join('sic_compromiso_presupuestario cp','cp.id_orden_compra=o.id_orden_compra','left')
                          ->join('sic_fuentes_fondo f','f.id_fuentes=cp.id_fuentes','left')
                          ->join('sic_detalle_solicitud_disponibilidad sd','sd.id_detalle_solicitud_disponibilidad=o.id_detalle_solicitud_disponibilidad')
                          ->order_by('o.id_orden_compra','asc')
                          ->group_by('o.id_orden_compra')
                          ->limit($segmento,$porpagina)
                          ->where("o.fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'");
               } else {
                 $this->db->select('o.id_orden_compra,o.numero_orden_compra,s.numero_solicitud_compra,f.nombre_fuente,o.fecha,
                 sec.nombre_seccion,p.nombre,prov.nombre_proveedor,o.monto_total_oc,det.cantidad,catp.tipo_empresa,
                 s.nivel_solicitud,s.fecha_solicitud_compra')
                          ->from('sic_orden_compra o')
                          ->join('sic_proveedores prov','prov.id_proveedores=o.id_proveedores')
                          ->join('sic_categoria_proveedor catp','catp.id_categoria_proveedor=prov.id_categoria_proveedor')
                          ->join('sic_solicitud_compra s','s.id_solicitud_compra=o.id_solicitud_compra')
                          ->join('sic_detalle_solicitud_compra det','det.id_solicitud_compra=s.id_solicitud_compra')
                          ->join('sic_detalle_producto dp','dp.id_detalleproducto=det.id_detalleproducto')
                          ->join('sic_producto p','p.id_producto=dp.id_producto')
                          ->join('sir_empleado e','e.id_empleado=s.solicitante')
                          ->join('sir_empleado_informacion_laboral eil','eil.id_empleado=e.id_empleado')
                          ->join('org_seccion sec','sec.id_seccion=eil.id_seccion')
                          ->join('sic_compromiso_presupuestario cp','cp.id_orden_compra=o.id_orden_compra')
                          ->join('sic_fuentes_fondo f','f.id_fuentes=cp.id_fuentes')
                          ->join('sic_detalle_solicitud_disponibilidad sd','sd.id_detalle_solicitud_disponibilidad=o.id_detalle_solicitud_disponibilidad')
                          ->order_by('o.id_orden_compra','asc')
                          ->group_by('o.id_orden_compra')
                          ->limit($segmento,$porpagina)
                          ->where('catp.tipo_empresa',$tipo)
                          ->where("o.fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'");
               }
      $query=$this->db->get();
      if ($query->num_rows()>0) {
        return $query->result();
      }else{
        return FALSE;
      }
    }

    public function obtenerOrdenesFiltroTotal($tipo,$fecha_inicio,$fecha_fin){
        $this->db->select('count(*) as total')
                 ->from('sic_orden_compra o')
                 ->join('sic_proveedores prov','prov.id_proveedores=o.id_proveedores')
                 ->join('sic_categoria_proveedor catp','catp.id_categoria_proveedor=prov.id_categoria_proveedor')
                 ->join('sic_solicitud_compra s','s.id_solicitud_compra=o.id_solicitud_compra')
                 ->join('sic_detalle_solicitud_compra det','det.id_solicitud_compra=s.id_solicitud_compra')
                 ->join('sic_detalle_producto dp','dp.id_detalleproducto=det.id_detalleproducto')
                 ->join('sic_producto p','p.id_producto=dp.id_producto')
                 ->join('sir_empleado e','e.id_empleado=s.solicitante')
                 ->join('sir_empleado_informacion_laboral eil','eil.id_empleado=e.id_empleado')
                 ->join('org_seccion sec','sec.id_seccion=eil.id_seccion')
                 ->join('sic_compromiso_presupuestario cp','cp.id_orden_compra=o.id_orden_compra','left')
                 ->join('sic_fuentes_fondo f','f.id_fuentes=cp.id_fuentes','left')
                 ->join('sic_detalle_solicitud_disponibilidad sd','sd.id_detalle_solicitud_disponibilidad=o.id_detalle_solicitud_disponibilidad')
                 ->group_by('o.id_orden_compra')
                 ->where('catp.tipo_empresa',$tipo)
                 ->where("o.fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'");
        $query=$this->db->get();
        if ($query->num_rows()>0) {
          return $query->row();
        }else{
          return FALSE;
        }
      }

      public function obtenerDescripcionProductos($id){
        $this->db->select('p.nombre,det.cantidad,u.nombre as unidad')
                 ->from('sic_orden_compra o')
                 ->join('sic_detalle_orden_resumen det','det.id_orden_compra=o.id_orden_compra')
                 ->join('sic_detalle_producto dp','dp.id_detalleproducto=det.id_detalleproducto')
                 ->join('sic_producto p','p.id_producto=dp.id_producto')
                 ->join('sic_unidad_medida u','u.id_unidad_medida=p.id_unidad_medida')
                 ->where('o.id_orden_compra',$id);
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

      public function obtenerOrdenesExcel($tipo,$fecha_inicio,$fecha_fin){
        if($tipo==NULL){
          $this->db->select('o.id_orden_compra,o.numero_orden_compra,s.numero_solicitud_compra,f.nombre_fuente,o.fecha,
          sec.nombre_seccion,p.nombre,prov.nombre_proveedor,o.monto_total_oc,det.cantidad,catp.tipo_empresa,
          s.nivel_solicitud,s.fecha_solicitud_compra')
                   ->from('sic_orden_compra o')
                   ->join('sic_proveedores prov','prov.id_proveedores=o.id_proveedores')
                   ->join('sic_categoria_proveedor catp','catp.id_categoria_proveedor=prov.id_categoria_proveedor')
                   ->join('sic_solicitud_compra s','s.id_solicitud_compra=o.id_solicitud_compra')
                   ->join('sic_detalle_solicitud_compra det','det.id_solicitud_compra=s.id_solicitud_compra')
                   ->join('sic_detalle_producto dp','dp.id_detalleproducto=det.id_detalleproducto')
                   ->join('sic_producto p','p.id_producto=dp.id_producto')
                   ->join('sir_empleado e','e.id_empleado=s.solicitante')
                   ->join('sir_empleado_informacion_laboral eil','eil.id_empleado=e.id_empleado')
                   ->join('org_seccion sec','sec.id_seccion=eil.id_seccion')
                   ->join('sic_compromiso_presupuestario cp','cp.id_orden_compra=o.id_orden_compra','left')
                   ->join('sic_fuentes_fondo f','f.id_fuentes=cp.id_fuentes','left')
                   ->join('sic_detalle_solicitud_disponibilidad sd','sd.id_detalle_solicitud_disponibilidad=o.id_detalle_solicitud_disponibilidad')
                   ->order_by('o.id_orden_compra','asc')
                   ->group_by('o.id_orden_compra')
                   ->where("o.fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'");
        } else {
          $this->db->select('o.id_orden_compra,o.numero_orden_compra,s.numero_solicitud_compra,f.nombre_fuente,o.fecha,
          sec.nombre_seccion,p.nombre,prov.nombre_proveedor,o.monto_total_oc,det.cantidad,catp.tipo_empresa,
          s.nivel_solicitud,s.fecha_solicitud_compra')
                   ->from('sic_orden_compra o')
                   ->join('sic_proveedores prov','prov.id_proveedores=o.id_proveedores')
                   ->join('sic_categoria_proveedor catp','catp.id_categoria_proveedor=prov.id_categoria_proveedor')
                   ->join('sic_solicitud_compra s','s.id_solicitud_compra=o.id_solicitud_compra')
                   ->join('sic_detalle_solicitud_compra det','det.id_solicitud_compra=s.id_solicitud_compra')
                   ->join('sic_detalle_producto dp','dp.id_detalleproducto=det.id_detalleproducto')
                   ->join('sic_producto p','p.id_producto=dp.id_producto')
                   ->join('sir_empleado e','e.id_empleado=s.solicitante')
                   ->join('sir_empleado_informacion_laboral eil','eil.id_empleado=e.id_empleado')
                   ->join('org_seccion sec','sec.id_seccion=eil.id_seccion')
                   ->join('sic_compromiso_presupuestario cp','cp.id_orden_compra=o.id_orden_compra','left')
                   ->join('sic_fuentes_fondo f','f.id_fuentes=cp.id_fuentes','left')
                   ->join('sic_detalle_solicitud_disponibilidad sd','sd.id_detalle_solicitud_disponibilidad=o.id_detalle_solicitud_disponibilidad')
                   ->order_by('o.id_orden_compra','asc')
                   ->group_by('o.id_orden_compra')
                   ->where('catp.tipo_empresa',$tipo)
                   ->where("o.fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'");
        }

          $query=$this->db->get();
          if ($query->num_rows()>0) {
            return $query->result();
          }else{
            return FALSE;
          }
        }
  }
?>
