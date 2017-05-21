<?php
  class Compromiso_Presupuestario_Model extends CI_Model{
    public $id_compromiso;
    public $id_fuentes;
    public $id_orden_compra;
    public $numero_compromiso;
    function __construct() {
        parent::__construct();
    }
    public function insertarCompromiso($data){
        $this->numero_compromiso = $data['numero_compromiso'];
        $this->id_fuentes = $data['id_fuentes'];
        $this->id_orden_compra = $data['id_orden_compra'];
        $this->db->insert('sic_compromiso_presupuestario', $this);
        return $this->db->insert_id();
    }

    /*Consulta que sirve para el autocompletar de compromisos se devuelven los que contengan especificos
    del proceso de bodega*/
    public function obtenerCompromisosAutocomplete(){
      $this->db->select('c.id_compromiso,c.id_fuentes,f.nombre_fuente,c.id_orden_compra,c.numero_compromiso')
              ->from('sic_compromiso_presupuestario c')
              ->join('sic_fuentes_fondo f','f.id_fuentes = c.id_fuentes')
              ->join('sic_orden_compra o','o.id_orden_compra = c.id_orden_compra')
              ->join('sic_solicitud_compra s','s.id_solicitud_compra=o.id_solicitud_compra')
              ->join('sic_detalle_solicitud_compra ds','ds.id_solicitud_compra=s.id_solicitud_compra')
              ->join('sic_detalle_producto dp','dp.id_detalleproducto=ds.id_detalleproducto')
              ->join('sic_especifico es','es.id_especifico=dp.id_especifico')
              ->order_by('c.id_compromiso')
              ->where('es.proceso','BODEGA');
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    /*Consulta que sirve para el autocompletar de compromisos se devuelven los que contengan los especificos
    del proceso de bodega, en este caso se filtra por el número del compromiso*/
    public function buscarCompromisosAutocomplete($busca){
      $this->db->select('c.id_compromiso,c.id_fuentes,f.nombre_fuente,c.id_orden_compra,c.numero_compromiso')
              ->from('sic_compromiso_presupuestario c')
              ->join('sic_fuentes_fondo f','f.id_fuentes = c.id_fuentes')
              ->join('sic_orden_compra o','o.id_orden_compra = c.id_orden_compra')
              ->join('sic_solicitud_compra s','s.id_solicitud_compra=o.id_solicitud_compra')
              ->join('sic_detalle_solicitud_compra ds','ds.id_solicitud_compra=s.id_solicitud_compra')
              ->join('sic_detalle_producto dp','dp.id_detalleproducto=ds.id_detalleproducto')
              ->join('sic_especifico es','es.id_especifico=dp.id_especifico')
              ->order_by('c.id_compromiso')
              ->where('es.proceso','BODEGA')
              ->like('c.numero_compromiso',$busca);
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

      public function obtenerCompromiso($id){
          $this->db->where('id_compromiso',$id);
          $query = $this->db->get('sic_compromiso_presupuestario');
          if ($query->num_rows() > 0) {
            $nombre;
            foreach ($query->result() as $comp) {
              $nombre = $comp->institucion;
            }
            return  $nombre;
          }
          else {
              return FALSE;
          }
      }
      public function obtenerTodoCompromiso($id){
          $this->db->where('id_compromiso',$id);
          $query = $this->db->get('sic_compromiso_presupuestario');
          if ($query->num_rows() > 0) {
            return  $query->result();
          }
          else {
              return FALSE;
          }
      }
      public function buscarCompromisos($busca){
        $this->db->select('c.id_compromiso,c.numero_compromiso, c.id_fuentes,f.nombre_fuente,c.id_orden_compra,o.fecha,
        o.id_solicitud_compra,o.numero_orden_compra,s.nivel_solicitud,s.estado_solicitud_compra,sd.id_solicitud_disponibilidad,
        s.numero_solicitud_compra,s.fecha_solicitud_compra')
                ->from('sic_compromiso_presupuestario c')
                ->join('sic_fuentes_fondo f','f.id_fuentes = c.id_fuentes')
                ->join('sic_orden_compra o','o.id_orden_compra = c.id_orden_compra')
                ->join('sic_solicitud_compra s','s.id_solicitud_compra=o.id_solicitud_compra')
                ->order_by('c.id_compromiso desc')
                ->like('c.id_compromiso', $busca);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return  $query->result();
        }
        else {
            return FALSE;
        }
      }
    public function obtenerTotalCompromiso($id){
        $this->db->where('id_compromiso',$id);
        $query = $this->db->get('sic_compromiso_presupuestario');
        if ($query->num_rows() > 0) {
          $total;
          foreach ($query->result() as $comp) {
            $total = $comp->total;
          }
          return  $total;
        }
        else {
            return FALSE;
        }
    }
    public function actualizarCompromiso($id, $data){
      $this->db->where('id_compromiso',$id);
      $this->db->update('sic_compromiso_presupuestario', $data);
    }
    public function eliminarCompromiso($id){
      $this->db->delete('sic_compromiso_presupuestario', array('id_compromiso' => $id));
    }
    public function totalCompromisos(){
      return $this->db->count_all('sic_compromiso_presupuestario');
    }
    public function obtenerCompromisosLimit($porpagina, $segmento){
      $this->db->select('c.id_compromiso,c.numero_compromiso,c.id_fuentes,f.nombre_fuente,c.id_orden_compra,o.fecha,
      o.id_solicitud_compra,o.numero_orden_compra,s.nivel_solicitud,s.estado_solicitud_compra,sd.id_solicitud_disponibilidad,
      s.numero_solicitud_compra,s.fecha_solicitud_compra')
              ->from('sic_compromiso_presupuestario c')
              ->join('sic_fuentes_fondo f','f.id_fuentes = c.id_fuentes')
              ->join('sic_orden_compra o','o.id_orden_compra = c.id_orden_compra')
              ->join('sic_solicitud_compra s','s.id_solicitud_compra=o.id_solicitud_compra')
              ->join('sic_solicitud_disponibilidad sd','s.id_solicitud_compra=sd.id_solicitud_compra')
              ->order_by('c.id_compromiso desc')
              ->limit($porpagina,$segmento);
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }
    public function retornarEstado($id){
        $this->db->where('id_compromiso', $id);
        $query = $this->db->get('sic_compromiso_presupuestario');
        if ($query->num_rows() > 0) {
          $estado;
          foreach ($query->result() as $detalle) {
            $estado = $detalle->estado_compromiso;
          }
          return  $estado;
        }
        else {
            return FALSE;
        }
    }
    public function obtenerProductosSeccionTotal($fecha_inicio,$fecha_fin,$seccion){
      $this->db->order_by("f.id_factura", "asc");
      $this->db->select('count(*) as numero');
           $this->db->from('sic_detalle_factura df');
           $this->db->join('sic_factura f', 'f.id_factura = df.id_factura');
           $this->db->join('sic_detalle_producto dp', 'dp.id_detalleproducto = df.id_detalleproducto');
           $this->db->join('sic_producto p', 'p.id_producto = dp.id_producto');
           $this->db->join('sic_unidad_medida u', 'p.id_unidad_medida = u.id_unidad_medida');
           $this->db->join('org_seccion sec', 'sec.id_seccion = f.id_seccion');
           $this->db->join('sic_proveedores v', 'v.id_proveedores=f.id_proveedores');
           $this->db->join('sic_fuentes_fondo o', 'o.id_fuentes=f.id_fuentes');
           $this->db->where('f.estado','LIQUIDADA');
           $this->db->where('f.fecha_ingreso <',$fecha_fin);
           $this->db->where('f.fecha_ingreso >',$fecha_inicio);
           $this->db->where('f.id_seccion',$seccion);
           $query = $this->db->get();
           return $query->row();
    }
    public function obtenerProductosSeccionTodo($fecha_inicio,$fecha_fin,$seccion){
      $this->db->order_by("f.id_factura", "asc");
      $this->db->select('sec.nombre_seccion,v.nombre_proveedor,f.numero_factura,f.orden_compra,f.numero_compromiso,
      f.fecha_ingreso,df.cantidad,p.nombre as producto,u.nombre as unidad,o.nombre_fuente,o.id_fuentes,f.fecha_factura,
      dp.id_detalleproducto');
           $this->db->from('sic_detalle_factura df');
           $this->db->join('sic_factura f', 'f.id_factura = df.id_factura');
           $this->db->join('sic_detalle_producto dp', 'dp.id_detalleproducto = df.id_detalleproducto');
           $this->db->join('sic_producto p', 'p.id_producto = dp.id_producto');
           $this->db->join('sic_unidad_medida u', 'p.id_unidad_medida = u.id_unidad_medida');
           $this->db->join('org_seccion sec', 'sec.id_seccion = f.id_seccion');
           $this->db->join('sic_proveedores v', 'v.id_proveedores=f.id_proveedores');
           $this->db->join('sic_fuentes_fondo o', 'o.id_fuentes=f.id_fuentes');
           $this->db->where('f.estado','LIQUIDADA');
           $this->db->where('f.fecha_ingreso <',$fecha_fin);
           $this->db->where('f.fecha_ingreso >',$fecha_inicio);
           $this->db->where('f.id_seccion',$seccion);
           $this->db->group_by('df.id_detalle_factura');
           $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }
    public function ObtenerPorNumeroFactura($numero){
        $this->db->where('numero_factura',$numero);
        $query = $this->db->get('sic_factura');
        if ($query->num_rows() > 0) {
          return  $query->row('id_factura');
        }
        else {
            return FALSE;
        }
    }
    public function obtenerCompromisoId($id){
      $this->db->select('a.id_compromiso, a.id_orden_compra, d.id_proveedores, d.nombre_proveedor, f.id_fuentes,
       f.nombre_fuente, s.id_seccion,s.nombre_seccion,b.monto_total_oc,b.numero_orden_compra,a.numero_compromiso')
              ->from('sic_compromiso_presupuestario a')
              ->join('sic_orden_compra b','b.id_orden_compra = a.id_orden_compra')
              ->join('sic_solicitud_compra c','c.id_solicitud_compra = b.id_solicitud_compra')
              ->join('sic_solicitud_disponibilidad sd','sd.id_solicitud_compra=c.id_solicitud_compra')
              ->join('org_seccion s','s.id_seccion=c.id_seccion')
              ->join('sic_proveedores d','d.id_proveedores = b.id_proveedores')
              ->join('sic_fuentes_fondo f','f.id_fuentes = a.id_fuentes')
              ->where('id_compromiso', $id);
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->row_array();
      }
      else {
          return FALSE;
      }
    }
    public function obtenerDetalleOrden($id){
      $this->db->select('e.id_producto, e.nombre AS nombre_producto, d.id_especifico, dsc.cantidad, dsc.precio,
      d.id_detalleproducto,u.nombre as unidad')
               ->from('sic_compromiso_presupuestario a')
               ->join('sic_orden_compra b', 'a.id_orden_compra = b.id_orden_compra')
               ->join('sic_solicitud_compra s','s.id_solicitud_compra=b.id_solicitud_compra')
               ->join('sic_detalle_solicitud_compra dsc','dsc.id_solicitud_compra=s.id_solicitud_compra')
               ->join('sic_detalle_producto d', 'dsc.id_detalleproducto = d.id_detalleproducto')
               ->join('sic_producto e', 'd.id_producto = e.id_producto')
               ->join('sic_unidad_medida u','u.id_unidad_medida=e.id_unidad_medida')
               ->where('a.id_compromiso = '.$id);
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
         return  $query->result_array();
      }
      else {
         return FALSE;
      }
    }
    public function existeFactura($comp){
      $this->db->select('f.numero_compromiso')
      ->from('sic_factura f')
      ->where('f.numero_compromiso',$comp);
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
         return  TRUE;
      }
      else {
         return FALSE;
      }
    }
    public function obtenerGastoComprasSeccion($seccion, $minFecha, $maxFecha) {
      $this->db->select('SUM(b.total) total')
               ->from("sic_solicitud_compra a")
               ->join("sic_detalle_solicitud_compra b", "a.id_solicitud_compra = b.id_solicitud_compra")
               ->where("a.nivel_solicitud", 7)
               ->where("a.id_seccion", $seccion)
               ->where("a.fecha_solicitud_compra BETWEEN '$minFecha' AND '$maxFecha'");
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
         return  $query->row();
      }
      else {
         return FALSE;
      }
    }

/*Obtiene el numero maximo de requerimiento correspondiente a la fuente de fondos del parametro, esto para el año
en curso*/
    public function obtenerUltimoFuente($id_fuentes){
      date_default_timezone_set('America/El_Salvador');
      $anyo=20;
      $fecha_min=date($anyo."y-01-01");
      $fecha_max=date($anyo."y-12-31");
      $this->db->select('max(s.numero_solicitud_compra) as ultimo')
               ->from('sic_solicitud_compra s')
               ->join('sic_orden_compra oc','oc.id_solicitud_compra=s.id_solicitud_compra')
               ->join('sic_compromiso_presupuestario cp','cp.id_orden_compra=oc.id_orden_compra')
               ->where('cp.id_fuentes',$id_fuentes)
               ->where('s.nivel_solicitud',7)
               ->where("oc.fecha BETWEEN '$fecha_min' AND '$fecha_max'");
      $query=$this->db->get();
      return $query->row();
    }

/*Obtine el id del requerimiento de compra correspondiente a la orden de compra*/
    public function obtenerSolicitudCompraPorOrden($id_orden_compra){
      $this->db->select('id_solicitud_compra')
               ->from('sic_orden_compra')
               ->where('id_orden_compra',$id_orden_compra);
      $query=$this->db->get();
      $sol_compra=$query->row();
      return $sol_compra->id_solicitud_compra;
    }
}
?>
