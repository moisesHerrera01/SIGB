<?php
  class Seleccion_subcategoria_model extends CI_Model{

    public $id_bien;
    public $tipo_computadora;

    function __construct() {
        parent::__construct();
    }

    public function insertarEquipo($data){
        $this->id_bien = $data['id_bien'];
        $this->tipo_computadora = $data['tipo_computadora'];
        $this->db->insert('sic_equipo_informatico', $this);
        return $this->db->insert_id();
    }

    public function obtenerEquipoLimit($porpagina, $segmento){
      $this->db->select('e.id_equipo_informatico,d.descripcion,e.id_bien,e.tipo_computadora')
               ->from('sic_equipo_informatico e')
               ->join('sic_bien b','b.id_bien=e.id_bien')
               ->join('sic_datos_comunes d','d.id_dato_comun=b.id_dato_comun')
               ->limit($porpagina, $segmento);
      $query=$this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function buscarEquipo($busca){
      $this->db->select('e.id_equipo_informatico,d.descripcion,e.id_bien,e.tipo_computadora')
           ->from('sic_equipo_informatico e')
           ->join('sic_bien b','b.id_bien=e.id_bien')
           ->join('sic_datos_comunes d','d.id_dato_comun=b.id_dato_comun')
           ->like('d.descripcion',$busca)
           ->or_like('tipo_computadora',$busca);
      $query=$this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function totalEquipo(){
      $this->db->select('count(*) as total')
           ->from('sic_equipo_informatico e')
           ->join('sic_bien b','b.id_bien=e.id_bien')
           ->join('sic_datos_comunes d','d.id_dato_comun=b.id_dato_comun');
      $query=$this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->row();
      }
      else {
          return FALSE;
      }
    }

    public function actualizarEquipo($id, $data){
      $this->db->where('id_equipo_informatico',$id);
      $this->db->update('sic_equipo_informatico', $data);
    }

    public function eliminarEquipo($id){
      $this->db->delete('sic_equipo_informatico', array('id_equipo_informatico' => $id));
    }

    public function obtenerIdSubcategoria($nombre_subcategoria){
      $this->db->select('*')
               ->from('sic_subcategoria s')
               ->join('sic_categoria c','c.id_categoria=s.id_categoria')
               ->where('nombre_subcategoria',$nombre_subcategoria);
      $query=$this->db->get();
      $id;
      foreach ($query->result() as $cat) {
        if($cat->nombre_categoria=='SOFTWARE' || $cat->nombre_categoria=='HARDWARE' ||
        $cat->nombre_categoria=='RED'){
          $id=$cat->id_subcategoria;
        }
      }
      if ($query->num_rows() > 0) {
        return $id;
      }
      else {
          return FALSE;
      }
    }

    public function validarDetalleEquipo($id_equipo_informatico){
      $this->db->select('')
               ->from('sic_equipo_informatico e')
               ->join('sic_detalle_equipo_informatico d','d.id_equipo_informatico=e.id_equipo_informatico')
               ->where('e.id_equipo_informatico',$id_equipo_informatico);
      if ($this->db->get()->num_rows()>0) {
        return TRUE;
      }else {
        return FALSE;
      }
    }
}
?>
