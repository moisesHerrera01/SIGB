//Reglas de validacion de detalle factura
var reglas = {
  rules: {
    "autocomplete": {
      required: true
    },
    "cantidad": {
        required: true,
        min:0
    },
    "precio": {
      required: true,
      min:0
    },
    "autocomplete2": {
      required: true
    }
  },
  messages: {
    "autocomplete": {
      required: " El nombre de producto es obligatorio."
    },
    "cantidad": {
      required: "La cantidad es obligatoria.",
      min:"Cantidad no puede ser negativa."
    },
    "precio": {
      required: "El precio es obligatorio.",
      min:"Precio no puede ser negativo."
    },
    "autocomplete2": {
      required: "El nombre de especifico es obligatorio."
    },
  },
};

$(document).ready(function() {
  //producto
  $.autocomplete({
    elemet: $('input[name=autocomplete]'),
    url: $('input[name=autocomplete]').attr('uri'),
    name: 'producto',
    asociacion1: 'cantidad',
    siguiente: 'cantidad',
    content: 'suggestions'
  });

});
