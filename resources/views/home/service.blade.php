@extends('home\layout\usehome')
@section('title','Services')
@section('content')

<style>
  /* Global Styles */
  :root {
    --primary-color: #3498db;
    --secondary-color: #e74c3c;
    --accent-color: #f39c12;
    --dark-color: #2c3e50;
    --light-color: #ecf0f1;
    --success-color: #2ecc71;
  }

  /* Scroll Snap Container */
  .sp-scroll-container {
    scroll-snap-type: y mandatory;
    height: 100vh;
    overflow-y: scroll;
    scroll-behavior: smooth;
  }

  /* Service Sections */
  .sp-service-section {
    scroll-snap-align: start;
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    position: relative;
    overflow: hidden;
  }

  /* Odd/Even Background Colors */
  .sp-service-section:nth-child(odd) {
    background-color: #f8f9fa;
  }
  .sp-service-section:nth-child(even) {
    background-color: #ffffff;
  }

  /* Content Styling */
  .sp-service-content {
    max-width: 1200px;
    width: 100%;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
    align-items: center;
  }

  /* Image Styling */
  .sp-service-image {
    position: relative;
    height: 400px;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
  }

  .sp-service-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
  }

  .sp-service-image:hover img {
    transform: scale(1.05);
  }

  /* Text Content */
  .sp-service-text {
    padding: 2rem;
  }

  .sp-service-text h2 {
    font-size: 2.5rem;
    color: var(--dark-color);
    margin-bottom: 1.5rem;
    position: relative;
  }

  .sp-service-text h2::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 0;
    width: 60px;
    height: 4px;
    background: var(--accent-color);
  }

  .sp-service-text ul {
    list-style: none;
    padding: 0;
    margin-top: 2rem;
  }

  .sp-service-text li {
    margin-bottom: 1rem;
    display: flex;
    align-items: flex-start;
    font-size: 1.1rem;
    line-height: 1.6;
  }

  .sp-check-icon {
    color: var(--success-color);
    font-weight: bold;
    margin-right: 12px;
    font-size: 1.3rem;
  }

  /* Navigation Dots */
  .sp-scroll-dots {
    position: fixed;
    right: 2rem;
    top: 50%;
    transform: translateY(-50%);
    display: flex;
    flex-direction: column;
    gap: 1rem;
    z-index: 100;
  }

  .sp-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background-color: rgba(0, 0, 0, 0.2);
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .sp-dot:hover, .sp-dot.active {
    background-color: var(--secondary-color);
    transform: scale(1.3);
  }

  /* Back Button */
  .back-to-all {
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    z-index: 100;
    background: var(--primary-color);
    color: white;
    padding: 12px 20px;
    border-radius: 30px;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
  }

  .back-to-all:hover {
    background: var(--dark-color);
    transform: translateY(-3px);
  }

  /* Responsive Design */
  @media (max-width: 992px) {
    .sp-service-content {
      grid-template-columns: 1fr;
    }
    
    .sp-service-image {
      height: 300px;
    }
  }

  @media (max-width: 768px) {
    .sp-service-text h2 {
      font-size: 2rem;
    }
    
    .sp-service-text li {
      font-size: 1rem;
    }
    
    .sp-scroll-dots {
      right: 1rem;
    }
    
    .back-to-all {
      bottom: 1rem;
      right: 1rem;
    }
  }
</style>

<div class="sp-scroll-container" id="serviceContainer">
  <!-- Attendance Management -->
  <section class="sp-service-section" id="attendance">
    <div class="sp-service-content">
      <div class="sp-service-image">
        <img src="./assets/img/IMG/service1.png" alt="Attendance Management">
      </div>
      <div class="sp-service-text">
        <h2>Attendance Management</h2>
        <p>Efficient tracking and management of employee attendance with advanced features.</p>
        <ul>
          <li><span class="sp-check-icon">&#10003;</span> View employee attendance logs</li>
          <li><span class="sp-check-icon">&#10003;</span> Register attendance via mobile/desktop</li>
          <li><span class="sp-check-icon">&#10003;</span> Calculate working hours automatically</li>
          <li><span class="sp-check-icon">&#10003;</span> Track late arrivals and early departures</li>
        </ul>
      </div>
    </div>
  </section>

  <!-- Leave Management -->
  <section class="sp-service-section" id="leave">
    <div class="sp-service-content">
      <div class="sp-service-text">
        <h2>Leave Management</h2>
        <p>Streamlined leave request and approval process for modern workplaces.</p>
        <ul>
          <li><span class="sp-check-icon">&#10003;</span> Submit various leave requests</li>
          <li><span class="sp-check-icon">&#10003;</span> View approval status in real-time</li>
          <li><span class="sp-check-icon">&#10003;</span> Track remaining leave days</li>
        </ul>
      </div>
      <div class="sp-service-image">
        <img src="./assets/img/IMG/service2.png" alt="Leave Management">
      </div>
    </div>
  </section>

  <section class="sp-service-section" id="absences">
    <div class="sp-service-content">
      <div class="sp-service-image">
        <img src="./assets/img/IMG/service3.png" alt="Absences Management">
      </div>
      <div class="sp-service-text">
        <h2>Absences Management</h2>
        <p>Comprehensive tools to monitor and manage employee absences.</p>
        <ul>
          <li><span class="sp-check-icon">&#10003;</span> Monitor unauthorized absences</li>
          <li><span class="sp-check-icon">&#10003;</span> Manage emergency situations</li>
          <li><span class="sp-check-icon">&#10003;</span> Alerts for extended absences</li>
        </ul>
      </div>
    </div>
  </section>

  <!-- Performance Evaluations -->
  <section class="sp-service-section" id="performance">
    <div class="sp-service-content">
      <div class="sp-service-text">
        <h2>Performance Evaluations</h2>
        <p>Comprehensive employee performance tracking and analysis.</p>
        <ul>
          <li><span class="sp-check-icon">&#10003;</span> View evaluation results</li>
          <li><span class="sp-check-icon">&#10003;</span> Add manager reviews</li>
          <li><span class="sp-check-icon">&#10003;</span> Rank employee performance</li>
        </ul>
      </div>
      <div class="sp-service-image">
        <img src="./assets/img/IMG/service4.png" alt="Performance Evaluations">
      </div>
    </div>
  </section>

  <!-- Reports -->
  <section class="sp-service-section" id="reports">
    <div class="sp-service-content">
      <div class="sp-service-image">
        <img src="./assets/img/IMG/service5.png" alt="Reports">
      </div>
      <div class="sp-service-text">
        <h2>Reports & Analytics</h2>
        <p>Detailed analytics and comprehensive reporting tools.</p>
        <ul>
          <li><span class="sp-check-icon">&#10003;</span> Regular automated reports</li>
          <li><span class="sp-check-icon">&#10003;</span> Export to PDF/Excel</li>
          <li><span class="sp-check-icon">&#10003;</span> Graphical data visualization</li>
        </ul>
      </div>
    </div>
  </section>

  <!-- Navigation Dots -->
  <div class="sp-scroll-dots">
    <div class="sp-dot active" data-section="attendance"></div>
    <div class="sp-dot" data-section="leave"></div>
    <div class="sp-dot" data-section="absences"></div>
    <div class="sp-dot" data-section="performance"></div>
    <div class="sp-dot" data-section="reports"></div>
  </div>

  <!-- Back to All Services Button -->
 <!-- Back to All Services Button -->

</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('serviceContainer');
    const dots = document.querySelectorAll('.sp-dot');
    const sections = document.querySelectorAll('.sp-service-section');
    
    // Check for hash on page load
    if (window.location.hash) {
      const targetSection = document.querySelector(window.location.hash);
      if (targetSection) {
        container.scrollTo({
          top: targetSection.offsetTop,
          behavior: 'smooth'
        });
      }
    }
    
    // Update active dot on scroll
    container.addEventListener('scroll', function() {
      const scrollPosition = container.scrollTop;
      const containerHeight = container.clientHeight;
      
      sections.forEach((section, index) => {
        const sectionTop = section.offsetTop;
        const sectionHeight = section.clientHeight;
        
        if (scrollPosition >= sectionTop - containerHeight * 0.3 && 
            scrollPosition < sectionTop + sectionHeight - containerHeight * 0.3) {
          dots.forEach(dot => dot.classList.remove('active'));
          dots[index].classList.add('active');
          
          // Update URL hash without scrolling
          history.replaceState(null, null, `#${section.id}`);
        }
      });
    });
    
    // Click on dot to scroll to section
    dots.forEach(dot => {
      dot.addEventListener('click', function() {
        const sectionId = this.getAttribute('data-section');
        const section = document.getElementById(sectionId);
        container.scrollTo({
          top: section.offsetTop,
          behavior: 'smooth'
        });
      });
    });
  });
</script>

@endsection