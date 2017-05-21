<?php

  $atributos = array(
    'class' => 'form-horizontal',
    'role' => 'form'
  );

  echo $this->breadcrumb->build_breadcrump($this->uri->uri_string());

  echo "<div class='content-form'>";
    echo "<div class='limit-content-title'>";
      echo "<span class='icono icon-paste icon-title'> Productos</span>";
    echo "</div>";
    echo "<div class='limit-content'>";
      echo form_open("/Bodega/Productos/RecibirDatos", $atributos);

      $nom = array(
          'name' => 'nombre',
          'placeholder' => 'Escribe Nombre',
          'class' => "form-control"
      );

      $um = array(
          'name' => 'autocomplete',
          'placeholder' => 'Escribe Unidad Medida',
          'class' => "form-control autocomplete",
          'autocomplete' => 'off',
          'uri' => 'index.php/Bodega/Unidadmedidas/Autocomplete',
          'name_op' => 'unidadMedida',
          'siguiente' => 'descripcion',
          'content' => 'suggestions'
      );

      // $um = array(
      //     'default' => 'SELECCIONE UNIDAD MEDIDA'
      // );
      //
      // foreach ($unidades as $unidad) {
      //   $um[$unidad->id_unidadmedida] = $unidad->nombre;
      // }

      $des = array(
          'name' => 'descripcion',
          'placeholder' => 'Escribe Descripción',
          'class' => 'form-control'
      );

      $est = array(
          'default' => 'SELECCIONE ESTADO',
          'INACTIVO' => 'INACTIVO',
          'ACTIVO' => 'ACTIVO'
      );

      $fecha = array(
          'name' => 'fecha',
          'type' => "date",
          'placeholder' => 'Escribe Fecha Caducidad',
          'class' => "form-control"
      );

      $stok = array(
          'name' => 'stok',
          'type' => 'number',
          'placeholder' => 'Escribe Stock Minimo',
          'class' => "form-control"
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
        echo form_label('Unidad Medida:', 'um', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($um);
          echo form_hidden('unidadMedida');
          echo '<div id="suggestions" class="suggestions"></div>';
        echo "</div>";
      echo "</div>";

      echo "<div class='form-group'>";
        echo form_label('Descripción:', 'des', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($des);
        echo "</div>";
      echo "</div>";

      echo "<div class='form-group'>";
        echo form_label('Estado:', 'est', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_dropdown('estado', $est, 'default', array('class' => 'form-control'));
        echo "</div>";
      echo "</div>";

      echo "<div class='form-group'>";
        echo form_label('Fecha Caducidad:', 'fecha', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($fecha);
        echo "</div>";
      echo "</div>";

      echo "<div class='form-group'>";
        echo form_label('Stock Minimo:', 'fecha', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($stok);
        echo "</div>";
      echo "</div>";

      echo form_hidden('id');

      echo form_submit('','Guardar', $button);

      echo "<a class=\"btn btn-warning\" onclick=\"limpiar(['id', 'nombre', 'unidadMedida', 'autocomplete','descripcion',
            'estado', 'fecha', 'stok'], ['estado'])\">Limpiar</a>";

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
    'url' => 'index.php/Bodega/Productos/mostrarTabla'
  );

  echo "<div class='content_buscar'>";
  echo form_input($buscar);
  echo "</div>";
?>
