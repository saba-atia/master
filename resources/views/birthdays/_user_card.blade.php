{{-- _user_card.blade.php --}}
<div class="birthday-card {{ $today ? 'today' : '' }}">
    <div class="card-decoration"></div>
    
   <div class="user-avatar">
    @if($user->photo && Storage::disk('public')->exists($user->photo))
        <img src="{{ Storage::url($user->photo) }}" 
             alt="{{ $user->name }}" 
             class="avatar-image"
             onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.png') }}'">
    @else
        <div class="avatar-initials">
            {{ substr($user->name, 0, 1) }}
        </div>
    @endif
</div>
    
    <div class="user-details">
        <h3 class="user-name">{{ $user->name }}</h3>
        <p class="birthday-date">
            <i class="far fa-calendar-alt"></i> 
            {{ $user->birth_date->format('F jS') }}
            @if($today)
            <span class="age-bubble">{{ Carbon\Carbon::parse($user->birth_date)->age }} years</span>
            @endif
        </p>
        
        @if($today)
        <div class="user-actions">
            @if(auth()->check() && auth()->id() != $user->id)
            <button class="send-wish-btn" data-bs-toggle="modal" data-bs-target="#wishModal{{ $user->id }}">
                <i class="far fa-paper-plane"></i> Send Birthday Wish
            </button>
            @endif
        </div>
        @endif
    </div>
    
    @if($today && $user->birthdayWishes->isNotEmpty())
    <div class="recent-wishes">
        <p class="wishes-label">
            <i class="fas fa-quote-left"></i> Latest Wishes
        </p>
        @foreach($user->birthdayWishes->take(1) as $wish)
        <div class="wish-item">
            <div class="wish-sender">
                <span class="sender-avatar">{{ substr($wish->sender->name ?? 'U', 0, 1) }}</span>
                <strong>{{ $wish->sender->name ?? 'User' }}</strong>
            </div>
            <p class="wish-text">"{{ Str::limit($wish->message, 50) }}"</p>
        </div>
        @endforeach
    </div>
    @endif
</div>

@if($today && auth()->check() && auth()->id() != $user->id)
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
            <form action="{{ route('dash.birthdays.wish', $user) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Your Personal Message</label>
                        <textarea name="message" rows="4" placeholder="Write something special..." required></textarea>
                        <div class="suggestions">
                            <p class="suggestions-title">Quick Suggestions:</p>
                            <div class="suggestion-buttons">
                                @foreach($suggestedMessages as $message)
                                <button type="button" class="suggestion-btn">{{ $message }}</button>
                                @endforeach
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
@endif

<style>
    /* Main Dashboard */
    .birthday-dashboard {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem;
        font-family: 'Inter', -apple-system, sans-serif;
        background: #f9fafb;
    }
    
    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2.5rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .header-content {
        flex: 1;
    }
    
    .dashboard-title {
        font-size: 2rem;
        font-weight: 600;
        color: #111827;
        margin-bottom: 0.25rem;
        display: flex;
        align-items: center;
    }
    
    .title-icon {
        margin-right: 0.75rem;
        font-size: 1.5rem;
    }
    
    .dashboard-subtitle {
        color: #6b7280;
        font-size: 0.95rem;
    }
    
    .birthday-stats {
        display: flex;
        gap: 1.5rem;
        margin-left: 2rem;
    }
    
    .stat-bubble {
        padding: 1rem 1.5rem;
        border-radius: 1rem;
        text-align: center;
        font-weight: 500;
        position: relative;
        overflow: hidden;
        min-width: 90px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    
    .stat-bubble.today {
        background: linear-gradient(135deg, #fce7f3, #fbcfe8);
        color: #db2777;
    }
    
    .stat-bubble.week {
        background: linear-gradient(135deg, #e0e7ff, #c7d2fe);
        color: #4f46e5;
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
        background: #db2777;
    }
    
    .week .bubble-decoration {
        background: #4f46e5;
    }
    
    .stat-bubble .count {
        font-size: 1.5rem;
        font-weight: 700;
        display: block;
        line-height: 1;
    }
    
    .stat-bubble .label {
        font-size: 0.875rem;
        margin-top: 0.25rem;
        display: block;
    }
    
    /* Birthday Sections */
    .birthday-section {
        margin-bottom: 3rem;
    }
    
    .today-section {
        position: relative;
    }
    
    .confetti-effect {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 10px;
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10 10"><circle cx="5" cy="5" r="5" fill="%23db2777"/></svg>'),
                         url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10 10"><circle cx="5" cy="5" r="5" fill="%234f46e5"/></svg>');
        background-repeat: repeat-x;
        animation: confetti 10s linear infinite;
        opacity: 0.3;
    }
    
    .section-header {
        margin-bottom: 1.5rem;
    }
    
    .section-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #111827;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .text-gradient {
        background: linear-gradient(90deg, #db2777, #4f46e5);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
    }
    
    .birthday-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
    }
    
    /* Birthday Card */
    .birthday-card {
        background: white;
        border-radius: 1rem;
        padding: 1.75rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        border: 1px solid #e5e7eb;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .birthday-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
    }
    
    .birthday-card.today {
        border-top: 3px solid #f472b6;
        background: linear-gradient(to bottom, #fff, #fdf2f8);
    }
    
    .card-decoration {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, #f472b6, #818cf8);
    }
    
    .user-avatar {
        position: relative;
        margin-bottom: 1.25rem;
        display: flex;
        justify-content: center;
    }
    
    .avatar-image {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid white;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    .avatar-initials {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, #818cf8, #a78bfa);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: 500;
        border: 3px solid white;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    .today-badge {
        position: absolute;
        top: -5px;
        right: 10px;
        background: linear-gradient(135deg, #f472b6, #ec4899);
        color: white;
        font-size: 0.75rem;
        padding: 0.35rem 0.75rem;
        border-radius: 1rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
        box-shadow: 0 2px 4px rgba(219, 39, 119, 0.2);
    }
    
    .today-badge i {
        font-size: 0.6rem;
    }
    
    .user-details {
        margin-bottom: 1.25rem;
        text-align: center;
    }
    
    .user-name {
        font-size: 1.25rem;
        font-weight: 600;
        color: #111827;
        margin-bottom: 0.5rem;
    }
    
    .birthday-date {
        color: #6b7280;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }
    
    .age-bubble {
        background: #e0e7ff;
        color: #4f46e5;
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 1rem;
        margin-left: 0.5rem;
    }
    
    .user-actions {
        margin-top: 1.5rem;
    }
    
    .send-wish-btn {
        background: linear-gradient(135deg, #818cf8, #a78bfa);
        border: none;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-size: 0.95rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(129, 140, 248, 0.3);
        width: 100%;
        justify-content: center;
    }
    
    .send-wish-btn:hover {
        background: linear-gradient(135deg, #747cf0, #9061f9);
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(129, 140, 248, 0.3);
    }
    
    .recent-wishes {
        border-top: 1px solid #e5e7eb;
        padding-top: 1.25rem;
        margin-top: 1.25rem;
    }
    
    .wishes-label {
        color: #9ca3af;
        font-size: 0.8rem;
        text-transform: uppercase;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        justify-content: center;
    }
    
    .wish-item {
        background: #f9fafb;
        border-radius: 0.75rem;
        padding: 1rem;
    }
    
    .wish-sender {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 0.5rem;
    }
    
    .sender-avatar {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: #e0e7ff;
        color: #4f46e5;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .wish-sender strong {
        font-size: 0.9rem;
        color: #4b5563;
    }
    
    .wish-text {
        font-size: 0.9rem;
        color: #6b7280;
        font-style: italic;
        line-height: 1.4;
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem;
        background: white;
        border-radius: 1rem;
        border: 1px dashed #d1d5db;
        max-width: 500px;
        margin: 0 auto;
    }
    
    .empty-illustration {
        margin-bottom: 1.5rem;
        position: relative;
    }
    
    .calendar-icon {
        font-size: 3rem;
        color: #9ca3af;
        position: relative;
        display: inline-block;
    }
    
    .highlight-dot {
        position: absolute;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #f472b6;
        top: 5px;
        right: 5px;
    }
    
    .empty-state h3 {
        font-size: 1.5rem;
        color: #111827;
        margin-bottom: 0.5rem;
    }
    
    .refresh-btn {
        background: #f3f4f6;
        border: none;
        color: #4b5563;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        margin-top: 1.5rem;
        display: inline-flex;
        align-items: center;
        transition: all 0.2s;
    }
    
    .refresh-btn:hover {
        background: #e5e7eb;
    }
    
    /* Modal */
    .modal-content {
        border-radius: 1rem;
        overflow: hidden;
        border: none;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    
    .modal-header {
        padding: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        position: relative;
    }
    
    .modal-icon {
        position: absolute;
        top: -20px;
        left: 20px;
        background: linear-gradient(135deg, #f472b6, #ec4899);
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        box-shadow: 0 4px 6px rgba(244, 114, 182, 0.3);
    }
    
    .modal-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #111827;
        padding-left: 30px;
    }
    
    .close-btn {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: #9ca3af;
        cursor: pointer;
        transition: color 0.2s;
    }
    
    .close-btn:hover {
        color: #6b7280;
    }
    
    .modal-body {
        padding: 1.5rem;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 0.75rem;
        color: #374151;
        font-size: 0.95rem;
        font-weight: 500;
    }
    
    .form-group textarea {
        width: 100%;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        padding: 1rem;
        resize: none;
        min-height: 120px;
        transition: all 0.2s;
        font-family: 'Inter', sans-serif;
    }
    
    .form-group textarea:focus {
        outline: none;
        border-color: #a78bfa;
        box-shadow: 0 0 0 3px rgba(167, 139, 250, 0.2);
    }
    
    .suggestions {
        margin-top: 1rem;
    }
    
    .suggestions-title {
        color: #6b7280;
        font-size: 0.85rem;
        margin-bottom: 0.5rem;
    }
    
    .suggestion-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    
    .suggestion-btn {
        background: #f3f4f6;
        border: none;
        border-radius: 1rem;
        padding: 0.5rem 1rem;
        font-size: 0.85rem;
        color: #4b5563;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .suggestion-btn:hover {
        background: #e5e7eb;
    }
    
    .modal-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid #e5e7eb;
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
    }
    
    .cancel-btn {
        background: #f3f4f6;
        border: none;
        color: #4b5563;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s;
    }
    
    .cancel-btn:hover {
        background: #e5e7eb;
    }
    
    .submit-btn {
        background: linear-gradient(135deg, #818cf8, #a78bfa);
        border: none;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s;
        box-shadow: 0 2px 4px rgba(129, 140, 248, 0.3);
    }
    
    .submit-btn:hover {
        background: linear-gradient(135deg, #747cf0, #9061f9);
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
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Suggestion buttons
    document.querySelectorAll('.suggestion-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const textarea = this.closest('.modal-content').querySelector('textarea');
            textarea.value = this.textContent;
            textarea.focus();
        });
    });
    
    // Refresh button
    const refreshBtn = document.querySelector('.refresh-btn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Refreshing';
            setTimeout(() => {
                location.reload();
            }, 800);
        });
    }
});


document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.avatar-image').forEach(img => {
        img.onerror = function() {
            console.error('Failed to load image:', this.src);
            this.style.display = 'none';
            const initials = this.closest('.user-avatar').querySelector('.avatar-initials');
            if (initials) initials.style.display = 'flex';
        };
    });
});
</script>