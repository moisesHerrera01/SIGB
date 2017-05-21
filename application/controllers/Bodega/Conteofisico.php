<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ConteoFisico extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('form', 'paginacion'));
    $this->load->library(array('table'));
    $this->load->model(array('Bodega/Conteofisico_model', 'Bodega/Producto', 'Bodega/DetalleConteoFisico_model',
                      'Bodega/Especifico', 'Bodega/DetalleProducto_model', 'Bodega/Kardex_model', 'Bodega/UnidadMedida'));
  }

  public function index(){

    $data['title'] = "Conteo Fisico";
    $data['js'] = "assets/js/validate/cf.js";

    $msg = array('alert' => $this->uri->segment(5), );

		$data['body'] = $this->load->view('mensajes', $msg, TRUE) . $this->load->view('Bodega/conteoFisico_view',$msg,TRUE) .
                    "<br><div class='content_table'>" .
                    "<div class='limit-content-title'><span class='icono icon-table icon-title'> Conteo Fisico </span></div>".
                    "<div class='limit-content'>" . $this->mostrarTabla() . "</div>";
    $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'), $this->uri->segment(1));
    $this->load->view('base', $data);
	}

  public function mostrarTabla(){
    $USER = $this->session->userdata('logged_in');
    if($USER){
      /*
      * Configuracion de la tabla
      */

      $template = array(
          'table_open' => '<table class="table table-striped table-bordered">'
      );
      $this->table->set_template($template);
      $this->table->set_heading('#','Nombre', 'Fecha Inicial', 'Fecha Final', 'Descripción', 'Modificar', 'Detalle');

      /*
      * Filtro a la BD
      */

      /*Obtiene el numero de registros a mostrar por pagina */
      $num = '5';
      $pagination = '';
      $registros;
      if ($this->input->is_ajax_request()) {
        if (!($this->input->post('busca') == "")) {
            $registros = $this->Conteofisico_model->buscarConteoNombre($this->input->post('busca'));
        } else {
            $registros = $this->Conteofisico_model->obtenerConteosLimit($num, $this->uri->segment(4));
            $pagination = paginacion('index.php/Bodega/ConteoFisico/index/', $this->Conteofisico_model->totalConteos(),
                          $num, '4');
        }
      } else {
            $registros = $this->Conteofisico_model->obtenerConteosLimit($num, $this->uri->segment(4));
            $pagination = paginacion('index.php/Bodega/ConteoFisico/index/', $this->Conteofisico_model->totalConteos(),
                          $num, '4');
      }

      /*
      * llena la tabla con los datos consultados
      */

      if (!($registros == FALSE)) {
        $i = 1;
        foreach($registros as $conteo) {
            $onClick = "llenarFormulario('UnidadMedida', ['nombre', 'fecha_inicial', 'fecha_final', 'descripcion'],
                        ['$conteo->nombre_conteo', '$conteo->fecha_inicial' , '$conteo->fecha_final', '$conteo->descripcion'])";

            $this->table->add_row($i, $conteo->nombre_conteo, $conteo->fecha_inicial, $conteo->fecha_final, $conteo->descripcion,
                            //form_button($btn_act), $form_el,
                            '<a class="icono icon-actualizar" onClick="'.$onClick.'"></a>',
                            '<a class="icono icon-detalle" href="'.base_url('index.php/Bodega/DetalleConteo/index/'.
                            str_replace(" ", "_", $conteo->nombre_conteo).'/').'"></a>');

            $i++;
        }
      } else {
        $msg = array('data' => "No se encontraron resultados", 'colspan' => "7");
        $this->table->add_row($msg);
      }

      /*
      * vuelve a verificar para mostrar los datos
      */
      if ($this->input->is_ajax_request()) {
        echo "<div class='table-responsive'>" . $this->table->generate() . "</div>" . $pagination;
      } else {
        return "<div class='table-responsive'>" . $this->table->generate() . "</div>" . $pagination;
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  /*
  * Actualiza o Registra al sistema
  */
  public function RecibirDatos(){
    $modulo=$this->User_model->obtenerModulo('Bodega/ConteoFisico');
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $data = array(
          'nombre_conteo' => $this->input->post('nombre'),
          'fecha_inicial' => $this->input->post('fecha_inicial'),
          'fecha_final' => $this->input->post('fecha_final'),
          'descripcion' => $this->input->post('descripcion')
      );

      if (!($this->Conteofisico_model->obtenerConteo($data['nombre_conteo'])) == '') {
        if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'update')) {
          $this->Conteofisico_model->actualizarConteo($this->input->post('nombre'), $data);
          redirect('/Bodega/ConteoFisico/index/'.$data['nombre_conteo'].'/update');
        } else {
          redirect('/Bodega/UnidadMedidas/index/forbidden');
        }
      }

      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'insert')) {
        $this->Conteofisico_model->insertarConteo($data);
        redirect('/Bodega/ConteoFisico/index/'.$data['nombre_conteo'].'/new');
      } else {
        redirect('/Bodega/UnidadMedidas/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  /*
  * elimina un registro cuando se le pasa por la url el id

  public function EliminarDato(){
    $id = $this->uri->segment(4);
    $this->UnidadMedida->eliminarUnidad($id);
    redirect('/Bodega/UnidadMedidas/index/delete');
  }
  */

  public function Autocomplete(){
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $registros = '';
      if ($this->input->is_ajax_request()) {
        if (!($this->input->post('autocomplete') == "")) {
            $registros = $this->Conteofisico_model->buscarConteoNombre($this->input->post('autocomplete'));
        } else {
            $registros = $this->Conteofisico_model->obtenerConteos();
        }
      } else {
            $registros = $this->Conteofisico_model->obtenerConteos();
      }

      if ($registros == '') {
        echo '<div class="suggest-element"><a id="conteo">No se encontraron coincidencias</a></div>';
      }else {
        foreach ($registros as $conteo) {
          $nombre = str_replace(" ", "_", $conteo->nombre_conteo);
          echo '<div class="suggest-element" ida="conteo'.$nombre.'"><a id="conteo'.
          $nombre.'" data1="'.$conteo->nombre_conteo.'">'.$conteo->nombre_conteo.'</a></div>';
        }
      }
    } else {
      redirect('login');
    }
  }

  public function RecibirConteo() {
    $USER = $this->session->userdata('logged_in');
    if($USER){
      if (($this->input->post()) != '') {
        $conteo = str_replace(" ", "_", $this->input->post('conteo'));
        redirect('Bodega/ConteoFisico/Reporte/'.$conteo);
      } else {
        redirect('Bodega/ConteoFisico/Reporte/');
      }
    } else {
      redirect('login');
    }
  }

  public function reporte(){
    $nom_conteo = str_replace("_", " ", $this->uri->segment(4));
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $data['title'] = "Reporte Conteo Fisico";
      $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
      $table = '';
      if (($nom_conteo) != '') {
        $template = array(
            'table_open' => '<table class="table table-striped table-bordered">'
        );
        $this->table->set_template($template);
        $this->table->set_heading('#','Especifico', 'Nombre del producto', 'Unidad Medida', 'Fuente de Fondos', 'Conteo',
                                  'Contador', 'Contador Sistema','Diferencia');

        $num = '15';
        $registros = $this->DetalleConteoFisico_model->obtenerDetalleConteosLimit($nom_conteo, $num, $this->uri->segment(5));
        $pagination = paginacion('index.php/Bodega/ConteoFisico/reporte/',
                      $this->DetalleConteoFisico_model->totalDetalleConteo($nom_conteo), $num, '5');

        $fecha = $this->Conteofisico_model->obtenerFechaConteo($nom_conteo);
        if (!($registros == FALSE)) {
          $i = 1;
          foreach($registros as $conteo) {
            $producto = $this->Producto->obtenerTodoProducto($conteo->id_producto);
            $unidad = $this->UnidadMedida->obtenerUnidad($producto->id_unidad_medida);
            $fuente = $this->Kardex_model->obtenerFuenteFondo($conteo->id_detalleproducto, $fecha);
            $nombre_especifico = $this->Especifico->obtenerEspecifico($conteo->id_especifico);
            $existencia = intval($this->Kardex_model->obtenerExistencias($conteo->id_detalleproducto, $fecha));
            $this->table->add_row($i, $conteo->id_especifico, $producto->nombre, $unidad, $fuente, $nom_conteo,
                                  $conteo->cantidad, $existencia,  $conteo->cantidad - $existencia);

            $i++;
          }
        } else {
          $msg = array('data' => "No se encontraron resultados", 'colspan' => "9");
          $this->table->add_row($msg);
        }
        $table = "<div class='content_table '>" .
                  "<div class='limit-content-title'><span class='icono icon-table icon-title'> ".$nom_conteo."</span></div>".
                  "<div class='limit-content'>" .
                  "<div class='exportar'><a href='".base_url('/index.php/Bodega/ConteoFisico/ReporteExcel/'.$nom_conteo)."' class='icono icon-file-excel'>
                  Exportar Excel</a></div>" . "<div class='table-responsive'>" . $this->table->generate() . "</div>" . $pagination . "</div></div>";
      }

  		$data['body'] = $this->load->view('Bodega/Reportes/conteoFisico_view', '',TRUE) . "<br>" .
                      $table;
      $this->load->view('base', $data);
    } else {
      redirect('login/index/forbidden');
    }
	}

  public function ReporteExcel() {
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $this->load->library(array('excel'));

      $estilo_titulo = array(
        'font' => array(
          'name' => 'Calibri',
          'bold' => TRUE,
          'size' => 12,
        ),
        'borders' => array(
          'allborders' => array(
          'style' => PHPExcel_Style_Border::BORDER_THICK
          ),
  		    'color' => array('rgb' => '676767'),
        ),
  	    'alignment' => array(
          'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
          'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
          'rotation' => 0,
          'wrap' => TRUE,
        ),
      );


      $estilo_contenido = array(
        'font' => array(
          'name' => 'Calibri',
          'bold' => FALSE,
          'size' => 11,
        ),
        'borders' => array(
          'allborders' => array(
          'style' => PHPExcel_Style_Border::BORDER_THIN
          ),
  		    'color' => array('rgb' => '676767'),
        ),
        'alignment' => array(
          'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
          'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
          'rotation' => 0,
          'wrap' => TRUE,
        ),
      );

      $objPHPExcel = new PHPExcel();
      $objPHPExcel->getProperties()->setCreator("SICBAF")
      						 ->setLastModifiedBy("SICBAF")
      						 ->setTitle("Reporte comparación conteo físico")
      						 ->setSubject("Reporte comparación conteo físico")
      						 ->setDescription("Reporte generado para comprarar el conteo fisico con los registros del sistema.")
      						 ->setKeywords("office PHPExcel php")
      						 ->setCategory("Reporte comparación conteo físico");
      $objPHPExcel->setActiveSheetIndex(0)
                   ->setCellValue('A1', '#')
                   ->setCellValue('B1', 'Especifico')
                   ->setCellValue('C1', 'Nombre del producto')
                   ->setCellValue('D1', 'Unidad Medida')
                   ->setCellValue('E1', 'Fuente de Fondos')
                   ->setCellValue('F1', 'Conteo')
                   ->setCellValue('G1', 'Contador')
                   ->setCellValue('H1', 'Contador Sistema')
                   ->setCellValue('I1', 'Diferencia');
      $objPHPExcel->getActiveSheet()->getStyle('A1:I1')->applyFromArray($estilo_titulo);

      $registros = $this->DetalleConteoFisico_model->obtenerDetalleConteosTotal($this->uri->segment(4));
      $fecha = $this->Conteofisico_model->obtenerFechaConteo($this->uri->segment(4));

      if (!($registros == FALSE)) {
        $i = 2;
        foreach($registros as $conteo) {
          $producto = $this->Producto->obtenerTodoProducto($conteo->id_producto);
          $unidad = $this->UnidadMedida->obtenerUnidad($producto->id_unidad_medida);
          $fuente = $this->Kardex_model->obtenerFuenteFondo($conteo->id_detalleproducto, $fecha);
          $nombre_especifico = $this->Especifico->obtenerEspecifico($conteo->id_especifico);
          $existencia = intval($this->Kardex_model->obtenerExistencias($conteo->id_detalleproducto, $fecha));

          $objPHPExcel->setActiveSheetIndex(0)
                      ->setCellValue('A'.$i, $i-1)
                      ->setCellValue('B'.$i, $conteo->id_especifico)
                      ->setCellValue('C'.$i, $producto->nombre)
                      ->setCellValue('D'.$i, $unidad)
                      ->setCellValue('E'.$i, $fuente)
                      ->setCellValue('F'.$i, $this->uri->segment(4))
                      ->setCellValue('G'.$i, $conteo->cantidad)
                      ->setCellValue('H'.$i, $existencia)
                      ->setCellValue('I'.$i, $conteo->cantidad - $existencia);
          $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->applyFromArray($estilo_contenido);
          $i++;
        }

        foreach(range('A','I') as $columnID){
          $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        }

        $objPHPExcel->setActiveSheetIndex(0);

        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename='comparacion_conteo_fisico.xlsx'");
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
      }
    } else {
      redirect('login');
    }
  }
}
?>
