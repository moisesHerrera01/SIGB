//Reglas de validacion de objeto especifico
var reglas = {
  rules: {
    "id_especifico": {
        required: true,
        min:0
    },
    "nombre": {
        required: true,
        lettersonly: true,
    },
  },
  messages: {
    "id_especifico": {
      required: "El número de especifico es obligatorio.",
      min: "El número de especifico debe ser un número positivo.",
    },
    "nombre": {
      required: "El nombre es obligatorio.",
      lettersonly: "El nombre solo ocupa letras",
    },
  },
};
