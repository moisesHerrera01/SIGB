<?php
  class ConteoFisico_model extends CI_Model{

    public $nombre_conteo;
    public $fecha_inicial;
    public $fecha_final;
    public $descripcion;

    function __construct() {
        parent::__construct();
    }

    public function insertarConteo($data){

        $this->nombre_conteo = $data['nombre_conteo'];
        $this->fecha_inicial = $data['fecha_inicial'];
        $this->fecha_final = $data['fecha_final'];
        $this->descripcion = $data['descripcion'];

        $this->db->insert('sic_conteo_fisico', $this);
    }

    public function obtenerConteos(){
      $this->db->order_by("fecha_inicial", "asc");
      $query = $this->db->get('sic_conteo_fisico');
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerConteo($nombre){
        $this->db->where('nombre_conteo', $nombre);
        $query = $this->db->get('sic_conteo_fisico');
        if ($query->num_rows() > 0) {
          $nombre;
          foreach ($query->result() as $conteo) {
            $nombre = $conteo->nombre_conteo;
          }
          return  $nombre;
        }
        else {
            return FALSE;
        }
    }

    public function buscarConteoNombre($busca){
      $this->db->order_by("fecha_inicial", "asc");
      $this->db->like('nombre_conteo', $busca);
      $query = $this->db->get('sic_conteo_fisico', 10);
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function buscarConteoFecha($busca){
      $this->db->order_by("fecha_inicial", "asc");
      $query = $this->db->query("SELECT * FROM sic_conteo_fisico WHERE  date >= '.$busca.'");
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function actualizarConteo($id, $data){
      $this->db->where('nombre_conteo',$id);
      $this->db->update('sic_conteo_fisico', $data);
    }

    public function eliminarConteo($id){
      $this->db->delete('sic_conteo_fisico', array('nombre_conteo' => $id));
    }

    function totalConteos(){
      return $this->db->count_all('sic_conteo_fisico');
    }

    public function obtenerConteosLimit($porpagina, $segmento){
      $this->db->order_by("fecha_inicial", "asc");
      $query = $this->db->get('sic_conteo_fisico', $porpagina, $segmento);
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerFechaConteo($nombre){
        $this->db->where('nombre_conteo', $nombre);
        $query = $this->db->get('sic_conteo_fisico');
        if ($query->num_rows() > 0) {
          $fecha;
          foreach ($query->result() as $conteo) {
            $fecha = $conteo->fecha_final;
          }
          return  $fecha;
        }
        else {
            return FALSE;
        }
    }

  }
?>
