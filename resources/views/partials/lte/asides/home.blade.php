<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">    
        <li class="nav-item">
            <a href="/dashboard" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a>
        </li>
        <li class="nav-item">
            <a href="/profile" class="nav-link"><i class="nav-icon fas fa-user"></i><p>Profile</p></a>
        </li>
        <li class="nav-item">
            <a href="/notifications" class="nav-link"><i class="nav-icon fa fa-bell"></i><p>Notifications</p></a>
        </li>
        <li class="nav-item">
            <a href="/loans" class="nav-link"><i class="nav-icon fas fa-chart-bar"></i><p>Loan History</p></a>
        </li>
        <li class="nav-item">
            <a href="/tickets" class="nav-link"><i class="nav-icon fas fa-tags"></i><p>Tickets</p></a>
        </li>
        <li class="nav-item">
            <a href="/guarantors" class="nav-link"><i class="nav-icon fas fa-user-friends"></i><p>Guarantors</p></a>
        </li>
        
        <li class="nav-item">
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="nav-link">
                <i class="nav-icon fas fa-power-off"></i><p>Log Out </p></a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
        </li>
    </ul>
</nav>
