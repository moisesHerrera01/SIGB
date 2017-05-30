<?php

  $atributos = array(
    'class' => 'form-horizontal stepMe',
    'role' => 'form'
  );

  echo $this->breadcrumb->build_breadcrump($this->uri->uri_string());

  echo "<div class='content-form'>";
    echo "<div class='limit-content-title'>";
      echo "<span class='icono icon-paste icon-title'> Categoría Proveedor</span>";
    echo "</div>";
    echo "<div class='limit-content'>";
      echo form_open("/Bodega/Categoria_proveedor/RecibirDatos", $atributos);

      $nom = array(
          'name' => 'nombre',
          'placeholder' => 'Escribe Nombre de la categoría',
          'class' => "form-control"
      );

      $tipo= array(
          'default'=>'SELECCIONE EL TIPO DE EMPRESA',
          'MICRO_EMPRESA' => 'MICRO_EMPRESA',
          'PEQUEÑA_EMPRESA' => 'PEQUEÑA_EMPRESA',
          'MEDIANA_EMPRESA'=>'MEDIANA_EMPRESA',
          'GRAN_EMPRESA'=>'GRAN_EMPRESA',
          'OTROS_CONTRIBUYENTES'=>'OTROS_CONTRIBUYENTES',
      );

      $rubro= array(
          'default'=>'SELECCIONE TIPO DE SERVICIO',
          'BIENES' => 'BIENES',
          'SERVICIOS' => 'SERVICIOS',
          'CONSULTORES'=>'CONSULTORES',
          'OBRA'=>'OBRA',
      );


      $atriLabel = array('class' => 'col-lg-2 control-label');

      $button = array('class' => 'btn btn-success',);

      echo "<div class='form-group'>";
        echo form_label('Nombre:', 'nom', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($nom);
        echo "</div>";
      echo "</div>";

      echo "<div class='form-group'>";
        echo form_label('Tipo de empresa:', 'tipo', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_dropdown('tipo_empresa', $tipo, 'default', array('class' => 'form-control'));
        echo "</div>";
      echo "</div>";

      echo "<div class='form-group'>";
        echo form_label('Tipo de servicio:', 'rubro', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_dropdown('rubro_empresa', $rubro, 'default', array('class' => 'form-control'));
        echo "</div>";
      echo "</div>";

      echo form_hidden('id');
      echo form_submit('','Guardar', $button);

      echo "<button class='btn btn-warning' type='reset' value='Reset'>Limpiar</button>";
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
    'url' => 'index.php/Bodega/Categoria_proveedor/mostrarTabla'
  );

  echo "<div class='content_buscar'>";
    echo form_input($buscar);
  echo "</div>";
?>
