<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->helper(array('form', 'fecha', 'date'));
    $this->load->library(array('form_validation'));
    $this->load->model('User_model');
  }

  public function index() {
    $USER = $this->session->userdata('logged_in');
    if (!$USER) {
      $data['title'] = "Login";
      $data['msg'] = $this->load->view('mensajes', array('alert' => $this->uri->segment(3), 'controller' => 'login'), TRUE);
      $this->load->view('login_view', $data);
    } else {
      redirect('dashboard'.'/');
    }
  }

  public function verificaLogin() {

    $this->form_validation->set_rules('username', 'Username', 'trim|required|min_length[5]|max_length[50]');
    $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[3]|max_length[50]|callback_check_database');
    //$this->form_validation->set_rules('modulo', 'Modulo', 'trim|required');

    if ($this->form_validation->run() == FALSE) {
      redirect('login/index/error_autenticar');
    } else {
       if ($this->session->userdata('logged_in')) {
         $user = $this->session->userdata('logged_in');
          $segura=TRUE;
          $password = $this->input->post('password');
          if(strlen($password) < 6){
                $segura=FALSE;
          }
          if(strlen($password) > 16){
               $segura=FALSE;
          }
          if (!preg_match('`[a-z]`',$password)){
                $segura=FALSE;
          }
          if (!preg_match('`[A-Z]`',$password)){
                $segura=FALSE;
          }
          if (!preg_match('`[0-9]`',$password)){
                $segura=FALSE;
          }
          if($segura=TRUE){
            redirect('dashboard/index/');
          }else{
              redirect('dashboard/index/inseguro');
          }
       }
    }

  }

  public function check_database($password) {
    if ($password != '') {
      $username = $this->input->post('username');

      $user = $this->User_model->login(array('username' => $username, 'password' => $password));

      if (!$user) {

        $active = '';

        if ($active == "login") {
          $user = TRUE;
        } else {
          return FALSE;
        }

      }

      $user_data = $this->User_model->dataUser($username);

      if ($user_data) {
        $sess = array(
          'id' => $user_data->id_usuario,
          'nombre_completo' => $user_data->nombre_completo,
          'usuario' => $user_data->usuario,
          'id_seccion' => $user_data->id_seccion,
          'estado' => $user_data->estado,
          'id_rol'=> $user_data->id_rol,
          'rol'=>$user_data->nombre_rol,
          'id_empleado'=>$user_data->id_empleado,
          'nombre_empleado' =>$user_data->nombre_empleado,
          'linea_trabajo'=>$user_data->linea_trabajo,
          'cargo_funcional'=>$user_data->cargo_funcional
        );

        $this->session->set_userdata('logged_in', $sess);

        return TRUE;
      } else {
        redirect('login/index/error_rol');
      }

    }
  }

  public function logout() {
    if($this->session->userdata('logged_in')){
      $this->session->unset_userdata('logged_in');
      session_destroy();
      redirect('login/index/cerrar', 'refresh');
    }
  }
}

?>
