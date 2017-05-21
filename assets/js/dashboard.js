$(document).ready(function() {
  $('.icon-circle-up').toggle(

    function(e) {
      $(this).removeClass("icon-circle-up");
      $(this).addClass("icon-circle-down");
      $(this).parent().siblings().slideUp(500);
    },

    function(e) {
      $(this).removeClass("icon-circle-down");
      $(this).addClass("icon-circle-up");
      $(this).parent().siblings().slideDown(500);
    }

  );

  $('.icon-cancel-circle').click(function() {
    $(this).parent().parent().slideUp(600);
  });
});
