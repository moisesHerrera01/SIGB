<?php
  class FuenteFondos_model extends CI_Model{

    public $nombre_fuente;
    public $codigo;
    public $descripcion;

    function __construct() {
        parent::__construct();
    }

    public function insertarFuente($data){

        $this->nombre_fuente = $data['nombre_fuente'];
        $this->codigo = $data['codigo'];
        $this->descripcion = $data['descripcion'];

        $this->db->insert('sic_fuentes_fondo', $this);
        return $this->db->insert_id();
    }

    public function obtenerFuentes(){
      $this->db->order_by("id_fuentes", "asc");
      $query = $this->db->get('sic_fuentes_fondo');
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerFuente($id){
        $this->db->where('id_fuentes',$id);
        $query = $this->db->get('sic_fuentes_fondo');
        if ($query->num_rows() > 0) {
          $nombre;
          foreach ($query->result() as $fuente) {
            $nombre = $fuente->nombre_fuente;
          }
          return  $nombre;
        }
        else {
            return FALSE;
        }
    }

    public function obtenerFuentesLimit($porpagina, $segmento){
      $this->db->order_by("id_fuentes", "asc");
      $query = $this->db->get('sic_fuentes_fondo', $porpagina, $segmento);
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    function totalFuentes(){
      return $this->db->count_all('sic_fuentes_fondo');
    }

    public function buscarFuentes($busca){
      $this->db->order_by("id_fuentes", "asc");
      $this->db->like('nombre_fuente', $busca);
      $this->db->or_like('codigo', $busca);
      $query = $this->db->get('sic_fuentes_fondo', 10);
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function actualizarFuente($id, $data){
      $this->db->where('id_fuentes',$id);
      $this->db->update('sic_fuentes_fondo', $data);
    }

    public function eliminarFuente($id){
      $this->db->delete('sic_fuentes_fondo', array('id_fuentes' => $id));
    }

    public function obtenerIdPorNombre($nombre) {
      $this->db->where("nombre_fuente", $nombre);
      $query = $this->db->get('sic_fuentes_fondo');
      if ($query->num_rows() > 0) {
          return  $query->row('id_fuentes');
      }
      else {
          return FALSE;
      }
    }

  }
?>
