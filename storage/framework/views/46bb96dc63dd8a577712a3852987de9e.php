<nav class="main-header navbar navbar-expand navbar-white navbar-dark bg-navy">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>
    <form class="form-inline ml-3">
        <div class="input-group input-group-sm">
            <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
            <div class="input-group-append">
            <button class="btn btn-navbar" type="submit">
                <i class="fas fa-search"></i>
            </button>
            </div>
        </div>
    </form>
    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">
            <a id="dropdownSubMenu2" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">
                <?php if(isset($icon)): ?><i class="<?php echo e($icon); ?> mr-1"></i>
                <?php endif; ?>
                <?php echo e($page_title); ?>

            </a>
            <ul aria-labelledby="dropdownSubMenu2" class="dropdown-menu border-0 shadow">
                <?php if(Auth::user()->hasRole('Approvals') || Auth::user()->hasRole('Super Admin')): ?>
                <li><a href="/approvals" class="dropdown-item"><i class="mr-1 fas fa-file-signature"></i> Approvals</a></li>
                <?php endif; ?>
                <?php if(Auth::user()->hasRole('Archives') || Auth::user()->hasRole('Super Admin')): ?>
                <li><a href="/archives" class="dropdown-item"><i class="mr-1 fas fa-archive"></i> Archives</a></li>
                <?php endif; ?>
                <?php if(Auth::user()->hasRole('Coop') || Auth::user()->hasRole('Super Admin')): ?>
                <li class="dropdown-submenu dropdown-hover">
                    <a id="dropdownSubMenu2" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-item dropdown-toggle"><i class="mr-1 fas fa-piggy-bank"></i> Cooperative</a>
                    <ul aria-labelledby="dropdownSubMenu2" class="dropdown-menu border-0 shadow">
                        <li><a href="/coop/administrator" class="dropdown-item"><i class="mr-1 fas fa-user-cog"></i> Administrator</a></li>
                        <li><a href="/coop/dashboard" class="dropdown-item"><i class="mr-1 fas fa-piggy-bank"></i> Cooperator</a></li>
                    </ul>
                </li>
                <?php endif; ?>
                <?php if(Auth::user()->hasRole('Customer Relations') || Auth::user()->hasRole('Super Admin')): ?>
                <li><a href="/customer_relations" class="dropdown-item"><i class="mr-1 fa fa-users"></i> Customer Relations</a></li>
                <?php endif; ?>
                <li><a href="/dashboard" class="dropdown-item"><i class="mr-1 fas fa-tachometer-alt"></i> Dashboard</a></li>
                <?php if(Auth::user()->hasRole('EMR') || Auth::user()->hasRole('Super Admin')): ?>
                <li class="dropdown-submenu dropdown-hover">
                    <a id="dropdownSubMenu2" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-item dropdown-toggle"><i class="mr-1 fas fa-clinic-medical"></i> EMR</a>
                    <ul aria-labelledby="dropdownSubMenu2" class="dropdown-menu border-0 shadow">
                        <li><a href="/emr/anesthesist" class="dropdown-item"><i class="mr-1 fas fa-user-md"></i> Anesthesist</a></li>
                        <li><a href="/emr/consultations" class="dropdown-item"><i class="mr-1 fas fa-user-md"></i> Consultation</a></li>
                        <li><a href="/emr/billings" class="dropdown-item"><i class="mr-1 fas fa-cash-register"></i> Billings</a></li>
                        <li><a href="/emr/front_office" class="dropdown-item"><i class="mr-1 fas fa-laptop-house"></i> Front Office</a></li>
                        <li><a href="/emr/laboratory" class="dropdown-item"><i class="mr-1 fas fa-flask"></i> Laboratory</a></li>
                        <li><a href="/emr/insurance" class="dropdown-item"><i class="mr-1 fas fa-file-alt"></i> Managed Care</a></li>
                        <li><a href="/emr/nursing" class="dropdown-item"><i class="mr-1 fas fa-user-nurse"></i> Nursing Care</a></li>
                        <li><a href="/emr/operations" class="dropdown-item"><i class="mr-1 fas fa-cogs"></i> Operations</a></li>
                        <li><a href="/emr/pharmacy" class="dropdown-item"><i class="mr-1 fas fa-pills"></i> Pharmacy</a></li>
                        <li><a href="/emr/procedures" class="dropdown-item"><i class="mr-1 fas fa-user-md"></i> Procedures</a></li>
                        <li><a href="/emr/radiology" class="dropdown-item"><i class="mr-1 fas fa-x-ray"></i> Radiology</a></li>
                        <li><a href="/emr/records" class="dropdown-item"><i class="mr-1 fas fa-file-medical"></i> Records</a></li>
                    </ul>
                </li>
                <?php endif; ?>
                <?php if(Auth::user()->hasRole('Equipments') || Auth::user()->hasRole('Super Admin')): ?>
                <li><a href="/equipments" class="dropdown-item"><i class="mr-1 fas fa-laptop-house"></i> Equipment Manager</a></li>
                <?php endif; ?>
                <?php if(Auth::user()->hasRole('Escrows') || Auth::user()->hasRole('Super Admin')): ?>
                <li class="dropdown-submenu dropdown-hover">
                    <a id="dropdownSubMenu2" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-item dropdown-toggle"><i class="mr-1 fas fa-money-check"></i> Escrows</a>
                    <ul aria-labelledby="dropdownSubMenu2" class="dropdown-menu border-0 shadow">
                        <li><a href="/escrow_admin" class="dropdown-item"><i class="mr-1 fas fa-user-cog"></i> Administrator</a></li>
                        <li><a href="/escrows" class="dropdown-item"><i class="mr-1 fas fa-piggy-bank"></i> Cooperator</a></li>
                    </ul>
                </li>
                <?php endif; ?>
                <?php if(Auth::user()->hasRole('Facility') || Auth::user()->hasRole('Super Admin')): ?>
                <li><a href="/facility" class="dropdown-item"><i class="mr-1 fas fa-building"></i> Facility Manager</a></li>
                <?php endif; ?>
                <?php if(Auth::user()->hasRole('Finance') || Auth::user()->hasRole('Super Admin')): ?>
                <li><a href="/finance" class="dropdown-item"><i class="mr-1 fas fa-money-bill-wave"></i> Finance</a></li>
                <?php endif; ?>
                <?php if(Auth::user()->hasRole('HR') || Auth::user()->hasRole('Super Admin')): ?>
                <li class="dropdown-submenu dropdown-hover">
                    <a id="dropdownSubMenu2" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-item dropdown-toggle"><i class="mr-1 fas fa-users-cog"></i> Human Resources</a>
                    <ul aria-labelledby="dropdownSubMenu2" class="dropdown-menu border-0 shadow">
                        <li><a href="/hrms/dashboard" class="dropdown-item"><i class="mr-1 fas fa-user"></i> General</a></li>
                        <li><a href="/hrms_admin/dashboard" class="dropdown-item"><i class="mr-1 fas fa-user-cog"></i> Admin</a></li>
                    </ul>
                </li>
                <?php endif; ?>
                <?php if(Auth::user()->hasRole('Insurance') || Auth::user()->hasRole('Super Admin')): ?>
                <li><a href="/insurance" class="dropdown-item"><i class="mr-1 fas fa-house-damage"></i> Insurance</a></li>
                <?php endif; ?>
                <?php if(Auth::user()->hasRole('Inventory') || Auth::user()->hasRole('Super Admin')): ?>
                <li><a href="/inventory" class="dropdown-item"><i class="mr-1 fas fa-warehouse"></i> Inventory</a></li>
                <?php endif; ?>
                <?php if(Auth::user()->hasRole('Learn') || Auth::user()->hasRole('Super Admin')): ?>
                <li class="dropdown-submenu dropdown-hover">
                    <a id="dropdownSubMenu4" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-item dropdown-toggle"><i class="mr-1 fas fa-scroll"></i> Learn Mgt</a>
                    <ul aria-labelledby="dropdownSubMenu4" class="dropdown-menu border-0 shadow">
                        <?php if(Auth::user()->hasRole('Learn User') || Auth::user()->hasRole('Super Admin')): ?><li><a href="/learn/student/dashboard" class="dropdown-item"><i class="fas fa-chalkboard nav-icon mr-1"></i>Student</a></li><?php endif; ?>
                        <?php if(Auth::user()->hasRole('Learn Tutor') || Auth::user()->hasRole('Super Admin')): ?><li><a href="/learn/tutor/dashboard" class="dropdown-item"><i class="fas fa-chalkboard-teacher mr-1 nav-icon"></i>Tutor</a></li><?php endif; ?>
                        <?php if(Auth::user()->hasRole('Learn Admin') || Auth::user()->hasRole('Super Admin')): ?><li><a href="/learn/admin/dashboard" class="dropdown-item"><i class="fas fa-user-cog nav-icon mr-1"></i>Admin</a></li><?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>
                <?php if(Auth::user()->hasRole('Loans') || Auth::user()->hasRole('Super Admin')): ?>
                <li class="dropdown-submenu dropdown-hover">
                    <a id="dropdownSubMenu2" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-item dropdown-toggle"><i class="mr-1 fas fa-hand-holding-usd"></i> Loans</a>
                    <ul aria-labelledby="dropdownSubMenu2" class="dropdown-menu border-0 shadow">
                        <li><a href="/loans/dashboard" class="dropdown-item"><i class="mr-1 fas fa-user-circle"></i> Customer Area</a></li>
                        <li><a href="/loans_staff/dashboard" class="dropdown-item"><i class="mr-1 fas fa-house-user"></i> Staff Area</a></li>
                    </ul>
                </li>
                <?php endif; ?>
                <?php if(Auth::user()->hasRole('Messages') || Auth::user()->hasRole('Super Admin')): ?>
                <li><a href="/chats" class="dropdown-item"><i class="mr-1 fas fa-comments"></i> Messages</a></li>
                <?php endif; ?>
                <?php if(Auth::user()->hasRole('Policies') || Auth::user()->hasRole('Super Admin')): ?>
                <li><a href="/policies" class="dropdown-item"><i class="mr-1 fas fa-copy"></i> Policies</a></li>
                <?php endif; ?>
                <?php if(Auth::user()->hasRole('Procurement') || Auth::user()->hasRole('Super Admin')): ?>
                <li><a href="/procurement" class="dropdown-item"><i class="mr-1 fas fa-shopping-cart"></i> Procurement</a></li>
                <?php endif; ?>
                <?php if(Auth::user()->hasRole('Sales Order') || Auth::user()->hasRole('Super Admin')): ?>
                <li><a href="/sales_orders" class="dropdown-item"><i class="mr-1 fas fa-cash-register"></i> Sales Order</a></li>
                <?php endif; ?>
                <?php if(Auth::user()->hasRole('Customer Relations') || Auth::user()->hasRole('Super Admin')): ?>
                <li><a href="/operations" class="dropdown-item"><i class="mr-1 fa fa-cogs"></i> System Config</a></li>
                <?php endif; ?>
                <?php if(Auth::user()->hasRole('Customer Relations') || Auth::user()->hasRole('Super Admin')): ?>
                <li><a href="/users" class="dropdown-item"><i class="mr-1 fa fa-users"></i> Users</a></li>
                <?php endif; ?>
                
            </ul>
        </li>
        <li class="nav-item d-none d-sm-inline-block dropdown"><header-branch :branches="<?php echo e(Auth::user()->branches); ?>" :staff_branch="<?php echo e(Auth::user()->branch); ?>" /></li>
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#"><i class="far fa-bell"></i><span class="badge badge-warning navbar-badge">15</span></a>
            <!--div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-header">15 Notifications</span>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item"><i class="fas fa-envelope mr-2"></i> 4 new messages<span class="float-right text-muted text-sm">3 mins</span></a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item"><i class="fas fa-users mr-2"></i> 8 friend requests<span class="float-right text-muted text-sm">12 hours</span></a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item"><i class="fas fa-file mr-2"></i> 3 new reports<span class="float-right text-muted text-sm">2 days</span></a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
            </div-->
        </li>
        <li class="nav-item dropdown">
            <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle"><?php echo e(Auth::user()->first_name); ?> &nbsp; <img height="30px" src="<?php echo e(asset('img/profile/'.Auth::user()->image)); ?>" class="img-circle elevation-2" alt="<?php echo e(Auth::user()->first_name); ?> <?php echo e(Auth::user()->last_name); ?>">
            </a>
            <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
                <li><a href="/profile" class="dropdown-item"><i class="fa fa-user mr-1"></i>Profile</a></li>
                <li><a href="/settings" class="dropdown-item"><i class="fa fa-cogs mr-1"></i> Settings</a></li>
                <li>
                    <a href="<?php echo e(route('logout')); ?>" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="dropdown-item"><i class="fas fa-power-off mr-1"></i>Log Out</a>
                    <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="d-none"><?php echo csrf_field(); ?></form>
                </li>
            </ul>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button"><i
                class="fas fa-th-large"></i>
            </a>
        </li>
    </ul>
</nav><?php /**PATH C:\wamp64\www\laravel10\ajeville\resources\views/partials/lte/top.blade.php ENDPATH**/ ?>