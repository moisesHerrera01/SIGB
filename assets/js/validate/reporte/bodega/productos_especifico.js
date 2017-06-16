//Reglas de validacion de factura
var reglas = {
  rules: {
    "autocomplete2": {
      required: true,
      checkautocomplete: 'especifico'
    }
  },
  messages: {
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
});
