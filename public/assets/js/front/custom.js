// Menu Toggle Script 
 
    $("#menu-toggle").click(function(e) {
      e.preventDefault();
      $("#wrapper").toggleClass("toggled");
    });
// hide-show
$(".but").click (function(){
  // Close all open windows
  $(".content").stop().slideUp(300); 
  // Toggle this window open/close
  $(this).next(".content").stop().slideToggle(300);
 
});

// 
$(function(){
  $("#bars li .bar").each(function(key, bar){
    var percentage = $(this).data('percentage');

    $(this).animate({
      'height':percentage+'%'
    }, 1000);
  })
})
