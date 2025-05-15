<aside class="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <div class="logo-icon">
                <i class="fas fa-fingerprint"></i>
            </div>
            <span class="logo-text">Smart Punch</span>
        </div>
        <p class="sidebar-subtitle">Employee Portal</p>
    </div>

    <div class="nav-container">
        <ul class="nav-menu">
            <!-- Dashboard -->
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <div class="nav-icon">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Attendance -->
            <li class="nav-item">
                <a href="{{ route('attendance') }}" class="nav-link {{ request()->routeIs('attendance') ? 'active' : '' }}">
                    <div class="nav-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <span>Attendance</span>
                </a>
            </li>

           <!-- Leave & Vacation Dropdown -->
<li class="nav-item">
    <div class="dropdown">
        <a href="#" class="nav-link dropdown-toggle {{ request()->routeIs(['leaves.*', 'vacations.*']) ? 'active' : '' }}">
            <div class="nav-icon">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <span>Leave & Vacation</span>
        </a>
        <ul class="dropdown-menu {{ request()->routeIs(['leaves.*', 'vacations.*']) ? 'show' : '' }}">
            <li>
                <a href="{{ route('leaves.index') }}" class="dropdown-item {{ request()->routeIs('leaves.*') ? 'active' : '' }}">
                    <i class="fas fa-sign-out-alt"></i> Leave Requests
                </a>
            </li>
            <li>
                <a href="{{ route('vacations.index') }}" class="dropdown-item {{ request()->routeIs('vacations.*') ? 'active' : '' }}">
                    <i class="fas fa-umbrella-beach"></i> Vacation
                </a>
            </li>
        </ul>
    </div>
</li>
            </li>

            <!-- Birthdays -->
            <li class="nav-item">
                <a href="{{ route('birthdays') }}" class="nav-link {{ request()->routeIs('birthdays') ? 'active' : '' }}">
                    <div class="nav-icon">
                        <i class="fas fa-birthday-cake"></i>
                    </div>
                    <span>Birthdays</span>
                </a>
            </li>

            <!-- Finance -->
            {{-- <li class="nav-item">
                <a href="{{ route('finance.index') }}" class="nav-link {{ request()->routeIs('finance.index') ? 'active' : '' }}">
                    <div class="nav-icon">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <span>Finance</span>
                </a>
            </li> --}}

   <!-- Admin Section (Conditional) -->
@if(auth()->user()->can('viewAny', App\Models\Evaluation::class))
<li class="nav-item">
    <div class="dropdown">
        <a href="#" class="nav-link dropdown-toggle {{ request()->routeIs(['admin.*', 'evaluations.*']) ? 'active' : '' }}">
            <div class="nav-icon">
                <i class="fas fa-user-shield"></i>
            </div>
            <span>Admin</span>
            <span class="admin-badge">ADMIN</span>
        </a>
        <ul class="dropdown-menu {{ request()->routeIs(['admin.*', 'evaluations.*']) ? 'show' : '' }}">
            @can('manage-users')
            <li>
                <a href="{{ route('admin.users.index') }}" class="dropdown-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fas fa-users-cog"></i> User Management
                </a>
            </li>
            @endcan
            
            @can('viewAny', App\Models\Evaluation::class)
            <li>
                <a href="{{ route('evaluations.index') }}" class="dropdown-item {{ request()->routeIs('evaluations.*') ? 'active' : '' }}">
                    <i class="fas fa-star"></i> Performance Evaluations
                </a>
            </li>
            @endcan
            
            @can('view-reports')
            <li>
                <a href="{{ route('reports.index') }}" class="dropdown-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar"></i> Reports
                </a>
            </li>
            @endcan
        </ul>
    </div>
</li>
@endif

            <!-- Profile -->
            <li class="nav-item">
                <a href="{{ route('profile.show') }}" class="nav-link {{ request()->routeIs('profile.show') ? 'active' : '' }}">
                    <div class="nav-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <span>My Profile</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </div>
</aside>

<!-- Mobile Menu Button -->
<button class="mobile-menu-btn">
    <i class="fas fa-bars"></i>
</button>

<style>
    /* Sidebar Base Styles */
    .sidebar {
        width: 280px;
        height: 100vh;
        background: #ffffff;
        box-shadow: 0 4px 24px 0 rgba(34, 41, 47, 0.1);
        display: flex;
        flex-direction: column;
        position: fixed;
        left: 0;
        top: 0;
        z-index: 1030;
        transition: all 0.3s ease;
        border-right: 1px solid rgba(0, 0, 0, 0.05);
    }

    /* Sidebar Header */
    .sidebar-header {
        padding: 1.5rem 1.5rem 1rem;
        text-align: center;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .logo {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0.5rem;
    }

    .logo-icon {
        width: 40px;
        height: 40px;
        background: #ea9413;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 0.75rem;
        box-shadow: 0 3px 10px rgba(115, 103, 240, 0.3);
    }

    .logo-icon i {
        color: white;
        font-size: 1.25rem;
    }

    .logo-text {
        font-size: 1.25rem;
        font-weight: 700;
        color: #0c0094;
        letter-spacing: 0.5px;
    }

    .sidebar-subtitle {
        font-size: 0.8rem;
        color: #82868b;
        margin-top: 0.25rem;
    }

    /* Navigation Container */
    .nav-container {
        flex: 1;
        overflow-y: auto;
        padding: 1rem 0;
        scrollbar-width: thin;
        scrollbar-color: rgba(0, 0, 0, 0.1) transparent;
    }

    .nav-container::-webkit-scrollbar {
        width: 6px;
    }

    .nav-container::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, 0.1);
        border-radius: 3px;
    }

    /* Navigation Menu */
    .nav-menu {
        list-style: none;
        padding: 0 1rem;
    }

    .nav-item {
        margin-bottom: 0.25rem;
        position: relative;
    }

    .nav-link {
        display: flex;
        align-items: center;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        color: #82868b;
        text-decoration: none;
        font-size: 0.95rem;
        font-weight: 500;
        transition: all 0.3s ease;
        position: relative;
    }

    .nav-link:hover {
        background-color: rgba(17, 17, 26, 0.12);
        color: #1200d5;
        transform: translateX(5px);
    }

    .nav-link.active {
        background-color: rgba(0, 0, 0, 0.12);
        color: #181722;
        font-weight: 600;
    }

    .nav-icon {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        margin-right: 0.75rem;
        background-color: rgba(94, 88, 115, 0.08);
        transition: all 0.3s ease;
    }

    .nav-link:hover .nav-icon,
    .nav-link.active .nav-icon {
        background-color: rgba(115, 103, 240, 0.12);
    }

    .nav-icon i {
        font-size: 1.1rem;
    }

    /* Dropdown Styles */
    .dropdown {
        position: relative;
    }

    .dropdown-toggle {
        position: relative;
        padding-right: 2.5rem;
    }

    .dropdown-arrow {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        font-size: 0.7rem;
        transition: all 0.3s ease;
    }

    .dropdown-toggle.show .dropdown-arrow {
        transform: translateY(-50%) rotate(180deg);
    }

    .dropdown-menu {
        list-style: none;
        padding-left: 1.5rem;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
    }

    .dropdown-menu.show {
        max-height: 500px;
        padding: 0.5rem 0 0.5rem 1.5rem;
    }

    .dropdown-item {
        padding: 0.5rem 1rem;
        color: #82868b;
        text-decoration: none;
        display: flex;
        align-items: center;
        font-size: 0.9rem;
        border-radius: 6px;
        transition: all 0.3s ease;
    }

    .dropdown-item:hover {
        color: #11101e;
        background-color: rgba(115, 103, 240, 0.08);
    }

    .dropdown-item i {
        margin-right: 0.75rem;
        font-size: 0.85rem;
        width: 20px;
        text-align: center;
    }

    .dropdown-item.active {
        color: whitesmoke;
        font-weight: 500;
    }

    /* Admin Badge */
    .admin-badge {
        background-color: #ff9f43;
        color: white;
        font-size: 0.6rem;
        padding: 0.15rem 0.4rem;
        border-radius: 10px;
        margin-left: auto;
        font-weight: 600;
    }

    /* Sidebar Footer */
    .sidebar-footer {
        padding: 1.5rem;
        border-top: 1px solid rgba(0, 0, 0, 0.05);
    }

    .logout-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        padding: 0.75rem;
        border-radius: 8px;
        background-color: transparent;
        border: 1px solid rgba(0, 0, 0, 0.05);
        color: #82868b;
        font-size: 0.95rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .logout-btn:hover {
        background-color: rgba(8, 18, 108, 0.08);
        color: #1c0f0f;
        border-color: rgba(84, 147, 234, 0.2);
    }

    .logout-btn i {
        margin-right: 0.75rem;
    }

    /* Mobile Menu Button */
    .mobile-menu-btn {
        display: none;
        position: fixed;
        top: 15px;
        left: 15px;
        z-index: 1040;
        background: #0a0169;
        color: white;
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }

    /* Responsive Styles */
    @media (max-width: 992px) {
        .sidebar {
            transform: translateX(-100%);
        }
        
        .sidebar.active {
            transform: translateX(0);
        }
        
        .mobile-menu-btn {
            display: flex;
        }
    }
</style>

<script>
   document.addEventListener('DOMContentLoaded', function() {
    // Dropdown functionality
    const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
    
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation(); // إضافة هذه السطر لمنع انتشار الحدث
            
            const dropdown = this.closest('.dropdown'); // استخدام closest بدلاً من parentElement
            const menu = dropdown.querySelector('.dropdown-menu');
            
            // Close all other dropdowns first
            document.querySelectorAll('.dropdown-menu').forEach(m => {
                if (m !== menu) {
                    m.classList.remove('show');
                    const otherToggle = m.closest('.dropdown').querySelector('.dropdown-toggle');
                    otherToggle.classList.remove('show');
                    const otherArrow = otherToggle.querySelector('.dropdown-arrow');
                    if (otherArrow) otherArrow.classList.remove('show');
                }
            });
            
            // Toggle current dropdown
            this.classList.toggle('show');
            menu.classList.toggle('show');
            const arrow = this.querySelector('.dropdown-arrow');
            if (arrow) arrow.classList.toggle('show');
        });
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.classList.remove('show');
                const toggle = menu.closest('.dropdown').querySelector('.dropdown-toggle');
                toggle.classList.remove('show');
                const arrow = toggle.querySelector('.dropdown-arrow');
                if (arrow) arrow.classList.remove('show');
            });
        }
    });
    
    // Mobile menu toggle
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const sidebar = document.querySelector('.sidebar');
    
    if (mobileMenuBtn && sidebar) {
        mobileMenuBtn.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });
    }
    
    // Set active dropdown if child is active
    document.querySelectorAll('.dropdown-item.active').forEach(item => {
        const dropdown = item.closest('.dropdown');
        if (dropdown) {
            const toggle = dropdown.querySelector('.dropdown-toggle');
            const menu = dropdown.querySelector('.dropdown-menu');
            const arrow = toggle.querySelector('.dropdown-arrow');
            
            toggle.classList.add('show');
            menu.classList.add('show');
            if (arrow) arrow.classList.add('show');
        }
    });
    
    // Perfect Scrollbar initialization
    if (typeof PerfectScrollbar !== 'undefined') {
        const ps = new PerfectScrollbar('.nav-container', {
            wheelSpeed: 2,
            wheelPropagation: true,
            minScrollbarLength: 20
        });
    }
});
</script>