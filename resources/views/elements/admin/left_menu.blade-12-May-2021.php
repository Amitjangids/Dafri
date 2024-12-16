<aside class="main-sidebar">
    <section class="sidebar">
        <ul class="sidebar-menu">
            <li class="treeview @if(isset($actdashboard)){{'active'}}@endif">
                <a href="{{URL::to('admin/admins/dashboard')}}">
                    <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                </a>
            </li>
            <li class="treeview @if(isset($actchangeusername) || isset($actchangepassword) || isset($actconfigurefees) || isset($actconfigrole) || isset($actsubadmin) || isset($actconfigtranslimit) || isset($actconfigagentlimit)){{'active'}}@endif">
                <a href="javascript:void(0)">
                    <i class="fa fa-gears"></i> <span>Configuration</span> <i class="fa fa-angle-right pull-right"></i>
                </a>
                <ul class="treeview-menu">
					<li class="@if(isset($actsubadmin)){{'active'}}@endif"><a href="{{URL::to('admin/admins/list-subadmin')}}"><i class="fa fa-circle-o"></i> Configure Subadmin</a></li>
                    <li class="@if(isset($actchangeusername)){{'active'}}@endif"><a href="{{URL::to('admin/admins/change-username')}}"><i class="fa fa-circle-o"></i> Change Username</a></li>
                    <li class="@if(isset($actchangepassword)){{'active'}}@endif"><a href="{{URL::to('admin/admins/change-password')}}"><i class="fa fa-circle-o"></i> Change Password</a></li>
                    <li class="@if(isset($actconfigurefees)){{'active'}}@endif"><a href="{{URL::to('admin/fees/list-fees')}}"><i class="fa fa-circle-o"></i> Configure Fees</a></li>
					<li class="@if(isset($actconfigtranslimit)){{'active'}}@endif"><a href="{{URL::to('admin/users/transactions-limit')}}"><i class="fa fa-circle-o"></i> Configure Trans. Limit</a></li>
					
					<li class="@if(isset($actconfigagentlimit)){{'active'}}@endif"><a href="{{URL::to('admin/users/agent-limit')}}"><i class="fa fa-circle-o"></i> Configure Agent Limit</a></li>
					
                    <li class="@if(isset($actconfigrole)){{'active'}}@endif"><a href="{{URL::to('admin/admins/roles')}}"><i class="fa fa-circle-o"></i> Configure Department</a></li>
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
            
            <li class="treeview @if(isset($actpages)){{'active'}}@endif">
                <a href="javascript:void(0)">
                    <i class="fa fa-file-text-o"></i> <span>Manage Pages</span> <i class="fa fa-angle-right pull-right"></i>
                </a>
                <ul class="treeview-menu">
                <li class="@if(isset($actpages) || isset($actpagesfaq)){{'active'}}@endif"><a href="{{HTTP_PATH}}/admin/pages"><i class="fa fa-circle-o"></i>Page List</a></li>
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
			
			<li class="treeview @if(isset($actbankagntreq)||isset($actsupportreq)||isset($actpaypalreq)){{'active'}}@endif">
                <a href="javascript:void(0)">
                    <i class="fa fa-file-text-o"></i> <span>Manage Requests</span> <i class="fa fa-angle-right pull-right"></i>
                </a>
                <ul class="treeview-menu">
                 <li class="@if(isset($actbankagntreq)){{'active'}}@endif"><a href="{{HTTP_PATH}}/admin/users/bank-agent-request"><i class="fa fa-circle-o"></i>Bank Agent Request</a></li>
				 <li class="@if(isset($actsupportreq)){{'active'}}@endif"><a href="{{HTTP_PATH}}/admin/users/support-request"><i class="fa fa-circle-o"></i>Support Request</a></li>
				 <li class="@if(isset($actpaypalreq)){{'active'}}@endif"><a href="{{HTTP_PATH}}/admin/users/paypal-request"><i class="fa fa-circle-o"></i>Paypal Request</a></li>
                </ul>
            </li>
			
			<li class="treeview @if(isset($acttransactionreport)){{'active'}}@endif">
                <a href="javascript:void(0)">
                    <i class="fa fa-file-text-o"></i> <span>Reports</span> <i class="fa fa-angle-right pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li class="@if(isset($acttransactionreport)){{'active'}}@endif"><a href="{{HTTP_PATH}}/admin/reports/transaction-report"><i class="fa fa-circle-o"></i>Transaction Report</a></li>
                </ul>
            </li>

        </ul>
    </section>
</aside>