<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item has-treeview"><a href="/dashboard" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a></li>
        <li class="nav-item"><a href="/profile" class="nav-link"><i class="nav-icon fas fa-user"></i><p>Profile</p></a></li>
        @if(Auth::user()->hasRole('Approvals') || Auth::user()->hasRole('Super Admin'))
        <li class="nav-item"><a href="/approvals" class="nav-link"><i class="nav-icon fas fa-file-signature"></i><p>Approvals</p></a></li>
        @endif
        @if(Auth::user()->hasRole('Archives') || Auth::user()->hasRole('Super Admin'))
        <li class="nav-item"><a href="/archives" class="nav-link"><i class="nav-icon fas fa-archive"></i><p>Archives</p></a></li>
        @endif
        @if(Auth::user()->hasRole('Coor') || Auth::user()->hasRole('Super Admin'))
        <li class="nav-item has-treeview">
            <a href="#" class="nav-link"><i class="nav-icon fas fa-piggy-bank"></i><p>Cooperative <i class="right fas fa-angle-left"></i></p></a>
            <ul class="nav nav-treeview">
                @if(Auth::user()->hasRole('Coop Staff') || Auth::user()->hasRole('Super Admin'))<li class="nav-item"><a href="/coop/dashboard" class="nav-link"><i class="fas fa-piggy-bank nav-icon"></i><p>Cooperator</p></a></li>@endif
                @if(Auth::user()->hasRole('Coop Admin') || Auth::user()->hasRole('Super Admin'))<li class="nav-item"><a href="/coop_admin/dashboard" class="nav-link"><i class="fas fa-user-cog nav-icon"></i><p>Admin</p></a></li>@endif
            </ul>
        </li>
        @endif
        @if(Auth::user()->hasRole('Customer Relations') || Auth::user()->hasRole('Super Admin'))
        <li class="nav-item"><a href="/customer_relations/dashboard" class="nav-link"><i class="nav-icon fas fa-users"></i><p>Customer Relations</p></a></li>
        @endif
        @if(Auth::user()->hasRole('EMR') || Auth::user()->hasRole('Super Admin'))
        <li class="nav-item has-treeview">
            <a href="#" class="nav-link"><i class="nav-icon fas fa-clinic-medical"></i><p>EMR <i class="right fas fa-angle-left"></i></p></a>
            <ul class="nav nav-treeview">
                @if(Auth::user()->hasRole('EMR Consultant') || Auth::user()->hasRole('Super Admin'))<li class="nav-item"><a href="/emr/consultations" class="nav-link"><i class="mr-1 fas fa-user-md"></i><p> Consultation</p></a></li>@endif
                @if(Auth::user()->hasRole('EMR Front Office') || Auth::user()->hasRole('Super Admin'))<li class="nav-item"><a href="/emr/front_office" class="nav-link"><i class="mr-1 fas fa-laptop-house"></i><p> Front Office</p></a></li>@endif
                @if(Auth::user()->hasRole('EMR Laboratory') || Auth::user()->hasRole('Super Admin'))<li class="nav-item"><a href="/emr/laboratory" class="nav-link"><i class="mr-1 fas fa-flask"></i><p> Laboratory</p></a></li>@endif
                @if(Auth::user()->hasRole('EMR Managed Care') || Auth::user()->hasRole('Super Admin'))<li class="nav-item"><a href="/emr/managed_care" class="nav-link"><i class="mr-1 fas fa-file-alt"></i><p> Managed Care</p></a></li>@endif
                @if(Auth::user()->hasRole('EMR Nurse') || Auth::user()->hasRole('Super Admin'))<li class="nav-item"><a href="/emr/nursing" class="nav-link"><i class="mr-1 fas fa-user-nurse"></i><p> Nursing Care</p></a></li>@endif
                @if(Auth::user()->hasRole('EMR Operations') || Auth::user()->hasRole('Super Admin'))<li class="nav-item"><a href="/emr/operations" class="nav-link"><i class="mr-1 fas fa-cogs"></i><p> Operations</p></a></li>@endif
                @if(Auth::user()->hasRole('EMR Radiology') || Auth::user()->hasRole('Super Admin'))<li class="nav-item"><a href="/emr/radiology" class="nav-link"><i class="mr-1 fas fa-x-ray"></i><p> Radiology</p></a></li>@endif
                @if(Auth::user()->hasRole('EMR Records') || Auth::user()->hasRole('Super Admin'))<li class="nav-item"><a href="/emr/records" class="nav-link"><i class="mr-1 fas fa-file-medical"></i><p> Records</p></a></li>@endif
            </ul>
        </li>
        @endif
        @if(Auth::user()->hasRole('Equipments') || Auth::user()->hasRole('Super Admin'))
        <li class="nav-item"><a href="/equipments" class="nav-link"><i class="nav-icon fas fa-laptop-house"></i><p>Equipment Manager</p></a></li>
        @endif
        @if(Auth::user()->hasRole('Escrows') || Auth::user()->hasRole('Super Admin'))
        <li class="nav-item has-treeview">
            <a href="#" class="nav-link"><i class="nav-icon fas fa-money-check"></i><p>Escrows <i class="right fas fa-angle-left"></i></p></a>
            <ul class="nav nav-treeview">
                @if(Auth::user()->hasRole('Escrows') || Auth::user()->hasRole('Super Admin'))<li class="nav-item"><a href="/escrows" class="nav-link"><i class="nav-icon fas fa-money-check"></i><p>Escrows</p></a></li>@endif
                @if(Auth::user()->hasRole('Escrow Admin') || Auth::user()->hasRole('Super Admin'))<li class="nav-item"><a href="/escrow_admin" class="nav-link"><i class="nav-icon fas fa-money-check"></i><p>Admin </p></a></li>@endif
            </ul>
        </li>
        @endif
        @if(Auth::user()->hasRole('Finances') || Auth::user()->hasRole('Super Admin'))
        <li class="nav-item"><a href="/finance" class="nav-link"><i class="nav-icon fas fa-money-bill-wave"></i><p>Finances</p></a></li>
        @endif
        @if(Auth::user()->hasRole('Human Resources') || Auth::user()->hasRole('Super Admin'))
        <li class="nav-item has-treeview">
            <a href="#" class="nav-link"><i class="nav-icon fas fa-users-cog"></i><p>Human Resources <i class="right fas fa-angle-left"></i></p></a>
            <ul class="nav nav-treeview">
                @if(Auth::user()->hasRole('HR Staff') || Auth::user()->hasRole('Super Admin'))<li class="nav-item"><a href="/hrms/dashboard" class="nav-link"><i class="fas fa-user nav-icon"></i><p>Staff</p></a></li>@endif
                @if(Auth::user()->hasRole('HR Admin') || Auth::user()->hasRole('Super Admin'))<li class="nav-item"><a href="/hrms_admin/dashboard" class="nav-link"><i class="fas fa-user-cog nav-icon"></i><p>Admin</p></a></li>@endif
            </ul>
        </li>
        @endif
        @if(Auth::user()->hasRole('Insurance') || Auth::user()->hasRole('Super Admin'))
        <li class="nav-item"><a href="/insurance" class="nav-link"><i class="nav-icon fas fa-house-damage"></i><p>Insurance</p></a></li>
        @endif
        @if(Auth::user()->hasRole('Inventory') || Auth::user()->hasRole('Super Admin'))
        <li class="nav-item"><a href="/inventory" class="nav-link"><i class="nav-icon fas fa-warehouse"></i><p>Inventory</p></a></li>
        @endif
        @if(Auth::user()->hasRole('Learn') || Auth::user()->hasRole('Super Admin'))
        <li class="nav-item has-treeview">
            <a href="#" class="nav-link"><i class="nav-icon fas fa-scroll"></i><p>Learn Management <i class="right fas fa-angle-left"></i></p></a>
            <ul class="nav nav-treeview">
                @if(Auth::user()->hasRole('Learn User') || Auth::user()->hasRole('Super Admin'))<li class="nav-item"><a href="/learn/student/dashboard" class="nav-link"><i class="fas fa-chalkboard nav-icon"></i><p>Student Area</p></a></li>@endif
                @if(Auth::user()->hasRole('Learn Tutor') || Auth::user()->hasRole('Super Admin'))<li class="nav-item"><a href="/learn/tutor/dashboard" class="nav-link"><i class="fas fa-chalkboard-teacher nav-icon"></i><p>Tutor Area</p></a></li>@endif
                @if(Auth::user()->hasRole('Learn Admin') || Auth::user()->hasRole('Super Admin'))<li class="nav-item"><a href="/learn/admin/dashboard" class="nav-link"><i class="fas fa-user-cog nav-icon"></i><p>Admin Area</p></a></li>@endif
            </ul>
        </li>
        @endif
        @if(Auth::user()->hasRole('Loans') || Auth::user()->hasRole('Super Admin'))
        <li class="nav-item has-treeview">
            <a href="#" class="nav-link"><i class="nav-icon fas fa-hand-holding-usd"></i><p>Loans <i class="right fas fa-angle-left"></i></p></a>
            <ul class="nav nav-treeview">
                @if(Auth::user()->hasRole('Loans Customer') || Auth::user()->hasRole('Super Admin'))<li class="nav-item"><a href="/loans/dashboard" class="nav-link"><i class="fas fa-user-circle nav-icon"></i><p>Customer Area</p></a></li>@endif
                @if(Auth::user()->hasRole('Loans Staff') || Auth::user()->hasRole('Super Admin'))<li class="nav-item"><a href="/loans_staff/dashboard" class="nav-link"><i class="fas fa-house-user nav-icon"></i><p>Staff Area</p></a></li>@endif
                @if(Auth::user()->hasRole('Loans Admin') || Auth::user()->hasRole('Super Admin'))<li class="nav-item"><a href="/loans_admin/dashboard" class="nav-link"><i class="fa fa-user-cog nav-icon"></i><p>Admin Area</p></a></li>@endif
            </ul>
        </li>
        @endif
        @if(Auth::user()->hasRole('Messages') || Auth::user()->hasRole('Super Admin'))
        <li class="nav-item"><a href="/messages" class="nav-link"><i class="nav-icon fas fa-comments"></i><p>Messages</p></a></li>
        @endif
        <li class="nav-item"><a href="/notices" class="nav-link"><i class="nav-icon fas fa-clipboard"></i><p>Notices</p></a></li>
        <li class="nav-item"><a href="/ticketing" class="nav-link"><i class="nav-icon fas fa-tags"></i><p>Tickets</p></a></li>
        @if(Auth::user()->hasRole('Policies') || Auth::user()->hasRole('Super Admin'))
        <li class="nav-item"><a href="/policies" class="nav-link"><i class="nav-icon fas fa-copy"></i><p>Policies</p></a></li>
        @endif
        @if(Auth::user()->hasRole('Procurement') || Auth::user()->hasRole('Super Admin'))
        <li class="nav-item"><a href="/procurement" class="nav-link"><i class="nav-icon fas fa-shopping-cart"></i><p>Procurement</p></a></li>
        @endif
        @if(Auth::user()->hasRole('Sales Orders') || Auth::user()->hasRole('Super Admin'))
        <li class="nav-item"><a href="/sales_orders" class="nav-link"><i class="nav-icon fas  fa-cash-register"></i><p>Sales Orders</p></a></li>
        @endif
        @if(Auth::user()->hasRole('Users') || Auth::user()->hasRole('Super Admin'))
        <li class="nav-item"><a href="/users" class="nav-link"><i class="nav-icon fa fa-users"></i><p>Users</p></a></li>
        @endif
    </ul>
</nav>