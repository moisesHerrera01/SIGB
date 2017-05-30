//Reglas de validacion de detalle del conteo fisico
var reglas = {
  rules: {
    "autocomplete":{
        required: true,
    },
    "cantidad": {
        required: true,
        min: 0
    },
    "autocomplete2":{
        required: true,
    }
  },
  messages: {
    "autocomplete":{
        required: "El nombre de producto es obligatorio.",
    },
    "cantidad": {
      required: "La cantidad es obligatoria.",
      min: "La cantidad no debe ser negativa.",
    },
    "autocomplete2":{
        required: "El nombre de especifico es obligatorio.",
    }
  },
};

$(document).ready(function() {
  //producto
  $.autocomplete({
    elemet: $('input[name=autocomplete]'),
    url: 'index.php/Bodega/Productos/Autocomplete',
    name: 'producto',
    siguiente: 'cantidad',
    content: 'suggestions'
  });

  //especifico
  $.autocomplete({
    elemet: $('input[name=autocomplete3]'),
    url: 'index.php/Bodega/Especificos/AutocompletePorProducto/',
    name: 'especifico',
    siguiente: 'button',
    content: 'suggestions2'
  });
});
