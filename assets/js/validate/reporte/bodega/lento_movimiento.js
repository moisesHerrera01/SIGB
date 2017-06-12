var reglas = {
  rules: {
    "autocomplete": {
      required: true,
      checkautocomplete: 'fuente'
    },
    "autocomplete2": {
      required: true,
      checkautocomplete: 'especifico'
    }
  },
  messages: {
    "autocomplete": {
      required: 'La fuente de fondo es obligatoria.'
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

  //Fuente
  $.autocomplete({
    elemet: $('input[name=autocomplete1]'),
    url: 'index.php/Bodega/Fuentefondos/Autocomplete',
    name: 'fuente',
    siguiente: 'autocomplete2',
    content: 'suggestions'
  });

});
