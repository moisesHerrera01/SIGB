//Reglas de validacion de detalle monto
var reglas = {
  rules: {
    "autocomplete1": {
        required: true
    },
    "precio": {
        min:0
    },
  },
  messages: {
    "autocomplete1": {
      required: "Debe ingresar una linea presupuestaria."
    },
    "monto": {
      min: "El monto debe ser positivo."
    },
  },
};
