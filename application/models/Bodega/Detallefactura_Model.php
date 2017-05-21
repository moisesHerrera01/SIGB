<?php
  class DetalleFactura_model extends CI_Model{

    public $id_detalle_factura;
    public $cantidad;
    public $precio;
    public $id_factura;
    public $total;
    public $id_detalleproducto;
    public $cantidad_descargo;
    public $estado_factura_producto;

    function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function insertarDetalleFactura($data){
        $this->id_detalle_factura = $data['id_detalle_factura'];
        $this->cantidad = $data['cantidad'];
        $this->precio = $data['precio'];
        $this->id_factura = $data['id_factura'];
        $this->total = $this->cantidad*$this->precio;
        $this->id_detalleproducto = $data['id_detalleproducto'];
        $this->cantidad_descargo = 0;
        $this->estado_factura_producto = 'INGRESADO';

        $this->db->insert('sic_detalle_factura', $this);
        return $this->db->insert_id();
    }

    public function actualizarDetalleFactura($id, $data){
      $this->db->where('id_detalle_factura',$id);
      $this->db->update('sic_detalle_factura', $data);
    }

    public function obtenerDetalleFacturas($id){
      $this->db->select('p.nombre as producto,dp.id_detalleproducto,u.nombre as unidad,f.id_detalle_factura,f.cantidad,
      f.precio,f.total,f.estado_factura_producto,f.id_factura')
               ->from('sic_detalle_factura f')
               ->join('sic_detalle_producto dp','f.id_detalleproducto=dp.id_detalleproducto')
               ->join('sic_producto p','p.id_producto=dp.id_producto')
               ->join('sic_unidad_medida u','p.id_unidad_medida=u.id_unidad_medida')
               ->order_by("id_detalle_factura", "asc")
               ->where('f.id_factura',$id);
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerdetalleFactura($id){
        $this->db->where('id_detalle_factura',$id);
        $query = $this->db->get('sic_detalle_factura');
        if ($query->num_rows() > 0) {
          $fact;
          foreach ($query->result() as $factura) {
            $fact = $factura->id_factura;
          }
          return  $fact;
        }
        else {
            return FALSE;
        }
    }
    public function eliminarDetalleFactura($id){
      $this->db->delete('sic_detalle_factura', array('id_detalle_factura' => $id));
    }

    public function cargar($id,$id2,$tot){
      $dat = array(
        'estado_factura_producto' => 'CARGADO',
      );
      $dat2 = array(
        'total' => $tot,
      );
      $this->db->where('id_detalle_factura', $id);
      $this->db->update('sic_detalle_factura', $dat);
      $this->db->where('id_factura',$id2);
      $this->db->update('sic_factura',$dat2);
    }

    public function retornarEstado($id){
        $this->db->where('id_detalle_factura', $id);
        $query = $this->db->get('sic_detalle_factura');
        if ($query->num_rows() > 0) {
          $estado;
          foreach ($query->result() as $detalle) {
            $estado = $detalle->estado_factura_producto;
          }
          return  $estado;
        }
        else {
            return FALSE;
        }
    }

    public function existeFactura($id){
      $this->db->where('id_factura',$id);
      $query = $this->db->get('sic_detalle_factura');
      if ($query->num_rows()>0){
        return TRUE;
      }
      else {
        return FALSE;
      }
    }

    public function obtenerEspecificosProductos($id_factura){
      $this->db->select('p.nombre,e.id_especifico,dp.id_detalleproducto, u.nombre nombre_unidad,ds.cantidad')
               ->from('sic_producto p')
               ->join('sic_detalle_producto dp','dp.id_producto=p.id_producto')
               ->join('sic_detalle_solicitud_compra ds','ds.id_detalleproducto=dp.id_detalleproducto')
               ->join('sic_solicitud_compra s','s.id_solicitud_compra=ds.id_solicitud_compra')
               ->join('sic_orden_compra oc','oc.id_solicitud_compra=s.id_solicitud_compra')
               ->join('sic_compromiso_presupuestario cp','cp.id_orden_compra=oc.id_orden_compra')
               ->join('sic_factura f','f.numero_compromiso=cp.id_compromiso')
               ->join('sic_especifico e','e.id_especifico=dp.id_especifico')
               ->join('sic_unidad_medida u', 'p.id_unidad_medida = u.id_unidad_medida')
               ->order_by('dp.id_detalleproducto')
               ->where('f.id_factura',$id_factura);
      $query=$this->db->get();
      if ($query->num_rows()>0){
        return $query->result();
      }
      else {
        return FALSE;
      }
    }

    public function buscarEspecificosProductos($id_factura,$busca){
      $this->db->select('p.nombre,e.id_especifico,dp.id_detalleproducto, u.nombre nombre_unidad,ds.cantidad')
               ->from('sic_producto p')
               ->join('sic_detalle_producto dp','dp.id_producto=p.id_producto')
               ->join('sic_detalle_solicitud_compra ds','ds.id_detalleproducto=dp.id_detalleproducto')
               ->join('sic_solicitud_compra s','s.id_solicitud_compra=ds.id_solicitud_compra')
               ->join('sic_orden_compra oc','oc.id_solicitud_compra=s.id_solicitud_compra')
               ->join('sic_compromiso_presupuestario cp','cp.id_orden_compra=oc.id_orden_compra')
               ->join('sic_factura f','f.numero_compromiso=cp.id_compromiso')
               ->join('sic_especifico e','e.id_especifico=dp.id_especifico')
               ->join('sic_unidad_medida u', 'p.id_unidad_medida = u.id_unidad_medida')
               ->order_by('dp.id_detalleproducto')
               ->where('f.id_factura',$id_factura)
               ->like('e.id_especifico',$busca)
               ->or_like('p.nombre',$busca);
      $query=$this->db->get();
      if ($query->num_rows()>0){
        return $query->result();
      }
      else {
        return FALSE;
      }
    }

    public function validarProductoIngresado($id_factura,$id_detalleproducto){
      $this->db->select('count(*) as total')
               ->from('sic_detalle_factura df')
               ->join('sic_factura f','f.id_factura=df.id_factura')
               ->where('f.id_factura',$id_factura)
               ->where('df.id_detalleproducto',$id_detalleproducto);
      $query=$this->db->get();
      if ($query->row()->total > 0) {
        return FALSE;
      }else {
        return TRUE;
      }
    }
  }
?>
