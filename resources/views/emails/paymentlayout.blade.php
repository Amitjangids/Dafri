<!DOCTYPE html>
<html>
    <head>
        <title>DafriBank Receipt Welcome</title>
        <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet">
        <style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style>
    </head>
    <body>
        <table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; ">
            <tbody>
                <tr>
                    <td align="center">
                        <table class="col-600" width="900" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;">
                            <tr align="center">
                                <td style="padding: 50px 0; width: 100%"><a href="javascript:void(0);"><img src="<?php echo HTTP_PATH; ?>/public/img/dafribank-logo-black.png" width="180"></a></td>
                            </tr>
                            @yield('content')
                            <tr>
                                <td style="padding: 40px 0" align="center">Head to your <a href="{{HTTP_PATH}}/business-login" style="color: #1381D0; text-decoration: none; font-size: 18px">dashboard </a> to see more information on this payment
                                    <p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="{{HTTP_PATH}}/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page
                                        <br>
                                        <br>© {{date("Y")}} DafriBank Digital. All Rights Reserved. </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </body>
</html>