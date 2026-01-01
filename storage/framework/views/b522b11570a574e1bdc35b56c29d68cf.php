<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">  
        <li class="nav-item"><a href="/emr/consultations/dashboard" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a></li>
        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fa fa-list"></i><p>Queues<i class="fas fa-angle-left right"></i></p></a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><router-link to="/emr/consultations/my_queue" class="nav-link"><i class="nav-icon fas fa-circle"></i><p>My Queue </p></router-link></li>  
                <li class="nav-item"><router-link to="/emr/consultations/department_queue" class="nav-link"><i class="nav-icon fas fa-circle"></i><p>Department Queue </p></router-link></li>  
                <li class="nav-item"><router-link to="/emr/consultations/doctor_queue" class="nav-link"><i class="nav-icon fas fa-circle"></i><p>Doctors' Queue </p></router-link></li>  
            </ul>
        </li>   
        <li class="nav-item"><router-link to="/emr/consultations/my_previous_consultations" class="nav-link"><i class="nav-icon fas fa-copy"></i><p>Previous Consultations</p></router-link></li>
                 
        <?php if(Auth::user()->hasRole('HR Admin') || Auth::user()->hasRole('Super Admin')): ?>
        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fa fa-laptop"></i><p>Hiring<i class="fas fa-angle-left right"></i></p></a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><router-link to="/emr/consultations/admin/applications" class="nav-link"><i class="nav-icon fas fa-circle"></i><p>Applications</p></router-link></li>
                <li class="nav-item"><router-link to="/emr/consultations/admin/jobs" class="nav-link"><i class="nav-icon fas fa-circle"></i><p>Jobs</p></router-link></li>
                <li class="nav-item"><router-link to="/emr/consultations/admin/designations" class="nav-link"><i class="nav-icon fas fa-circle"></i><p>Designations</p></router-link></li>
                <li class="nav-item"><router-link to="/emr/consultations/admin/skills" class="nav-link"><i class="nav-icon fas fa-circle"></i><p>Skills</p></router-link></li>
            </ul>
        </li>
        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fa fa-mug-hot"></i><p>Leaves<i class="fas fa-angle-left right"></i></p></a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><router-link to="/emr/consultations/admission" class="nav-link"><i class="nav-icon fas fa-circle"></i><p>Admitted Patients</p></router-link></li>
                <li class="nav-item"><router-link to="/emr/consultations/admin/leave_types" class="nav-link"><i class="nav-icon fas fa-circle"></i><p>All Leave Types</p></router-link></li>
            </ul>
        </li>
        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fa fa-money-bill"></i><p>Payroll<i class="fas fa-angle-left right"></i></p></a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><router-link to="/emr/consultations/admin/salary_rates" class="nav-link"><i class="nav-icon far fa-circle"></i><p>Salary Rates</p></router-link></li>
                <li class="nav-item"><router-link to="/emr/consultations/admin/salaries" class="nav-link"><i class="nav-icon far fa-circle"></i><p>All Salaries</p></router-link></li>
                <li class="nav-item"><router-link to="/emr/consultations/admin/payslips" class="nav-link"><i class="nav-icon far fa-circle"></i><p>All Payslips</p></router-link></li>
                <li class="nav-item"><router-link to="/emr/consultations/admin/bonuses" class="nav-link"><i class="nav-icon far fa-circle"></i><p>Bonus</p></router-link></li>
                <li class="nav-item"><router-link to="/emr/consultations/admin/deductions" class="nav-link"><i class="nav-icon far fa-circle"></i><p>Deductions</p></router-link></li>
            </ul>
        </li>
        <?php endif; ?>
        <li class="nav-item"><router-link to="<?php echo e(route('logout')); ?>" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="nav-link"><i class="nav-icon fas fa-power-off"></i><p>Log Out </p></router-link></li>
        <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="d-none"><?php echo csrf_field(); ?></form>
    </ul>
</nav>
<?php /**PATH C:\wamp64\www\laravel10\ajeville\resources\views/partials/lte/asides/consultation.blade.php ENDPATH**/ ?>