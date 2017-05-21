<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reporte_estadistico extends CI_Controller {

  public function __construct() {
    parent::__construct();
    if($this->session->userdata('logged_in') == FALSE){
      redirect('login/index/error_no_autenticado');
    }
    $this->load->helper(array('form', 'paginacion'));
    $this->load->library('table');
    $this->load->model(array('ActivoFijo/Subcategoria_Model'));
  }

  public function RecibirDatos() {
    $USER = $this->session->userdata('logged_in');
    if($USER){
      if ($this->input->post('fechaMin')!=NULL && $this->input->post('fechaMax')!=NULL) {
          redirect('ActivoFijo/Reportes/Reporte_estadistico/Reporte/'.$this->input->post('fechaMin').'/'.$this->input->post('fechaMax'));
        } else {
          $data = array(
            'title' => "Reporte Estadistico",
            'menu' => $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1)),
            'body' => $this->load->view('ActivoFijo/Reportes/reporte_estadistico_view', array('result' => FALSE), TRUE)
          );
          $this->load->view('base', $data);
      }
    } else {
      redirect('login');
    }
  }

  public function Reporte() {
    $USER = $this->session->userdata('logged_in');
    if($USER){
      if ($this->uri->segment(6) != NULL) {
        $data['title'] = "Reporte Estadistico";
        $data['menu'] = $this->menu_dinamico->menus($this->session->userdata('logged_in'),$this->uri->segment(1));

        $tipos_mov = array(
          'nuevo' => 2,
          'uso' => 14,
          'obsoleto' => 3,
          'descargo' => 12
        );

        $subcategorias = array(
          'pc1' => 1,
          'pc2' => 2,
          'pc3' => 3,
          'pc4' => 166,
          'ups' => 4,
          'impresor' => 7,
          'scanner' => 8,
          'laptop' => 5,
          'escritorio' => 113,
          'silla' => 116
        );

        $oficinas = array(
          'regionales' => array(52,53,54,55,56,57,58,59,60,61,65,66),
          'recreativos' => array(5,6,8,9)
        );

        $fecha = $this->uri->segment(6);

        $i = 0;
        $j = 0;
        $result;

        foreach ($subcategorias as $sub) {

          $j = 0;
          foreach ($oficinas as $dir) {

            $nuevo = 0;
            $uso = 0;
            $obsoleto = 0;
            $descargo = 0;

            foreach ($dir as $seccion) {

              $bienes = $this->Subcategoria_Model->obtenerEstadisticoSubCategoria($sub, $seccion, $fecha);

              if ($bienes) {

                foreach ($bienes as $bien) {

                  switch ($bien->id_tipo_movimiento) {
                    case $tipos_mov['nuevo']:
                      $fechalim = $this->FechaAnterior($bien->fecha_guarda);
                      if ($fecha <= $fechalim) {
                        $nuevo++;
                      } else {
                        $uso++;
                      }
                      break;
                    case $tipos_mov['uso']:
                      $uso++;
                      break;
                    case $tipos_mov['obsoleto']:
                      $obsoleto++;
                      break;
                    case $tipos_mov['descargo']:
                      $descargo++;
                      break;
                  }

                }

              }

            }

            $result[$i][$j] = $nuevo;
            $result[$i][$j+1] = $uso;
            $result[$i][$j+2] = $obsoleto;
            $result[$i][$j+3] = $descargo;

            $j+=4;
          }

          $i++;
        }

        for ($i=0; $i < 8; $i++) {
          $result[3][$i] = $result[0][$i] + $result[1][$i] + $result[2][$i] + $result[3][$i];
        }

        unset($result[0], $result[1], $result[2]);

        if ('Imprimir' == $this->uri->segment(7)) {
          $this->load->view('ActivoFijo/Reportes/imprimir_reporte_estadistico_view', array('result' => $result));
        } else {
          $data['body'] = $this->load->view('ActivoFijo/Reportes/reporte_estadistico_view', array('result' => $result), TRUE);
          $this->load->view('base', $data);
        }
      } else {
        redirect('ActivoFijo/Reportes/Reporte_estadistico/RecibirDatos');
      }


    } else {
      redirect('login');
    }
  }

  public function FechaAnterior($fecha) {
    $time = strtotime($fecha);
    $newTime = date('Y-m-d', $time);
    $nuevafecha = strtotime ( '-1 month' , strtotime ( $newTime ) ) ;
    return date ( 'Y-m-d' , $nuevafecha );
  }

}

?>
