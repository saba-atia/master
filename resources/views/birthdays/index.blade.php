@extends('dash.dash')

@section('contentdash')
<div class="birthday-dashboard">
    <!-- Header Section -->
    <div class="dashboard-header">
        <div class="header-content">
            <h1 class="dashboard-title text-gradient">
                <i class="fas fa-birthday-cake title-icon"></i>
                Birthday Celebrations
            </h1>
            <p class="dashboard-subtitle">Celebrate your team members' special days in style</p>
        </div>
        
        <!-- Stats -->
        <div class="birthday-stats">
            <div class="stat-bubble today">
                <span class="count">{{ $todayBirthdays->count() }}</span>
                <span class="label">Today</span>
                <div class="bubble-decoration"></div>
            </div>
            <div class="stat-bubble week">
                <span class="count">{{ $users->count() }}</span>
                <span class="label">This Week</span>
                <div class="bubble-decoration"></div>
            </div>
        </div>
    </div>

    @if($todayBirthdays->count())
    <!-- Today's Birthdays Section -->
    <section class="birthday-section today-section">
        <div class="confetti-effect"></div>
        
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-birthday-cake"></i>
                <span>Today's Birthdays</span>
            </h2>
        </div>
        
        <div class="birthday-grid">
            @foreach($todayBirthdays as $user)
                <div class="birthday-card today">
                    <div class="user-avatar-wrapper">
                        <div class="user-avatar">
                            @if($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path))
                                <img src="{{ Storage::url($user->profile_photo_path) }}" 
                                     alt="{{ $user->name }}" 
                                     class="avatar-image"
                                     onerror="this.onerror=null;this.nextElementSibling.style.display='flex';this.style.display='none'">
                                <div class="avatar-initials" style="display:none">
                                    @php
                                        $names = explode(' ', $user->name);
                                        echo strtoupper(substr($names[0] ?? '', 0, 1));
                                        if(isset($names[1])) {
                                            echo strtoupper(substr($names[1], 0, 1));
                                        }
                                    @endphp
                                </div>
                            @else
                                <div class="avatar-initials">
                                    @php
                                        $names = explode(' ', $user->name);
                                        echo strtoupper(substr($names[0] ?? '', 0, 1));
                                        if(isset($names[1])) {
                                            echo strtoupper(substr($names[1], 0, 1));
                                        }
                                    @endphp
                                </div>
                            @endif
                        </div>
                        <div class="today-badge">
                            <i class="fas fa-birthday-cake"></i> Today
                        </div>
                    </div>
                    
                    <div class="user-details">
                        <h3 class="user-name">{{ $user->name }}</h3>
                        <p class="birthday-date">
                            <i class="far fa-calendar-alt"></i> 
                            {{ $user->birth_date->format('F jS') }}
                            <span class="age-bubble">{{ $user->birth_date->age }} years</span>
                        </p>
                        
                        <div class="user-actions">
                            @if(auth()->check() && auth()->id() != $user->id)
                            <button class="send-wish-btn" data-bs-toggle="modal" data-bs-target="#wishModal{{ $user->id }}">
                                <i class="far fa-paper-plane"></i> Send Birthday Wish
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    @endif

    @if($users->count())
    <!-- Upcoming Birthdays Section -->
    <section class="birthday-section">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-calendar-days"></i>
                <span>Upcoming Birthdays This Week</span>
            </h2>
        </div>
        
        <div class="birthday-grid">
            @foreach($users as $user)
                @unless($user->birth_date->isBirthday())
                <div class="birthday-card">
                    <div class="user-avatar-wrapper">
                        <div class="user-avatar">
                            @if($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path))
                                <img src="{{ Storage::url($user->profile_photo_path) }}" 
                                     alt="{{ $user->name }}" 
                                     class="avatar-image"
                                     onerror="this.onerror=null;this.nextElementSibling.style.display='flex';this.style.display='none'">
                                <div class="avatar-initials" style="display:none">
                                    @php
                                        $names = explode(' ', $user->name);
                                        echo strtoupper(substr($names[0] ?? '', 0, 1));
                                        if(isset($names[1])) {
                                            echo strtoupper(substr($names[1], 0, 1));
                                        }
                                    @endphp
                                </div>
                            @else
                                <div class="avatar-initials">
                                    @php
                                        $names = explode(' ', $user->name);
                                        echo strtoupper(substr($names[0] ?? '', 0, 1));
                                        if(isset($names[1])) {
                                            echo strtoupper(substr($names[1], 0, 1));
                                        }
                                    @endphp
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="user-details">
                        <h3 class="user-name">{{ $user->name }}</h3>
                        <p class="birthday-date">
                            <i class="far fa-calendar-alt"></i> 
                            {{ $user->birth_date->format('F jS') }}
                        </p>
                    </div>
                </div>
                @endunless
            @endforeach
        </div>
    </section>
    @else
    <!-- Empty State -->
    <div class="empty-state">
        <div class="empty-illustration">
            <div class="calendar-icon animate-float">
                <div class="highlight-dot"></div>
            </div>
        </div>
        <h3>No birthdays this week</h3>
        <p>It seems quiet now, but celebrations are coming soon!</p>
        <button class="refresh-btn">
            <i class="fas fa-sync-alt"></i> Refresh
        </button>
    </div>
    @endif
</div>

<style>
    /* Main Container */
    .birthday-dashboard {
        font-family: 'Inter', sans-serif;
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }
    
    /* Header Styles */
    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid #eee;
    }
    
    .dashboard-title {
        font-size: 2.5rem;
        background: linear-gradient(to right, #0765d9, #8be2fa);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        margin-bottom: 10px;
    }
    
    .dashboard-subtitle {
        color: #666;
        font-size: 1.1rem;
    }
    
    /* Stats Bubbles */
    .birthday-stats {
        display: flex;
        gap: 20px;
    }
    
    .stat-bubble {
        padding: 20px;
        border-radius: 15px;
        text-align: center;
        min-width: 100px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        position: relative;
        overflow: hidden;
    }
    
    .stat-bubble.today {
        background: linear-gradient(135deg, #ff7e5f, #feb47b);
        color: white;
    }
    
    .stat-bubble.week {
        background: linear-gradient(135deg, #8be2fa, #0765d9);
        color: white;
    }
    
    .stat-bubble .count {
        font-size: 2rem;
        font-weight: bold;
        display: block;
    }
    
    .stat-bubble .label {
        font-size: 1rem;
    }
    
    /* Cards Section */
    .birthday-section {
        margin-bottom: 40px;
    }
    
    .today-section {
        background: rgba(255, 126, 95, 0.05);
        padding: 20px;
        border-radius: 15px;
    }
    
    .section-header {
        margin-bottom: 20px;
    }
    
    .section-title {
        font-size: 1.8rem;
        color: #333;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .birthday-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
    }
    
    /* Card Styles */
    .birthday-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }
    
    .birthday-card:hover {
        transform: translateY(-5px);
    }
    
    .birthday-card.today {
        background: linear-gradient(135deg, #ff7e5f, #feb47b);
        color: white;
    }
    
    /* Avatar Styles */
    .user-avatar-wrapper {
        position: relative;
        margin-bottom: 15px;
        display: flex;
        justify-content: center;
    }
    
    .user-avatar {
        position: relative;
    }
    
    .avatar-image {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid white;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        display: block;
    }
    
    .avatar-initials {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: linear-gradient(135deg, #0765d9, #8be2fa);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: bold;
        border: 3px solid white;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        text-transform: uppercase;
    }
    
    .today-badge {
        position: absolute;
        top: 0;
        right: 0;
        background: white;
        color: #ff7e5f;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: bold;
    }
    
    /* User Details */
    .user-details {
        text-align: center;
    }
    
    .user-name {
        font-size: 1.3rem;
        font-weight: bold;
        margin-bottom: 10px;
    }
    
    .birthday-date {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        margin-bottom: 15px;
    }
    
    .age-bubble {
        background: rgba(255,255,255,0.2);
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 0.9rem;
    }
    
    /* Buttons */
    .user-actions {
        margin-top: 15px;
    }
    
    .send-wish-btn {
        background: white;
        color: #ff7e5f;
        border: none;
        padding: 10px 20px;
        border-radius: 20px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s ease;
        width: 100%;
    }
    
    .birthday-card:not(.today) .send-wish-btn {
        background: #0765d9;
        color: white;
    }
    
    .send-wish-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 40px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .calendar-icon {
        font-size: 3rem;
        color: #0765d9;
        margin-bottom: 20px;
    }
    
    .empty-state h3 {
        font-size: 1.5rem;
        margin-bottom: 10px;
    }
    
    .empty-state p {
        color: #666;
        margin-bottom: 20px;
    }
    
    .refresh-btn {
        background: #0765d9;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 20px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .refresh-btn:hover {
        background: #054da3;
    }
    
    /* Animations */
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
    
    .animate-float {
        animation: float 3s ease-in-out infinite;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .dashboard-header {
            flex-direction: column;
            text-align: center;
            gap: 20px;
        }
        
        .birthday-stats {
            width: 100%;
            justify-content: center;
        }
        
        .birthday-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle avatar image errors
    document.querySelectorAll('.avatar-image').forEach(img => {
        img.onerror = function() {
            const initials = this.nextElementSibling;
            if (initials) {
                initials.style.display = 'flex';
                this.style.display = 'none';
            }
        };
    });

    // Refresh button
    const refreshBtn = document.querySelector('.refresh-btn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Refreshing';
            setTimeout(() => {
                location.reload();
            }, 800);
        });
    }
});
</script>
@endsection