//Reglas de validacion de detalle solicitud compra
var reglas = {
  rules: {
    "autocomplete1": {
      required: true,
      checkautocomplete: 'producto'
    },
    "cantidad": {
      required: true,
      min: 1,
    },
  },
  messages: {
    "autocomplete1": {
      required: "El producto es obligatorio."
    },
    "cantidad": {
      required: "La cantidad es obligatoria.",
      min: "Cantidad a solicitar debe ser como minimo 1.",
    },
  },
};
