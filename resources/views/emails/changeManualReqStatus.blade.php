@extends('emails.layout')
@section('content')

<tr>
    <td>

        <h4 style="padding:0px 0px 0px 0px; font-size:16px; text-align: left;display: block; color:#242528;"><span>Hey </span> {{ strtoupper($userName)}},</h4>
        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379; line-height: 20px">
           Your {{$currency }} {{$amount }} EFT payment with transaction ID {{$TransId}} has left DafriBank and on it way to you. 
        </p>

        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379;line-height: 20px ">
        If this wasn't your action please e-mail us at hello@dafribank.com or call 011 568 5053.

        </p>
  
        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379; line-height: 20px">Yours Sincerely</p>
        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379;line-height: 1px">{{MAIL_SIGNATURE}}</p>

    </td>
</tr>

<tr>
    <td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a style="display: inline-block; font-size: 14px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="{{$loginLnk}}"> View your dashboard</a></td>
</tr>
@endsection
