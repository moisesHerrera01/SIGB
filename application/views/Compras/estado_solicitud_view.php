<?php
  $atributos = array(
    'class' => 'form-horizontal',
    'role' => 'form'
  );
  echo $this->breadcrumb->build_breadcrump($this->uri->uri_string());
?>
