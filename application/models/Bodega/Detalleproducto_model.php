<?php
  class DetalleProducto_model extends CI_Model{

    public $id_especifico;
    public $id_producto;
    public $id_detalleproducto;

    function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function insertarDetalleProducto($data){
        $count = $this->totalDetalleProductos($data['id_especifico']);
        $this->numero_producto = $data['id_especifico'].($count+1);
        $this->id_producto = $data['id_producto'];
        $this->id_especifico = $data['id_especifico'];

        if ($count>0){
          $this->db->where('id_especifico',$data['id_especifico']);
          $query = $this->db->get('sic_detalle_producto');
          foreach ($query->result() as $det){
            if ($this->numero_producto == $det->numero_producto){
              $this->numero_producto++;
            }
          }
        }
        $this->db->insert('sic_detalle_producto', $this);
        return $this->db->insert_id();
    }

    public function obtenerDetalleProductos(){
      $this->db->order_by("id_especifico", "asc");
      $query = $this->db->get('sic_detalle_producto');
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerDetalleProducto($id1,$id2){
        $this->db->where('id_producto',$id1);
        $this->db->where('id_especifico',$id2);
        $query = $this->db->get('sic_detalle_producto');
        if ($query->num_rows() > 0) {
          $detalle;
          foreach ($query->result() as $det) {
            $detalle = $det->id_detalleproducto;
          }
          return  $detalle;
        }
        else {
            return FALSE;
        }
    }

    public function obtenerIdDetalleProducto($id){
        $this->db->where('id_producto',$id);
        $query = $this->db->get('sic_detalle_producto');
        if ($query->num_rows() > 0) {
          $detalle;
          foreach ($query->result() as $det) {
            $detalle = $det->id_detalleproducto;
          }
          return  $detalle;
        }
        else {
            return FALSE;
        }
    }

    public function obtenerIdProducto($id){
        $this->db->where('id_detalleproducto',$id);
        $query = $this->db->get('sic_detalle_producto');
        if ($query->num_rows() > 0) {
          $prod;
          foreach ($query->result() as $det) {
            $prod = $det->id_producto;
          }
          return  $prod;
        }
        else {
            return FALSE;
        }
    }

    public function obtenerIdEspecifico($id){
        $this->db->where('id_detalleproducto',$id);
        $query = $this->db->get('sic_detalle_producto');
        if ($query->num_rows() > 0) {
          $esp;
          foreach ($query->result() as $det) {
            $esp = $det->id_especifico;
          }
          return  $esp;
        }
        else {
            return FALSE;
        }
    }




    /*public function obtenerNombres($id){
      $this->db->select('producto.nombre as prod','especifico.nombre as esp');
      $this->db->from('detalleproducto');
      $this->db->where('detalleproducto.id_detalleproducto ='.$id);
      $this->db->join('producto', 'detalleproducto.id_producto = producto.id_producto');
      $this->db->join('especifico', 'detalleproducto.id_especifico = especifico.id_especifico');
      $this->db->order_by("producto.nombre", "asc");
      $query =$this->db->get()->result();

       if ($query->num_rows() > 0) {
           return  $query->result();
       }
       else {
           return FALSE;
       }
    }*/

    public function buscarDetalleProductos($busca,$id){
      $this->db->select('a.id_detalleproducto, a.id_producto, a.id_especifico, a.numero_producto')
               ->from('sic_detalle_producto a')
               ->join('sic_producto b', 'a.id_producto = b.id_producto')
               ->where('a.id_especifico',$id)
               ->like('b.nombre', $busca)
               ->order_by("a.id_especifico", "asc")
               ->limit(10);
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function actualizarDetalleProducto($id, $data){
      $this->db->where('id_detalleproducto',$id);
      $this->db->update('sic_detalle_producto', $data);
    }

    public function eliminarDetalleProducto($data){
      $this->db->delete('sic_detalle_producto',$data);
    }

    function totalDetalleProductos($id){
      $this->db->where('id_especifico',$id);
      $query = $this->db->get('sic_detalle_producto');
      if ($query->num_rows() > 0) {
          $i = 0;
          foreach ($query->result() as $det) {
            $i++;
          }
          return  $i;
      }
      else {
          return FALSE;
      }

      //return $this->db->count_all('sic_detalle_producto');
    }

    public function obtenerDetalleProductosLimit($porpagina, $segmento, $id){
      $this->db->where('id_especifico',$id);
      $this->db->order_by("id_especifico", "asc");
      $query = $this->db->get('sic_detalle_producto', $porpagina, $segmento);
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function existeEspecifico($id){
      $this->db->where('id_especifico',$id);
      $query = $this->db->get('sic_detalle_producto');
      if ($query->num_rows()>0){
        return TRUE;
      }
      else {
        return FALSE;
      }
    }

  }
?>
