<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">    
        <li class="nav-item"><router-link to="/learn/dashboard" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></router-link></li>
        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fas fa-user-graduate"></i><p>Student Area<i class="fas fa-angle-left right"></i></p></a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><router-link to="/learn/student/user_courses" class="nav-link"><i class="nav-icon fa fa-book"></i><p>Courses</p></router-link></li>
                <li class="nav-item"><router-link to="/learn/student/user_exams" class="nav-link"><i class="nav-icon fa fa-user-clock"></i><p>Exams</p></router-link></li>
                <li class="nav-item"><router-link to="/learn/student/exam_results" class="nav-link"><i class="nav-icon fa fa-portrait"></i><p>Results</p></router-link></li>
            </ul>
        </li>
        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fas fa-chalkboard-teacher"></i><p>Tutor Area<i class="fas fa-angle-left right"></i></p></a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><router-link to="/learn/tutor/courses" class="nav-link"><i class="nav-icon fa fa-book-open"></i><p>My Courses</p></router-link></li>
                <li class="nav-item"><router-link to="/learn/tutor/exams" class="nav-link"><i class="nav-icon fa fa-user-clock"></i><p>My Exams</p></router-link></li>
                <li class="nav-item"><router-link to="/learn/tutor/results" class="nav-link"><i class="nav-icon fas fa-portrait"></i><p>My Results</p></router-link></li>
            </ul>
        </li>
        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fas fa-user-cog"></i><p>Admin Area<i class="fas fa-angle-left right"></i></p></a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><router-link to="/learn/admin/courses" class="nav-link"><i class="nav-icon fas fa-book-open"></i><p>All Courses</p></router-link></li>
                <li class="nav-item"><router-link to="/learn/admin/exams" class="nav-link"><i class="nav-icon fa fa-user-clock"></i><p>Exams</p></router-link></li>
                <li class="nav-item"><router-link to="/learn/admin/results" class="nav-link"><i class="nav-icon fas fa-portrait"></i><p>Results</p></router-link></li>
                <li class="nav-item"><router-link to="/learn/admin/categories" class="nav-link"><i class="nav-icon fa fa-cogs"></i><p>Categories</p></router-link></li>
            </ul>
        </li>
        <li class="nav-item"><router-link to="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="nav-link"><i class="nav-icon fas fa-power-off"></i><p>Log Out </p></router-link></li>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
    </ul>
</nav>