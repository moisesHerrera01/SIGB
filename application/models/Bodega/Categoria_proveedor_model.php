<?php
  class Categoria_proveedor_model extends CI_Model{

    public $nombre_categoria;
    public $tipo_empresa;
    public $rubro;

    function __construct() {
        parent::__construct();
    }

    public function insertarCategoria($data){

        $this->nombre_categoria = $data['nombre_categoria'];
        $this->tipo_empresa = $data['tipo_empresa'];
        $this->rubro = $data['rubro'];
        $this->db->insert('sic_categoria_proveedor', $this);
        return $this->db->insert_id();
    }

    public function obtenerCategorias(){
      $this->db->order_by("id_categoria_proveedor", "asc");
      $query = $this->db->get('sic_categoria_proveedor');
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerCategoria($id){
        $this->db->where('id_categoria_proveedor',$id);
        $query = $this->db->get('sic_categoria_proveedor');
        if ($query->num_rows() > 0) {
          $nombre;
          foreach ($query->result() as $cat) {
            $nombre = $cat->nombre;
          }
          return  $nombre;
        }
        else {
            return FALSE;
        }
    }

    public function buscarCategorias($busca){
      $this->db->order_by("id_categoria_proveedor", "asc");
      $this->db->like('nombre_categoria', $busca);
      $this->db->or_like('tipo_empresa', $busca);
      $this->db->or_like('rubro', $busca);
      $query = $this->db->get('sic_categoria_proveedor', 10);
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function actualizarCategoria($id, $data){
      $this->db->where('id_categoria_proveedor',$id);
      $this->db->update('sic_categoria_proveedor', $data);
    }

    public function eliminarCategoria($id){
      $this->db->delete('sic_categoria_proveedor', array('id_categoria_proveedor' => $id));
    }

    function totalCategorias(){
      return $this->db->count_all('sic_categoria_proveedor');
    }

    public function obtenerCategoriasLimit($porpagina, $segmento){
      $this->db->order_by("id_categoria_proveedor", "asc");
      $query = $this->db->get('sic_categoria_proveedor', $porpagina, $segmento);
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

  }
?>
