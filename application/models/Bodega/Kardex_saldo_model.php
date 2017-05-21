<?php
  class Kardex_saldo_model extends CI_Model{

    public $id_kardex;
    public $id_fuentes;
    public $existencia;
    public $precio_unitario;
    public $total;

    function __construct() {
        parent::__construct();
    }

    public function insertarKardexSaldo($data, $ids) {

      $data['kardex'] = $ids[0];

      if (2 == count($ids)) {
        $ban = TRUE;
        $existencia_ant = $this->obtenerSaldosXKardex($ids[1]);
        foreach ($existencia_ant as $saldo) {
          if ($data['precio'] == $saldo->precio_unitario && $data['id_fuentes'] == $saldo->id_fuentes) {
            $ban = FALSE;
            if ($data['movimiento'] == 'ENTRADA') {
              $this->registrarEntrada($data, $saldo);
            } else {
              $this->registrarSalida($data, $saldo);
            }
          }
          /*Redundancia*/
          else {
            $this->registrarSaldo($saldo, $data['kardex']);
          }

        }
        if ($ban) {
          /*Es un nuevo registro por que tiene diferente precio o una fuente distinta*/
          if ($data['movimiento'] == 'ENTRADA') {
            $this->registrarEntrada($data);
          }
        }
      } else {
        #Ocurrio la primera transaccion para ese producto pero de ingreso
        if ($data['movimiento'] == 'ENTRADA') {
          $this->registrarEntrada($data);
        } else {
          echo "El producto no tiene existencia";
        }
      }

    }

    public function obtenerSaldosXKardex($id_kardex) {
      $this->db->from('sic_kardex_saldo')
               ->where('id_kardex', $id_kardex);

      $query = $this->db->get();
      if ($query->num_rows() > 0) {
        return $query->result();
      } else {
        return FALSE;
      }

    }

    public function registrarEntrada($data, $saldo = '') {
      if ('' == $saldo) {
        $this->id_kardex = $data['kardex'];
        $this->id_fuentes = $data['id_fuentes'];
        $this->existencia = $data['cantidad'];
        $this->precio_unitario = $data['precio'];
        $this->total = $data['cantidad'] * $data['precio'];

        $this->db->insert('sic_kardex_saldo', $this);
      } else {
        $this->id_kardex = $data['kardex'];
        $this->id_fuentes = $data['id_fuentes'];
        $this->existencia = $data['cantidad'] + $saldo->existencia;
        $this->precio_unitario = $data['precio'];
        $this->total = ($data['cantidad'] + $saldo->existencia) * $data['precio'];

        $this->db->insert('sic_kardex_saldo', $this);
      }
    }

    public function registrarSalida($data, $saldo) {
        if (!empty($saldo)) {
          $this->id_kardex = $data['kardex'];
          $this->id_fuentes = $data['id_fuentes'];
          $this->existencia = $saldo->existencia - $data['cantidad'];
          $this->precio_unitario = $data['precio'];
          $this->total = ($saldo->existencia - $data['cantidad']) * $data['precio'];

          $this->db->insert('sic_kardex_saldo', $this);
        }
    }

    public function registrarSaldo($saldo, $id_kardex) {

      if (0 != $saldo->existencia) {
        $this->id_kardex = $id_kardex;
        $this->id_fuentes = $saldo->id_fuentes;
        $this->existencia = $saldo->existencia;
        $this->precio_unitario = $saldo->precio_unitario;
        $this->total = $saldo->total;

        $this->db->insert('sic_kardex_saldo', $this);
      }
    }

    public function NumeroResgistros($id_kardex, $fuente = 0) {
      $this->db->from('sic_kardex_saldo')
               ->where('id_kardex', $id_kardex);
      if ($fuente != 0) {
        $this->db->where('id_fuentes', $fuente);
      }
      return $this->db->count_all_results();
    }

    public function ObtenerExistenciaKardex($id_kardex) {
      $this->db->select_sum('existencia', 'existencia')
               ->where('id_kardex', $id_kardex);
      $query = $this->db->get('sic_kardex_saldo');
      if ($query->num_rows() > 0) {
        return $query->row('existencia');
      } else {
        return 0;
      }

    }

    public function obtenerAnteriorKardexSaldo($kardex, $id_detalleproducto) {
      if ($kardex) {
        $this->db->select("MAX(a.id_kardex) min")
                 ->from("sic_kardex a")
                 ->where("a.id_kardex <", $kardex)
                 ->where("a.id_detalleproducto", $id_detalleproducto);
        return $this->db->get()->row()->min;
      } else {
        return 0;
      }
    }

  }
?>
