@extends('emails.layout')
@section('content')
<tr>
    <td>
        <h4 style="padding:0px 0px 0px 0px; font-size:16px; text-align: left;display: block; color:#242528;">Dear</span> Admin,</h4>
        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379; line-height: 20px">
            You are receiving this email because we received a password reset request for your DafrrBank Admin Account.
        </p>
        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379;line-height: 14px ">
            Please find your new password below:
        </p>
        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379;line-height: 14px ">
            Email Address: {{$emailId}}
        </p>
        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379;line-height: 14px ">
            Username: {{$username}}
        </p>
        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379;line-height: 14px ">
            Password: {{$plainPassword}}
        </p>

        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379;line-height: 14px ">
            <a href="<?php echo HTTP_PATH;?>/admin">Click Here To Login</a>.
        </p>
    </td>
</tr>

<p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379; line-height: 20px">Yours Sincerely</p>
<p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379;line-height: 1px">{{MAIL_SIGNATURE}}</p>
@endsection


