<section class="dashboard-panel-izq">
  <div class="dashboard-box">
    <div class="dashboard-box-title">
      <span>Activos Fijos</span>
      <span class="icono icon-cancel-circle"></span>
      <span class="icono max-min icon-circle-up"></span>
    </div>
    <div class="dashboard-box-content">
      <div class="chart-content">
        <canvas id="total_af" width="300" height="300"></canvas>
      </div>
    </div>
  </div>
</section>
<section class="dashboard-panel-der">
  <div class="dashboard-box">
    <div class="dashboard-box-title">
      <span>Solicitudes de Bodega y Compras</span>
      <span class="icono icon-cancel-circle"></span>
      <span class="icono max-min icon-circle-up"></span>
    </div>
    <div class="dashboard-box-content">
      <div class="chart-content">
        <canvas id="sols_bodega_compra"></canvas>
      </div>
    </div>
  </div>
</section>

<script src=<?= base_url("assets/js/Chart.js")?>></script>
<script type="text/javascript">
  $(document).ready(function(){
    var lbls = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];

    $.ajax({
      type: 'post',
      url: baseurl + "index.php/Dashboard/obtenerSolicitudesCompraBodegaUsuario",
      data: {},
      success: function(result) {
        var res = JSON.parse(result);

        var ctx = $("#sols_bodega_compra");
        var data = {
          labels: lbls,
          datasets: [
              {
                  label: "Solicitudes de Compras",
                  fill: false,
                  lineTension: 0.1,
                  backgroundColor: "rgba(255,105,97,0.4)",
                  borderColor: "rgba(255,105,97,1)",
                  borderCapStyle: 'butt',
                  borderDash: [],
                  borderDashOffset: 0.0,
                  borderJoinStyle: 'miter',
                  pointBorderColor: "rgba(255,105,97,1)",
                  pointBackgroundColor: "#fff",
                  pointBorderWidth: 1,
                  pointHoverRadius: 5,
                  pointHoverBackgroundColor: "rgba(255,105,97,1)",
                  pointHoverBorderColor: "rgba(220,220,220,1)",
                  pointHoverBorderWidth: 2,
                  pointRadius: 1,
                  pointHitRadius: 10,
                  data: [res[0]['enero'],res[0]['febrero'],res[0]['marzo'],res[0]['abril'],res[0]['mayo'],res[0]['junio'],res[0]['julio'],res[0]['agosto'],res[0]['septiembre'],res[0]['octubre'],res[0]['noviembre'],res[0]['diciembre']],
                  spanGaps: false,
              },
              {
                  label: "Solicitudes de Bodega",
                  fill: false,
                  lineTension: 0.1,
                  backgroundColor: "rgba(255,179,71,0.4)",
                  borderColor: "rgba(255,179,71,1)",
                  borderCapStyle: 'butt',
                  borderDash: [],
                  borderDashOffset: 0.0,
                  borderJoinStyle: 'miter',
                  pointBorderColor: "rgba(255,179,71,1)",
                  pointBackgroundColor: "#fff",
                  pointBorderWidth: 1,
                  pointHoverRadius: 5,
                  pointHoverBackgroundColor: "rgba(255,179,71,1)",
                  pointHoverBorderColor: "rgba(220,220,220,1)",
                  pointHoverBorderWidth: 2,
                  pointRadius: 1,
                  pointHitRadius: 10,
                  data: [res[1]['enero'],res[1]['febrero'],res[1]['marzo'],res[1]['abril'],res[1]['mayo'],res[1]['junio'],res[1]['julio'],res[1]['agosto'],res[1]['septiembre'],res[1]['octubre'],res[1]['noviembre'],res[1]['diciembre']],
                  spanGaps: false,
              }
          ]
        };

        var line = new Chart(ctx, {
            type: 'line',
            data: data,
            options: {
                scales: {
                    yAxes: [{
                        stacked: false
                    }]
                }
            }
        });
      },
    });

    $.ajax({
      type: 'post',
      url: baseurl + "index.php/Dashboard/obtenerActivosFijosUser",
      data: {},
      success: function(result) {
        var res = JSON.parse(result);
        var myBarChart = new Chart($('#total_af'), {
          type: 'bar',
          data: {
            labels: ["Activos Fijos"],
            datasets: [
              {
                label: "Activos Fijos",
                backgroundColor: [
                  'rgba(75, 192, 192, 0.2)',
                ],
                borderColor: [
                  'rgba(75, 192, 192, 1)',
                ],
                borderWidth: 1,
                data: [res]
              }
            ]
          },
          options: {
            scales: {
              xAxes: [{
                stacked: true
              }],
              yAxes: [{
                stacked: true
              }]
            }
          }
        });
      },
    });

  });
</script>
