<?php
  class Cuenta_contable_model extends CI_Model{

    public $id_cuenta_contable;
    public $nombre_cuenta;
    public $numero_cuenta;
    public $porcentaje_depreciacion;
    public $vida_util;

    function __construct() {
        parent::__construct();
    }

    public function insertarCuenta($data){
        $this->nombre_cuenta = $data['nombre_cuenta'];
        $this->numero_cuenta = $data['numero_cuenta'];
        $this->porcentaje_depreciacion = $data['porcentaje_depreciacion'];
        $this->vida_util = $data['vida_util'];
        $this->db->insert('sic_cuenta_contable', $this);
        return $this->db->insert_id();
    }

    public function obtenerCuentas(){
      $this->db->order_by("id_cuenta_contable", "asc");
      $query = $this->db->get('sic_cuenta_contable');
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerCuenta($id) {
      $this->db->where("id_cuenta_contable", $id);
      $query = $this->db->get('sic_cuenta_contable');
      if ($query->num_rows() > 0) {
          return  $query->row();
      }
      else {
          return FALSE;
      }
    }

    public function buscarCuentas($busca){
      $this->db->order_by("id_cuenta_contable", "desc");
      $this->db->like('nombre_cuenta', $busca);
      $this->db->or_like('numero_cuenta', $busca);
      $query = $this->db->get('sic_cuenta_contable', 10);
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function actualizarCuenta($id, $data){
      $this->db->where('id_cuenta_contable',$id);
      $this->db->update('sic_cuenta_contable', $data);
    }

    public function eliminarCuenta($id){
      $this->db->delete('sic_cuenta_contable', array('id_cuenta_contable' => $id));
    }

    function totalCuentas(){
      return $this->db->count_all('sic_cuenta_contable');
    }

    public function obtenerCuentasLimit($porpagina, $segmento){
      $this->db->order_by("id_cuenta_contable", "desc");
      $query = $this->db->get('sic_cuenta_contable', $porpagina, $segmento);
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function resumenCuentaContable($porpagina, $segmento) {
      $registros = $this->obtenerCuentasLimit($porpagina, $segmento);

      $cuentas = array();
      foreach ($registros as $cuenta) {
        $data = array(
          'id_cuenta_contable' => $cuenta->id_cuenta_contable,
          'nombre_cuenta' => $cuenta->nombre_cuenta,
          'numero_cuenta' => $cuenta->numero_cuenta,
          'precio' => 0,
          'dep_anual' => 0,
          'dep_acum' => 0,
          'valor_libro' => 0
        );

        /*Procesamiento de datos*/
        $this->db->from("sic_cuenta_contable a")
                 ->join("sic_datos_comunes b", "a.id_cuenta_contable = b.id_cuenta_contable")
                 ->join("sic_bien c", "c.id_dato_comun = b.id_dato_comun")
                 ->where("a.id_cuenta_contable", $cuenta->id_cuenta_contable);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
          $datos_comunes = $query->result();

          $acum_precio = 0;
          $acum_dep_anual = 0;
          $acum_dep_acum = 0;
          $acum_valor_libro = 0;

          foreach ($datos_comunes as $dato_comun) {

            $anio_adq = substr($dato_comun->fecha_adquisicion, 0, 4);
            $dif_anio = date("Y") - $anio_adq + 1;

            $dep_anual = ($dato_comun->precio_unitario - ($cuenta->porcentaje_depreciacion * $dato_comun->precio_unitario))/$cuenta->vida_util;

            $dep_acum = $dif_anio * $dep_anual;

            if ($dep_acum > $dato_comun->precio_unitario * $dato_comun->precio_unitario || $dif_anio > $cuenta->vida_util) {
              $dep_anual = 0;
              $dep_acum = ($dato_comun->precio_unitario - ($cuenta->porcentaje_depreciacion * $dato_comun->precio_unitario));
            }

            $acum_precio += $dato_comun->precio_unitario;
            $acum_dep_anual += $dep_anual;
            $acum_dep_acum += $dep_acum;
          }

          $acum_valor_libro = $acum_precio - $acum_dep_acum;

          $data['precio'] = $acum_precio;
          $data['dep_anual'] = $acum_dep_anual;
          $data['dep_acum'] = $acum_dep_acum;
          $data['valor_libro'] = $acum_valor_libro;
        }

        array_push($cuentas, $data);
      }

      return $cuentas;
    }


    public function depreciacionCuentaContable($cuenta, $fuente, $porpagina, $segmento) {
      $this->db->select('b.descripcion, d.nombre_marca, b.modelo, c.serie, c.numero_placa, c.codigo_anterior, c.codigo,
              e.nombre_oficina, f.primer_nombre, f.primer_apellido, f.nr, g.nombre_doc_ampara, b.nombre_doc_ampara as documento,
              b.fecha_adquisicion, b.precio_unitario, a.porcentaje_depreciacion, a.vida_util')
               ->from('sic_cuenta_contable a')
               ->join('sic_datos_comunes b', 'b.id_cuenta_contable = a.id_cuenta_contable')
               ->join('sic_bien c', 'c.id_dato_comun = b.id_dato_comun')
               ->join('sic_marcas d', 'd.id_marca = b.id_marca')
               ->join('org_oficina e', 'e.id_oficina = c.id_oficina')
               ->join('sir_empleado f', 'f.id_empleado = c.id_empleado')
               ->join('sic_doc_ampara g', 'g.id_doc_ampara = b.id_doc_ampara')
               ->where('a.id_cuenta_contable', $cuenta)
               ->where('b.id_fuentes', $fuente)
               ->limit($porpagina, $segmento);

      $query = $this->db->get();

      if ($query->num_rows() > 0) {
         return  $query->result();
      }
      else {
         return FALSE;
      }

    }

    public function totalDepreciacionCuentaContable($cuenta, $fuente) {
      $this->db->select('count(*) as total')
               ->from('sic_cuenta_contable a')
               ->join('sic_datos_comunes b', 'b.id_cuenta_contable = a.id_cuenta_contable')
               ->join('sic_bien c', 'c.id_dato_comun = b.id_dato_comun')
               ->join('sic_marcas d', 'd.id_marca = b.id_marca')
               ->join('org_oficina e', 'e.id_oficina = c.id_oficina')
               ->join('sir_empleado f', 'f.id_empleado = c.id_empleado')
               ->join('sic_doc_ampara g', 'g.id_doc_ampara = b.id_doc_ampara')
               ->where('a.id_cuenta_contable', $cuenta)
               ->where('b.id_fuentes', $fuente);

      $query = $this->db->get();

      if ($query->num_rows() > 0) {
         return  $query->row();
      }
      else {
         return FALSE;
      }

    }
    public function contieneDatoComun($id){
      $this->db->select('count(id_cuenta_contable) as asociados')
               ->from('sic_datos_comunes')
               ->where('id_cuenta_contable',$id);
      $query=$this->db->get();
      if ($query->num_rows()>0) {
        return $query->row();
      }else {
        return FALSE;
      }
    }

  }
?>
