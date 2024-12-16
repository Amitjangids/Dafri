<!DOCTYPE html>
<html>

    <head>
        <title>DafriBank business-account</title>
        <style type="text/css">
            @font-face {
                font-family: 'CorisandeRegular';
                src: url('../../../public/pdf/CorisandeRegular.eot');
                src: url('../../../public/pdf/CorisandeRegular.eot') format('embedded-opentype'),
                    url('../../../public/pdf/CorisandeRegular.woff2') format('woff2'),
                    url('../../../public/pdf/CorisandeRegular.woff') format('woff'),
                    url('../../../public/pdf/CorisandeRegular.ttf') format('truetype'),
                    url('../../../public/pdf/CorisandeRegular.svg#CorisandeRegular') format('svg');
            }

            @font-face {
                font-family: 'CorisandeBold';
                src: url('../../../public/pdf/CorisandeBold.eot');
                src: url('../../../public/pdf/CorisandeBold.eot') format('embedded-opentype'),
                    url('../../../public/pdf/CorisandeBold.woff2') format('woff2'),
                    url('../../../public/pdf/CorisandeBold.woff') format('woff'),
                    url('../../../public/pdf/CorisandeBold.ttf') format('truetype'),
                    url('../../../public/pdf/CorisandeBold.svg#CorisandeBold') format('svg');
            }

            @font-face {
                font-family: 'CorisandeLight';
                src: url('../../../public/pdf/CorisandeLight.eot');
                src: url('../../../public/pdf/CorisandeLight.eot') format('embedded-opentype'),
                    url('../../../public/pdf/CorisandeLight.woff2') format('woff2'),
                    url('../../../public/pdf/CorisandeLight.woff') format('woff'),
                    url('../../../public/pdf/CorisandeLight.ttf') format('truetype'),
                    url('../../../public/pdf/CorisandeLight.svg#CorisandeLight') format('svg');
            }

            body {
                padding: 0;
                margin: 0;
                font-family: 'CorisandeRegular';
            }
        </style>
    </head>

    <body>
        <table width="850px" border="0" cellpadding="0" cellspacing="0" style="margin: 0 auto; background: #fff; box-shadow:0 0 10px rgba(0,0,0,0.1);">
            <tr>
                <th align="left" bgcolor="#000" style="padding:40px;"><a href="#"><img src="<?php echo HTTP_PATH; ?>/public/img/pdf/dafri-white-logo.png" width="198"></a></th>
                <th align="right" bgcolor="#000" style="color:#fff; padding:40px; font-weight: normal; line-height: 24px;">
                    P.B. 1257, Bonovo Road, <br>
                    Fomboni, Comoros<br>
                    T: 011 568 5053<br>
                    hello@dafribank.com<br>
                    www.dafribank.com
                </th>
            </tr>
            <tr>
                <td bgcolor="#fff" style="height:10px; margin-top: 10px;"></td>
                <td bgcolor="#fff" style="height:10px; margin-top: 10px; "></td>
            </tr>
            <tr>
                <td bgcolor="#000" style="height:10px; margin-top: 10px;"></td>
                <td bgcolor="#000" style="height:10px; margin-top: 10px; "></td>
            </tr>
            <tr>
                <td colspan="2" style="color:#474748; padding:40px; font-weight: normal; line-height: 24px;">
                    <p style="width: 200px;">
                        {{ucfirst($detl['address'])}} <br> {{$detl['address2']}}
                    </p>
                </td>
            </tr>
            <tr>
                <td style="color:#000; padding:0 40px 40px; font-weight: normal; line-height: 24px;font-family: 'CorisandeBold'; font-size: 24px;" colspan="2">
                    {{$detl['userName']}}
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding:0 40px 20px;">
                    <h4 style="font-size:24px;margin: 0;">Dear client</h4>
                    <p style="font-size: 14px; line-height: 24px;">We are pleased to confirm that the above entity has a {{$detl['acc_type']}} account with DafriBank Digital LTD since <b>{{$detl['date']}}</b>
                    </p>
                </td>
            </tr>
            
            <tr>
                <td colspan="2"  style="padding:0 40px 20px;">

                    <table>
                        <tr>
                            <td width="250px" style="margin-bottom: 20px;"><strong><span style=" ">ACCOUNT NUMBER:</span></strong></td>
                            <td style="margin-bottom: 20px;"><strong>{{$detl['account_number']}}</strong></td>
                        </tr>
                        <tr>
                            <td><br></td>
                            <td><br></td>
                        </tr>
                        <tr>
                            <td width="250px"><strong><span style=" ">BRANCH CODE:</span></strong></td>
                            <td><strong>UNIVERSAL</strong></td>
                        </tr>
                    </table>


                </td>
            </tr>
            
            <tr>
                <td colspan="2" style="padding:0 40px 40px;">
            <p style="font-size: 14px; line-height: 24px;">We trust that the above information will be of assistance to you, and request that you contact the writer should require
                        further information in this regard and assure of our best service at all time </p>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding:0 40px 40px;">
                    <p>Yours Faithfully</p>
                    <br>
                    <img src="<?php echo HTTP_PATH; ?>/public/img/pdf/signature.png" style="width: 250px;">
                    <br>
                    <p>Miss. Catherine Anajemba, Director, <br> <br>
                        DafriBank Digital LTD 
                    </p>
                </td>
            </tr>


            <tr>
                <td colspan="2" style="padding:40px;">
                    <img src="<?php echo HTTP_PATH; ?>/public/img/pdf/dafri-short-logo.png" style="width: 50px;">
                    <br>
                    <p style="font-size:10px; color: #565656; text-align: justify; line-height:14px;">   DafriBank Digital LTD is a bank duly licensed by the Central Bank of Comoros with banking License B2019005. DafriBank is a division of DafriGroup PLC, a public company incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Digital asset markets and exchanges are not regulated with the same controls or customer protections available with other forms of financial products and are subject to an evolving regulatory environment. Digital assets do not typically have legal tender status and are not covered by deposit protection insurance. The past performance of a digital asset is not a guide to future performance, nor is it a reliable indicator of future results or performance. Additional disclosures can be found on the Legal and Privacy page


                        <br><br>
                        &copy;{{date('Y')}} DafriBank Digital LTD. All Rights Reserved. A DafriGroup PLC Company</p>
                </td>
            </tr>
        </table>