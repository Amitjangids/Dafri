@extends('emails.layout')
@section('content')
<tr>
    <td>
       <h4 style="padding:0px 0px 0px 0px; font-size:16px; text-align: left;display: block; color:#242528;"><span>Dear </span> {{strtoupper($userName)}},</h4>

        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379;line-height: 20px ">
           Your paypal request with transaction ID {{$TransId}} has been submitted successfully and will be processed shortly.
        </p>
       
        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379;line-height: 20px ">If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a>
            or call 011 568 5053.
        </p>
        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379; line-height: 20px">Yours Sincerely</p>
        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379;line-height: 1px">{{MAIL_SIGNATURE}}</p>

  
    </td>
</tr>
  
@endsection
