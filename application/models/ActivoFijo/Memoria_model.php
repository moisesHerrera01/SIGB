<?php
  class Memoria_model extends CI_Model{

    public $tipo_memoria;

    function __construct() {
        parent::__construct();

    }

    public function insertarMemoria($data){
        $this->tipo_memoria = $data['tipo_memoria'];
        $this->db->insert('sic_memoria', $this);
        return $this->db->insert_id();
    }

    public function obtenerMemorias(){
      $this->db->order_by("id_memoria", "asc");
      $query = $this->db->get('sic_memoria');
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function buscarMemorias($busca){
      $this->db->like('tipo_memoria', $busca);
      $query = $this->db->get('sic_memoria', 10);
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function actualizarMemoria($id, $data){
      $this->db->where('id_memoria',$id);
      $this->db->update('sic_memoria', $data);
    }

    public function eliminarMemoria($id){
      $this->db->delete('sic_memoria', array('id_memoria' => $id));
    }

    function totalMemorias(){
      return $this->db->count_all('sic_memoria');
    }

    public function obtenerMemoriasLimit($porpagina, $segmento){
      $this->db->order_by("id_memoria", "asc");
      $query = $this->db->get('sic_memoria', $porpagina, $segmento);
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }
  }
?>
