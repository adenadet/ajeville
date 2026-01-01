<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item"><router-link to="/hrms_admin/dashboard" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></router-link></li>
        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fa fa-file"></i><p>Assessments<i class="fas fa-angle-left right"></i></p></a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><router-link to="/hrms_admin/assessments" class="nav-link"><i class="nav-icon fas fa-circle"></i><p>Assessments</p></router-link></li>
                <li class="nav-item"><router-link to="/hrms_admin/assessment_employee_targets" class="nav-link"><i class="nav-icon fas fa-circle"></i><p>Employee Targets</p></router-link></li>
                <li class="nav-item"><router-link to="/hrms_admin/assessment_hr_items" class="nav-link"><i class="nav-icon fas fa-circle"></i><p>HR Items</p></router-link></li>
                <li class="nav-item"><router-link to="/hrms_admin/assessment_periods" class="nav-link"><i class="nav-icon fas fa-circle"></i><p>Periods</p></router-link></li>
            </ul>
        </li>
        <li class="nav-item"><router-link to="/hrms_admin/jobs" class="nav-link"><i class="nav-icon fas fa-tasks"></i><p>Jobs</p></router-link></li>
        <li class="nav-item"><router-link to="/hrms_admin/applicants" class="nav-link"><i class="nav-icon fas fa-users"></i><p>Applicants</p></router-link></li>
        <li class="nav-item"><router-link to="/hrms_admin/employees" class="nav-link"><i class="nav-icon fas fa-users"></i><p>Employees</p></router-link></li>  
        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fa fa-file"></i><p>Payroll Manager<i class="fas fa-angle-left right"></i></p></a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><router-link to="/hrms_admin/salary_structures" class="nav-link"><i class="nav-icon fas fa-circle"></i><p>Salary Structures</p></router-link></li>
                <li class="nav-item"><router-link to="/hrms_admin/employee_deductions" class="nav-link"><i class="nav-icon fas fa-circle"></i><p>Employee Deductions</p></router-link></li>
                <li class="nav-item"><router-link to="/hrms_admin/employee_bonus" class="nav-link"><i class="nav-icon fas fa-circle"></i><p>Employee Bonus</p></router-link></li>
            </ul>
        </li>
        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fa fa-calendar"></i><p>Leave Manager<i class="fas fa-angle-left right"></i></p></a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><router-link to="/hrms_admin/leave_types" class="nav-link"><i class="nav-icon fas fa-circle"></i><p>Leave Types</p></router-link></li>  
                <li class="nav-item"><router-link to="/hrms_admin/leave_allowances" class="nav-link"><i class="nav-icon fas fa-circle"></i><p>Leave Allowances</p></router-link></li>
            </ul>
        </li>
        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fa fa-cogs"></i><p>General Settings<i class="fas fa-angle-left right"></i></p></a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><router-link to="/hrms_admin/departments" class="nav-link"><i class="nav-icon fas fa-circle"></i><p>Departments</p></router-link></li>  
                <li class="nav-item"><router-link to="/hrms_admin/designations" class="nav-link"><i class="nav-icon fas fa-circle"></i><p>Designations</p></router-link></li>  
            </ul>
        </li>
    </ul>
</nav>