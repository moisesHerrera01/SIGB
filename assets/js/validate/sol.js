//Reglas de validacion de solicitud bodega
var reglas = {
  rules: {
    "autocomplete3": {
        required: true,
        checkautocomplete: 'seccion'
    },
    "autocomplete2": {
        required: true,
        checkautocomplete: 'id_fuentes'
    },
  },
  messages: {
    "autocomplete3": {
      required: "El nombre es sección es obligatorio."
    },
    "autocomplete2": {
      required: "La fuente de fondo es obligatoria."
    },
  },
};

$(document).ready(function() {
  //unidad
  $.autocomplete({
    elemet: $('input[name=autocomplete3]'),
    url: 'index.php/ActivoFijo/almacenes/AutocompleteSeccion',
    name: 'seccion',
    siguiente: 'solicitante',
    content: 'suggestions3'
  });

  //fuente
  $.autocomplete({
    elemet: $('input[name=autocomplete2]'),
    url: 'index.php/Bodega/Fuentefondos/Autocomplete',
    name: 'fuente',
    siguiente: 'comentario',
    content: 'suggestions2'
  });
});
