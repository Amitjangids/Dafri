@extends('emails.layout')
@section('content')


<tr>
    <td align="center" style="padding: 0 0 20px; display: block; width: 100%">
        <h1 style="font-size: 25px; margin-bottom: 0; font-weight: 300;">Hello, {{strtoupper($userName)}}</h1>
        <h6 style="padding:3px 0px; font-size:14px;display: block; text-align: center; color:#727379; line-height: 20px">A ePay Card Deposit Request of {{$currency}} {{$amount}} has been successful.</h6>
    </td>
    <td align="center" style="display: block; width: 100%; padding-bottom: 36px;"><span  style="display: inline-block; font-size: 33px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);">{{$currency}} {{$amount}}</span></td>
</tr>
<tr>
    <td style="display: block; width: 100%; border-spacing: 0px !important; "><img src="<?php echo HTTP_PATH; ?>/public/img/divider-mail.png" style="width: 100%"></td>
</tr>
<tr>
    <td style="width: 100%; display: block; padding: 0 40px; box-sizing: border-box;">
        <h4 style="font-size: 20px;  text-transform: uppercase; font-weight: 400;">Payment details</h4>
        <table width="100%">
            <tr>
                <td align="left" style="padding: 15px 0;border-bottom: 1px solid #C7C7C7 ">Amount Received</td>
                <td align="right" style="padding: 15px 0;border-bottom: 1px solid #C7C7C7 ">{{$currency}} {{$amount}}</td>
            </tr>
            <tr>
                <td align="left" style="padding: 15px 0;border-bottom: 1px solid #C7C7C7 ">Transaction Fees</td>
                <td align="right" style="padding: 15px 0;border-bottom: 1px solid #C7C7C7 ">{{$currency}} {{$fees_amount}}</td>
            </tr>
            <tr>
                <td align="left" style="padding: 15px 0;border-bottom: 1px solid #C7C7C7 ">Payment Method</td>
                <td align="right" style="padding: 15px 0;border-bottom: 1px solid #C7C7C7 ">Debit/Credit Card</td>
            </tr>
            <tr>
                <td align="left" style="padding: 15px 0;border-bottom: 1px solid #C7C7C7 ">Transaction ID</td>
                <td align="right" style="padding: 15px 0;border-bottom: 1px solid #C7C7C7 ">{{$TransId}}</td>
            </tr>
        </table>
    </td>
</tr>
<tr>
    <td align="center" style="width: 100%; display: block;  font-size: 20px; padding: 40px 0 20px">{{date("D, F d, Y")}}</td>
</tr>
<tr>
    <td colspan="2"><a href="{{$loginLnk}}" style="padding:12px 0px; text-decoration:none; font-size:16px; width:240px; margin:0px auto; letter-spacing:1px; font-weight:bold;display: block; text-align: center; color:#ffffff; border-radius:30px; background:#242528;">View Your Dashboard</a></td></tr>

@endsection
