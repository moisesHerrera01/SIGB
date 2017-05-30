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
  $.autocomplete({
    elemet: $('input[name=autocomplete1]'),
    url: 'index.php/Bodega/Productos/AutocompleteExistencia',
    name: 'detalleproducto',
    siguiente: 'cantidad',
    content: 'suggestions1',
    ajaxdata: {fuente: $('input[name=fuente]').val(), porpagina: 20}
  });

});
