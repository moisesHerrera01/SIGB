<?php
  class Detalle_solicitud_producto_model extends CI_Model{

    public $id_detalle_solicitud_producto;
    public $cantidad;
    public $precio;
    public $total;
    public $id_detalleproducto;
    public $id_solicitud;
    public $estado_solicitud_producto;
    public $id_fuentes;

    function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function insertarDetalleSolicitudProducto($data){
        $this->id_detalle_solicitud_producto = $data['id_detalle_solicitud_producto'];
        $this->cantidad = $data['cantidad'];
        $this->precio = $data['precio'];
        $this->total = $data['total'];
        $this->id_detalleproducto = $data['id_detalleproducto'];
        $this->id_solicitud = $data['id_solicitud'];
        $this->estado_solicitud_producto = 'INGRESADO';
        $this->id_fuentes = $data['id_fuentes'];

        $this->db->insert('sic_detalle_solicitud_producto', $this);
    }

    public function obtenerDetalleSolicitudProductos($id){
      $this->db->order_by("id_detalle_solicitud_producto", "asc");
      $this->db->where('id_solicitud',$id);
      $query = $this->db->get('sic_detalle_solicitud_producto');
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }
    public function obtenerDetalleSolicitudProductosDescargados($id){
      $this->db->order_by("id_detalle_solicitud_producto", "asc");
      $this->db->where('id_solicitud',$id);
      $this->db->where('estado_solicitud_producto','DESCARGADO');
      $query = $this->db->get('sic_detalle_solicitud_producto');
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerDatos($id){
      $this->db->order_by("id_producto", "asc");
      $this->db->select('p.nombre as producto,p.id_producto,e.id_especifico,e.nombre_especifico,u.nombre as unidad');
           $this->db->from('sic_detalle_producto d');
           $this->db->join('sic_especifico e', 'e.id_especifico = d.id_especifico');
           $this->db->join('sic_producto p', 'p.id_producto = d.id_producto');
           $this->db->join('sic_unidad_medida u', 'u.id_unidad_medida = p.id_unidad_medida');
           $this->db->where('d.id_detalleproducto',$id);
           $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function asignarIdDetalleProducto($id){
      $this->db->select('id_detalleproducto');
      $this->db->from('sic_detalle_producto');
      $this->db->where('id_producto',$id);
      $query = $this->db->get();
      if($query->num_rows()>0){
        $det;
        foreach ($query->result() as $detalle) {
          $det=$detalle->id_detalleproducto;
        }
        return $det;
      }else{
        return FALSE;
      }
    }

    public function obtenerDetalleSolicitudProducto($id){
        $this->db->where('id_detalle_solicitud_producto',$id);
        $query = $this->db->get('sic_detalle_solicitud_producto');
        if ($query->num_rows() > 0) {
          $sol;
          foreach ($query->result() as $det) {
            $sol = $det->id_solicitud;
          }
          return  $sol;
        }
        else {
            return FALSE;
        }
    }
    public function obtenerDetalleCompleto($id){
      $this->db->where('id_detalle_solicitud_producto',$id);
      $query=$this->db->get('sic_detalle_solicitud_producto');
      return $query->row();
    }

    public function eliminarDetalleSolicitudProducto($id){
      $this->db->delete('sic_detalle_solicitud_producto', array('id_detalle_solicitud_producto' => $id));
    }

    public function descargar($id){
      $dat = array(
        'estado_solicitud_producto' => 'DESCARGADO',
      );
      $this->db->where('id_detalle_solicitud_producto', $id);
      $this->db->update('sic_detalle_solicitud_producto', $dat);
    }

    public function retornarEstado($id){
        $this->db->where('id_detalle_solicitud_producto', $id);
        $query = $this->db->get('sic_detalle_solicitud_producto');
        if ($query->num_rows() > 0) {
          $estado;
          foreach ($query->result() as $detalle) {
            $estado = $detalle->estado_solicitud_producto;
          }
          return  $estado;
        }
        else {
            return FALSE;
        }
    }

    public function actualizarDetalleSolicitudProducto($id,$data){
      $this->db->where('id_detalle_solicitud_producto',$id);
      $this->db->update('sic_detalle_solicitud_producto', $data);
    }

    public function preciosFecha2($id,$id_fuentes){
      $this->db->order_by("f.fecha_ingreso", "asc");
      $this->db->select('df.cantidad,df.precio,f.fecha_ingreso');
           $this->db->from('sic_factura f');
           $this->db->join('sic_detalle_factura df', 'f.id_factura = df.id_factura');
           $this->db->where('f.estado','LIQUIDADA');
           $this->db->where('df.id_detalleproducto',$id);
           $this->db->where('f.id_fuentes',$id_fuentes);
           $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function existencia($id_detalleproducto,$id_fuentes){
      $this->db->select('SUM(k.cantidad) as existencia')
               ->from('sic_kardex k')
               //->join('sic_kardex_saldo ks','ks.id_kardex=k.id_kardex')
               ->where('k.id_detalleproducto',$id_detalleproducto)
               ->where('k.movimiento','ENTRADA')
               ->where('k.id_fuentes',$id_fuentes)
               ->order_by('k.id_kardex','desc');
      $query1=$this->db->get();

      $this->db->select('SUM(k.cantidad) as existencia')
               ->from('sic_kardex k')
               //->join('sic_kardex_saldo ks','ks.id_kardex=k.id_kardex')
               ->where('k.id_detalleproducto',$id_detalleproducto)
               ->where('k.movimiento','SALIDA')
               ->where('k.id_fuentes',$id_fuentes)
               ->order_by('k.id_kardex','desc');
      $query2=$this->db->get();
      $existencias = array(
        'entrada' => $query1->row()->existencia,
        'salida' => $query2->row()->existencia,
        'existencia' =>$query1->row()->existencia-$query2->row()->existencia
      );
      return $existencias;
    }

    public function existenciaFuentes($id,$mov){
      $this->db->select('id_fuentes,SUM(cantidad) as existencia');
           $this->db->from('sic_kardex');
           $this->db->where('id_detalleproducto',$id);
           $this->db->where('movimiento',$mov);
           $this->db->group_by('id_fuentes');
           $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function existeSolicitud($id){
      $this->db->where('id_solicitud',$id);
      $query = $this->db->get('sic_detalle_solicitud_producto');
      if ($query->num_rows()>0){
        return TRUE;
      }
      else {
        return FALSE;
      }
    }

    public function obtenerEspecificosLimit($fecha_inicio,$fecha_fin,$fuente,$segmento,$porpagina){
      $segmento = intval($segmento);
      $this->db->order_by("id_especifico", "asc");
      $this->db->select('e.id_especifico,e.nombre_especifico,k.id_detalleproducto,k.id_fuentes');
      $this->db->group_by('id_especifico');
           $this->db->from('sic_kardex k');
           $this->db->join('sic_detalle_producto d', 'd.id_detalleproducto = k.id_detalleproducto');
           $this->db->join('sic_especifico e', 'e.id_especifico = d.id_especifico');
           $this->db->where('k.fecha_ingreso <=',$fecha_fin);
           $this->db->where('k.fecha_ingreso >=',$fecha_inicio);
           $this->db->where('k.movimiento','SALIDA');
           $this->db->where('k.id_fuentes',$fuente);
           $this->db->limit($segmento,$porpagina);
           $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerEspecificosTotal($fecha_inicio,$fecha_fin,$fuente){
      $this->db->order_by("id_especifico", "asc");
      $this->db->select('e.id_especifico,e.nombre_especifico,k.id_detalleproducto,k.id_fuentes');
      $this->db->group_by('id_especifico');
           $this->db->from('sic_kardex k');
           $this->db->join('sic_detalle_producto d', 'd.id_detalleproducto = k.id_detalleproducto');
           $this->db->join('sic_especifico e', 'e.id_especifico = d.id_especifico');
           $this->db->where('k.fecha_ingreso <=',$fecha_fin);
           $this->db->where('k.fecha_ingreso >=',$fecha_inicio);
           $this->db->where('k.movimiento','SALIDA');
           $this->db->where('k.id_fuentes',$fuente);
           $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    function totalEspecifico($fecha_inicio,$fecha_fin,$fuente){
      $this->db->from('sic_kardex a')
               ->join('sic_detalle_producto b', 'a.id_detalleproducto = b.id_detalleproducto')
               ->where("a.fecha_ingreso BETWEEN '$fecha_inicio' AND '$fecha_fin'")
               ->where('a.movimiento','SALIDA')
               ->where('a.id_fuentes', $fuente)
               ->group_by('b.id_especifico');

      $query = $this->db->get();
      if($query->num_rows()>0){
        return $query->num_rows();
      }else{
        return FALSE;
      }
    }

    public function obtenerKardex(){
      $this->db->order_by("id_kardex", "asc");
      $this->db->select('k.cantidad,k.precio,d.id_especifico,k.movimiento,k.fecha_ingreso,k.id_fuentes');
      $this->db->from('sic_kardex k');
      $this->db->join('sic_detalle_producto d', 'd.id_detalleproducto = k.id_detalleproducto');
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerKardexProducto($id){
      $this->db->order_by("id_kardex", "asc");
      $this->db->select('k.cantidad,k.precio,d.id_especifico,k.movimiento,k.fecha_ingreso,k.id_fuentes');
      $this->db->from('sic_kardex k');
      $this->db->join('sic_detalle_producto d', 'd.id_detalleproducto = k.id_detalleproducto');
      $this->db->where('k.id_detalleproducto',$id);
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerProductosLimit($especifico,$fecha_inicio,$fecha_fin,$fuente){
      $this->db->order_by("id_especifico", "asc");
      $this->db->select('s.fecha_salida,p.nombre as producto,u.nombre as unidad,ds.cantidad,ds.total,s.numero_solicitud');
      //$this->db->group_by('id_especifico');
           $this->db->from('sic_solicitud s');
           $this->db->join('sic_detalle_solicitud_producto ds', 's.id_solicitud = ds.id_solicitud');
           $this->db->join('sic_detalle_producto dp', 'dp.id_detalleproducto = ds.id_detalleproducto');
           $this->db->join('sic_producto p', 'p.id_producto = dp.id_producto');
           $this->db->join('sic_unidad_medida u', 'p.id_unidad_medida = u.id_unidad_medida');
           $this->db->where('s.estado_solicitud','LIQUIDADA');
           $this->db->where('s.fecha_salida <',$fecha_fin);
           $this->db->where('s.fecha_salida >',$fecha_inicio);
           $this->db->where('dp.id_especifico',$especifico);
           $this->db->where('ds.id_fuentes',$fuente);
           //$this->db->where('')
           $this->db->order_by('s.id_solicitud');
           //$this->db->where('k.movimiento','SALIDA');
           $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerProductosSeccion($fecha_inicio,$fecha_fin,$seccion,$especifico,$segmento,$porpagina){
      $this->db->order_by("s.id_solicitud", "asc");
      $this->db->select('s.numero_solicitud,s.fecha_salida,sec.nombre_seccion,e.id_especifico,dp.numero_producto,
        p.nombre as producto,u.nombre as unidad,ds.cantidad,ds.total');
      $this->db->from('sic_detalle_solicitud_producto ds');
      $this->db->join('sic_solicitud s', 's.id_solicitud = ds.id_solicitud');
      $this->db->join('sic_detalle_producto dp', 'dp.id_detalleproducto = ds.id_detalleproducto');
      $this->db->join('sic_producto p', 'p.id_producto = dp.id_producto');
      $this->db->join('sic_especifico e', 'e.id_especifico = dp.id_especifico');
      $this->db->join('sic_unidad_medida u', 'p.id_unidad_medida = u.id_unidad_medida');
      $this->db->join('mtps.org_seccion sec', 'sec.id_seccion = s.id_seccion');
      $this->db->where('s.estado_solicitud','LIQUIDADA');
      $this->db->where('s.fecha_salida <=',$fecha_fin);
      $this->db->where('s.fecha_salida >=',$fecha_inicio);
      $this->db->where('e.id_especifico',$especifico);
      if ($seccion!=0) {
       $this->db->where('s.id_seccion',$seccion);
      }
      $this->db->limit($segmento,$porpagina);
      $this->db->group_by('ds.id_detalle_solicitud_producto');
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function buscarProductosSeccion($fecha_inicio,$fecha_fin,$seccion,$especifico,$busca){
      $this->db->order_by("s.id_solicitud", "asc");
      $this->db->select('s.numero_solicitud,s.fecha_salida,sec.nombre_seccion,e.id_especifico,dp.numero_producto,
        p.nombre as producto,u.nombre as unidad,ds.cantidad,ds.total');
      $this->db->from('sic_detalle_solicitud_producto ds');
      $this->db->join('sic_solicitud s', 's.id_solicitud = ds.id_solicitud');
      $this->db->join('sic_detalle_producto dp', 'dp.id_detalleproducto = ds.id_detalleproducto');
      $this->db->join('sic_producto p', 'p.id_producto = dp.id_producto');
      $this->db->join('sic_especifico e', 'e.id_especifico = dp.id_especifico');
      $this->db->join('sic_unidad_medida u', 'p.id_unidad_medida = u.id_unidad_medida');
      $this->db->join('mtps.org_seccion sec', 'sec.id_seccion = s.id_seccion');
      $this->db->where('s.estado_solicitud','LIQUIDADA');
      $this->db->where('s.fecha_salida <=',$fecha_fin);
      $this->db->where('s.fecha_salida >=',$fecha_inicio);
      $this->db->where('e.id_especifico',$especifico);
      if ($seccion!=0) {
       $this->db->where('s.id_seccion',$seccion);
      }
      $this->db->like('p.nombre',$busca);
      $this->db->group_by('ds.id_detalle_solicitud_producto');
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerProductosSeccionTotal($fecha_inicio,$fecha_fin,$seccion,$especifico){
      $this->db->order_by("s.id_solicitud", "asc");
      $this->db->select('count(*) numero');
      $this->db->from('sic_detalle_solicitud_producto ds');
      $this->db->join('sic_solicitud s', 's.id_solicitud = ds.id_solicitud');
      $this->db->join('sic_detalle_producto dp', 'dp.id_detalleproducto = ds.id_detalleproducto');
      $this->db->join('sic_producto p', 'p.id_producto = dp.id_producto');
      $this->db->join('sic_especifico e', 'e.id_especifico = dp.id_especifico');
      $this->db->join('sic_unidad_medida u', 'p.id_unidad_medida = u.id_unidad_medida');
      $this->db->join('mtps.org_seccion sec', 'sec.id_seccion = s.id_seccion');
      $this->db->where('s.estado_solicitud','LIQUIDADA');
      $this->db->where('s.fecha_salida <=',$fecha_fin);
      $this->db->where('s.fecha_salida >=',$fecha_inicio);
      $this->db->where('e.id_especifico',$especifico);
      if ($seccion!=0) {
       $this->db->where('s.id_seccion',$seccion);
      }
      $query = $this->db->get();
      return $query->row();
    }

    public function obtenerProductosSeccionTodo($fecha_inicio,$fecha_fin,$seccion,$especifico){
      $this->db->select('s.numero_solicitud,s.fecha_salida,sec.nombre_seccion,e.id_especifico,dp.numero_producto,
        p.nombre as producto,u.nombre as unidad,ds.cantidad,ds.total');
      $this->db->from('sic_detalle_solicitud_producto ds');
      $this->db->join('sic_solicitud s', 's.id_solicitud = ds.id_solicitud');
      $this->db->join('sic_detalle_producto dp', 'dp.id_detalleproducto = ds.id_detalleproducto');
      $this->db->join('sic_producto p', 'p.id_producto = dp.id_producto');
      $this->db->join('sic_especifico e', 'e.id_especifico = dp.id_especifico');
      $this->db->join('sic_unidad_medida u', 'p.id_unidad_medida = u.id_unidad_medida');
      $this->db->join('mtps.org_seccion sec', 'sec.id_seccion = s.id_seccion');
      $this->db->where('s.estado_solicitud','LIQUIDADA');
      $this->db->where('s.fecha_salida <=',$fecha_fin);
      $this->db->where('s.fecha_salida >=',$fecha_inicio);
      $this->db->where('e.id_especifico',$especifico);
      if ($seccion!=0) {
        $this->db->where('s.id_seccion',$seccion);
      }
      $this->db->order_by("s.id_solicitud", "asc");
      $this->db->group_by('ds.id_detalle_solicitud_producto');
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerDetalles($id){
      $this->db->select('id_detalle_solicitud_producto')
               ->from('sic_detalle_solicitud_producto')
               ->where('id_solicitud',$id);
               $query = $this->db->get();
          if ($query->num_rows() > 0) {
              return  $query->result();
          }
          else {
              return FALSE;
          }
    }

    public function obtenerDetallesSolicitud($id){
        $this->db->select('e.id_especifico,p.nombre')
                 ->from('sic_detalle_solicitud_producto dc')
                 ->join('sic_detalle_producto dp','dp.id_detalleproducto=dc.id_detalleproducto')
                 ->join('sic_especifico e','e.id_especifico=dp.id_especifico')
                 ->join('sic_producto p','p.id_producto=dp.id_producto')
                 ->where('dc.id_solicitud',$id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
          return $query->result();
        }
        else {
            return FALSE;
        }
    }

    public function obtenerEstadoSolicitud($id_detalle_solicitud_producto){
      $this->db->select('s.estado_solicitud')
               ->from('sic_solicitud s')
               ->join('sic_detalle_solicitud_producto ds','ds.id_solicitud=s.id_solicitud')
               ->where('ds.id_detalle_solicitud_producto',$id_detalle_solicitud_producto);
      $query=$this->db->get();
      return $query->row()->estado_solicitud;
    }
  }
?>
