@extends('emails.layout')
@section('content')
<tr>
    <td>
        <table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 0px; border: 0px solid #C7C7C7; padding: 30px 50px;">
            <tr>
                <td>
                    <h4 style="padding:0px 0px 0px 0px; font-size:16px; text-align: left;display: block; color:#242528;">Hey, {{strtoupper($userName)}}</h4>
                    <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379;line-height: 20px ">
                        {{ $body }}</p>
                    <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379;line-height: 20px ">
                        If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a> or call us on 0115 685 053.
                    </p>
                    <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379; line-height: 20px">Yours Sincerely</p>
                    <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379;line-height: 1px">{{MAIL_SIGNATURE}}</p>
                </td>
            </tr>
        </table>
    </td>
</tr>

@endsection


