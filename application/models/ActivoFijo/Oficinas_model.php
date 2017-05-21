<?php
  class Oficinas_model extends CI_Model{

    public $id_seccion_has_almacen;
    public $nombre_oficina;

    function __construct() {
        parent::__construct();
    }

    public function insertarOficina($data){
        $this->id_seccion_has_almacen=$data['id_seccion_has_almacen'];
        $this->nombre_oficina = $data['nombre_oficina'];
        $this->db->insert('org_oficina', $this);
        return $this->db->insert_id();
    }

    public function obtenerDatos(){
      $this->db->order_by("o.id_oficina", "asc");
      $this->db->select('o.id_oficina,a.nombre_almacen,s.nombre_seccion,o.nombre_oficina,sa.id_seccion_has_almacen');
           $this->db->from('org_oficina o');
           $this->db->join('org_seccion_has_almacen sa', 'sa.id_seccion_has_almacen = o.id_seccion_has_almacen');
           $this->db->join('org_seccion s', 's.id_seccion = sa.id_seccion');
           $this->db->join('org_almacen a', 'a.id_almacen = sa.id_almacen');
           $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function buscarOficinas($busca){
      $this->db->order_by("o.id_oficina", "desc");
      $this->db->select('o.id_oficina,a.nombre_almacen,s.nombre_seccion,o.nombre_oficina,sa.id_seccion_has_almacen')
           ->from('org_oficina o')
           ->join('org_seccion_has_almacen sa', 'sa.id_seccion_has_almacen = o.id_seccion_has_almacen')
           ->join('org_seccion s', 's.id_seccion = sa.id_seccion')
           ->join('org_almacen a', 'a.id_almacen = sa.id_almacen');
           $this->db->like('o.nombre_oficina', $busca);
           $this->db->or_like('o.id_oficina', $busca);
           $this->db->limit(25);
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function actualizarOficina($id, $data){
      $this->db->where('id_oficina',$id);
      $this->db->update('org_oficina', $data);
    }

    public function eliminarOficina($id){
      $this->db->delete('org_oficina', array('id_oficina' => $id));
    }

    public function contieneBien($id){
      $this->db->select('count(id_oficina) as asociados')
               ->from('sic_bien')
               ->where('id_oficina',$id);
      $query=$this->db->get();
      if ($query->num_rows()>0) {
        return $query->row();
      }else {
        return FALSE;
      }
    }
    public function contieneMovimiento($id){
      $this->db->select('count(*) as asociados')
               ->from('sic_movimiento')
               ->where('id_oficina_entrega',$id)
               ->or_where('id_oficina_recibe',$id);
      $query=$this->db->get();
      if ($query->num_rows()>0) {
        return $query->row();
      }else {
        return FALSE;
      }
    }


    function totalOficinas(){
      return $this->db->count_all('org_oficina');
    }

    public function obtenerDatosLimit($porpagina,$segmento){
      $this->db->order_by("o.id_oficina", "desc");
      $this->db->select('o.id_oficina,a.nombre_almacen,s.nombre_seccion,o.nombre_oficina,sa.id_seccion_has_almacen');
           $this->db->from('org_oficina o');
           $this->db->join('org_seccion_has_almacen sa', 'sa.id_seccion_has_almacen = o.id_seccion_has_almacen');
           $this->db->join('org_seccion s', 's.id_seccion = sa.id_seccion');
           $this->db->join('org_almacen a', 'a.id_almacen = sa.id_almacen');
           $this->db->limit($porpagina,$segmento);
           $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }
    public function obtenerSeccionesAlmacenes(){
      $this->db->order_by("sa.id_seccion_has_almacen", "asc");
      $this->db->select('a.nombre_almacen,s.nombre_seccion,sa.id_seccion_has_almacen,a.id_almacen,s.id_seccion');
           $this->db->from('org_seccion_has_almacen sa');
           $this->db->join('org_seccion s', 's.id_seccion = sa.id_seccion');
           $this->db->join('org_almacen a', 'a.id_almacen = sa.id_almacen');
           $this->db->limit(10);
           $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }
    public function buscarSeccionesAlmacenes($busca){
      $this->db->order_by("sa.id_seccion_has_almacen", "desc");
      $this->db->select('a.nombre_almacen,s.nombre_seccion,sa.id_seccion_has_almacen,s.id_seccion,a.id_almacen');
           $this->db->from('org_seccion_has_almacen sa');
           $this->db->join('org_seccion s', 's.id_seccion = sa.id_seccion');
           $this->db->join('org_almacen a', 'a.id_almacen = sa.id_almacen');
           $this->db->like('s.nombre_seccion', $busca);
           $this->db->or_like('a.nombre_almacen', $busca);
           $this->db->or_like('sa.id_seccion_has_almacen', $busca);
           $this->db->limit(10);
           $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

  }
?>
