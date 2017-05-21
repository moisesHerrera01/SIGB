<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notificacion extends CI_Controller {

  public function __construct() {
    parent::__construct();

    $this->load->helper(array('url'));
    $this->load->model(array('Notificacion_model'));
  }

  public function ConsultarNotificaciones(){
    $USER = $this->session->userdata('logged_in');
    $id_compromiso = $this->input->post('id');
    $data = array();

    $notificaciones = $this->Notificacion_model->obtenerNotificaciones($USER['id']);
    if ($notificaciones) {
      foreach ($notificaciones as $not) {
        $data[] = $not;
      }

      print json_encode($data);
    }
  }

  public function TotalNotificaciones() {
    $USER = $this->session->userdata('logged_in');
    print $this->Notificacion_model->totalNotificacion($USER['id']);
  }

  public function RecibirDatos($data = '') {
    if (is_array($data)) {
      $this->Notificacion_model->insertarNotificacion($data);
    }
  }

  public function EliminarDato() {
    $id = $this->input->post('id');
    $this->Notificacion_model->eliminarNotificacion($id);
    print $this->TotalNotificaciones();
  }

}
?>
