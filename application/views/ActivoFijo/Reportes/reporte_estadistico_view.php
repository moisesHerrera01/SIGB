<style media="screen">
  table th, td {
    text-align: center;
  }

  .direccion th:first-child {
    width: 15%;
  }
</style>

<?php

echo $this->breadcrumb->build_breadcrump(implode('/', array_slice($this->uri->segment_array(), 0, 4)));

echo "<div style='text-align:center'>";
    echo "<div class='form-group'>";
    echo "<h3><font color=black>13-Reporte Estadistico</font></h3>";
  echo "</div>";
echo "</div>";

$fechaInicial = array(
    'name' => 'fechaMin',
    'type' => "date",
    'placeholder' => 'Escribe Fecha de Inicial',
    'class' => "form-control"
);

$fechaFinal = array(
    'name' => 'fechaMax',
    'type' => "date",
    'placeholder' => 'Escribe Fecha de Final',
    'class' => "form-control"
);

$button = array('class' => 'btn btn-success',);
$atriLabel = array('class' => 'col-lg-2 control-label');

$atributos = array(
  'class' => 'form-horizontal',
  'role' => 'form',
);

echo "<div class='content-form'>";
  echo "<div class='limit-content-title'>";
    echo "<span class='icono icon-filter icon-title'> Filtro</span>";
  echo "</div>";
  echo "<div class='limit-content'>";
    echo form_open("/ActivoFijo/Reportes/Reporte_estadistico/RecibirDatos", $atributos);

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

      echo form_submit('','Generar', $button);
    echo form_close();
  echo "</div>";
echo "</div>";
?>

<?php if ($result): ?>

<div class='content_table '>
  <div class='limit-content-title'>
    <span class='icono icon-table icon-title'>Reporte Estadistico</span>
  </div>
    <div class='limit-content'>
      <div class='exportar'>
        <a href='<?= base_url("/index.php/ActivoFijo/Reportes/Reporte_estadistico/Reporte/".$this->uri->segment(5)."/".$this->uri->segment(6).'/Imprimir')?>' 
          class='icono icon-printer' target='_blank'>Imprimir</a>
      </div>
      <div class='table-responsive'>
        <table class='table table-bordered'>
          <tr class="direccion">
            <th rowspan="3">DIRECCIONES</th>
            <th colspan="20">EQUIPO INFORMATICO</th>
          </tr>
          <tr>
            <th colspan="4">P.C. (4)*</th>
            <th colspan="4">U.P.S</th>
            <th colspan="4">IMPRESOR</th>
            <th colspan="4">SCANNERS</th>
            <th colspan="4">LAPTOP</th>
          </tr>
          <tr>
            <th>N</th>
            <th>U</th>
            <th>O</th>
            <th>D</th>

            <th>N</th>
            <th>U</th>
            <th>O</th>
            <th>D</th>

            <th>N</th>
            <th>U</th>
            <th>O</th>
            <th>D</th>

            <th>N</th>
            <th>U</th>
            <th>O</th>
            <th>D</th>

            <th>N</th>
            <th>U</th>
            <th>O</th>
            <th>D</th>
          </tr>
          <tr>
            <th>OFICINAS REGIONALES Y DEPARTAMENTALES</th>
            <?php for ($i = 3; $i < 8; $i++): ?>
              <?php for ($j = 0; $j < 4; $j++): ?>
                <td><?= $result[$i][$j]?></td>
              <?php endfor; ?>
            <?php endfor;?>
          </tr>
          <tr>
            <th>CENTRO DE RECREACION A TRABAJADORES</th>
            <?php for ($i = 3; $i < 8; $i++): ?>
              <?php for ($j = 4; $j < 8; $j++): ?>
                <td><?= $result[$i][$j]?></td>
              <?php endfor; ?>
            <?php endfor;?>
          </tr>
          <tr>
            <th>TOTAL</th>
            <?php for ($i = 3; $i < 8; $i++): ?>
              <?php for ($j = 0; $j < 4; $j++): ?>
                <th><?= $result[$i][$j] + $result[$i][$j+4]?></th>
              <?php endfor; ?>
            <?php endfor;?>
          </tr>
        </table>

        <table class='table table-bordered'>
          <tr class="direccion">
            <th rowspan="3">DIRECCIONES</th>
            <th colspan="8">MOBILIARIO</th>
          </tr>
          <tr>
            <th colspan="4">ESCRITORIO</th>
            <th colspan="4">SILLA</th>
          </tr>
          <tr>
            <th>N</th>
            <th>U</th>
            <th>O</th>
            <th>D</th>

            <th>N</th>
            <th>U</th>
            <th>O</th>
            <th>D</th>
          </tr>
          <tr>
            <th>OFICINAS REGIONALES Y DEPARTAMENTALES</th>
            <?php for ($i = 8; $i < 10; $i++): ?>
              <?php for ($j = 0; $j < 4; $j++): ?>
                <td><?= $result[$i][$j]?></td>
              <?php endfor; ?>
            <?php endfor;?>
          </tr>
          <tr>
            <th>CENTRO DE RECREACION A TRABAJADORES</th>
            <?php for ($i = 8; $i < 10; $i++): ?>
              <?php for ($j = 4; $j < 8; $j++): ?>
                <td><?= $result[$i][$j]?></td>
              <?php endfor; ?>
            <?php endfor;?>
          </tr>
          <tr>
            <th>TOTAL</th>
            <?php for ($i = 8; $i < 10; $i++): ?>
              <?php for ($j = 4; $j < 8; $j++): ?>
                <th><?= $result[$i][$j-4] + $result[$i][$j]?></th>
              <?php endfor; ?>
            <?php endfor;?>
          </tr>
        </table>
      </div>
    </div>
</div>

<?php endif ?>
