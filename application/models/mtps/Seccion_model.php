<?php
  class Seccion_model extends CI_Model{

    function __construct() {
        parent::__construct();
    }

    public function obtenerPorIdSeccion($id){
      $this->db->where('id_seccion', $id);
      $query = $this->db->get('org_seccion')->row();
      $seccion = '';
      if (!is_null($query)) {
          $seccion = $query->nombre_seccion;
      }
      return $seccion;
    }

    public function obtenerOficinasSeccion($id) {
      $this->db->select('a.id_oficina, a.nombre_oficina')
               ->from('org_oficina a')
               ->join('org_seccion_has_almacen b', 'a.id_seccion_has_almacen = b.id_seccion_has_almacen')
               ->where('b.id_seccion', $id)
               ->group_by('a.id_oficina');

       $query=$this->db->get();
       if ($query->num_rows()>0) {
         return $query->result();
       }
    }

    public function buscarOficinasSeccion($id, $busca) {
      $this->db->select('a.id_oficina, a.nombre_oficina')
               ->from('org_oficina a')
               ->join('org_seccion_has_almacen b', 'a.id_seccion_has_almacen = b.id_seccion_has_almacen')
               ->where('b.id_seccion', $id)
               ->like('a.nombre_oficina', $busca)
               ->group_by('a.id_oficina');

       $query=$this->db->get();
       if ($query->num_rows()>0) {
         return $query->result();
       }
    }

    public function nombreEmpleado($id_empleado) {
      $this->db->select('primer_nombre, segundo_nombre, primer_apellido, segundo_apellido')
               ->from('sir_empleado')
               ->where('id_empleado', $id_empleado);

       $query = $this->db->get();
       if ($query->num_rows()>0) {
         $empleado = $query->row();
         return $empleado->primer_nombre . " " . $empleado->segundo_nombre . " " . $empleado->primer_apellido . " " . $empleado->segundo_apellido;
       }
    }

    public function obtenerAlmacenSeccionOficina($id) {
      $this->db->select('d.nombre_almacen, c.nombre_seccion, a.nombre_oficina')
               ->from('org_oficina a')
               ->join('org_seccion_has_almacen b', 'a.id_seccion_has_almacen = b.id_seccion_has_almacen')
               ->join('org_seccion c', 'c.id_seccion = b.id_seccion')
               ->join('org_almacen d', 'b.id_almacen = d.id_almacen')
               ->where('a.id_oficina', $id);
      $query=$this->db->get();
      if ($query->num_rows()>0) {
       return $query->row_array();
      }
    }
  }
?>
