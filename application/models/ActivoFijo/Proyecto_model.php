<?php
  class Proyecto_model extends CI_Model{

    public $nombre_proyecto;
    public $numero_proyecto;
    public $descripcion;

    function __construct() {
        parent::__construct();
    }

    public function insertarProyecto($data){

        $this->nombre_proyecto = $data['nombre_proyecto'];
        $this->numero_proyecto = $data['numero_proyecto'];
        $this->descripcion = $data['descripcion'];

        $this->db->insert('sic_proyecto', $this);
        return $this->db->insert_id();
    }

    public function obtenerProyectos(){
      $this->db->order_by("id_proyecto", "asc");
      $query = $this->db->get('sic_proyecto');
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerproyecto($id){
        $this->db->where('id_proyecto',$id);
        $query = $this->db->get('sic_proyecto');
        if ($query->num_rows() > 0) {
          $nombre_proyecto;
          foreach ($query->result() as $fuente) {
            $nombre_proyecto = $fuente->nombre_proyecto;
          }
          return  $nombre_proyecto;
        }
        else {
            return FALSE;
        }
    }

    public function obtenerProyectosLimit($porpagina, $segmento){
      $this->db->order_by("id_proyecto", "asc");
      $query = $this->db->get('sic_proyecto', $porpagina, $segmento);
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    function totalProyectos(){
      return $this->db->count_all('sic_proyecto');
    }

    public function buscarProyectos($busca){
      $this->db->order_by("id_proyecto", "asc");
      $this->db->like('nombre_proyecto', $busca);
      $this->db->or_like('numero_proyecto', $busca);
      $query = $this->db->get('sic_proyecto', 10);
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function actualizarProyecto($id, $data){
      $this->db->where('id_proyecto',$id);
      $this->db->update('sic_proyecto', $data);
    }

    public function eliminarProyecto($id){
      $this->db->delete('sic_proyecto', array('id_proyecto' => $id));
    }

    public function obtenerIdPorNombre_Proyecto($nombre_proyecto) {
      $this->db->where("nombre_proyecto", $nombre_proyecto);
      $query = $this->db->get('sic_proyecto');
      if ($query->num_rows() > 0) {
          return  $query->row('id_proyecto');
      }
      else {
          return FALSE;
      }
    }

  }
?>
