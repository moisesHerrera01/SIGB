<?php

  $atributos = array(
    'class' => 'form-horizontal',
    'role' => 'form'
  );

  echo $this->breadcrumb->build_breadcrump($this->uri->uri_string());

  echo "<div class='content-form'>";
    echo "<div class='limit-content-title'>";
      echo "<span class='icono icon-paste icon-title'> Solicitud</span>";
    echo "</div>";
    echo "<div class='limit-content'>";
      echo form_open("/Bodega/Solicitud/RecibirDatos", $atributos);
      $dat= array(
          'name' => 'fecha_solicitud',
          'placeholder' => 'Escribe la fecha de ingreso',
          'class' => "form-control",
          'value'=> $fecha,
          'readonly'=>"readonly",
          'type'=>'date'
      );

      /*$sec= array(
          'name' => 'id_seccion',
          'placeholder' => 'Escribe la sección',
          'class' => "form-control",
          'value'=> $seccion,
          'readonly'=>"readonly",
      );*/

      $sec = array(
          'name' => 'autocomplete3',
          'placeholder' => 'Ingrese el nombre de la sección',
          'class' => "form-control",
          'value'=> $seccion,
          'autocomplete' => 'off',
      );

      $sol = array(
          'name' => 'solicitante',
          'placeholder' => 'Escribe el número de la Solicitud',
          'class' => "form-control",
          'readonly'=>"readonly",
          'value'=> $solicitante,
      );

      $fun = array(
          'name' => 'autocomplete2',
          'placeholder' => 'Escribe Fuente de Fondos',
          'class' => "form-control",
          'autocomplete' => 'off',
      );

      $com = array(
        'name' => 'comentario',
        'placeholder' => 'ESCRIBA LA JUSTIFICACION ',
        'class' => "form-control",
        "rows" => "3"
      );


      $atriLabel = array('class' => 'col-lg-2 control-label');

      $button = array('class' => 'btn btn-success',);

      echo "<fieldset>";
        echo "<legend>Paso 1: Datos Generales.</legend>";
      echo "<div class='form-group'>";
        echo form_label('Fecha:', 'dat', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($dat);
        echo "</div>";
      echo "</div>";

      /*echo "<div class='form-group'>";
        echo form_label('Sección:', 'sec', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($sec);
        echo "</div>";
      echo "</div>";
*/
      echo "<div class='form-group'>";
        echo form_label('Sección:', 'sec', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($sec);
          echo form_hidden('seccion',$id_seccion);
          echo '<div id="suggestions3" class="suggestions"></div>';
        echo "</div>";
      echo "</div>";

      echo "<div class='form-group'>";
        echo form_label('Solicitante:', 'sol', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($sol);
        echo "</div>";
      echo "</div>";

      echo "<div class='form-group'>";
        echo form_label('Fuente de Fondos:', 'fun', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($fun);
          echo form_hidden('fuente');
          echo '<div id="suggestions2" class="suggestions"></div>';
        echo "</div>";
      echo "</div>";

      echo "<div class='form-group'>";
        echo form_label('Justificación:', 'com', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_textarea($com);
        echo "</div>";
      echo "</div>";

      echo form_hidden('id');
      echo form_submit('','Siguiente', $button);

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
    'url' => 'index.php/Bodega/solicitud/mostrarTabla'
  );

  echo "<div class='content_buscar'>";
  echo form_input($buscar);
  echo "</div>";
?>
