<style>
    .nav-link-custom {
        border-radius: 6px;
        margin: 0 8px;
        transition: all 0.3s ease;
        color: #5e5873;
    }
    .nav-link-custom.active {
        background-color: #f6f6f6;
        font-weight: bold;
    }
</style>

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

            {{-- Leave Requests --}}
            <li class="nav-item mb-1">
                <a class="nav-link py-3 nav-link-custom {{ request()->routeIs('leave.index') ? 'active' : '' }}" href="{{ route('leave.index') }}">
                    <div class="icon icon-shape icon-sm border-radius-md bg-gray-100 text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-plane-departure text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Leave Requests</span>
                </a>
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
            @if(in_array(auth()->user()->role, ['admin', 'super_admin']))
                <li class="nav-item mb-1">
                    <a class="nav-link py-3 nav-link-custom {{ request()->routeIs('evaluations') ? 'active' : '' }}" href="{{ route('evaluations') }}">
                        <div class="icon icon-shape icon-sm border-radius-md bg-gray-100 text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fas fa-star text-dark text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Evaluations</span>
                    </a>
                </li>
            @endif

            {{-- Reports (Admins only) --}}
            @if(in_array(auth()->user()->role, ['admin', 'super_admin']))
                <li class="nav-item mb-1">
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

    <div class="sidenav-footer mx-3 pt-3 pb-4">
        <form method="POST" action="{{ route('logout') }}" class="mt-2">
            @csrf
            <button type="submit" class="btn w-100 d-flex align-items-center justify-content-center logout-btn">
                <i class="fas fa-sign-out-alt me-2"></i> Logout
            </button>
        </form>
    </div>
</aside>
