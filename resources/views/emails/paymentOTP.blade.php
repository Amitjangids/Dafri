@extends('emails.layout')
@section('content')

<tr>
    <td style="padding-top: 25px">
        <h4 style="padding:0px 0px 0px 0px; font-size:16px; text-align: left;display: block; color:#242528;"><span>Dear </span> {{strtoupper($userName)}},</h4>

        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379; line-height: 20px">
            You have requested a OTP to Access your DafriBank Online banking profile.
        </p>
        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379;line-height: 20px ">
            Verification code:
        <p><b>{{$otp}}</b></p> 
        </p>
        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379;line-height: 20px "> The verification code will be valid for {{OTP_TIME}} minutes. Please do not share this code with anyone.
        </p>
        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379;line-height: 20px ">If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a>
            or call 011 568 5053.
        </p>
        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379; line-height: 20px">Yours Sincerely</p>
        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379;line-height: 1px">{{MAIL_SIGNATURE}}</p>

    </td>
</tr>




@endsection

