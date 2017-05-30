//Reglas de validacion de producto
var reglas = {
  rules: {
    "nombre": {
        required: true,
    },
    "autocomplete":{
        required: true,
        checkautocomplete: 'unidadMedida'
    },
    "estado": {
        required: true,
    },
    "stok": {
        min: 0
    },
  },
  messages: {
    "nombre": {
      required: "El nombre es obligatorio.",
    },
    "autocomplete":{
        required: "La unidad de medida es obligatoria.",
    },
    "estado": {
      required: "Seleccione un estado.",
    },
    "stok": {
        min: "La cantidad no debe ser negativa."
    }
  },
};

$(document).ready(function() {
  $.autocomplete({
    elemet: $('input[name=autocomplete]'),
    url: 'index.php/Bodega/Unidadmedidas/Autocomplete',
    name: 'unidadMedida',
    siguiente: 'descripcion',
    content: 'suggestions'
  });
});
