<?php
  class Producto extends CI_Model{

    public $nombre;
    public $id_unidad_medida;
    public $descripcion;
    public $estado;
    public $fecha_caducidad;
    public $stock_minimo;

    function __construct() {
        parent::__construct();

    }

    public function insertarProducto($data){

        $this->nombre = $data['nombre'];
        $this->id_unidad_medida = $data['id_unidad_medida'];
        $this->descripcion = $data['descripcion'];
        $this->estado = $data['estado'];
        $this->fecha_caducidad = $data['fecha_caducidad'];
        $this->stock_minimo = $data['stock_minimo'];

        $this->db->insert('sic_producto', $this);
        return $this->db->insert_id();
    }

    public function obtenerProductos(){
      $this->db->select("a.id_producto, a.nombre, a.id_unidad_medida, u.nombre nombre_unidad, a.descripcion, a.estado, a.fecha_caducidad, a.stock_minimo, a.exento");
      $this->db->order_by("a.id_producto", "asc");
      $this->db->join('sic_unidad_medida u', 'a.id_unidad_medida = u.id_unidad_medida');
      $query = $this->db->get('sic_producto a');
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerProducto($id){
        $this->db->where('id_producto',$id);
        $query = $this->db->get('sic_producto');
        if ($query->num_rows() > 0) {
          $nombre;
          foreach ($query->result() as $producto) {
            $nombre = $producto->nombre;
          }
          return  $nombre;
        }
        else {
            return FALSE;
        }
    }
    public function obtenerIdUnidad($id){
        $this->db->where('id_producto',$id);
        $query = $this->db->get('sic_producto');
        if ($query->num_rows() > 0) {
          $uni;
          foreach ($query->result() as $producto) {
            $uni = $producto->id_unidad_medida;
          }
          return  $uni;
        }
        else {
            return FALSE;
        }
    }


    public function buscarProductos($busca){
      $this->db->select("a.id_producto, a.nombre, a.id_unidad_medida, u.nombre nombre_unidad, a.descripcion, a.estado, a.fecha_caducidad, a.stock_minimo, a.exento");
      $this->db->like('a.nombre', $busca);
      $this->db->or_like('a.descripcion', $busca);
      $this->db->or_like('a.estado', $busca);
      $this->db->join('sic_unidad_medida u', 'a.id_unidad_medida = u.id_unidad_medida');
      $query = $this->db->get('sic_producto a', 10);
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function actualizarProducto($id, $data){
      $this->db->where('id_producto',$id);
      $this->db->update('sic_producto', $data);
    }

    public function eliminarProducto($id){
      $this->db->delete('sic_producto', array('id_producto' => $id));
    }

    function totalProdutos(){
      return $this->db->count_all('sic_producto');
    }

    public function obtenerProdutosLimit($porpagina, $segmento){
      $this->db->order_by("id_producto", "asc");
      $query = $this->db->get('sic_producto', $porpagina, $segmento);
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerProductosExistencia($mov){
      $this->db->order_by("id_producto", "asc");
      $this->db->group_by("id_producto", "asc");
      $this->db->select('p.nombre,p.id_producto,sum(k.cantidad) as existencia, e.id_especifico');
           $this->db->from('sic_producto p');
           $this->db->join('sic_detalle_producto d', 'p.id_producto = d.id_producto');
           $this->db->join('sic_kardex k', 'k.id_detalleproducto = d.id_detalleproducto');
           $this->db->join('sic_especifico e','e.id_especifico=d.id_especifico');
           $this->db->where('k.movimiento',$mov);
           $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function buscarProductosExistencia($busca,$mov){
      $this->db->like('nombre', $busca);
      $this->db->order_by("id_producto", "asc");
      $this->db->group_by("id_producto", "asc");
      $this->db->select('p.nombre,p.id_producto,sum(k.cantidad) as existencia,e.id_especifico');
           $this->db->from('sic_producto p');
           $this->db->join('sic_detalle_producto d', 'p.id_producto = d.id_producto');
           $this->db->join('sic_especifico e','e.id_especifico=d.id_especifico');
           $this->db->join('sic_kardex k', 'k.id_detalleproducto = d.id_detalleproducto');
           $this->db->where('k.movimiento',$mov);
           $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerTodoProducto($id){
      $this->db->where('id_producto',$id);
      $query = $this->db->get('sic_producto');
      if ($query->num_rows() > 0) {
        $producto;
        foreach ($query->result() as $pro) {
          $producto = $pro;
        }
        return  $producto;
      }
      else {
          return FALSE;
      }
    }
    public function obtenerProductosFuenteLimit($fuente,$segmento,$porpagina){
      $this->db->order_by("f.id_factura", "asc");
      $this->db->select('dp.numero_producto,e.id_especifico,p.nombre as producto, u.nombre as unidad, o.nombre_fuente,
      f.fecha_ingreso,sec.nombre_seccion,dp.id_detalleproducto,o.id_fuentes');
           $this->db->from('sic_detalle_factura df');
           $this->db->join('sic_factura f', 'f.id_factura = df.id_factura');
           $this->db->join('sic_detalle_producto dp', 'dp.id_detalleproducto = df.id_detalleproducto');
           $this->db->join('sic_producto p', 'p.id_producto = dp.id_producto');
           $this->db->join('sic_especifico e', 'e.id_especifico = dp.id_especifico');
           $this->db->join('sic_unidad_medida u', 'p.id_unidad_medida = u.id_unidad_medida');
           $this->db->join('mtps.org_seccion sec', 'sec.id_seccion = f.id_seccion');
           $this->db->join('sic_fuentes_fondo o', 'o.id_fuentes=f.id_fuentes');
           $this->db->where('f.estado','LIQUIDADA');
           $this->db->where('f.id_fuentes',$fuente);
           $this->db->where('p.exento','N');
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

    public function obtenerProductosFuenteTotal($fuente){
      $this->db->order_by("f.id_factura", "asc");
      $this->db->select('count(*) as numero');
           $this->db->from('sic_detalle_factura df');
           $this->db->join('sic_factura f', 'f.id_factura = df.id_factura');
           $this->db->join('sic_detalle_producto dp', 'dp.id_detalleproducto = df.id_detalleproducto');
           $this->db->join('sic_producto p', 'p.id_producto = dp.id_producto');
           $this->db->join('sic_especifico e', 'e.id_especifico = dp.id_especifico');
           $this->db->join('sic_unidad_medida u', 'p.id_unidad_medida = u.id_unidad_medida');
           $this->db->join('mtps.org_seccion sec', 'sec.id_seccion = f.id_seccion');
           $this->db->join('sic_fuentes_fondo o', 'o.id_fuentes=f.id_fuentes');
           $this->db->where('f.estado','LIQUIDADA');
           $this->db->where('f.id_fuentes',$fuente);
           $this->db->where('p.exento','N');
           $query = $this->db->get();
           return $query->row();
    }

    public function obtenerProductosFuenteTodo($fuente){
      $this->db->order_by("f.id_factura", "asc");
      $this->db->select('dp.numero_producto,e.id_especifico,p.nombre as producto, u.nombre as unidad, o.nombre_fuente,
      f.fecha_ingreso,sec.nombre_seccion,dp.id_detalleproducto,o.id_fuentes');
           $this->db->from('sic_detalle_factura df');
           $this->db->join('sic_factura f', 'f.id_factura = df.id_factura');
           $this->db->join('sic_detalle_producto dp', 'dp.id_detalleproducto = df.id_detalleproducto');
           $this->db->join('sic_producto p', 'p.id_producto = dp.id_producto');
           $this->db->join('sic_especifico e', 'e.id_especifico = dp.id_especifico');
           $this->db->join('sic_unidad_medida u', 'p.id_unidad_medida = u.id_unidad_medida');
           $this->db->join('mtps.org_seccion sec', 'sec.id_seccion = f.id_seccion');
           $this->db->join('sic_fuentes_fondo o', 'o.id_fuentes=f.id_fuentes');
           $this->db->where('f.estado','LIQUIDADA');
           $this->db->where('f.id_fuentes',$fuente);
           $this->db->where('p.exento','N');
           $this->db->group_by('df.id_detalle_factura');
           $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerExistenciaDetalleProducto($fuente, $porpagina, $busca = ''){
      # Utilizando el kardex dato
      $this->db->select("a.id_detalleproducto, a.id_especifico, b.id_producto, b.nombre nombre_producto, c.nombre nombre_unidad")
               ->from("sic_detalle_producto a")
               ->join("sic_producto b", "a.id_producto = b.id_producto")
               ->join("sic_unidad_medida c", "b.id_unidad_medida = c.id_unidad_medida	")
               ->limit($porpagina);
      if ($busca) {
        $this->db->like('b.nombre', $busca);
        $this->db->or_like('a.id_especifico', $busca);
      }
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
        $productos = $query->result();
        $existencias = array();
        foreach ($productos as $producto) {
          $this->db->select("a.id_kardex, SUM(d.existencia) existencia")
                   ->from("sic_kardex a")
                   ->join("sic_kardex_saldo d", "a.id_kardex = d.id_kardex")
                   ->where("a.id_detalleproducto", $producto->id_detalleproducto)
                   ->where("a.id_fuentes", $fuente)
                   ->group_by('a.id_kardex')
                   ->order_by("a.id_kardex", "DESC")
                   ->limit(1);
          $query2 = $this->db->get()->row();
          if (isset($query2)) {
             $producto->existencia = $query2->existencia;
          } else {
            $producto->existencia = 0;
          }
          array_push($existencias, $producto);
        }
        return $existencias;
      } else {
        return FALSE;
      }
    }

    public function obtenerIdPorNombre($nombre) {
      $this->db->where('nombre', $nombre);
      $query = $this->db->get('sic_producto');
      if ($query->num_rows() > 0) {
        return $query->row('id_producto');
      }
      else {
          return FALSE;
      }
    }

    public function obtenerProductoMasMovimiento() {
      $this->db->select("COUNT(a.id_detalleproducto) AS total, a.id_detalleproducto, c.nombre AS nombre_producto")
               ->from("sic_kardex a")
               ->join("sic_detalle_producto b", "a.id_detalleproducto = b.id_detalleproducto")
               ->join("sic_producto c", "b.id_producto = c.id_producto")
               ->group_by("a.id_detalleproducto")
               ->order_by("COUNT(a.id_detalleproducto) DESC")
               ->limit(5);
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
        return $query->result_array();
      }
      else {
          return FALSE;
      }
    }
  }
?>
