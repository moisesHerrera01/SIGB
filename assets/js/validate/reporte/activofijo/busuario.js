
var reglas = {
  rules: {
    "autocomplete": {
        required: true,
    },
  },
  messages: {
    "autocomplete": {
      required: "El empleado es obligatorio."
    },
  },
};

$(document).ready(function() {
  //empleado
  $.autocomplete({
    elemet: $('input[name=autocomplete]'),
    url: 'index.php/ActivoFijo/Reportes/Bienes_por_usuario/AutocompleteEmpleado',
    name: 'empleado',
    siguiente: 'button',
    content: 'suggestions',
  });
});
