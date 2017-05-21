<?php
  $USER = $this->session->userdata('logged_in');


    $atributos = array(
      'class' => 'form-horizontal stepMe',
      'role' => 'form'
    );

  echo $this->breadcrumb->build_breadcrump($this->uri->uri_string());

  echo "<div class='content-form'>";
    echo "<div class='limit-content-title'>";
      echo "<span class='icono icon-paste icon-title'> Disponibilidad Financiera</span>";
    echo "</div>";
    echo "<div class='limit-content'>";
      echo form_open("/Compras/Solicitud_Disponibilidad/RecibirDatos", $atributos);

      $sol = array(
          'name' => 'autocomplete',
          'type'=> 'number',
          'placeholder' => 'Número de solicitud de compra',
          'class' => "form-control autocomplete_asoc3",
          'autocomplete' => 'off',
          'name_op' => 'solicitud_compra',
          'uri' => 'index.php/Compras/Solicitud_Disponibilidad/Autocomplete',
          'siguiente' => 'seccion',
          'content' => 'suggestions',
          'asociacion' => 'seccion',
          'asociacion2' => 'especifico',
          'asociacion3' => 'fechaIngreso'
      );

      $ingreso = array(
          'name' => 'fechaIngreso',
          'type' => "date",
          'placeholder' => 'Escribe Fecha de Ingreso',
          'class' => "form-control",
          //'value'=>$fecha,
          //'readonly'=>"readonly"
      );

      $sec= array(
          'name' => 'seccion',
          'class' => "form-control",
          'placeholder' => 'sección',
          'readonly'=>"readonly"
      );

      $esp= array(
          'name' => 'especifico',
          'class' => "form-control",
          'placeholder' => 'objeto especifico',
          'readonly'=>"readonly"
      );

      $dat= array(
          'name' => 'fecha',
          'placeholder' => 'Escribe la fecha de retorno UFI',
          'class' => "form-control",
          'type'=>'date',
      );

      $obs = array(
          'name' => 'observaciones',
          'class' => "form-control",
          'placeholder' => 'INGRESE LAS OBSERVACIONES POR PARTE DE LA UFI',
          'rows' => '3'
      );

      $atriLabel = array('class' => 'col-lg-2 control-label');
      $button = array('class' => 'btn btn-success',);


      echo "<div class='form-group'>";
        echo form_label('Solicitud:', 'sol', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($sol);
          echo form_hidden('solicitud_compra');
          echo '<div id="suggestions" class="suggestions"></div>';
        echo "</div>";
      echo "</div>";

      echo "<div class='form-group'>";
        echo form_label('Fecha Ingreso:', 'ingreso', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($ingreso);
        echo "</div>";
      echo "</div>";

      echo "<div class='form-group'>";
        echo form_label('Sección:', 'sec', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($sec);
        echo "</div>";
      echo "</div>";

      echo "<div class='form-group'>";
        echo form_label('Especifico:', 'esp', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($esp);
        echo "</div>";
      echo "</div>";

      echo "<div class='form-group'>";
        echo form_label('Fecha:', 'dat', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($dat);
        echo "</div>";
      echo "</div>";

      echo "<div class='form-group'>";
        echo form_label('Observaciones:', 'obs', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_textarea($obs);
        echo "</div>";
      echo "</div>";

      echo form_hidden('id');
      echo form_hidden('nivel');
      echo form_submit('','Guardar', $button);

      echo "<a class=\"btn btn-warning\" onclick=\"limpiar(['autocomplete','seccion','especifico','fecha',
      'numero_confirmacion'],false,['observaciones'])\">Limpiar</a>";


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
    'url' => 'index.php/Compras/Solicitud_Disponibilidad/mostrarTabla'
  );

  echo "<div class='content_buscar'>";
  echo form_input($buscar);
  echo "</div>";
?>
