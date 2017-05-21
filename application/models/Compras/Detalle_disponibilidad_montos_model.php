<?php
  class Detalle_disponibilidad_montos_model extends CI_Model{

    public $id_detalle_solicitud_disponibilidad;
    public $id_solicitud_disponibilidad;
    public $id_linea_trabajo;
    public $monto_sub_total;

    function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function insertarDetalleDisponibilidad($data){
        $this->id_detalle_solicitud_disponibilidad = $data['id_detalle_solicitud_disponibilidad'];
        $this->id_solicitud_disponibilidad = $data['id_solicitud_disponibilidad'];
        $this->id_linea_trabajo = $data['id_linea_trabajo'];
        $this->monto_sub_total = $data['monto_sub_total'];
        $this->db->insert('sic_detalle_solicitud_disponibilidad', $this);
    }

    public function obtenerDetalleDisponibilidad($id){
      $this->db->select('sd.id_solicitud_compra,sd.id_solicitud_disponibilidad,
      dsd.id_detalle_solicitud_disponibilidad,dsd.id_linea_trabajo,dsd.monto_sub_total,l.linea_trabajo,
      sc.estado_solicitud_compra,sc.nivel_solicitud')
               ->from('sic_solicitud_disponibilidad sd')
               ->join('sic_solicitud_compra sc','sc.id_solicitud_compra=sd.id_solicitud_compra')
               ->join('sic_detalle_solicitud_disponibilidad dsd','dsd.id_solicitud_disponibilidad=sd.id_solicitud_disponibilidad')
               ->join('org_linea_trabajo l','l.id_linea_trabajo=dsd.id_linea_trabajo')
               ->order_by("dsd.id_detalle_solicitud_disponibilidad", "asc")
               ->where('sd.id_solicitud_disponibilidad',$id);
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function eliminarDetalleDisponibilidad($id){
      $this->db->delete('sic_detalle_solicitud_disponibilidad',
       array('id_detalle_solicitud_disponibilidad' => $id));
    }

      public function actualizarDetalleDisponibilidad($id,$data){
        $this->db->where('id_detalle_solicitud_disponibilidad',$id);
        $this->db->update('sic_detalle_solicitud_disponibilidad', $data);
      }

      public function obtenerSolicitudCompra($id_solicitud_disponibilidad){
        $this->db->select('*')
                 ->from('sic_solicitud_disponibilidad sd')
                 ->join('sic_solicitud_compra sc','sc.id_solicitud_compra=sd.id_solicitud_compra')
                 ->where('sd.id_solicitud_disponibilidad',$id_solicitud_disponibilidad);
        $query=$this->db->get();
        return $query->row();
      }
  }
?>
