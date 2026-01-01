
    
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">    
    <li class="nav-item"><a href="/staff/dashboard" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a></li>
    <li class="nav-item"><a href="/staff/contacts" class="nav-link"><i class="nav-icon fas fa-comments"></i><p>Contacts</p></a></li>
    <li class="nav-item"><a href="/staff/customers" class="nav-link"><i class="nav-icon fas fa-users"></i><p>Customers</p></a></li>
    <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fas fa-copy"></i><p>Loans<i class="fas fa-angle-left right"></i></p></a>
        <ul class="nav nav-treeview">
            @if(Auth::user()->hasRole('Risk Officer') || Auth::user()->hasRole('Managing Director') || Auth::user()->hasRole('Group Head') || Auth::user()->hasRole('Super Admin'))<li class="nav-item"><a href="/staff/loans" class="nav-link"><i class="nav-icon fas fa-file"></i><p>All Loans</p></a></li>@endif
            @if(Auth::user()->hasRole('Managing Director') || Auth::user()->hasRole('Group Head') || Auth::user()->hasRole('Super Admin'))<li class="nav-item"><a href="/staff/loans/undisbursed" class="nav-link"><i class="nav-icon fas fa-file"></i><p>Undisbursed Loans</p></a></li>@endif
            @if(Auth::user()->hasRole('Account Officer') || Auth::user()->hasRole('Super Admin'))<li class="nav-item"><a href="/staff/accounts/assigned" class="nav-link"><i class="nav-icon fa fa-clipboard"></i><p>My Assigned Loans</p></a></li>@endif
            @if(Auth::user()->hasRole('Risk Officer') || Auth::user()->hasRole('Enterprise Risk Manager') || Auth::user()->hasRole('Super Admin'))<li class="nav-item"><a href="/staff/accounts/risks" class="nav-link"><i class="nav-icon fa fa-clipboard"></i><p>Awaiting Risk Analysis</p></a></li>@endif
            @if(Auth::user()->hasRole('Managing Director') || Auth::user()->hasRole('Group Head') || Auth::user()->hasRole('Super Admin'))<li class="nav-item"><a href="/staff/accounts/pending" class="nav-link"><i class="nav-icon fas fa-file"></i><p>Pending Loans</p></a></li>@endif
        </ul>
    </li>
    <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fas fa-check"></i><p>Confirmation<i class="fas fa-angle-left right"></i></p></a>
        <ul class="nav nav-treeview">
            <li class="nav-item"><a href="/staff/confirm/loans" class="nav-link"><i class="nav-icon fas fa-file"></i><p>Approve Loans</p></a></li>
            @if(Auth::user()->hasRole('Risk Officer') || Auth::user()->hasRole('Enterprise Risk Manager') || Auth::user()->hasRole('Group Head') || Auth::user()->hasRole('Super Admin'))<li class="nav-item"><a href="/staff/confirm/addresses" class="nav-link"><i class="nav-icon fas fa-file"></i><p>Confirm Address</p></a></li>@endif
            @if(Auth::user()->hasRole('Risk Officer') || Auth::user()->hasRole('Enterprise Risk Manager') || Auth::user()->hasRole('Group Head') || Auth::user()->hasRole('Super Admin'))<li class="nav-item"><a href="/staff/confirm/bvns" class="nav-link"><i class="nav-icon fas fa-file"></i><p>Confirm BVN</p></a></li>@endif
            @if(Auth::user()->hasRole('Risk Officer') || Auth::user()->hasRole('Enterprise Risk Manager') || Auth::user()->hasRole('Group Head') || Auth::user()->hasRole('Super Admin'))<li class="nav-item"><a href="/staff/confirm/nins" class="nav-link"><i class="nav-icon fas fa-file"></i><p>Confirm NIN</p></a></li>@endif
        </ul>
    </li>
    <li class="nav-item"><a href="/staff/tickets" class="nav-link"><i class="nav-icon fas fa-tags"></i><p>Tickets</p></a></li>
    <li class="nav-item"><a href="/staff/chats" class="nav-link"><i class="nav-icon fas fa-comments"></i><p>Chats</p></a></li>
</ul>
