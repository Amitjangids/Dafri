@extends('emails.layout')
@section('content')

<tr>
    <td align="center" style="padding: 0 0 20px; display: block; width: 100%">
        <h1 style="font-size: 25px; margin-bottom: 0; font-weight: 300;">Hello, DafriTechnologies LTD</h1></td>
    <td align="center" style="display: block; width: 100%; padding-bottom: 36px;"><span style="display: inline-block; font-size: 33px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #000; "> Feedback Type : {{$fbType}}</span></td>
</tr>
<tr>
    <td style="display: block; width: 100%; border-spacing: 0px !important; "><img src="' . HTTP_PATH . '/public/img/divider-mail.png" style="width: 100%"></td>
</tr>
<tr>
    <td style="width: 100%; display: block; padding: 0 40px; box-sizing: border-box;">
        <table width="100%">
            <tr>
                <td align="left" style="padding: 15px 0;border-bottom: 1px solid #C7C7C7 " width="30%">Ticket ID</td>
                <td align="right" style="padding: 15px 0;border-bottom: 1px solid #C7C7C7 " width="70%"> DBS-{{$TicketID}}</td>
            </tr>
            <tr>
                <td align="left" style="padding: 15px 0;border-bottom: 1px solid #C7C7C7 " width="30%">First Name</td>
                <td align="right" style="padding: 15px 0;border-bottom: 1px solid #C7C7C7 " width="70%">{{$fbFname}}</td>
            </tr>
            <tr>
                <td align="left" style="padding: 15px 0;border-bottom: 1px solid #C7C7C7 " width="30%">Last Name </td>
                <td align="right" style="padding: 15px 0;border-bottom: 1px solid #C7C7C7 " width="70%">{{$fbLname}}</td>
            </tr>
            <tr>
                <td align="left" style="padding: 15px 0;border-bottom: 1px solid #C7C7C7 " width="30%">Email</td>
                <td align="right" style="padding: 15px 0;border-bottom: 1px solid #C7C7C7 " width="70%">{{$fbEmail}}</td>
            </tr>
            <tr style="width: 100%;">
                <td align="left" style="padding: 15px 0;" width="100%">Description</td>
            </tr>
            <tr style="width: 100%;">
                <td align="left" style="" width="100%" colspan="2">
                    <p style="margin: 0; font-size: 14px; line-height: 27px;">{{$fbDesc}}</p>
                </td>
            </tr>
            <tr></tr>
            

            @endsection