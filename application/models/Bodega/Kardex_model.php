<?php
  class Kardex_model extends CI_Model{

    public $id_detalleproducto;
    public $cantidad;
    public $precio;
    public $movimiento;
    public $fecha_ingreso;

    function __construct() {
        parent::__construct();
        $this->load->model('Bodega/Kardex_saldo_model');
    }

    public function insertarKardex($data){

        $this->id_detalleproducto = $data['id_detalleproducto'];
        $this->cantidad = $data['cantidad'];
        $this->precio = $data['precio'];
        $this->movimiento = $data['movimiento'];
        $this->fecha_ingreso = $data['fecha_ingreso'];
        $this->id_fuentes=$data['id_fuentes'];

        $this->db->insert('sic_kardex', $this);

        $kardex = $this->ObtenerTransaccionesProducto($data['id_detalleproducto']);
        $this->Kardex_saldo_model->insertarKardexSaldo($data, $kardex);

        return $kardex[0];
    }

    public function obtenerExistencias($id, $fecha){

      $kardex = $this->obtenerKardexProductoFecha($id, $fecha);

      if (0 != $kardex) {
        return $this->Kardex_saldo_model->ObtenerExistenciaKardex($kardex);
      } else {
        return 0;
      }
    }

    public function obtenerFuenteFondo($id_detalleproducto, $fecha){
      $this->db->select('b.nombre_fuente')
               ->from('sic_kardex a')
               ->join('sic_fuentes_fondo b', 'a.id_fuentes = b.id_fuentes')
               ->where('a.id_detalleproducto', $id_detalleproducto)
               ->where('a.fecha_ingreso <', $fecha);

      return $this->db->get()->row('nombre_fuente');
    }

    public function TotalDetalleProducto($id,$minFecha,$maxFecha,$fuente){
      $this->db->select('count(id_detalleproducto) as cantidad');
      $this->db->from('sic_kardex');
      $this->db->where('id_detalleproducto', $id);
      $this->db->where("fecha_ingreso BETWEEN '$minFecha' AND '$maxFecha'");
      $this->db->where("id_fuentes", $fuente);
      $query=$this->db->get();
      return $query->row();
    }

    public function TotalDetalleProductoTodos($minFecha,$maxFecha){
      $this->db->select('count(id_detalleproducto) as cantidad');
      $this->db->from('sic_kardex');
      $this->db->where("fecha_ingreso BETWEEN '$minFecha' AND '$maxFecha'");
      $query=$this->db->get();
      return $query->row();
    }

    public function GeneracionKardexProductoEntradaLimit($minFecha, $maxFecha, $id, $fuente, $porpagina, $segmento) {
        $this->db->select('d.id_detalleproducto, d.fecha_ingreso, d.id_kardex, d.cantidad, d.precio, e.existencia, e.total, c.id_factura, c.numero_compromiso')
                 ->from('sic_detalle_producto a')
                 ->join('sic_detalle_factura b', 'b.id_detalleproducto = a.id_detalleproducto')
                 ->join('sic_factura c', 'c.id_factura = b.id_factura')
                 ->join('sic_kardex d', 'd.id_detalleproducto = b.id_detalleproducto')
                 ->join('sic_kardex_saldo e', 'e.id_kardex = d.id_kardex')
                 ->where('a.id_producto', $id)
                 ->where('a.id_detalleproducto = d.id_detalleproducto')
                 ->where('c.fecha_ingreso = d.fecha_ingreso')
                 ->where('d.movimiento', 'ENTRADA')
                 ->where('d.id_fuentes', $fuente)
                 ->where('e.id_fuentes', $fuente)
                 ->where("d.fecha_ingreso BETWEEN '$minFecha' AND '$maxFecha'")
                 ->order_by('d.id_kardex', 'DESC')
                 ->group_by('e.id_kardex_saldo')
                 ->limit($porpagina, $segmento);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
          return $query->result();
        } else {
          return FALSE;
        }
    }

    public function GeneracionKardexEntradaLimit($minFecha, $maxFecha, $porpagina, $segmento) {
      $this->db->select('d.id_detalleproducto, d.fecha_ingreso, d.id_kardex, d.cantidad, d.precio, e.existencia, e.total, c.id_factura,
              c.numero_compromiso, c.id_seccion ,p.nombre as producto, g.nombre as unidad')
                 ->from('sic_detalle_producto a')
                 ->join('sic_detalle_factura b', 'a.id_detalleproducto = b.id_detalleproducto')
                 ->join('sic_factura c', 'b.id_factura = c.id_factura')
                 ->join('sic_kardex d', 'a.id_detalleproducto = d.id_detalleproducto')
                 ->join('sic_kardex_saldo e', 'e.id_kardex = d.id_kardex')
                 ->join('sic_producto p', 'a.id_producto=p.id_producto')
                 ->join('sic_unidad_medida g', 'g.id_unidad_medida=p.id_unidad_medida')
                 ->where('a.id_detalleproducto = d.id_detalleproducto')
                 ->where('c.fecha_ingreso = d.fecha_ingreso')
                 ->where('d.movimiento', 'ENTRADA')
                 ->where("d.fecha_ingreso BETWEEN '$minFecha' AND '$maxFecha'")
                 ->order_by('d.id_kardex', 'DESC')
                 ->group_by('e.id_kardex_saldo')
                 ->limit($porpagina,$segmento);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
          return $query->result();
        } else {
          return FALSE;
        }
    }

    public function GeneracionKardexEntrada($minFecha, $maxFecha) {
      $this->db->select('d.id_detalleproducto, d.fecha_ingreso, d.id_kardex, d.cantidad, d.precio, e.existencia, e.total, c.id_factura,
              c.numero_compromiso, c.id_seccion ,p.nombre as producto, g.nombre as unidad')
                 ->from('sic_detalle_producto a')
                 ->join('sic_detalle_factura b', 'a.id_detalleproducto = b.id_detalleproducto')
                 ->join('sic_factura c', 'b.id_factura = c.id_factura')
                 ->join('sic_kardex d', 'a.id_detalleproducto = d.id_detalleproducto')
                 ->join('sic_kardex_saldo e', 'e.id_kardex = d.id_kardex')
                 ->join('sic_producto p', 'a.id_producto=p.id_producto')
                 ->join('sic_unidad_medida g', 'g.id_unidad_medida=p.id_unidad_medida')
                 ->where('a.id_detalleproducto = d.id_detalleproducto')
                 ->where('c.fecha_ingreso = d.fecha_ingreso')
                 ->where('d.movimiento', 'ENTRADA')
                 ->where("d.fecha_ingreso BETWEEN '$minFecha' AND '$maxFecha'")
                 ->order_by('d.id_kardex', 'DESC')
                 ->group_by('e.id_kardex_saldo');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
          return $query->result();
        } else {
          return FALSE;
        }
    }

    public function GeneracionKardexProductoSalidaLimit($minFecha, $maxFecha, $id, $fuente, $porpagina, $segmento) {
        $this->db->select('d.id_detalleproducto, d.fecha_ingreso, d.id_kardex, d.cantidad, d.precio, e.existencia, e.total, c.id_solicitud,
                c.numero_solicitud')
                 ->from('sic_detalle_producto a')
                 ->join('sic_detalle_solicitud_producto b', 'b.id_detalleproducto = a.id_detalleproducto')
                 ->join('sic_solicitud c', 'b.id_solicitud = c.id_solicitud')
                 ->join('sic_kardex d', 'a.id_detalleproducto = d.id_detalleproducto')
                 ->join('sic_kardex_saldo e', 'd.id_kardex = e.id_kardex')
                 ->where('a.id_producto', $id)
                 ->where('c.fecha_salida = d.fecha_ingreso')
                 ->where('a.id_detalleproducto = d.id_detalleproducto')
                 ->where('d.movimiento', 'SALIDA')
                 ->where('d.id_fuentes', $fuente)
                 ->where('e.id_fuentes', $fuente)
                 ->where("d.fecha_ingreso BETWEEN '$minFecha' AND '$maxFecha'")
                 ->order_by('d.id_kardex', 'DESC')
                 ->group_by('e.id_kardex_saldo')
                 ->limit($porpagina, $segmento);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
          return $query->result();
        } else {
          return FALSE;
        }
    }

    public function GeneracionKardexSalidaLimit($minFecha, $maxFecha,$porpagina, $segmento) {
        $this->db->select('d.id_detalleproducto, d.fecha_ingreso, d.id_kardex, d.cantidad, d.precio, e.existencia, e.total, c.id_solicitud,
                c.numero_solicitud, c.id_seccion, p.nombre as producto, g.nombre as unidad')
                 ->from('sic_detalle_producto a')
                 ->join('sic_detalle_solicitud_producto b', 'a.id_detalleproducto = b.id_detalleproducto')
                 ->join('sic_solicitud c', 'b.id_solicitud = c.id_solicitud')
                 ->join('sic_kardex d', 'a.id_detalleproducto = d.id_detalleproducto')
                 ->join('sic_kardex_saldo e', 'd.id_kardex = e.id_kardex')
                 ->join('sic_producto p', 'a.id_producto=p.id_producto')
                 ->join('sic_unidad_medida g', 'g.id_unidad_medida=p.id_unidad_medida')
                 ->where('c.fecha_salida = d.fecha_ingreso')
                 ->where('d.movimiento', 'SALIDA')
                 ->where("d.fecha_ingreso BETWEEN '$minFecha' AND '$maxFecha'")
                 ->order_by('d.id_kardex', 'DESC')
                 ->group_by('e.id_kardex_saldo')
                 ->limit($porpagina, $segmento);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
          return $query->result();
        } else {
          return FALSE;
        }
    }

    public function GeneracionKardexSalida($minFecha, $maxFecha) {
      $this->db->select('d.id_detalleproducto, d.fecha_ingreso, d.id_kardex, d.cantidad, d.precio, e.existencia, e.total, c.id_solicitud,
              c.numero_solicitud, c.id_seccion, p.nombre as producto, g.nombre as unidad')
               ->from('sic_detalle_producto a')
               ->join('sic_detalle_solicitud_producto b', 'a.id_detalleproducto = b.id_detalleproducto')
               ->join('sic_solicitud c', 'b.id_solicitud = c.id_solicitud')
               ->join('sic_kardex d', 'a.id_detalleproducto = d.id_detalleproducto')
               ->join('sic_kardex_saldo e', 'd.id_kardex = e.id_kardex')
               ->join('sic_producto p', 'a.id_producto=p.id_producto')
               ->join('sic_unidad_medida g', 'g.id_unidad_medida=p.id_unidad_medida')
               ->where('c.fecha_salida = d.fecha_ingreso')
               ->where('d.movimiento', 'SALIDA')
               ->where("d.fecha_ingreso BETWEEN '$minFecha' AND '$maxFecha'")
               ->order_by('d.id_kardex', 'DESC')
               ->group_by('e.id_kardex_saldo');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
          return $query->result();
        } else {
          return FALSE;
        }
    }

    public function numeroPorDetalleProducto(){
      $this->db->select('p.nombre, d.id_detalleproducto,count(k.id_detalleproducto) as numero')
               ->from('sic_kardex k')
               ->join('sic_detalle_producto d','d.id_detalleproducto=k.id_detalleproducto')
               ->join('sic_producto p','p.id_producto=d.id_producto')
               ->group_by('id_detalleproducto');
      $query=$this->db->get();
      if ($query->num_rows() > 0) {
        return $query->result();
      } else {
        return FALSE;
      }
    }

    public function GeneracionKardexProductoSalida($minFecha, $maxFecha, $id, $fuente) {
        $this->db->select('d.id_detalleproducto, d.fecha_ingreso, d.id_kardex, d.cantidad, d.precio, e.existencia, e.total, c.id_solicitud, c.numero_solicitud, c.id_seccion')
                 ->from('sic_detalle_producto a')
                 ->join('sic_detalle_solicitud_producto b', 'b.id_detalleproducto = a.id_detalleproducto')
                 ->join('sic_solicitud c', 'b.id_solicitud = c.id_solicitud')
                 ->join('sic_kardex d', 'a.id_detalleproducto = d.id_detalleproducto')
                 ->join('sic_kardex_saldo e', 'd.id_kardex = e.id_kardex')
                 ->where('a.id_producto', $id)
                 ->where('c.fecha_salida = d.fecha_ingreso')
                 ->where('a.id_detalleproducto = d.id_detalleproducto')
                 ->where('d.movimiento', 'SALIDA')
                 ->where('d.id_fuentes', $fuente)
                 ->where('e.id_fuentes', $fuente)
                 ->where("d.fecha_ingreso BETWEEN '$minFecha' AND '$maxFecha'")
                 ->order_by('d.id_kardex', 'DESC')
                 ->group_by('e.id_kardex_saldo');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
          return $query->result();
        } else {
          return FALSE;
        }
    }

    public function GeneracionKardexProductoEntrada($minFecha, $maxFecha, $id, $fuente) {
        $this->db->select('d.id_detalleproducto, d.fecha_ingreso, d.id_kardex, d.cantidad, d.precio, e.existencia, e.total, c.id_factura, c.numero_compromiso, c.id_seccion')
                 ->from('sic_detalle_producto a')
                 ->join('sic_detalle_factura b', 'b.id_detalleproducto = a.id_detalleproducto')
                 ->join('sic_factura c', 'c.id_factura = b.id_factura')
                 ->join('sic_kardex d', 'd.id_detalleproducto = b.id_detalleproducto')
                 ->join('sic_kardex_saldo e', 'e.id_kardex = d.id_kardex')
                 ->where('a.id_producto', $id)
                 ->where('a.id_detalleproducto = d.id_detalleproducto')
                 ->where('c.fecha_ingreso = d.fecha_ingreso')
                 ->where('d.movimiento', 'ENTRADA')
                 ->where('d.id_fuentes', $fuente)
                 ->where('e.id_fuentes', $fuente)
                 ->where("d.fecha_ingreso BETWEEN '$minFecha' AND '$maxFecha'")
                 ->order_by('d.id_kardex', 'DESC')
                 ->group_by('e.id_kardex_saldo');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
          return $query->result();
        } else {
          return FALSE;
        }
    }

    public function ObtenerInventarioGeneral($id, $fuente, $minFecha, $maxFecha) {
      $this->db->select('c.nombre_especifico, c.id_especifico, b.numero_producto AS "id_producto", a.nombre AS "nombre_producto", e.nombre AS "unidad_medida",
                        d.precio, SUM(d.cantidad) AS "existencia"')
               ->from('sic_producto a')
               ->join('sic_detalle_producto b', 'a.id_producto = b.id_producto')
               ->join('sic_especifico c', 'b.id_especifico = c.id_especifico')
               ->join('sic_kardex d', 'b.id_detalleproducto = d.id_detalleproducto')
               ->join('sic_unidad_medida e', 'e.id_unidad_medida = a.id_unidad_medida')
               ->where('a.id_producto', $id)
               ->where("d.id_fuentes", $fuente)
               ->where("d.fecha_ingreso BETWEEN '$minFecha' AND '$maxFecha'")
               ->where('d.movimiento', 'ENTRADA')
               ->order_by('c.id_especifico', 'asc')
               ->group_by('d.precio', 'asc');
      $query1 = $this->db->get();

      $this->db->select('c.nombre_especifico, c.id_especifico, b.numero_producto AS "id_producto", a.nombre AS "nombre_producto", e.nombre AS "unidad_medida",
                        d.precio, SUM(d.cantidad) AS "existencia"')
               ->from('sic_producto a')
               ->join('sic_detalle_producto b', 'a.id_producto = b.id_producto')
               ->join('sic_especifico c', 'b.id_especifico = c.id_especifico')
               ->join('sic_kardex d', 'b.id_detalleproducto = d.id_detalleproducto')
               ->join('sic_unidad_medida e', 'e.id_unidad_medida = a.id_unidad_medida')
               ->where('a.id_producto', $id)
               ->where("d.id_fuentes", $fuente)
               ->where("d.fecha_ingreso BETWEEN '$minFecha' AND '$maxFecha'")
               ->where('d.movimiento', 'SALIDA')
               ->order_by('c.id_especifico', 'asc')
               ->group_by('d.precio', 'asc');
      $query2 = $this->db->get();

      $aux = array();
      if ($query1->num_rows() > 0) {
        $entradas = $query1->result_array();
        if ($query2->num_rows() > 0) {
          $salidas = $query2->result_array();
          foreach ($salidas as $salida) {
            foreach ($entradas as $entrada) {
              if ($salida['precio'] == $entrada['precio']) {
                $entrada['existencia'] -= $salida['existencia'];
              }
              $aux[] = $entrada;
            }
          }
        } else {
          $aux = $entradas;
        }
      } else {
        $aux = FALSE;
      }

      return $aux;
    }

    public function ObtenerProductosInventario($fuente, $minFecha, $maxFecha, $porpagina = 10000, $segmento = 0) {
      $this->db->select("a.id_producto")
               ->from("sic_detalle_producto a")
               ->join("sic_kardex b", "a.id_detalleproducto = b.id_detalleproducto")
               ->where("b.id_fuentes", $fuente)
               ->where("b.fecha_ingreso BETWEEN '$minFecha' AND '$maxFecha'")
               ->order_by('a.id_especifico', 'asc')
               ->group_by('a.id_detalleproducto', 'asc')
               ->limit($porpagina, $segmento);
      $query = $this->db->get();

      $this->db->select("count(*) AS cuenta")
               ->from("sic_detalle_producto a")
               ->join("sic_kardex b", "a.id_detalleproducto = b.id_detalleproducto")
               ->where("b.id_fuentes", $fuente)
               ->where("b.fecha_ingreso BETWEEN '$minFecha' AND '$maxFecha'")
               ->order_by('a.id_especifico', 'asc')
               ->group_by('a.id_detalleproducto');

      $query1 = $this->db->get();

      $resultado = array(
        'cuenta' => 0,
        'registros' => ''
      );
      if ($query1->row('cuenta') > 0) {
        $resultado['cuenta'] = $query1->result();
        $resultado['registros'] = $query->result();
        return $resultado;
      } else {
        return $resultado;
      }
    }

    public function ObtenerTransaccionesProducto($id) {
      $this->db->select('id_kardex')
               ->from('sic_kardex')
               ->where('id_detalleproducto', $id)
               ->order_by('id_kardex', 'desc')
               ->limit(2);
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
        $resultados = $query->result();
        $data = array();
        foreach ($resultados as $value) {
          $data[] = $value->id_kardex;
        }
        return $data;
      } else {
        return FALSE;
      }
    }

    public function obtenerKardexProductoFecha($id, $fecha) {
      $this->db->select('id_kardex')
               ->from('sic_kardex')
               ->where('id_detalleproducto', $id)
               ->where('fecha_ingreso <=', $fecha)
               ->order_by('id_kardex', 'desc')
               ->limit(1);
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
        return $query->row('id_kardex');
      } else {
        return 0;
      }

    }

    public function obtenerExistenciaFuente($producto, $fuente) {
      $this->db->select('id_kardex')
               ->from('sic_kardex')
               ->where('id_detalleproducto', $producto)
               ->order_by('id_kardex', 'desc')
               ->limit(1);
      $query = $this->db->get();

      if ($query->num_rows() > 0) {
        $kardex = $query->row('id_kardex');

        $this->db->select('*')
                 ->from('sic_kardex_saldo')
                 ->where('id_kardex', $kardex)
                 ->where('id_fuentes', $fuente)
                 ->order_by('id_kardex', 'desc')
                 ->limit(1);
        $query1 = $this->db->get();

        $cantidad = 0;
        if ($query->num_rows() > 0) {
          foreach ($query1->result() as $value) {
            $cantidad += $value->existencia;
          }
        }

        return $cantidad;

      } else {
        return 0;
      }
    }

    public function obtenerTotalKardexPermisos($id_especifico) {
      $this->db->select("COUNT(*) numero")
               ->from("sic_detalle_producto a")
               ->join("sic_producto b", "a.id_producto = b.id_producto")
               ->where("a.id_especifico", $id_especifico);
      return $this->db->get()->row('numero');
    }

    public function obtenerKardexResumido($id_especifico, $id_fuentes, $fecha_inicio, $fecha_fin, $porpagina, $segmento) {
      $this->db->select("a.id_detalleproducto, c.id_producto, c.nombre nombre_producto, b.id_especifico")
               ->from("sic_kardex a")
               ->join("sic_detalle_producto b", "a.id_detalleproducto = b.id_detalleproducto")
               ->join("sic_producto c", "b.id_producto = c.id_producto")
               ->where("a.fecha_ingreso BETWEEN '$fecha_inicio' AND '$fecha_fin'")
               ->group_by("c.id_producto")
               ->limit($porpagina, $segmento);

      if ($id_especifico != 0) {
        $this->db->where("b.id_especifico", $id_especifico);
      }

      $productos = $this->db->get()->result();
      if ($productos) {
        $i = 0;
        foreach ($productos as $producto) {
          $this->db->select("MAX(a.id_kardex) max, MIN(a.id_kardex) min")
                   ->from("sic_kardex a")
                   ->where("a.id_detalleproducto", $producto->id_detalleproducto)
                   ->where("a.id_fuentes", $id_fuentes)
                   ->where("a.fecha_ingreso BETWEEN '$fecha_inicio' AND '$fecha_fin'");
          $producto->rango = $this->db->get()->result();

          $this->db->select("SUM(a.cantidad) cantidad, a.precio")
                   ->from("sic_kardex a")
                   ->where("a.id_detalleproducto", $producto->id_detalleproducto)
                   ->where("a.id_fuentes", $id_fuentes)
                   ->where("a.fecha_ingreso BETWEEN '$fecha_inicio' AND '$fecha_fin'")
                   ->where("a.movimiento", "ENTRADA")
                   ->order_by('a.id_kardex', 'asc')
                   ->group_by('a.precio');
          $producto->detalle_ingreso = $this->db->get()->result();

          $this->db->select("SUM(a.cantidad) cantidad, a.precio")
                   ->from("sic_kardex a")
                   ->where("a.id_detalleproducto", $producto->id_detalleproducto)
                   ->where("a.id_fuentes", $id_fuentes)
                   ->where("a.fecha_ingreso BETWEEN '$fecha_inicio' AND '$fecha_fin'")
                   ->where("a.movimiento", "SALIDA")
                   ->order_by('a.id_kardex', 'asc')
                   ->group_by('a.precio');
          $producto->detalle_salida = $this->db->get()->result();

          if (NULL == $producto->detalle_ingreso && NULL == $producto->detalle_salida) {
            unset($productos[$i]);
          }
          $i++;
        }
        return $productos;
      } else {
        return FALSE;
      }
    }

    public function buscarKardexResumido($id_especifico, $id_fuentes, $fecha_inicio, $fecha_fin, $busca) {
      $this->db->select("a.id_detalleproducto, c.id_producto, c.nombre nombre_producto, b.id_especifico")
               ->from("sic_kardex a")
               ->join("sic_detalle_producto b", "a.id_detalleproducto = b.id_detalleproducto")
               ->join("sic_producto c", "b.id_producto = c.id_producto")
               ->where("a.fecha_ingreso BETWEEN '$fecha_inicio' AND '$fecha_fin'")
               ->group_by("c.id_producto")
               ->like('c.nombre',$busca);

      if ($id_especifico != 0) {
        $this->db->where("b.id_especifico", $id_especifico);
      }

      $productos = $this->db->get()->result();
      if ($productos) {
        $i = 0;
        foreach ($productos as $producto) {
          $this->db->select("MAX(a.id_kardex) max, MIN(a.id_kardex) min")
                   ->from("sic_kardex a")
                   ->where("a.id_detalleproducto", $producto->id_detalleproducto)
                   ->where("a.id_fuentes", $id_fuentes)
                   ->where("a.fecha_ingreso BETWEEN '$fecha_inicio' AND '$fecha_fin'");
          $producto->rango = $this->db->get()->result();

          $this->db->select("SUM(a.cantidad) cantidad, a.precio")
                   ->from("sic_kardex a")
                   ->where("a.id_detalleproducto", $producto->id_detalleproducto)
                   ->where("a.id_fuentes", $id_fuentes)
                   ->where("a.fecha_ingreso BETWEEN '$fecha_inicio' AND '$fecha_fin'")
                   ->where("a.movimiento", "ENTRADA")
                   ->order_by('a.id_kardex', 'asc')
                   ->group_by('a.precio');
          $producto->detalle_ingreso = $this->db->get()->result();

          $this->db->select("SUM(a.cantidad) cantidad, a.precio")
                   ->from("sic_kardex a")
                   ->where("a.id_detalleproducto", $producto->id_detalleproducto)
                   ->where("a.id_fuentes", $id_fuentes)
                   ->where("a.fecha_ingreso BETWEEN '$fecha_inicio' AND '$fecha_fin'")
                   ->where("a.movimiento", "SALIDA")
                   ->order_by('a.id_kardex', 'asc')
                   ->group_by('a.precio');
          $producto->detalle_salida = $this->db->get()->result();

          if (NULL == $producto->detalle_ingreso && NULL == $producto->detalle_salida) {
            unset($productos[$i]);
          }
          $i++;
        }
        return $productos;
      } else {
        return FALSE;
      }
    }

    public function totalKardexResumido($id_especifico, $id_fuentes, $fecha_inicio, $fecha_fin) {
      $this->db->select("a.id_detalleproducto, c.id_producto, c.nombre nombre_producto")
               ->from("sic_kardex a")
               ->join("sic_detalle_producto b", "a.id_detalleproducto = b.id_detalleproducto")
               ->join("sic_producto c", "b.id_producto = c.id_producto")
               ->where("a.fecha_ingreso BETWEEN '$fecha_inicio' AND '$fecha_fin'")
               ->group_by("c.id_producto");

      if ($id_especifico != 0) {
       $this->db->where("b.id_especifico", $id_especifico);
      }

      return count($this->db->get()->result());
    }

    public function eliminarKardex($id_detalle_solicitud_producto,$id_detalleproducto,$fuente,$cantidad,$precio){
      $this->db->select('ds.id_detalle_solicitud_producto,k.id_kardex,ks.id_kardex_saldo')
               ->from('sic_detalle_solicitud_producto ds')
               ->join('sic_solicitud s','ds.id_solicitud=s.id_solicitud')
               ->join('sic_kardex k','k.id_detalleproducto=ds.id_detalleproducto')
               ->join('sic_kardex_saldo ks','ks.id_kardex=k.id_kardex')
               ->where('ds.id_detalleproducto',$id_detalleproducto)
               ->where('k.id_fuentes',$fuente)
               ->where('ks.id_fuentes',$fuente)
               ->where('k.cantidad',$cantidad)
               ->where('k.movimiento','SALIDA')
               ->where('k.precio',$precio)
               ->where('ks.precio_unitario',$precio)
               ->where('ds.id_detalle_solicitud_producto',$id_detalle_solicitud_producto)
               ->group_by('ks.id_kardex_saldo');
      $query=$this->db->get();

      foreach ($query->result() as $ids) {
        $this->db->delete('sic_kardex_saldo', array('id_kardex_saldo' => $ids->id_kardex_saldo));
        $this->db->delete('sic_kardex', array('id_kardex' => $ids->id_kardex));
        $this->db->delete('sic_detalle_solicitud_producto', array('id_detalle_solicitud_producto' => $ids->id_detalle_solicitud_producto));
      }
    }

    /*SIGB*/
    public function comparacionFuenteFondo($anio) {
      $this->db->select('b.id_fuentes, b.nombre_fuente, SUM(a.cantidad) cantidad, SUM(a.cantidad * a.precio) saldo')
               ->from('sic_kardex a')
               ->join('sic_fuentes_fondo b', 'a.id_fuentes = b.id_fuentes')
               ->where('a.movimiento', 'SALIDA')
               ->where("a.fecha_ingreso BETWEEN '".$anio."-01-01' AND '".$anio."-12-31'")
               ->group_by('a.id_fuentes');

      $query=$this->db->get();

      if ($query->num_rows() > 0) {
        return $query->result();
      } else {
        return 0;
      }
    }
  }
?>
