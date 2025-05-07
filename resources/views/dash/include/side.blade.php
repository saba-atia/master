<style>
   /* القائمة الجانبية */
.sidenav {
    width: 280px;
    background: #fff;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    padding: 1.5rem 0;
    height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
    z-index: 100;
    overflow-y: auto;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    background: linear-gradient(#fff 30%, rgba(255,255,255,0)),
            linear-gradient(rgba(255,255,255,0), #fff 70%) bottom,
            radial-gradient(farthest-side at 50% 0, rgba(0,0,0,0.1), transparent),
            radial-gradient(farthest-side at 50% 100%, rgba(0,0,0,0.1), transparent) bottom;
background-repeat: no-repeat;
background-size: 100% 20px, 100% 20px, 100% 14px, 100% 14px;
background-attachment: local, local, scroll, scroll;

}

.sidenav-header {
    padding: 0 1.5rem 1.25rem;
    margin-bottom: 0.5rem;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.sidenav-header h6 {
    font-size: 1.25rem;
    color: #3a3541;
    font-weight: 700;
    letter-spacing: 0.5px;
}

.sidenav-header p {
    font-size: 0.85rem;
    color: #6d6d6d;
    margin-top: 0.25rem;
}

/* القائمة */
.navbar-nav {
    padding: 0 1rem;
}

.nav-item {
    margin-bottom: 0.375rem;
    position: relative;
}

.nav-link-custom {
    display: flex;
    align-items: center;
    padding: 0.75rem 1.25rem;
    border-radius: 8px;
    color: #5e5873;
    font-size: 0.95rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid transparent;
}

.nav-link-custom:hover {
    background-color: rgba(115, 103, 240, 0.04);
    color: #7367f0;
    transform: translateX(4px);
}

.nav-link-custom.active {
    background-color: rgba(115, 103, 240, 0.08);
    color: #7367f0;
    font-weight: 500;
    border-color: rgba(115, 103, 240, 0.12);
}

.nav-link-custom.active .icon {
    background-color: rgba(115, 103, 240, 0.12);
}

.icon {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    margin-right: 0.875rem;
    background-color: rgba(94, 88, 115, 0.08);
    transition: all 0.3s ease;
}

.icon i {
    font-size: 1.1rem;
}

/* القوائم المنسدلة */
.nav-link-custom.dropdown-toggle {
    margin-bottom: 0;
    padding-bottom: 0.75rem;
    position: relative;
}

.nav-link-custom.dropdown-toggle::after {
    content: "";
    position: absolute;
    right: 1.25rem;
    top: 50%;
    margin-top: -0.25rem;
    width: 0.5rem;
    height: 0.5rem;
    border-right: 2px solid currentColor;
    border-bottom: 2px solid currentColor;
    transform: rotate(45deg);
    transition: transform 0.3s ease;
}
.dropdown {
    position: relative;
}

.dropdown-menu {
    position: absolute;
    bottom: 100%; /* تظهر فوق العنصر */
    left: 0;
    width: 100%;
    background: white;
    border-radius: 8px;
    box-shadow: 0 5px 25px rgba(0, 0, 0, 0.12);
    padding: 0;
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease, padding 0.3s ease;
    z-index: 1100;
}


.dropdown-menu.show {
    max-height: 200px; /* ارتفاع كافٍ للعناصر */
    padding: 0.5rem 0;
    margin-bottom: 5px; /* مسافة صغيرة بين القائمة والعنصر */
}
.dropdown-item {
    padding: 0.75rem 1.5rem;
    color: #5e5873;
    font-size: 0.925rem;
    display: flex;
    align-items: center;
    transition: all 0.2s ease;
}

.dropdown-item:hover {
    background-color: rgba(115, 103, 240, 0.06);
    color: #7367f0;
    padding-left: 1.75rem;
}

.dropdown-item i {
    margin-right: 0.875rem;
    font-size: 0.95rem;
    width: 20px;
    text-align: center;
}

/* تسجيل الخروج */
.sidenav-footer {
    border-top: 1px solid rgba(0, 0, 0, 0.05);
    padding: 1.5rem;
    margin-top: 1rem;
}

.custom-logout-btn {
    background: transparent;
    border: 1px solid rgba(0, 0, 0, 0.08);
    color: #5e5873;
    padding: 0.75rem 1.25rem;
    border-radius: 8px;
    transition: all 0.3s ease;
    font-size: 0.95rem;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer; /* مؤشر الماوس */
    transform: none !important; /* إلغاء التحويلات */
}


.custom-logout-btn:hover {
    background-color: rgba(234, 84, 85, 0.06);
    color: #ea5455;
    border-color: rgba(234, 84, 85, 0.12);
    
    /* التأكد من عدم وجود تحويلات في حالة hover */
    transform: none !important;
}



.custom-logout-btn:active {
    transform: none !important; /* التأكد من عدم وجود حركة عند الضغط */
}

/* المحتوى الرئيسي */
.main-content {
    margin-left: 280px;
    padding: 2rem;
}

</style>

<!-- HTML يبقى كما هو مع تغيير كلاسات py-3 إلى py-2 -->
  


<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-4" id="sidenav-main" style="background: #fffafa; box-shadow: 0 0 28px 0 rgba(82, 63, 105, 0.08);">
    <div class="sidenav-header d-flex flex-column align-items-center pt-4">
        <div class="mt-3 text-center">
            <h6 class="mb-0 text-dark font-weight-bold">Smart Punch</h6>
            <p class="text-xs text-secondary mb-0">Employee Portal</p>
        </div>
    </div>
    <hr class="horizontal dark mt-3 mb-1 opacity-2">

    <div class="collapse navbar-collapse w-auto h-auto pt-1" id="sidenav-collapse-main">
        <ul class="navbar-nav">

            {{-- Dashboard --}}
            <li class="nav-item mb-1">
                <a class="nav-link py-3 nav-link-custom {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <div class="icon icon-shape icon-sm border-radius-md bg-gray-100 text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-chart-bar me-2 text-dark"></i>
                    </div>
                    <span class="nav-link-text ms-2">Dashboard</span>
                </a>
            </li>

            {{-- Attendance --}}
            <li class="nav-item mb-1">
                <a class="nav-link py-3 nav-link-custom {{ request()->routeIs('attendance') ? 'active' : '' }}" href="{{ route('attendance') }}">
                    <div class="icon icon-shape icon-sm border-radius-md bg-gray-100 text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-calendar-check text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Attendance</span>
                </a>
            </li>

             {{-- Leave & Vacation Dropdown --}}
             <li class="nav-item">
                <div class="dropdown">
                    <a class="nav-link nav-link-custom dropdown-toggle py-2" href="#" role="button" aria-expanded="false">
                        <div class="icon icon-shape icon-sm border-radius-md bg-gray-100 text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fas fa-calendar-alt text-dark text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Leave & Vacation</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="{{ route('leaves.index') }}">
                                <i class="fas fa-sign-out-alt me-2"></i> Leave
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('vacations.index') }}">
                                <i class="fas fa-umbrella-beach me-2"></i> Vacation
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            {{-- Birthdays --}}
            <li class="nav-item mb-1">
                <a class="nav-link py-3 nav-link-custom {{ request()->routeIs('birthdays') ? 'active' : '' }}" href="{{ route('birthdays') }}">
                    <div class="icon icon-shape icon-sm border-radius-md bg-gray-100 text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-birthday-cake text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Birthdays</span>
                </a>
            </li>

            {{-- Finance --}}
            <li class="nav-item mb-1">
                <a class="nav-link py-3 nav-link-custom {{ request()->routeIs('finance') ? 'active' : '' }}" href="{{ route('finance') }}">
                    <div class="icon icon-shape icon-sm border-radius-md bg-gray-100 text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-money-bill-wave text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Finance</span>
                </a>
            </li>

            {{-- Evaluations (Admins only) --}}
            @if(in_array(auth()->user()->role, ['admin', 'super_admin', 'department_manager']))
            <li class="nav-item mb-1">
                    <a class="nav-link py-3 nav-link-custom {{ request()->routeIs('evaluations') ? 'active' : '' }}" href="{{ route('evaluations') }}">
                        <div class="icon icon-shape icon-sm border-radius-md bg-gray-100 text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fas fa-star text-dark text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Evaluations</span>
                    </a>
                </li>
            @endif
          
            @if(in_array(auth()->user()->role, ['super_admin', 'admin', 'department_manager']))            <li class="nav-item mb-1">
                <a class="nav-link py-3 nav-link-custom {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}" href="{{ route('admin.employees.index') }}">
                    <div class="icon icon-shape icon-sm border-radius-md bg-gray-100 text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-users text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Employees Management</span>
                </a>
            </li>
            @endif
            {{-- Reports (Admins only) --}}
            @if(in_array(auth()->user()->role, ['super_admin', 'admin', 'department_manager']))            <li class="nav-item mb-1">
                    <a class="nav-link py-3 nav-link-custom {{ request()->routeIs('reports') ? 'active' : '' }}" href="{{ route('reports') }}">
                        <div class="icon icon-shape icon-sm border-radius-md bg-gray-100 text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fas fa-file-alt text-dark text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Reports</span>
                    </a>
                </li>
            @endif

            {{-- Profile --}}
            <li class="nav-item mb-1">
                <a class="nav-link py-3 nav-link-custom {{ request()->routeIs('profile.show') ? 'active' : '' }}" href="{{ route('profile.show') }}">
                    <div class="icon icon-shape icon-sm border-radius-md bg-gray-100 text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-user text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Profile</span>
                </a>
            </li>

        </ul>
    </div>

<!-- الزر بعد تغيير الكلاس -->
<div class="sidenav-footer mx-3 pt-3 pb-4">
    <form method="POST" action="{{ route('logout') }}" class="mt-2">
        @csrf
        <button type="submit"class="w-100 custom-logout-btn" onclick="event.preventDefault(); this.closest('form').submit();">
            <i class="fas fa-sign-out-alt me-2"></i> Logout
        </button>
    </form>
</div>

</aside>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dropdownToggle = document.querySelector('.dropdown-toggle');
        const dropdownMenu = document.querySelector('.dropdown-menu');
        
        dropdownToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // حساب ارتفاع القائمة المنسدلة ديناميكياً
            const itemsHeight = dropdownMenu.querySelectorAll('li').length * 48; // 48px لكل عنصر
            
            if (dropdownMenu.classList.contains('show')) {
                dropdownMenu.style.maxHeight = '0';
                dropdownMenu.style.padding = '0';
                setTimeout(() => {
                    dropdownMenu.classList.remove('show');
                }, 300);
            } else {
                dropdownMenu.classList.add('show');
                dropdownMenu.style.maxHeight = itemsHeight + 'px';
                dropdownMenu.style.padding = '0.5rem 0';
            }
            
            this.setAttribute('aria-expanded', dropdownMenu.classList.contains('show'));
        });
        
        // إغلاق القائمة عند النقر خارجها
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.dropdown')) {
                dropdownMenu.style.maxHeight = '0';
                dropdownMenu.style.padding = '0';
                setTimeout(() => {
                    dropdownMenu.classList.remove('show');
                }, 300);
                dropdownToggle.setAttribute('aria-expanded', 'false');
            }
        });
        
        // منع إغلاق القائمة عند النقر على عناصرها
        dropdownMenu.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });

 

    </script>
