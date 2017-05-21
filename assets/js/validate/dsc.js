//Reglas de validacion de detalle solicitud compra
var reglas = {
  rules: {
    "producto": {
      required: true,
    },
    "cantidad": {
      required: true,
      min: 1,
    },
  },
  messages: {
    "producto": {
      required: "El producto es obligatorio."
    },
    "cantidad": {
      required: "La cantidad es obligatoria.",
      min: "Cantidad a solicitar debe ser como minimo 1.",
    },
  },
};
