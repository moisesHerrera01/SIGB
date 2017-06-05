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
    "autocomplete": {
      required: true,
      checkautocomplete: 'seccion'
    },
    "autocomplete2": {
      required: true,
      checkautocomplete: 'especifico'
    }
  },
  messages: {
    "fecha_inicio": {
      required: "La Fecha inicial es obligatoria.",
    },
    "fecha_fin": {
      required: "La fecha final es obligatoria.",
    },
    "autocomplete": {
      required: 'La seccion es obligatoria.'
    },
    "autocomplete2": {
      required: 'El objeto especifico es obligatorio.'
    }
  },
};

$(document).ready(function() {
  //Especificos
  $.autocomplete({
    elemet: $('input[name=autocomplete2]'),
    url: 'index.php/Bodega/Especificos/Autocomplete',
    name: 'especifico',
    siguiente: 'fecha_inicio',
    content: 'suggestions1'
  });

  //Sección
  $.autocomplete({
    elemet: $('input[name=autocomplete]'),
    url: 'index.php/Bodega/Solicitud/Autocomplete',
    name: 'seccion',
    siguiente: 'button',
    content: 'suggestions'
  });

});
