//Reglas de validacion de orden de compra
var reglas = {
  rules: {
    "numero": {
      required: true,
      number: true
    },
    "autocomplete1": {
        required: true
    },
    "autocomplete2": {
        required: true
    },
    "fecha": {
        required: true,
        minordate: true
    },
    "monto_total_oc": {
        required: true,
        min:0

    },
  },
  messages: {
    "numero": {
      required: "El numero es obligatorio.",
      number: "La solicitud tiene que ser un numero."
    },
    "autocomplete2": {
      required: "Debe ingresar la disponibilidad."
    },
    "autocomplete1": {
      required: "Debe ingresar el proveedor."
    },
    "fecha": {
      required: "La fecha es obligatoria."
    },
    "monto_total_oc": {
      min: "El monto total de la OC es obligatorio.",
      required: "El monto es obligatorio."
    },
  },
};
