<?php

 echo $this->breadcrumb->build_breadcrump(implode('/', array_slice($this->uri->segment_array(), 0, 3)));

$anio= array(
    'name' => 'anio',
    'placeholder' => 'Escribe la cantidad de años',
    'class' => "form-control",
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
      echo "Cuadro comparativo de gastos por fuente de fondo por año.";
    echo "</div>";
    echo
    "<div class='title-header'>
      <ul>
        <li>Fecha emisión: ".date('d/m/Y')."</li>
        <li>Nombre la compañia: MTPS</li>
        <li>N° pagina: 1/1</li>
        <li>Nombre pantalla:</li>
        <li>Usuario: ".$user['nombre_completo']."</li>
      </ul>
    </div>";
  echo "</div>";
  echo "<div class='limit-content'>";

    echo form_open("/Estrategico/Comparativo_fuente/RecibirDato", $atributos);

      echo "<div class='form-group'>";
        echo form_label('Cantidad Años:', 'cant', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($anio);
        echo "</div>";
      echo "</div>";

      echo form_submit('','Generar', $button);
    echo form_close();
  echo "</div>";
echo "</div>";
?>
