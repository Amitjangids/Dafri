

      
    function scrollToElement(element,speed)
    {
        $('html, body').animate({scrollTop:$(element).position().top + 10},speed);
    }



    var headerHeight = $('header').outerHeight();
    $(window).scroll(function(){
        if ($(window).scrollTop() >= 0.1) {
            $('header').addClass('fixed-header');
            $('body').css('padding-top', headerHeight);
        }
        else {
            $('header').removeClass('fixed-header');
            $('body').css('padding-top', 0);
        }
    });

    $("#main-menu-toggle").click(function(){
          $(".main-menu").addClass("show-main-menu-bx");
          $(".black-layer").addClass("show-black-layer");
          $("body").addClass("overflow-off");
          $("html").addClass("overflow-off");
          
      });

      $("#close-main-menu, .main-menu ul li a").click(function(){
          $(".main-menu").removeClass("show-main-menu-bx");
          $(".black-layer").removeClass("show-black-layer");
          $("body").removeClass("overflow-off");
          $("html").removeClass("overflow-off");
      });

      $(".black-layer").click(function(){
          $(".main-menu").removeClass("show-main-menu-bx");
          $(".black-layer").removeClass("show-black-layer");
          $("body").removeClass("overflow-off");
          $("html").removeClass("overflow-off");
      });

      $("#sub-header-open-btn").click(function(){
        $("#sub-header-box").slideToggle();
      });

  