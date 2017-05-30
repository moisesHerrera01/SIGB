//Reglas de validacion de factura
var reglas = {
  rules: {
    "fecha_inicio": {
      required: true,
    },
    "fecha_fin": {
      required: true,
      comparedate: 'fecha_inicio'
    },
    "autocomplete2": {
      required: true,
      checkautocomplete: 'especifico'
    },
    "autocomplete1": {
      required: true,
      checkautocomplete: 'fuente'
    },
  },
  messages: {
    "fecha_inicio": {
      required: "La Fecha inicial es obligatoria.",
    },
    "fecha_fin": {
      required: "La fecha final es obligatoria.",
    },
    "autocomplete2": {
      required: 'El objeto especifico es obligatorio.'
    },
    "autocomplete1": {
      required: 'La fuente de fondo es obligatoria.'
    }
  },
};

$(document).ready(function() {
  //objeto especifico
  $.autocomplete({
    elemet: $('input[name=autocomplete2]'),
    url: 'index.php/Bodega/Especificos/Autocomplete',
    name: 'especifico',
    siguiente: 'fecha_inicio',
    content: 'suggestions1'
  });

  //fuente
  $.autocomplete({
    elemet: $('input[name=autocomplete1]'),
    url: 'index.php/Bodega/Fuentefondos/Autocomplete',
    name: 'fuente',
    siguiente: 'autocomplete2',
    content: 'suggestions'
  });

});
