<html>
  <head>
    <meta charset="utf-8">
    <title><?= $title?></title>
    <link rel="shortcut icon" href="<?= base_url("assets/image/logo.jpg")?>" />
    <link href=<?= base_url("vendor/twbs/bootstrap/dist/css/bootstrap.min.css")?> rel="stylesheet">
    <style >
      .content {
        width: 86%;
        margin: 2em auto;
        font-size: 12px;
      }

      table {
         font-size: 12px;
       }

       .firma {
         padding: 1em;
       }
    </style>
  </head>
  <body onload="window.print()">
    <div class="content">
      <div>
        <p align="right">
          <img src=<?= base_url("assets/image/icono.jpg")?> width="200px" />
        </p>
        <p align="right">
          <?php
            echo "San Salvador, " . fecha(strftime("%m/%d/%Y"));
          ?>
        </p>
      </div>
      <p>Se&ntilde;ores</p>
      <p>Jefe de la Unidad Financiera</p>
      <p>Presente.</p>
      <p>
        En atención a los artículos 10 literal e) y 11 de la Ley de Adquiiciones  y Contrataciones de la Administración Pública ( LACAP),
        solicito su colaboración en el sentido de verificar  la asignación presupuestaria para dar inicio  a los procesos de compras siguientes:
      </p>
      <table border="1" width="100%">
        <tr>
          <th colspan="6"><p align="center">UNIDAD DE ADQUISICIONES Y CONTRATACIONES INSTITUCIONAL</p></th>
        </tr>
        <tr>
          <th><p align="center">Código del Proceso</p></th>
          <th><p align="center">Descripción del Proceso</p></th>
          <th><p align="center">Unidad Presupuestaria Línea de Trabajo (Unidad Solicitante)</p></th>
          <th><p align="center">Objeto Específico de Gasto Presupuestario</p></th>
          <th width="100"><p align="center">Monto</p></th>
        </tr>
        <?php
          $nombre_productos='';
          $count=0;
          foreach ($datos as $data) {
            if($count<3){
              $nombre_productos.=$data->nombre.', ';
            }
            $count++;
          }
          if($data->precio_estimado==0){
            $data->precio_estimado='$'."&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp";
          }else{
            $data->precio_estimado='$'.number_format($data->precio_estimado, 2);
          }if($count<=3){
            $nombre_productos='COMPRA DE '. substr($nombre_productos,0,-2).'.';
          }else{
            $nombre_productos='COMPRA DE '. $nombre_productos .'ENTRE OTROS.';
          }
            echo "<tr>";
            echo "<td align=center>". $data->numero_confirmacion ."</td>";
            echo "<td align=center>".$nombre_productos."</td>";
            echo "<td align=center><p>". $data->nombre_seccion . "</p><p>" .$data->linea_trabajo. "</p></td>";
            echo "<td align=center>". $data->id_especifico ."</td>";
            echo "<td align=center>".$data->precio_estimado."</td>";
            echo "</tr>";
            $especifico=$data->id_especifico;
        ?>
        <tr>
          <td colspan="6" height="50">
            Justificación: <?php
                echo $data->justificacion;
            ?>
          </td>
        </tr>
      </table>

      <div class="firma">
        <br>
        <p align="center">
          <?= $uaci ?>
        </p>
        <p align="center">
          Jefa UACI
        </p>
        <br>
      </div>

      <table border="1" width="100%">
        <tr>
          <th colspan="4">
            <p align="center">UNIDAD FINANCIERA</p>
          </th>
        </tr>
        <?php
        if ($data->fecha=='0000-00-00'){
          echo "<tr>";
          echo "<td colspan=4><p>"."Confirmación de asignación prespuestaria No.".$data->numero_confirmacion."&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
          &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp Fecha:"."______________". "</p></td>";
          echo "</tr>";
        }else{
            echo "<tr>";
            echo "<td colspan=4><p>"."Confirmación de asignación prespuestaria No.". $data->numero_confirmacion ."&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
            &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbspFecha:".$data->fecha. "</p></td>";
            echo "</tr>";
            }
        ?>
        <tr>
          <th><p align="center">Unidad Presupuestaria</p></th>
          <th><p align="center">Linea de Trabajo</p></th>
          <th><p align="center">Objeto Especifico</p></th>
          <th><p align="center">Monto</p></th>
        </tr>
        <?php
        $total = 0;
        $j = 0;
        if ($datos2!=NULL) {
          $j = 0;
          foreach ($datos2 as $data) {
            echo "<tr>";
            echo "<td align=center>". substr($data->linea_trabajo,0,-2) . "</td>";
            echo "<td align=center>".substr($data->linea_trabajo,2,2). "</td>";
            echo "<td align=center>". $especifico ."</td>";
            echo "<td align=center>";
            echo ($data->monto_sub_total != 0) ? "$" . number_format($data->monto_sub_total, 2) : "$&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
            &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp";
            echo "</td>";
            echo "</tr>";
            $total+=$data->monto_sub_total;
            $j++;
          }
        }

        for ($i=0; $i < 5-$j; $i++) {
          echo "<tr>";
          echo "<td align=center></td>";
          echo "<td align=center></td>";
          echo "<td align=center></td>";
          echo "<td align=center>&nbsp</td>";
          echo "</tr>";
        }
        ?>
        <tr>
          <td colspan="2"></td>
          <td>Total</td>
          <td>US <?= ($total != 0) ? "$" . number_format($total, 2) : "$" ?></td>
        </tr>
        <tr>

          <td colspan="4" height="50">
            Observaciones: <?php
            $i = 0;
            foreach ($datos as $data) {
              if (0 == $i) {
                echo $data->observaciones;
              }
              $i++;
            }
            ?>
          </td>
        </tr>
      </table>

      <div class="firma">
        <br>
        <p align="center">
          <?= $ufi?>
        </p>
        <p align="center">
          Jefe UFI
        </p>
        <br>
      </div>

    </div>
  </body>

</html>
