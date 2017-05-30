<?php

  $atributos = array(
    'class' => 'form-horizontal stepMe',
    'role' => 'form'
  );

  echo $this->breadcrumb->build_breadcrump($this->uri->uri_string());

  echo "<div class='content-form'>";
    echo "<div class='limit-content-title'>";
      echo "<span class='icono icon-paste icon-title'> Facturas</span>";
    echo "</div>";
    echo "<div class='limit-content'>";
      echo form_open("/Bodega/Factura/RecibirDatos", $atributos);

      $num = array(
          'name' => 'numeroFactura',
          'placeholder' => 'Escribe el número de la Factura',
          'class' => "form-control"
      );

      $entrega = array(
          'name' => 'nombreEntrega',
          'placeholder' => 'Escribe el nombre de Entrega',
          'class' => "form-control"
      );

      $fechaFactura = array(
          'name' => 'fechaFactura',
          'type' => "date",
          'placeholder' => 'Escribe Fecha de la Factura',
          'class' => "form-control"
      );

      $ingreso = array(
          'name' => 'fechaIngreso',
          'type' => "date",
          'placeholder' => 'Escribe Fecha de Ingreso',
          'class' => "form-control",
          'value'=>$fecha,
          'readonly'=>"readonly"
      );

      $numcompromiso = array(
          'name' => 'autocomplete1',
          'placeholder' => 'Escribe Número de Compromiso',
          'class' => "form-control",
          'autocomplete' => 'off',
      );

      $coment = array(
        'name' => 'comentario_productos',
        'placeholder' => 'INGRESE OBSERVACIONES SOBRE LOS PRODUCTOS',
        'class' => "form-control",
        "rows" => "3"
      );

      $atriLabel = array('class' => 'col-lg-2 control-label');

      $button = array('class' => 'btn btn-success',);

      echo "<fieldset>";
        echo "<legend>Paso 1:</legend>";
        echo "<div class='form-group'>";
          echo form_label('Número:', 'num', $atriLabel);
          echo "<div class='col-lg-10'>";
            echo form_input($num);
          echo "</div>";
        echo "</div>";

        echo "<div class='form-group'>";
          echo form_label('Nombre Entrega:', 'nombreEntrega', $atriLabel);
          echo "<div class='col-lg-10'>";
            echo form_input($entrega);
          echo "</div>";
        echo "</div>";

        echo "<div class='form-group'>";
          echo form_label('Fecha Factura:', 'fechaFactura', $atriLabel);
          echo "<div class='col-lg-10'>";
            echo form_input($fechaFactura);
          echo "</div>";
        echo "</div>";

        echo "<div class='form-group'>";
          echo form_label('Fecha Ingreso:', 'ingreso', $atriLabel);
          echo "<div class='col-lg-10'>";
            echo form_input($ingreso);
          echo "</div>";
        echo "</div>";
      echo "</fieldset>";

      echo "<fieldset>";
        echo "<legend>Paso 2:</legend>";
        echo "<div class='form-group'>";
          echo form_label('Compromiso:', 'numcompromiso', $atriLabel);
          echo "<div class='col-lg-10'>";
            echo form_input($numcompromiso);
            echo form_hidden('compromiso');
            echo '<div id="suggestions1" class="suggestions"></div>';
          echo "</div>";
        echo "</div>";

        echo "<div class='form-group'>";
          echo form_label('Notas:', 'coment', $atriLabel);
          echo "<div class='col-lg-10'>";
            echo form_textarea($coment);
          echo "</div>";
        echo "</div>";

        echo "<div id='content_detalle'></div>";

        echo form_hidden('id');
        echo form_hidden('fuente');
        echo form_hidden('proveedor');
        echo form_hidden('seccion');
        echo form_hidden('orden');
        echo form_submit('','Guardar', $button);

        echo "<button class='btn btn-warning' type='reset' value='Reset'>Limpiar</button>";

      echo "</fieldset>";
      echo form_close();
    echo "</div>";
    echo "<div class='barra_carga'>";
    echo "</div>";
  echo "</div>";

  $buscar = array(
    'name' => 'buscar',
    'type' => 'search',
    'placeholder' => 'Buscar',
    'class' => 'form-control',
    'autocomplete' => 'off',
    'id' => 'buscar',
    'url' => 'index.php/Bodega/Factura/mostrarTabla'
  );

  echo "<div class='content_buscar'>";
  echo form_input($buscar);
  echo "</div>";
?>
