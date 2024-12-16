<!DOCTYPE html>
<html>

    <head>
        <title>DafriBank Receipt</title>
        <style type="text/css">
            @font-face {
                font-family: 'LiberationSerifRegular';
                src: url('../../../public/pdf/LiberationSerifRegular.eot');
                src: url('../../../public/pdf/LiberationSerifRegular.eot') format('embedded-opentype'),
                    url('../../../public/pdf/LiberationSerifRegular.woff2') format('woff2'),
                    url('../../../public/pdf/LiberationSerifRegular.woff') format('woff'),
                    url('../../../public/pdf/LiberationSerifRegular.ttf') format('truetype'),
                    url('../../../public/pdf/LiberationSerifRegular.svg#LiberationSerifRegular') format('svg');
            }


            body {
                padding: 0;
                margin: 0;
                font-family: 'LiberationSerifRegular';
                letter-spacing: normal;
            }

            table {
                border-spacing: 0px !important;
            }
        </style>
    </head>

    <body>
        <table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin: 0 auto;background: #fff; box-shadow:0 0 10px rgba(0,0,0,0.1);">
            <!-- START HEADER/BANNER -->
            <tbody>
                <tr>
                    <td>
                        <table border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important; padding: 0 0; margin:0 auto; ">
                            <tr align="right">
                                <td style="padding: 50px 0; width: 100%"><a href="#"><img src="<?php echo HTTP_PATH; ?>/public/img/dafribank-logo-black.png" width="250"></a></td>
                            </tr>
                            <tr>
                                <td>
                                    <table style="width:100%; margin: 0 auto; background: #fff;border-radius: 40px; padding: 0 0;">
                                        <tr>
                                            <td align="left" style="padding: 0 0 20px; width: 100%">
                                                <h1 style="font-size: 28px; margin-bottom: 15px;  margin-top:0px; font-weight: 600;">Payment Notification</h1>
                                                <p style="font-size: 16px; color: #8e8e8e;margin-top:20px; ">DafriBank is happy to confirm that the following payment has been made into your account.</p>
                                            </td>
                                            
                                        </tr>
                                        <tr>
                                            
                                            <td align="left" style="width: 100%; font-size: 15px; font-family: 'CorisandeRegular';"> Payment Date: {{$detl['payment_date']}}</span></td>
                                        </tr>
                                        <tr>
                                            <td style="width: 100%; padding: 0 0px; box-sizing: border-box;">
                                                <h4 style="font-size: 19px;text-transform: uppercase; color: #000;font-weight: 600;">BENEFICIARY DETAILS</h4>
                                                <table width="100%">
                                                    <tr>
                                                        <td align="left" style="padding: 20px 0;width: 50%; border-bottom: 1px solid #C7C7C7;font-size: 16px;">Name</td>
                                                        <td align="right" style="padding: 20px 0;width: 50%; border-bottom: 1px solid #C7C7C7;font-size: 16px;">{{$detl['receiverName']}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td align="left" style="padding: 20px 0;width: 50%; border-bottom: 1px solid #C7C7C7;font-size: 16px;">Bank</td>
                                                        <td align="right" style="padding: 20px 0;width: 50%; border-bottom: 1px solid #C7C7C7;font-size: 16px;">{{$detl['bank']}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td align="left" style="padding: 20px 0;width: 50%; border-bottom: 1px solid #C7C7C7;font-size: 16px;">Account number</td>
                                                        <td align="right" style="padding: 20px 0;width: 50%; border-bottom: 1px solid #C7C7C7;font-size: 16px;">{{$detl['account_number']}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td align="left" style="padding: 20px 0;width: 50%; border-bottom: 1px solid #C7C7C7;font-size: 16px;">Amount</td>
                                                        <td align="right" style="padding: 20px 0;width: 50%; border-bottom: 1px solid #C7C7C7;font-size: 16px;">{{$detl['amount']}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td align="left" style="padding: 20px 0;width: 50%; border-bottom: 1px solid #C7C7C7;font-size: 16px;">Reference</td>
                                                        <td align="right" style="padding: 20px 0;width: 50%; border-bottom: 1px solid #C7C7C7;font-size: 16px;word-break: break-all;">{{$detl['reference_note']= preg_replace("/([0-9|#][\x{20E3}])|[\x{00ae}|\x{00a9}|\x{203C}|\x{2047}|\x{2048}|\x{2049}|\x{3030}|\x{303D}|\x{2139}|\x{2122}|\x{3297}|\x{3299}][\x{FE00}-\x{FEFF}]?|[\x{2190}-\x{21FF}][\x{FE00}-\x{FEFF}]?|[\x{2300}-\x{23FF}][\x{FE00}-\x{FEFF}]?|[\x{2460}-\x{24FF}][\x{FE00}-\x{FEFF}]?|[\x{25A0}-\x{25FF}][\x{FE00}-\x{FEFF}]?|[\x{2600}-\x{27BF}][\x{FE00}-\x{FEFF}]?|[\x{2900}-\x{297F}][\x{FE00}-\x{FEFF}]?|[\x{2B00}-\x{2BF0}][\x{FE00}-\x{FEFF}]?|[\x{1F000}-\x{1F6FF}][\x{FE00}-\x{FEFF}]?/u", "", $detl['reference_note'])}}
                                                    
                                               </td>
                                                    </tr>
                                                    <tr>
                                                        <td align="left" style="padding: 20px 0;width: 50%; border-bottom: 1px solid #C7C7C7;font-size: 16px;">Transaction ID</td>
                                                        <td align="right" style="padding: 20px 0;width: 50%; border-bottom: 1px solid #C7C7C7;font-size: 16px;">{{$detl['transId']}}</td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 40px 0; width: 100%;" align="left">
                                            <br><br><br><br>
                                                <img src="<?php echo HTTP_PATH; ?>/public/img/pdf/dafri-short-logo.png" style="width:30px;">
                                                <p style="    font-size: 13px;
                                                   color: #000;
                                                   line-height: 18px; text-align: justify; ">
                                                    DafriBank Digital LTD is a bank duly licensed by the Central Bank of Comoros with banking License B2019005. DafriBank is a division of DafriGroup PLC, a public company incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Digital asset markets and exchanges are not regulated with the same controls or customer protections available with other forms of financial products and are subject to an evolving regulatory environment. Digital assets do not typically have legal tender status and are not covered by deposit protection insurance. The past performance of a digital asset is not a guide to future performance, nor is it a reliable indicator of future results or performance. Additional disclosures can be found on the Legal and Privacy page


                                                    <br><br>
                                                    &copy; <?php echo date('Y'); ?> DafriBank Digital LTD. All Rights Reserved. A DafriGroup PLC Company
                                                </p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </body>
</html>
