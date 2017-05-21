//Reglas de validacion de cuenta contable
var reglas = {
  rules: {
    "nombre_cuenta": {
        required: true,
    },
    "numero_cuenta": {
        min:0,
        required:true,
    },
    "porcentaje_depreciacion": {
        min:0,
        max:100,
        required:true,
    },
    "vida_util": {
        min:0,
        required:true,
    },
  },
  messages: {
    "numero_cuenta": {
      min: "No debe ser negativo",
      required: "Este campo es obligatorio.",
    },
    "nombre_cuenta": {
      required: "El nombre es obligatorio.",
    },
    "porcentaje_depreciacion": {
      min: "Debe ser positivo",
      max: "Debe ser menor o igual a 100",
      required: "Este campo es obligatorio.",
    },
    "vida_util": {
      min: "No debe ser negativo",
      required: "Este campo es obligatorio.",
    },
  },
};
