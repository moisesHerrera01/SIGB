<?php
  class Etl_model extends CI_Model{

    function __construct() {
        parent::__construct();
    }

    public function cargarGrupo1(){
      /*Obteniene el nombre de la base de datos en uso (para el SIG)*/
      $this->db->select('DATABASE() as nombre');
      $query=$this->db->get();
      $base=$query->row()->nombre;
      /*trunca los los registros de las tablas de la bd*/
      $this->vaciarBD();
      /*Extrae, transforma y carga los rgistros de las tablas contenidos en la bd transaccional a la bd gerencial
      tabla sic_unidad_medida en el orden padre a hijo*/
      $this->db->query("INSERT INTO $base.sic_unidad_medida (id_unidad_medida,nombre) select id_unidad_medida,
      nombre from mtps.sic_unidad_medida");
      /*tabla sic_producto*/
      $this->db->query("INSERT INTO $base.sic_producto (id_producto,nombre,id_unidad_medida,exento) select id_producto,nombre,
      id_unidad_medida,exento from mtps.sic_producto");
      /*tabla sic_especifico*/
      $this->db->query("INSERT INTO $base.sic_especifico (id_especifico,nombre_especifico) select id_especifico,nombre_especifico
      from mtps.sic_especifico");
      /*tabla sic_detalle_producto*/
      $this->db->query("INSERT INTO $base.sic_detalle_producto (id_detalleproducto,id_producto,id_especifico,numero_producto)
      select id_detalleproducto,id_producto,id_especifico,numero_producto from mtps.sic_detalle_producto");
      /*tabla sic_conteo_fisico*/
      $this->db->query("INSERT INTO $base.sic_conteo_fisico (nombre_conteo,fecha_inicial,fecha_final,descripcion)
      select nombre_conteo,fecha_inicial,fecha_final,descripcion from mtps.sic_conteo_fisico");
      /*tabla sic_detalle_conteo*/
      $this->db->query("INSERT INTO $base.sic_detalle_conteo (nombre_conteo,id_detalleproducto,cantidad)
      select nombre_conteo,id_detalleproducto,cantidad from mtps.sic_detalle_conteo");
      /*tabla sic_fuentes_fondo*/
      $this->db->query("INSERT INTO $base.sic_fuentes_fondo (id_fuentes,nombre_fuente)
      select id_fuentes,nombre_fuente from mtps.sic_fuentes_fondo");
      /*tabla sic_categoria_proveedor*/
      $this->db->query("INSERT INTO $base.sic_categoria_proveedor (id_categoria_proveedor,nombre_categoria)
      select id_categoria_proveedor,nombre_categoria from mtps.sic_categoria_proveedor");
      /*tabla sic_proveedor*/
      $this->db->query("INSERT INTO $base.sic_proveedores (id_proveedores,nombre_proveedor,id_categoria_proveedor)
      select id_proveedores,nombre_proveedor,id_categoria_proveedor from mtps.sic_proveedores");
      /*tabla sic_factura*/
      $this->db->query("INSERT INTO $base.sic_factura (id_factura,numero_factura,id_proveedores,fecha_factura,fecha_ingreso,id_fuentes,
      numero_compromiso,orden_compra,id_seccion,total,estado,hora,correlativo_fuente_fondo)
      select id_factura,numero_factura,id_proveedores,fecha_factura,fecha_ingreso,id_fuentes,numero_compromiso,orden_compra,id_seccion,
      total,estado,hora,correlativo_fuente_fondo from mtps.sic_factura");
      /*tabla sic_detalle_factura*/
      $this->db->query("INSERT INTO $base.sic_detalle_factura (id_detalle_factura,cantidad,precio,id_factura,total,id_detalleproducto,
      estado_factura_producto) select id_detalle_factura,cantidad,precio,id_factura,total,id_detalleproducto,estado_factura_producto
      from mtps.sic_detalle_factura");
      /*tabla sic_kardex*/
      $this->db->query("INSERT INTO $base.sic_kardex (id_kardex,id_detalleproducto,cantidad,precio,movimiento,fecha_ingreso,id_fuentes)
      select id_kardex,id_detalleproducto,cantidad,precio,movimiento,fecha_ingreso,id_fuentes from mtps.sic_kardex");
      /*tabla sic_kardex*/
      $this->db->query("INSERT INTO $base.sic_kardex_saldo (id_kardex_saldo,id_kardex,id_fuentes,existencia,precio_unitario,total)
      select id_kardex_saldo,id_kardex,id_fuentes,existencia,precio_unitario,total from mtps.sic_kardex_saldo");
      /*tabla sic_solicitud*/
      $this->db->query("INSERT INTO $base.sic_solicitud (id_solicitud,fecha_solicitud,id_seccion,numero_solicitud,estado_solicitud,
      fecha_salida,id_usuario,nivel_solicitud,nivel_anterior,id_fuentes) select id_solicitud,fecha_solicitud,id_seccion,numero_solicitud,
      estado_solicitud,fecha_salida,id_usuario,nivel_solicitud,nivel_anterior,id_fuentes from mtps.sic_solicitud");
      /*tabla sic_kardex*/
      $this->db->query("INSERT INTO $base.sic_detalle_solicitud_producto (id_detalle_solicitud_producto,cantidad,precio,total,
      id_detalleproducto,id_solicitud,estado_solicitud_producto,id_fuentes) select id_detalle_solicitud_producto,cantidad,precio,total,
      id_detalleproducto,id_solicitud,estado_solicitud_producto,id_fuentes from mtps.sic_detalle_solicitud_producto");
    }

    public function obtenerCantReg(){
      /*Obteniene el nombre de la base de datos en uso (para el SIG)*/
      $this->db->select('DATABASE() as nombre');
      $query=$this->db->get();
      $base=$query->row()->nombre;
      /*Obtiene nombre y cantidad de registros para las tablas correspondientes a la bd transaccional*/
      $this->db->select('table_name,table_rows')
               ->from('information_schema.tables')
               ->where("table_schema='mtps'")
               ->where("table_name like 'sic%'")
               ->where("table_name NOT IN ('sic_notificacion','sic_rastreabilidad')");
      $query=$this->db->get();
      /*Obtiene nombre y cantidad de registros para las tablas correspondientes a la bd gerencial*/
      $this->db->select('table_name,table_rows as cant_sigb,version as cant_mtps')
               ->from('information_schema.tables')
               ->where("table_schema='$base'")
               ->where("table_name like 'sic%'")
               ->where("table_name NOT IN ('sic_notificacion','sic_rastreabilidad')");
      $query2=$this->db->get();
      /*Se asigna el total de registros de c/tabla de la bd transaccional para ser devuelto en una misma consulta
       junto con el gerencial*/
      foreach ($query2->result() as $sigb) {
        foreach ($query->result() as $mtps) {
          if ($sigb->table_name==$mtps->table_name) {
            $sigb->cant_mtps=$mtps->table_rows;
          }
        }
      }
      if ($query2->num_rows() > 0) {
        return $query2->result();
      } else {
        return FALSE;
      }
    }

    public function vaciarBD(){
      /*Obteniene el nombre de la base de datos en uso (para el SIG)*/
      $this->db->select('DATABASE() as nombre');
      $query=$this->db->get();
      $base=$query->row()->nombre;
      /*Trunca los datos de las tablas comenzando de hijo a padre*/
      $this->db->query("SET FOREIGN_KEY_CHECKS = 0");
      $this->db->truncate($base.'.sic_detalle_solicitud_producto');
      $this->db->truncate($base.'.sic_solicitud');
      $this->db->truncate($base.'.sic_kardex_saldo');
      $this->db->truncate($base.'.sic_kardex');
      $this->db->truncate($base.'.sic_detalle_factura');
      $this->db->truncate($base.'.sic_factura');
      $this->db->truncate($base.'.sic_detalle_conteo');
      $this->db->truncate($base.'.sic_conteo_fisico');
      $this->db->truncate($base.'.sic_detalle_producto');
      $this->db->truncate($base.'.sic_especifico');
      $this->db->truncate($base.'.sic_producto');
      $this->db->truncate($base.'.sic_proveedores');
      $this->db->truncate($base.'.sic_categoria_proveedor');
      $this->db->truncate($base.'.sic_fuentes_fondo');
      $this->db->truncate($base.'.sic_unidad_medida');
      $this->db->query("SET FOREIGN_KEY_CHECKS = 1");
    }
  }
?>
