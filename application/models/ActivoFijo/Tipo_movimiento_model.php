<?php
  class Tipo_movimiento_model extends CI_Model{

    public $nombre_movimiento;


    function __construct() {
        parent::__construct();
    }

    public function insertarMovimiento($data){

        $this->nombre_movimiento = $data['nombre_movimiento'];

        $this->db->insert('sic_tipo_movimiento', $this);
        return $this->db->insert_id();
    }

    public function obtenerMovimientos(){
      $this->db->order_by("id_tipo_movimiento", "asc");
      $query = $this->db->get('sic_tipo_movimiento');
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerMovimiento($id){
        $this->db->where('id_tipo_movimiento',$id);
        $query = $this->db->get('sic_tipo_movimiento');
        if ($query->num_rows() > 0) {
          $nombre;
          foreach ($query->result() as $unidad) {
            $nombreMovimiento = $unidad->nombre_movimiento;
          }
          return  $nombreMovimiento;
        }
        else {
            return FALSE;
        }
    }
    public function obtenerMovimientoId($nombre){
        $this->db->where('nombre_movimiento',$nombre);
        $query = $this->db->get('sic_tipo_movimiento');
        if ($query->num_rows() > 0) {
          $id;
          foreach ($query->result() as $mov) {
            $id = $mov->id_tipo_movimiento;
          }
          return  $id;
        }
        else {
            return FALSE;
        }
    }

    public function buscarMovimientos($busca){
      $this->db->order_by("id_tipo_movimiento", "asc");
      $this->db->like('nombre_movimiento', $busca);
      $query = $this->db->get('sic_tipo_movimiento', 10);
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function actualizarMovimiento($id, $data){
      $this->db->where('id_tipo_movimiento',$id);
      $this->db->update('sic_tipo_movimiento', $data);
    }

    public function eliminarMovimiento($id){
      $this->db->delete('sic_tipo_movimiento', array('id_tipo_movimiento' => $id));
    }

    function totalMovimientos(){
      return $this->db->count_all('sic_tipo_movimiento');
    }

    public function obtenerMovimientosLimit($porpagina, $segmento){
      $this->db->order_by("id_tipo_movimiento", "desc");
      $query = $this->db->get('sic_tipo_movimiento', $porpagina, $segmento);
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerIdPorNombreMovimiento($nombreMovimiento) {
      $this->db->where('nombre_movimiento', $nombreMovimiento);
      $query = $this->db->get('sic_tipo_movimiento');
      if ($query->num_rows() > 0) {
        return  $query->row('id_tipo_movimiento');
      }
      else {
          return FALSE;
      }
    }
    public function contieneMovimiento($id){
      $this->db->select('count(id_tipo_movimiento) as asociados')
               ->from('sic_movimiento')
               ->where('id_tipo_movimiento',$id);
      $query=$this->db->get();
      if ($query->num_rows()>0) {
        return $query->row();
      }else {
        return FALSE;
      }
    }
    public function obtenerMovimientosPorTipoLimit($id_tipo_movimiento,$fecha_inicio,$fecha_fin,$porpagina, $segmento){
      $this->db->select('m.id_movimiento,m.id_oficina_recibe,m.id_oficina_entrega,m.estado_movimiento,o.nombre_oficina,m.observacion,
               tm.nombre_movimiento')
               ->from('sic_movimiento m')
               ->join('sic_tipo_movimiento tm','tm.id_tipo_movimiento=m.id_tipo_movimiento')
               ->join('org_oficina o','o.id_oficina=m.id_oficina_entrega')
               ->where('m.id_tipo_movimiento',$id_tipo_movimiento)
               ->limit($porpagina,$segmento)
               ->where("m.fecha_guarda BETWEEN '$fecha_inicio' AND '$fecha_fin'");
      $query=$this->db->get();
      if ($query->num_rows()>0) {
        return $query->result();
      }else {
        return FALSE;
      }
    }
    public function obtenerMovimientosPorTipo($id_tipo_movimiento,$fecha_inicio,$fecha_fin){
      $this->db->select('m.id_movimiento,m.id_oficina_recibe,m.id_oficina_entrega,m.estado_movimiento,o.nombre_oficina,m.observacion,
               tm.nombre_movimiento')
               ->from('sic_movimiento m')
               ->join('sic_tipo_movimiento tm','tm.id_tipo_movimiento=m.id_tipo_movimiento')
               ->join('org_oficina o','o.id_oficina=m.id_oficina_entrega')
               ->where('m.id_tipo_movimiento',$id_tipo_movimiento)
               ->where("m.fecha_guarda BETWEEN '$fecha_inicio' AND '$fecha_fin'");
      $query=$this->db->get();
      if ($query->num_rows()>0) {
        return $query->result();
      }else {
        return FALSE;
      }
    }
    public function totalMovimientosPorTipo($id_tipo_movimiento,$fecha_inicio,$fecha_fin){
      $this->db->select('count(m.id_movimiento) as total')
               ->from('sic_movimiento m')
               ->join('sic_tipo_movimiento tm','tm.id_tipo_movimiento=m.id_tipo_movimiento')
               ->where('m.id_tipo_movimiento',$id_tipo_movimiento)
               ->where("m.fecha_guarda BETWEEN '$fecha_inicio' AND '$fecha_fin'");
      $query=$this->db->get();
      if ($query->num_rows()>0) {
        return $query->row();
      }else {
        return FALSE;
      }
    }
  }
?>
