//Reglas de validacion de unidad de medida
var reglas = {
  rules: {
    "autocomplete1": {
        checkautocomplete: 'usuario'
    },
    "autocomplete2": {
        checkautocomplete: 'rol'
    },
  },
};

$(document).ready(function() {

  //usuario
  $.autocomplete({
    elemet: $('input[name=autocomplete1]'),
    url: 'index.php/Usuario_Rol/AutocompleteUsuario',
    name: 'usuario',
    siguiente: 'rol',
    content: 'suggestions1'
  });

  //rol
  $.autocomplete({
    elemet: $('input[name=autocomplete2]'),
    url: 'index.php/Usuario_Rol/AutocompleteRol',
    name: 'rol',
    siguiente: 'button',
    content: 'suggestions2'
  });

});
