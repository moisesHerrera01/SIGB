<?php
  class Prueba_model extends CI_Model{

    function __construct() {
        parent::__construct();
    }

    public function obtener(){
      return $mtps->get('mtps.org_departamento')->result();
    }
  }
?>
