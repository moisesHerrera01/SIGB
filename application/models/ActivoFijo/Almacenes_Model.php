<?php
  class Almacenes_model extends CI_Model{

    public $id_almacen;
    public $id_seccion;

    function __construct() {
        parent::__construct();
    }

    public function insertarAlmacen($data){
        $this->id_almacen=$data['id_almacen'];
        $this->id_seccion = $data['id_seccion'];
        $this->db->insert('org_seccion_has_almacen', $this);
        return $this->db->insert_id();
    }

    public function obtenerSeccionesAlmacenesLimit($porpagina,$segmento){
      $this->db->order_by("sa.id_seccion_has_almacen", "desc");
      $this->db->select('sa.id_seccion_has_almacen,a.nombre_almacen,s.nombre_seccion,a.id_almacen,s.id_seccion');
           $this->db->from('org_seccion_has_almacen sa');
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

    public function actualizarAlmacen($id, $data){
      $this->db->where('id_seccion_has_almacen',$id);
      $this->db->update('org_seccion_has_almacen', $data);
    }

    public function eliminarAlmacen($id){
      $this->db->delete('org_seccion_has_almacen', array('id_seccion_has_almacen' => $id));
    }
    public function contieneOficina($id){
      $this->db->select('count(id_seccion_has_almacen) as asociados')
               ->from('org_oficina')
               ->where('id_seccion_has_almacen',$id);
      $query=$this->db->get();
      if ($query->num_rows()>0) {
        return $query->row();
      }else {
        return FALSE;
      }
    }

    function totalAlmacenes(){
      return $this->db->count_all('org_seccion_has_almacen');
    }

    public function obtenerAlmacenes(){
      $this->db->order_by("a.id_almacen", "asc");
      $query = $this->db->get('org_almacen a');
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function buscarAlmacenes($busca){
      $this->db->order_by("a.id_almacen", "asc");
      $this->db->select('a.nombre_almacen,a.id_almacen');
           $this->db->from('org_almacen a');
           $this->db->like('a.nombre_almacen', $busca);
           $this->db->or_like('a.id_almacen', $busca);
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
