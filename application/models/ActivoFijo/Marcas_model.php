<?php
  class Marcas_model extends CI_Model{

    public $nombre_marca;


    function __construct() {
        parent::__construct();
    }

    public function insertarMarcas($data){

        $this->nombre_marca = $data['nombre_marca'];

        $this->db->insert('sic_marcas', $this);
        return $this->db->insert_id();
    }

    public function obtenerMarcas(){
      $this->db->order_by("id_marca", "asc");
      $query = $this->db->get('sic_marcas');
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerMarcasDat($id){
        $this->db->where('id_marca',$id);
        $query = $this->db->get('sic_marcas');
        if ($query->num_rows() > 0) {
          $nombre_marca;
          foreach ($query->result() as $marca) {
            $nombre_marca = $marca->nombre_marca;
          }
          return  $nombre_marca;
        }
        else {
            return FALSE;
        }
    }

    public function obtenerMarcasLimit($porpagina, $segmento){
      $this->db->order_by("id_marca", "desc");
      $query = $this->db->get('sic_marcas', $porpagina, $segmento);
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    function totalMarcas(){
      return $this->db->count_all('sic_marcas');
    }

    public function buscarMarcas($busca){
      $this->db->order_by("id_marca", "desc");
      $this->db->like('nombre_marca', $busca);
      $query = $this->db->get('sic_marcas', 10);
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function actualizarMarcas($id, $data){
      $this->db->where('id_marca',$id);
      $this->db->update('sic_marcas', $data);
    }

    public function eliminarMarcas($id){
      $this->db->delete('sic_marcas', array('id_marca' => $id));
    }
    public function contieneDatoComun($id){
      $this->db->select('count(id_marca) as asociados')
               ->from('sic_datos_comunes')
               ->where('id_marca',$id);
      $query=$this->db->get();
      if ($query->num_rows()>0) {
        return $query->row();
      }else {
        return FALSE;
      }
    }
  }
?>
