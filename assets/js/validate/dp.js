//Reglas de validacion de detalle producto
var reglas = {
  rules: {
    "id_especifico": {
        required: true
    },
    "autocomplete": {
        required: true
    },
  },
  messages: {
    "id_especifico": {
      required: "El número de especifico es obligatorio."
    },
    "autocomplete": {
      required: "El nombre de producto es obligatorio."
    },
  },
};
