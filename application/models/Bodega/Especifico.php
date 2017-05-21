<?php
  class Especifico extends CI_Model{

    public $id_especifico;
    public $nombre_especifico;
    public $proceso;

    function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function insertarEspecifico($data){

        $this->nombre_especifico = $data['nombre_especifico'];
        $this->id_especifico = $data['id_especifico'];
        $this->proceso = $data['proceso'];

        $this->db->insert('sic_especifico', $this);
    }

    public function obtenerEspecificos(){
      $this->db->order_by("id_especifico", "asc");
      $query = $this->db->get('sic_especifico');
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerEspecificosProducto($id){
      $this->db->select('e.nombre_especifico,e.id_especifico')
               ->from('sic_detalle_producto dp')
               ->join('sic_especifico e','e.id_especifico=dp.id_especifico')
               ->where('dp.id_producto',$id);
      $query=$this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function buscarEspecificosProducto($id, $busca) {
      $this->db->select('e.nombre_especifico,e.id_especifico')
               ->from('sic_detalle_producto dp')
               ->join('sic_especifico e','e.id_especifico=dp.id_especifico')
               ->where('dp.id_producto',$id)
               ->like('e.nombre_especifico', $busca);
      $query=$this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerEspecifico($id){
        $this->db->where('id_especifico',$id);
        $query = $this->db->get('sic_especifico');
        if ($query->num_rows() > 0) {
          $nombre;
          foreach ($query->result() as $espe) {
            $nombre = $espe->nombre_especifico;
          }
          return  $nombre;
        }
        else {
            return FALSE;
        }
    }

    public function buscarEspecificos($busca){
      $this->db->order_by("id_especifico", "asc");
      $this->db->like('nombre_especifico', $busca);
      $this->db->or_like('id_especifico', $busca);
      $query = $this->db->get('sic_especifico', 10);
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function actualizarEspecifico($id, $data){
      $this->db->where('id_especifico',$id);
      $this->db->update('sic_especifico', $data);
    }

    public function eliminarEspecifico($id){
      $this->db->delete('sic_especifico', array('id_especifico' => $id));
    }

    function totalEspecificos(){
      return $this->db->count_all('sic_especifico');
    }

    public function obtenerEspecificosLimit($porpagina, $segmento){
      $this->db->order_by("id_especifico", "asc");
      $query = $this->db->get('sic_especifico', $porpagina, $segmento);
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }
  }
?>
