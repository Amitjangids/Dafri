@extends('emails.layout')
@section('content')


<tr>
    <td>
        <h4 style="padding:0px 0px 0px 0px; font-size:16px; text-align: left;display: block; color:#242528;"><span>Greetings</span> {{strtoupper($userName)}},</h4>
        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379; line-height: 20px">
            Thank you for opening a DafriBank account.</p>
        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379;line-height: 20px ">
       Please deposit a minimum of USD 10 {{$usdString}}within the next 10 days to ensure your account stays active. Visit and <a href="<?php echo HTTP_PATH; ?>/personal-login" target="_blank">sign in</a> to your DafriBank account to see the available funding methods.</p>
   
        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379;line-height: 20px ">
            
            If you need any assistance, please e-mail us at
            <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a>
            <br>
            or call us on 0115 685 053.
                    </p>

        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379; line-height: 20px">Yours Sincerely</p>
        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379;line-height: 1px">{{MAIL_SIGNATURE}}</p>
    </td>
</tr>
<tr>
    <td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a  style="display: inline-block; font-size: 14px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="<?php echo HTTP_PATH; ?>/personal-login"> View your dashboard</a></td>
</tr>
@endsection

