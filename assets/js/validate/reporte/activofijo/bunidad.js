
var reglas = {
  rules: {
    "autocomplete": {
        required: true,
    },
  },
  messages: {
    "autocomplete": {
      required: "La seccion es obligatoria."
    },
  },
};

var iseccion;

$(document).ready(function() {
  //Seccion
  $.autocomplete({
    elemet: $('input[name=autocomplete]'),
    url: 'index.php/Bodega/Solicitud/Autocomplete',
    name: 'seccion',
    siguiente: 'autocomplete3',
    content: 'suggestions1',
    addfunction: function() {

      //oficina
      $.autocomplete({
        elemet: $('input[name=autocomplete3]'),
        url: 'index.php/ActivoFijo/Reportes/Bienes_por_unidad/autocompleteOficina',
        name: 'oficina',
        siguiente: 'button',
        content: 'suggestions2',
        ajaxdata: {seccion: $('input[name=seccion]').val()},
      });

    }
  });

});
