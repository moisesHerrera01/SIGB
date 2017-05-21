<?php
  class Detalle_orden_resumen_model extends CI_Model{

    public $id_detalle_orden_resumen;
    public $cantidad;
    public $precio;
    public $total;
    public $id_detalleproducto;
    public $id_orden_compra;
    public $especificaciones;

    function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function insertarDetalleSolicitudCompra($data){
        $this->id_detalle_orden_resumen = $data['id_detalle_orden_resumen'];
        $this->cantidad = $data['cantidad'];
        $this->precio = $data['precio'];
        $this->total = $data['total'];
        $this->id_detalleproducto = $data['id_detalleproducto'];
        $this->id_orden_compra = $data['id_orden_compra'];
        $this->especificaciones=$data['especificaciones'];
        $this->db->insert('sic_detalle_orden_resumen', $this);
    }

    public function obtenerDetalleSolicitudCompra($id){
      $this->db->order_by("id_detalle_orden_resumen", "asc");
      $this->db->where('id_orden_compra',$id);
      $query = $this->db->get('sic_detalle_orden_resumen');
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

    public function obtenerDetallesSolicitud($id){
        $this->db->select('dp.id_detalleproducto,e.id_especifico,p.nombre,p.id_unidad_medida')
                 ->from('sic_detalle_orden_resumen dc')
                 ->join('sic_orden_compra oc','oc.id_orden_compra=dc.id_orden_compra')
                 ->join('sic_solicitud_compra sc','oc.id_solicitud_compra=sc.id_solicitud_compra')
                 ->join('sic_detalle_producto dp','dp.id_detalleproducto=dc.id_detalleproducto')
                 ->join('sic_especifico e','e.id_especifico=dp.id_especifico')
                 ->join('sic_producto p','p.id_producto=dp.id_producto')
                 ->where('sc.id_solicitud_compra',$id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
          return $query->row();
        }
        else {
            return FALSE;
        }
    }

    public function obtenerEspecifico($id){
              $this->db->select('dp.id_detalleproducto,e.id_especifico,p.nombre,p.id_unidad_medida')
              ->from('sic_detalle_producto dp')
              ->join('sic_especifico e','e.id_especifico=dp.id_especifico')
              ->join('sic_producto p','p.id_producto=dp.id_producto')
              ->where('dp.id_detalleproducto',$id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
        return $query->row();
        }
        else {
         return FALSE;
        }
      }


    public function obtenerDetalleCompraCompleto($id){
      $this->db->where('id_detalle_orden_resumen',$id);
      $query=$this->db->get('sic_detalle_orden_resumen');
      return $query->row();
    }

    public function eliminarDetalleSolicitudCompra($id){
      $this->db->delete('sic_detalle_orden_resumen', array('id_detalle_orden_resumen' => $id));
    }

      public function actualizarDetalleSolicitudCompra($id,$data){
        $this->db->where('id_detalle_orden_resumen',$id);
        $this->db->update('sic_detalle_orden_resumen', $data);
      }


/*Obtine el listado de productos correspondientes al requerimiento del parametro, para el resumen del detalle de la
orden de compra*/
      public function obtenerDetalleOrdenResumen($id_orden_compra){
        $this->db->select('p.nombre,e.id_especifico,dp.id_detalleproducto, u.nombre nombre_unidad,ds.especificaciones,ds.cantidad')
                 ->from('sic_producto p')
                 ->join('sic_detalle_producto dp','dp.id_producto=p.id_producto')
                 ->join('sic_detalle_solicitud_compra ds','ds.id_detalleproducto=dp.id_detalleproducto')
                 ->join('sic_solicitud_compra s','s.id_solicitud_compra=ds.id_solicitud_compra')
                 ->join('sic_orden_compra oc','oc.id_solicitud_compra=s.id_solicitud_compra')
                 ->join('sic_especifico e','e.id_especifico=dp.id_especifico')
                 ->join('sic_unidad_medida u', 'p.id_unidad_medida = u.id_unidad_medida')
                 ->order_by('dp.id_detalleproducto')
                 ->where('oc.id_orden_compra',$id_orden_compra);
        $query=$this->db->get();
        if ($query->num_rows()>0){
          return $query->result();
        }
        else {
          return FALSE;
        }
      }

/*Obtine el listado filtrado de productos correspondientes al requerimiento del parametro, para el resumen del
 detalle de la orden de compra, se filtra por especifico y por nombre del producto*/
      public function buscarDetalleOrdenResumen($id_orden_compra,$busca){
        $this->db->select('p.nombre,e.id_especifico,dp.id_detalleproducto, u.nombre nombre_unidad,ds.especificaciones,ds.cantidad')
                 ->from('sic_producto p')
                 ->join('sic_detalle_producto dp','dp.id_producto=p.id_producto')
                 ->join('sic_detalle_solicitud_compra ds','ds.id_detalleproducto=dp.id_detalleproducto')
                 ->join('sic_solicitud_compra s','s.id_solicitud_compra=ds.id_solicitud_compra')
                 ->join('sic_orden_compra oc','oc.id_solicitud_compra=s.id_solicitud_compra')
                 ->join('sic_especifico e','e.id_especifico=dp.id_especifico')
                 ->join('sic_unidad_medida u', 'p.id_unidad_medida = u.id_unidad_medida')
                 ->order_by('dp.id_detalleproducto')
                 ->where('oc.id_orden_compra',$id_orden_compra)
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

      public function obtenerSolicitudCompleta($id_orden_compra){
        $this->db->select('sc.id_solicitud_compra,sc.estado_solicitud_compra,sc.nivel_solicitud')
                 ->from('sic_orden_compra oc')
                 ->join('sic_solicitud_compra sc','sc.id_solicitud_compra=oc.id_solicitud_compra')
                 ->where('oc.id_orden_compra',$id_orden_compra);
        $query=$this->db->get();
        return $query->row();
      }
  }
?>
