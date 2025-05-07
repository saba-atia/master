@extends('dash.dash')
@section('title', 'Birthdays Dashboard')
@section('contentdash')

<style>
    .birthday-container {
        font-family: 'Arial', sans-serif;
        margin: 20px auto;
        max-width: 1400px;
        padding: 25px;
        border-radius: 12px;
        background: #ffffff;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }
    
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eaeaea;
    }
    
    .section-title {
        font-size: 1.5rem;
        color: #2c3e50;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .employee-view, .calendar-view, .full-access-view {
        background-color: #f8fafc;
        padding: 25px;
        border-radius: 12px;
        margin-bottom: 30px;
        border: 1px solid #e2e8f0;
    }
    
    .birthday-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin: 15px 0;
        box-shadow: 0 3px 10px rgba(0,0,0,0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: transform 0.2s, box-shadow 0.2s;
        border-left: 4px solid #4CAF50;
    }
    
    .birthday-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .birthday-card .employee-info h4 {
        margin: 0;
        color: #2d3748;
        font-size: 1.1rem;
    }
    
    .birthday-card .employee-info p {
        margin: 5px 0 0;
        color: #718096;
        font-size: 0.9rem;
    }
    
    .birthday-card button {
        background-color: #4CAF50;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.3s;
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 500;
    }
    
    .birthday-card button:hover {
        background-color: #3d8b40;
    }
    
    .calendar {
        width: 100%;
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.05);
    }
    
    .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .calendar-title {
        font-size: 1.3rem;
        color: #2c3e50;
        font-weight: 600;
    }
    
    .calendar-nav-btn {
        background-color: #4CAF50;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: background-color 0.3s;
    }
    
    .calendar-nav-btn:hover {
        background-color: #3d8b40;
    }
    
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 12px;
    }
    
    .calendar-weekdays {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 12px;
        margin-bottom: 10px;
        text-align: center;
        font-weight: 600;
        color: #4a5568;
    }
    
    .calendar-day {
        border: 1px solid #e2e8f0;
        padding: 12px;
        min-height: 120px;
        position: relative;
        border-radius: 8px;
        background: white;
        transition: all 0.2s;
    }
    
    .calendar-day:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .calendar-day.empty {
        background-color: #f8fafc;
        border: 1px dashed #e2e8f0;
    }
    
    .calendar-day.today {
        background-color: #ebf8ff;
        border: 2px solid #3182ce;
    }
    
    .calendar-day.has-birthday {
        background-color: #fffaf0;
        border: 1px solid #f6ad55;
        cursor: pointer;
    }
    
    .calendar-day-number {
        position: absolute;
        top: 8px;
        right: 8px;
        font-weight: bold;
        color: #4a5568;
    }
    
    .birthday-badge {
        position: absolute;
        top: 5px;
        left: 5px;
        background-color: #f6ad55;
        color: white;
        border-radius: 50%;
        width: 22px;
        height: 22px;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 12px;
        font-weight: bold;
    }
    
    .birthday-list-item {
        font-size: 0.8rem;
        margin-top: 8px;
        padding: 4px 8px;
        background: rgba(246, 173, 85, 0.1);
        border-radius: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .modal {
        display: none;
        position: fixed;
        z-index: 1050;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.5);
        backdrop-filter: blur(3px);
    }
    
    .modal-content {
        background-color: #fefefe;
        margin: 10% auto;
        padding: 25px;
        border-radius: 12px;
        width: 90%;
        max-width: 650px;
        box-shadow: 0 5px 25px rgba(0,0,0,0.2);
        position: relative;
        animation: modalopen 0.3s;
    }
    
    @keyframes modalopen {
        from {opacity: 0; transform: translateY(-20px);}
        to {opacity: 1; transform: translateY(0);}
    }
    
    .close {
        color: #aaa;
        position: absolute;
        top: 15px;
        right: 20px;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
        transition: color 0.2s;
    }
    
    .close:hover {
        color: #333;
    }
    
    .filter-section {
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
        padding: 15px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    
    .filter-section label {
        font-weight: 600;
        color: #4a5568;
        margin-right: 8px;
    }
    
    .filter-section select, .filter-section input {
        padding: 8px 12px;
        border-radius: 6px;
        border: 1px solid #ced4da;
        min-width: 200px;
    }
    
    .birthday-list {
        margin-top: 15px;
    }
    
    .birthday-item {
        padding: 12px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: background-color 0.2s;
    }
    
    .birthday-item:hover {
        background-color: #f8f9fa;
    }
    
    .wish-form {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #eee;
    }
    
    .wish-form textarea {
        width: 100%;
        padding: 12px;
        border-radius: 6px;
        border: 1px solid #ced4da;
        margin-bottom: 15px;
        min-height: 100px;
        resize: vertical;
    }
    
    .alert-section {
        background-color: #fff3cd;
        padding: 25px;
        border-radius: 10px;
        text-align: center;
        margin: 20px 0;
    }
    
    .no-birthdays {
        text-align: center;
        padding: 30px;
        color: #718096;
    }
    
    .birthday-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #4a5568;
        font-weight: bold;
        margin-right: 15px;
    }
    
    .employee-details {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }
    
    .employee-details-info h4 {
        margin: 0;
        color: #2d3748;
    }
    
    .employee-details-info p {
        margin: 5px 0 0;
        color: #718096;
    }
    
    .birthday-stats {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 25px;
    }
    
    .stat-card {
        background: white;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        text-align: center;
    }
    
    .stat-card h3 {
        margin: 0;
        font-size: 2rem;
        color: #4CAF50;
    }
    
    .stat-card p {
        margin: 5px 0 0;
        color: #718096;
        font-size: 0.9rem;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .birthday-card {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }
        
        .calendar-grid {
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
        }
        
        .calendar-day {
            min-height: 80px;
            padding: 5px;
        }
        
        .birthday-list-item {
            font-size: 0.7rem;
        }
    }
</style>

<div class="birthday-container">
    @if(auth()->user()->role === 'employee')
        <div class="employee-view">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-birthday-cake"></i> Today's Birthdays
                </h2>
                <div class="birthday-stats">
                    <div class="stat-card">
                        <h3>{{ $todayBirthdays->count() }}</h3>
                        <p>Celebrating Today</p>
                    </div>
                    <div class="stat-card">
                        <h3>{{ $upcomingBirthdays->count() }}</h3>
                        <p>Upcoming This Month</p>
                    </div>
                </div>
            </div>
            
            @if($todayBirthdays->count() > 0)
                @foreach($todayBirthdays as $employee)
                    <div class="birthday-card">
                        <div class="employee-info">
                            <h4>{{ $employee->name }}</h4>
                            <p><i class="far fa-calendar-alt"></i> Birthday: {{ $employee->birth_date->format('Y-m-d') }} (Today)</p>
                            <p><i class="fas fa-briefcase"></i> {{ $employee->position }} - {{ $employee->department->name ?? 'No Department' }}</p>
                        </div>
                        <button onclick="sendWishes({{ $employee->id }})">
                            <i class="far fa-paper-plane"></i> Send Wishes
                        </button>
                    </div>
                @endforeach
            @else
                <div class="no-birthdays">
                    <i class="fas fa-birthday-cake fa-2x" style="color: #e2e8f0; margin-bottom: 15px;"></i>
                    <h3>No Birthdays Today</h3>
                    <p>There are no employees celebrating their birthday today.</p>
                    @if($upcomingBirthdays->count() > 0)
                        <button class="calendar-nav-btn" style="margin-top: 15px;" onclick="showUpcomingBirthdays()">
                            <i class="fas fa-calendar-alt"></i> View Upcoming Birthdays
                        </button>
                    @endif
                </div>
            @endif
        </div>

    @elseif(auth()->user()->role === 'department_head')
        <div class="calendar-view">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-calendar-alt"></i> Department Birthdays Calendar
                </h2>
                <div>
                    <button class="calendar-nav-btn" id="export-btn">
                        <i class="fas fa-download"></i> Export
                    </button>
                </div>
            </div>
            
            <div class="calendar">
                <div class="calendar-header">
                    <button class="calendar-nav-btn" id="prev-month">
                        <i class="fas fa-chevron-left"></i> Previous Month
                    </button>
                    <h3 class="calendar-title" id="current-month">{{ now()->translatedFormat('F Y') }}</h3>
                    <button class="calendar-nav-btn" id="next-month">
                        Next Month <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
                
                <div class="calendar-weekdays">
                    <div>Sun</div>
                    <div>Mon</div>
                    <div>Tue</div>
                    <div>Wed</div>
                    <div>Thu</div>
                    <div>Fri</div>
                    <div>Sat</div>
                </div>
                
                <div class="calendar-grid" id="calendar-grid">
                    <!-- Calendar days will be populated by JavaScript -->
                </div>
            </div>
            
            @if($upcomingBirthdays->count() > 0)
                <div style="margin-top: 30px;">
                    <h3 class="section-title">
                        <i class="fas fa-calendar-week"></i> Upcoming Department Birthdays
                    </h3>
                    <div class="birthday-list">
                        @foreach($upcomingBirthdays as $employee)
                            <div class="birthday-item">
                                <div>
                                    <strong>{{ $employee->name }}</strong> - {{ $employee->position }}
                                    <div style="color: #718096; font-size: 0.9rem;">
                                        <i class="far fa-calendar-alt"></i> 
                                        {{ $employee->birth_date->format('F j') }} 
                                        (in {{ $employee->birth_date->diffInDays(now()) }} days)
                                    </div>
                                </div>
                                <button onclick="sendWishesPrompt({{ $employee->id }}, '{{ $employee->name }}')" class="calendar-nav-btn" style="padding: 5px 10px;">
                                    <i class="far fa-paper-plane"></i> Send Wishes
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

    @elseif(in_array(auth()->user()->role, ['admin', 'super_admin']))
        <div class="full-access-view">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-birthday-cake"></i> Birthday Management
                </h2>
                <div>
                    <button class="calendar-nav-btn" id="export-btn">
                        <i class="fas fa-download"></i> Export
                    </button>
                </div>
            </div>
            
            <div class="filter-section">
                <div>
                    <label for="date-filter"><i class="far fa-calendar-alt"></i> Month:</label>
                    <input type="month" id="date-filter" value="{{ now()->format('Y-m') }}">
                </div>
                <div>
                    <label for="department-filter"><i class="fas fa-building"></i> Department:</label>
                    <select id="department-filter">
                        <option value="all">All Departments</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="search"><i class="fas fa-search"></i> Search:</label>
                    <input type="text" id="search" placeholder="Employee name...">
                </div>
            </div>
            
            <div class="birthday-stats">
                <div class="stat-card">
                    <h3>{{ $totalEmployees }}</h3>
                    <p>Total Employees</p>
                </div>
                <div class="stat-card">
                    <h3>{{ $birthdaysThisMonth }}</h3>
                    <p>This Month</p>
                </div>
                <div class="stat-card">
                    <h3>{{ $birthdaysToday }}</h3>
                    <p>Today</p>
                </div>
                <div class="stat-card">
                    <h3>{{ $birthdaysNextMonth }}</h3>
                    <p>Next Month</p>
                </div>
            </div>
            
            <div class="calendar">
                <div class="calendar-header">
                    <button class="calendar-nav-btn" id="prev-month">
                        <i class="fas fa-chevron-left"></i> Previous Month
                    </button>
                    <h3 class="calendar-title" id="current-month">{{ now()->translatedFormat('F Y') }}</h3>
                    <button class="calendar-nav-btn" id="next-month">
                        Next Month <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
                
                <div class="calendar-weekdays">
                    <div>Sun</div>
                    <div>Mon</div>
                    <div>Tue</div>
                    <div>Wed</div>
                    <div>Thu</div>
                    <div>Fri</div>
                    <div>Sat</div>
                </div>
                
                <div class="calendar-grid" id="calendar-grid">
                    <!-- Calendar days will be populated by JavaScript -->
                </div>
            </div>
            
            <div style="margin-top: 30px;">
                <h3 class="section-title">
                    <i class="fas fa-users"></i> Employees Without Birthdays
                </h3>
                @if($employeesWithoutBirthdays->count() > 0)
                    <div class="birthday-list">
                        @foreach($employeesWithoutBirthdays as $employee)
                            <div class="birthday-item">
                                <div>
                                    <strong>{{ $employee->name }}</strong> - {{ $employee->position }}
                                    <div style="color: #718096; font-size: 0.9rem;">
                                        {{ $employee->department->name ?? 'No Department' }}
                                    </div>
                                </div>
                                <a href="{{ route('profile.edit', $employee->id) }}" class="calendar-nav-btn" style="padding: 5px 10px; text-decoration: none;">
                                    <i class="fas fa-user-edit"></i> Update Profile
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="no-birthdays" style="padding: 15px;">
                        <i class="fas fa-check-circle fa-2x" style="color: #4CAF50; margin-bottom: 10px;"></i>
                        <p>All employees have birthday records!</p>
                    </div>
                @endif
            </div>
        </div>
    @else
        <div class="alert-section">
            <h1><i class="fas fa-exclamation-triangle"></i> Restricted Access</h1>
            <p>You don't have sufficient permissions to access this page.</p>
        </div>
    @endif
</div>

<!-- Birthday Details Modal -->
<div id="birthdayModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3 id="modal-title"><i class="fas fa-birthday-cake"></i> Birthday Details</h3>
        <div id="modal-content">
            <!-- Content will be populated by JavaScript -->
        </div>
        <div class="wish-form" id="wish-form-container" style="display: none;">
            <textarea id="wish-message" placeholder="Write your birthday message here..."></textarea>
            <button class="calendar-nav-btn" id="send-wishes-btn">
                <i class="far fa-paper-plane"></i> Send Birthday Wishes
            </button>
        </div>
    </div>
</div>

<!-- Upcoming Birthdays Modal -->
<div id="upcomingModal" class="modal">
    <div class="modal-content" style="max-width: 800px;">
        <span class="close" onclick="closeUpcomingModal()">&times;</span>
        <h3><i class="fas fa-calendar-week"></i> Upcoming Birthdays</h3>
        <div id="upcoming-content">
            @if($upcomingBirthdays->count() > 0)
                <div class="birthday-list">
                    @foreach($upcomingBirthdays as $employee)
                        <div class="birthday-item">
                            <div class="employee-details">
                                <div class="birthday-avatar">
                                    {{ substr($employee->name, 0, 1) }}
                                </div>
                                <div class="employee-details-info">
                                    <h4>{{ $employee->name }}</h4>
                                    <p>
                                        <i class="fas fa-briefcase"></i> {{ $employee->position }} • 
                                        <i class="fas fa-building"></i> {{ $employee->department->name ?? 'No Department' }}
                                    </p>
                                    <p>
                                        <i class="far fa-calendar-alt"></i> 
                                        {{ $employee->birth_date->format('F j, Y') }} 
                                        (in {{ $employee->birth_date->diffInDays(now()) }} days)
                                    </p>
                                </div>
                            </div>
                            <button onclick="sendWishesPrompt({{ $employee->id }}, '{{ $employee->name }}')" class="calendar-nav-btn">
                                <i class="far fa-paper-plane"></i> Send Wishes
                            </button>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="no-birthdays">
                    <i class="fas fa-birthday-cake fa-2x" style="color: #e2e8f0;"></i>
                    <p>No upcoming birthdays in the next 30 days.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    // Global variables
    let currentMonth = new Date().getMonth();
    let currentYear = new Date().getFullYear();
    let selectedEmployeeId = null;
    const monthNames = ["January", "February", "March", "April", "May", "June",
                       "July", "August", "September", "October", "November", "December"];
    const weekdayNames = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];

    // Initialize calendar
    document.addEventListener('DOMContentLoaded', function() {
        @if(in_array(auth()->user()->role, ['department_head', 'admin', 'super_admin']))
            generateCalendar(currentMonth, currentYear);
            
            // Event listeners for month navigation
            document.getElementById('prev-month').addEventListener('click', function() {
                currentMonth--;
                if (currentMonth < 0) {
                    currentMonth = 11;
                    currentYear--;
                }
                updateDateFilter();
                generateCalendar(currentMonth, currentYear);
            });
            
            document.getElementById('next-month').addEventListener('click', function() {
                currentMonth++;
                if (currentMonth > 11) {
                    currentMonth = 0;
                    currentYear++;
                }
                updateDateFilter();
                generateCalendar(currentMonth, currentYear);
            });
            
            // Modal close button
            document.querySelector('.close').addEventListener('click', function() {
                document.getElementById('birthdayModal').style.display = 'none';
            });
            
            // Department filter change
            @if(in_array(auth()->user()->role, ['admin', 'super_admin']))
                document.getElementById('department-filter').addEventListener('change', function() {
                    generateCalendar(currentMonth, currentYear);
                });
                
                document.getElementById('date-filter').addEventListener('change', function() {
                    const [year, month] = this.value.split('-').map(Number);
                    currentMonth = month - 1;
                    currentYear = year;
                    generateCalendar(currentMonth, currentYear);
                });
                
                document.getElementById('search').addEventListener('input', function() {
                    filterBirthdays(this.value);
                });
                
                document.getElementById('export-btn').addEventListener('click', function() {
                    exportBirthdays();
                });
            @endif
            
            // Send wishes button in modal
            document.getElementById('send-wishes-btn').addEventListener('click', function() {
                if (selectedEmployeeId) {
                    const message = document.getElementById('wish-message').value;
                    if (message.trim() === '') {
                        alert('Please enter a birthday message');
                        return;
                    }
                    sendWishes(selectedEmployeeId, message);
                }
            });
        @endif
    });

    // Function to update date filter input
    function updateDateFilter() {
        @if(in_array(auth()->user()->role, ['admin', 'super_admin']))
            const month = (currentMonth + 1).toString().padStart(2, '0');
            document.getElementById('date-filter').value = `${currentYear}-${month}`;
        @endif
    }

    // Function to generate calendar
    function generateCalendar(month, year) {
        const calendarGrid = document.getElementById('calendar-grid');
        
        document.getElementById('current-month').textContent = `${monthNames[month]} ${year}`;
        
        // Clear previous calendar
        calendarGrid.innerHTML = '';
        
        // Get first day of month and total days in month
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        
        // Add empty cells for days before the first day of the month
        for (let i = 0; i < firstDay; i++) {
            const emptyDay = document.createElement('div');
            emptyDay.className = 'calendar-day empty';
            calendarGrid.appendChild(emptyDay);
        }
        
        // Add cells for each day of the month
        for (let day = 1; day <= daysInMonth; day++) {
            const dayElement = document.createElement('div');
            dayElement.className = 'calendar-day';
            
            const today = new Date();
            if (day === today.getDate() && month === today.getMonth() && year === today.getFullYear()) {
                dayElement.classList.add('today');
            }
            
            // Add day number
            const dayNumber = document.createElement('div');
            dayNumber.className = 'calendar-day-number';
            dayNumber.textContent = day;
            dayElement.appendChild(dayNumber);
            
            // Store the full date as a data attribute
            const fullDate = new Date(year, month, day).toISOString().split('T')[0];
            dayElement.dataset.date = fullDate;
            
            calendarGrid.appendChild(dayElement);
        }
        
        // Load birthdays for this month
        loadBirthdaysForMonth(month + 1, year);
    }
    
    // Function to load birthdays for a specific month
    function loadBirthdaysForMonth(month, year) {
        const date = `${year}-${month.toString().padStart(2, '0')}-01`;
        let url = `/birthdays/${date}`;
        
        @if(in_array(auth()->user()->role, ['admin', 'super_admin']))
            const departmentId = document.getElementById('department-filter').value;
            if (departmentId !== 'all') {
                url += `?department_id=${departmentId}`;
            }
        @endif
        
        axios.get(url)
            .then(response => {
                const birthdays = response.data;
                const birthdaysByDay = {};
                
                // Group birthdays by day
                birthdays.forEach(employee => {
                    const birthDate = new Date(employee.birth_date);
                    const day = birthDate.getDate();
                    
                    if (!birthdaysByDay[day]) {
                        birthdaysByDay[day] = [];
                    }
                    
                    birthdaysByDay[day].push(employee);
                });
                
                // Mark days with birthdays
                const calendarDays = document.querySelectorAll('.calendar-day:not(.empty)');
                calendarDays.forEach(dayElement => {
                    const day = parseInt(dayElement.querySelector('.calendar-day-number').textContent);
                    
                    if (birthdaysByDay[day]) {
                        dayElement.classList.add('has-birthday');
                        const badge = document.createElement('div');
                        badge.className = 'birthday-badge';
                        badge.textContent = birthdaysByDay[day].length;
                        dayElement.appendChild(badge);
                        
                        // Add birthday names to the day
                        const birthdayList = document.createElement('div');
                        birthdayList.className = 'birthday-list-container';
                        
                        birthdaysByDay[day].forEach(emp => {
                            const item = document.createElement('div');
                            item.className = 'birthday-list-item';
                            item.textContent = emp.name;
                            item.title = `${emp.name} (${emp.department?.name || 'No Department'})`;
                            birthdayList.appendChild(item);
                        });
                        
                        dayElement.appendChild(birthdayList);
                        
                        dayElement.addEventListener('click', function() {
                            showBirthdayDetails(birthdaysByDay[day], dayElement.dataset.date);
                        });
                    }
                });
            })
            .catch(error => {
                console.error('Error loading birthdays:', error);
                alert('Failed to load birthday data. Please try again.');
            });
    }
    
    // Function to show birthday details in modal
    function showBirthdayDetails(birthdays, date) {
        const modal = document.getElementById('birthdayModal');
        const modalContent = document.getElementById('modal-content');
        const wishForm = document.getElementById('wish-form-container');
        const modalTitle = document.getElementById('modal-title');
        
        let content = '';
        const displayDate = new Date(date).toLocaleDateString('en-US', { 
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' 
        });
        
        if (birthdays.length === 1) {
            selectedEmployeeId = birthdays[0].id;
            content = `
                <div class="employee-details">
                    <div class="birthday-avatar">
                        ${birthdays[0].name.charAt(0)}
                    </div>
                    <div class="employee-details-info">
                        <h4>${birthdays[0].name}</h4>
                        <p><i class="fas fa-briefcase"></i> ${birthdays[0].position || 'Not specified'}</p>
                        <p><i class="fas fa-building"></i> ${birthdays[0].department?.name || 'No Department'}</p>
                        <p><i class="fas fa-envelope"></i> ${birthdays[0].email || 'No email'}</p>
                    </div>
                </div>
                <div style="margin-top: 15px;">
                    <p><strong><i class="far fa-calendar-alt"></i> Birthday:</strong> ${displayDate}</p>
                    <p><strong><i class="fas fa-birthday-cake"></i> Age:</strong> ${calculateAge(birthdays[0].birth_date)}</p>
                </div>
            `;
            wishForm.style.display = 'block';
            modalTitle.innerHTML = `<i class="fas fa-birthday-cake"></i> ${birthdays[0].name}'s Birthday`;
        } else {
            content = `<h4 style="margin-bottom: 15px;">Employees celebrating on ${displayDate}:</h4><div class="birthday-list">`;
            birthdays.forEach(emp => {
                content += `
                    <div class="birthday-item">
                        <div>
                            <strong>${emp.name}</strong>
                            <div style="color: #718096; font-size: 0.9rem;">
                                ${emp.position} • ${emp.department?.name || 'No Department'}
                            </div>
                        </div>
                        <button onclick="event.stopPropagation(); sendWishesPrompt(${emp.id}, '${emp.name}')" class="calendar-nav-btn" style="padding: 5px 10px;">
                            <i class="far fa-paper-plane"></i> Send Wishes
                        </button>
                    </div>
                `;
            });
            content += '</div>';
            selectedEmployeeId = null;
            wishForm.style.display = 'none';
            modalTitle.innerHTML = `<i class="fas fa-birthday-cake"></i> Birthdays on ${displayDate}`;
        }
        
        modalContent.innerHTML = content;
        modal.style.display = 'block';
    }
    
    // Function to calculate age from birth date
    function calculateAge(birthDate) {
        const today = new Date();
        const birth = new Date(birthDate);
        let age = today.getFullYear() - birth.getFullYear();
        const monthDiff = today.getMonth() - birth.getMonth();
        
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
            age--;
        }
        
        return age;
    }
    
    // Function to send birthday wishes
    function sendWishes(employeeId, message = '') {
        axios.post('/send-wishes', {
            employee_id: employeeId,
            message: message
        }, {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            alert(response.data.message);
            document.getElementById('birthdayModal').style.display = 'none';
        })
        .catch(error => {
            console.error('Error sending wishes:', error);
            alert('Failed to send birthday wishes. Please try again.');
        });
    }
    
    // Function to show prompt for sending wishes
    function sendWishesPrompt(employeeId, employeeName) {
        const message = prompt(`Write your birthday message for ${employeeName}:`, 
            `Happy Birthday ${employeeName}! Wishing you a wonderful year ahead filled with success and happiness.`);
        if (message !== null && message.trim() !== '') {
            sendWishes(employeeId, message);
        }
    }
    
    // Function to show upcoming birthdays modal
    function showUpcomingBirthdays() {
        document.getElementById('upcomingModal').style.display = 'block';
    }
    
    // Function to close upcoming birthdays modal
    function closeUpcomingModal() {
        document.getElementById('upcomingModal').style.display = 'none';
    }
    
    // Function to filter birthdays by search term
    function filterBirthdays(searchTerm) {
        // This would be implemented with server-side filtering in a real application
        console.log('Filtering by:', searchTerm);
        // For demo purposes, we'll just highlight matching names in the calendar
        const searchLower = searchTerm.toLowerCase();
        const birthdayItems = document.querySelectorAll('.birthday-list-item');
        
        birthdayItems.forEach(item => {
            if (item.textContent.toLowerCase().includes(searchLower)) {
                item.style.backgroundColor = 'rgba(76, 175, 80, 0.2)';
                item.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            } else {
                item.style.backgroundColor = '';
            }
        });
    }
    
    // Function to export birthdays data
    function exportBirthdays() {
        const month = currentMonth + 1;
        const year = currentYear;
        let url = `/birthdays/export?month=${month}&year=${year}`;
        
        @if(in_array(auth()->user()->role, ['admin', 'super_admin']))
            const departmentId = document.getElementById('department-filter').value;
            if (departmentId !== 'all') {
                url += `&department_id=${departmentId}`;
            }
        @endif
        
        window.location.href = url;
    }
    
    // Close modal when clicking outside of it
    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    }
</script>

@endsection