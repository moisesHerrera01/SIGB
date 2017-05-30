<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Retiro extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('form', 'paginacion'));
    $this->load->library('table');
    $this->load->model(array('Bodega/Solicitud_Model','Bodega/Detalle_solicitud_producto_model', 'Bodega/Fuentefondos_model', 'Notificacion_model'));
  }

  public function index(){
    if($this->session->userdata('logged_in')){
      $data['title'] = "Retiros";
      $pri=$this->Solicitud_Model->obtenerId();
      $data['js'] = "assets/js/validate/ret.js";
      date_default_timezone_set('America/El_Salvador');
      $anyo=20;
      $fecha_actual=date($anyo."y-m-d");
      $msg = array('alert' => $this->uri->segment(4),'fecha'=>$fecha_actual,'id'=>$pri,'controller'=>'retiro','controller'=>'retiro');

  		$data['body'] = $this->load->view('mensajes', $msg, TRUE) . $this->load->view('Bodega/retiro_view',$msg,TRUE) .
                      "<br><div class='content_table '>" .
                      "<div class='limit-content-title'><span class='icono icon-table icon-title'> Retiros</span></div>".
                      "<div class='limit-content'>" . $this->mostrarTabla() . "</div></div>";
      $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
      $this->load->view('base', $data);
    } else {
      redirect('login/index/error_no_autenticado');
    }
	}


  public function mostrarTabla(){
    $USER = $this->session->userdata('logged_in');

    //$USER = $this->session->userdata('logged_in');
    $modulo=$this->User_model->obtenerModulo('Bodega/Retiro');
    if($USER){

      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'update')) {


          /*
          * Configuracion de la tabla
          */

          $template = array(
              'table_open' => '<table class="table table-striped table-bordered">'
          );
          $this->table->set_template($template);

          /*
          * Filtro a la BD
          */
          /*Obtiene el numero de registros a mostrar por pagina */
          $num = '10';
          $pagination = '';
          $registros = FALSE;

          if ($USER['rol'] == 'COLABORADOR BODEGA' || $USER['rol'] == 'JEFE BODEGA' || $USER['rol'] == 'TECNICO BODEGA' || $USER['rol'] == 'ADMINISTRADOR SICBAF' ) {

            $this->table->set_heading('Id','Número', 'Sección', 'Fuente Fondo','Estado','Prioridad','Comenatario Jefe Unidad','Comentario Director','Editar','Detalle','Liquidar','Acta');

            if ($this->input->is_ajax_request()) {
              if (!($this->input->post('busca') == "")) {
                  $registros = $this->Solicitud_Model->buscarSolicitudes($this->input->post('busca'));
              } else {
                  $registros = $this->Solicitud_Model->obtenerSolicitudesEstadoLimit(array(3, 4), 0, $num, $this->uri->segment(4));
                  $pagination = paginacion('index.php/Bodega/retiro/index/', $this->Solicitud_Model->totalSolicitudesRetiro()->total,
                                $num, '4');
              }
            } else {
                  $registros = $this->Solicitud_Model->obtenerSolicitudesEstadoLimit(array(3, 4), 0, $num, $this->uri->segment(4));
                  $pagination = paginacion('index.php/Bodega/retiro/index/', $this->Solicitud_Model->totalSolicitudesRetiro()->total,
                                $num, '4');
            }

            /*
            * llena la tabla con los datos consultados
            */

            if (!($registros == FALSE)) {
              foreach($registros as $sol) {
                  $fuente = $this->Fuentefondos_model->obtenerFuente($sol->id_fuentes);
                  $seccion = $this->Solicitud_Model->obtenerSeccion($sol->id_seccion);
                  $botones='<a class="icono icon-detalle" href="'.base_url('index.php/Bodega/detalle_retiro/index/'.$sol->id_solicitud.'/'.$modulo.'/').'"></a>';
                  $onClick = "llenarFormulario('solicitud', ['id', 'fecha_solicitud', 'seccion','autocomplete',
                   'numero_solicitud'],
                              [$sol->id_solicitud, '$sol->fecha_solicitud', '$sol->id_seccion', '$seccion',
                              '$sol->numero_solicitud'], 'prioridad', '$sol->prioridad')";
                    if($sol->estado_solicitud=='APROBADA' || $sol->estado_solicitud=='EN DESPACHO'){
                        //$eliminar='<a class="icono icon-eliminar" uri='.base_url('index.php/Bodega/retiro/EliminarDato/'.$sol->id_solicitud).'></a>';
                        $editar='<a class="icono icon-pencil" onClick="'.$onClick.'"></a>';
                        if($this->Solicitud_Model->validarLiquidar($sol->id_solicitud)){
                          $liquidar='<a class="icono icon-liquidar" href="'.base_url('index.php/Bodega/retiro/Liquidar/'.$sol->id_solicitud.'/').'"></a>';
                        }else{
                          $liquidar='<a class="icono icon-lock"></a>';
                        }
                    }else{
                      //$eliminar='<a class="icono icon-denegar"></a>';
                      $editar='<a class="icono icon-denegar"></a>';
                      $liquidar='<a class="icono icon-denegar"></a>';
                    }
                    if ($sol->estado_solicitud=='LIQUIDADA'){
                      $acta = '<a class="icono icon-acta" href="'.base_url('index.php/Bodega/ActaRetiro/index/'.$sol->id_solicitud.'/').'" target="_blank"></a>';
                    } else {
                      $acta = '<a class="icono icon-lock"></a>';
                    }

                  $this->table->add_row($sol->id_solicitud, $sol->numero_solicitud, $seccion, $fuente, $sol->estado_solicitud,
                  $sol->prioridad,$sol->comentario_jefe,$sol->comentario_admin,$editar, $botones,$liquidar,$acta);

              }
            } else {
              $msg = array('data' => "Texto no encontrado", 'colspan' => "7");
              $this->table->add_row($msg);
            }
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
      redirect('/Bodega/UnidadMedidas/index/forbidden');
    }
  } else {
    redirect('login/index/error_no_autenticado');
  }
  }

  /*
  * Actualiza o Registra al sistema
  */
  public function RecibirDatos(){
    $modulo=$this->User_model->obtenerModulo('Bodega/Retiro');
    $USER = $this->session->userdata('logged_in');
    if($USER){
      $data = array(
          'fecha_solicitud' => $this->input->post('fecha_solicitud'),
          'id_seccion' => $this->input->post('seccion'),
          'numero_solicitud' => $this->input->post('numero_solicitud'),
          'prioridad'=>$this->input->post('prioridad'),
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
              $this->Solicitud_Model->actualizarSolicitud($this->input->post('id'),$data);
              redirect('/Bodega/retiro/index/update');
          } else {
                redirect('/Bodega/retiro/index/forbidden');
          }
      }
      if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'insert')) {
        $rastrea['operacion']='INSERTA';
        $rastrea['id_registro']=$this->User_model->obtenerSiguienteIdModuloIncrement('sic_solicitud');
        $this->User_model->insertarRastreabilidad($rastrea);
        $this->Solicitud_Model->insertarSolicitud($data);
        redirect('/Bodega/retiro/index/new');
      } else {
        redirect('/Bodega/retiro/index/forbidden');
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  public function EliminarDato(){
    $modulo=$this->User_model->obtenerModulo('Bodega/Retiro');
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
      $id = $this->uri->segment(4);
      if ($this->Detalle_solicitud_producto_model->existeSolicitud($id)){
        redirect('/Bodega/retiro/index/existeRet');
      }
      else {
        if ($this->User_model->validarAccesoCrud($modulo, $USER['id'], 'delete')) {
          $this->Solicitud_Model->eliminarSolicitud($id);
          $this->User_model->insertarRastreabilidad($rastrea);
          redirect('/Bodega/retiro/index/delete');
        } else {
          redirect('/Bodega/UnidadMedidas/index/forbidden');
        }
      }
    } else {
      redirect('login/index/error_no_autenticado');
    }
  }

  public function Liquidar(){
    $USER = $this->session->userdata('logged_in');
    $id = $this->uri->segment(4);
    $usuario = $this->Solicitud_Model->obtenerSolicitudUsuario($id);
    $estado=$this->Solicitud_Model->retornarEstado($id);
    if($estado=='APROBADA'){
      $this->Notificacion_model->NotificacionSolicitudBodega($id, $USER, 4);
      $this->Solicitud_Model->liquidar($id);
      redirect('/Bodega/kardex/insertarDescargo?controller=retiro&&id='.$id);
    }else{
      redirect('/Bodega/retiro/index/liquidada/'.$id);
    }
  }

  public function Aprobar() {
    $this->load->library('email');
    $id = $this->uri->segment(4);
    $usuario = $this->Solicitud_Model->obtenerSolicitudUsuario($id);
    $estado=$this->Solicitud_Model->retornarEstado($id);
    if ($estado == 'COMPROBADA') {
      $this->Solicitud_Model->aprobar($id);

     //configuracion para gmail
     $configGmail = array(
     'protocol' => 'smtp',
     'smtp_host' => 'ssl://smtp.gmail.com',
     'smtp_port' => 465,
     'smtp_user' => '',
     'smtp_pass' => '',
     'mailtype' => 'html',
     'charset' => 'utf-8',
     'newline' => "\r\n"
     );
     //cargamos la configuración para enviar con gmail
     $this->email->initialize($configGmail);

     $this->email->from('nombre');
     $this->email->to($usuario . "@mtps.gob.sv");
     $this->email->subject('SICBAF: APROBADO');
     $this->email->message('<h2>La solicitud con número '.$id.' ha sido aprobada.</h2>');
     $this->email->send();

     redirect('/Bodega/retiro/index/aprobado/'.$id);

    } else {
      redirect('/Bodega/retiro/index/aprobada/'.$id);
    }

  }

  public function Autocomplete(){
    $registros = '';
    if ($this->input->is_ajax_request()) {
      if (!($this->input->post('autocomplete') == "")) {
          $registros = $this->Solicitud_Model->buscarSecciones($this->input->post('autocomplete'));
      } else {
          $registros = $this->Solicitud_Model->obtenerSecciones();
      }
    } else {
          $registros = $this->Solicitud_Model->obtenerSecciones();
    }

    if ($registros == '') {
      echo '<div id="1" class="suggest-element"><a id="conteo">No se encontraron coincidencias</a></div>';
    }else {
      $i = 1;
      foreach ($registros as $sec) {
        echo '<div id="'.$i.'" class="suggest-element" ida="seccion'.$sec->id_seccion.'"><a id="seccion'.
        $sec->id_seccion.'" data="'.$sec->id_seccion.'"  data1="'.$sec->nombre_seccion.'" >'
        .$sec->nombre_seccion.'</a></div>';
        $i++;
      }
    }
  }

  public function RecibirRetiro() {
    date_default_timezone_set('America/El_Salvador');
    $anyo=20;
    $fecha_actual=date($anyo."y-m-d");
    if ($this->input->post('fecha_inicio')!=NULL && $this->input->post('fuente')!=NULL) {
      if($this->input->post('fecha_fin')==NULL){
        redirect('Bodega/retiro/reporte/'.$this->input->post('fecha_inicio').'/'
        .$fecha_actual.'/'.$this->input->post('fuente'));
      }else{
        redirect('Bodega/retiro/reporte/'.$this->input->post('fecha_inicio').'/'
        .$this->input->post('fecha_fin').'/'.$this->input->post('fuente'));
      }} else {
        redirect('Bodega/retiro/reporte/');
    }
  }

  public function reporte(){

    $data['title'] = "Reporte Salidas";
    $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));
    $data['js'] = 'assets/js/validate/reporte/bodega/salida.js';
    $table = '';
    if (($this->uri->segment(4))!=NULL) {
      $template = array(
          'table_open' => '<table class="table table-striped table-bordered">'
      );
      $this->table->set_template($template);
      $this->table->set_heading('#','Número Especifico', 'Nombre Especifico', 'Saldo','Salida', 'detalle');

      $num = '10';
      $registros = $this->Detalle_solicitud_producto_model->obtenerEspecificosLimit($this->uri->segment(4),
      $this->uri->segment(5),$this->uri->segment(6),$num, $this->uri->segment(7));
      $pagination = paginacion('index.php/Bodega/retiro/reporte/'.$this->uri->segment(4).'/'.$this->uri->segment(5).'/'.$this->uri->segment(6),
                    $this->Detalle_solicitud_producto_model->totalEspecifico($this->uri->segment(4),
                    $this->uri->segment(5)),$num, '7');


      if (!($registros == FALSE)) {
        $fecha_inicio=$this->uri->segment(4);
        $fecha_fin=$this->uri->segment(5);
        $fuente=$this->uri->segment(6);
        $i = 1;
        foreach($registros as $salida) {
          $saldo=0.0;
          $entradas=0.0;
          $salidas=0.0;
          $salidas_rango=0.0;
          $kardex=$this->Detalle_solicitud_producto_model->obtenerKardex();
          foreach ($kardex as $kar) {
            if($kar->id_especifico==$salida->id_especifico){
              if($kar->movimiento=='SALIDA'){
                $salidas=$salidas+$kar->cantidad*$kar->precio;
                if($kar->fecha_ingreso>=$fecha_inicio &&
                 $kar->fecha_ingreso<=$fecha_fin && $kar->id_fuentes==$fuente){
                   $salidas_rango=$salidas_rango+$kar->cantidad*$kar->precio;
                 }
              }else{
                $entradas=$entradas+$kar->cantidad*$kar->precio;
              }
            }
          }
          $saldo=$entradas-$salidas;
          $this->table->add_row($i, $salida->id_especifico,$salida->nombre_especifico,number_format($saldo,3),number_format($salidas_rango,3),
          '<a class="icono icon-detalle" href="'.base_url('index.php/Bodega/retiro/reporteDetalleRetiro/'
            .$salida->id_especifico.'/'.$fecha_inicio.'/'.$fecha_fin.'/'.$fuente.'/').'"></a>');

          $i++;
        }
      } else {
        $msg = array('data' => "No se encontraron resultados", 'colspan' => "6");
        $this->table->add_row($msg);
      }
      $table =  "<div class='content_table '>" .
                "<div class='limit-content-title'><span class='icono icon-table icon-title'> ".$this->Fuentefondos_model->obtenerFuente($this->uri->segment(6)). " " . $this->uri->segment(4) . " - " . $this->uri->segment(5)."</span></div>".
                "<div class='limit-content'>" .
                "<div class='exportar'><a href='".base_url('/index.php/Bodega/retiro/RetiroReporteExcel/'.$this->uri->segment(4).'/'
                .$this->uri->segment(5).'/'.$this->uri->segment(6).'/'.$this->uri->segment(7))."' class='icono icon-file-excel'>
                Exportar Excel</a></div>" . "<div class='table-responsive'>" . $this->table->generate() . "</div>" . $pagination . "</div></div>";
    }

    $data['body'] = $this->load->view('Bodega/Reportes/salidas_view', '',TRUE) . "<br>" . $table;
    $this->load->view('base', $data);
  }
  public function reporteDetalleRetiro(){

    $data['title'] = "Reporte Detalle";
    $table = '';
    if (($this->uri->segment(4))!=NULL) {
      $template = array(
          'table_open' => '<table class="table table-striped table-bordered">'
      );
      $this->table->set_template($template);
      $this->table->set_heading('Solicitud','Fecha Salida', 'Producto', 'Unidad Medida','Cantidad', 'Sub Total');

      $num = '2';
      $registros = $this->Detalle_solicitud_producto_model->obtenerProductosLimit($this->uri->segment(4),
      $this->uri->segment(5),$this->uri->segment(6),$this->uri->segment(7));

      if (!($registros == FALSE)) {
        $i = 1;
        $total_productos=0.0;
        foreach($registros as $prod) {
          $this->table->add_row($prod->numero_solicitud,$prod->fecha_salida,$prod->producto,$prod->unidad,
        $prod->cantidad,$prod->total);
          $total_productos+=$prod->total;
          $i++;
        }
        $msg = array('data' => "Total:", 'colspan' => "5");
        $this->table->add_row($msg,  number_format($total_productos, 3));
      } else {
        $msg = array('data' => "No se encontraron resultados", 'colspan' => "6");
        $this->table->add_row($msg);
      }
      $table = "<div class='exportar icono'><a href='".base_url('/index.php/Bodega/retiro/DetalleRetiroReporteExcel/'
      .$this->uri->segment(4).'/'.$this->uri->segment(5).'/'.$this->uri->segment(6).'/'.$this->uri->segment(7))."' class='icon-file-excel'>
                Exportar Excel</a></div>" . $this->table->generate();
    }

    $data['body'] = $this->load->view('Bodega/Reportes/detalle_salidas_view', '',TRUE) . "<br>" . $table;
    $this->load->view('base', $data);
  }

  public function RetiroReporteExcel() {

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
                 ->setTitle("Reporte de Salidas y Saldos de Bodega de productos por Objeto Especifico.")
                 ->setSubject("Reporte de Salidas y Saldos de Bodega de productos por Objeto Especifico.")
                 ->setDescription("Reporte generado para conciliaciones contables al cierre de cada mes..")
                 ->setKeywords("office PHPExcel php")
                 ->setCategory("Reporte de Salidas y Saldos de Bodega de productos por Objeto Especifico.");

    $objPHPExcel->setActiveSheetIndex(0)
                 ->setCellValue('A1', '#')
                 ->setCellValue('B1', 'Número Especifico')
                 ->setCellValue('C1', 'Nombre Especifico')
                 ->setCellValue('D1', 'Saldo')
                 ->setCellValue('E1', 'Salida');
    $objPHPExcel->getActiveSheet()->getStyle('A1:E1')->applyFromArray($estilo_titulo);

    $registros = $this->Detalle_solicitud_producto_model->obtenerEspecificosTotal($this->uri->segment(4),
    $this->uri->segment(5),$this->uri->segment(6));

    if (!($registros == FALSE)) {
      $fecha_inicio=$this->uri->segment(4);
      $fecha_fin=$this->uri->segment(5);
      $fuente=$this->uri->segment(6);
      $i = 2;
      foreach($registros as $salida) {
        $saldo=0.0;
        $entradas=0.0;
        $salidas=0.0;
        $salidas_rango=0.0;
        $kardex=$this->Detalle_solicitud_producto_model->obtenerKardex();
        foreach ($kardex as $kar) {
          if($kar->id_especifico==$salida->id_especifico){
            if($kar->movimiento=='SALIDA'){
              $salidas=$salidas+$kar->cantidad*$kar->precio;
              if($kar->fecha_ingreso>$fecha_inicio &&
               $kar->fecha_ingreso<$fecha_fin && $kar->id_fuentes==$fuente){
                 $salidas_rango=$salidas_rango+$kar->cantidad*$kar->precio;
               }
            }else{
              $entradas=$entradas+$kar->cantidad*$kar->precio;
            }
          }
        }
        $saldo=$entradas-$salidas;

        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i, $i-1)
                    ->setCellValue('B'.$i, $salida->id_especifico)
                    ->setCellValue('C'.$i, $salida->nombre_especifico)
                    ->setCellValue('D'.$i, $saldo)
                    ->setCellValue('E'.$i, $salidas_rango);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':E'.$i)->applyFromArray($estilo_contenido);
        $i++;
      }

      foreach(range('A','E') as $columnID){
        $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
      }

      $objPHPExcel->setActiveSheetIndex(0);

      header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
      header("Content-Disposition: attachment; filename='reporte_salidas_saldos.xlsx'");
      header('Cache-Control: max-age=0');

      $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
      $objWriter->save('php://output');
    }
  }

  public function DetalleRetiroReporteExcel() {

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
                 ->setTitle("Reporte Detalle de Salidas y Saldos de Bodega de productos por Objeto Especifico.")
                 ->setSubject("Reporte de Detalle Salidas y Saldos de Bodega de productos por Objeto Especifico.")
                 ->setDescription("Reporte generado para conciliaciones contables al cierre de cada mes.")
                 ->setKeywords("office PHPExcel php")
                 ->setCategory("Reporte de DetalleSalidas y Saldos de Bodega de productos por Objeto Especifico.");

    $objPHPExcel->setActiveSheetIndex(0)
                 ->setCellValue('A1', 'Número Solicitud')
                 ->setCellValue('B1', 'Fecha Salida')
                 ->setCellValue('C1', 'Producto')
                 ->setCellValue('D1', 'Unidad Medidad')
                 ->setCellValue('E1', 'Cantidad')
                 ->setCellValue('F1', 'Sub Total');
    $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->applyFromArray($estilo_titulo);

    $registros = $this->Detalle_solicitud_producto_model->obtenerProductosLimit($this->uri->segment(4),
    $this->uri->segment(5),$this->uri->segment(6),$this->uri->segment(7));

    if (!($registros == FALSE)) {
      $i = 2;
      $total=0.0;
      foreach($registros as $prod) {
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i, $prod->numero_solicitud)
                    ->setCellValue('B'.$i, $prod->fecha_salida)
                    ->setCellValue('C'.$i, $prod->producto)
                    ->setCellValue('D'.$i, $prod->unidad)
                    ->setCellValue('E'.$i, $prod->cantidad)
                    ->setCellValue('F'.$i, $prod->total);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->applyFromArray($estilo_contenido);
        $i++;
        $total+=$prod->total;
      }
      $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$i.':E'.$i);
      $objPHPExcel->setActiveSheetIndex(0)
                  ->setCellValue('A'.$i, "TOTAL:")
                  ->setCellValue('F'.$i, $total);
      $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->applyFromArray($estilo_contenido);

      foreach(range('A','F') as $columnID){
        $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
      }

      $objPHPExcel->setActiveSheetIndex(0);

      header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
      header("Content-Disposition: attachment; filename='reporte_detalle_salidas_saldos.xlsx'");
      header('Cache-Control: max-age=0');

      $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
      $objWriter->save('php://output');
    }
  }
}
?>
