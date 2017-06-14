$(document).ready(function(){
  $('form').submit(function() {
  }).validate({
    debug: false,
    errorPlacement: function(error, element) {
      // Append error within linked label
      $( element )
        .closest( "div" )
          .append( error );
    },
    rules: {
      "username": {
          required: true,
      },
      "password": {
          required: true,
      },
    },
    messages: {
      "username": {
        required: "El usuario es obligatorio.",
      },
      "password": {
        required: "La contraseña es obligatoria.",
      },
    },
  });
});
