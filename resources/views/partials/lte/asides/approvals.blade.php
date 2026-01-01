<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">  
        <li class="nav-item"><a href="/approvals/dashboard" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a></li>
        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fa fa-shopping-cart"></i><p>Procurements<i class="fas fa-angle-left right"></i></p></a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><router-link to="/approvals/batches" class="nav-link"><i class="nav-icon fas fa-clipboard-check"></i><p>Batches</p></router-link></li>
                <li class="nav-item"><router-link to="/approvals/purchase_requests" class="nav-link"><i class="nav-icon fas fa-tasks"></i><p>Job Completions</p></router-link></li>
                <li class="nav-item"><router-link to="/approvals/purchase_orders" class="nav-link"><i class="nav-icon fas fa-shopping-cart"></i><p>Purchase Orders</p></router-link></li>
                <li class="nav-item"><router-link to="/approvals/purchase_requests" class="nav-link"><i class="nav-icon fas fa-list-alt"></i><p>Purchase Requests</p></router-link></li>
                <li class="nav-item"><router-link to="/approvals/work_orders" class="nav-link"><i class="nav-icon fas fa-concierge-bell"></i><p>Work Orders</p></router-link></li>
            </ul>
        </li>
        <!--li class="nav-item"><router-link to="/approvals/sales_orders" class="nav-link"><i class="nav-icon fas fa-cash-register"></i><p>Sales Orders</p></li-->         
        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fas fa-cash-register"></i><p>Sales Orders<i class="fas fa-angle-left right"></i></p></a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><router-link to="/approvals/sales_orders" class="nav-link"><i class="nav-icon fas fa-circle"></i><p>Orders</p></router-link></li>
                <li class="nav-item"><router-link to="/approvals/returns" class="nav-link"><i class="nav-icon fas fa-circle"></i><p>Returns</p></router-link></li>
                <!--li class="nav-item"><router-link to="/approvals/admin/designations" class="nav-link"><i class="nav-icon fas fa-circle"></i><p>Designations</p></router-link></li>
                <li class="nav-item"><router-link to="/approvals/admin/skills" class="nav-link"><i class="nav-icon fas fa-circle"></i><p>Skills</p></router-link></li-->
            </ul>
        </li>
        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fa fa-warehouse mr-1"></i><p>Inventory<i class="fas fa-angle-left right"></i></p></a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><router-link to="/approvals/inward_transfer_orders" class="nav-link"><i class="nav-icon fas fa-indent"></i><p>Inward Transfers</p></router-link></li>
                <li class="nav-item"><router-link to="/approvals/outward_transfer_orders" class="nav-link"><i class="nav-icon fas fa-outdent"></i><p>Outward Transfers</p></router-link></li>
                <!--li class="nav-item"><router-link to="/approvals/admin/designations" class="nav-link"><i class="nav-icon fas fa-circle"></i><p>Designations</p></router-link></li>
                <li class="nav-item"><router-link to="/approvals/admin/skills" class="nav-link"><i class="nav-icon fas fa-circle"></i><p>Skills</p></router-link></li-->
            </ul>
        </li>
        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fa fa-money-bill-wave mr-1"></i><p>Finance<i class="fas fa-angle-left right"></i></p></a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><router-link to="/approvals/expenses" class="nav-link"><i class="nav-icon fas fa-money-bill-alt"></i><p>Expenses</p></router-link></li>
                <li class="nav-item"><router-link to="/approvals/invoices" class="nav-link"><i class="nav-icon fas fa-file-invoice"></i><p>Invoices</p></router-link></li>
                <!--li class="nav-item"><router-link to="/approvals/outward_transfer_orders" class="nav-link"><i class="nav-icon fas fa-outdent"></i><p>Outward Transfers</p></router-link></li-->
                <!--li class="nav-item"><router-link to="/approvals/admin/designations" class="nav-link"><i class="nav-icon fas fa-circle"></i><p>Designations</p></router-link></li>
                <li class="nav-item"><router-link to="/approvals/admin/skills" class="nav-link"><i class="nav-icon fas fa-circle"></i><p>Skills</p></router-link></li-->
            </ul>
        </li>
        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fa fa-cogs"></i><p>Settings<i class="fas fa-angle-left right"></i></p></a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><router-link to="/approvals/admin/approval_matrices" class="nav-link"><i class="nav-icon far fa-circle"></i><p>Approval Matrices</p></router-link></li>
            </ul>
        </li>
    </ul>
</nav>
