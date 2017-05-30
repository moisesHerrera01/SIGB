
var reglas = {
  rules: {
    "autocomplete": {
        required: true,
    },
  },
  messages: {
    "autocomplete": {
      required: "El proyecto es obligatorio."
    },
  },
};

$(document).ready(function() {
  //proyecto
  $.autocomplete({
    elemet: $('input[name=autocomplete]'),
    url: 'index.php/ActivoFijo/Reportes/Bienes_por_proyecto/AutocompleteProyecto',
    name: 'proyecto',
    siguiente: 'button',
    content: 'suggestions',
  });
});
