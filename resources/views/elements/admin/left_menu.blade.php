<aside class="main-sidebar"> 
    <section class="sidebar">
        <ul class="sidebar-menu">
            <li class="treeview @if(isset($actdashboard)){{'active'}}@endif">
                <a href="{{URL::to('admin/admins/dashboard')}}">
                    <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                </a>
            </li>
            <li class="treeview @if(isset($actchangeusername) || isset($actchangepassword) || isset($actconfigurefees) || isset($actconfigrole) || isset($actsubadmin) || isset($actconfigtranslimit) || isset($actconfigagentlimit) || isset($actwallet) || isset($actconversion) || isset($actconfiguremerchantfees) || isset($actcryptowithdraw)  || isset($actcryptodeposit) || isset($giftdbasetting) || isset($actdbasetting) || isset($acttotalfee) ){{'active'}}@endif">
                <a href="javascript:void(0)">
                    <i class="fa fa-gears"></i> <span>Configuration</span> <i class="fa fa-angle-right pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li class="@if(isset($actsubadmin)){{'active'}}@endif"><a href="{{URL::to('admin/admins/list-subadmin')}}"><i class="fa fa-circle-o"></i> Configure Subadmin</a></li>
                    <li class="@if(isset($actchangeusername)){{'active'}}@endif"><a href="{{URL::to('admin/admins/change-username')}}"><i class="fa fa-circle-o"></i> Change Username</a></li>
                    <li class="@if(isset($actchangepassword)){{'active'}}@endif"><a href="{{URL::to('admin/admins/change-password')}}"><i class="fa fa-circle-o"></i> Change Password</a></li>
                    <li class="@if(isset($actwallet)){{'active'}}@endif"><a href="{{URL::to('admin/admins/wallet-balance')}}"><i class="fa fa-circle-o"></i> Wallet Balance</a></li>
                    <li class="@if(isset($acttotalfee)){{'active'}}@endif"><a href="{{URL::to('admin/users/total-fee-collection')}}"><i class="fa fa-circle-o"></i> Total Fee Collection</a></li>
                    <li class="treeview @if(isset($actconfigurefees) || isset($actconfiguremerchantfees)){{'active'}}@endif">
                        <a href="javascript:void(0)">
                            <i class="fa fa-user"></i> <span>Configure Fees</span> <i class="fa fa-angle-right pull-right"></i>
                        </a>

                        <ul class="treeview-menu">
                            <li class="@if(isset($actconfigurefees)){{'active'}}@endif"><a href="{{URL::to('admin/fees/list-fees')}}"><i class="fa fa-circle-o"></i>Personal Users Fees</a></li>
                            <li class="@if(isset($actconfiguremerchantfees)){{'active'}}@endif"><a href="{{URL::to('admin/fees/list-merchant-fees')}}"><i class="fa fa-circle-o"></i>Business Users Fees</a></li>
                        </ul>					
                    </li>

                    <li class="@if(isset($actconfigtranslimit)){{'active'}}@endif"><a href="{{URL::to('admin/users/transactions-limit')}}"><i class="fa fa-circle-o"></i> Configure Trans. Limit</a></li>

                    <li class="@if(isset($actconfigagentlimit)){{'active'}}@endif"><a href="{{URL::to('admin/users/agent-limit')}}"><i class="fa fa-circle-o"></i> Configure Agent Limit</a></li>

                    <li class="@if(isset($actconfigrole)){{'active'}}@endif"><a href="{{URL::to('admin/admins/roles')}}"><i class="fa fa-circle-o"></i> Configure Department</a></li>
                    
                    <li class="@if(isset($actconversion)){{'active'}}@endif"><a href="{{URL::to('admin/users/conversion')}}"><i class="fa fa-circle-o"></i> Conversion Setting</a></li>

                    <li class="@if(isset($actcryptodeposit)){{'active'}}@endif"><a href="{{URL::to('admin/users/crypto_debit')}}"><i class="fa fa-circle-o"></i> Crypto Currency For Deposit</a></li>

                    <li class="@if(isset($actcryptowithdraw)){{'active'}}@endif"><a href="{{URL::to('admin/users/crypto_withdraw_currency')}}"><i class="fa fa-circle-o"></i> Crypto Currency For WithDraw</a></li>
                    <li class="@if(isset($giftdbasetting)){{'active'}}@endif"><a href="{{URL::to('admin/users/giftairtime_setting')}}"><i class="fa fa-circle-o"></i> Gift Card / Airtime Limit Setting</a></li>
                    <li class="@if(isset($actdbasetting)){{'active'}}@endif"><a href="{{URL::to('admin/users/dba_setting')}}"><i class="fa fa-circle-o"></i> Admin Setting</a></li>

                </ul>
            </li>            
            <li class="treeview @if(isset($actusers)){{'active'}}@endif">
                <a href="javascript:void(0)">
                    <i class="fa fa-user"></i> <span>Manage Personal Users</span> <i class="fa fa-angle-right pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li class="@if(isset($actusers)){{'active'}}@endif"><a href="{{URL::to('admin/users')}}"><i class="fa fa-circle-o"></i>Personal Users List</a></li>
                </ul>
            </li>
            <li class="treeview @if(isset($actmerchants)){{'active'}}@endif">
                <a href="javascript:void(0)">
                    <i class="fa fa-users"></i> <span>Manage Business Users</span> <i class="fa fa-angle-right pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li class="@if(isset($actmerchants)){{'active'}}@endif"><a href="{{URL::to('admin/merchants')}}"><i class="fa fa-circle-o"></i>Business Users List</a></li>
                </ul>
            </li>

            <li class="treeview @if(isset($actpagesfaq)){{'active'}}@endif">
                <a href="javascript:void(0)">
                    <i class="fa fa-file-text-o"></i> <span>Manage Pages</span> <i class="fa fa-angle-right pull-right"></i>
                </a>
                <ul class="treeview-menu">
                <!--<li class="@if(isset($actpages) || isset($actpagesfaq)){{'active'}}@endif"><a href="{{HTTP_PATH}}/admin/pages"><i class="fa fa-circle-o"></i>Page List</a></li>-->
                    <li class="@if(isset($actpagesfaq)){{'active'}}@endif"><a href="{{HTTP_PATH}}/admin/pages/faq-list"><i class="fa fa-circle-o"></i>FAQ List</a></li>
                </ul>
            </li>

            <li class="treeview @if(isset($actblogs)){{'active'}}@endif">
                <a href="javascript:void(0)">
                    <i class="fa fa-file-text-o"></i> <span>Manage Blogs</span> <i class="fa fa-angle-right pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li class="@if(isset($actblogs)){{'active'}}@endif"><a href="{{HTTP_PATH}}/admin/blogs"><i class="fa fa-circle-o"></i>Blog List</a></li>
                </ul>
            </li>
            
            <li class="treeview @if(isset($actbankagntreq)||isset($actsupportreq)||isset($actpaypalreq)||isset($actcryptodepositreq) || isset($acthelpreq) || isset($actgetintouchreq) || isset($actcryptowithdrawreq) || isset($actmanualdepositreq) || isset($actmanualwithdrawreq) || isset($actdbadepositreq) || isset($actdbadepositreqcard) || isset($actdbawithdrawreq) || isset($actgiftcardreq) || isset($acttopuprequest) || isset($actglobalwithdrawreq) ){{'active'}}@endif">
                <a href="javascript:void(0)">
                    <i class="fa fa-file-text-o"></i> <span>Manage Requests</span> <i class="fa fa-angle-right pull-right"></i>
                </a>
                <ul class="treeview-menu">
                     <li class="@if(isset($actdbadepositreq)){{'active'}}@endif"><a href="{{HTTP_PATH}}/admin/users/dba-deposit-request"><i class="fa fa-circle-o"></i>DBA Deposit Request</a></li>

                     <li class="@if(isset($actdbadepositreqcard)){{'active'}}@endif"><a href="{{HTTP_PATH}}/admin/users/dba-deposit-by-card"><i class="fa fa-circle-o"></i>DBA Deposit by Card</a></li>

                    <li class="@if(isset($actdbawithdrawreq)){{'active'}}@endif"><a href="{{HTTP_PATH}}/admin/users/dba-withdraw-request"><i class="fa fa-circle-o"></i>DBA Withdraw Request</a></li>

                    <li class="@if(isset($actmanualdepositreq)){{'active'}}@endif"><a href="{{HTTP_PATH}}/admin/users/manual-deposit-request"><i class="fa fa-circle-o"></i>Manual Deposit Request</a></li>

                    <li class="@if(isset($actmanualwithdrawreq)){{'active'}}@endif"><a href="{{HTTP_PATH}}/admin/users/manual-withdraw-request"><i class="fa fa-circle-o"></i>Manual Withdraw Request</a></li>

                    <li class="@if(isset($actglobalwithdrawreq)){{'active'}}@endif"><a href="{{HTTP_PATH}}/admin/users/global-withdraw-request"><i class="fa fa-circle-o"></i>Global / 3rd Party Pay <br> Withdraw Request</a></li>

                    <li class="@if(isset($actcryptodepositreq)){{'active'}}@endif"><a href="{{HTTP_PATH}}/admin/users/crypto-deposit-request"><i class="fa fa-circle-o"></i>Crypto Deposit Request</a></li>

                    <li class="@if(isset($actcryptowithdrawreq)){{'active'}}@endif"><a href="{{HTTP_PATH}}/admin/users/crypto-withdraw-request"><i class="fa fa-circle-o"></i>Crypto Withdraw Request</a></li>

                    <li class="@if(isset($actgiftcardreq)){{'active'}}@endif"><a href="{{HTTP_PATH}}/admin/users/g_a_request/giftcard"><i class="fa fa-circle-o"></i>GiftCard Request</a></li>

                    <li class="@if(isset($acttopuprequest)){{'active'}}@endif"><a href="{{HTTP_PATH}}/admin/users/g_a_request/topup"><i class="fa fa-circle-o"></i>Top Up Request</a></li>

                    <li class="@if(isset($actgetintouchreq)){{'active'}}@endif"><a href="{{HTTP_PATH}}/admin/users/get-in-touch-request"><i class="fa fa-circle-o"></i>Get in touch Request</a></li>
                    <li class="@if(isset($actbankagntreq)){{'active'}}@endif"><a href="{{HTTP_PATH}}/admin/users/bank-agent-request"><i class="fa fa-circle-o"></i>Bank Agent Request</a></li>
                    <li class="@if(isset($actsupportreq)){{'active'}}@endif"><a href="{{HTTP_PATH}}/admin/users/support-request"><i class="fa fa-circle-o"></i>Support Request</a></li>
                    <li class="@if(isset($acthelpreq)){{'active'}}@endif"><a href="{{HTTP_PATH}}/admin/users/help-request"><i class="fa fa-circle-o"></i>Help Request</a></li>
                    <li class="@if(isset($actpaypalreq)){{'active'}}@endif"><a href="{{HTTP_PATH}}/admin/users/paypal-request"><i class="fa fa-circle-o"></i>Paypal Request</a></li>


                </ul>
            </li>

            <li class="treeview @if(isset($acttransactionreport) || isset($actdbatransactionreport) ){{'active'}}@endif">
                <a href="javascript:void(0)">
                    <i class="fa fa-file-text-o"></i> <span>Reports</span> <i class="fa fa-angle-right pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li class="@if(isset($acttransactionreport)){{'active'}}@endif"><a href="{{HTTP_PATH}}/admin/reports/transaction-report"><i class="fa fa-circle-o"></i>Transaction Report</a></li>
                    <li class="@if(isset($actdbatransactionreport)){{'active'}}@endif"><a href="{{HTTP_PATH}}/admin/reports/dba-transaction-report"><i class="fa fa-circle-o"></i>DBA Transaction Report</a></li>
                </ul>
            </li>
        </ul>
    </section>
</aside>