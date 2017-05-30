//Reglas de validacion de solicitud + retiro
var reglas = {
  rules: {
    "autocomplete3": {
      required: true,
      checkautocomplete: 'seccion'
    },
    "fecha_solicitud":{
      required: true,
      minordate: true
    },
    "autocomplete2":{
      required: true,
      checkautocomplete: 'id_fuentes'
    },
    "autocomplete":{
      required: true,
      checkautocomplete: 'id_usuario'
    }
  },
  messages: {
    "autocomplete3": {
      required: "El nombre es sección es obligatorio."
    },
    "autocomplete2": {
      required: "La fuente de fondo es obligatoria."
    },
    "fecha_solicitud":{
      required: "La fecha es obligatoria."
    },
    "autocomplete":{
      required: "El Solicitante es obligatorio."
    }
  },
};

$(document).ready(function() {
  $('#autocomplete').focus(autocomplete_solicitante_seccion);

  //fuente de fondo
  $.autocomplete({
    elemet: $('input[name=autocomplete2]'),
    url: 'index.php/Bodega/Fuentefondos/Autocomplete',
    name: 'id_fuentes',
    siguiente: 'button',
    content: 'suggestions2'
  });

  //seccion
  $.autocomplete({
    elemet: $('input[name=autocomplete3]'),
    url: 'index.php/ActivoFijo/Almacenes/AutocompleteSeccion',
    name: 'seccion',
    siguiente: 'id_usuario',
    content: 'suggestions3'
  });
});

var autocomplete_solicitante_seccion = function() {
  //variable que controla los autocomplete para teclados
  var mark = 0;
  var elemet = $(this);
  var id_seccion = $('input[name=seccion]').val();
  var content = elemet.attr('content');
  if ('' != id_seccion) {
    $.ajax({
      type: 'post',
      url: baseurl + "index.php/Bodega/Solicitud_retiro/AutocompleteUsuarioSeccion",
      data: { autocomplete: elemet.val(), seccion: id_seccion },
      beforeSend: function(){
        $("#"+content).html("<p id='cargando' align='center' class='icono icon-spinner'></p>");
        var angulo = 0;
        setInterval(function(){
              angulo += 3;
             $("#cargando").rotate(angulo);
        },10);
      },
      success: function(result) {
        $('#'+content).fadeIn(300).html(result);

        $('.suggest-element').click(function(){
          //Obtenemos la id unica de la sugerencia pulsada
          var id = $(this).attr('ida');
          //Editamos el valor del input con data de la sugerencia pulsada
          elemet.val($('#'+id).attr("data1"));
          $('input[name='+elemet.attr('name_op')+']').val($('#'+id).attr("data"));
          //Hacemos desaparecer el resto de sugerencias
          $('#'+content).fadeOut(300);
        });

        elemet.keyup( function(event) {
            var code = event.keyCode;

            if((code>47 && code<91)||(code>96 && code<123) || code == 8 ) {
              $.ajax({
                type: 'post',
                url: baseurl + "index.php/Bodega/Solicitud_retiro/AutocompleteUsuarioSeccion",
                data: { autocomplete: elemet.val(), seccion: id_seccion },
                beforeSend: function(){
                  $("#"+content).html("<p id='cargando' align='center' class='icono icon-spinner'></p>");
                  var angulo = 0;
                  setInterval(function(){
                        angulo += 3;
                       $("#cargando").rotate(angulo);
                  },10);
                },
                success: function(result) {
                  $('#'+content).fadeIn(300).html(result);

                  $('.suggest-element').click(function(){
                    //Obtenemos la id unica de la sugerencia pulsada
                    var id = $(this).attr('ida');
                    //Editamos el valor del input con data de la sugerencia pulsada
                    elemet.val($('#'+id).attr("data1"));
                    $('input[name='+elemet.attr('name_op')+']').val($('#'+id).attr("data"));
                    //Hacemos desaparecer el resto de sugerencias
                    $('#'+content).fadeOut(300);
                  });
                },
              });
            } else if(code==40 || code ==38 ) {
                var elements = $('#'+content+' .suggest-element');
                elements.removeClass('suggest-element-select');
                if (elements.size() > 0) {
                  if (code==38){
                      mark --;
                  }else{
                      mark ++;
                  }

                  if (mark > elements.size()){
                      mark=0;
                  }else if (mark < 0){
                      mark=elements.size();
                  }

                  elements.each(function(){
                      if ($(this).attr('id') == mark)
                      {
                          $(this).addClass('suggest-element-select');
                      }
                  });
                }
            } else if (code == 13) {
              var elements = $('#'+content+' .suggest-element');
              elements.each(function(){
                  if ($(this).attr('id') == mark)
                  {
                    //Obtenemos la id unica de la sugerencia pulsada
                    var id = $(this).attr('ida');
                    //Editamos el valor del input con data de la sugerencia pulsada
                    elemet.val($('#'+id).attr("data1"));
                    $('input[name='+elemet.attr('name_op')+']').val($('#'+id).attr("data"));
                    //Hacemos desaparecer el resto de sugerencias
                    $('#'+content).fadeOut(300);
                    return false;
                  }
              });
            }
          }
        );
      },
    });
  }
  //$('body').click(function(){ $('#'+content).fadeOut(300); });
  elemet.focusout(function () {
    $('#'+content).fadeOut(300);
  });
  $('input[name='+elemet.attr('siguiente')+']').focus(function(){ $('#'+content).fadeOut(300); });
}
