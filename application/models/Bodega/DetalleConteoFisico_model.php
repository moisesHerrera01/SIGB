<?php
  class DetalleConteoFisico_model extends CI_Model{

    public $nombre_conteo;
    public $cantidad;
    public $id_detalleproducto;

    function __construct() {
        parent::__construct();
    }

    public function insertarDetalleConteo($data){

        $this->id_detalleproducto = $data['id_detalleproducto'];
        $this->nombre_conteo = $data['nombre_conteo'];
        $this->cantidad = $data['cantidad'];

        $this->db->insert('sic_detalle_conteo', $this);
    }

    public function obtenerDetalleConteos(){
      $query = $this->db->get('sic_detalle_conteo');
      if ($query->num_rows() > 0) {
          return  $query;
      }
      else {
          return FALSE;
      }
    }

    public function obtenerDetalleConteo($producto, $conteo){
        $this->db->where('id_producto', $producto);
        $this->db->where('nombre_conteo', $conteo);
        $query = $this->db->get('sic_detalle_conteo');
        if ($query->num_rows() > 0) {
          $detalle_conteo;
          foreach ($query->result() as $dconteo) {
            $detalle_conteo = $dconteo;
          }
          return  $detalle_conteo;
        }
        else {
            return FALSE;
        }
    }

    public function buscarDetalleConteos($busca){
      $this->db->like('nombre_conteo', $busca);
      $query = $this->db->get('sic_detalle_conteo', 10);
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function actualizarDetalleConteo($data){
      $this->db->where('id_producto', $data['id_producto']);
      $this->db->where('nombre_conteo', $data['nombre_conteo']);
      $this->db->update('sic_detalle_conteo', $data);
    }

    function totalDetalleConteos(){
      return $this->db->count_all('sic_detalle_conteo');
    }

    public function obtenerDetalleConteosLimit($conteo, $porpagina, $segmento){
      $segmento = intval($segmento);

      $this->db->select('b.id_producto, b.id_especifico, a.nombre_conteo, a.cantidad, b.id_detalleproducto,
                          c.nombre nombre_producto, d.nombre nombre_unidad')
               ->from('sic_detalle_conteo a')
               ->join('sic_detalle_producto b', 'a.id_detalleproducto = b.id_detalleproducto')
               ->join('sic_producto c', 'b.id_producto = c.id_producto')
               ->join('sic_unidad_medida d', 'c.id_unidad_medida = d.id_unidad_medida')
               ->where('a.nombre_conteo', $conteo)
               ->limit($porpagina, $segmento);
      
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerDetalleConteosBusca($conteo, $busca) {
      $this->db->select('b.id_producto, b.id_especifico, a.nombre_conteo, a.cantidad, b.id_detalleproducto,
                          c.nombre nombre_producto, d.nombre nombre_unidad')
               ->from('sic_detalle_conteo a')
               ->join('sic_detalle_producto b', 'a.id_detalleproducto = b.id_detalleproducto')
               ->join('sic_producto c', 'b.id_producto = c.id_producto')
               ->join('sic_unidad_medida d', 'c.id_unidad_medida = d.id_unidad_medida')
               ->where('a.nombre_conteo', $conteo)
               ->like('c.nombre', $busca)
               ->or_like('b.id_especifico', $busca);

      $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    function totalDetalleConteo($conteo){
      $this->db->where('nombre_conteo', $conteo);
      return $this->db->count_all('sic_detalle_conteo');
    }

    public function obtenerDetalleConteosTotal($conteo){
      $query = $this->db->query('SELECT p.id_producto, p.id_especifico, c.nombre_conteo, c.cantidad, c.id_detalleproducto
        FROM sic_detalle_conteo c JOIN sic_detalle_producto p
        ON c.id_detalleproducto = p.id_detalleproducto WHERE c.nombre_conteo = "'.$conteo.'";');
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

  }
?>
