<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cargamasiva extends CI_Controller {

  private $datos = array();
  #indica en base a que se ordena
  private $aux = array();

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('form', 'paginacion'));
    $this->load->library(array('table', 'excel'));
    $this->load->model(array('Bodega/Fuentefondos_model', 'Bodega/Factura_Model', 'Bodega/Detallefactura_Model',
                       'Bodega/UnidadMedida', 'Bodega/Producto', 'Bodega/DetalleProducto_model', 'Bodega/Kardex_model'));
  }

  public function index() {
    $data['title'] = "Carga Masiva";
    $data['js'] = "assets/js/carga.js";
    $data['body'] = $this->load->view('Bodega/cargaMasiva_view', '', TRUE) .
                    "<br><div class='content_table table-responsive'>". $this->mostrarTabla() ."</div>";
    $data['menu'] = $this->menu_dinamico->build_menu_vertical($this->session->userdata('logged_in'), $this->uri->segment(1));
    $this->load->view('base', $data);
  }

  public function cargar_archivo() {

    $config['upload_path'] = "uploads/";
    $config['allowed_types'] = "xls|xlsx|xlsb";
    $config['max_size'] = "50000";

    $this->load->library('upload', $config);

    if (!$this->upload->do_upload('archivo')) {
        //*** ocurrio un error
        $data['uploadError'] = $this->upload->display_errors();
        echo $this->upload->display_errors();
        return;
    }

    $this->leer_archivo($this->upload->data('full_path'));
    $this->mostrarTabla(TRUE);
	}

  public function leer_archivo($archivo = '') {
    if (file_exists($archivo)) {
      $objPHPExcel = PHPExcel_IOFactory::load($archivo);
      foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
        $worksheetTitle     = $worksheet->getTitle();
        $highestRow         = $worksheet->getHighestRow(); // e.g. 10
        $highestColumn      = $worksheet->getHighestColumn(); // e.g 'F'
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
        $nrColumns = ord($highestColumn) - 64;
        $b = FALSE;
        $col;
        for ($row = 1; $row <= 1; ++ $row) {
          for ($col = 0; $col < $highestColumnIndex; ++ $col) {
            $cell = $worksheet->getCellByColumnAndRow($col, $row);
            $val = $cell->getValue();
            $val = mb_strtolower($val, 'UTF-8');
            switch ($col) {
              case 0:
                $b = (strpos($val, 'producto') === FALSE) ? TRUE : FALSE ;
                break;
              case 1:
                $b = (strpos($val, 'unidad') === FALSE) ? TRUE : FALSE ;
                break;
              case 2:
                $b = (strpos($val, 'cantidad') === FALSE) ? TRUE : FALSE ;
                break;
              case 3:
                $b = (strpos($val, 'precio') === FALSE) ? TRUE : FALSE ;
                break;
              case 4:
                $b = (strpos($val, 'total') === FALSE) ? TRUE : FALSE ;
                break;
              case 5:
                $b = (strpos($val, 'fondo') === FALSE) ? TRUE : FALSE ;
                break;
              case 6:
                $b = (strpos($val, 'especifico') === FALSE) ? TRUE : FALSE ;
                break;
            }
            if ($b) {
              break;
            }
          }
        }
        if (!$b) {
          for ($row = 2; $row <= $highestRow; ++ $row) {
              $data = array();
              for ($col = 0; $col < $highestColumnIndex; ++ $col) {
                  $cell = $worksheet->getCellByColumnAndRow($col, $row);
                  $val = $cell->getValue();
                  switch ($col) {
                    case 0:
                      $data['nombre_producto'] = trim($val);
                      break;
                    case 1:
                      $data['unidad_medida'] = trim($val);
                      break;
                    case 2:
                      $data['cantidad'] = intval(trim($val));
                      break;
                    case 3:
                      $data['precio'] = floatval(trim($val));
                      break;
                    case 4:
                      $data['total'] = number_format($data['cantidad'] * $data['precio'], 3);
                      break;
                    case 5:
                      $data['fuente_fondos'] = trim($val);
                      break;
                    case 6:
                      $data['objeto_especifico'] = trim($val);
                      break;
                  }
              }
              if (!$this->ArrayIsEmpty($data)) {
                array_push($this->datos, $data);
                array_push($this->aux, $data['fuente_fondos']);
              }
          }
        } else {
          echo "Verifique, el archivo no es compatible";
        }

        break;
      }

      $json_datos = json_encode($this->datos);
      $json_aux = json_encode($this->aux);
      $file1 = 'uploads/datos.json';
      $file2 = 'uploads/fondos.json';
      file_put_contents($file1, $json_datos);
      file_put_contents($file2, $json_aux);
    }
  }

  public function mostrarTabla($mostrar = FALSE) {

    if (empty($this->aux) && empty($this->datos)) {
      if (file_exists("uploads/datos.json") && file_exists("uploads/fondos.json")) {
        $datos_json = file_get_contents("uploads/datos.json");
        $fondos_json = file_get_contents("uploads/fondos.json");
        $this->datos = json_decode($datos_json, true);
        $this->aux = json_decode($fondos_json, true);
      }
    }

    if (!$this->ArrayIsEmpty($this->aux) && count($this->datos) > 0) {
      $template = array(
          'table_open' => '<table class="table table-striped table-bordered">'
      );
      $this->table->set_template($template);
      $this->table->set_heading('Nombre del producto','Unidad Medida', 'Cantidad', 'Precio', 'Total', 'Fuentes de Fondo', 'Objeto Especifico');
      $num = 15;
      $pagination = paginacion('index.php/Bodega/Cargamasiva/index/', count($this->datos), $num, '4');
      $segmento = intval($this->uri->segment(4));

      $data = array_slice($this->datos, $segmento, $num);

      foreach ($data as $dato) {
        $this->table->add_row($dato['nombre_producto'], $dato['unidad_medida'], $dato['cantidad'], number_format($dato['precio'], 3),
                              $dato['total'], $dato['fuente_fondos'], $dato['objeto_especifico']);
      }

      $button = "<a class='btn btn-default' onclick='cargar()'>Cargar datos al sistema</a>" .
                '<div class="mensaje_ajax"></div>' ;

      if ($mostrar) {
        echo $this->table->generate() . $pagination . $button;
      } else {
        return $this->table->generate() . $pagination . $button;
      }
    }
  }

  public function CargaMasiva() {

    date_default_timezone_set('America/El_Salvador');
    set_time_limit(2400);
    $anyo = 20;
    $fecha_actual = date($anyo."y-m-d");

    if (file_exists("uploads/datos.json") && file_exists("uploads/fondos.json")) {
      $datos_json = file_get_contents("uploads/datos.json");
      $fondos_json = file_get_contents("uploads/fondos.json");
      $this->datos = json_decode($datos_json, true);
      $this->aux = json_decode($fondos_json, true);
      unlink("uploads/datos.json");
      unlink("uploads/fondos.json");
    }

    if (count($this->datos) > 0 && !$this->ArrayIsEmpty($this->aux)) {
      array_multisort($this->aux, SORT_ASC, $this->datos);

      $fondos_prev = '';
      $i = 0;
      $id_factura = 0;
      $total = 0;

      while ($dato = current($this->datos)) {

        $id_medida = $this->UnidadMedida->obtenerIdPorNombre($dato['unidad_medida']);
        if ($id_medida == FALSE) {
          $id_medida = $this->UnidadMedida->insertarUnidad(
            array(
              'nombre' => $dato['unidad_medida'],
              'abreviatura' => " ",
            )
          );
        }

        $id_producto = $this->Producto->obtenerIdPorNombre($dato['nombre_producto']);
        if ($id_producto == FALSE) {
          $id_producto = $this->Producto->insertarProducto(
            array(
              'nombre' => $dato['nombre_producto'],
              'id_unidad_medida' => $id_medida,
              'descripcion' => " ",
              'estado' => 'ACTIVO',
              'fecha_caducidad' => "0000-00-00",
              'stock_minimo' => 0,
            )
          );
        }

        $id_detalleproducto = $this->DetalleProducto_model->obtenerDetalleProducto($id_producto, $dato['objeto_especifico']);
        if (!$id_detalleproducto) {
          $id_detalleproducto = $this->DetalleProducto_model->insertarDetalleProducto(
            array(
              'id_especifico' => $dato['objeto_especifico'],
              'id_producto' => $id_producto,
            )
          );
        }

        if ($dato['fuente_fondos'] != $fondos_prev) {

          $id_fuente = $this->Fuentefondos_model->obtenerIdPorNombre($dato['fuente_fondos']);
          if ($id_fuente == FALSE) {
            $id_fuente = $this->Fuentefondos_model->insertarFuente(
              array(
                'nombreFuente' => $dato['fuente_fondos'],
                'codigo' => $dato['fuente_fondos'],
                'descripcion' => ""
              )
            );
            $id_fuente = $this->Fuentefondos_model->obtenerIdPorNombre($dato['fuente_fondos']);
          }

          // Se liquida la factura anterior
          $this->Factura_Model->liquidar($id_factura, $this->Factura_Model->obtenerCorrelativoFuente($id_fuente));

          $id_factura = $this->Factura_Model->insertarFactura(
            array(
              'numero_factura' => $i,
              'id_proveedores' => 0,
              'nombre_entrega' => "",
              'fecha_factura' => $fecha_actual,
              'fecha_ingreso' => $fecha_actual,
              'id_fuentes' => $id_fuente,
              'numero_compromiso' => 0,
              'orden_compra' => 0,
              'id_seccion' => 72,
              'total' => 0,
              'estado' => 'INGRESADA',
              'hora' => date("h:i:sa"),
            )
          );
          #se resetea el total para cada factura
          $total = 0;
        }

        $id_detalle_factura = $this->Detallefactura_Model->insertarDetalleFactura(
          array(
            'id_detalle_factura' => '',
            'cantidad' => $dato['cantidad'],
            'precio' => $dato['precio'],
            'id_factura' => $id_factura,
            'id_detalleproducto' => $id_detalleproducto,
          )
        );
        $total += $dato['cantidad'] * $dato['precio'];

        $this->Detallefactura_Model->cargar($id_detalle_factura, $id_factura, $total);

        $id_kardex = $this->Kardex_model->insertarKardex(
          array(
            'id_detalleproducto' => $id_detalleproducto,
            'cantidad' => $dato['cantidad'],
            'precio' => $dato['precio'],
            'movimiento' => 'ENTRADA',
            'fecha_ingreso' => $fecha_actual,
            'id_fuentes' => $id_fuente,
          )
        );

        $fondos_prev = $dato['fuente_fondos'];
        $next = next($this->datos);
        $i++;
      }
      // Se liquida la ultima factura ingresada
      $id_fuente = $this->Fuentefondos_model->obtenerIdPorNombre($dato['fuente_fondos']);
      $this->Factura_Model->liquidar($id_factura, $this->Factura_Model->obtenerCorrelativoFuente($id_fuente));
    }
  }

  public function ArrayIsEmpty($array) {
    if(is_array($array)) {
      if (empty($array)) {
        return TRUE;
      } else {
        foreach ($array as $key => $value) {
          if (empty($value) && $key) {
            return TRUE;
          }
        }
      }
    } else {
      return TRUE;
    }

    return FALSE;
  }
}
?>
