<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Proveedores extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('form', 'paginacion'));
    $this->load->library('table');
    $this->load->model('Bodega/Proveedor');
  }

  public function index(){
    $data['title'] = "Proveedores";
    $data['js'] = "assets/js/validate/proveedores.js";

    $msg = array('alert' => $this->uri->segment(4), );

		$data['body'] = $this->load->view('mensajes', $msg, TRUE) . $this->load->view('Bodega/Proveedores',$msg,TRUE)
                    . "<br><div class='content_table table-responsive'>" .
                    "<div class='limit-content-title'><span class='icono icon-table icon-title'> Proveedores</span></div>".
                    "<div class='exportar'><a href='".base_url('/index.php/Bodega/Proveedores/ReporteExcelProveedores/')."'class='icono icon-file-excel'>Exportar Excel</a></div>".
                    "<div class='limit-content'>" . $this->mostrarTabla() . "</div></div>";
    $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
    $this->load->view('base', $data);
	}

  public function mostrarTabla(){
    $USER = $this->session->userdata('logged_in');
    $modulo=$this->User_model->obtenerModulo('Bodega/Proveedores');
    if($USER){
      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'select')) {
    /*
    * Configuracion de la tabla
    */

    $template = array(
        'table_open' => '<table class="table table-striped table-bordered">'
    );
    $this->table->set_template($template);
    $this->table->set_heading('#','Nombre','NIT','Teléfono','Detalle','Modificar', 'Eliminar');

    /*
    * Filtro a la BD
    */
    /*Obtiene el numero de registros a mostrar por pagina */
    $num = '10';
    $pagination = '';
    $registros;
    if ($this->input->is_ajax_request()) {
      if (!($this->input->post('busca') == "")) {
          $registros = $this->Proveedor->buscarProveedores($this->input->post('busca'));
      } else {
          $registros = $this->Proveedor->obtenerProveedoresLimit($num, $this->uri->segment(4));
          $pagination = paginacion('index.php/Bodega/Proveedores/index/', $this->Proveedor->totalProveedores()->total,
                        $num, '4');
      }
    } else {
        $registros = $this->Proveedor->obtenerProveedoresLimit($num, $this->uri->segment(4));
        $pagination = paginacion('index.php/Bodega/Proveedores/index/', $this->Proveedor->totalProveedores()->total,
                      $num, '4');
    }
    /*
    * llena la tabla con los datos consultados
    */
    if (!($registros == FALSE)) {
      foreach($registros as $prov) {
          $onClick = "llenarFormulario('Proveedores', ['id', 'categoria','autocomplete','nombre_contacto','nit','correo','telefono','fax','direccion'],
          [$prov->id_proveedores,'$prov->id_categoria_proveedor', '$prov->nombre_categoria - $prov->rubro - $prov->tipo_empresa', '$prov->nombre_contacto',
          '$prov->nit','$prov->telefono','$prov->fax'],
          false, false,false,['nombreProveedor','correo','direccion'],['$prov->nombre_proveedor','$prov->correo','$prov->direccion'])";
          $this->table->add_row($prov->id_proveedores, $prov->nombre_proveedor, $prov->nit, $prov->telefono,
                          //form_button($btn_act), $form_el,
                          '<a class="icono icon-detalle" href="'.base_url('index.php/Bodega/DetalleProveedor/index/'.$prov->id_proveedores.'/').'"></a>',
                          '<a class="icono icon-actualizar" onClick="'.$onClick.'"></a>',
                          '<a class="icono icon-eliminar" uri='.base_url('index.php/Bodega/Proveedores/EliminarDato/'.$prov->id_proveedores).'></a>');
      }
    } else {
      $msg = array('data' => "Texto no encontrado", 'colspan' => "13");
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
    redirect('/Bodega/Proveedores/index/forbidden');
    }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  /*
  * Actualiza o Registra al sistema
  */
  public function RecibirDatos(){
    $modulo=$this->User_model->obtenerModulo('Bodega/Proveedores');
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $data = array(
          'id_categoria_proveedor' => $this->input->post('categoria'),
          'nombre_proveedor' => $this->input->post('nombreProveedor'),
          'nombre_contacto' => $this->input->post('nombre_contacto'),
          'nit' => $this->input->post('nit'),
          'correo' => $this->input->post('correo'),
          'telefono' => $this->input->post('telefono'),
          'fax' => $this->input->post('fax'),
          'direccion' => $this->input->post('direccion')
      );
      date_default_timezone_set('America/El_Salvador');
      $anyo=20;
      $fecha_actual=date($anyo."y-m-d");
      $hora=date("H:i:s");
      $rastrea = array(
        'id_usuario' =>$USER['id'],
        'id_modulo' =>$modulo,
        'fecha' =>$fecha_actual,
        'hora' =>$hora,
      );
      if (!($this->input->post('id') == '')){
        if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'update')) {
          $rastrea['operacion']='ACTUALIZA';
          $rastrea['id_registro']=$this->input->post('id');
          $this->User_model->insertarRastreabilidad($rastrea);
          $this->Proveedor->actualizarProveedor($this->input->post('id'),$data);
          redirect('/Bodega/Proveedores/index/update');
        } else {
          redirect('/Bodega/Proveedores/index/forbidden');
        }
      }
      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'insert')) {
        $rastrea['operacion']='INSERTA';
        $rastrea['id_registro']=$this->User_model->obtenerSiguienteIdModuloIncrement('sic_proveedores');
        $this->User_model->insertarRastreabilidad($rastrea);
        $this->Proveedor->insertarProveedor($data);
        redirect('/Bodega/Proveedores/index/new');
      } else {
        redirect('/Bodega/Proveedores/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  /*
  * elimina un registro cuando se le pasa por la url el id
  */
  public function EliminarDato(){
    $modulo=$this->User_model->obtenerModulo('Bodega/Proveedores');
    $USER = $this->session->userdata('logged_in');
    date_default_timezone_set('America/El_Salvador');
    $anyo=20;
    $fecha_actual=date($anyo."y-m-d");
    $hora=date("H:i:s");
    $rastrea = array(
      'id_usuario' =>$USER['id'],
      'id_modulo' =>$modulo,
      'fecha' =>$fecha_actual,
      'hora' =>$hora,
      'operacion' =>'ELIMINA',
      'id_registro' =>$this->uri->segment(4),
    );
    if($USER){
      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'delete')) {
        $id = $this->uri->segment(4);
        $this->Proveedor->eliminarProveedor($id);
        $this->User_model->insertarRastreabilidad($rastrea);
        redirect('/Bodega/Proveedores/index/delete');
      } else {
        redirect('/Bodega/Proveedores/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  public function Autocomplete(){
    $registros = '';
    if ($this->input->is_ajax_request()) {
      if (!($this->input->post('autocomplete') == "")) {
          $registros = $this->Proveedor->buscarProveedores($this->input->post('autocomplete'));
      } else {
          $registros = $this->Proveedor->obtenerProveedores();
      }
    } else {
          $registros = $this->Proveedor->obtenerProveedores();
    }
    if ($registros == '') {
      echo '<div id="1" class="suggest-element"><a id="conteo">No se encontraron coincidencias</a></div>';
    }else {
      $i = 1;
      foreach ($registros as $prov) {
        echo '<div id="'.$i.'" class="suggest-element" ida="proveedor'.$prov->id_proveedores.'"><a id="proveedor'.
        $prov->id_proveedores.'" data="'.$prov->id_proveedores.'"  data1="'.$prov->nombre_proveedor.'" >'
        .$prov->nombre_proveedor.'</a></div>';
        $i++;
      }
    }
  }

  public function RecibirFiltro() {

    if ($this->input->post()==NULL) {
      redirect('Bodega/Proveedores/Reporte/');
    } else {
      redirect('Bodega/Proveedores/Reporte/'.$this->input->post('fuente').'/'.$this->input->post('fechaMin').'/'.$this->input->post('fechaMax') );
    }
  }

  public function Reporte(){
    $this->load->model('Bodega/Fuentefondos_model');
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $this->load->library(array('table'));

      $data['title'] = "4-Reporte por Proveedor, Factura y Especifico";
      $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
      $data['js'] = 'assets/js/validate/reporte/bodega/general.js';

      $template = array(
          'table_open' => '<table class="table table-striped table-bordered">'
      );
      $this->table->set_template($template);
      $this->table->set_heading('Fecha Factura', 'Numero Factura', 'Compromiso', 'Proveedor', 'Objeto Especifico',
                                'Total OE');

      $num = 12;
      $total_registros = $this->Proveedor->TotalReporteProveedores($this->uri->segment(4), $this->uri->segment(5), $this->uri->segment(6));
      $pagination = paginacion('index.php/Bodega/Proveedores/Reporte/' .$this->uri->segment(4). '/' .$this->uri->segment(5). '/' . $this->uri->segment(6),
                    $total_registros, $num, '7');

      if ($total_registros > 0) {

        $registros = $this->Proveedor->ReporteProveedores($this->uri->segment(4), $this->uri->segment(5), $this->uri->segment(6), $num, $this->uri->segment(7));

        $total = 0;
        while ($registro = current($registros)) {
          $this->table->add_row($registro['fecha_factura'], $registro['numero_factura'], $registro['numero_compromiso'],
                                $registro['nombre_proveedor'], $registro['id_especifico'], number_format($registro['total'], 3));

          $total += $registro['total'];

          $next = next($registros);

          if ($next != FALSE) {
            if($registro['id_factura'] != $next['id_factura'] && $total != 0){
              $msg = array('data' => "Total factura:", 'colspan' => "5");
              $this->table->add_row($msg,  number_format($total, 3));
              $total = 0;
            }
          } else {
            $msg = array('data' => "Total factura:", 'colspan' => "5");
            $this->table->add_row($msg,  number_format($total, 3));
            $total = 0;
          }

        }
      } else {
        $msg = array('data' => "No se encontraron resultados", 'colspan' => "6");
        $this->table->add_row($msg);
      }

      $data['body'] = $this->load->view('Bodega/Reportes/filtroFuentes_view', array('url' => '/Bodega/Proveedores/RecibirFiltro','title'=>$data['title']) ,TRUE) . "<br>" .
                      "<div class='content_table'>" .
                      "<div class='limit-content-title'><span class='icono icon-table icon-title'> ".$this->Fuentefondos_model->obtenerFuente($this->uri->segment(4)). " " . $this->uri->segment(5) ." - ". $this->uri->segment(6) ."</span></div>".
                      "<div class='limit-content'>" .
                     "<div class='exportar'>
                        <a href='".base_url('/index.php/Bodega/Proveedores/ReporteExcel/'.$this->uri->segment(4).'/'.$this->uri->segment(5).'/'.$this->uri->segment(6))."'
                        class='icono icon-file-excel'> Exportar Excel</a>
                     </div>" . "<div class='table-responsive'>" . $this->table->generate() . "</div>" . $pagination . "</div></div>";

      $this->load->view('base', $data);
    } else {
      redirect('login/index/forbidden');
    }
  }

  public function ReporteExcel() {
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
    						 ->setTitle("Reporte por Proveedor, Factura y Especifico")
    						 ->setSubject("Reporte por Proveedor, Factura y Especifico")
    						 ->setDescription("Reporte por Proveedor, Factura y Especifico.")
    						 ->setKeywords("office PHPExcel php")
    						 ->setCategory("Reporte por Proveedor, Factura y Especifico");
    $objPHPExcel->setActiveSheetIndex(0)
                 ->setCellValue('A1', 'Fecha Factura')
                 ->setCellValue('B1', 'Numero Factura')
                 ->setCellValue('C1', 'Compromiso')
                 ->setCellValue('D1', 'Proveedor')
                 ->setCellValue('E1', 'Objeto Especifico')
                 ->setCellValue('F1', ' Total OE');
    $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->applyFromArray($estilo_titulo);

    $total_registros = $this->Proveedor->TotalReporteProveedores($this->uri->segment(4), $this->uri->segment(5), $this->uri->segment(6));

    if ($total_registros > 0) {

      $registros = $this->Proveedor->ReporteProveedores($this->uri->segment(4), $this->uri->segment(5), $this->uri->segment(6));

      $total = 0;
      $i = 2;
      while ($registro = current($registros)) {

        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i, $registro['fecha_factura'])
                    ->setCellValue('B'.$i, $registro['numero_factura'])
                    ->setCellValue('C'.$i, $registro['numero_compromiso'])
                    ->setCellValue('D'.$i, $registro['nombre_proveedor'])
                    ->setCellValue('E'.$i, $registro['id_especifico'])
                    ->setCellValue('F'.$i, $registro['total']);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':f'.$i)->applyFromArray($estilo_contenido);

        $total += $registro['total'];

        $next = next($registros);

        if ($next != FALSE) {
          if($registro['id_factura'] != $next['id_factura'] && $total != 0){
            $i++;
            $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$i.':E'.$i);
            $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A'.$i, "Total factura:")
                        ->setCellValue('F'.$i, $total);
            $total = 0;
            $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':f'.$i)->applyFromArray($estilo_contenido);
          }
        } else {
          $i++;
          $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$i.':E'.$i);
          $objPHPExcel->setActiveSheetIndex(0)
                      ->setCellValue('A'.$i, "Total factura:")
                      ->setCellValue('F'.$i, $total);
          $total = 0;
          $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':f'.$i)->applyFromArray($estilo_contenido);
        }
        $i++;
      }
    } else {
      $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A2:F2');
      $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', "No se encontraron resultados");
      $objPHPExcel->getActiveSheet()->getStyle('A2:f2')->applyFromArray($estilo_contenido);
    }

    foreach(range('A','F') as $columnID){
      $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
    }

    $objPHPExcel->setActiveSheetIndex(0);

    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header("Content-Disposition: attachment; filename='reporte_proveedores.xlsx'");
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');
  }


  public function ReporteExcelProveedores() {
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
                   ->setTitle("Reporte de proveedores.")
                   ->setSubject("Reporte de proveedores.")
                   ->setDescription("Reporte de proveedores. ")
                   ->setKeywords("office PHPExcel php")
                   ->setCategory("Reporte de proveedores.");

      $objPHPExcel->setActiveSheetIndex(0)
                   ->setCellValue('A1', 'ID')
                   ->setCellValue('B1', 'Nombre proveedor')
                   ->setCellValue('C1', 'Nombre contacto')
                   ->setCellValue('D1', 'NIT')
                   ->setCellValue('E1', 'Correo Electrónico')
                   ->setCellValue('F1', 'Teléfono')
                   ->setCellValue('G1', 'Fax')
                   ->setCellValue('H1', 'Dirección')
                   ->setCellValue('I1', 'Categoría')
                   ->setCellValue('J1', 'Tipo Servicio')
                   ->setCellValue('K1', 'Tipo Empresa');

      $objPHPExcel->getActiveSheet()->getStyle('A1:K1')->applyFromArray($estilo_titulo);

      $registros = $this->Proveedor->obtenerProveedores();
      if (!($registros == FALSE)) {
        $i = 2;
        foreach($registros as $pro) {
          $objPHPExcel->setActiveSheetIndex(0)
                      ->setCellValue('A'.$i, $pro->id_proveedores)
                      ->setCellValue('B'.$i, $pro->nombre_proveedor)
                      ->setCellValue('C'.$i, $pro->nombre_contacto)
                      ->setCellValue('D'.$i, $pro->nit)
                      ->setCellValue('E'.$i, $pro->correo)
                      ->setCellValue('F'.$i, $pro->telefono)
                      ->setCellValue('G'.$i, $pro->fax)
                      ->setCellValue('H'.$i, $pro->direccion)
                      ->setCellValue('I'.$i, $pro->nombre_categoria)
                      ->setCellValue('J'.$i, $pro->rubro)
                      ->setCellValue('K'.$i, $pro->tipo_empresa);

          $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':K'.$i)->applyFromArray($estilo_contenido);
          $i++;
        }

        foreach(range('A','K') as $columnID){
          $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        }

        $objPHPExcel->setActiveSheetIndex(0);

        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename='reporte_proveedores.xlsx'");
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
