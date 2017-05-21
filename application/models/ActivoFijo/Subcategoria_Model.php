<?php
  class Subcategoria_model extends CI_Model{

    public $id_subcategoria;
    public $id_categoria;
    public $nombre_subcategoria;
    public $numero_subcategoria;
    public $descripcion;

    function __construct() {
        parent::__construct();
    }

    public function insertarSubcategoria($data){
        $this->id_categoria = $data['id_categoria'];
        $this->nombre_subcategoria = $data['nombre_subcategoria'];
        $this->numero_subcategoria = $data['numero_subcategoria'];
        $this->descripcion = $data['descripcion'];
        $this->db->insert('sic_subcategoria', $this);
        return $this->db->insert_id();
    }

    public function obtenerSubcategorias(){
      $this->db->order_by("id_subcategoria", "asc");
      $query = $this->db->get('sic_subcategoria');
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerSubcategoriasInformatico(){
      $this->db->select('s.id_subcategoria,s.nombre_subcategoria,c.nombre_categoria')
               ->from('sic_subcategoria s')
               ->join('sic_categoria c','c.id_categoria=s.id_categoria');
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

      public function buscarSubcategorias($busca,$id){
      $this->db->where('id_categoria',$id);
      $this->db->order_by("id_subcategoria", "desc");
      $this->db->like('nombre_subcategoria', $busca);
      $this->db->or_like('numero_subcategoria', $busca);
      $query = $this->db->get('sic_subcategoria', 10);
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function buscarSubcategoriasAutocomplete($busca){
    $this->db->select('s.id_subcategoria,s.nombre_subcategoria,c.nombre_categoria')
             ->from('sic_subcategoria s')
             ->join('sic_categoria c','c.id_categoria=s.id_categoria');
    $this->db->like('nombre_subcategoria', $busca);
    $this->db->or_like('numero_subcategoria', $busca);
    $query = $this->db->get();
    if ($query->num_rows() > 0) {
        return  $query->result();
    }
    else {
        return FALSE;
    }
  }

    public function actualizarSubcategoria($id, $data){
      $this->db->where('id_subcategoria',$id);
      $this->db->update('sic_subcategoria', $data);
    }

    public function eliminarSubcategoria($id){
      $this->db->delete('sic_subcategoria', array('id_subcategoria' => $id));
    }

    function totalSubcategorias($id){
      $this->db->where('id_categoria',$id);
      $query = $this->db->get('sic_subcategoria');
      if ($query->num_rows() > 0) {
          $i = 0;
          foreach ($query->result() as $cat) {
            $i++;
          }
          return  $i;
      }
      else {
          return FALSE;
      }

    }

    public function obtenerSubcategoriasLimit($porpagina, $segmento,$id){
      $this->db->where('id_categoria',$id);
      $this->db->order_by("numero_subcategoria", "desc");
      $query = $this->db->get('sic_subcategoria', $porpagina, $segmento);
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }


    public function obtenerEstadisticoSubCategoria($subcategoria, $seccion, $fecha) {
      $query = $this->db->query("SELECT *
        FROM org_seccion aa
        INNER JOIN (
        	SELECT a.id_detalle_movimiento, a.id_bien, c.id_oficina_entrega, c.id_oficina_recibe,
        		c.id_tipo_movimiento, c.fecha_guarda, f.id_seccion, i.id_subcategoria, i.nombre_subcategoria
        	FROM sic_detalle_movimiento a
        	INNER JOIN (
        		SELECT MAX( id_detalle_movimiento ) detalle, id_bien
        		FROM sic_detalle_movimiento
        		GROUP BY id_bien
        	)b ON a.id_bien = b.id_bien
        	INNER JOIN sic_movimiento c ON a.id_movimiento = c.id_movimiento
        	INNER JOIN org_oficina d ON c.id_oficina_recibe = d.id_oficina
        	INNER JOIN org_seccion_has_almacen e ON d.id_seccion_has_almacen = e.id_seccion_has_almacen
        	INNER JOIN org_seccion f ON e.id_seccion = f.id_seccion
        	INNER JOIN sic_bien g ON g.id_bien = a.id_bien
        	INNER JOIN sic_datos_comunes h ON g.id_dato_comun = h.id_dato_comun
        	INNER JOIN sic_subcategoria i ON i.id_subcategoria = h.id_subcategoria
        	AND b.detalle = a.id_detalle_movimiento
        	AND i.id_subcategoria = ".$subcategoria."
        	AND c.fecha_guarda < '".$fecha."'
        	ORDER BY a.id_detalle_movimiento DESC
        ) ba ON ba.id_seccion = aa.id_seccion
        AND aa.id_seccion = ".$seccion."
      ");

      if ($query->num_rows() > 0) {
          return  $query->result();
      } else {
        return FALSE;
      }
    }
}
?>
