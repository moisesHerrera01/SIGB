<?php
  class Factura_model extends CI_Model{

    public $numero_factura;
    public $id_proveedores;
    public $nombre_entrega;
    public $fecha_factura;
    public $fecha_ingreso;
    public $id_fuentes;
    public $numero_compromiso;
    public $orden_compra;
    public $id_seccion;
    public $total;
    public $estado;
    public $hora;
    public $comentario_productos;

    function __construct() {
        parent::__construct();
    }

    public function insertarFactura($data){

        $this->numero_factura = $data['numero_factura'];
        $this->id_proveedores = $data['id_proveedores'];
        $this->nombre_entrega = $data['nombre_entrega'];
        $this->fecha_factura = $data['fecha_factura'];
        $this->fecha_ingreso = $data['fecha_ingreso'];
        $this->id_fuentes = $data['id_fuentes'];
        $this->numero_compromiso = $data['numero_compromiso'];
        $this->orden_compra = $data['orden_compra'];
        $this->id_seccion = $data['id_seccion'];
        $this->total = 'default';
        $this->estado = 'INGRESADA';
        $this->hora=$data['hora'];
        $this->comentario_productos=$data['comentario_productos'];
        $this->db->insert('sic_factura', $this);
        return $this->db->insert_id();
    }

      public function obtenerFacturas(){
        $this->db->order_by("id_factura", "desc");
        $query = $this->db->get('sic_factura');
        if ($query->num_rows() > 0) {
            return  $query->result();
        }
        else {
            return FALSE;
        }
      }

      public function obtenerFactura($id){
          $this->db->where('id_factura',$id);
          $query = $this->db->get('sic_factura');
          if ($query->num_rows() > 0) {
            $nombre;
            foreach ($query->result() as $fact) {
              $nombre = $fact->numero_factura;
            }
            return  $nombre;
          }
          else {
              return FALSE;
          }
      }

      public function obtenerTodaFactura($id){
          $this->db->where('id_factura',$id);
          $query = $this->db->get('sic_factura');
          if ($query->num_rows() > 0) {
            return  $query->result();
          }
          else {
              return FALSE;
          }
      }

    public function buscarFacturas($busca){
      $this->db->like('numero_factura', $busca);
      $this->db->or_like('fecha_ingreso', $busca);
      $query = $this->db->get('sic_factura', 10);
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerTotalFactura($id){
        $this->db->where('id_factura',$id);
        $query = $this->db->get('sic_factura');
        if ($query->num_rows() > 0) {
          $total;
          foreach ($query->result() as $fact) {
            $total = $fact->total;
          }
          return  $total;
        }
        else {
            return FALSE;
        }
    }

    public function actualizarFactura($id, $data){
      $this->db->where('id_factura',$id);
      $this->db->update('sic_factura', $data);
    }

    public function obtenerCorrelativoFuente($id){
      $this->db->where('id_fuentes',$id);
      $this->db->order_by("id_factura", "asc");
      $query = $this->db->get('sic_factura');
      if ($query->num_rows() > 0) {
        $correlativo=0;
        foreach ($query->result() as $fact) {
          if ($fact->estado == 'LIQUIDADA'){
            if($fact->correlativo_fuente_fondo>$correlativo){
              $correlativo=$fact->correlativo_fuente_fondo;
            }
          }
        }
        return  $correlativo+1;
      }
      else {
          return 1;
      }
    }

    public function liquidar($id,$correlativo){
      $dat = array(
        'estado' => 'LIQUIDADA','correlativo_fuente_fondo' => $correlativo,
      );
      $this->db->where('id_factura', $id);
      $this->db->update('sic_factura', $dat);
    }

    public function eliminarFactura($id){
      $this->db->delete('sic_factura', array('id_factura' => $id));
    }

    public function totalFacturas(){
      return $this->db->count_all('sic_factura');
    }

     public function obtenerFacturasLimit($porpagina, $segmento){
       $this->db->order_by("id_factura", "desc");
       $query = $this->db->get('sic_factura', $porpagina, $segmento);
       if ($query->num_rows() > 0) {
           return  $query->result();
       }
       else {
           return FALSE;
       }
     }

     public function obtenerDatosFactura($id){
       $this->db->select('t.nombre_fuente,p.nombre_proveedor,f.nombre_entrega,f.numero_factura,f.fecha_factura,
            f.fecha_ingreso,f.hora,f.correlativo_fuente_fondo,f.comentario_productos');
            $this->db->from('sic_fuentes_fondo t');
            $this->db->join('sic_factura f', 't.id_fuentes = f.id_fuentes');
            $this->db->join('sic_proveedores p', 'p.id_proveedores = f.id_proveedores');
            $this->db->where('f.id_factura',$id);
            $query = $this->db->get();
        if($query->num_rows() > 0 )
        {
            return $query->result();
        }
    }
    //para el correlativo fuente de fondos de Acta de Recepción de mercadería
    public function obtenerTotalFuentes($id_fuente, $fecha_factura){
      $this->db->from('sic_factura')
               ->where('id_fuentes', $id_fuente)
               ->where('fecha_factura <=', $fecha_factura);
      return $this->db->count_all_results();
      $i = 0;
      if ($query->num_rows() > 0) {
        foreach ($query->result() as $fact) {
          $i++;
        }
        return  $i;
      }
      else {
          return FALSE;
      }

  }


    public function retornarEstado($id){
        $this->db->where('id_factura', $id);
        $query = $this->db->get('sic_factura');
        if ($query->num_rows() > 0) {
          $estado;
          foreach ($query->result() as $detalle) {
            $estado = $detalle->estado;
          }
          return  $estado;
        }
        else {
            return FALSE;
        }
    }

    public function obtenerProductosSeccionLimit($fecha_inicio,$fecha_fin,$seccion,$segmento,$porpagina){
      $this->db->order_by("f.id_factura", "asc");
      $this->db->select('sec.nombre_seccion,v.nombre_proveedor,f.numero_factura,f.orden_compra,f.numero_compromiso,
      f.fecha_ingreso,df.cantidad,p.nombre as producto,u.nombre as unidad,o.nombre_fuente,o.id_fuentes,f.fecha_factura,
      dp.id_detalleproducto');
           $this->db->from('sic_detalle_factura df');
           $this->db->join('sic_factura f', 'f.id_factura = df.id_factura');
           $this->db->join('sic_detalle_producto dp', 'dp.id_detalleproducto = df.id_detalleproducto');
           $this->db->join('sic_producto p', 'p.id_producto = dp.id_producto');
           $this->db->join('sic_unidad_medida u', 'p.id_unidad_medida = u.id_unidad_medida');
           $this->db->join('mtps.org_seccion sec', 'sec.id_seccion = f.id_seccion');
           $this->db->join('sic_proveedores v', 'v.id_proveedores=f.id_proveedores');
           $this->db->join('sic_fuentes_fondo o', 'o.id_fuentes=f.id_fuentes');
           $this->db->where('f.estado','LIQUIDADA');
           $this->db->where('f.fecha_ingreso <=',$fecha_fin);
           $this->db->where('f.fecha_ingreso >=',$fecha_inicio);
           if ($seccion!=NULL) {
              $this->db->where('f.id_seccion',$seccion);
           }
           $this->db->limit($segmento,$porpagina);
           $this->db->group_by('df.id_detalle_factura');
           $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }
    public function obtenerProductosSeccionTotal($fecha_inicio,$fecha_fin,$seccion){
      $this->db->order_by("f.id_factura", "asc");
      $this->db->select('count(*) as numero');
           $this->db->from('sic_detalle_factura df');
           $this->db->join('sic_factura f', 'f.id_factura = df.id_factura');
           $this->db->join('sic_detalle_producto dp', 'dp.id_detalleproducto = df.id_detalleproducto');
           $this->db->join('sic_producto p', 'p.id_producto = dp.id_producto');
           $this->db->join('sic_unidad_medida u', 'p.id_unidad_medida = u.id_unidad_medida');
           $this->db->join('mtps.org_seccion sec', 'sec.id_seccion = f.id_seccion');
           $this->db->join('sic_proveedores v', 'v.id_proveedores=f.id_proveedores');
           $this->db->join('sic_fuentes_fondo o', 'o.id_fuentes=f.id_fuentes');
           $this->db->where('f.estado','LIQUIDADA');
           $this->db->where('f.fecha_ingreso <=',$fecha_fin);
           $this->db->where('f.fecha_ingreso >=',$fecha_inicio);
           $this->db->where('f.id_seccion',$seccion);
           //$this->db->group_by('df.id_detalle_factura');
           $query = $this->db->get();
           return $query->row();
    }

    public function obtenerProductosSeccionTodo($fecha_inicio,$fecha_fin,$seccion){
      $this->db->order_by("f.id_factura", "asc");
      $this->db->select('sec.nombre_seccion,v.nombre_proveedor,f.numero_factura,f.orden_compra,f.numero_compromiso,
      f.fecha_ingreso,df.cantidad,p.nombre as producto,u.nombre as unidad,o.nombre_fuente,o.id_fuentes,f.fecha_factura,
      dp.id_detalleproducto');
           $this->db->from('sic_detalle_factura df');
           $this->db->join('sic_factura f', 'f.id_factura = df.id_factura');
           $this->db->join('sic_detalle_producto dp', 'dp.id_detalleproducto = df.id_detalleproducto');
           $this->db->join('sic_producto p', 'p.id_producto = dp.id_producto');
           $this->db->join('sic_unidad_medida u', 'p.id_unidad_medida = u.id_unidad_medida');
           $this->db->join('mtps.org_seccion sec', 'sec.id_seccion = f.id_seccion');
           $this->db->join('sic_proveedores v', 'v.id_proveedores=f.id_proveedores');
           $this->db->join('sic_fuentes_fondo o', 'o.id_fuentes=f.id_fuentes');
           $this->db->where('f.estado','LIQUIDADA');
           $this->db->where('f.fecha_ingreso <',$fecha_fin);
           $this->db->where('f.fecha_ingreso >',$fecha_inicio);
           if ($seccion!=NULL) {
             $this->db->where('f.id_seccion',$seccion);
           }
           $this->db->group_by('df.id_detalle_factura');
           $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function ObtenerPorNumeroFactura($numero){
        $this->db->where('numero_factura',$numero);
        $query = $this->db->get('sic_factura');
        if ($query->num_rows() > 0) {
          return  $query->row('id_factura');
        }
        else {
            return FALSE;
        }
    }

    public function obtenerUltimaFactura() {
      return $this->db->insert_id();
    }

    public function obtenerFacturaKardex($fecha, $detalle_producto, $cantidad, $precio) {
      $this->db->select("a.id_factura, a.numero_compromiso, a.id_seccion")
               ->from("sic_factura a")
               ->join("sic_detalle_factura b", "a.id_factura = b.id_factura")
               ->where("a.fecha_ingreso", $fecha)
               ->where("a.estado", "LIQUIDADA")
               ->where("b.id_detalleproducto", $detalle_producto)
               ->where("b.cantidad", $cantidad)
               ->where("b.precio", $precio)
               ->group_by("a.id_factura");

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
          return  $query->row();
        }
        else {
            return FALSE;
        }
    }

    public function obtenerLiquidadas($anio = 0) {
      $this->db->select("*")
               ->from("sic_factura")
               ->where("estado", "LIQUIDADA");
      if ($anio != 0) {
          $this->db->where('fecha_ingreso > ', $anio.'-01-01');
      }

       $query = $this->db->get();
       if ($query->num_rows() > 0) {
         return  $query->result();
       }
       else {
           return FALSE;
       }
    }
    public function validarMontoTotalFactura($id_factura){
      $this->db->select('f.total as total_factura,df.id_detalle_factura,oc.monto_total_oc')
               ->from('sic_factura f')
               ->join('sic_detalle_factura df','f.id_factura=df.id_factura')
               ->join('sic_orden_compra oc','oc.id_orden_compra=f.orden_compra')
               ->join('sic_solicitud_compra s','s.id_solicitud_compra=oc.id_solicitud_compra')
               ->join('sic_solicitud_disponibilidad sd','sd.id_solicitud_compra=s.id_solicitud_compra')
               ->where('f.id_factura',$id_factura);
      $query=$this->db->get();
      $total_factura=$query->row()->total_factura;
      $total_oc=$query->row()->monto_total_oc;
      $data_factura = array(
        'total' => 0,
      );
      $data_detalle_factura = array(
        'estado_factura_producto' => 'INGRESADO',
      );
      if ($total_factura==$total_oc) {
        return TRUE;
      }else{
        $this->db->where('id_factura',$id_factura);
        $this->db->update('sic_factura', $data_factura);
        foreach ($query->result() as $fact) {
          $this->db->where('id_detalle_factura',$fact->id_detalle_factura);
          $this->db->update('sic_detalle_factura', $data_detalle_factura);
        }
        return FALSE;
      }
    }

    public function ingresoSeccionEspecifico($fecha_inicio,$fecha_fin,$seccion,$porpagina,$segmento)
    {
      $this->db->select("id_especifico,nombre_especifico,max(countfac) as facturas,sum(cant) as cantidad,sum(tot) as total")
               ->from("(select dp.id_especifico,es.nombre_especifico,count(f.id_factura) countfac,
                df.cantidad cant,df.total as tot
                from sic_factura f
                join sic_detalle_factura df on df.id_factura=f.id_factura
                join sic_detalle_producto dp on df.id_detalleproducto = dp.id_detalleproducto
                join sic_especifico es on dp.id_especifico = es.id_especifico
                join mtps.org_seccion sec on f.id_seccion=sec.id_seccion
                where f.estado = 'LIQUIDADA' and f.fecha_ingreso between '$fecha_inicio' and '$fecha_fin' and f.id_seccion = $seccion
                group by df.id_detalleproducto) x")
                ->group_by('id_especifico')
                ->order_by('id_especifico')
                ->limit($porpagina,$segmento);
                $query = $this->db->get();
                if ($query->num_rows() > 0) {
                  return  $query->result();
                }
                else {
                    return FALSE;
                }

    }

    public function totalIngresoSeccionEspecifico($fecha_inicio,$fecha_fin,$seccion)
    {
      $this->db->select('id_especifico,nombre_especifico,max(countfac) as facturas,sum(cant) as cantidad,sum(tot) as total')
               ->from("(select dp.id_especifico,es.nombre_especifico,count(f.id_factura) countfac,
                df.cantidad cant,df.total as tot
                from sic_factura f
                join sic_detalle_factura df on df.id_factura=f.id_factura
                join sic_detalle_producto dp on df.id_detalleproducto = dp.id_detalleproducto
                join sic_especifico es on dp.id_especifico = es.id_especifico
                join mtps.org_seccion sec on f.id_seccion=sec.id_seccion
                where f.estado = 'LIQUIDADA' AND f.fecha_ingreso between '$fecha_inicio' and '$fecha_fin' and f.id_seccion = $seccion
                group by df.id_detalleproducto) x")
                ->group_by('id_especifico')
                ->order_by('id_especifico');
                $query = $this->db->get();
                if ($query->num_rows() > 0) {
                  return  $query->num_rows();
                }
                else {
                    return FALSE;
                }

    }

    public function todosIngresoSeccionEspecifico($fecha_inicio,$fecha_fin,$seccion)
    {
      $this->db->select('id_especifico,nombre_especifico,max(countfac) as facturas,sum(cant) as cantidad,sum(tot) as total')
               ->from("(select dp.id_especifico,es.nombre_especifico,count(f.id_factura) countfac,
                df.cantidad cant,df.total as tot
                from sic_factura f
                join sic_detalle_factura df on df.id_factura=f.id_factura
                join sic_detalle_producto dp on df.id_detalleproducto = dp.id_detalleproducto
                join sic_especifico es on dp.id_especifico = es.id_especifico
                join mtps.org_seccion sec on f.id_seccion=sec.id_seccion
                where f.estado = 'LIQUIDADA' AND f.fecha_ingreso between '$fecha_inicio' and '$fecha_fin' and f.id_seccion = $seccion
                group by df.id_detalleproducto) x")
                ->group_by('id_especifico')
                ->order_by('id_especifico');
                $query = $this->db->get();
                if ($query->num_rows() > 0) {
                  return  $query->result();
                }
                else {
                    return FALSE;
                }

    }

    public function SumaTotalIngresoSeccionEspecifico($fecha_inicio,$fecha_fin,$seccion)
    {
      $this->db->select("sum(total) as tot")
               ->from("(select sum(tot) as total from
                (select dp.id_especifico,es.nombre_especifico,count(f.id_factura) countfac,
                df.cantidad cant,df.total as tot
                from sic_factura f
                join sic_detalle_factura df on df.id_factura=f.id_factura
                join sic_detalle_producto dp on df.id_detalleproducto = dp.id_detalleproducto
                join sic_especifico es on dp.id_especifico = es.id_especifico
                join mtps.org_seccion sec on f.id_seccion=sec.id_seccion
                where f.estado = 'LIQUIDADA' AND f.fecha_ingreso between '$fecha_inicio' and '$fecha_fin' and f.id_seccion = $seccion
                group by df.id_detalleproducto) x group by id_especifico order by id_especifico) y");
                $query = $this->db->get();
                if ($query->num_rows() > 0) {
                  return  $query->row();
                }
                else {
                    return FALSE;
                }

    }

    public function buscaIngresoSeccionEspecifico($fecha_inicio,$fecha_fin,$seccion,$busca)
    {
      $this->db->select('id_especifico,nombre_especifico,max(countfac) as facturas,sum(cant) as cantidad,sum(tot) as total')
               ->from("(select dp.id_especifico,es.nombre_especifico,count(f.id_factura) countfac,
                df.cantidad cant,df.total as tot
                from sic_factura f
                join sic_detalle_factura df on df.id_factura=f.id_factura
                join sic_detalle_producto dp on df.id_detalleproducto = dp.id_detalleproducto
                join sic_especifico es on dp.id_especifico = es.id_especifico
                join mtps.org_seccion sec on f.id_seccion=sec.id_seccion
                where f.estado = 'LIQUIDADA' AND f.fecha_ingreso between '$fecha_inicio' and '$fecha_fin' and f.id_seccion = $seccion
                group by df.id_detalleproducto) x")
                ->like('nombre_especifico',$busca)
                ->group_by('id_especifico');
                $query = $this->db->get();
                if ($query->num_rows() > 0) {
                  return  $query->result();
                }
                else {
                    return FALSE;
                }

    }

    public function obtenerSeccion($id){
        $this->db->where('id_seccion',$id);
        $query = $this->db->get('mtps.org_seccion');
        if ($query->num_rows() > 0) {
          $nombre;
          foreach ($query->result() as $sec) {
            $nombre = $sec->nombre_seccion;
          }
          return  $nombre;
        }
        else {
            return FALSE;
        }
    }
}
?>
