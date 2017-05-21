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
      // $this->db->select('p.id_producto, p.id_especifico, c.nombre_conteo, c.cantidad');
      // $this->db->from('sic_detalle_conteo c');
      // $this->db->join('sic_detalle_producto p', 'c.id_detalleproducto = p.id_detalleproducto');
      // $this->db->where('c.nombre_conteo', $conteo);
      // $query = $this->db->get('sic_detalle_conteo', $porpagina, $segmento);
      $segmento = intval($segmento);

      $query = $this->db->query('SELECT p.id_producto, p.id_especifico, c.nombre_conteo, c.cantidad, c.id_detalleproducto
        FROM sic_detalle_conteo c JOIN sic_detalle_producto p
        ON c.id_detalleproducto = p.id_detalleproducto WHERE c.nombre_conteo = "'.$conteo.'" LIMIT '.$segmento.', '.$porpagina.';');
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
