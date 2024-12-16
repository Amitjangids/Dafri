@extends('layouts.inner')
@section('content')
<div class="d-flex" id="wrapper">
    @include('elements.side_menu')        
    <!-- Page Content -->
    <div id="page-content-wrapper">
        @include('elements.top_header')
        <div class="wrapper2">
       
                <div class="table-box col-sm-12">
                    <div class="ref-link">
                        <div class="ref-link-box">
                            <h6>My Payment link</h6>
                            <span>
                                <p>Why expose your banking details online when you can give your clients a payment link to tap and pay you or your business and your money enters your bank account while you are home having dinner?</p>
                                <p> The ePayMe Powered by DafriBank™ is easy to use and available to every DafriBank account holder. It supports DafriBank W2W, ePay, OZOW EFT and USDT</p>
                            </span>
                        </div>
                        @if (empty($refCodes[0]->id))
                        <a href="{{URL::to('auth/generate-merchant-payment-link')}}">Generate link</a>
                        @endif
                    </div>
                    <div class="table-responsive payment-table">
                        <table class="table">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col">Link</th>
                                    <th scope="col">Copy Link</th>
                                </tr>
                            </thead>
                            @if (!empty($refCodes))
                            <tbody>
                                @foreach ($refCodes as $refcode)
                                <?php 
                                $res = getUserByUserId($refcode->user_id);
                                ?>
                                <tr>
                                    <td>{{HTTP_PATH}}/merchant-payment/{{$refcode->slug}}</a></td>
                                    <td>
                                    <a href="javascript:void(0);" class="cp-link" onclick="copyTextToClipboard('{{HTTP_PATH}}/merchant-payment/{{$refcode->slug}}');">Copy</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            @endif
                        </table>
                    </div>
                    <!-- <p class="stat-pata">Our affiliate partners are entitled to a whopping 25% of the total revenue we earn on fees from transactions carried out by each of their customers.</p> -->
                </div>

            </div>
        </div>
    </div>
    <!-- /#page-content-wrapper -->
</div>
<script src="<?php echo HTTP_PATH; ?>/public/assets/js/front/top_search.js"></script>
<script>
                                function deleteRefCode(refCode)
                                {
                                    $('#confim_btn').attr('onClick', 'deleteFunc(' + refCode + ')');
                                    $('#basicModal').modal('show');
//                            var a = confirm('Do you really want to delete this referral code? All the benifits of this referral will be flused.');
//                            if (a) {
//                                location.href = '/dafri/auth/deleteRefCode/' + refCode;
//                            }
                                }

                                function deleteFunc(refCode) {
                                    location.href = '<?php echo HTTP_PATH; ?>/auth/deleteRefCode/' + refCode;
                                }

                                function myFunction() {
                                    /* Get the text field */
                                    var copyText = document.getElementById("myInput");

                                    /* Select the text field */
                                    copyText.select();
                                    copyText.setSelectionRange(0, 99999); /* For mobile devices */

                                    /* Copy the text inside the text field */
                                    document.execCommand("copy");

                                    /* Alert the copied text */
//                            alert("Copied the text: " + copyText.value);
                                    $('#blank_message').html("Copied the text: " + copyText.value);
                                    $('#blank-alert-Modal').modal('show');
                                }
                                
                                function copyTextToClipboard(text) {
                                    var textArea = document.createElement("textarea");
                                    textArea.value = text
                                            document.body.appendChild(textArea);
                                    textArea.focus();
                                    textArea.select();
                                    try {
                                    var successful = document.execCommand('copy');
                                    var msg = successful ? 'successful' : 'unsuccessful';
//                                    alert("Your account details copied successfully");
                                    $('#blank_message').html('Your payment link copied successfully');
                                                                    $('#blank-alert-Modal').modal('show');
                                    //console.log('Copying text command was ' + msg);
                                    } catch (err) {
                                    console.log('Oops, unable to copy');
                                    }

                                    document.body.removeChild(textArea);
                                    }
</script>

<!-- basic modal -->
<div class="modal x-dialog fade" id="basicModal" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body ">
                <p class="text-center"><strong>Do you really want to delete this referral code? All the benefits of this referral will be flushed.</strong></p>
                <ul class="list-inline btn-list">
                    <li class="list-inline-item"><button type="button" class="btn btn-dark" id="confim_btn" onclick="deleteFunc();">OK</button></li>
                    <li class="list-inline-item"><button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button></li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection