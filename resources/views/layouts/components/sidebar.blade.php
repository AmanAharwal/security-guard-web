<div class="vertical-menu">

    <div data-simplebar class="h-100">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title" key="t-menu">Menu</li>

                <li @class(['mm-active' => Request::is('dashboard')])>
                    <a href="{{ route('admin.dashboard.index') }}" class="waves-effect">
                        <i class="bx bx-home-alt"></i>
                        <span key="t-dashboards">Dashboard</span>
                    </a>
                </li>
                @can('view security guards')
                <li class="{{ Request::segment(2) == 'security-guards' ? 'mm-active' : '' }}">
                    <a href="{{ route('security-guards.index')}}" class="waves-effect">
                        <i class="fas fa-user-secret"></i>
                        <span key="t-spreadsheet">Onboard Guard</span>
                    </a>
                </li>
                @endcan
               
                @can('view guard roster')
                <li class="{{ Request::segment(2) == 'guard-rosters' ? 'mm-active' : '' }}">
                    <a href="{{ route('guard-rosters.index')}}" class="waves-effect">
                        <i class="bx bx-hive"></i>
                        <span key="t-spreadsheet">Guard Roster</span>
                    </a>
                </li>
                @endcan
                @can('view attendance')
                <li class="{{ Request::segment(2) == 'attendance' ? 'mm-active' : '' }}">
                    <a href="{{ route('attendance.index')}}" class="waves-effect">
                        <i class="bx bx-spreadsheet"></i>
                        <span key="t-spreadsheet">Attendance</span>
                    </a>
                </li>
                @endcanany
                @can('view nst deduction')
                <li class="{{ Request::segment(2) == 'deductions' ? 'mm-active' : '' }}">
                    <a href="{{ route('deductions.index')}}" class="waves-effect">
                        <i class="bx bx-briefcase-alt"></i>
                        <span key="t-spreadsheet">NST Deduction</span>
                    </a>
                </li>
                @endcanany
                @can('view payroll')
                <li class="{{ Request::segment(2) == 'payrolls' ? 'mm-active' : '' }}">
                    <a href="{{ route('payrolls.index')}}" class="waves-effect">
                        <i class="bx bx-checkbox-square"></i>
                        <span key="t-spreadsheet">Payroll</span>
                    </a>
                </li>
                @endcan
                @can('view invoice')
                <li class="{{ Request::segment(2) == 'invoices' ? 'mm-active' : '' }}">
                    <a href="{{ route('invoices.index')}}" class="waves-effect">
                        <i class="bx bx-selection"></i>
                        <span key="t-spreadsheet">Invoice</span>
                    </a>
                </li>
                @endcan
                @can('view leaves')
                <li class="{{ Request::segment(2) == 'leaves' ? 'mm-active' : '' }}">
                    <a href="{{ route('leaves.index')}}" class="waves-effect">
                        <i class="bx bx-tone"></i>
                        <span key="t-spreadsheet">Leaves</span>
                    </a>
                </li>
                @endcan
                {{--<li {{ Request::segment(2) == 'calendar-management' ? 'mm-active' : '' }}>
                    <a href="{{ route('calendar.management') }}" class="waves-effect">
                        <i class="bx bx-calendar"></i>
                        <span key="t-calendar">Calendar Management</span>
                    </a>
                </li> --}}
               
                @can('view client')
                <li class="{{ Request::segment(2) == 'clients' ? 'mm-active' : '' }}">
                    <a href="{{ route('clients.index')}}" class="waves-effect">
                        <i class="bx bxs-group"></i>
                        <span key="t-user">Client listing</span>
                    </a>
                </li>
                @endcan
                @can('view client site')
                <li class="{{ Request::segment(2) == 'client-sites' ? 'mm-active' : '' }}">
                    <a href="{{ route('client-sites.index')}}" class="waves-effect">
                        <i class="bx bx-buildings"></i>
                        <span key="t-user">Client sites</span>
                    </a>
                </li>
                @endcan
                @can('view rate master')
                <li class="{{ Request::segment(2) == 'rate-master' ? 'mm-active' : '' }}">
                    <a href="{{ route('rate-master.index')}}" class="waves-effect">
                        <i class="bx bx-receipt"></i>
                        <span key="t-receipt">Rate Master</span>
                    </a>
                </li>
                @endcan
                <li class="{{ Request::segment(2) == 'fortnight-dates' ? 'mm-active' : '' }}">
                    <a href="{{ route('fortnight-dates.index')}}" class="waves-effect">
                        <i class="bx bx-grid-horizontal"></i>
                        <span key="t-wrench">Fortnight Dates</span>
                    </a>
                </li>
                @can('view public holiday')
                <li class="{{ Request::segment(2) == 'public-holidays' ? 'mm-active' : '' }}">
                    <a href="{{ route('public-holidays.index')}}" class="waves-effect">
                        <i class="bx bx-gift"></i>
                        <span key="t-receipt">Public Holidays</span>
                    </a>
                </li>
                @endcan

                @canany(['view employee', 'view employee rate master', 'view employee leaves', 'view employee payroll'])
                <li class="menu-title" key="t-menu">Employee</li>
                @can('view employee')
                <li class="{{ Request::segment(2) == 'employees' ? 'mm-active' : '' }}">
                    <a href="{{ route('employees.index')}}" class="waves-effect">
                        <i class="fas fa-female"></i>
                        <span key="t-spreadsheet">Employee</span>
                    </a>
                </li>
                @endcan       
                @can('view employee rate master')
                <li class="{{ Request::segment(2) == 'employee-rate-master' ? 'mm-active' : '' }}">
                    <a href="{{ route('employee-rate-master.index')}}" class="waves-effect">
                        <i class="bx bx-bolt-circle"></i>
                        <span key="t-spreadsheet">Employee Rate Master</span>
                    </a>
                </li>
                @endcan
                <li class="{{ Request::segment(2) == 'employee-deductions' ? 'mm-active' : '' }}">
                    <a href="{{ route('employee-deductions.index')}}" class="waves-effect">
                        <i class="bx bx-briefcase-alt"></i>
                        <span key="t-spreadsheet">Employee Deduction</span>
                    </a>
                </li>
                @can('view employee leaves')
                <li class="{{ Request::segment(2) == 'employee-leaves' ? 'mm-active' : '' }}">
                    <a href="{{ route('employee-leaves.index')}}" class="waves-effect">
                        <i class="bx bx-basket"></i>
                        <span key="t-spreadsheet">Employee Leaves</span>
                    </a>
                </li>
                @endcan
                @can('view employee payroll')
                <li class="{{ Request::segment(2) == 'employee-payroll' ? 'mm-active' : '' }}">
                    <a href="{{ route('employee-payroll.index')}}" class="waves-effect">
                        <i class="bx bx-checkbox-square"></i>
                        <span key="t-spreadsheet">Employee Payroll</span>
                    </a>
                </li>
                @endcan
                <li class="{{ Request::segment(2) == 'twenty-two-days-interval' ? 'mm-active' : '' }}">
                    <a href="{{ route('get-interval')}}" class="waves-effect">
                        <i class="bx bx-grid-horizontal"></i>
                        <span key="t-spreadsheet">Twenty Two Days Interval</span>
                    </a>
                </li>
                @endcanany
                <li class="menu-title" key="t-menu">Other</li>        
                @canany(['view faq', 'view help request'])
                <li @class([
                    'active' => Request::is('faq', 'help_requests'),
                ])>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="bx bx-list-ul"></i>
                        <span key="t-dashboards">Manage Pages</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        @can('view faq')
                        <li><a href="{{ route('faq.index')}}" key="t-tui-calendar">FAQ</a></li>
                        @endcan
                        @can('view help request')
                        <li><a href="{{ route('help_requests.index')}}" key="t-tui-calendar">Help Request</a></li>
                        @endcan
                    </ul>
                </li>
                @endcanany
                @canany(['view site setting', 'view gerenal setting', 'view payment setting'])
                <li @class([
                    'active' => Request::is('settings', 'settings/general-setting', 'settings/payment-setting'),
                ])>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="bx bxs-wrench"></i>
                        <span key="t-dashboards">Settings</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        @can('view site setting')
                        <li><a href="{{ route('settings.index')}}" key="t-tui-calendar">Site Settings</a></li>
                        @endcan
                        @can('view gerenal setting')
                        <li><a href="{{ route('settings.gerenal-settings')}}" key="t-tui-calendar">Gerenal Settings</a></li>
                        @endcan
                        @can('view payment setting')
                        <li><a href="{{ route('settings.payment-settings')}}" key="t-tui-calendar">Payment Settings</a></li>
                        @endcan
                    </ul>
                </li>
                @endcanany

                @canany(['view roles & permissions', 'view user'])
                <li  @class([
                    'active' => Request::is('roles-and-permissions', 'users/index', 'roles-and-permissions/role-list', 'roles-and-permissions/index'),
                ])>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="bx bxs-user-detail"></i>
                        <span key="t-dashboards">User/Roles</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        @can('view roles & permissions')
                        <li><a href="{{ route('roles-and-permissions.role-list') }}" key="t-tui-calendar">Manage Roles</a></li>
                        @endcan
                        @can('view user')
                        <li class="{{ Request::segment(2) == 'users' ? 'mm-active' : '' }}"><a href="{{ route('users.index')}}" key="t-user">User</a></li>
                        @endcan
                        @can('view roles & permissions')
                        <li><a href="{{ route('roles-and-permissions.index') }}" key="t-full-calendar">Manage Permissions</a></li>
                        @endcan
                    </ul>
                </li>
                @endcanany

                {{--<li>
                    <a href="javascript: void(0);" class="waves-effect has-arrow">
                        <i class="bx bx-briefcase-alt"></i>
                        <span key="t-jobs">Jobs</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="javascript: void(0);" key="t-job-list">Job List</a></li>
                        <li><a href="javascript: void(0);" key="t-job-grid">Job Grid</a></li>
                        <li><a href="javascript: void(0);" key="t-apply-job">Apply Job</a></li>
                        <li><a href="javascript: void(0);" key="t-job-details">Job Details</a></li>
                        <li><a href="javascript: void(0);" key="t-Jobs-categories">Jobs Categories</a></li>
                        <li>
                            <a href="javascript: void(0);" class="has-arrow" key="t-candidate">Candidate</a>
                            <ul class="sub-menu" aria-expanded="true">
                                <li><a href="javascript: void(0);" key="t-list">List</a></li>
                                <li><a href="javascript: void(0);" key="t-overview">Overview</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>--}}
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>