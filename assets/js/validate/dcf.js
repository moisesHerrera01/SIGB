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
