
<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">    
        <li class="nav-item"><router-link to="/payroll/dashboard" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></router-link></li>
        <li class="nav-item"><router-link to="/payroll/salary_rates" class="nav-link"><i class="nav-icon fa fa-money-bill-wave"></i><p>Salary Rates</p></router-link></li>
        <li class="nav-item"><router-link to="/payroll/salaries" class="nav-link"><i class="nav-icon fa fa-money-bill"></i><p>All Salaries</p></router-link></li>
        <li class="nav-item"><router-link to="/payroll/payslips" class="nav-link"><i class="nav-icon far fa-circle"></i><p>All Payslips</p></router-link></li>
        <li class="nav-item"><router-link to="/payroll/bonuses" class="nav-link"><i class="nav-icon far fa-circle"></i><p>Bonus</p></router-link></li>
        <li class="nav-item"><router-link to="/payroll/deductions" class="nav-link"><i class="nav-icon far fa-circle"></i><p>Deductions</p></router-link></li>
        <li class="nav-item"><router-link to="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="nav-link"><i class="nav-icon fas fa-power-off"></i><p>Log Out </p></router-link></li>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
    </ul>
</nav>
