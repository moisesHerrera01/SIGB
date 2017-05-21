//Reglas de validacion de detalle disponibilidad
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
        min:0
    },
  },
  messages: {
    "autocomplete": {
      required: "Debe ingresar producto."
    },
    "cantidad": {
      min: "La cantidad no debe ser negativa."
    },
    "precio": {
      min: "El precio no debe ser negativo."
    },
  },
};
