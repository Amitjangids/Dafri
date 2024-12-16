<!DOCTYPE html>
<html>

    <head>
        <title>DafriBank e-Statement</title>
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

            @font-face {
                font-family: 'GothamMediumRegular';
                src: url('../../../public/pdf/GothamMediumRegular.eot');
                src: url('../../../public/pdf/GothamMediumRegular.eot') format('embedded-opentype'),
                    url('../../../public/pdf/GothamMediumRegular.woff2') format('woff2'),
                    url('../../../public/pdf/GothamMediumRegular.woff') format('woff'),
                    url('../../../public/pdf/GothamMediumRegular.ttf') format('truetype'),
                    url('../../../public/pdf/GothamMediumRegular.svg#GothamMediumRegular') format('svg');
            }

            body {
                padding: 0;
                margin: 0;
                font-family: 'CorisandeRegular';
            }

            table {
                border-spacing: 0px !important;
            }

            p,
            td {
                font-size: 14px;
            }

            .after:after {
                position: absolute;
                right: -59px;
                content: "";
                height: 100%;
                background: #fff;
                width: 100%;
                top: 0;
                z-index: 0;
            }

/*            .strip tr:nth-child(even) {
                background: #f00;
            }
            .strip tr:nth-child(odd) {
                background: #ffffff;
            }*/

            td {
                font-size: 15px;
            }

            footer {
                position: fixed; 
                bottom: -40px; 
                left: 0px; 
                right: 0px;
                height: 50px; 
                width: 100%;

            }
            .page_break { page-break-before: always; }
            .page-number:before {
                content: "Page " counter(page);
            }

            footer:last-child
            {
                display:none;
            }

/*            footer:last-child {
                background-color: yellow;
            }*/
        </style>
    </head>

    <body>
         <script type="text/php">
        <?php /*if (isset($pdf)) {
            echo $text = "page {PAGE_NUM} / {PAGE_COUNT}";
            $size = 10;
            $font = $fontMetrics->getFont("Verdana");
            $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
            $x = ($pdf->get_width() - $width) / 2;
            $y = $pdf->get_height() - 35;
            $pdf->page_text($x, $y, $text, $font, $size);
    
        } */?>
    </script>



        <table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin: 0 auto; background: #fff; box-shadow:0 0 10px rgba(0,0,0,0.1);">
            <!-- START HEADER/BANNER -->
            <tbody>
                <tr>
                    <td>
                        <table border="0" cellpadding="0" cellspacing="0" style="border-spacing:0px !important; padding: 0 0; width: 100%;">
                            <tr align="left">
                                <td style="padding: 50px 0 0; "><a href="#"><img src="<?php echo HTTP_PATH; ?>/public/img/dafribank-logo-black.png" width="250"></a></td>
                            </tr>
                            <tr align="right">
                                <td align="right" colspan="3" width="100%">
                                    <p style="margin-bottom:30px; line-height: 24px; font-family: 'GothamMediumRegular'; padding-right:-5px"> P.B 1257, Bonovo Road<br>
                                    Fomboni, Comoros</p>
                                </td>
                            </tr>
                            <tr>
                                <td width="40%" colspan="1">
                                    <p style="margin-bottom:30px; line-height: 20px;">
                                        BBST21 <br>
                                        {{strtoupper($detl['name'])}}<br>
                                        @if($detl['addrs_line1']!="")
                                        {{ucfirst($detl['addrs_line1'])}}<br>
                                        @endif
                                        @if($detl['addrs_line2']!="")
                                        {{ucfirst($detl['addrs_line2'])}}<br>
                                        @endif
                                        {{$detl['email']}}</p>
                                </td>
                                <td align="right" width="60%" colspan="2">
                                    <p style="margin-bottom: 0; line-height: 24px;">
                                    Customer VAT Registration Number : Not Provided<br>
                                    Bank Registration Number: B2019005<br>
                                    Tax Invoice/Statement Number : 21
                                    </p>
                                    <p style=" line-height: 24px; margin: 0;">Total VAT Charged: 15%<br>
                                        Statement Period : {{$detl['statement_period']}}
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td align="right" colspan="3" width="100%" style="font-family: Arial, sans-serif; font-weight: 600;">Statement Date : {{date('d F Y')}}</td>
                            </tr>
                            <tr>
                                <td colspan="2" width="70%">
                                    <table width="100%">
                                        @php $i = 0; @endphp
                                        @foreach ($transPDF as $tran)
                                        @if($i == 0)
                                        @if($tran->user_id == $detl['user_id'])
                                        @php 
                                        if(strpos($tran->trans_for,'(Refund)') !== false)
                                        {   
                                        $openingBal = $tran->user_close_bal - $tran->real_value; 
                                        }
                                        else{
                                        $openingBal = $tran->user_close_bal + $tran->sender_real_value;    
                                        }
                                        
                                        @endphp
                                        @else
                                        @php $openingBal = $tran->receiver_close_bal - $tran->real_value; @endphp
                                        @endif

                                        @endif

                                        @if($tran->user_id == $detl['user_id'])
                                        @php 
                                        $totalCredit = $tran->user_close_bal; 
                                        $totalDebit = $tran->user_close_bal;  
                                        @endphp
                                        @else
                                        @php 
                                        $totalCredit = $tran->receiver_close_bal; 
                                        $totalDebit = $tran->receiver_close_bal; 
                                        @endphp
                                        @endif


                                        @if($tran->user_id == $detl['user_id'])
                                        @php $closeBal = $tran->user_close_bal; @endphp
                                        @else
                                        @php $closeBal = $tran->receiver_close_bal; @endphp
                                        @endif
                                        @php $i++;@endphp
                                        @endforeach
                                        <tr>
                                            <td align="left" colspan="3" style="padding: 20px 0 10px;"><strong style="font-family: Arial, sans-serif; font-weight: 600; font-size: 24px;">DafriBank Digital {{$detl["acc_type"]}}: {{$detl["acc_number"]}}</strong>
                                            </td>
                                        </tr>
                                        <tr style=" padding: 10px 0 0;">
                                            <td align="left" colspan="2" style="border-bottom:1px solid #000; font-size: 22px; font-weight: 600;font-family: Arial, sans-serif;">Summary</td>
                                            <td align="right" style=" border-bottom:1px solid #000;font-size: 22px; font-weight: 600;font-family: Arial, sans-serif;">{{$detl['acc_currency']}}</td>
                                        </tr>
                                        <tr>
                                            <td align="left" colspan="2" style="padding:20px 0 5px;font-weight: 600; font-size: 18px;">Opening Balance</td>
                                            <td align="right" style="  padding:20px 0 5px;font-weight: 600;font-size: 18px;"><strong>{{number_format(floor($openingBal*100)/100,2,'.',',')}} Cr</strong></td>
                                        </tr>
                                        <tr>
                                            <td width="33.33%" style="font-weight: 600;">Funds Received (Credits) </td>
                                            <td width="33.33%" style="font-weight: 600;" align="center">{{$detl['ttlCrdtBankCnt']+ $detl['ttlCashDepositCount_1_fund_transfer_count'] + $detl['ttlAgentCrtCnt'] + $detl['ttlCrdtCptCnt'] + $detl['ttlCrdtOzowCnt'] + $detl['ttlCashDepositCount']+$detl['merchant_widraw_total_count']+$detl['total_refund_credit_count'] +$detl['fund_transfer_to_new_user_count']+$detl['ttlAccCredit_Online_payment_count']+$detl['currency_conversion_count']+$detl['ttlCashDepositCount_1_fund_transfer_count_agent']+$detl['ttlCashDeposit_1_fund_receiver_count']+$detl['total_dba_cash_received_count']+$detl["total_epay_me_received_count"]}}</td>
                                            <td width="33.33%" style="font-weight: 600;" align="right"> {{$detl['ttlCrdtAmount']+$detl["total_dba_cash_received"]}} Cr</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">
                                                <table width="100%" style="border:1px solid #000; padding:10px; ">
                                                    <tr style="margin-bottom: 10px;">
                                                        <td width="33.33%" style="padding:0 0 0px">Bank Transfer </td>
                                                        <td width="33.33%" style="padding:0 0 0px" align="center">{{$detl['ttlCrdtBankCnt']}}</td>
                                                        <td width="33.33%" style="padding:0 0 0px" align="right">{{number_format($detl['ttlCrdtBank'], 2, '.', ',')}}</td>
                                                    </tr>
                                                    <tr style="margin-bottom: 10px;">
                                                        <td width="33.33%" style="padding:0 0 0px">W2W Credit </td>
                                                        <td width="33.33%" align="center" style="padding:0 0 0px">{{$detl['ttlCashDepositCount_1_fund_transfer_count']+$detl['fund_transfer_to_new_user_count']+$detl['ttlCashDeposit_1_fund_receiver_count']}}</td>
                                                        <td width="33.33%" align="right" style="padding:0 0 0px"> {{number_format($detl['ttlCashDeposit_1_fund_transfer']+$detl['fund_transfer_to_new_user']+$detl['ttlCashDeposit_1_fund_receiver'], 2, '.', ',')}}</td>
                                                    </tr>
                                                    <tr style="margin-bottom: 10px;">
                                                        <td width="33.33%" style="padding:0 0 0px">Bank Agent (CR)</td>
                                                        <td width="33.33%" align="center" style="padding:0 0 0px">{{$detl['ttlAgentCrtCnt']+$detl['ttlCashDepositCount_1_fund_transfer_count_agent']}}</td>
                                                        <td width="33.33%" align="right" style="padding:0 0 0px">{{number_format($detl['ttlAgentCrt']+$detl['ttlCashDeposit_1_fund_transfer_agent'], 2, '.', ',')}}</td>
                                                    </tr>
                                                    <tr style="margin-bottom: 10px;">
                                                        <td width="33.33%" style="padding:0 0 0px">Card Deposit</td>
                                                        <td width="33.33%" align="center" style="padding:0 0 0px">0</td>
                                                        <td width="33.33%" align="right" style="padding:0 0 0px">0.00</td>
                                                    </tr>
                                                    <tr style="margin-bottom: 10px;">
                                                        <td width="33.33%" style="padding:0 0 0px">Crypto Deposit </td>
                                                        <td width="33.33%" align="center" style="padding:0 0 0px">{{$detl['ttlCrdtCptCnt']}}</td>
                                                        <td width="33.33%" align="right" style="padding:0 0 0px">{{number_format($detl['ttlCrdtCpt'], 2, '.', ',')}}</td>
                                                    </tr>

                                                    <tr style="margin-bottom: 10px;">
                                                        <td width="33.33%" style="padding:0 0 0px">Merchant Deposit </td>
                                                        <td width="33.33%" align="center" style="padding:0 0 0px">{{$detl['merchant_widraw_total_count']+$detl['ttlAccCredit_Online_payment_count']}}</td>
                                                        <td width="33.33%" align="right" style="padding:0 0 0px">{{number_format($detl['merchant_widraw_total']+$detl['ttlAccCredit_Online_payment'], 2, '.', ',')}}</td>
                                                    </tr>

                                                    <tr style="margin-bottom: 10px;">
                                                        <td width="33.33%" style="padding:0 0 0px">OZOW Deposit </td>
                                                        <td width="33.33%" align="center" style="padding:0 0 0px">{{$detl['ttlCrdtOzowCnt']}}</td>
                                                        <td width="33.33%" align="right" style="padding:0 0 0px">{{number_format($detl['ttlCrdtOzow'], 2, '.', ',')}}</td>
                                                    </tr>

                                                    <tr style="margin-bottom: 10px;">
                                                        <td width="33.33%" style="padding:0 0 0px">DBA Cash </td>
                                                        <td width="33.33%" align="center" style="padding:0 0 0px">{{$detl['total_dba_cash_received_count']}}</td>
                                                        <td width="33.33%" align="right" style="padding:0 0 0px">{{number_format($detl["total_dba_cash_received"],2, '.', ',') }}</td>
                                                    </tr>


                                                    <tr style="margin-bottom: 10px;">
                                                        <td width="33.33%" style="padding:0 0 0px">ePay Me</td>
                                                        <td width="33.33%" align="center" style="padding:0 0 0px">{{$detl['total_epay_me_received_count']}}</td>
                                                        <td width="33.33%" align="right" style="padding:0 0 0px">{{number_format($detl["total_epay_me_received"],2, '.', ',') }}</td>
                                                    </tr>


                                                    <tr style="margin-bottom: 10px;">
                                                        <td width="33.33%" style="padding:0 0 0px">Reverse </td>
                                                        <td width="33.33%" align="center" style="padding:0 0 0px">{{$detl['total_refund_credit_count']}}</td>
                                                        <td width="33.33%" align="right" style="padding:0 0 0px">{{number_format($detl['total_refund_credit'], 2, '.', ',')}}</td>
                                                    </tr>

                                                    <tr style="margin-bottom: 10px;">
                                                        <td width="33.33%" style="padding:0 0 0px">Others </td>
                                                        <td width="33.33%" align="center" style="padding:0 0 0px">{{$detl['ttlCashDepositCount']+$detl['currency_conversion_count']}}</td>
                                                        <td width="33.33%" align="right" style="padding:0 0 0px">{{number_format($detl['ttlCashDeposit']+$detl['currency_conversion_amount'], 2, '.', ',')}}</td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="33.33%" style="padding-top:20px; font-weight:600">Funds Used (Debit )</td>
                                            <td width="33.33%" style="padding-top:20px;  font-weight:600" align="center">{{$detl['ttlAccDebitCount']+$detl['fund_transfer_debit_count']+$detl["ttlCashDebitCount"]+$detl["ttlOtherDebitCount"]+$detl['W2W_admin_debit_count']+$detl["ttlAccDebit_Online_payment_count"]+$detl["sender_fund_transfer_to_new_user_count"]+$detl["merchant_widraw_api_total_sender_count"]+$detl["exchange_charge_count"]+$detl["mobile_topup_total_count"]+$detl["total_dba_swap_count"]+$detl["total_epay_me_sender_count"]+$detl['total_gift_card_sender_count']}}</td>
                                            <td width="33.33%" style="padding-top:20px;  font-weight:600" align="right"> {{number_format($detl['ttlAccDebit']+$detl['fund_transfer_debit']+$detl["ttlCashDebit"]+$detl["ttlOtherDebit"]+$detl['W2W_admin_debit_amount']+$detl["ttlAccDebit_Online_payment"]+$detl["sender_fund_transfer_to_new_user"]+$detl["merchant_widraw_api_total_sender"]+$detl["exchange_charge"]+$detl["mobile_topup_total"]+$detl["total_dba_swap"]+$detl["total_epay_me_sender"]+$detl["total_gift_card_sender"],2, '.', ',')}} Dr</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">
                                                <table width="100%" style="border:1px solid #000; padding:10px; ">
                                                    <tr style="margin-bottom: 10px;">
                                                        <td width="33.33%" style="padding:0 0 0px">Bank Transfer </td>
                                                        <td width="33.33%" style="padding:0 0 0px" align="center">{{$detl['ttlAccDebitCount']}}</td>
                                                        <td width="33.33%" style="padding:0 0 0px" align="right">{{number_format($detl['ttlAccDebit'], 2, '.', ',')}}</td>
                                                    </tr>

                                                    <tr style="margin-bottom: 10px;">
                                                        <td width="33.33%" style="padding:0 0 0">W2W Payment </td>
                                                        <td width="33.33%" style="padding:0 0 0px" align="center">{{$detl['fund_transfer_debit_count']+$detl["sender_fund_transfer_to_new_user_count"]}}</td>
                                                        <td width="33.33%" style="padding:0 0 0px" align="right">{{number_format($detl['fund_transfer_debit']+$detl["sender_fund_transfer_to_new_user"], 2, '.', ',')}}</td>
                                                    </tr>

                                                    <tr style="margin-bottom: 10px;">
                                                        <td width="33.33%" style="padding:0 0 0px">Bank Agent (DR)</td>
                                                        <td width="33.33%" align="center" style="padding:0 0 0px">{{$detl["ttlCashDebitCount"]}}</td>
                                                        <td width="33.33%" align="right" style="padding:0 0 0px">{{number_format($detl["ttlCashDebit"], 2, '.', ',')}}</td>
                                                    </tr>

                                                    <tr style="margin-bottom: 10px;">
                                                        <td width="33.33%" style="padding:0 0 0">Card Withdrawals </td>
                                                        <td width="33.33%" align="center" style="padding:0 0 0px">0</td>
                                                        <td width="33.33%" align="right" style="padding:0 0 0px">0.00</td>
                                                    </tr>

                                                    <tr style="margin-bottom: 10px;">
                                                        <td width="33.33%" style="padding:0 0 0">Crypto Withdrawal </td>
                                                        <td width="33.33%" align="center" style="padding:0 0 0px">{{$detl["ttlOtherDebitCount"]}}</td>
                                                        <td width="33.33%" align="right" style="padding:0 0 0px">{{number_format($detl["ttlOtherDebit"], 2, '.', ',')}}</td>
                                                    </tr>

                                                    <tr style="margin-bottom: 10px;">
                                                        <td width="33.33%" style="padding:0 0 0px">Merchant Payment </td>
                                                        <td width="33.33%" align="center" style="padding:0 0 0px">{{$detl["ttlAccDebit_Online_payment_count"]+$detl["merchant_widraw_api_total_sender_count"]}}</td>
                                                        <td width="33.33%" align="right" style="padding:0 0 0px">{{number_format($detl["ttlAccDebit_Online_payment"]+$detl["merchant_widraw_api_total_sender"],2, '.', ',') }}</td>
                                                    </tr>

                                                    <tr style="margin-bottom: 10px;">
                                                        <td width="33.33%" style="padding:0 0 0px">Airtime TopUp </td>
                                                        <td width="33.33%" align="center" style="padding:0 0 0px">{{$detl["mobile_topup_total_count"]}}</td>
                                                        <td width="33.33%" align="right" style="padding:0 0 0px">{{number_format($detl["mobile_topup_total"],2, '.', ',') }}</td>
                                                    </tr>

                                                    <tr style="margin-bottom: 10px;">
                                                        <td width="33.33%" style="padding:0 0 0px">DBA Swap </td>
                                                        <td width="33.33%" align="center" style="padding:0 0 0px">{{$detl["total_dba_swap_count"]}}</td>
                                                        <td width="33.33%" align="right" style="padding:0 0 0px">{{number_format($detl["total_dba_swap"],2, '.', ',') }}</td>
                                                    </tr>

                                                    <tr style="margin-bottom: 10px;">
                                                        <td width="33.33%" style="padding:0 0 0px">ePay Me</td>
                                                        <td width="33.33%" align="center" style="padding:0 0 0px">{{$detl['total_epay_me_sender_count']}}</td>
                                                        <td width="33.33%" align="right" style="padding:0 0 0px">{{number_format($detl["total_epay_me_sender"],2, '.', ',') }}</td>
                                                    </tr>

                                                    <tr style="margin-bottom: 10px;">
                                                        <td width="33.33%" style="padding:0 0 0px">Gift Card</td>
                                                        <td width="33.33%" align="center" style="padding:0 0 0px">{{$detl['total_gift_card_sender_count']}}</td>
                                                        <td width="33.33%" align="right" style="padding:0 0 0px">{{number_format($detl["total_gift_card_sender"],2, '.', ',') }}</td>
                                                    </tr>

                                                    <tr style="margin-bottom: 10px;">
                                                        <td width="33.33%" style="padding:0 0 0px">Others </td>
                                                        <td width="33.33%" align="center" style="padding:0 0 0px">{{$detl['W2W_admin_debit_count']+$detl["exchange_charge_count"]}}</td>
                                                        <td width="33.33%" align="right" style="padding:0 0 0px">{{number_format($detl['W2W_admin_debit_amount']+$detl["exchange_charge"], 2, '.', ',')}}</td>
                                                    </tr>

                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="33.33%" style="padding-top:20px;  font-weight:600">Bank Charges </td>
<!--                                            <td width="33.33%" style="padding-top:20px;  font-weight:600" align="center">{{$detl['ttlFeeCount']}}</td>
                                            <td width="33.33%" style="padding-top:20px;  font-weight:600" align="right"> {{$detl['ttlFee']}} Dr</td>-->
                                            <td width="33.33%" style="padding-top:20px;  font-weight:600" align="center">{{  $detl["Ozow_fees_count"]+$detl["W2W_admin_credit_fees_count"] + $detl["Manual_deposit_fee_count"] + $detl["crypto_deposit_fee_count"]+$detl['ttlCashDepositCount_1_fund_transfer_count']+$detl['merchant_widraw_total_count'] + $detl['ttlAgentCrtCnt']+ $detl['W2W_admin_debit_fees_count']+$detl["ttlAccDebitCount"]+$detl["crypto_withdraw_fee_count"]+$detl["fund_transfer_debit_count"]+$detl["ttlCashDebitCount"]+$detl['fund_transfer_to_new_user_count']+$detl["sender_fund_transfer_to_new_user_count"]+$detl["merchant_widraw_api_total_sender_count"]+$detl['ttlAccCredit_Online_payment_count']+$detl["ttlAccDebit_Online_payment_count"]+$detl["W2W_admin_credit_fees_count_fund_transfer_agent"]+$detl["mobile_topup_total_count"]+$detl['ttlCashDeposit_1_fund_receiver_count']+$detl['total_dba_cash_received_count']+$detl["total_dba_swap_count"]+$detl['total_epay_me_received_count']+$detl['total_epay_me_sender_count']+$detl['total_gift_card_sender_count'] }}</td>
                                            <td width="33.33%" style="padding-top:20px;  font-weight:600" align="right"> {{  number_format($detl["Ozow_fees"]+$detl["W2W_admin_credit_fees"] + $detl["Manual_deposit_fee"] + $detl["crypto_deposit_fee"]+$detl["W2W_admin_credit_fees_fund_transfer"]+$detl['merchant_widraw_total_fees']+$detl["W2W_admin_debit_fees"]+$detl["manual_withdraw_fees"]+$detl["fund_transfer_debit_fees"]+$detl['Bank_Agent_dr_fees']+$detl['fund_transfer_to_new_user_fees']+$detl["sender_fund_transfer_to_new_user_fees"]+$detl["merchant_widraw_api_total_sender_fees"]+$detl['ttlAccCredit_Online_payment_fees']+$detl["ttlAccDebit_Online_payment_fees"]+$detl["W2W_admin_credit_fees_fund_transfer_agent"]+$detl["mobile_topup_total_fees"]+$detl['ttlCashDeposit_1_fund_receiver_fees']+$detl["total_dba_cash_received_fees"]+$detl["total_dba_swap_fees"]+$detl["total_epay_me_received_fees"]+$detl["total_epay_me_sender_fees"]+$detl['total_gift_card_sender_fees'],2, '.', ',') }} Dr</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">
                                                <table width="100%" style="border:1px solid #000; padding:10px; ">
                                                    <tr style="margin-bottom: 10px;">
                                                        <td width="33.33%" style="padding:0 0 0px">Bank Transfer </td>
                                                        <td width="33.33%" style="padding:0 0 0px" align="center">{{$detl["Manual_deposit_fee_count"]+$detl["ttlAccDebitCount"]}}</td>
                                                        <td width="33.33%" style="padding:0 0 0px" align="right">{{number_format($detl["Manual_deposit_fee"]+$detl["manual_withdraw_fees"], 2, '.', ',')}}</td>
                                                    </tr>  
                                                    <tr style="margin-bottom: 10px;">
                                                        <td width="33.33%" style="padding:0 0 0px">W2W Transfer </td>
                                                        <td width="33.33%" align="center" style="padding:0 0 0px">{{$detl["W2W_admin_credit_fees_count_fund_transfer"]+$detl["fund_transfer_debit_count"]+$detl['fund_transfer_to_new_user_count']+$detl['sender_fund_transfer_to_new_user_count']+$detl['ttlCashDeposit_1_fund_receiver_count']}}</td>
                                                        <td width="33.33%" align="right" style="padding:0 0 0px">{{number_format($detl["W2W_admin_credit_fees_fund_transfer"]+$detl["fund_transfer_debit_fees"]+$detl['fund_transfer_to_new_user_fees']+$detl['sender_fund_transfer_to_new_user_fees']+$detl['ttlCashDeposit_1_fund_receiver_fees'], 2, '.', ',')}}</td>
                                                    </tr>
                                                    <tr style="margin-bottom: 10px;">
                                                        <td width="33.33%" style="padding:0 0 0px">Agent Transaction</td>
                                                        <td width="33.33%" align="center" style="padding:0 0 0px">{{$detl['ttlAgentCrtCnt']+$detl["ttlCashDebitCount"]+$detl["W2W_admin_credit_fees_count_fund_transfer_agent"]}} </td>   
                                                        <td width="33.33%" align="right" style="padding:0 0 0px">{{number_format($detl['Bank_Agent_dr_fees']+$detl["W2W_admin_credit_fees_fund_transfer_agent"], 2, '.', ',')}} </td>
                                                    </tr>
                                                    <tr style="margin-bottom: 10px;">
                                                        <td width="33.33%" style="padding:0 0 0">Card Transaction </td>
                                                        <td width="33.33%" align="center" style="padding:0 0 0px"></td>
                                                        <td width="33.33%" align="right" style="padding:0 0 0px">0.00</td>
                                                    </tr>
                                                    <tr style="margin-bottom: 10px;">
                                                        <td width="33.33%" style="padding:0 0 0">Crypto Transaction </td>
                                                        <td width="33.33%" align="center" style="padding:0 0 0px">{{$detl["crypto_deposit_fee_count"]+$detl["crypto_withdraw_fee_count"]}}
                                                        </td>
                                                        <td width="33.33%" align="right" style="padding:0 0 0px">{{ number_format($detl["crypto_deposit_fee"], 2, '.', ',')}}</td>
                                                    </tr>

                                                    <tr style="margin-bottom: 10px;">
                                                        <td width="33.33%" style="padding:0 0 0px">Merchant Transaction</td>
                                                        <td width="33.33%" align="center" style="padding:0 0 0px">{{
                                                            $detl['merchant_widraw_total_count']+$detl["ttlAccDebit_Online_payment_count"]+$detl["merchant_widraw_api_total_sender_count"]+$detl['ttlAccCredit_Online_payment_count']}}</td>
                                                        <td width="33.33%" align="right" style="padding:0 0 0px">{{ number_format($detl['merchant_widraw_total_fees']+$detl["ttlAccDebit_Online_payment_fees"]+$detl["merchant_widraw_api_total_sender_fees"]+$detl['ttlAccCredit_Online_payment_fees'], 2, '.', ',') }}</td>
                                                    </tr>


                                                    <tr style="margin-bottom: 10px;">
                                                        <td width="33.33%" style="padding:0 0 0">OZOW Deposit </td>
                                                        <td width="33.33%" align="center" style="padding:0 0 0px">{{$detl["Ozow_fees_count"]}}</td>
                                                        <td width="33.33%" align="right" style="padding:0 0 0px">{{number_format($detl["Ozow_fees"], 2, '.', ',')}}</td>
                                                    </tr>

                                                    <tr style="margin-bottom: 10px;">
                                                        <td width="33.33%" style="padding:0 0 0px">Airtime TopUp </td>
                                                        <td width="33.33%" align="center" style="padding:0 0 0px">{{$detl["mobile_topup_total_count"]}}</td>
                                                        <td width="33.33%" align="right" style="padding:0 0 0px">{{number_format($detl["mobile_topup_total_fees"],2, '.', ',') }}</td>
                                                    </tr>

                                                    <tr style="margin-bottom: 10px;">
                                                        <td width="33.33%" style="padding:0 0 0px">DBA Cash </td>
                                                        <td width="33.33%" align="center" style="padding:0 0 0px">{{$detl['total_dba_cash_received_count']}}</td>
                                                        <td width="33.33%" align="right" style="padding:0 0 0px">{{number_format($detl["total_dba_cash_received_fees"],2, '.', ',') }}</td>
                                                    </tr>

                                                    <tr style="margin-bottom: 10px;">
                                                        <td width="33.33%" style="padding:0 0 0px">DBA Swap </td>
                                                        <td width="33.33%" align="center" style="padding:0 0 0px">{{$detl["total_dba_swap_count"]}}</td>
                                                        <td width="33.33%" align="right" style="padding:0 0 0px">{{number_format($detl["total_dba_swap_fees"],2, '.', ',') }}</td>
                                                    </tr>

                                                    <tr style="margin-bottom: 10px;">
                                                        <td width="33.33%" style="padding:0 0 0px">ePay Me</td>
                                                        <td width="33.33%" align="center" style="padding:0 0 0px">{{$detl['total_epay_me_received_count']+$detl['total_epay_me_sender_count']}}</td>
                                                        <td width="33.33%" align="right" style="padding:0 0 0px">{{number_format($detl["total_epay_me_received_fees"]+$detl["total_epay_me_sender_fees"],2, '.', ',') }}</td>
                                                    </tr>

                                                    <tr style="margin-bottom: 10px;">
                                                        <td width="33.33%" style="padding:0 0 0px">Gift Card</td>
                                                        <td width="33.33%" align="center" style="padding:0 0 0px">{{$detl['total_gift_card_sender_count']}}</td>
                                                        <td width="33.33%" align="right" style="padding:0 0 0px">{{number_format($detl["total_gift_card_sender_fees"],2, '.', ',') }}</td>
                                                    </tr>


                                                    <tr style="margin-bottom: 10px;">
                                                        <td width="33.33%" style="padding:0 0 10px">Others </td>
                                                        <td width="33.33%" align="center" style="padding:0 0 0px">{{$detl["W2W_admin_credit_fees_count"]+$detl['W2W_admin_debit_fees_count']}}</td>
                                                        <td width="33.33%" align="right" style="padding:0 0 0px">{{number_format($detl["W2W_admin_credit_fees"]+$detl["W2W_admin_debit_fees"], 2, '.', ',')}}</td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="left" colspan="2" style="padding:10px 0 0px; font-weight:600">Closing Balance</td>
                                            <td align="right" style="  padding:10px 0 0px;  font-weight:600">{{number_format(floor($closeBal*100)/100,2,'.',',')}} Cr</td>
                                        </tr>

                                    </table>
                                </td>

                                <td colspan="1" align="right" valign="top">
                                    <table width="100%" style="padding-left: 50px;font-family: 'GothamMediumRegular';">
                                        <tr style="width: 100%;">
                                            <td colspan="2">
                                                <h4 style="text-align: left; margin-top:150px">Contact us</h4>
                                            </td>
                                        </tr>
                                        <tr style="width: 100%;">                                            
                                            <td width="50%">
                                                <table width="100%">
                                                    <tr>
                                                        <td style="padding: 5px 3px 0px 3px;background: #000; float: left;">
                                                            <img style="margin-bottom: 5px;" src="<?php echo HTTP_PATH; ?>/public/img/website.png">
                                                        </td>
                                                        <td style="backgroud: #f3f4f4;padding-left: 10px;">
                                                            <span style="padding: 0 0 15px;
                                                      position: relative;
                                                      z-index: 99;
                                                      display: block;">www.dafribank.com</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding: 2px 3px 0px 3px;background: #000; float: left;">
                                                            <img style="margin-bottom: 5px;"  src="<?php echo HTTP_PATH; ?>/public/img/phone.png" >
                                                        </td>
                                                        <td style="backgroud: #f3f4f4;padding-left: 10px;">
                                                            <span style="padding: 0 0 15px;
                                                      position: relative;
                                                      z-index: 99;
                                                      display: block;">+27 11 568 5053</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding: 2px 3px 5px 3px;background: #000; float: left;">
                                                            <img  src="<?php echo HTTP_PATH; ?>/public/img/employe.png">
                                                        </td>
                                                        <td style="backgroud: #f3f4f4;padding-left: 10px;">
                                                            <span style="position: relative; z-index: 99;">hello@dafribank.com</span>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                            
<!--                                            <td  style="padding: 10px 10px;background: #000; float: left; position: relative; text-align:center;" width="20%">
                                                <img style="margin-bottom: 5px;" src="<?php echo HTTP_PATH; ?>/public/img/website.png">
                                                <br> <br> 
                                                <img style="margin-bottom: 5px;"  src="<?php echo HTTP_PATH; ?>/public/img/phone.png" >
                                                <br> <br> 
                                                <img  src="<?php echo HTTP_PATH; ?>/public/img/employe.png">

                                            </td>

                                            <td style=" float: left; padding: 0 6px;" width="300px">
                                                <span style="padding: 0 0 18px;
                                                      position: relative;
                                                      z-index: 99;
                                                      display: block;">www.dafribank.com</span>
                                                <span style="padding: 0 0 18px;
                                                      position: relative;
                                                      z-index: 99;
                                                      display: block;">+27 11 568 5053</span>
                                                <span style="position: relative; z-index: 99;">hello@dafribank.com</span>
                                            </td>-->
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>


        <footer class="footer">
            <table style="padding: 10px 20px; font-family: Sora, sans-serif !important;">
                <tr>
                    <td> <img src="<?php echo HTTP_PATH; ?>/public/img/pdf/dafri-short-logo.png" style="width:30px;"> <br></td>
                </tr>
            </table>
        </footer>

        <div class="page_break"></div>
        <br>
        <table class="strip" width="100%" border="0" cellpadding="0" cellspacing="0" style="margin: 0 auto;background: #fff; box-shadow:0 0 10px rgba(0,0,0,0.1); margin-top: 30px; padding:0 0 0;" page-break-inside: auto;>
            <tr style="background:none; padding-bottom: 10px;">
                <td align="left" colspan="5" style="font-size:24px; padding-bottom: 10px; font-weight:600"> DafriBank Digital {{$detl["acc_type"]}}: {{$detl["acc_number"]}}</td>
            </tr>
            <thead>
                <tr style="background:none">
                    <td width="17%" style="padding: 10px; border: 1px solid #000;"><strong>Date</strong></td>
                    <td width="25%" style=" padding: 10px; border: 1px solid #000;"><strong>Description</strong></td>
                    <td width="20%" style=" padding: 10px; border: 1px solid #000;"><strong>Amount</strong></td>
                    <td width="20%" style=" padding: 10px; border: 1px solid #000;"><strong>Balance</strong></td>
                    <td width="15%" style="padding: 10px; border: 1px solid #000;"><strong>Accrued Bank Charges</strong></td>
                </tr>
            </thead>
            <tr style="background:none">
                <td width="17%" style="padding:8px 5px; font-size: 16px;"><strong></strong></td>
                <td width="25%" style=" padding:8px 5px;font-size: 16px;"><strong>Opening Balance</strong></td>
                <td width="20%" style=" padding:8px 5px;font-size: 16px;"><strong>{{number_format(floor($openingBal*100)/100,2,'.',',')}} Cr</strong></td>
                <td width="20%" style=" padding:8px 5px;font-size: 16px;"><strong></strong></td>
                <td width="15%" style=" padding:8px 5px;font-size: 16px;"><strong></strong></td>
            </tr>
        </table>

        <table style="padding:0 0; font-family: Sora, sans-serif !important; border: 2px solid #000;" width="100%" class="strip">
            @php $i = 0; @endphp
            @foreach ($transPDF as $tran)

            @php
            $date = date_create($tran->updated_at);
            $transDate = date_format($date,'d F Y');
            $trans=$tran;
            $recordInfo = \App\User::where('id', $detl['user_id'])->first();
            if ($recordInfo->user_type == 'Agent') {
            $agent = \App\Agent::where('user_id', $detl['user_id'])->first();
            $agent_id = $agent->id;
            } else {
            $agent = false;
            $agent_id = 0;
            }
            @endphp
            
            <?php if(($i % 2) == 0) {
               $vall = "style='background:#E7E9EA'";
            } else {
                $vall = "style='background:#fff'";
            } ?>

            <tr <?php echo $vall;?>>
                <td width="17%" style="padding: 8px;border-right: 1px solid #000;">{{$transDate}}</td>
                <td width="25%" style=" padding: 8px;border-right: 1px solid #000;">
                    <div class="trans-card">
                        <div>
                            <?php
                            $trans->trans_for = str_replace("Refund", "Reverse",$trans->trans_for); ?>
                            @if($trans->trans_for == 'Mobile Top-up')
                            Mobile Top-up
                            @elseif($trans->trans_for == 'Exchange Charge')
                            Exchange Charge
                            @elseif($trans->trans_for == 'GIFT CARD')
                            GIFT CARD
                            @elseif($trans->trans_for == 'EPAY ME' || $trans->trans_for == 'EPAY MERCHANT')
                            ePay Me
                            @elseif($trans->trans_for == 'DBA eCash')
                            DBA eCash
                            @elseif($trans->trans_for == 'SWAP')
                            DBA Swap
                            @elseif($trans->trans_for == 'Merchant_Withdraw')
                            Merchant Withdraw
                            @endif

                            @if($trans->trans_for == 'ONLINE_PAYMENT')
                            @if($trans->user_id != $detl['user_id'])
                            Online Purchase
                            @else
                            Online Purchase
                            @endif
                            @else
                            @if($trans->user_id != $detl['user_id'] && $trans->receiver_id == $detl['user_id'] && $trans->trans_type == 1 && $trans->trans_for == 'W2W')
                            @php
                            $sender = getUserByUserId($trans->user_id);
                            @endphp
                            @if ($sender->user_type == 'Agent')
                            Agent Withdraw
                            @endif
                            @elseif($trans->user_id != $detl['user_id'] && $trans->receiver_id == $detl['user_id'] && $trans->trans_type == 2 && $trans->trans_for == 'W2W')
                            @php
                            $sender = getUserByUserId($trans->user_id);
                            @endphp
                            @if ($sender->user_type == 'Agent')
                            Agent Topup
                            @elseif($sender->user_type == 'Personal')
                            @if($trans->user_id==1)
                            Admin Wallet Adjust
                            @else
                            Wallet2Wallet
                            @endif
                            @elseif($sender->user_type == 'Business')
                            @if($trans->user_id==1)
                            Admin Wallet Adjust
                            @else
                            Wallet2Wallet
                            @endif
                            @endif
                            @elseif($trans->user_id == $detl['user_id'] && $trans->receiver_id != $detl['user_id'] && $trans->trans_type == 2 && $trans->trans_for == 'W2W')
                            @php
                            $receiver = getUserByUserId($trans->receiver_id);
                            @endphp
                            @if ($receiver->user_type == 'Agent')
                            Transfer
                            @elseif($receiver->user_type == 'Personal')
                            Wallet2Wallet
                            @elseif($receiver->user_type == 'Business')
                            Transfer
                            @endif
                            @elseif($trans->user_id != $detl['user_id'] && $trans->receiver_id == $detl['user_id'] && $trans->trans_type == 2 && $trans->trans_for == 'Withdraw##Invite_New_User')
                            Invite Pay
                            @elseif($trans->user_id == $detl['user_id'] && $trans->receiver_id != $detl['user_id'] && $trans->trans_type == 2 && $trans->trans_for == 'Withdraw##Invite_New_User')
                            Invite Pay
                            @elseif($trans->user_id == $detl['user_id'] && $trans->receiver_id != $detl['user_id'] && $trans->trans_type == 1 && $trans->trans_for == 'W2W')
                            @php
                            $receiver = getUserByUserId($trans->receiver_id);
                            @endphp
                            @if ($receiver->user_type == 'Agent')
                            Agent Topup
                            @elseif($receiver->user_type == 'Personal')
                            Wallet Topup
                            @elseif($receiver->user_type == 'Business')
                            Wallet Topup
                            @endif
                            @elseif($trans->user_id == $detl['user_id'] && $trans->receiver_id == 0 && $trans->trans_type == 2)
                            @php
                            $transForArr = explode("##",$trans->trans_for);
                            if (Count($transForArr) >= 2) {
                            $paymentType = $transForArr[0];
                            }
                            else {
                            $paymentType = $transForArr[0];
                            }
                            if ($paymentType == 'CryptoWithdraw') {
                            $paymentType = 'Crypto Withdraw';	
                            }
                            @endphp
                            Wallet Withdraw ({{str_replace("_"," ",$paymentType)}})
                            @elseif($trans->user_id == $detl['user_id'] && $trans->receiver_id == 0 && $trans->trans_type == 1)
                            @if($trans->trans_for == 'ManualDeposit')
                            Wallet Topup ({{ 'Manual Deposit' }})
                            @elseif($trans->trans_for == 'Converted Amount')
                            Amount (After Currency Change)
                            @elseif($trans->trans_for == 'CryptoDeposit')
                            Wallet Topup ({{ 'Crypto Deposit' }})
                            @else
                            Wallet Topup ({{ $trans->trans_for }})
                            @endif
                            @elseif($trans->user_id == $detl['user_id'] && $trans->trans_type == 2 && $trans->trans_for == "Withdraw##Agent")
                            Agent Withdraw
                            @elseif($trans->receiver_id == $agent_id && $trans->trans_type == 2 && $trans->trans_for == "Withdraw##Agent")
                            Agent Withdraw
                            @elseif($trans->user_id == $detl['user_id'] && $trans->receiver_id > 0 && $trans->trans_type == 2 && $trans->trans_for == 'ONLINE_PAYMENT')
                            Online Purchase
                            @endif
                            @endif
                        </div>
                    </div>

                </td>
                <td width="20%" style=" padding: 8px;">
                    @if ($tran->user_id == $detl['user_id'] && $tran->receiver_id == 0 && $tran->trans_type == 1)
                    {{number_format(floor($tran->real_value*100)/100,2,'.',',')}} Cr
                    @elseif ($tran->user_id != $detl['user_id'] && $tran->receiver_id == $detl['user_id'] && $tran->trans_type == 2)
                    @if($tran->trans_for=='Withdraw##Agent')
                    {{number_format(floor($tran->sender_real_value*100)/100,2,'.',',')}} Cr
                    @else
                    {{number_format(floor($tran->real_value*100)/100,2,'.',',')}} Cr
                    @endif
                    @elseif ($tran->user_id != $detl['user_id'] && $tran->receiver_id == $detl['user_id'] && $tran->trans_type == 1)
                    {{number_format(floor($tran->real_value*100)/100,2,'.',',')}} Cr
                    @endif

                    @if ($tran->user_id == $detl['user_id'] && $tran->receiver_id != 0 && $tran->trans_type == 2)
                    @if($tran->trans_for=='ONLINE_PAYMENT' || $tran->trans_for=='Withdraw##Invite_New_User' || $tran->trans_for=='Merchant_Withdraw' || $tran->trans_for=='EPAY ME' || $tran->trans_for=='EPAY MERCHANT' || $tran->trans_for=='GIFT CARD')
                   {{number_format(floor($tran->sender_real_value*100)/100,2,'.',',')}} Dr
                    @elseif(strpos($tran->billing_description,'Wallet2Wallet') !== false && $tran->trans_for=='W2W')
                   {{number_format(floor($tran->sender_real_value*100)/100,2,'.',',')}} Dr
                    @else
                    {{number_format(floor($tran->real_value*100)/100,2,'.',',')}} Dr
                    @endif
                    @elseif ($tran->user_id == $detl['user_id'] && $tran->receiver_id == 0 && $tran->trans_type == 2)
                    @if($tran->trans_for=='Withdraw##Invite_New_User' && $tran->receiver_id == 0)
                   {{number_format(floor($tran->sender_real_value*100)/100,2,'.',',')}} Dr
                    @else
                    {{number_format(floor($tran->real_value*100)/100,2,'.',',')}} Dr
                    @endif
                    @endif
                </td>
                <td width="20%" style=" padding: 8px;border-left: 1px solid #000;">
                    @if ($tran->user_id == $detl['user_id'])
                    {{number_format(floor($tran->user_close_bal*100)/100,2,'.',',')}} Cr
                    @else
                    {{number_format(floor($tran->receiver_close_bal*100)/100,2,'.',',')}} Cr
                    @endif
                </td>
                <td width="15%" style=" padding: 8px;border-left: 1px solid #000;">
                    @if($tran->receiver_id == $detl['user_id'] && $tran->receiver_id==0)
                   {{number_format(floor($tran->receiver_fees*100)/100,2,'.',',')}}
                    @elseif($tran->user_id == $detl['user_id'] && $tran->receiver_id==0 )
                    @if($tran->trans_for=='CryptoWithdraw' || $tran->trans_for=='Manual Withdraw' || $tran->trans_for=='Withdraw##Agent' ||  $tran->trans_for=='Withdraw##Invite_New_User' ||  $tran->trans_for=='Global Pay' ||  $tran->trans_for=='3rd Party Pay')
                   {{number_format(floor($tran->sender_fees*100)/100,2,'.',',')}}
                    @else  
                    @if($tran->trans_for=='Withdraw##Invite_New_User' && $tran->receiver_id != 0)
                   {{number_format(floor($tran->sender_fees*100)/100,2,'.',',')}}
                    @else
                   {{number_format(floor($tran->receiver_fees*100)/100,2,'.',',')}}
                    @endif
                    @endif
                    @elseif($tran->user_id == $detl['user_id'] && $tran->receiver_id!=0)
                   {{number_format(floor($tran->sender_fees*100)/100,2,'.',',')}}
                    @else
                    {{  number_format($tran->fees,2)}}
                    @endif

                </td>

            </tr>
            @php $i++; @endphp
            @endforeach
        </table>
        
        <table>
            <tr style="background:none">
                <td width="17%" style="padding:8px 5px; font-size: 16px; padding-bottom: 100px;"><strong></strong></td>
                <td width="25%" style=" padding:8px 5px;font-size: 16px;padding-bottom: 100px;"><strong>Closing Balance</strong></td>
                <td width="20%" style=" padding:8px 5px;font-size: 16px;padding-bottom: 100px;"><strong>{{number_format(floor($closeBal*100)/100,2,'.',',')}} Cr</strong></td>
                <td width="20%" style=" padding:8px 5px;font-size: 16px;padding-bottom: 100px;"><strong></strong></td>
                <td width="15%" style=" padding:8px 5px;font-size: 16px;padding-bottom: 100px;"><strong></strong></td>
            </tr>
            <tr>
                <td><br><br><br></td>
            </tr>
            <tr style="background:none;">
                <td style="border-radius: 15px;
                    padding: 8px;
                    line-height: 18px;
                    font-size: 13px;
                    color: #9c9c9c; text-align: justify;" colspan="5">
                    <img src="<?php echo HTTP_PATH; ?>/public/img/pdf/dafri-short-logo.png" style="width:30px;"> <br><br>
                    DafriBank Digital LTD is a bank duly licensed by the Central Bank of Comoros with banking License B2019005. DafriBank is a division of DafriGroup PLC, a public company incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Digital asset markets and exchanges are not regulated with the same controls or customer protections available with other forms of financial products and are subject to an evolving regulatory environment. Digital assets do not typically have legal tender status and are not covered by deposit protection insurance. The past performance of a digital asset is not a guide to future performance, nor is it a reliable indicator of future results or performance. Additional disclosures can be found on the Legal and Privacy page


                    <br><br>
                    &copy;{{date('Y')}} DafriBank Digital LTD. All Rights Reserved. A DafriGroup PLC Company
                </td>
            </tr>
        </table>
    </body>
    
</html>

<?php
//print_r($detl); die;
?>