@extends('emails.layout')
@section('content')

<tr>
    <td>
        <h4 style="padding:0px 0px 0px 0px; font-size:16px; text-align: left;display: block; color:#242528;"><span>Dear </span> {{strtoupper($username)}},</h4>

        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379; line-height: 20px">
        Thank you for submitting your KYC information. 
        </p>

        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379; line-height: 20px">
        Unfortunately we were unable to accept your KYC due to one of the following reasons 
        </p>
       
        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379; line-height: 20px">
        • Low quality and blurred pictures, including pictures taken in the dark. 
        </p>

        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379; line-height: 20px">
        • No selfie or picture holding your Identity close to your face submitted.
        </p>

        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379; line-height: 20px">
        • Profile name different from the one in proof of address submitted
        </p>

        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379; line-height: 20px">
        • No selfie or picture taken holding a note written "DAFRIBANK" submitted.
        </p>

        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379; line-height: 20px">
        • Expired ID/Passport & drivers licence.
        </p>

        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379; line-height: 20px">
        • Proof of address not in a PDF format. 
        </p>

        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379; line-height: 20px">
        • Proof of address is older than 3 months. 
        </p>

        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379; line-height: 20px">
        • Address filled during registration not matching the address in the uploaded proof of address document.
        </p>

        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379; line-height: 20px">
        • Name or date of birth filled during registration not matching the ones in the uploaded document.
        </p>

        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379; line-height: 20px">
        • Fake and edited identity.
        </p>

        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379; line-height: 20px">
         We only accept proof of address in high quality PDF format. (No cellphone screenshots will be accepted)
        </p>

        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379; line-height: 20px">
        Proof of address can be a utility bill; electricity bill; bank statement, tax return, council tax, other financial document with the current residential address.
        </p>

        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379; line-height: 20px">
        To proceed further with your application, please login to your DafriBank online banking https://www.dafribank.com/ and navigate to compliance to re- submit your KYC documents in accepted format as described above 
        </p>

        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379; line-height: 20px">
        For further clarification or assistance on the aforementioned, please reach out to us on kyc@dafrigroup.com
        </p>


        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379; line-height: 20px">Yours Sincerely</p>
        <p style="padding:3px 0px; font-size:14px;display: block; text-align: left; color:#727379;line-height: 1px">Compliance Department </p>

    </td>
</tr>



@endsection
