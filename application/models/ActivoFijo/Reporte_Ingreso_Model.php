<?php
  class Reporte_Ingreso_Model extends CI_Model{

    function __construct() {
        parent::__construct();
    }

    public function obtenerIngresosFiltro($fecha_inicio,$fecha_fin,$segmento,$porpagina){
     $this->db->select('e.nombre_marca,a.modelo,b.serie,f.nombre_cuenta,a.descripcion,a.nombre_doc_ampara,a.fecha_adquisicion,a.precio_unitario,b.codigo,b.codigo_anterior,c.nombre_subcategoria,h.nombre_categoria,g.nombre_fuente,j.nombre_seccion,i.primer_nombre,i.segundo_nombre,i.primer_apellido,i.segundo_apellido')
             ->from("(select max(id_detalle_movimiento) as id_detalle_movimiento,max(id_movimiento) as id_movimiento,
              id_bien from sic_detalle_movimiento group by id_bien order by id_detalle_movimiento) as dm")
             ->join('sic_movimiento m','dm.id_movimiento=m.id_movimiento')
             ->join('sic_bien b','b.id_bien=dm.id_bien')
             ->join('sic_datos_comunes a','a.id_dato_comun=b.id_dato_comun')
              ->join('sic_subcategoria c', 'c.id_subcategoria= a.id_subcategoria')
              ->join('sir_empleado_informacion_laboral d','d.id_empleado = m.id_empleado')
              ->join('sic_marcas e','e.id_marca = a.id_marca')
              ->join('sic_cuenta_contable f','f.id_cuenta_contable = a.id_cuenta_contable')
              ->join('sic_fuentes_fondo g','g.id_fuentes = a.id_fuentes')
              ->join('sic_categoria h','h.id_categoria = c.id_categoria')
              ->join('sir_empleado i','i.id_empleado = m.id_empleado')
              ->join('org_seccion j','j.id_seccion = d.id_seccion')
              ->where("a.fecha_adquisicion BETWEEN '$fecha_inicio' AND '$fecha_fin'")
              ->order_by('a.fecha_adquisicion','desc')
              ->group_by('b.id_bien')
              ->where('m.estado_movimiento','CERRADO')
              ->limit($segmento,$porpagina);
     $query=$this->db->get();
     if ($query->num_rows()>0) {
       return $query->result();
     }else{
       return FALSE;
     }
   }

   public function obtenerIngresosFiltroTotal($fecha_inicio,$fecha_fin){
     $this->db->select('count(*) as total')
             ->from("(select max(id_detalle_movimiento) as id_detalle_movimiento,max(id_movimiento) as id_movimiento,
              id_bien from sic_detalle_movimiento group by id_bien order by id_detalle_movimiento) as dm")
             ->join('sic_movimiento m','dm.id_movimiento=m.id_movimiento')
             ->join('sic_bien b','b.id_bien=dm.id_bien')
             ->join('sic_datos_comunes a','a.id_dato_comun=b.id_dato_comun')
              ->join('sic_subcategoria c', 'c.id_subcategoria= a.id_subcategoria')
              ->join('sir_empleado_informacion_laboral d','d.id_empleado = m.id_empleado')
              ->join('sic_marcas e','e.id_marca = a.id_marca')
              ->join('sic_cuenta_contable f','f.id_cuenta_contable = a.id_cuenta_contable')
              ->join('sic_fuentes_fondo g','g.id_fuentes = a.id_fuentes')
              ->join('sic_categoria h','h.id_categoria = c.id_categoria')
              ->join('sir_empleado i','i.id_empleado = m.id_empleado')
              ->join('org_seccion j','j.id_seccion = d.id_seccion')
              ->where("a.fecha_adquisicion BETWEEN '$fecha_inicio' AND '$fecha_fin'")
              ->order_by('a.fecha_adquisicion','desc')
              ->group_by('b.id_bien')
              ->where('m.estado_movimiento','CERRADO');
      $query = $this->db->get();
      return count($query->result());
   }
 }
?>
