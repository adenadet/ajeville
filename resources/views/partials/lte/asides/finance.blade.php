<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item"><router-link to="/finance/dashboard" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></router-link></li>
        <li class="nav-item"><router-link to="/finance/assets" class="nav-link"><i class="nav-icon fas fa-boxes"></i><p>Assets</p></router-link></li>
        <li class="nav-item"><router-link to="/finance/transactions" class="nav-link"><i class="nav-icon fas fa-file-alt"></i><p>Transactions</p></router-link></li>
        <li class="nav-item"><router-link to="/finance/invoices" class="nav-link"><i class="nav-icon fas fa-file-invoice"></i><p>Invoices</p></router-link></li>
        <!--li class="nav-item"><router-link to="/finance/reconciliations" class="nav-link"><i class="nav-icon fas fa-file-signature"></i><p>Reconciliations<i class="fas fa-angle-left right"></i></p></router-link>
            <ul class="nav nav-treeview">
                <li class="nav-item"><router-link to="/finance/reconciliations/pending" class="nav-link"><i class="nav-icon fas fa-tags"></i><p>Pending Reconciliations</p></router-link></li>
                <li class="nav-item"><router-link to="/finance/reconciliations/overdue" class="nav-link"><i class="nav-icon fas fa-tags"></i><p>Overdue Reconciliations</p></router-link></li>
                <li class="nav-item"><router-link to="/finance/reconciliations/completed" class="nav-link"><i class="nav-icon fas fa-tags"></i><p>Completed</p></router-link></li>
            </ul>
        </li-->
        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fa fa-indent"></i><p>Accounts Receivables<i class="fas fa-angle-left right"></i></p></a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><router-link to="/finance/incomes" class="nav-link"><i class="nav-icon fas fa-circle"></i><p>Incomes</p></router-link></li>
                <li class="nav-item"><router-link to="/finance/payments" class="nav-link"><i class="nav-icon fas fa-circle"></i><p>Payments</p></router-link></li>
            </ul>
        </li>
        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fa fa-outdent"></i><p>Accounts Payables<i class="fas fa-angle-left right"></i></p></a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><router-link to="/finance/expenses" class="nav-link"><i class="nav-icon fas fa-circle"></i><p>Expenses</p></router-link></li>
                <li class="nav-item"><router-link to="/finance/pay_outs" class="nav-link"><i class="nav-icon fas fa-circle"></i><p>Payments</p></router-link></li>
            </ul>
        </li>
        <li class="nav-item"><router-link to="/finance/reports" class="nav-link"><i class="nav-icon fa fa-copy"></i><p>Reports</p></router-link></li>
        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fas fa-cogs"></i><p>Settings<i class="fas fa-angle-left right"></i></p></a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><router-link to="/finance/settings/branch_price_lists" class="nav-link"><i class="nav-icon far fa-circle"></i><p>Branch Price Lists</p></router-link></li>
                <li class="nav-item"><router-link to="/finance/settings/branch_accounts" class="nav-link"><i class="nav-icon far fa-circle"></i><p>Branch Accounts</p></router-link></li>
                <li class="nav-item"><router-link to="/finance/settings/expense_types" class="nav-link"><i class="nav-icon far fa-circle"></i><p>Expense Types</p></router-link></li>
                <li class="nav-item"><router-link to="/finance/settings/payment_modes" class="nav-link"><i class="nav-icon far fa-circle"></i><p>Payment Modes</p></router-link></li>
                <li class="nav-item"><router-link to="/finance/settings/price_lists" class="nav-link"><i class="nav-icon far fa-circle"></i><p>Price Lists</p></router-link></li>
            </ul>
        </li> 
    </ul>
</nav>