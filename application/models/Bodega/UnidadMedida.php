<?php
  class UnidadMedida extends CI_Model{

    public $nombre;
    public $abreviatura;

    function __construct() {
        parent::__construct();
    }

    public function insertarUnidad($data){

        $this->nombre = $data['nombre'];
        $this->abreviatura = $data['abreviatura'];

        $this->db->insert('sic_unidad_medida', $this);
        return $this->db->insert_id();
    }

    public function obtenerUnidades(){
      $this->db->order_by("id_unidad_medida", "asc");
      $query = $this->db->get('sic_unidad_medida');
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerUnidad($id){
        $this->db->where('id_unidad_medida',$id);
        $query = $this->db->get('sic_unidad_medida');
        if ($query->num_rows() > 0) {
          $nombre;
          foreach ($query->result() as $unidad) {
            $nombre = $unidad->nombre;
          }
          return  $nombre;
        }
        else {
            return FALSE;
        }
    }

    public function buscarUnidades($busca){
      $this->db->order_by("id_unidad_medida", "asc");
      $this->db->like('nombre', $busca);
      $this->db->or_like('abreviatura', $busca);
      $query = $this->db->get('sic_unidad_medida', 10);
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function actualizarUnidad($id, $data){
      $this->db->where('id_unidad_medida',$id);
      $this->db->update('sic_unidad_medida', $data);
    }

    public function eliminarUnidad($id){
      $this->db->delete('sic_unidad_medida', array('id_unidad_medida' => $id));
    }

    function totalUnidades(){
      return $this->db->count_all('sic_unidad_medida');
    }

    public function obtenerUnidadesLimit($porpagina, $segmento){
      $this->db->order_by("id_unidad_medida", "asc");
      $query = $this->db->get('sic_unidad_medida', $porpagina, $segmento);
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerIdPorNombre($nombre) {
      $this->db->where('nombre', $nombre);
      $query = $this->db->get('sic_unidad_medida');
      if ($query->num_rows() > 0) {
        return  $query->row('id_unidad_medida');
      }
      else {
          return FALSE;
      }
    }

  }
?>
