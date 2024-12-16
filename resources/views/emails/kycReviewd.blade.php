@extends('emails.layout')
@section('content')

<tr>
    <td>

        <h4 style="padding:0px 0px 0px 0px; font-size:16px; text-align: left;display: block; color:#242528;"><span>Dear </span> {{strtoupper($username)}},</h4>
        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379; line-height: 20px">
            We are happy to inform you that your KYC has been successfully approved by the compliance department
        </p>
        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379;line-height: 20px ">
            If this action wasn't done by you, please contact us immediately to secure your account.   
        </p>    



        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379;line-height: 20px ">  If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a>
            or call us on 011 568 5053.</p>


        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379; line-height: 20px">Yours Sincerely</p>
        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379;line-height: 1px">{{MAIL_SIGNATURE}}</p>

    </td>
</tr>


@endsection


