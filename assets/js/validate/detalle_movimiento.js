$(document).ready(function() {
  $("select[name=ingresar]").change(function(){
    var option = $('select[name=ingresar]').val();

    if ("bien" == option) {

      $('#bien').show();
      $('#empleado').hide();
      $('#oficina').hide();

    } else if ("empleado" == option) {

      $('#empleado').show();
      $('#bien').hide();
      $('#oficina').hide();

    } else if ("oficina" == option) {

      $('#oficina').show();
      $('#empleado').hide();
      $('#bien').hide();

    }

  });

  $('input[name=autocomplete2]').focus(autocomplete_func($('input[name=autocomplete2]'), model_empleado));
  $('input[name=autocomplete3]').focus(autocomplete_func($('input[name=autocomplete3]'), model_oficina));
});


function model_empleado() {

  $("#bienes-modal").modal('toggle');
  $.ajax({
    url: baseurl + "index.php/ActivoFijo/Reportes/Bienes_por_usuario/Bienes_empleado",
    type: 'POST',
    // Form data
    //datos del formulario
    data: { empleado: $('input[name=empleado]').val() },
    //mientras enviamos el archivo
    beforeSend: function(){
      $("#bienes-modal .modal-body").html("<p id='cargando' align='center' class='icono icon-spinner'></p>");
      var angulo = 0;
      setInterval(function(){
            angulo += 3;
           $("#cargando").rotate(angulo);
      },10);
    },
    //una vez finalizado correctamente
    success: function(data){
        $("#bienes-modal .modal-body").html(data);
        $("button[name=cerrar]").click(function() {
          $('input[name=empleado]').val("");
          $('input[name=autocomplete2]').val("");
        });
    },
    //si ha ocurrido un error
    error: function(){
        $("#bienes-modal .modal-body").html("<span class='error'>Ha ocurrido un error.</span>");
    }
  });
}

function model_oficina() {

  $("#bienes-modal").modal('toggle');
  $.ajax({
    url: baseurl + "index.php/ActivoFijo/Reportes/Bienes_por_unidad/Bienes_oficina",
    type: 'POST',
    // Form data
    //datos del formulario
    data: { oficina: $('input[name=oficina]').val() },
    //mientras enviamos el archivo
    beforeSend: function(){
      $("#bienes-modal .modal-body").html("<p id='cargando' align='center' class='icono icon-spinner'></p>");
      var angulo = 0;
      setInterval(function(){
            angulo += 3;
           $("#cargando").rotate(angulo);
      },10);
    },
    //una vez finalizado correctamente
    success: function(data){
        $("#bienes-modal .modal-body").html(data);
        $("button[name=cerrar]").click(function() {
          $('input[name=empleado]').val("");
          $('input[name=autocomplete2]').val("");
        });
    },
    //si ha ocurrido un error
    error: function(){
        $("#bienes-modal .modal-body").html("<span class='error'>Ha ocurrido un error.</span>");
    }
  });
}
