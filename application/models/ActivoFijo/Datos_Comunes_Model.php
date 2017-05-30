<?php
  class Datos_comunes_model extends CI_Model{

    public $id_subcategoria;
    public $id_tipo_movimiento;
    public $id_marca;
    public $descripcion;
    public $modelo;
    public $color;
    public $id_doc_ampara;
    public $nombre_doc_ampara;
    public $fecha_adquisicion;
    public $precio_unitario;
    public $id_proveedores;
    public $id_fuentes;
    public $garantia_mes;
    public $observacion;
    public $id_cuenta_contable;
    public $codificar;

    function __construct() {
        parent::__construct();
    }

    public function insertarDatosComunes($data){
        $this->id_subcategoria = $data['id_subcategoria'];
        $this->id_tipo_movimiento = $data['id_tipo_movimiento'];
        $this->id_marca = $data['id_marca'];
        $this->descripcion = $data['descripcion'];
        $this->modelo = $data['modelo'];
        $this->color = $data['color'];
        $this->id_doc_ampara = $data['id_doc_ampara'];
        $this->nombre_doc_ampara = $data['nombre_doc_ampara'];
        $this->fecha_adquisicion = $data['fecha_adquisicion'];
        $this->precio_unitario = $data['precio_unitario'];
        $this->id_proveedores = $data['id_proveedores'];
        $this->id_fuentes = $data['id_fuentes'];
        $this->garantia_mes = $data['garantia_mes'];
        $this->observacion = $data['observacion'];
        $this->id_cuenta_contable = $data['id_cuenta_contable'];
        $this->codificar = 'SI';
        $this->db->insert('sic_datos_comunes', $this);
        return $this->db->insert_id();
    }

    public function obtenerDatosComunes(){
       $this->db->select('d.id_dato_comun,d.id_subcategoria,s.nombre_subcategoria,d.id_tipo_movimiento,t.nombre_movimiento,
       d.id_marca,m.nombre_marca,d.descripcion,d.modelo,d.color,d.id_doc_ampara,dc.nombre_doc_ampara,d.nombre_doc_ampara as nombre_doc,
       d.fecha_adquisicion,d.precio_unitario,d.id_proveedores,pv.nombre_proveedor,d.id_fuentes,py.nombre_fuente,d.garantia_mes,
       d.observacion,d.id_cuenta_contable,c.nombre_cuenta,d.codificar')
                ->from('sic_datos_comunes d')
                ->join('sic_subcategoria s', 's.id_subcategoria = d.id_subcategoria')
                ->join('sic_tipo_movimiento t', 't.id_tipo_movimiento = d.id_tipo_movimiento')
                ->join('sic_marcas m', 'm.id_marca = d.id_marca')
                ->join('sic_doc_ampara dc', 'dc.id_doc_ampara = d.id_doc_ampara')
                ->join('sic_proveedores pv', 'pv.id_proveedores = d.id_proveedores')
                ->join('sic_fuentes_fondo py', 'py.id_fuentes = d.id_fuentes')
                ->join('sic_cuenta_contable c', 'c.id_cuenta_contable = d.id_cuenta_contable')
                ->order_by('d.id_dato_comun','desc');
     $query = $this->db->get();
       if($query->num_rows() > 0 )
       {
           return $query->result();
       }
   }

    public function buscarDatosComunes($busca){
       $this->db->select('d.id_dato_comun,d.id_subcategoria,s.nombre_subcategoria,d.id_tipo_movimiento,t.nombre_movimiento,
       d.id_marca,m.nombre_marca,d.descripcion,d.modelo,d.color,d.id_doc_ampara,dc.nombre_doc_ampara,d.nombre_doc_ampara as nombre_doc,
       d.fecha_adquisicion,d.precio_unitario,d.id_proveedores,pv.nombre_proveedor,d.id_fuentes,py.nombre_fuente,d.garantia_mes,
       d.observacion,d.id_cuenta_contable,c.nombre_cuenta,d.codificar,b.codigo_anterior,b.serie')
                ->from('sic_datos_comunes d')
                ->join('sic_bien b','b.id_dato_comun=d.id_dato_comun')
                ->join('sic_subcategoria s', 's.id_subcategoria = d.id_subcategoria')
                ->join('sic_tipo_movimiento t', 't.id_tipo_movimiento = d.id_tipo_movimiento')
                ->join('sic_marcas m', 'm.id_marca = d.id_marca')
                ->join('sic_doc_ampara dc', 'dc.id_doc_ampara = d.id_doc_ampara')
                ->join('sic_proveedores pv', 'pv.id_proveedores = d.id_proveedores')
                ->join('sic_fuentes_fondo py', 'py.id_fuentes = d.id_fuentes')
                ->join('sic_cuenta_contable c', 'c.id_cuenta_contable = d.id_cuenta_contable')
                ->order_by('d.id_dato_comun','desc')
                ->group_by('d.id_dato_comun')
                ->like('modelo', $busca)
                ->or_like('d.id_dato_comun', $busca)
                ->or_like('d.descripcion', $busca)
                ->or_like('b.codigo_anterior',$busca)
                ->or_like('b.codigo',$busca);
     $query = $this->db->get();
       if($query->num_rows() > 0 )
       {
           return $query->result();
       }
   }

    public function actualizarDatosComunes($id, $data){
      $this->db->where('id_dato_comun',$id);
      $this->db->update('sic_datos_comunes', $data);
    }

    public function eliminarDatosComunes($id){
      $this->db->delete('sic_datos_comunes', array('id_dato_comun' => $id));
    }

    public function obtenerDatosComunesLimit($porpagina,$segmento){
       $this->db->select('d.id_dato_comun,d.id_subcategoria,s.nombre_subcategoria,d.id_tipo_movimiento,t.nombre_movimiento,
       d.id_marca,m.nombre_marca,d.descripcion,d.modelo,d.color,d.id_doc_ampara,dc.nombre_doc_ampara,d.nombre_doc_ampara as nombre_doc,
       d.fecha_adquisicion,d.precio_unitario,d.id_proveedores,pv.nombre_proveedor,d.id_fuentes,py.nombre_fuente,d.garantia_mes,
       d.observacion,d.id_cuenta_contable,c.nombre_cuenta,d.codificar,COALESCE(b.codigo_anterior,"N/A") as codigo_anterior,
       COALESCE(b.serie,"N/A") as serie')
                ->from('sic_datos_comunes d');
                $this->db->join('sic_subcategoria s', 's.id_subcategoria = d.id_subcategoria')
                ->join('sic_bien b','b.id_dato_comun=d.id_dato_comun','left')
                ->join('sic_tipo_movimiento t', 't.id_tipo_movimiento = d.id_tipo_movimiento')
                ->join('sic_marcas m', 'm.id_marca = d.id_marca')
                ->join('sic_doc_ampara dc', 'dc.id_doc_ampara = d.id_doc_ampara')
                ->join('sic_proveedores pv', 'pv.id_proveedores = d.id_proveedores')
                ->join('sic_fuentes_fondo py', 'py.id_fuentes = d.id_fuentes')
                ->join('sic_cuenta_contable c', 'c.id_cuenta_contable = d.id_cuenta_contable')
                ->order_by('d.id_dato_comun','desc')
                ->group_by('d.id_dato_comun')
                ->limit($porpagina,$segmento);
     $query = $this->db->get();
       if($query->num_rows() > 0 )
       {
           return $query->result();
       }
   }

   public function totalDatosComunes(){
      $this->db->select('count(d.id_dato_comun) as total')
               ->from('sic_datos_comunes d')
               ->join('sic_subcategoria s', 's.id_subcategoria = d.id_subcategoria')
               ->join('sic_tipo_movimiento t', 't.id_tipo_movimiento = d.id_tipo_movimiento')
               ->join('sic_marcas m', 'm.id_marca = d.id_marca')
               ->join('sic_doc_ampara dc', 'dc.id_doc_ampara = d.id_doc_ampara')
               ->join('sic_proveedores pv', 'pv.id_proveedores = d.id_proveedores')
               ->join('sic_fuentes_fondo py', 'py.id_fuentes = d.id_fuentes')
               ->join('sic_cuenta_contable c', 'c.id_cuenta_contable = d.id_cuenta_contable')
               ->order_by('d.id_dato_comun');
    $query = $this->db->get();
      if($query->num_rows() > 0 )
      {
          return $query->row();
      }
  }

   public function obtenerBienesUsuario($empleado,$porpagina,$segmento){
     $this->db->select('d.descripcion,d.modelo,d.color,d.precio_unitario,b.serie,b.codigo,b.codigo_anterior,ma.nombre_marca')
              ->from("(select max(id_detalle_movimiento) as id_detalle_movimiento,max(id_movimiento) as id_movimiento,
               id_bien from sic_detalle_movimiento group by id_bien order by id_detalle_movimiento) as dm")
              ->join('sic_movimiento m','dm.id_movimiento=m.id_movimiento')
              ->join('sic_bien b','b.id_bien=dm.id_bien')
              ->join('sic_datos_comunes d','d.id_dato_comun=b.id_dato_comun')
              ->join('sic_marcas ma','ma.id_marca=d.id_marca')
              ->where('m.id_empleado',$empleado)
              ->where('m.estado_movimiento','CERRADO')
              ->where('m.id_tipo_movimiento != 12')
              ->limit($porpagina,$segmento);
    $query=$this->db->get();
    if ($query->num_rows()>0) {
      return $query->result();
    }
   }

   public function obtenerBienesUsuarioExcel($empleado){
     $this->db->select('d.descripcion,d.modelo,d.color,d.precio_unitario,b.serie,b.codigo,b.codigo_anterior,ma.nombre_marca,b.id_bien')
              ->from("(select max(id_detalle_movimiento) as id_detalle_movimiento,max(id_movimiento) as id_movimiento,
               id_bien from sic_detalle_movimiento group by id_bien order by id_detalle_movimiento) as dm")
              ->join('sic_movimiento m','dm.id_movimiento=m.id_movimiento')
              ->join('sic_bien b','b.id_bien=dm.id_bien')
              ->join('sic_datos_comunes d','d.id_dato_comun=b.id_dato_comun')
              ->join('sic_marcas ma','ma.id_marca=d.id_marca')
              ->where('m.estado_movimiento','CERRADO')
              ->where('m.id_tipo_movimiento != 12')
              ->where('m.id_empleado',$empleado);
    $query=$this->db->get();
    if ($query->num_rows()>0) {
      return $query->result();
    }
   }

   public function totalBienesUsuario($empleado){
     $this->db->select('count(dm.id_bien) as total')
              ->from("(select max(id_detalle_movimiento) as id_detalle_movimiento,max(id_movimiento) as id_movimiento,
               id_bien from sic_detalle_movimiento) as dm")
              ->join('sic_movimiento m','dm.id_movimiento=m.id_movimiento')
              ->join('sic_bien b','b.id_bien=dm.id_bien')
              ->join('sic_datos_comunes d','d.id_dato_comun=b.id_dato_comun')
              ->join('sic_marcas ma','ma.id_marca=d.id_marca')
              ->where('m.estado_movimiento','CERRADO')
              ->where('m.id_tipo_movimiento != 12')
              ->where('m.id_empleado',$empleado);
    $query=$this->db->get();
    if ($query->num_rows()>0) {
      return $query->row();
    }
  }

   public function buscarEmpleados($busca){
     $this->db->select('e.primer_nombre,e.segundo_nombre,e.primer_apellido,e.segundo_apellido,e.id_empleado')
              ->from('sir_empleado e')
              ->order_by('e.id_empleado asc')
              ->like('e.primer_nombre',$busca)
              ->or_like('e.id_empleado',$busca);
     $query = $this->db->get();
     if ($query->num_rows() > 0) {
         return  $query->result();
     }
     else {
         return FALSE;
     }
   }

   public function buscarEmpleado($emp){
     $this->db->select('e.primer_nombre,e.segundo_nombre,e.primer_apellido,e.segundo_apellido,e.id_empleado')
              ->from('sir_empleado e')
              ->order_by('e.id_empleado asc')
              ->where('e.id_empleado',$emp);
     $query = $this->db->get();
     if ($query->num_rows() > 0) {
         return  $query->result();
     }
     else {
         return FALSE;
     }
   }

   public function obtenerEmpleados(){
     $this->db->select('e.primer_nombre,e.segundo_nombre,e.primer_apellido,e.segundo_apellido,e.id_empleado')
              ->from('sir_empleado e')
              ->order_by('e.id_empleado asc');
     $query = $this->db->get();
     if ($query->num_rows() > 0) {
         return  $query->result();
     }
     else {
         return FALSE;
     }
   }

    public function obtenerBienesOficina($id_oficina,$porpagina = -1, $segmento = -1){
      $this->db->select('d.descripcion,d.modelo,d.color,d.precio_unitario,b.serie,b.codigo,b.codigo_anterior,ma.nombre_marca,m.id_empleado,
      d.codificar, d.precio_unitario, of.nombre_oficina, b.id_bien')
               ->from("(select max(id_detalle_movimiento) as id_detalle_movimiento,max(id_movimiento) as id_movimiento,
                id_bien from sic_detalle_movimiento group by id_bien order by id_detalle_movimiento) as dm")
               ->join('sic_movimiento m','dm.id_movimiento=m.id_movimiento')
               ->join('org_oficina of','of.id_oficina=m.id_oficina_recibe')
               ->join('sic_bien b','b.id_bien=dm.id_bien')
               ->join('sic_datos_comunes d','d.id_dato_comun=b.id_dato_comun')
               ->join('sic_marcas ma','ma.id_marca=d.id_marca')
               ->where('m.estado_movimiento','CERRADO')
               ->where('m.id_tipo_movimiento != 12')
               ->where('m.id_oficina_recibe',$id_oficina);

               if ($porpagina != -1 && $segmento != -1) {
                 $this->db->limit($porpagina,$segmento);
               }
               $query = $this->db->get();
               if ($query->num_rows() > 0) {
                 return $query->result();
               }
    }


        public function totalBienesOficina($id_oficina){
          $this->db->select('count(b.id_bien) as total')
                   ->from("(select max(id_detalle_movimiento) as id_detalle_movimiento,max(id_movimiento) as id_movimiento,
                    id_bien from sic_detalle_movimiento group by id_bien order by id_detalle_movimiento) as dm")
                   ->join('sic_movimiento m','dm.id_movimiento=m.id_movimiento')
                   ->join('org_oficina of','of.id_oficina=m.id_oficina_recibe')
                   ->join('sic_bien b','b.id_bien=dm.id_bien')
                   ->join('sic_datos_comunes d','d.id_dato_comun=b.id_dato_comun')
                   ->join('sic_marcas ma','ma.id_marca=d.id_marca')
                   ->where('m.estado_movimiento','CERRADO')
                   ->where('m.id_tipo_movimiento != 12')
                   ->where('m.id_oficina_recibe',$id_oficina);
                   $query = $this->db->get();
                     return $query->row();
        }

    public function obtenerBienesUnidad($seccion,$porpagina = -1, $segmento = -1){
      $this->db->select('d.descripcion,d.modelo,d.color,d.precio_unitario,b.serie,b.codigo,b.codigo_anterior,ma.nombre_marca,m.id_empleado,
      d.codificar, d.precio_unitario, of.nombre_oficina, b.id_bien')
               ->from("(select max(id_detalle_movimiento) as id_detalle_movimiento,max(id_movimiento) as id_movimiento,
                id_bien from sic_detalle_movimiento group by id_bien order by id_detalle_movimiento) as dm")
               ->join('sic_movimiento m','dm.id_movimiento=m.id_movimiento')
               ->join('org_oficina of','of.id_oficina=m.id_oficina_recibe')
               ->join('org_seccion_has_almacen sa','sa.id_seccion_has_almacen=of.id_seccion_has_almacen')
               ->join('sic_bien b','b.id_bien=dm.id_bien')
               ->join('sic_datos_comunes d','d.id_dato_comun=b.id_dato_comun')
               ->join('sic_marcas ma','ma.id_marca=d.id_marca')
               ->where('m.estado_movimiento','CERRADO')
               ->where('m.id_tipo_movimiento != 12')
               ->where('sa.id_seccion',$seccion);
               if ($porpagina != -1 && $segmento != -1) {
                 $this->db->limit($porpagina,$segmento);
               }
               $query = $this->db->get();
               if ($query->num_rows() > 0) {
                 return $query->result();
               }
    }

    public function totalBienesUnidad($seccion){
      $this->db->select('count(b.id_bien) as total')
               ->from("(select max(id_detalle_movimiento) as id_detalle_movimiento,max(id_movimiento) as id_movimiento,
                id_bien from sic_detalle_movimiento group by id_bien order by id_detalle_movimiento) as dm")
               ->join('sic_movimiento m','dm.id_movimiento=m.id_movimiento')
               ->join('org_oficina of','of.id_oficina=m.id_oficina_recibe')
               ->join('org_seccion_has_almacen sa','sa.id_seccion_has_almacen=of.id_seccion_has_almacen')
               ->join('sic_bien b','b.id_bien=dm.id_bien')
               ->join('sic_datos_comunes d','d.id_dato_comun=b.id_dato_comun')
               ->join('sic_marcas ma','ma.id_marca=d.id_marca')
               ->where('m.estado_movimiento','CERRADO')
               ->where('m.id_tipo_movimiento != 12')
               ->where('sa.id_seccion',$seccion);
               $query = $this->db->get();
                 return $query->row();
    }

    public function obtenerBienesProyecto($proyecto,$porpagina,$segmento){
      $this->db->select('c.nombre_categoria,s.nombre_subcategoria,d.descripcion,d.modelo,d.id_doc_ampara,
                        d.fecha_adquisicion,d.precio_unitario,b.codigo,b.codigo_anterior,o.nombre_oficina,
                        m.id_empleado,CONCAT_WS(" ",primer_nombre,segundo_nombre,primer_apellido,segundo_apellido) as nombre_empleado')
               ->from("(select max(id_detalle_movimiento) as id_detalle_movimiento,max(id_movimiento) as id_movimiento,
                id_bien from sic_detalle_movimiento group by id_bien order by id_detalle_movimiento) as dm")
               ->join('sic_movimiento m','dm.id_movimiento=m.id_movimiento')
               ->join('sir_empleado e','e.id_empleado=m.id_empleado')
               ->join('org_oficina o','o.id_oficina=m.id_oficina_recibe')
               ->join('org_seccion_has_almacen sa','sa.id_seccion_has_almacen=o.id_seccion_has_almacen')
               ->join('sic_bien b','b.id_bien=dm.id_bien')
               ->join('sic_datos_comunes d','d.id_dato_comun=b.id_dato_comun')
               ->join('sic_fuentes_fondo f','f.id_fuentes=d.id_fuentes')
               ->join('sic_subcategoria s','s.id_subcategoria=d.id_subcategoria')
               ->join('sic_categoria c', 'c.id_categoria=s.id_categoria')
               ->join('sic_marcas ma','ma.id_marca=d.id_marca')
               ->where('d.id_fuentes',$proyecto)
               ->where('m.estado_movimiento','CERRADO')
               ->where('m.id_tipo_movimiento != 12')
               ->limit($porpagina,$segmento);
     $query=$this->db->get();
     if ($query->num_rows()>0) {
       return $query->result();
     }
    }

    public function totalBienesProyecto($proyecto){
      $this->db->select('count(b.id_bien) as total')
               ->from("(select max(id_detalle_movimiento) as id_detalle_movimiento,max(id_movimiento) as id_movimiento,
                id_bien from sic_detalle_movimiento group by id_bien order by id_detalle_movimiento) as dm")
               ->join('sic_movimiento m','dm.id_movimiento=m.id_movimiento')
               ->join('sir_empleado e','e.id_empleado=m.id_empleado')
               ->join('org_oficina o','o.id_oficina=m.id_oficina_recibe')
               ->join('org_seccion_has_almacen sa','sa.id_seccion_has_almacen=o.id_seccion_has_almacen')
               ->join('sic_bien b','b.id_bien=dm.id_bien')
               ->join('sic_datos_comunes d','d.id_dato_comun=b.id_dato_comun')
               ->join('sic_fuentes_fondo f','f.id_fuentes=d.id_fuentes')
               ->join('sic_subcategoria s','s.id_subcategoria=d.id_subcategoria')
               ->join('sic_categoria c', 'c.id_categoria=s.id_categoria')
               ->join('sic_marcas ma','ma.id_marca=d.id_marca')
               ->where('m.estado_movimiento','CERRADO')
               ->where('m.id_tipo_movimiento != 12')
               ->where('d.id_fuentes',$proyecto);
     $query=$this->db->get();
     if ($query->num_rows()>0) {
       return $query->result();
     }
    }

    public function buscarProyecto($pro){
      $this->db->select('f.nombre_fuente')
               ->from('sic_fuentes_fondo f')
               ->where('f.id_fuentes',$pro);
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function buscarProyectos($busca){
      $this->db->select('f.id_fuentes,f.nombre_fuente')
               ->from('sic_fuentes_fondo f')
               ->order_by('f.id_fuentes asc')
               ->like('f.nombre_fuente',$busca)
               ->or_like('f.id_fuentes',$busca);
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerProyectos(){
      $this->db->select('f.id_fuentes,f.nombre_fuente')
               ->from('sic_fuentes_fondo f')
               ->order_by('f.id_fuentes asc');
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerBienesProyectoExcel($proyecto){
      $this->db->select('c.nombre_categoria,s.nombre_subcategoria,d.descripcion,d.modelo,d.id_doc_ampara,
                        d.fecha_adquisicion,d.precio_unitario,b.codigo,b.codigo_anterior,o.nombre_oficina,
                        m.id_empleado,CONCAT_WS(" ",primer_nombre,segundo_nombre,primer_apellido,segundo_apellido) as nombre_empleado')
               ->from("(select max(id_detalle_movimiento) as id_detalle_movimiento,max(id_movimiento) as id_movimiento,
                id_bien from sic_detalle_movimiento group by id_bien order by id_detalle_movimiento) as dm")
               ->join('sic_movimiento m','dm.id_movimiento=m.id_movimiento')
               ->join('sir_empleado e','e.id_empleado=m.id_empleado')
               ->join('org_oficina o','o.id_oficina=m.id_oficina_recibe')
               ->join('org_seccion_has_almacen sa','sa.id_seccion_has_almacen=o.id_seccion_has_almacen')
               ->join('sic_bien b','b.id_bien=dm.id_bien')
               ->join('sic_datos_comunes d','d.id_dato_comun=b.id_dato_comun')
               ->join('sic_fuentes_fondo f','f.id_fuentes=d.id_fuentes')
               ->join('sic_subcategoria s','s.id_subcategoria=d.id_subcategoria')
               ->join('sic_categoria c', 'c.id_categoria=s.id_categoria')
               ->join('sic_marcas ma','ma.id_marca=d.id_marca')
               ->where('m.estado_movimiento','CERRADO')
               ->where('m.id_tipo_movimiento != 12')
               ->where('d.id_fuentes',$proyecto);
     $query=$this->db->get();
     if ($query->num_rows()>0) {
       return $query->result();
     }
    }

    public function obtenerBien($id){
       $this->db->select('d.id_dato_comun,d.id_subcategoria,s.nombre_subcategoria,d.id_tipo_movimiento,t.nombre_movimiento,
       d.id_marca,m.nombre_marca,d.descripcion,d.modelo,d.color,d.id_doc_ampara,dc.nombre_doc_ampara,d.nombre_doc_ampara as numero_doc,
       d.fecha_adquisicion,d.precio_unitario,d.id_proveedores,pv.nombre_proveedor,d.id_fuentes,py.nombre_fuente,d.garantia_mes,
       d.observacion,d.id_cuenta_contable,c.nombre_cuenta,d.codificar,cat.nombre_categoria,b.id_bien,b.codigo,b.serie,b.numero_motor,
       b.numero_placa,b.matricula,cb.nombre_condicion_bien,b.codigo,b.codigo_anterior,of.nombre_oficina,emp.primer_nombre,emp.segundo_nombre,
       emp.primer_apellido,emp.segundo_apellido')
       ->from("(select max(id_detalle_movimiento) as id_detalle_movimiento,max(id_movimiento) as id_movimiento,
                 id_bien from sic_detalle_movimiento group by id_bien order by id_detalle_movimiento) as dm")
                ->join('sic_movimiento mov','dm.id_movimiento=mov.id_movimiento')
                ->join('sic_bien b','b.id_bien=dm.id_bien')
                ->join('sic_datos_comunes d','d.id_dato_comun=b.id_dato_comun')
                ->join('sic_condicion_bien cb','cb.id_condicion_bien=b.id_condicion_bien')
                ->join('org_oficina of','of.id_oficina=mov.id_oficina_recibe')
                ->join('sir_empleado emp','emp.id_empleado=mov.id_empleado')
                ->join('sic_subcategoria s', 's.id_subcategoria = d.id_subcategoria')
                ->join('sic_categoria cat','cat.id_categoria=s.id_categoria')
                ->join('sic_tipo_movimiento t', 't.id_tipo_movimiento = mov.id_tipo_movimiento')
                ->join('sic_marcas m', 'm.id_marca = d.id_marca')
                ->join('sic_doc_ampara dc', 'dc.id_doc_ampara = d.id_doc_ampara')
                ->join('sic_proveedores pv', 'pv.id_proveedores = d.id_proveedores')
                ->join('sic_fuentes_fondo py', 'py.id_fuentes = d.id_fuentes')
                ->join('sic_cuenta_contable c', 'c.id_cuenta_contable = d.id_cuenta_contable')
                ->order_by('b.id_bien')
                ->where('b.id_bien',$id)
                ->where('mov.estado_movimiento','CERRADO')
                ->where('mov.id_tipo_movimiento != 12');
     $query = $this->db->get();
       if($query->num_rows() > 0 ){
           return $query->row();
       }else{
         return FALSE;
       }
   }

   public function obtenerBienesAutocomplete(){
      $this->db->select('d.id_dato_comun,d.id_subcategoria,s.nombre_subcategoria,d.id_tipo_movimiento,t.nombre_movimiento,
      d.id_marca,m.nombre_marca,d.descripcion,d.modelo,d.color,d.id_doc_ampara,dc.nombre_doc_ampara,d.nombre_doc_ampara as nombre_doc,
      d.fecha_adquisicion,d.precio_unitario,d.id_proveedores,pv.nombre_proveedor,d.id_fuentes,py.nombre_fuente,d.garantia_mes,
      d.observacion,d.id_cuenta_contable,c.nombre_cuenta,d.codificar,cat.nombre_categoria,b.id_bien,b.codigo,b.serie')
               ->from('sic_datos_comunes d')
               ->join('sic_bien b','b.id_dato_comun=d.id_dato_comun')
               ->join('sic_subcategoria s', 's.id_subcategoria = d.id_subcategoria')
               ->join('sic_categoria cat','cat.id_categoria=s.id_categoria')
               ->join('sic_tipo_movimiento t', 't.id_tipo_movimiento = d.id_tipo_movimiento')
               ->join('sic_marcas m', 'm.id_marca = d.id_marca')
               ->join('sic_doc_ampara dc', 'dc.id_doc_ampara = d.id_doc_ampara')
               ->join('sic_proveedores pv', 'pv.id_proveedores = d.id_proveedores')
               ->join('sic_fuentes_fondo py', 'py.id_fuentes = d.id_fuentes')
               ->join('sic_cuenta_contable c', 'c.id_cuenta_contable = d.id_cuenta_contable');
    $query = $this->db->get();
      if($query->num_rows() > 0 ){
          return $query->result();
      }else{
        return FALSE;
      }
  }

  public function buscarBienesAutocomplete($buscar){
     $this->db->select('d.id_dato_comun,d.id_subcategoria,s.nombre_subcategoria,d.id_tipo_movimiento,t.nombre_movimiento,
     d.id_marca,m.nombre_marca,d.descripcion,d.modelo,d.color,d.id_doc_ampara,dc.nombre_doc_ampara,d.nombre_doc_ampara as nombre_doc,
     d.fecha_adquisicion,d.precio_unitario,d.id_proveedores,pv.nombre_proveedor,py.nombre_fuente,d.garantia_mes,
     d.observacion,d.id_cuenta_contable,c.nombre_cuenta,d.codificar,cat.nombre_categoria,b.id_bien,b.codigo,b.serie')
              ->from('sic_datos_comunes d')
              ->join('sic_bien b','b.id_dato_comun=d.id_dato_comun')
              ->join('sic_subcategoria s', 's.id_subcategoria = d.id_subcategoria')
              ->join('sic_categoria cat','cat.id_categoria=s.id_categoria')
              ->join('sic_tipo_movimiento t', 't.id_tipo_movimiento = d.id_tipo_movimiento')
              ->join('sic_marcas m', 'm.id_marca = d.id_marca')
              ->join('sic_doc_ampara dc', 'dc.id_doc_ampara = d.id_doc_ampara')
              ->join('sic_proveedores pv', 'pv.id_proveedores = d.id_proveedores')
              ->join('sic_fuentes_fondo py', 'py.id_fuentes = d.id_fuentes')
              ->join('sic_cuenta_contable c', 'c.id_cuenta_contable = d.id_cuenta_contable')
              ->order_by('b.id_bien')
              ->like('b.serie',$buscar)
              ->or_like('b.codigo',$buscar);
     $query = $this->db->get();
       if($query->num_rows() > 0 ){
           return $query->result();
       }else{
         return FALSE;
       }
   }

     public function buscarPorCualquierCampo($criterio,$porpagina,$segmento){
        $this->db->select('d.id_dato_comun,d.id_subcategoria,s.nombre_subcategoria,d.id_tipo_movimiento,t.nombre_movimiento,
        d.id_marca,m.nombre_marca,d.descripcion,d.modelo,d.color,d.id_doc_ampara,dc.nombre_doc_ampara,d.nombre_doc_ampara as numero_doc,
        d.fecha_adquisicion,d.precio_unitario,d.id_proveedores,pv.nombre_proveedor,d.id_fuentes,py.nombre_fuente,d.garantia_mes,
        d.observacion,d.id_cuenta_contable,c.nombre_cuenta,d.codificar,cat.nombre_categoria,b.id_bien,b.codigo,b.serie,b.numero_motor,
        b.numero_placa,b.matricula,cb.nombre_condicion_bien,b.codigo,b.codigo_anterior,of.nombre_oficina,emp.primer_nombre,emp.segundo_nombre,
        emp.primer_apellido,emp.segundo_apellido,cat.numero_categoria,m.id_marca,c.id_cuenta_contable')
                 ->from("(select max(id_detalle_movimiento) as id_detalle_movimiento,max(id_movimiento) as id_movimiento,
                  id_bien from sic_detalle_movimiento group by id_bien order by id_detalle_movimiento) as dm")
                 ->join('sic_movimiento mov','dm.id_movimiento=mov.id_movimiento')
                 ->join('sic_bien b','b.id_bien=dm.id_bien')
                 ->join('sic_datos_comunes d','d.id_dato_comun=b.id_dato_comun')
                 ->join('sic_condicion_bien cb','cb.id_condicion_bien=b.id_condicion_bien')
                 ->join('org_oficina of','of.id_oficina=mov.id_oficina_recibe')
                 ->join('sir_empleado emp','emp.id_empleado=mov.id_empleado')
                 ->join('sic_subcategoria s', 's.id_subcategoria = d.id_subcategoria')
                 ->join('sic_categoria cat','cat.id_categoria=s.id_categoria')
                 ->join('sic_tipo_movimiento t', 't.id_tipo_movimiento = mov.id_tipo_movimiento')
                 ->join('sic_marcas m', 'm.id_marca = d.id_marca')
                 ->join('sic_doc_ampara dc', 'dc.id_doc_ampara = d.id_doc_ampara')
                 ->join('sic_proveedores pv', 'pv.id_proveedores = d.id_proveedores')
                 ->join('sic_fuentes_fondo py', 'py.id_fuentes = d.id_fuentes')
                 ->join('sic_cuenta_contable c', 'c.id_cuenta_contable = d.id_cuenta_contable')
                 ->order_by('b.id_bien');
                 if($criterio=='ssssxxxx'){
                 $this->db->where('b.id_bien>0');
               }else{
                 $this->db->like('b.id_bien',$criterio)
                 ->or_like('d.descripcion',$criterio)
                 ->or_like('m.nombre_marca',$criterio)
                 ->or_like('m.id_marca',$criterio)
                 ->or_like('d.modelo',$criterio)
                 ->or_like('b.serie',$criterio)
                 ->or_like('b.numero_motor',$criterio)
                 ->or_like('b.numero_placa',$criterio)
                 ->or_like('b.matricula',$criterio)
                 ->or_like('d.color',$criterio)
                 ->or_like('d.id_cuenta_contable',$criterio)
                 ->or_like('cat.numero_categoria',$criterio)
                 ->or_like('b.codigo_anterior',$criterio)
                 ->or_like('b.codigo',$criterio);
               }
                 $this->db->limit($porpagina,$segmento)
                 ->where('mov.estado_movimiento','CERRADO');
      $query = $this->db->get();
        if($query->num_rows() > 0 ){
            return $query->result();
        }else{
          return FALSE;
        }
    }

    public function totalPorCualquierCampo($criterio){
       $this->db->select('count(b.id_bien) as total')
               ->from("(select max(id_detalle_movimiento) as id_detalle_movimiento,max(id_movimiento) as id_movimiento,
                id_bien from sic_detalle_movimiento group by id_bien order by id_detalle_movimiento) as dm")
               ->join('sic_movimiento mov','dm.id_movimiento=mov.id_movimiento')
               ->join('sic_bien b','b.id_bien=dm.id_bien')
               ->join('sic_datos_comunes d','d.id_dato_comun=b.id_dato_comun')
                ->join('sic_condicion_bien cb','cb.id_condicion_bien=b.id_condicion_bien')
                ->join('org_oficina of','of.id_oficina=mov.id_oficina_recibe')
                ->join('sir_empleado emp','emp.id_empleado=mov.id_empleado')
                ->join('sic_subcategoria s', 's.id_subcategoria = d.id_subcategoria')
                ->join('sic_categoria cat','cat.id_categoria=s.id_categoria')
                ->join('sic_tipo_movimiento t', 't.id_tipo_movimiento = mov.id_tipo_movimiento')
                ->join('sic_marcas m', 'm.id_marca = d.id_marca')
                ->join('sic_doc_ampara dc', 'dc.id_doc_ampara = d.id_doc_ampara')
                ->join('sic_proveedores pv', 'pv.id_proveedores = d.id_proveedores')
                ->join('sic_fuentes_fondo py', 'py.id_fuentes = d.id_fuentes')
                ->join('sic_cuenta_contable c', 'c.id_cuenta_contable = d.id_cuenta_contable')
                ->order_by('b.id_bien')
                ->like('b.id_bien',$criterio)
                ->or_like('d.descripcion',$criterio)
                ->or_like('m.nombre_marca',$criterio)
                ->or_like('m.id_marca',$criterio)
                ->or_like('d.modelo',$criterio)
                ->or_like('b.serie',$criterio)
                ->or_like('b.numero_motor',$criterio)
                ->or_like('b.numero_placa',$criterio)
                ->or_like('b.matricula',$criterio)
                ->or_like('d.color',$criterio)
                ->or_like('d.id_cuenta_contable',$criterio)
                ->or_like('cat.numero_categoria',$criterio)
                ->or_like('b.codigo_anterior',$criterio)
                ->or_like('b.codigo',$criterio)
                ->where('mov.estado_movimiento','CERRADO');
     $query = $this->db->get();
       if($query->num_rows() > 0 ){
           return $query->row();
       }else{
         return FALSE;
       }
   }

   public function buscarPorCualquierCampoAutocomplete($criterio){
      $this->db->select('d.id_dato_comun,d.id_subcategoria,s.nombre_subcategoria,d.id_tipo_movimiento,t.nombre_movimiento,
      d.id_marca,m.nombre_marca,d.descripcion,d.modelo,d.color,d.id_doc_ampara,dc.nombre_doc_ampara,d.nombre_doc_ampara as numero_doc,
      d.fecha_adquisicion,d.precio_unitario,d.id_proveedores,pv.nombre_proveedor,d.id_fuentes,py.nombre_fuente,d.garantia_mes,
      d.observacion,d.id_cuenta_contable,c.nombre_cuenta,d.codificar,cat.nombre_categoria,b.id_bien,b.codigo,b.serie,b.numero_motor,
      b.numero_placa,b.matricula,cb.nombre_condicion_bien,b.codigo,b.codigo_anterior,of.nombre_oficina,emp.primer_nombre,emp.segundo_nombre,
      emp.primer_apellido,emp.segundo_apellido,cat.numero_categoria,m.id_marca,c.id_cuenta_contable')
               ->from("(select max(id_detalle_movimiento) as id_detalle_movimiento,max(id_movimiento) as id_movimiento,
               id_bien from sic_detalle_movimiento group by id_bien order by id_detalle_movimiento) as dm")
               ->join('sic_movimiento mov','dm.id_movimiento=mov.id_movimiento')
               ->join('sic_bien b','b.id_bien=dm.id_bien')
               ->join('sic_datos_comunes d','d.id_dato_comun=b.id_dato_comun')
               ->join('sic_condicion_bien cb','cb.id_condicion_bien=b.id_condicion_bien')
               ->join('org_oficina of','of.id_oficina=mov.id_oficina_recibe')
               ->join('sir_empleado emp','emp.id_empleado=mov.id_empleado')
               ->join('sic_subcategoria s', 's.id_subcategoria = d.id_subcategoria')
               ->join('sic_categoria cat','cat.id_categoria=s.id_categoria')
               ->join('sic_tipo_movimiento t', 't.id_tipo_movimiento = mov.id_tipo_movimiento')
               ->join('sic_marcas m', 'm.id_marca = d.id_marca')
               ->join('sic_doc_ampara dc', 'dc.id_doc_ampara = d.id_doc_ampara')
               ->join('sic_proveedores pv', 'pv.id_proveedores = d.id_proveedores')
               ->join('sic_fuentes_fondo py', 'py.id_fuentes = d.id_fuentes')
               ->join('sic_cuenta_contable c', 'c.id_cuenta_contable = d.id_cuenta_contable')
               ->order_by('b.id_bien')
               ->where('mov.estado_movimiento','CERRADO');;
               if($criterio=='ssssxxxx'){
               $this->db->where('b.id_bien>0');
             }else{
               $this->db->like('b.id_bien',$criterio)
               ->or_like('d.descripcion',$criterio)
               ->or_like('m.nombre_marca',$criterio)
               ->or_like('m.id_marca',$criterio)
               ->or_like('d.modelo',$criterio)
               ->or_like('b.serie',$criterio)
               ->or_like('b.numero_motor',$criterio)
               ->or_like('b.numero_placa',$criterio)
               ->or_like('b.matricula',$criterio)
               ->or_like('d.color',$criterio)
               ->or_like('d.id_cuenta_contable',$criterio)
               ->or_like('cat.numero_categoria',$criterio)
               ->or_like('b.codigo_anterior',$criterio)
               ->or_like('b.codigo',$criterio);
             }
    $query = $this->db->get();
      if($query->num_rows() > 0 ){
          return $query->result();
      }else{
        return FALSE;
      }
  }
 public function obtenerPorCualquierCampoTotal(){
    $this->db->select('count(b.id_bien) as total')
            ->from("(select max(id_detalle_movimiento) as id_detalle_movimiento,max(id_movimiento) as id_movimiento,
             id_bien from sic_detalle_movimiento group by id_bien order by id_detalle_movimiento) as dm")
            ->join('sic_movimiento mov','dm.id_movimiento=mov.id_movimiento')
            ->join('sic_bien b','b.id_bien=dm.id_bien')
            ->join('sic_datos_comunes d','d.id_dato_comun=b.id_dato_comun')
             ->join('sic_condicion_bien cb','cb.id_condicion_bien=b.id_condicion_bien')
             ->join('org_oficina of','of.id_oficina=mov.id_oficina_recibe')
             ->join('sir_empleado emp','emp.id_empleado=mov.id_empleado')
             ->join('sic_subcategoria s', 's.id_subcategoria = d.id_subcategoria')
             ->join('sic_categoria cat','cat.id_categoria=s.id_categoria')
             ->join('sic_tipo_movimiento t', 't.id_tipo_movimiento = mov.id_tipo_movimiento')
             ->join('sic_marcas m', 'm.id_marca = d.id_marca')
             ->join('sic_doc_ampara dc', 'dc.id_doc_ampara = d.id_doc_ampara')
             ->join('sic_proveedores pv', 'pv.id_proveedores = d.id_proveedores')
             ->join('sic_fuentes_fondo py', 'py.id_fuentes = d.id_fuentes')
             ->join('sic_cuenta_contable c', 'c.id_cuenta_contable = d.id_cuenta_contable')
             ->order_by('b.id_bien')
             ->where('mov.estado_movimiento','CERRADO');;
  $query = $this->db->get();
    if($query->num_rows() > 0 ){
        return $query->row();
    }else{
      return FALSE;
    }
}
public function contieneBien($id){
  $this->db->select('count(id_dato_comun) as asociados')
           ->from('sic_bien')
           ->where('id_dato_comun',$id);
  $query=$this->db->get();
  if ($query->num_rows()>0) {
    return $query->row();
  }else {
    return FALSE;
  }
}
  }

?>
