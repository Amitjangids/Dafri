@extends('emails.layout')
@section('content')

<tr>
    <td>
        <h4 style="padding:0px 0px 0px 0px; font-size:16px; text-align: left;display: block; color:#242528;"><span>Dear </span> {{$username}},</h4>
        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379; line-height: 20px">
          We are happy to inform you that your KYC information has been reviewed successfully, and your DafriBank {{$type}} account has now been approved. Click 
          <a href="'.$lognLnk.'" target="_blank">here</a> to log in to your account.   
        </p>
       
        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379;line-height: 20px ">
          We wish you an awesome banking experience with us.  
        </p>
        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379;line-height: 20px ">
            If this is not you, please contact DafriBank Admin.
        </p>
        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379;line-height: 20px ">
                 If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a>
           or call us on 0115 685 053.
        </p>
        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379;line-height: 20px">
            Regards,
        </p> 
        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379;line-height: 1px"> 
            DafriBank Team</p>
    </td>
</tr>
@endsection

