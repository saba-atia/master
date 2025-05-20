@extends('dash.dash')
@section('title','Birthday ')
@section('contentdash')
@php
    if (!function_exists('getInitials')) {
        function getInitials($name) {
            $names = explode(' ', $name);
            $initials = '';

            if (count($names)) {
                $initials .= strtoupper(substr($names[0], 0, 1));
            }

            if (count($names) > 1) {
                $initials .= strtoupper(substr(end($names), 0, 1));
            }

            return $initials;
        }
    }
@endphp
<div class="birthday-dashboard">
    <!-- Header Section -->
    <div class="dashboard-header">
        <div class="header-content">
            <h1 class="dashboard-title text-gradient">
                <i class="fas fa-birthday-cake title-icon"></i>
                Birthday Celebrations
            </h1>
            <p class="dashboard-subtitle">Celebrate your team members' special days</p>
        </div>
        
        <!-- Stats -->
        <div class="birthday-stats">
            <div class="stat-bubble today">
                <span class="count">{{ $todayBirthdays->count() }}</span>
                <span class="label">Today</span>
                <div class="bubble-decoration"></div>
            </div>
            <div class="stat-bubble week">
                <span class="count">{{ $upcomingBirthdays->count() }}</span>
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
                            @if($user->profile_photo_path)
                                <img src="{{ Storage::url($user->profile_photo_path) }}" 
                                     alt="{{ $user->name }}" 
                                     class="avatar-image"
                                     onerror="this.onerror=null;this.style.display='none';this.nextElementSibling.style.display='flex'">
                                <div class="avatar-initials" style="display:none">
                                    {{ getInitials($user->name) }}
                                </div>
                            @else
                                <div class="avatar-initials">
                                    {{ getInitials($user->name) }}
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

                    <!-- Received Wishes Section -->
                    @if($user->receivedWishes->count() > 0)
                    <div class="wishes-container">
                        <h4 class="wishes-title">
                            <i class="fas fa-gift"></i> Received Wishes ({{ $user->receivedWishes->count() }})
                        </h4>
                        <div class="wishes-list">
                            @foreach($user->receivedWishes->take(3) as $wish)
                            <div class="wish-item">
                                <div class="wisher-avatar">
                                    @if($wish->sender->profile_photo_path)
                                        <img src="{{ Storage::url($wish->sender->profile_photo_path) }}" 
                                             alt="{{ $wish->sender->name }}"
                                             onerror="this.onerror=null;this.style.display='none';this.nextElementSibling.style.display='flex'">
                                        <div class="avatar-initials" style="display:none">
                                            {{ getInitials($wish->sender->name) }}
                                        </div>
                                    @else
                                        <div class="avatar-initials">
                                            {{ getInitials($wish->sender->name) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="wish-details">
                                    <strong>{{ $wish->sender->name }}</strong>
                                    <p class="wish-text">"{{ Str::limit($wish->message, 50) }}"</p>
                                    <small class="wish-time">{{ $wish->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                            @endforeach
                            
                            @if($user->receivedWishes->count() > 3)
                            <div class="view-all-wishes">
                                <a href="#" data-bs-toggle="modal" data-bs-target="#allWishesModal{{ $user->id }}">
                                    View all wishes ({{ $user->receivedWishes->count() }})
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Wish Modal -->
                <div class="modal fade" id="wishModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <div class="modal-icon">
                                    <i class="fas fa-birthday-cake"></i>
                                </div>
                                <h5 class="modal-title">
                                    Send Birthday Wishes to {{ $user->name }}
                                </h5>
                                <button type="button" class="close-btn" data-bs-dismiss="modal">
                                    &times;
                                </button>
                            </div>
                            <form action="{{ route('birthdays.wish', $user->id) }}" method="POST">
                                @csrf
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Your Personal Message</label>
                                        <textarea name="message" rows="4" placeholder="Write something special..." required></textarea>
                                        <div class="suggestions">
                                            <p class="suggestions-title">Quick Suggestions:</p>
                                            <div class="suggestion-buttons">
                                                <button type="button" class="suggestion-btn">Happy Birthday! Wishing you all the best!</button>
                                                <button type="button" class="suggestion-btn">Wishing you a fantastic birthday and a wonderful year ahead!</button>
                                                <button type="button" class="suggestion-btn">Happy Birthday! Enjoy your special day!</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="cancel-btn" data-bs-dismiss="modal">
                                        <i class="far fa-times-circle"></i> Cancel
                                    </button>
                                    <button type="submit" class="submit-btn">
                                        <i class="far fa-paper-plane"></i> Send Wish
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- All Wishes Modal -->
                <div class="modal fade" id="allWishesModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    <i class="fas fa-gift"></i> All Birthday Wishes for {{ $user->name }}
                                </h5>
                                <button type="button" class="close-btn" data-bs-dismiss="modal">
                                    &times;
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="all-wishes-list">
                                    @foreach($user->receivedWishes as $wish)
                                    <div class="wish-item">
                                        <div class="wisher-avatar">
                                            @if($wish->sender->profile_photo_path)
                                                <img src="{{ Storage::url($wish->sender->profile_photo_path) }}" 
                                                     alt="{{ $wish->sender->name }}"
                                                     onerror="this.onerror=null;this.style.display='none';this.nextElementSibling.style.display='flex'">
                                                <div class="avatar-initials" style="display:none">
                                                    {{ getInitials($wish->sender->name) }}
                                                </div>
                                            @else
                                                <div class="avatar-initials">
                                                    {{ getInitials($wish->sender->name) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="wish-details">
                                            <div class="wish-header">
                                                <strong>{{ $wish->sender->name }}</strong>
                                                <small class="wish-time">{{ $wish->created_at->format('M j, Y \a\t g:i a') }}</small>
                                            </div>
                                            <p class="wish-text">{{ $wish->message }}</p>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    @endif

    @if($upcomingBirthdays->count())
    <!-- Upcoming Birthdays Section -->
    <section class="birthday-section">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-calendar-days"></i>
                <span>Upcoming Birthdays This Week</span>
            </h2>
        </div>
        
        <div class="birthday-grid">
            @foreach($upcomingBirthdays as $user)
                <div class="birthday-card">
                    <div class="user-avatar-wrapper">
                        <div class="user-avatar">
                            @if($user->profile_photo_path)
                                <img src="{{ Storage::url($user->profile_photo_path) }}" 
                                     alt="{{ $user->name }}" 
                                     class="avatar-image"
                                     onerror="this.onerror=null;this.style.display='none';this.nextElementSibling.style.display='flex'">
                                <div class="avatar-initials" style="display:none">
                                    {{ getInitials($user->name) }}
                                </div>
                            @else
                                <div class="avatar-initials">
                                    {{ getInitials($user->name) }}
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="user-details">
                        <h3 class="user-name">{{ $user->name }}</h3>
                        <p class="birthday-date">
                            <i class="far fa-calendar-alt"></i> 
                            {{ $user->birth_date->format('F jS') }}
                            ({{ $user->birth_date->format('l') }})
                        </p>
                    </div>
                </div>
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
        <button class="refresh-btn" onclick="window.location.reload()">
            <i class="fas fa-sync-alt"></i> Refresh
        </button>
    </div>
    @endif
</div>

<style>
    /* Main Container */
    .birthday-dashboard {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
        color: #333;
    }
    
    /* Header Styles */
    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .dashboard-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
    }
    
    .text-gradient {
        background: linear-gradient(90deg, #FF6B6B, #4ECDC4);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
    }
    
    .title-icon {
        margin-right: 15px;
        font-size: 1.8rem;
    }
    
    .dashboard-subtitle {
        color: #6b7280;
        font-size: 1.1rem;
        margin-top: -5px;
    }
    
    /* Stats Bubbles */
    .birthday-stats {
        display: flex;
        gap: 20px;
    }
    
    .stat-bubble {
        padding: 20px 25px;
        border-radius: 15px;
        text-align: center;
        min-width: 100px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        position: relative;
        overflow: hidden;
        transition: transform 0.3s ease;
    }
    
    .stat-bubble:hover {
        transform: translateY(-3px);
    }
    
    .stat-bubble.today {
        background: linear-gradient(135deg, #FF9A9E 0%, #FAD0C4 100%);
        color: white;
    }
    
    .stat-bubble.week {
        background: linear-gradient(135deg, #A1C4FD 0%, #C2E9FB 100%);
        color: white;
    }
    
    .bubble-decoration {
        position: absolute;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        opacity: 0.1;
        top: -20px;
        right: -20px;
    }
    
    .today .bubble-decoration {
        background: #FF6B6B;
    }
    
    .week .bubble-decoration {
        background: #4ECDC4;
    }
    
    .stat-bubble .count {
        font-size: 2rem;
        font-weight: 700;
        display: block;
        line-height: 1;
    }
    
    .stat-bubble .label {
        font-size: 0.9rem;
        opacity: 0.9;
    }
    
    /* Sections */
    .birthday-section {
        margin-bottom: 40px;
    }
    
    .today-section {
        position: relative;
        background: rgba(255, 154, 158, 0.05);
        padding: 20px;
        border-radius: 15px;
        border: 1px solid rgba(255, 154, 158, 0.2);
    }
    
    .confetti-effect {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 10px;
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10 10"><circle cx="5" cy="5" r="5" fill="%23FF6B6B"/></svg>'),
                         url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10 10"><circle cx="5" cy="5" r="5" fill="%234ECDC4"/></svg>');
        background-repeat: repeat-x;
        animation: confetti 10s linear infinite;
        opacity: 0.3;
    }
    
    .section-header {
        margin-bottom: 20px;
    }
    
    .section-title {
        font-size: 1.8rem;
        font-weight: 600;
        color: #333;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .section-title i {
        color: #FF6B6B;
    }
    
    /* Birthday Grid */
    .birthday-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
    }
    
    /* Birthday Card */
    .birthday-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        border: 1px solid #e5e7eb;
    }
    
    .birthday-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
    }
    
    .birthday-card.today {
        border-top: 4px solid #FF6B6B;
        background: linear-gradient(to bottom, #fff, #FFF5F5);
    }
    
    /* User Avatar */
    .user-avatar-wrapper {
        position: relative;
        margin-bottom: 20px;
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
        background: linear-gradient(135deg, #4ECDC4, #A1C4FD);
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
        top: -5px;
        right: 0;
        background: #FF6B6B;
        color: white;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: bold;
        box-shadow: 0 2px 5px rgba(255,107,107,0.3);
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    /* User Details */
    .user-details {
        text-align: center;
        margin-bottom: 15px;
    }
    
    .user-name {
        font-size: 1.4rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
    }
    
    .birthday-date {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        color: #6b7280;
        font-size: 0.95rem;
        margin-bottom: 15px;
    }
    
    .age-bubble {
        background: rgba(78, 205, 196, 0.2);
        color: #4ECDC4;
        font-size: 0.8rem;
        padding: 3px 10px;
        border-radius: 20px;
        margin-left: 8px;
        font-weight: 600;
    }
    
    .birthday-card.today .age-bubble {
        background: rgba(255, 255, 255, 0.3);
        color: white;
    }
    
    /* Buttons */
    .user-actions {
        margin-top: 20px;
    }
    
    .send-wish-btn {
        background: linear-gradient(135deg, #4ECDC4, #A1C4FD);
        border: none;
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .send-wish-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    /* Wishes Container */
    .wishes-container {
        border-top: 1px solid #f0f0f0;
        padding-top: 15px;
        margin-top: 20px;
    }
    
    .wishes-title {
        font-size: 1rem;
        color: #6b7280;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .wishes-list {
        max-height: 200px;
        overflow-y: auto;
        padding-right: 5px;
    }
    
    .wish-item {
        display: flex;
        gap: 12px;
        padding: 10px 0;
        border-bottom: 1px solid #f5f5f5;
    }
    
    .wish-item:last-child {
        border-bottom: none;
    }
    
    .wisher-avatar {
        flex-shrink: 0;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        overflow: hidden;
    }
    
    .wisher-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .wisher-avatar .avatar-initials {
        width: 100%;
        height: 100%;
        font-size: 1rem;
        background: linear-gradient(135deg, #FF9A9E, #FAD0C4);
    }
    
    .wish-details {
        flex: 1;
    }
    
    .wish-details strong {
        display: block;
        font-size: 0.95rem;
        color: #333;
        margin-bottom: 3px;
    }
    
    .wish-text {
        font-size: 0.9rem;
        color: #6b7280;
        margin: 0;
        line-height: 1.4;
    }
    
    .wish-time {
        font-size: 0.75rem;
        color: #9CA3AF;
        display: block;
        margin-top: 3px;
    }
    
    .view-all-wishes {
        text-align: center;
        margin-top: 10px;
    }
    
    .view-all-wishes a {
        color: #4ECDC4;
        font-size: 0.85rem;
        text-decoration: none;
        font-weight: 500;
    }
    
    .view-all-wishes a:hover {
        text-decoration: underline;
    }
    
    /* All Wishes Modal */
    .all-wishes-list {
        max-height: 60vh;
        overflow-y: auto;
        padding-right: 10px;
    }
    
    .all-wishes-list .wish-item {
        padding: 15px;
        border-radius: 8px;
        transition: background 0.2s;
    }
    
    .all-wishes-list .wish-item:hover {
        background: #f9f9f9;
    }
    
    .wish-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 5px;
    }
    
    /* Modal Styles */
    .modal-content {
        border-radius: 12px;
        overflow: hidden;
        border: none;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    
    .modal-header {
        padding: 20px;
        border-bottom: 1px solid #f0f0f0;
        position: relative;
    }
    
    .modal-icon {
        position: absolute;
        top: -20px;
        left: 20px;
        background: linear-gradient(135deg, #FF6B6B, #FF9A9E);
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        box-shadow: 0 4px 6px rgba(255,107,107,0.3);
    }
    
    .modal-title {
        font-size: 1.3rem;
        font-weight: 600;
        color: #333;
        padding-left: 30px;
    }
    
    .close-btn {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: #9CA3AF;
        cursor: pointer;
        transition: color 0.2s;
    }
    
    .close-btn:hover {
        color: #6B7280;
    }
    
    .modal-body {
        padding: 20px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 10px;
        color: #374151;
        font-weight: 500;
    }
    
    .form-group textarea {
        width: 100%;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 12px;
        resize: none;
        min-height: 120px;
        transition: all 0.2s;
        font-family: 'Inter', sans-serif;
    }
    
    .form-group textarea:focus {
        outline: none;
        border-color: #A1C4FD;
        box-shadow: 0 0 0 3px rgba(161, 196, 253, 0.2);
    }
    
    .suggestions {
        margin-top: 15px;
    }
    
    .suggestions-title {
        color: #6B7280;
        font-size: 0.85rem;
        margin-bottom: 8px;
    }
    
    .suggestion-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    
    .suggestion-btn {
        background: #F3F4F6;
        border: none;
        border-radius: 6px;
        padding: 8px 12px;
        font-size: 0.85rem;
        color: #4B5563;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .suggestion-btn:hover {
        background: #E5E7EB;
    }
    
    .modal-footer {
        padding: 15px 20px;
        border-top: 1px solid #f0f0f0;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }
    
    .cancel-btn {
        background: #F3F4F6;
        border: none;
        color: #4B5563;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .cancel-btn:hover {
        background: #E5E7EB;
    }
    
    .submit-btn {
        background: linear-gradient(135deg, #4ECDC4, #A1C4FD);
        border: none;
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .submit-btn:hover {
        background: linear-gradient(135deg, #3DBDB4, #91B4FD);
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 40px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        max-width: 500px;
        margin: 0 auto;
    }
    
    .empty-illustration {
        margin-bottom: 20px;
    }
    
    .calendar-icon {
        font-size: 3rem;
        color: #A1C4FD;
        position: relative;
        display: inline-block;
    }
    
    .highlight-dot {
        position: absolute;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #FF6B6B;
        top: 5px;
        right: 5px;
    }
    
    .empty-state h3 {
        font-size: 1.5rem;
        color: #333;
        margin-bottom: 10px;
    }
    
    .empty-state p {
        color: #6B7280;
        margin-bottom: 20px;
    }
    
    .refresh-btn {
        background: #F3F4F6;
        border: none;
        color: #4B5563;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .refresh-btn:hover {
        background: #E5E7EB;
    }
    
    /* Animations */
    @keyframes confetti {
        0% { background-position: 0 0, 20px 0, 40px 0; }
        100% { background-position: 100px 0, 120px 0, 140px 0; }
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
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
        
        .modal-dialog {
            margin: 10px;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle suggestion buttons
    document.querySelectorAll('.suggestion-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const modal = this.closest('.modal-content');
            const textarea = modal.querySelector('textarea');
            textarea.value = this.textContent;
            textarea.focus();
        });
    });
    
    // Handle avatar image errors
    document.querySelectorAll('.avatar-image').forEach(img => {
        img.onerror = function() {
            this.style.display = 'none';
            const initials = this.nextElementSibling;
            if (initials) initials.style.display = 'flex';
        };
    });
    
    // Confetti effect for today's birthdays
    if (document.querySelector('.today-section')) {
        setTimeout(() => {
            const confettiSettings = {
                target: 'confetti-canvas',
                max: 150,
                size: 1.5,
                animate: true,
                colors: [[255, 107, 107], [78, 205, 196], [255, 154, 158], [161, 196, 253]],
                clock: 25,
                rotate: true,
                start_from_edge: true,
                respawn: true
            };
            
            const confetti = new ConfettiGenerator(confettiSettings);
            confetti.render();
            
            // Stop after 5 seconds
            setTimeout(() => confetti.clear(), 5000);
        }, 1000);
    }
});

// Helper function to get initials
function getInitials(name) {
    const names = name.split(' ');
    let initials = names[0].substring(0, 1).toUpperCase();
    
    if (names.length > 1) {
        initials += names[names.length - 1].substring(0, 1).toUpperCase();
    }
    
    return initials;
}
</script>

<!-- Add Confetti JS library -->
@endsection