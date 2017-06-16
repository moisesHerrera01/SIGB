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

        public function obtenerProductosFuenteLimitBusca($fuente,$especifico, $busca) {
      $this->db->select('p.nombre as nombre_producto,kar.id_detalleproducto,ks.existencia,ff.nombre_fuente,dp.numero_producto,f.fecha_ingreso,um.nombre,sec.nombre_seccion');

        $this->db->from('(select k.cantidad,k.fecha_ingreso,k.id_fuentes,dp.id_detalleproducto,max(ks.id_kardex_saldo) as id_kardex_saldo
                          from sic_kardex k
                          join sic_kardex_saldo ks on ks.id_kardex=k.id_kardex
                          join sic_detalle_producto dp on dp.id_detalleproducto=k.id_detalleproducto
                          group by id_detalleproducto) kar');
           $this->db->join('sic_kardex_saldo ks ',' ks.id_kardex_saldo=kar.id_kardex_saldo');
           $this->db->join('sic_fuentes_fondo ff ',' ks.id_fuentes=ff.id_fuentes');
           $this->db->join('sic_detalle_producto dp ',' dp.id_detalleproducto=kar.id_detalleproducto');
           $this->db->join('sic_producto p',' p.id_producto=dp.id_producto');
           $this->db->join('sic_unidad_medida um ',' p.id_unidad_medida=um.id_unidad_medida');
           $this->db->join('sic_factura f ',' f.fecha_ingreso=kar.fecha_ingreso');
           $this->db->join('sic_factura fa ',' fa.id_fuentes=kar.id_fuentes');
           $this->db->join('mtps.org_seccion sec ',' fa.id_seccion=sec.id_seccion');
           $this->db->join('sic_detalle_factura detf ',' detf.id_detalleproducto=dp.id_detalleproducto');
           $this->db->join('sic_detalle_factura detfa ',' detf.cantidad=kar.cantidad');
           $this->db->where('dp.id_especifico',$especifico);
           $this->db->where('kar.id_fuentes',$fuente);
           $this->db->like('p.nombre', $busca);
           $this->db->group_by('kar.id_detalleproducto');
           $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerProductosFuenteLimit($fuente,$especifico,$segmento,$porpagina){
      //$this->db->order_by('kar.id_detalleproducto', 'asc');
      $this->db->select('p.nombre as nombre_producto,kar.id_detalleproducto,ks.existencia,ff.nombre_fuente,dp.numero_producto,f.fecha_ingreso,um.nombre,sec.nombre_seccion');

        $this->db->from('(select k.cantidad,k.fecha_ingreso,k.id_fuentes,dp.id_detalleproducto,max(ks.id_kardex_saldo) as id_kardex_saldo
                          from sic_kardex k
                          join sic_kardex_saldo ks on ks.id_kardex=k.id_kardex
                          join sic_detalle_producto dp on dp.id_detalleproducto=k.id_detalleproducto
                          group by id_detalleproducto) kar');
           $this->db->join('sic_kardex_saldo ks ',' ks.id_kardex_saldo=kar.id_kardex_saldo');
           $this->db->join('sic_fuentes_fondo ff ',' ks.id_fuentes=ff.id_fuentes');
           $this->db->join('sic_detalle_producto dp ',' dp.id_detalleproducto=kar.id_detalleproducto');
           $this->db->join('sic_producto p',' p.id_producto=dp.id_producto');
           $this->db->join('sic_unidad_medida um ',' p.id_unidad_medida=um.id_unidad_medida');
           $this->db->join('sic_factura f ',' f.fecha_ingreso=kar.fecha_ingreso');
           $this->db->join('sic_factura fa ',' fa.id_fuentes=kar.id_fuentes');
           $this->db->join('mtps.org_seccion sec ',' fa.id_seccion=sec.id_seccion');
           $this->db->join('sic_detalle_factura detf ',' detf.id_detalleproducto=dp.id_detalleproducto');
           $this->db->join('sic_detalle_factura detfa ',' detf.cantidad=kar.cantidad');
           $this->db->where('dp.id_especifico',$especifico);
           $this->db->where('kar.id_fuentes',$fuente);
           $this->db->limit($segmento,$porpagina);
           $this->db->group_by('kar.id_detalleproducto');
           $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function   obtenerProductosFuenteTotal($fuente,$especifico){
      $this->db->order_by("kar.id_fuentes", "asc");
      $this->db->select('count(*) as numero');
           $this->db->from('(select k.id_fuentes,dp.id_detalleproducto,max(ks.id_kardex_saldo) as id_kardex_saldo from sic_kardex k join sic_kardex_saldo ks on ks.id_kardex=k.id_kardex join sic_detalle_producto dp on dp.id_detalleproducto=k.id_detalleproducto group by id_detalleproducto) kar ');
           $this->db->join('sic_kardex_saldo ks',' ks.id_kardex_saldo=kar.id_kardex_saldo');
           $this->db->join('sic_detalle_producto dp',' dp.id_detalleproducto=kar.id_detalleproducto');
           $this->db->join('sic_producto p',' p.id_producto=dp.id_producto');
           $this->db->where('dp.id_especifico',$especifico);
           $this->db->where('kar.id_fuentes',$fuente);
           $query = $this->db->get();
           return $query->row();
    }

    public function obtenerProductosFuenteTodo($fuente,$especifico){
      $this->db->select('kar.id_detalleproducto,ks.existencia,ff.nombre_fuente,dp.numero_producto,f.fecha_ingreso,um.nombre,sec.nombre_seccion');

        $this->db->from('(select k.cantidad,k.fecha_ingreso,k.id_fuentes,dp.id_detalleproducto,max(ks.id_kardex_saldo) as id_kardex_saldo
                          from sic_kardex k
                          join sic_kardex_saldo ks on ks.id_kardex=k.id_kardex
                          join sic_detalle_producto dp on dp.id_detalleproducto=k.id_detalleproducto
                          group by id_detalleproducto) kar');
           $this->db->join('sic_kardex_saldo ks ',' ks.id_kardex_saldo=kar.id_kardex_saldo');
           $this->db->join('sic_fuentes_fondo ff ',' ks.id_fuentes=ff.id_fuentes');
           $this->db->join('sic_detalle_producto dp ',' dp.id_detalleproducto=kar.id_detalleproducto');
           $this->db->join('sic_producto p',' p.id_producto=dp.id_producto');
           $this->db->join('sic_unidad_medida um ',' p.id_unidad_medida=um.id_unidad_medida');
           $this->db->join('sic_factura f ',' f.fecha_ingreso=kar.fecha_ingreso');
           $this->db->join('sic_factura fa ',' fa.id_fuentes=kar.id_fuentes');
           $this->db->join('mtps.org_seccion sec ',' fa.id_seccion=sec.id_seccion');
           $this->db->join('sic_detalle_factura detf ',' detf.id_detalleproducto=dp.id_detalleproducto');
           $this->db->join('sic_detalle_factura detfa ',' detf.cantidad=kar.cantidad');
           $this->db->where('dp.id_especifico',$especifico);
           $this->db->where('kar.id_fuentes',$fuente);
           $this->db->group_by('kar.id_detalleproducto');
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


    public function obtenerProductoMasSolicitado($cantidad,$fecha_inicio,$fecha_fin) {
      $this->db->select("sum(ds.cantidad) as cant, ds.id_detalleproducto, p.nombre as nombre_producto,um.nombre as nombre_unidad_medida, dp.id_especifico")
               ->from("sic_detalle_solicitud_producto ds")
               ->join("sic_solicitud s", "ds.id_solicitud = s.id_solicitud")
               ->join("sic_detalle_producto dp", "ds.id_detalleproducto = dp.id_detalleproducto")
               ->join("sic_producto p", "dp.id_producto = p.id_producto")
               ->join("sic_unidad_medida um", "p.id_unidad_medida = um.id_unidad_medida")
               ->where("s.fecha_solicitud BETWEEN '$fecha_inicio' AND '$fecha_fin'")
               ->group_by("id_detalleproducto")
               ->order_by("cant DESC")
               ->limit($cantidad);
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
        return $query->result_array();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerProductoMasSolicitadoTotal($cantidad,$fecha_inicio,$fecha_fin) {
      $this->db->select("count(*) numero")
               ->from("sic_detalle_solicitud_producto ds")
               ->join("sic_solicitud s", "ds.id_solicitud = s.id_solicitud")
               ->join("sic_detalle_producto dp", "ds.id_detalleproducto = dp.id_detalleproducto")
               ->join("sic_producto p", "dp.id_producto = p.id_producto")
               ->join("sic_unidad_medida um", "p.id_unidad_medida = um.id_unidad_medida")
               ->where("s.fecha_solicitud BETWEEN '$fecha_inicio' AND '$fecha_fin'")
               ->group_by("ds.id_detalleproducto")

               ->limit($cantidad);
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
        return $query->result_array();
      }
      else {
          return FALSE;
      }
    }

  public function productosEspecifico($fecha_fin,$segmento,$porpagina){
    $this->db->select('dp.id_detalleproducto,e.id_especifico,e.nombre_especifico, sum(kar.existencia) cantidad,sum(kar.total) saldo')
             ->from('(select ks.existencia,ks.total,k.fecha_ingreso,k.id_fuentes,dp.id_detalleproducto,max(ks.id_kardex_saldo) as id_kardex_saldo
                                 from sic_kardex k
                                 join sic_kardex_saldo ks on ks.id_kardex=k.id_kardex
                                 join sic_detalle_producto dp on dp.id_detalleproducto=k.id_detalleproducto
                                 group by id_detalleproducto) as kar')
               ->join('sic_kardex_saldo ks ',' ks.id_kardex_saldo=kar.id_kardex_saldo')
               ->join('sic_detalle_producto dp','dp.id_detalleproducto=kar.id_detalleproducto')
               ->join('sic_especifico e','e.id_especifico=dp.id_especifico')
               ->group_by('e.id_especifico')
               ->limit($segmento,$porpagina)
               ->where('kar.fecha_ingreso<=',$fecha_fin);
      $query=$this->db->get();
      return  $query->result();

      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }


  public function totalproductosEspecifico($fecha_fin){
      $this->db->select('dp.id_detalleproducto,e.id_especifico,e.nombre_especifico, sum(kar.existencia) cantidad,sum(kar.total) saldo')
               ->from('(select ks.existencia,ks.total,k.fecha_ingreso,k.id_fuentes,dp.id_detalleproducto,max(ks.id_kardex_saldo) as id_kardex_saldo
                                 from sic_kardex k
                                 join sic_kardex_saldo ks on ks.id_kardex=k.id_kardex
                                 join sic_detalle_producto dp on dp.id_detalleproducto=k.id_detalleproducto
                                 group by id_detalleproducto) as kar')
               ->join('sic_kardex_saldo ks ',' ks.id_kardex_saldo=kar.id_kardex_saldo')
               ->join('sic_detalle_producto dp','dp.id_detalleproducto=kar.id_detalleproducto')
               ->join('sic_especifico e','e.id_especifico=dp.id_especifico')
               ->group_by('e.id_especifico')
               ->where('kar.fecha_ingreso<=',$fecha_fin);
      $query=$this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->num_rows();
      }
      else {
          return FALSE;
      }
    }

    public function buscarproductosEspecifico($fecha_fin,$busca){
      $this->db->select('dp.id_detalleproducto,e.id_especifico,e.nombre_especifico, sum(kar.existencia) cantidad,sum(kar.total) saldo')
               ->from('(select ks.existencia,ks.total,k.fecha_ingreso,k.id_fuentes,dp.id_detalleproducto,max(ks.id_kardex_saldo) as id_kardex_saldo
                                   from sic_kardex k
                                   join sic_kardex_saldo ks on ks.id_kardex=k.id_kardex
                                   join sic_detalle_producto dp on dp.id_detalleproducto=k.id_detalleproducto
                                   group by id_detalleproducto) as kar')
                 ->join('sic_kardex_saldo ks ',' ks.id_kardex_saldo=kar.id_kardex_saldo')
                 ->join('sic_detalle_producto dp','dp.id_detalleproducto=kar.id_detalleproducto')
                 ->join('sic_especifico e','e.id_especifico=dp.id_especifico')
                 ->group_by('e.id_especifico')
                 ->like('e.id_especifico',$busca)
                 ->or_like('e.nombre_especifico',$busca)
                 ->where('kar.fecha_ingreso<=',$fecha_fin);
        $query=$this->db->get();
        return  $query->result();

        if ($query->num_rows() > 0) {
            return  $query->result();
        }
        else {
            return FALSE;
        }
      }

      public function productosEspecificoExcel($fecha_fin){
        $this->db->select('dp.id_detalleproducto,e.id_especifico,e.nombre_especifico, sum(kar.existencia) cantidad,sum(kar.total) saldo')
                 ->from('(select ks.existencia,ks.total,k.fecha_ingreso,k.id_fuentes,dp.id_detalleproducto,max(ks.id_kardex_saldo) as id_kardex_saldo
                                     from sic_kardex k
                                     join sic_kardex_saldo ks on ks.id_kardex=k.id_kardex
                                     join sic_detalle_producto dp on dp.id_detalleproducto=k.id_detalleproducto
                                     group by id_detalleproducto) as kar')
                   ->join('sic_kardex_saldo ks ',' ks.id_kardex_saldo=kar.id_kardex_saldo')
                   ->join('sic_detalle_producto dp','dp.id_detalleproducto=kar.id_detalleproducto')
                   ->join('sic_especifico e','e.id_especifico=dp.id_especifico')
                   ->group_by('e.id_especifico')                
                   ->where('kar.fecha_ingreso<=',$fecha_fin);
          $query=$this->db->get();
          return  $query->result();

          if ($query->num_rows() > 0) {
              return  $query->result();
          }
          else {
              return FALSE;
          }
        }
}

?>
