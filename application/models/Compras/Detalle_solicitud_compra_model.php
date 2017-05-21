<?php
  class Detalle_solicitud_compra_model extends CI_Model{

    public $id_detalle_solicitud_compra;
    public $cantidad;
    public $precio;
    public $total;
    public $id_detalleproducto;
    public $id_solicitud_compra;
    public $especificaciones;

    function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function insertarDetalleSolicitudCompra($data){
        $this->id_detalle_solicitud_compra = $data['id_detalle_solicitud_compra'];
        $this->cantidad = $data['cantidad'];
        $this->precio = $data['precio'];
        $this->total = $data['total'];
        $this->id_detalleproducto = $data['id_detalleproducto'];
        $this->id_solicitud_compra = $data['id_solicitud_compra'];
        $this->especificaciones=$data['especificaciones'];
        $this->db->insert('sic_detalle_solicitud_compra', $this);
    }

    public function obtenerDetalleSolicitudCompra($id){
      $this->db->order_by("id_detalle_solicitud_compra", "asc");
      $this->db->where('id_solicitud_compra',$id);
      $query = $this->db->get('sic_detalle_solicitud_compra');
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
        $this->db->select('dp.id_detalleproducto,e.id_especifico,p.nombre')
                 ->from('sic_detalle_solicitud_compra dc')
                 ->join('sic_detalle_producto dp','dp.id_detalleproducto=dc.id_detalleproducto')
                 ->join('sic_especifico e','e.id_especifico=dp.id_especifico')
                 ->join('sic_producto p','p.id_producto=dp.id_producto')
                 ->where('dc.id_solicitud_compra',$id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
          return $query->row();
        }
        else {
            return FALSE;
        }
    }

    public function obtenerEspecifico($id){
              $this->db->select('dp.id_detalleproducto,e.id_especifico,p.nombre')
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
      $this->db->where('id_detalle_solicitud_compra',$id);
      $query=$this->db->get('sic_detalle_solicitud_compra');
      return $query->row();
    }

    public function eliminarDetalleSolicitudCompra($id){
      $this->db->delete('sic_detalle_solicitud_compra', array('id_detalle_solicitud_compra' => $id));
    }

      public function actualizarDetalleSolicitudCompra($id,$data){
        $this->db->where('id_detalle_solicitud_compra',$id);
        $this->db->update('sic_detalle_solicitud_compra', $data);
      }


/*Obtine el listado completo de productos para el detalle del requerimiento de compra*/
      public function obtenerEspecificosProductosCompras(){
        $this->db->select('p.nombre,e.id_especifico,dp.id_detalleproducto, u.nombre nombre_unidad')
                 ->from('sic_producto p')
                 ->join('sic_detalle_producto dp','dp.id_producto=p.id_producto')
                 ->join('sic_especifico e','e.id_especifico=dp.id_especifico')
                 ->join('sic_unidad_medida u', 'p.id_unidad_medida = u.id_unidad_medida')
                 ->order_by('dp.id_detalleproducto');
        $query=$this->db->get();
        if ($query->num_rows()>0){
          return $query->result();
        }
        else {
          return FALSE;
        }
      }

/*Obtine el listado filtrado de productos para el detalle del requerimiento de compra en base al id del especifico
o nombre del producto*/
      public function buscarEspecificosProductosCompras($busca){
        $this->db->select('p.nombre,e.id_especifico,dp.id_detalleproducto, u.nombre nombre_unidad')
                 ->from('sic_producto p')
                 ->join('sic_detalle_producto dp','dp.id_producto=p.id_producto')
                 ->join('sic_especifico e','e.id_especifico=dp.id_especifico')
                 ->join('sic_unidad_medida u', 'p.id_unidad_medida = u.id_unidad_medida')
                 ->order_by('dp.id_detalleproducto')
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
  }
?>
