<?php
  class Categoria_model extends CI_Model{

    public $id_categoria;
    public $nombre_categoria;
    public $numero_categoria;
    public $descripcion;

    function __construct() {
        parent::__construct();
    }

    public function insertarCategoria($data){
        $this->nombre_categoria = $data['nombre_categoria'];
        $this->numero_categoria = $data['numero_categoria'];
        $this->descripcion = $data['descripcion'];
        $this->db->insert('sic_categoria', $this);
        return $this->db->insert_id();
    }

    public function obtenerCategorias(){
      $this->db->order_by("id_categoria", "asc");
      $query = $this->db->get('sic_categoria');
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function buscarCategorias($busca){
      $this->db->order_by("id_categoria", "desc");
      $this->db->like('nombre_categoria', $busca);
      $this->db->or_like('numero_categoria', $busca);
      $query = $this->db->get('sic_categoria', 10);
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerCategoria($id){
        $this->db->where('id_categoria',$id);
        $query = $this->db->get('sic_categoria');
        if ($query->num_rows() > 0) {
          $nombre;
          foreach ($query->result() as $cat) {
            $nombre = $cat->nombre_categoria;
          }
          return  $nombre;
        }
        else {
            return FALSE;
        }
    }

    public function actualizarCategoria($id, $data){
      $this->db->where('id_categoria',$id);
      $this->db->update('sic_categoria', $data);
    }

    public function eliminarCategoria($id){
      $this->db->delete('sic_categoria', array('id_categoria' => $id));
    }

    function totalCategorias(){
      return $this->db->count_all('sic_categoria');
    }

    public function obtenerCategoriasLimit($porpagina, $segmento){
      $this->db->order_by("numero_categoria", "desc");
      $query = $this->db->get('sic_categoria', $porpagina, $segmento);
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function contieneSubCategoria($id) {
      $this->db->select('count(id_subcategoria) as asociados')
               ->from('sic_categoria')
               ->where('id_categoria',$id);
      $query=$this->db->get();
      if ($query->num_rows()>0) {
        return $query->row();
      }else {
        return FALSE;
      }
    }

    public function reporteEstadistico($fecha_inicio,$fecha_fin,$id_categoria){
        $this->db->select('cb.nombre_condicion_bien,c.id_categoria,c.nombre_categoria,s.id_subcategoria,
                          s.nombre_subcategoria,o.id_oficina,o.nombre_oficina')
                 ->from('sic_bien b')
                 ->join('sic_datos_comunes dc','dc.id_dato_comun=b.id_dato_comun')
                 ->join('sic_subcategoria s','s.id_subcategoria=dc.id_subcategoria')
                 ->join('sic_categoria c','c.id_categoria=s.id_categoria')
                 ->join('sic_condicion_bien cb','cb.id_condicion_bien=b.id_condicion_bien')
                 ->join('org_oficina o','o.id_oficina=b.id_oficina')
                 ->order_by('b.id_oficina','asc')
                 ->group_by('b.id_oficina')
                 ->where('c.id_categoria',$id_categoria)
                 ->where("dc.fecha_adquisicion BETWEEN '$fecha_inicio' AND '$fecha_fin'");
        $query=$this->db->get();
        if ($query->num_rows()>0) {
          return $query->result();
        }else{
          return FALSE;
        }
      }
}
?>
