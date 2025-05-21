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
                <a href="{{ route('attendance.index') }}" class="nav-link {{ request()->routeIs('attendance') ? 'active' : '' }}">
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

            <!-- Birthdays -->
            <li class="nav-item">
                <a href="{{ route('birthdays.index') }}" class="nav-link {{ request()->routeIs('birthdays.*') ? 'active' : '' }}">
                    <div class="nav-icon">
                        <i class="fas fa-birthday-cake"></i>
                    </div>
                    <span>Birthdays</span>
                </a>
            </li>

            <!-- Admin Section -->
            @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()))
<li class="nav-item">
    <div class="dropdown">
        <a href="#" class="nav-link dropdown-toggle {{ request()->routeIs(['admin.*', 'evaluations.*']) ? 'active' : '' }}">
            <div class="nav-icon">
                <i class="fas fa-user-shield"></i>
            </div>
            <span>Admin Panel</span>
            <span class="admin-badge {{ auth()->user()->isSuperAdmin() ? 'super-admin' : '' }}">
                {{ auth()->user()->isSuperAdmin() ? 'SUPER ADMIN' : 'ADMIN' }}
            </span>
        </a>
        <ul class="dropdown-menu {{ request()->routeIs(['admin.*', 'evaluations.*']) ? 'show' : '' }}">
            <!-- Employee Management -->
            <li>
                <a href="{{ route('admin.employees.index') }}" class="dropdown-item {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}">
                    <i class="fas fa-users-cog"></i> Employee Management
                </a>
            </li>
            
            <!-- Evaluations (for Super Admin and Admin) -->
            @if(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin())
            <li>
                <a href="{{ route('evaluations.index') }}" class="dropdown-item {{ request()->routeIs('evaluations.*') ? 'active' : '' }}">
                    <i class="fas fa-star"></i> Employee Evaluations
                </a>
            </li>
            @endif
        </ul>
    </div>
</li>
@endif

            <!-- Department Manager Section -->
            @if(auth()->check() && auth()->user()->isDepartmentManager())
            <li class="nav-item">
                <div class="dropdown">
                    <a href="#" class="nav-link dropdown-toggle {{ request()->routeIs(['department.*']) ? 'active' : '' }}">
                        <div class="nav-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <span>Department</span>
                        <span class="admin-badge manager">MANAGER</span>
                    </a>
                    <ul class="dropdown-menu {{ request()->routeIs(['department.*']) ? 'show' : '' }}">
                        <li>
                            <a href="{{ route('department.employees') }}" class="dropdown-item {{ request()->routeIs('department.employees') ? 'active' : '' }}">
                                <i class="fas fa-users"></i> My Team
                            </a>
                        </li>
                          <li>
                <a href="{{ route('evaluations.index') }}" class="dropdown-item {{ request()->routeIs('evaluations.*') ? 'active' : '' }}">
                    <i class="fas fa-star"></i> Employee Evaluations
                </a>
            </li>
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
        width: 260px;
        height: 100vh;
        background: #f8f9fa;
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
        display: flex;
        flex-direction: column;
        position: fixed;
        left: 0;
        top: 0;
        z-index: 1030;
        transition: all 0.3s ease;
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
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 0.75rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .logo-icon i {
        color: white;
        font-size: 1.25rem;
    }

    .logo-text {
        font-size: 1.15rem;
        font-weight: 700;
        color: #2d3748;
        letter-spacing: 0.5px;
    }

    .sidebar-subtitle {
        font-size: 0.8rem;
        color: #718096;
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
        padding: 0.65rem 1rem;
        border-radius: 6px;
        color: #4a5568;
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .nav-link:hover {
        background-color: rgba(102, 126, 234, 0.1);
        color: #667eea;
    }

    .nav-link.active {
        background-color: rgba(102, 126, 234, 0.15);
        color: #667eea;
        border-left: 3px solid #667eea;
        font-weight: 600;
    }

    .nav-icon {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        margin-right: 0.75rem;
        background-color: transparent;
        transition: all 0.2s ease;
    }

    .nav-link:hover .nav-icon,
    .nav-link.active .nav-icon {
        background-color: rgba(102, 126, 234, 0.1);
    }

    .nav-icon i {
        font-size: 1rem;
    }

    /* Dropdown Styles */
    .dropdown {
        position: relative;
    }

    .dropdown-toggle {
        position: relative;
        padding-right: 2.5rem;
    }

    .dropdown-toggle::after {
        content: '\f078';
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        font-size: 0.7rem;
        transition: transform 0.2s ease;
    }

    .dropdown-toggle.show::after {
        transform: translateY(-50%) rotate(180deg);
    }

    .dropdown-menu {
        list-style: none;
        padding-left: 0.5rem;
        margin-top: 0.25rem;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
    }

    .dropdown-menu.show {
        max-height: 500px;
        padding: 0.25rem 0 0.25rem 0.5rem;
    }

    .dropdown-item {
        padding: 0.4rem 0.8rem;
        color: #4a5568;
        text-decoration: none;
        display: flex;
        align-items: center;
        font-size: 0.85rem;
        border-radius: 4px;
        transition: all 0.2s ease;
    }

    .dropdown-item:hover {
        color: #667eea;
        background-color: rgba(102, 126, 234, 0.08);
    }

    .dropdown-item i {
        margin-right: 0.75rem;
        font-size: 0.8rem;
        width: 18px;
        text-align: center;
    }

    .dropdown-item.active {
        color: #667eea;
        font-weight: 500;
        background-color: rgba(102, 126, 234, 0.1);
    }

    /* Admin Badge */
    .admin-badge {
        background-color: #667eea;
        color: white;
        font-size: 0.55rem;
        padding: 0.1rem 0.35rem;
        border-radius: 10px;
        margin-left: auto;
        font-weight: 600;
        text-transform: uppercase;
    }

    .admin-badge.super-admin {
        background-color: #764ba2;
    }

    .admin-badge.manager {
        background-color: #38a169;
    }

    /* Sidebar Footer */
    .sidebar-footer {
        padding: 1rem;
        border-top: 1px solid rgba(0, 0, 0, 0.05);
    }

    .logout-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        padding: 0.65rem;
        border-radius: 6px;
        background-color: #f1f5f9;
        border: none;
        color: #4a5568;
        font-size: 0.9rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .logout-btn:hover {
        background-color: #e2e8f0;
        color: #2d3748;
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
        background: #667eea;
        color: white;
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    /* Overlay for mobile */
    .sidebar-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1029;
        display: none;
    }

    /* Responsive Styles */
    @media (max-width: 992px) {
        .sidebar {
            transform: translateX(-100%);
        }
        
        .sidebar.active {
            transform: translateX(0);
            box-shadow: 2px 0 15px rgba(0, 0, 0, 0.1);
        }
        
        .mobile-menu-btn {
            display: flex;
        }
        
        .mobile-menu-btn.active {
            transform: rotate(90deg);
        }
        
        .sidebar-overlay {
            display: block;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Improved dropdown functionality
    const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
    
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const parentItem = this.closest('.nav-item');
            const menu = this.nextElementSibling;
            
            // Close all other dropdowns except the current one
            document.querySelectorAll('.dropdown-menu').forEach(m => {
                if (m !== menu) {
                    m.classList.remove('show');
                    m.previousElementSibling.classList.remove('show');
                    m.closest('.nav-item').classList.remove('dropdown-open');
                }
            });
            
            // Toggle current dropdown
            this.classList.toggle('show');
            menu.classList.toggle('show');
            parentItem.classList.toggle('dropdown-open');
        });
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.classList.remove('show');
                menu.previousElementSibling.classList.remove('show');
                menu.closest('.nav-item').classList.remove('dropdown-open');
            });
        }
    });
    
    // Mobile menu toggle
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const sidebar = document.querySelector('.sidebar');
    
    if (mobileMenuBtn && sidebar) {
        mobileMenuBtn.addEventListener('click', function() {
            this.classList.toggle('active');
            sidebar.classList.toggle('active');
            
            // Add overlay when sidebar is open
            if (sidebar.classList.contains('active')) {
                const overlay = document.createElement('div');
                overlay.className = 'sidebar-overlay';
                overlay.addEventListener('click', function() {
                    sidebar.classList.remove('active');
                    mobileMenuBtn.classList.remove('active');
                    this.remove();
                });
                document.body.appendChild(overlay);
            } else {
                document.querySelector('.sidebar-overlay')?.remove();
            }
        });
    }
    
    // Auto set active dropdowns based on current route
    document.querySelectorAll('.dropdown-item.active').forEach(item => {
        const dropdown = item.closest('.dropdown');
        if (dropdown) {
            const toggle = dropdown.querySelector('.dropdown-toggle');
            const menu = dropdown.querySelector('.dropdown-menu');
            
            toggle.classList.add('show');
            menu.classList.add('show');
            dropdown.closest('.nav-item').classList.add('dropdown-open');
        }
    });
    
    // Fix for birthday link click issue
    document.querySelectorAll('.nav-link[href*="birthdays"]').forEach(link => {
        link.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
});
</script>