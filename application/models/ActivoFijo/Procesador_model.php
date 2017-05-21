<?php
  class Procesador_model extends CI_Model{

    public $nombre_procesador;

    function __construct() {
        parent::__construct();

    }

    public function insertarProcesador($data){
        $this->nombre_procesador = $data['nombre_procesador'];
        $this->db->insert('sic_procesador', $this);
        return $this->db->insert_id();
    }

    public function obtenerProcesadores(){
      $this->db->order_by("id_procesador", "asc");
      $query = $this->db->get('sic_procesador');
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function buscarProcesadores($busca){
      $this->db->like('nombre_procesador', $busca);
      $query = $this->db->get('sic_procesador', 10);
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function actualizarProcesador($id, $data){
      $this->db->where('id_procesador',$id);
      $this->db->update('sic_procesador', $data);
    }

    public function eliminarProcesador($id){
      $this->db->delete('sic_procesador', array('id_procesador' => $id));
    }

    function totalProcesadores(){
      return $this->db->count_all('sic_procesador');
    }

    public function obtenerProcesadoresLimit($porpagina, $segmento){
      $this->db->order_by("id_procesador", "asc");
      $query = $this->db->get('sic_procesador', $porpagina, $segmento);
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }
  }
?>
