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

    $('#buscar').keyup(
      function (event) {
        var code = event.keyCode;

        if((code>47 && code<91)||(code>96 && code<123) || code == 8 ) {
          $('.table').remove();
          $.ajax({
            type: 'post',
            url: baseurl + $(this).attr('url'),
            data: { busca: $('#buscar').val().toUpperCase() },
            beforeSend: function(){
              $(".content_table > .limit-content > .table-responsive").html("<p id='cargando' align='center' class='icono icon-spinner'></p>");
              var angulo = 0;
              setInterval(function(){
                    angulo += 3;
                   $("#cargando").rotate(angulo);
              },10);
            },
            success: function(result) {
              $('.content_table > .limit-content > .table-responsive').html(result);
              $('.icon-eliminar').click(confirmar);
            },
          });
        }
      }
    );

    $(".close").click(function(){
        $("#myAlert").slideUp();
    });
});

/*FUNCIONES*/

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
    },

    /*
     * ajaxdata: es el data que recibe el controlador desde el data del ajax
     */
    autocomplete : function ( param ) {
      //variable que controla los autocomplete para teclados
      var mark = 0;

      var elemet = param['elemet'];
      var content = param['content'];
      var uri = param['url'];
      var ajaxdata = {autocomplete: ''}

      if (param.hasOwnProperty('addfunction')) {
        var func = param['addfunction'];
      }

      if (param.hasOwnProperty('asociacion1')) {
        var asociacion1 = param['asociacion1'];
      }

      if (param.hasOwnProperty('ajaxdata')) {
        ajaxdata = param['ajaxdata'];
      }

      elemet.focus(function() {
        ajaxdata['autocomplete'] = elemet.val();
        $.ajax({
          type: 'post',
          url: baseurl + uri,
          data: ajaxdata,
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
              $('input[name='+param['name']+']').val($('#'+id).attr("data"));
              //Hacemos desaparecer el resto de sugerencias
              $('#'+content).fadeOut(300);
              //Ejecutamos funcion obtenida
              if (param.hasOwnProperty('addfunction')) {
                func();
              }

              if (param.hasOwnProperty('asociacion1')) {
                $('input[name='+asociacion1+']').val($('#'+id).attr("data2"));
              }
            });
          },
        });
      });

      elemet.keyup( function(event) {
          var code = event.keyCode;

          if((code>47 && code<91)||(code>96 && code<123) || code == 8 ) {
            ajaxdata['autocomplete'] = elemet.val();
            $.ajax({
              type: 'post',
              url: baseurl + uri,
              data: ajaxdata,
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
                  $('input[name='+param['name']+']').val($('#'+id).attr("data"));
                  //Hacemos desaparecer el resto de sugerencias
                  $('#'+content).fadeOut(300);
                  //Ejecutamos funcion obtenida
                  if (param.hasOwnProperty('addfunction')) {
                    func();
                  }

                  if (param.hasOwnProperty('asociacion1')) {
                    $('input[name='+asociacion1+']').val($('#'+id).attr("data2"));
                  }
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
                  $('input[name='+param['name']+']').val($('#'+id).attr("data"));
                  //Hacemos desaparecer el resto de sugerencias
                  $('#'+content).fadeOut(300);
                  //Ejecutamos funcion obtenida
                  if (param.hasOwnProperty('addfunction')) {
                    func();
                  }

                  if (param.hasOwnProperty('asociacion1')) {
                    $('input[name='+asociacion1+']').val($('#'+id).attr("data2"));
                  }
                  return false;
                }
            });
          }
        }
      );

      //$('body').click(function(){ $('#'+content).fadeOut(300); });
      elemet.focusout(function () {
        $('#'+content).fadeOut(300);
      });
      $('input[name='+param['siguiente']+']').focus(function(){ $('#'+content).fadeOut(300); });
    }
});
