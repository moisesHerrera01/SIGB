<?php
  class Prueba_model extends CI_Model{

    function __construct() {
        parent::__construct();
    }

    public function obtener(){
      return $mtps->get('org_departamento')->result();
    }
  }
?>
