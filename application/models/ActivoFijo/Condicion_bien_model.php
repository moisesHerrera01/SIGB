<?php
  class Condicion_bien_model extends CI_Model{

    public $nombre_condicion_bien;


    function __construct() {
        parent::__construct();
    }

    public function insertarCondicion($data){

        $this->nombre_condicion_bien = $data['nombre_condicion_bien'];

        $this->db->insert('sic_condicion_bien', $this);
        return $this->db->insert_id();
    }

    public function obtenerCondiciones(){
      $this->db->order_by("id_condicion_bien", "asc");
      $query = $this->db->get('sic_condicion_bien');
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function obtenercondicion($id){
        $this->db->where('id_condicion_bien',$id);
        $query = $this->db->get('sic_condicion_bien');
        if ($query->num_rows() > 0) {
          $nombre;
          foreach ($query->result() as $unidad) {
            $nombre_condicion_bien = $unidad->nombre_condicion_bien;
          }
          return  $nombre_condicion_bien;
        }
        else {
            return FALSE;
        }
    }

    public function obtenercondicionId($nombre){
        $this->db->where('nombre_condicion_bien',$nombre);
        $query = $this->db->get('sic_condicion_bien');
        if ($query->num_rows() > 0) {
          $nombre;
          foreach ($query->result() as $con) {
            $id = $con->id_condicion_bien;
          }
          return  $id;
        }
        else {
            return FALSE;
        }
    }

    public function buscarCondiciones($busca){
      $this->db->order_by("id_condicion_bien", "desc");
      $this->db->like('nombre_condicion_bien', $busca);
      $query = $this->db->get('sic_condicion_bien', 10);
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function actualizarCondicion($id, $data){
      $this->db->where('id_condicion_bien',$id);
      $this->db->update('sic_condicion_bien', $data);
    }

    public function eliminarCondicion($id){
      $this->db->delete('sic_condicion_bien', array('id_condicion_bien' => $id));
    }

    function totalCondiciones(){
      return $this->db->count_all('sic_condicion_bien');
    }

    public function obtenerCondicionesLimit($porpagina, $segmento){
      $this->db->order_by("id_condicion_bien", "desc");
      $query = $this->db->get('sic_condicion_bien', $porpagina, $segmento);
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerIdPorNombre_condicion_bien($nombre_condicion_bien) {
      $this->db->where('nombre_condicion_bien', $nombre_condicion_bien);
      $query = $this->db->get('sic_condicion_bien');
      if ($query->num_rows() > 0) {
        return  $query->row('id_mov');
      }
      else {
          return FALSE;
      }
    }

  }
?>
