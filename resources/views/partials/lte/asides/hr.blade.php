<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item"><router-link to="/hrms/dashboard" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></router-link></li>
        <li class="nav-item has-treeview">
            <a href="#" class="nav-link"><i class="nav-icon fas fa-calendar-check"></i><p>Attendance <i class="right fas fa-angle-left"></i></p></a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><a href="/hrms/clock_ins" class="nav-link"><i class="fas fa-clock nav-icon"></i><p>My Clock Ins</p></a></li>
                <li class="nav-item"><a href="/hrms/attendance_summaries" class="nav-link"><i class="fas fa-chalkboard-teacher nav-icon"></i><p>Attendance Summaries</p></a></li>
                <li class="nav-item"><a href="/hrms/team_schedules" class="nav-link"><i class="fas fa-calendar-alt nav-icon"></i><p>Team Shifts</p></a></li>
            </ul>
        </li>
        <li class="nav-item"><router-link to="/hrms/educations" class="nav-link"><i class="nav-icon fas fa-chalkboard-teacher"></i><p>Education</p></router-link></li>  
        <li class="nav-item"><router-link to="/hrms/trainings" class="nav-link"><i class="nav-icon fas fa-certificate"></i><p>Trainings</p></router-link></li>  
        <li class="nav-item"><router-link to="/hrms/payslips" class="nav-link"><i class="nav-icon fas fa-money-check"></i><p>Payslips</p></router-link></li>
        <li class="nav-item has-treeview">
            <a href="#" class="nav-link"><i class="nav-icon fa fa-calendar"></i><p>Leave Management <i class="right fas fa-angle-left"></i></p></a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><a href="/hrms/leaves/requests" class="nav-link"><i class="fas fa-user-graduate nav-icon"></i><p>My Requests</p></a></li>
                <li class="nav-item"><a href="/hrms/leaves/allowances" class="nav-link"><i class="fas fa-chalkboard-teacher nav-icon"></i><p>Allowances</p></a></li>
                <li class="nav-item"><a href="/hrms/leaves/types_assigned" class="nav-link"><i class="fas fa-chalkboard-teacher nav-icon"></i><p>Assigned Leave Types</p></a></li>
                <li class="nav-item"><a href="/hrms/leaves/team_requests" class="nav-link"><i class="fas fa-chalkboard-teacher nav-icon"></i><p>Team Requests</p></a></li>
            </ul>
        </li>
        
    </ul>
</nav>