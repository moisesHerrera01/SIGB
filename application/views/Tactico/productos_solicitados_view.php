<?php
$user = $this->session->userdata('logged_in');
echo $this->breadcrumb->build_breadcrump(implode('/', array_slice($this->uri->segment_array(), 0, 3)));


$fechaInicial = array(
    'name' => 'fechaMin',
    'type' => "date",
    'placeholder' => 'Escribe Fecha de Inicial',
    'class' => "form-control",
    'minordate'=>"true"
);

$fechaFinal = array(
    'name' => 'fechaMax',
    'type' => "date",
    'placeholder' => 'Escribe Fecha de Final',
    'class' => "form-control",
    'minordate'=>"true"
);

$Cantidad = array(
    'name' => 'cantidad',
    'placeholder' => 'Escribe la Cantidad de Productos a Mostrar',
    'class' => "form-control",
    'autocomplete' => 'off',
    'type'=>'number'
);



$button = array('class' => 'btn btn-success',);
$atriLabel = array('class' => 'col-lg-2 control-label');

$atributos = array(
  'class' => 'form-horizontal',
  'role' => 'form',
);

echo "<div class='content-form'>";
  echo "<div class='limit-content-title'>";
    echo "<div class='title-reporte'>";
     echo "Reporte de Productos más Solicitados.";
  echo "</div>";
  echo
    "<div class='title-header'>
      <ul>
        <li>Fecha emisión: ".date('d/m/Y')."</li>
        <li>Nombre la compañia: MTPS</li>
        <li>N° pagina: 1/1</li>
        <li>Usuario: ".$user['nombre_completo']."</li>
      </ul>
    </div>";
  echo "</div>";
  echo "<div class='limit-content'>";
    echo form_open("/Tactico/Productos_solicitados/RecibirFiltro", $atributos);

      echo "<div class='form-group'>";
        echo form_label('Fecha inicial:', 'fechaini', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($fechaInicial);
        echo "</div>";
      echo "</div>";

      echo "<div class='form-group'>";
        echo form_label('Fecha Final:', 'fechafin', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($fechaFinal);
        echo "</div>";
      echo "</div>";

      echo "<div class='form-group'>";
        echo form_label('Cantidad:', 'Cantidad', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($Cantidad);
          echo '<div id="suggestions" class="suggestions"></div>';
        echo "</div>";
      echo "</div>";

      echo form_submit('','Generar', $button);
    echo form_close();
  echo "</div>";
echo "</div>";
?>
