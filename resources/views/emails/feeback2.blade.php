@extends('emails.layout')
@section('content')


    <h4 style="padding:0px 0px 0px 0px; font-size:16px; text-align: left;display: block; color:#242528;"><span>Dear </span> {{strtoupper($user_name)}},</h4>
    <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379; line-height: 20px">Thank you for getting in touch!</p>
    <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379; line-height: 20px">
        Thank you for getting in touch!
    </p>


    <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379;line-height: 20px ">
        Please note ticket ID {{$TicketID}} for future reference. Have a great day!
    </p>
    <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379;line-height: 20px ">
        For Security, this request was received from I.P. address: {{$ip}}
    </p>
   
    <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379;line-height: 20px ">If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a>
        or call 011 568 5053.
    </p>
    <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379; line-height: 20px">Yours Sincerely</p>
    <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379;line-height: 1px">{{MAIL_SIGNATURE}}</p>

    <tr>
        <td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"></td>
    </tr>
@endsection

