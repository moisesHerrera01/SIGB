var colapse = 0;
$(document).ready(function(){

    var validation;
    if (typeof(reglas) != "undefined") {
      validation = {
        debug: false,
        errorPlacement: function(error, element) {
    			// Append error within linked label
    			$( element )
    				.closest( "div" )
    					.append( error );
      	},
        invalidHandler: function(event, validator) {
          $('input[type=submit]').prop( "disabled", false );
          $('input[type=submit]').val('Guardar');
        },
        rules: reglas.rules,
        messages: reglas.messages,
      };
    }

    $('form').submit(function() {
      $('input[type=submit]').val('Procesando');
      $('input[type=submit]').prop( "disabled", true );

      $("form input[type=text]").each(function(){
          $(this).val($(this).val().toUpperCase());
      });

      return true;
    }).validate(validation);

    // menu
    var w = $(window).width();
    if(w > 767) {

      $('.cbp-vimenu > li').hover(function() {
        if (colapse == 0) {
          var i = $(this).index();
          $('.cbp-vimenu > li:eq('+i+') a span').show(100);
        }
      }, function () {
        if (colapse == 0) {
          var i = $(this).index();
          $('.cbp-vimenu > li:eq('+i+') a span').hide(50);
        }
      });

      $("li.content-submenu").click(function() {
        var i = $(this).index()-1;
        var submenu = '.submenu-'+i;
        $(submenu).show(500);
        $('.cbp-vimenu > li a span').hide(50);

        if (0 == colapse) {
          $('ul'+submenu+' > li').hover(function() {
            if (0 == colapse) {
              var i = $(this).index();
              $('ul'+submenu+' > li:eq('+i+') a span').show(100);
            }
          }, function () {
            if (0 == colapse) {
              var i = $(this).index();
              $('ul'+submenu+' > li:eq('+i+') a span').hide(50);
            }
          });
        } else if (1 == colapse) {
          $('ul'+submenu+' > li a span').show(100);
        }
      });

      $("li.content-subsubmenu").click(function() {
        var padre = $(this).parent().attr('class').split(" ")[1].split("-")[1];
        var i = $(this).index()-1;
        var subsubmenu = '.submenu-'+padre+'-'+i;
        $(subsubmenu).css('z-index', '10000');
        $(subsubmenu).show(500);
        $('.cbp-submenu > li a span').hide(50);

        if (0 == colapse) {
          $('ul'+subsubmenu+' > li').hover(function() {
            if (0 == colapse) {
              var i = $(this).index();
              $('ul'+subsubmenu+' > li:eq('+i+') a span').show(100);
            }
          }, function () {
            if (0 == colapse) {
              var i = $(this).index();
              $('ul'+subsubmenu+' > li:eq('+i+') a span').hide(50);
            }
          });
        } else if (1 == colapse) {
          $('ul'+subsubmenu+' > li a span').show(100);
        }
      });

      $(".undo").click(function() {
        $(this).parent().parent().hide(500);

        if (1 == colapse) {
          var clases = $(this).parent().parent().attr('class').split(" ");
          var menus = clases[1].split("-");
          if (2 == menus.length) {
            // se puede cargar el menu principal
            $('.cbp-vimenu .cbp-vimenu-oc').each(function() {
              $(this).show(500);
            });
          } if (3 == menus.length) {
            // se puede cargar un submenu con numero ??
            $('.submenu-'+menus[1]+' .cbp-vimenu-oc').each(function() {
              $(this).show(500);
            });
          }
        }
      });

      $(".pull").click(function() {
        if (0 == colapse) {
          //colapse activo
          colapse = 1;
          $('.cbp-vimenu').css({'width': '13.65em'});
          $(this).parent().siblings().each(function() {
            $(this).find('span').show(500);
          });
        } else if (1 == colapse) {
          colapse = 0;
          $('.cbp-vimenu').css('width', '3em');
          $('.cbp-vimenu-oc').each(function() {
            $(this).hide(500);
          });
        }

      });

    }

    $(function() {
      var pull = $('.pull');
        menu = $('nav ul.cbp-vimenu');
        menuHeight = menu.height();
        w = $(window).width();

        if(w < 767){
          $(".hbp-vimenu li:nth-child(2) > #usuario_sistema").text("");
          $(pull).on('click', function(e) {
              e.preventDefault();
              menu.slideToggle();
              $(".cbp-submenu").hide(250);

              $("li.content-submenu").click(function() {
                var i = $(this).index()-1;
                var submenu = '.submenu-'+i;
                $(submenu).show(500);
              });

              $("li.content-subsubmenu").click(function() {
                var padre = $(this).parent().attr('class').split(" ")[1].split("-")[1];
                var i = $(this).index()-1;
                var subsubmenu = '.submenu-'+padre+'-'+i;
                $(subsubmenu).css('z-index', '10000');
                $(subsubmenu).show(500);
              });
          });

          $(".undo").click(function() {
            $(this).parent().parent().hide(500)
          });
        }
    });

    $(window).resize(function() {
        if(w > 767 && menu.is(':hidden')) {
            menu.removeAttr('style');
        }
    });
    // termina menu

    $(".close").click(function(){
        $("#myAlert").slideUp();
    });

    setInterval(function() {
        $("#myAlert").slideUp();
    }, 5000);

    $('#buscar').keyup(buscar);

    $('.autocomplete').focus(autocomplete);
    $('.autocomplete_asoc').focus(autocomplete_asoc);
    $('.autocomplete_asoc2').focus(autocomplete_asoc2);
    $('.autocomplete_asoc2_text_area').focus(autocomplete_asoc2_text_area);
    $('.autocomplete_asoc3').focus(autocomplete_asoc3);

    $('.icon-eliminar').click(confirmar);

    $.stepForm("Atrás", "Siguiente");

    $('.aprobar_compra').click(aprobar_compra);

    $('.denegar_compra').click(denegar_compra);
});

$(document).on('keyup keypress', 'form input[type!="submit"]', function(e) {
  if(e.keyCode == 13) {
    e.preventDefault();
    return false;
  }
});

/*FUNCIONES*/

var buscar = function () {
  $('.table').remove();
  $.ajax({
    type: 'post',
    url: baseurl + $(this).attr('url'),
    data: { busca: $('#buscar').val().toUpperCase() },
    beforeSend: function(){
      $(".content_table > .limit-content").html("<p id='cargando' align='center' class='icono icon-spinner'></p>");
      var angulo = 0;
      setInterval(function(){
            angulo += 3;
           $("#cargando").rotate(angulo);
      },10);
    },
    success: function(result) {
      $('.content_table > .limit-content').html(result);
      $('.icon-eliminar').click(confirmar);
    },
  });
}

var confirmar = function() {
   var elemet = $(this);
   swal({
     title: "Seguro que desea eliminar?",
     text: "Usted no será capaz de recuperar la información eliminada!",
     type: "warning",
     showCancelButton: true,
     confirmButtonColor: "#DD6B55",
     confirmButtonText: "Si, eliminar",
     cancelButtonText: "No, elimnar",
     closeOnConfirm: false,
     closeOnCancel: false },

     function(isConfirm){
       if (isConfirm) {
          var url = elemet.attr('uri');
          $(location).attr('href',url);
       } else {
         swal("Cancelando", "La información no ha sido eliminada.", "error");
       }
     });
}

/*
* Los campos select_name y select_value son opcionales solo cuando haya
* algun select que haya que poner.
*/
function llenarFormulario(tabla, name, value, select_name, select_value, atributo, textarea_name, textarea_value) {

    var defecto = false;
    var defecto1 = false;
    $.extend(defecto, select_name);
    $.extend(defecto, select_value);
    $.extend(defecto, atributo);
    $.extend(defecto, textarea_name);
    $.extend(defecto, textarea_value);

    for (var i = 0; i < name.length; i++) {
      $('input[name='+name[i]+']').val(value[i]);
    }

    if (select_name != false && select_value != false) {
      if (Array.isArray(select_name) && Array.isArray(select_value)) {
        for (var i = 0; i < select_name.length; i++) {
          $('select[name='+select_name[i]+'] > option').removeAttr('selected');
          $('select[name='+select_name[i]+'] > option[value='+select_value[i]+']').attr('selected', 'selected');
        }
      } else {
        $('select[name='+select_name+'] > option').removeAttr('selected');
        $('select[name='+select_name+'] > option[value='+select_value+']').attr('selected', 'selected');
      }
    }

    if (atributo) {
      if ((atributo.length % 3) == 0) {
        for (var i = 0; i < atributo.length; i=i+3) {
          $('input[name='+atributo[i]+']').attr(atributo[i+1] , atributo[i+2]);
        }
      }
    }

    if (textarea_name != false && textarea_value != false) {
      if (Array.isArray(textarea_name) && Array.isArray(textarea_value)) {
        for (var i = 0; i < textarea_name.length; i++) {
          $('textarea[name='+textarea_name[i]+']').val(textarea_value[i]);
        }
      } else {
        $('textarea[name='+textarea_name+']').val(textarea_value);
      }
    }

    $('html,body').animate({
      scrollTop: $(".content-form").offset().top
    }, 700);
}

/*
* mensajes
*/

function aprobar_compra() {
  var elemet = $(this);
  swal({
    title: "Desea Aprobar?",
    text: "Escriba un Comentario (Opcional):",
    type: "input",
    showCancelButton: true,
    closeOnConfirm: false,
    animation: "slide-from-top",
    inputPlaceholder: "Comentario"
    },
    function(inputValue){
      if (inputValue === false) return false;

      $.ajax({
        type: 'post',
        url: baseurl + elemet.data("url"),
        data: {comentario : inputValue},
        success: function(result) {

          $(".content_table .limit-content > .table-responsive").remove();

          $.ajax({
            type: 'post',
            url: baseurl + elemet.data("url-table"),
            data: '',
            success: function(result) {
              $(".content_table .limit-content").html(result);

              $('.aprobar_compra').click(aprobar_compra);

              $('.denegar_compra').click(denegar_compra);

              if( typeof open_modal !== 'undefined' && jQuery.isFunction( open_modal ) ) {
                if ( $(".modal_open").length > 0 ){
                    $('.modal_open').click(open_modal);
                }
              }

            }
          });

          swal("Exito!", "La solicitud a sido autorizada.", "success");

        },
      });

    }
  );
}

function denegar_compra() {
  var elemet = $(this);
  swal({
    title: "Desea Denegar?",
    text: "Escriba un Comentario (Obligatorio):",
    type: "input",
    showCancelButton: true,
    closeOnConfirm: false,
    animation: "slide-from-top",
    inputPlaceholder: "Comentario"
    },
    function(inputValue){
      if (inputValue === false) return false;

      if (inputValue === "") {
        swal.showInputError("Necesitas escribir algo.");
        return false
      }

      $.ajax({
        type: 'post',
        url: baseurl + elemet.data("url"),
        data: {comentario : inputValue},
        success: function(result) {

          $(".content_table .limit-content > .table-responsive").remove();

          $.ajax({
            type: 'post',
            url: baseurl + elemet.data("url-table"),
            data: '',
            success: function(result) {
              $(".content_table .limit-content").html(result);

              $('.aprobar_compra').click(aprobar_compra);

              $('.denegar_compra').click(denegar_compra);

              if( typeof open_modal !== 'undefined' && jQuery.isFunction( open_modal ) ) {
                if ( $(".modal_open").length > 0 ){
                    $('.modal_open').click(open_modal);
                }
              }

            }
          });

          swal("Exito!", "La solicitud a sido denegada.", "success");
        },
      });

    }
  );
}

/*
* El campo select son opcionales solo cuando haya
* algun select que haya que eliminar.
*/
function limpiar(array, select, textarea) {

  var defecto = false;
  $.extend(defecto, select);
  $.extend(defecto, textarea);

  for (i = 0; i < array.length; i++) {
    $('input[name='+array[i]+']').val('');
  }

  if (select != defecto) {
    for (i = 0; i < select.length; i++) {
      $('select[name='+select[i]+'] > option[value="default"]').removeAttr('selected');
      $('select[name='+select[i]+'] > option[value="default"]').attr('selected', 'selected');
    }
  }

  if (textarea != defecto) {
    for (i = 0; i < textarea.length; i++) {
      $('textarea[name='+textarea[i]+']').val('');
    }
  }
}

/*
* Funciones de autocomplementar
*/
var autocomplete = function() {
  //variable que controla los autocomplete para teclados
  var mark = 0;
  var elemet = $(this);
  var content = elemet.attr('content');
  $.ajax({
    type: 'post',
    url: baseurl + elemet.attr('uri'),
    data: { autocomplete: elemet.val() },
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
              url: baseurl + elemet.attr('uri'),
              data: { autocomplete: elemet.val() },
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
  //$('body').click(function(){ $('#'+content).fadeOut(300); });
  elemet.focusout(function () {
    $('#'+content).fadeOut(300);
  });
  $('input[name='+elemet.attr('siguiente')+']').focus(function(){ $('#'+content).fadeOut(300); });
}

/*
* Sirve para asociar dos input, ademas del selectcionado
*/
var autocomplete_asoc = function() {
  var mark = 0;
  var elemet = $(this);
  var content = elemet.attr('content');
  var asoc = elemet.attr('asociacion');
  $.ajax({
    type: 'post',
    url: baseurl + elemet.attr('uri'),
    data: { autocomplete: elemet.val() },
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
      $('body').click(function(){ $('#'+content).fadeOut(300); });
      $('input').focus(function(){ $('#'+content).fadeOut(300); });
      $('.suggest-element').on('click', function(){
        //Obtenemos la id unica de la sugerencia pulsada
        var id = $(this).attr('ida');
        //Editamos el valor del input con data de la sugerencia pulsada
        elemet.val($('#'+id).attr("data1"));
        $('input[name='+elemet.attr('name_op')+']').val($('#'+id).attr("data"));
        //Hacemos desaparecer el resto de sugerencias
        $('#'+content).fadeOut(300);

        $('input[name='+asoc+']').val($('#'+id).attr("data2"));

      });

      elemet.keyup( function(event) {
          var code = event.keyCode;

          if((code>47 && code<91)||(code>96 && code<123) || code == 8 ) {
            $.ajax({
              type: 'post',
              url: baseurl + elemet.attr('uri'),
              data: { autocomplete: elemet.val() },
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

                  $('input[name='+asoc+']').val($('#'+id).attr("data2"));
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

                  $('input[name='+asoc+']').val($('#'+id).attr("data2"));
                  return false;
                }
            });
          }
        }
      );

    },
  });
  //$('body').click(function(){ $('#'+content).fadeOut(300); });
  elemet.focusout(function () {
    $('#'+content).fadeOut(300);
  });
  $('input[name='+elemet.attr('siguiente')+']').focus(function(){ $('#'+content).fadeOut(300); });
}

var autocomplete_asoc2 = function() {
  var mark = 0;
  var elemet = $(this);
  var content = elemet.attr('content');
  var asoc = elemet.attr('asociacion');
  var asoc2 = elemet.attr('asociacion2');
  $.ajax({
    type: 'post',
    url: baseurl + elemet.attr('uri'),
    data: { autocomplete: elemet.val() },
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
      $('body').click(function(){ $('#'+content).fadeOut(300); });
      $('input').focus(function(){ $('#'+content).fadeOut(300); });
      $('.suggest-element').on('click', function(){
        //Obtenemos la id unica de la sugerencia pulsada
        var id = $(this).attr('ida');
        //Editamos el valor del input con data de la sugerencia pulsada
        elemet.val($('#'+id).attr("data1"));
        $('input[name='+elemet.attr('name_op')+']').val($('#'+id).attr("data"));
        //Hacemos desaparecer el resto de sugerencias
        $('#'+content).fadeOut(300);
        $('input[name='+asoc+']').val($('#'+id).attr("data2"));
        $('input[name='+asoc2+']').val($('#'+id).attr("data3"));

      });

      elemet.keyup( function(event) {
          var code = event.keyCode;

          if((code>47 && code<91)||(code>96 && code<123) || code == 8 ) {
            $.ajax({
              type: 'post',
              url: baseurl + elemet.attr('uri'),
              data: { autocomplete: elemet.val() },
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

                  $('input[name='+asoc+']').val($('#'+id).attr("data2"));
                  $('input[name='+asoc2+']').val($('#'+id).attr("data3"));
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

                  $('input[name='+asoc+']').val($('#'+id).attr("data2"));
                  $('input[name='+asoc2+']').val($('#'+id).attr("data3"));
                  return false;
                }
            });
          }
        }
      );

    },
  });
  //$('body').click(function(){ $('#'+content).fadeOut(300); });
  elemet.focusout(function () {
    $('#'+content).fadeOut(300);
  });
  $('input[name='+elemet.attr('siguiente')+']').focus(function(){ $('#'+content).fadeOut(300); });
}

var autocomplete_asoc2_text_area = function() {
  var mark = 0;
  var elemet = $(this);
  var content = elemet.attr('content');
  var asoc = elemet.attr('asociacion');
  var asoc2 = elemet.attr('asociacion2');
  $.ajax({
    type: 'post',
    url: baseurl + elemet.attr('uri'),
    data: { autocomplete: elemet.val() },
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
      $('body').click(function(){ $('#'+content).fadeOut(300); });
      $('input').focus(function(){ $('#'+content).fadeOut(300); });
      $('.suggest-element').on('click', function(){
        //Obtenemos la id unica de la sugerencia pulsada
        var id = $(this).attr('ida');
        //Editamos el valor del input con data de la sugerencia pulsada
        elemet.val($('#'+id).attr("data1"));
        $('input[name='+elemet.attr('name_op')+']').val($('#'+id).attr("data"));
        //Hacemos desaparecer el resto de sugerencias
        $('#'+content).fadeOut(300);
        $('input[name='+asoc+']').val($('#'+id).attr("data2"));
        $('textarea[name='+asoc2+']').val($('#'+id).attr("data3"));
      });

      elemet.keyup( function(event) {
          var code = event.keyCode;

          if((code>47 && code<91)||(code>96 && code<123) || code == 8 ) {
            $.ajax({
              type: 'post',
              url: baseurl + elemet.attr('uri'),
              data: { autocomplete: elemet.val() },
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

                  $('input[name='+asoc+']').val($('#'+id).attr("data2"));
                  $('textarea[name='+asoc2+']').val($('#'+id).attr("data3"));
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

                  $('input[name='+asoc+']').val($('#'+id).attr("data2"));
                  $('textarea[name='+asoc2+']').val($('#'+id).attr("data3"));
                  return false;
                }
            });
          }
        }
      );

    },
  });
  //$('body').click(function(){ $('#'+content).fadeOut(300); });
  elemet.focusout(function () {
    $('#'+content).fadeOut(300);
  });
  $('input[name='+elemet.attr('siguiente')+']').focus(function(){ $('#'+content).fadeOut(300); });
}

var autocomplete_asoc3 = function() {
  var mark = 0;
  var elemet = $(this);
  var content = elemet.attr('content');
  var asoc = elemet.attr('asociacion');
  var asoc2 = elemet.attr('asociacion2');
  var asoc21 = elemet.attr('asociacion2_1');
  var asoc3 = elemet.attr('asociacion3');
  $.ajax({
    type: 'post',
    url: baseurl + elemet.attr('uri'),
    data: { autocomplete: elemet.val() },
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
      $('body').click(function(){ $('#'+content).fadeOut(300); });
      $('input').focus(function(){ $('#'+content).fadeOut(300); });
      $('.suggest-element').on('click', function(){
        //Obtenemos la id unica de la sugerencia pulsada
        var id = $(this).attr('ida');
        //Editamos el valor del input con data de la sugerencia pulsada
        elemet.val($('#'+id).attr("data1"));
        $('input[name='+elemet.attr('name_op')+']').val($('#'+id).attr("data"));
        //Hacemos desaparecer el resto de sugerencias
        $('#'+content).fadeOut(300);
        $('input[name='+asoc+']').val($('#'+id).attr("data2"));
        $('input[name='+asoc2+']').val($('#'+id).attr("data3"));
        $('input[name='+asoc21+']').val($('#'+id).attr("data31"));
        $('input[name='+asoc3+']').val($('#'+id).attr("data4"));

      });

      elemet.keyup( function(event) {
          var code = event.keyCode;

          if((code>47 && code<91)||(code>96 && code<123) || code == 8 ) {
            $.ajax({
              type: 'post',
              url: baseurl + elemet.attr('uri'),
              data: { autocomplete: elemet.val() },
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

                  $('input[name='+asoc+']').val($('#'+id).attr("data2"));
                  $('input[name='+asoc2+']').val($('#'+id).attr("data3"));
                  $('input[name='+asoc21+']').val($('#'+id).attr("data31"));
                  $('input[name='+asoc3+']').val($('#'+id).attr("data4"));

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

                  $('input[name='+asoc+']').val($('#'+id).attr("data2"));
                  $('input[name='+asoc2+']').val($('#'+id).attr("data3"));
                  $('input[name='+asoc21+']').val($('#'+id).attr("data31"));
                  $('input[name='+asoc3+']').val($('#'+id).attr("data4"));

                  return false;
                }
            });
          }
        }
      );

    },
  });
  //$('body').click(function(){ $('#'+content).fadeOut(300); });
  elemet.focusout(function () {
    $('#'+content).fadeOut(300);
  });
  $('input[name='+elemet.attr('siguiente')+']').focus(function(){ $('#'+content).fadeOut(300); });
}


var autocomplete_func = function (elemet, func) {
  //variable que controla los autocomplete para teclados
  var mark = 0;
  var content = elemet.attr('content');
  $.ajax({
    type: 'post',
    url: baseurl + elemet.attr('uri'),
    data: { autocomplete: elemet.val() },
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
        //Ejecutamos funcion obtenida
        func();
      });

      elemet.keyup( function(event) {
          var code = event.keyCode;

          if((code>47 && code<91)||(code>96 && code<123) || code == 8 ) {
            $.ajax({
              type: 'post',
              url: baseurl + elemet.attr('uri'),
              data: { autocomplete: elemet.val() },
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
                  //Ejecutamos funcion obtenida
                  func();
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
                  //Ejecutamos funcion obtenida
                  func();
                  return false;
                }
            });
          }
        }
      );
    },
  });
  //$('body').click(function(){ $('#'+content).fadeOut(300); });
  elemet.focusout(function () {
    $('#'+content).fadeOut(300);
  });
  $('input[name='+elemet.attr('siguiente')+']').focus(function(){ $('#'+content).fadeOut(300); });
}

jQuery.extend({
    stepForm: function(txtBack, txtNext, token){
    	var fieldsets = $((token || 'fieldset'), $("form.stepMe"));
      var cont_form = $('.content-form');
      var barra = $('.barra_carga', $('.content-form'));
      var w = $(cont_form).width();
      var total = $(fieldsets).length;
      var pcu = w/total;
      $(barra).css('width', pcu + 'px');
	    $(fieldsets).each(function(x,el){
			    if (x > 0) {
			      $(el).hide();
			      $(el).append('<a class="backStep btn btn-primary" href="#">'+ (txtBack || 'Volver') +'</a>');
			      $(".backStep", $(el)).bind("click", function(){
                $("#x_" + (x - 1)).show();
                $(el).hide();
                $(barra).css('width', pcu*(x) + 'px');
			       });
			    }

			    if ((x+1)< total) {
  	        $(el).append('<a class="nextStep btn btn-primary" href="#">'+(txtNext || 'Seguir')+'</a>');
  	        $(".nextStep", $(el)).bind("click", function(){
              $("#x_" + (x + 1)).show();
              $(el).hide();
              $(barra).css('width', pcu*(x+2) + 'px');
  	        });
			    }
			    $(el).attr("id", "x_" + x);
	    });
    }
});
