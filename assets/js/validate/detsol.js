var porpagina = 0;
//Reglas de validacion de solicitud + retiro
var reglas = {
  rules: {
    "autocomplete1": {
        required: true
    },
    "cantidad": {
        required: true,
        min:0
    },
    "autocomplete2": {
        required: true
    },
  },
  messages: {
    "autocomplete1": {
      required: "El nombre de producto es obligatorio."
    },
    "cantidad": {
      required: "La cantidad es obligatoria.",
      min: "La cantidad no debe ser negativa."
    },
    "autocomplete2": {
      required: "El nombre de fuente de fondos es obligatorio."
    },
  },
};

$(document).ready(function() {

    $('#autocomplete1').keyup(autocomplete_existencia);
    $('#autocomplete1').click(autocomplete_existencia);

});


var autocomplete_existencia = function() {
  var elemet = $('#autocomplete1');
  var content = elemet.attr('content');
  var fuente_fondos = $('input[name=fuente]').val();
  porpagina = porpagina + 20;
  $.ajax({
    type: 'post',
    url: baseurl + "index.php/Bodega/Productos/AutocompleteExistencia",
    data: { autocomplete: elemet.val(), fuente: fuente_fondos, porpagina},
    beforeSend: function(){
      $("#"+content).html("<p id='cargando' align='center' class='icono icon-spinner'></p>");
      var angulo = 0;
      setInterval(function(){
            angulo += 3;
           $("#cargando").rotate(angulo);
      },10);
    },
    success: function(result) {
      $('#'+content).fadeIn(300).html(result);
      $('body').click(function(){ $('#'+content).fadeOut(300); });
      $('input').focus(function(){ $('#'+content).fadeOut(300); });
      $('.suggest-element').on('click', function(){
        //Obtenemos la id unica de la sugerencia pulsada
        var id = $(this).attr('ida');

        if (id == 'cargar_mas') {
          autocomplete_existencia.call(this);
        } else {
          //Editamos el valor del input con data de la sugerencia pulsada
          elemet.val($('#'+id).attr("data1"));
          $('input[name='+elemet.attr('name_op')+']').val($('#'+id).attr("data"));
          //Hacemos desaparecer el resto de sugerencias
          $('#'+content).fadeOut(300);
        }

      });
    },
  });
}
