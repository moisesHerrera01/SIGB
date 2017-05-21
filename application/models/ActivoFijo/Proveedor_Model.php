<?php
	class Proveedor_model extends CI_Model{


    public $nombre_proveedor;
    public $nit_proveedor;
    public $correo_proveedor;
    public $telefono_proveedor;

    function __construct() {
        parent::__construct();
	}


    public function insertarProveedor($data){

        $this->nombre_proveedor = $data['nombre_proveedor'];
        $this->nit_proveedor = $data['nit_proveedor'];
        $this->correo_proveedor = $data['correo_proveedor'];
        $this->telefono_proveedor = $data['telefono_proveedor'];

        $this->db->insert('sic_proveedor', $this);
        return $this->db->insert_id();
    }

    public function obtenerProveedor(){
      $this->db->order_by("id_proveedor", "asc");
      $query = $this->db->get('sic_proveedor');
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerProveedordat($id){
        $this->db->where('id_proveedor',$id);
        $query = $this->db->get('sic_proveedor');
        if ($query->num_rows() > 0) {
          $nombre;
          foreach ($query->result() as $prov) {
            $nombre = $prov->nombre_proveedor;
          }
          return  $nombre;
        }
        else {
            return FALSE;
        }
    }

    public function obtenerProveedorLimit($porpagina, $segmento){
      $this->db->order_by("id_proveedor", "asc");
      $query = $this->db->get('sic_proveedor', $porpagina, $segmento);
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    function totalProveedor(){
      return $this->db->count_all('sic_proveedor');
    }

    public function buscarProveedor($busca){
      $this->db->order_by("id_proveedor", "asc");
      $this->db->like('nombre_proveedor', $busca);
      $this->db->or_like('nit_proveedor', $busca);
      $query = $this->db->get('sic_proveedor', 10);
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function actualizarProveedor($id, $data){
      $this->db->where('id_proveedor',$id);
      $this->db->update('sic_proveedor', $data);
    }

    public function eliminarProveedor($id){
      $this->db->delete('sic_proveedor', array('id_proveedor' => $id));
    }
}
?>
