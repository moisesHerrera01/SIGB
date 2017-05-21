<?php
  class Disco_Duro_Model extends CI_Model{

    public $id_disco_duro;
    public $capacidad;

    function __construct() {
        parent::__construct();
    }

    public function insertarOficina($data){
        $this->capacidad = $data['capacidad'];
        $this->db->insert('sic_disco_duro', $this);
        return $this->db->insert_id();
    }

    public function obtenerDiscosDuros(){
      $this->db->order_by("id_disco_duro", "asc");
      $query = $this->db->get('sic_disco_duro');
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function totalDiscosDuros() {
      $this->db->order_by("id_disco_duro", "asc");
      $this->db->select("count(*) as total");
      $query = $this->db->get('sic_disco_duro');
      if ($query->num_rows() > 0) {
          return  $query->row()->total;
      }
      else {
          return FALSE;
      }
    }

    public function obtenerDiscosDurosLimit($num, $porpagina){
      $this->db->order_by("id_disco_duro", "asc")
               ->limit($porpagina, $num);
      $query = $this->db->get('sic_disco_duro');
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function buscarDiscos($busca){
      $this->db->from('sic_disco_duro')
               ->like('capacidad', $busca)
               ->order_by("id_oficina", "asc");
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerDiscoDuro($id) {
      $this->db->where("id_disco_duro", $id);
      $query = $this->db->get('sic_disco_duro');
      if ($query->num_rows() > 0) {
          return  $query->row();
      }
      else {
          return FALSE;
      }
    }

    public function actualizarDiscoDuro($id, $data){
      $this->db->where('id_disco_duro',$id);
      $this->db->update('sic_disco_duro', $data);
    }

    public function eliminarDiscoDuro($id){
      $this->db->delete('sic_disco_duro', array('id_disco_duro' => $id));
    }

  }
?>
