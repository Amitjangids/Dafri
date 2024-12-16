
        <style type="text/css">
            .website-modal .modal-dialog {max-width: 1000px;}
            .website-modal .modal-body{padding: 0; overflow: hidden;}
            .website-modal button.close {position: absolute;top: 0;right: 0;width: 40px;height: 40px;border-radius: 100%;background: #e9b30b;opacity: 1;color: #fff;font-size: 28px;display: flex;z-index: 99;align-items: center;justify-content: center;}
            .website-modal .modal-header {padding: 0;background: none;border: none;}
            .modal-banner-img{position: relative;}
            .giftcard-modal-content {position: absolute;top: 50%;left: 30px;transform: translateY(-50%); max-width: 50%;}
            .giftcard-modal-content h2 {font-size: 43px;color: #fff;text-transform: capitalize;font-weight: 500; line-height: 1.4; margin-bottom: 10px;}
            .giftcard-modal-content p {font-size: 22px;font-weight: 500;color: #fff;text-transform: capitalize;line-height: 1.4;}
            .giftcard-modal-content a.gitcard-btn {padding: 8px 20px;border: 1px solid #000;background: #e9b30b;color: #000;margin-top: 10px;display: inline-block;min-width: 150px; border: 1px solid transparent; text-align: center;font-size: 20px;border-radius: 10px;transition: 0.4s; text-decoration: none;}
            .giftcard-modal-content a.gitcard-btn:hover{background: transparent; color: #e9b30b; border-color: #e9b30b; text-decoration: none;}
            .modal-banner-img img{user-select: none; pointer-events: none; width: 100%; height: 100%;}
            .website-modal .modal-content{border: none;}

            @media only screen and (max-width: 1199px) {
               .website-modal .modal-dialog {max-width: 800px;margin: 30px auto!important;}
               .giftcard-modal-content h2 {font-size: 32px;}
               .giftcard-modal-content a.gitcard-btn {padding: 6px 20px;min-width: 130px;font-size: 18px;}
               .giftcard-modal-content p {font-size: 18px;max-width: 350px;}
            }
            @media only screen and (max-width: 991px) {
                .website-modal .modal-dialog {max-width: 700px;}
                .giftcard-modal-content h2 {font-size: 27px;}
                .giftcard-modal-content p {font-size: 16px; max-width: 300px;}
            }

            @media only screen and (max-width: 767px) {
                .website-modal.modal {top: 50%;left: 50%;height: auto;transform: translate(-50%, -50%);margin: 0 auto;}
                .website-modal button.close {width: 25px;height: 25px;font-size: 18px;border: none;top: inherit;right: -7px;}
                .website-modal .modal-dialog{padding: 10px;}
                .giftcard-modal-content h2 {font-size: 24px; margin-bottom: 10px;}
                .modal-banner-img img{object-fit: cover; height: 100%;}
                .giftcard-modal-content p {font-size: 15px; max-width: 270px;}
                .giftcard-modal-content a.gitcard-btn {padding: 5px 15px;min-width: 110px;font-size: 16px;margin-top: 10px;}
                .giftcard-modal-content{z-index: 1; max-width: 100%; text-align: left; left: 10px; right: 10px;}
            }


            @media only screen and (max-width: 576px) {
                .giftcard-modal-content h2 {font-size: 20px;margin-bottom: 5px;}
                .giftcard-modal-content p {font-size: 14px;max-width: 220px;}
                .giftcard-modal-content a.gitcard-btn {padding: 5px 13px;min-width: auto;font-size: 13px;margin-top: 0;}
            }

            @media only screen and (max-width: 480px) {
               .giftcard-modal-content{top: 58%;}
               .giftcard-modal-content h2 {font-size: 18px;}
               .giftcard-modal-content p {font-size: 12px;max-width: 180px;}
               .giftcard-modal-content a.gitcard-btn {padding: 4px 13px;font-size: 12px;margin-top: 0;}
            }

            @media only screen and (max-width: 380px) {
                .giftcard-modal-content h2 {font-size: 16px;}
                .giftcard-modal-content p {max-width: 150px; margin-bottom: 8px;}
                .giftcard-modal-content a.gitcard-btn {padding: 4px 11px;font-size: 11px;margin-top: 0;}
            }




        </style>

<!-- <button type="button" class="btn btn-primary giftward-button" data-backdrop="static" data-toggle="modal" data-target="#giftcardmodaldf">
  Launch demo modal
</button> -->


<!-- Modal -->
<div class="modal fade website-modal" data-backdrop="static" data-keyboard="false" id="giftcardmodaldf" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button  onClick="popup_content('hide')" type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="modal-banner-img">
            <img src="{{HTTP_PATH}}/public/img/front/banner-popup-image.PNG" alt="popup-image">
            <div class="giftcard-modal-content">
                <h2>Binance Gift Card <br> Now Available</h2>
                <p>Buy Binance Gift Card With Your DafriBank balance</p>
                <?php if(Session::get('user_id')) { ?>
                <a href="{{URL::to('auth/airtime_giftcard')}}" class="gitcard-btn">Buy Now</a>
                <?php }else{ ?>
                <a href="{{URL::to('personal-login')}}" class="gitcard-btn">Buy Now</a>
               <?php } ?>
            </div>
        </div>

      </div>
    </div>
  </div>
</div>
<script>
function popup_content(hideOrshow) {
        if (hideOrshow == 'hide') {
        $('#giftcardmodaldf').hide();
        $('#giftcardmodaldf').removeClass('show');
        document.cookie = "isClosed=true";
        } else {
        $('#giftcardmodaldf').show();
        $('#giftcardmodaldf').addClass('show');
        document.cookie = "isClosed=true";
        }
    }
    function getCookie(cname) {
        var name = cname + "=";
        var decodedCookie = decodeURIComponent(document.cookie);
        var ca = decodedCookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }

    var flag = getCookie("isClosed");

    if (flag == "") {
        window.onload = function() {
            setTimeout(function() {
                popup_content('show');
           
     }, 10000);
        }
    }

    </script>