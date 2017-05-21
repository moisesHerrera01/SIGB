<?php
  class Detalle_movimiento_model extends CI_Model{

    public $id_movimiento;
    public $id_bien;
    public $id_guarda;

    function __construct() {
        parent::__construct();
    }

    public function insertarDetalleMovimiento($data){
        $USER = $this->session->userdata('logged_in');
        $this->id_movimiento = $data['id_movimiento'];
        $this->id_bien = $data['id_bien'];
        $this->id_guarda=$USER['id'];
        $this->db->insert('sic_detalle_movimiento', $this);

        return $this->db->insert_id();
    }
    public function obtenerMovimiento($id){
      $this->db->select('m.observacion')
           ->from('sic_movimiento m')
           ->where('m.id_movimiento',$id);
      $query = $this->db->get();
      if($query->num_rows() > 0 )
        {
           return $query->row();
        }
    }

    public function obtenerCorrelativo(){
      $this->db->select('max(correlativo) as cor')
               ->from('sic_bien');
      $query = $this->db->get();
      $cor;
          foreach ($query->result() as $var) {
              $cor=$var->cor+1;
          }
      return $cor;
    }

    public function obtenerDetalleMovimientos(){
      $this->db->select('d.id_movimiento,m.observacion,d.id_bien,b.codigo,d.id_detalle_movimiento')
               ->from('sic_detalle_movimiento d')
               ->join('sic_movimiento m','d.id_movimiento=m.id_movimiento')
               ->join('sic_bien b','d.id_bien=b.id_bien')
               ->order_by('d.id_detalle_movimiento');
      $query=$this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function buscarDetalleMovimiento($busca){
      $this->db->select('d.id_detalle_movimiento,d.id_movimiento,m.observacion,d.id_bien,b.codigo')
               ->from('sic_detalle_movimiento d')
               ->join('sic_movimiento m','d.id_movimiento=m.id_movimiento')
               ->join('sic_bien b','d.id_bien=b.id_bien')
               ->order_by('d.id_detalle_movimiento')
               ->like('d.id_detalle_movimiento', $busca)
               ->or_like('b.codigo', $busca);
      $query=$this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function actualizarDetalleMovimiento($id, $data){
      $this->db->where('id_detalle_movimiento',$id);
      $this->db->update('sic_detalle_movimiento', $data);
    }

    public function eliminarDetalleMovimiento($id){
      $this->db->delete('sic_detalle_movimiento', array('id_detalle_movimiento' => $id));
    }

    function totalDetalleMovimientos(){
      return $this->db->count_all('sic_doc_ampara');
    }

    public function obtenerDetalleMovimientosLimit($porpagina, $segmento,$id){
      $this->db->select('d.id_detalle_movimiento,d.id_movimiento,m.observacion,d.id_bien,b.codigo,dc.descripcion,
      mc.nombre_marca,dc.modelo,dc.color,b.serie,b.codigo,b.codigo_anterior')
               ->from('sic_detalle_movimiento d')
               ->join('sic_movimiento m','d.id_movimiento=m.id_movimiento')
               ->join('sic_bien b','d.id_bien=b.id_bien')
               ->join('sic_datos_comunes dc','dc.id_dato_comun=b.id_dato_comun')
               ->join('sic_marcas mc','mc.id_marca=dc.id_marca')
               ->order_by('d.id_detalle_movimiento')
               ->where('m.id_movimiento',$id)
               ->limit($porpagina,$segmento);
      $query=$this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerDetallePorMovimientos($id_mov) {
      $this->db->select('d.id_detalle_movimiento,d.id_movimiento,m.observacion,d.id_bien,b.codigo,dc.descripcion,
      mc.nombre_marca,dc.modelo,dc.color,b.serie,b.codigo,b.codigo_anterior')
               ->from('sic_detalle_movimiento d')
               ->join('sic_movimiento m','d.id_movimiento=m.id_movimiento')
               ->join('sic_bien b','d.id_bien=b.id_bien')
               ->join('sic_datos_comunes dc','dc.id_dato_comun=b.id_dato_comun')
               ->join('sic_marcas mc','mc.id_marca=dc.id_marca')
               ->where('d.id_movimiento', $id_mov)
               ->order_by('d.id_detalle_movimiento');
      $query=$this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result_array();
      }
      else {
          return FALSE;
      }
    }
    public function obtenerTodoMovimiento($id) {
      $this->db->select('*')
               ->from('sic_movimiento a')
               ->where('id_movimiento',$id);
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
        return  $query->row();
      }
      else {
        return FALSE;
      }
    }
    public function obtenerTodoDetalleMovimiento($id) {
      $this->db->select('*')
               ->from('sic_detalle_movimiento a')
               ->where('id_detalle_movimiento',$id);
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
        return  $query->row();
      }
      else {
        return FALSE;
      }
    }

    public function bienes_sin_movimiento($maxFecha, $minFecha, $porpagina, $segmento) {
      if ($segmento == NULL) {
        $segmento = 1;
      }
       $query = $this->db->query("SELECT c.id_bien, d.descripcion, e.nombre_marca, d.modelo, c.serie, c.codigo, c.codigo_anterior
        FROM sic_detalle_movimiento a
        INNER JOIN (
        	SELECT ab.id_detalle_movimiento FROM sic_movimiento aa
        	INNER JOIN sic_detalle_movimiento ab ON aa.id_movimiento = ab.id_movimiento
        	WHERE fecha_guarda BETWEEN '".$minFecha."' AND '".$maxFecha."'
        ) b ON a.id_detalle_movimiento = b.id_detalle_movimiento
        RIGHT JOIN sic_bien c ON a.id_bien = c.id_bien
        INNER JOIN sic_datos_comunes d ON d.id_dato_comun = c.id_dato_comun
        INNER JOIN sic_marcas e ON e.id_marca = d.id_marca
        WHERE a.id_bien IS NULL
        LIMIT ".$segmento.", ".$porpagina."
       ");

       if ($query->num_rows() > 0) {
         return $query->result();
       } else {
         return false;
       }
    }

    public function total_bienes_sin_movimiento($maxFecha, $minFecha) {

       $query = $this->db->query("SELECT c.id_bien, d.descripcion, e.nombre_marca, d.modelo, c.serie, c.codigo, c.codigo_anterior
        FROM sic_detalle_movimiento a
        INNER JOIN (
        	SELECT ab.id_detalle_movimiento FROM sic_movimiento aa
        	INNER JOIN sic_detalle_movimiento ab ON aa.id_movimiento = ab.id_movimiento
        	WHERE fecha_guarda BETWEEN '".$minFecha."' AND '".$maxFecha."'
        ) b ON a.id_detalle_movimiento = b.id_detalle_movimiento
        RIGHT JOIN sic_bien c ON a.id_bien = c.id_bien
        INNER JOIN sic_datos_comunes d ON d.id_dato_comun = c.id_dato_comun
        INNER JOIN sic_marcas e ON e.id_marca = d.id_marca
        WHERE a.id_bien IS NULL
       ");

       if ($query->num_rows() > 0) {
         return $query->num_rows();
       } else {
         return 0;
       }
    }

  }
?>
