<?php

  $atributos = array(
    'class' => 'form-horizontal',
    'role' => 'form'
  );

  echo $this->breadcrumb->build_breadcrump($this->uri->uri_string());

  echo "<div class='content-form'>";
    echo "<div class='limit-content-title'>";
      echo "<span class='icono icon-paste icon-title'> Especifico</span>";
    echo "</div>";
    echo "<div class='limit-content'>";

      echo form_open("/Bodega/Especificos/RecibirDatos", $atributos);

      $id_espe = array(
          'name' => 'id_especifico',
          'placeholder' => 'Escribe Número de Especifico',
          'type' => 'number',
          'class' => "form-control"
      );

      $nom = array(
          'name' => 'nombre',
          'placeholder' => 'Escribe Nombre',
          'type' => 'text',
          'pattern'=>'[A-Z]',
          'class' => "form-control"
      );

      $proceso= array(
          'default'=>'SELECCIONE TIPO DE PROCESO',
          'BODEGA' => 'BODEGA',
          'COMPRAS' => 'COMPRAS',
      );


      $atriLabel = array('class' => 'col-lg-2 control-label');

      $button = array('class' => 'btn btn-success',);

      echo "<div class='form-group'>";
        echo form_label('ID:', 'id_espe', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($id_espe);
        echo "</div>";
      echo "</div>";

      echo "<div class='form-group'>";
        echo form_label('Nombre:', 'nom', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($nom);
        echo "</div>";
      echo "</div>";

      echo "<div class='form-group'>";
        echo form_label('Proceso:', 'proceso', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_dropdown('proces', $proceso, 'default', array('class' => 'form-control'));
        echo "</div>";
      echo "</div>";


      echo form_hidden('id');

      echo form_submit('','Guardar', $button);

      echo "<a class=\"btn btn-warning\" onclick=\"limpiar(['id_especifico', 'nombre'],['proces'])\">Limpiar</a>";

      echo form_close();
    echo "</div>";
  echo "</div>";
  $buscar = array(
    'name' => 'buscar',
    'type' => 'search',
    'placeholder' => 'Buscar',
    'class' => 'form-control',
    'autocomplete' => 'off',
    'id' => 'buscar',
    'url' => 'index.php/Bodega/Especificos/mostrarTabla'
  );

  echo "<div class='content_buscar'>";
  echo form_input($buscar);
  echo "</div>";
?>
