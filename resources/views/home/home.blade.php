@extends('home\layout\usehome')
@section('title','SMART PUNCH')
@section('content')
<style>


.services-scroller {
    width: 100%;
    overflow-x: auto;
    scroll-behavior: smooth;
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none;  /* Firefox */
    padding: 20px 0;
}

.services-scroller::-webkit-scrollbar {
    display: none; /* Hide scrollbar for Chrome, Safari and Opera */
}

.services-container {
    display: flex;
    gap: 30px;
    padding: 0 50px;
}

.service-card {
    min-width: 300px;
    text-align: center;
    padding: 30px 20px;
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    transition: transform 0.3s ease;
}

.service-card:hover {
    transform: translateY(-5px);
}

.service-circle {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    overflow: hidden;
}

.service-circle img {
    width: 80%;
    height: auto;
    object-fit: contain;
}

.details-btn {
    display: inline-block;
    margin-top: 15px;
    padding: 8px 20px;
    background: #007bff;
    color: white;
    border-radius: 30px;
    text-decoration: none;
    transition: all 0.3s ease;
}

.details-btn:hover {
    background: #0056b3;
    transform: translateX(5px);
}

.scroll-arrow {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: white;
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    z-index: 10;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    color: #007bff;
}

.left-arrow {
    left: 10px;
}

.right-arrow {
    right: 10px;
}

@media (max-width: 768px) {
    .scroll-arrow {
        display: none;
    }
    
    .services-container {
        padding: 0 20px;
    }
}
  /* Responsive Design */
  @media (max-width: 992px) {
    .service-item {
      padding: 30px 20px;
    }
    
    .service-item h3 {
      font-size: 1.6rem;
    }
  }



  @media (max-width: 576px) {
    .service-item {
      padding: 25px 15px;
    }
    
    .service-item h3 {
      font-size: 1.4rem;
    }
    
    .service-item p {
      font-size: 1rem;
    }
  }
</style>
<section id="hero" class="hero section">

    <div class="container">
      <div class="row gy-4">
        <div class="col-lg-6 order-2 order-lg-1 d-flex flex-column justify-content-center" data-aos="fade-up">
          <h1>SMART PUNCH</h1>
          <p>A comprehensive HR system to manage attendance, leave, evaluations, and more, with advanced features and an interactive dashboard</p>
          <div class="d-flex">
            <a href="./contact.html" class="btn-get-started">Request a Demo</a>
          </div>
        </div>
        <div class="col-lg-6 order-1 order-lg-2 hero-img" data-aos="zoom-out" data-aos-delay="100">
          <img src="assets/img/hero-img.png" class="img-fluid animated" alt="" >
        </div>
      </div>
    </div>

  </section>

  <section id="featured-services" class="featured-services section">

    <div class="container">

      <div class="row gy-4">

        <div class="col-lg-4 d-flex" data-aos="fade-up" data-aos-delay="100">
          <div class="service-item position-relative">
            <div class="icon"><i class="fa-solid fa-clock"></i>
            </div>
            <h4><a href="" class="stretched-link">Smart Attendance Management</a></h4>
            <p>Record and manage employee attendance accurately without the need for fingerprint systems, supported by detailed reports and analytics.</p>
          </div>
        </div><!-- End Service Item -->

        <div class="col-lg-4 d-flex" data-aos="fade-up" data-aos-delay="200">
          <div class="service-item position-relative">
            <div class="icon"><i class="fa-solid fa-calendar-check"></i>
            </div>
            <h4><a href="" class="stretched-link">Seamless Leave & Approvals</a></h4>
            <p>Submit and approve leave requests effortlessly, with an intelligent system to track remaining balances.</p>
          </div>
        </div><!-- End Service Item -->

        <div class="col-lg-4 d-flex" data-aos="fade-up" data-aos-delay="300">
          <div class="service-item position-relative">
            <div class="icon"><i class="fa-solid fa-chart-line"></i>
            </div>
            <h4><a href="" class="stretched-link">Advanced Reports & Insights</a></h4>
            <p>Gain clear insights into employee performance, attendance records, and productivity through an interactive dashboard.

            </p>
          </div>
        </div><!-- End Service Item -->

      </div>

    </div>

  </section>

  <section id="about" class="about section">

    <!-- Section Title -->
    <div class="container section-title" data-aos="fade-up">
      <span>Who We Are<br></span>
      <h2>About Smart Punch</h2>
    </div><!-- End Section Title -->
  
    <div class="container">
      <div class="row gy-4">
        <div class="col-lg-6 position-relative align-self-start" data-aos="fade-up" data-aos-delay="100">
          <img src="assets/img/IMG/aboutbage.png" class="img-fluid" alt="Smart Punch Overview">
        </div>
  
        <div class="col-lg-6 content" data-aos="fade-up" data-aos-delay="200">
          <h3>More Than Just a Time-Tracking System</h3>
          <p>
            Smart Punch is a <strong>comprehensive and intelligent</strong> solution for managing employee <strong>attendance, leaves, and performance</strong>.  
            Designed with <strong>efficiency and simplicity</strong> in mind, our system enables organizations to effortlessly <strong>track attendance, generate insightful reports,</strong> and make <strong>data-driven decisions</strong> with ease.
          </p>
  
          <h3>Why Choose Smart Punch?</h3>
          <ul>
            <li><i class="bi bi-check-circle"></i> <strong>Automate tedious tasks</strong> and minimize manual errors.</li>
            <li><i class="bi bi-bar-chart"></i> <strong>Gain valuable insights</strong> through smart analytics and reports.</li>
            <li><i class="bi bi-people"></i> <strong>Seamless adaptability</strong> for small teams and large enterprises alike.</li>
          </ul>
  
          <p>
            With Smart Punch, you’re not just using a system—you’re transforming your workflow, <strong>boosting productivity, and optimizing workforce management</strong>.  
            No matter the size of your team, our solution <strong>adapts to your needs and grows with you.</strong>
          </p>
        </div>
      </div>
    </div>
  
  </section>

  <section id="services" class="services section light-background">
    <!-- Section Title -->
    <div class="container section-title" data-aos="fade-up">
        <span>Services</span>
        <h2>Services</h2>
    </div><!-- End Section Title -->

    <div class="container position-relative">
        <button class="scroll-arrow left-arrow" aria-label="Previous services">
            <i class="bi bi-chevron-left"></i>
        </button>
        
        <div class="services-scroller">
            <div class="services-container">
                <!-- Service 1 -->
                <div class="service-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="service-circle">
                        <img src="./assets/img/IMG/service1.png" alt="Attendance Management">
                    </div>
                    <h3>Attendance Management</h3>
                    <a href="/service" class="details-btn">Details <i class="bi bi-arrow-right"></i></a>
                </div>

                <!-- Service 2 -->
                <div class="service-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="service-circle">
                        <img src="./assets/img/IMG/service2.png" alt="Leave Management">
                    </div>
                    <h3>Leave Management</h3>
                    <a href="/service" class="details-btn">Details <i class="bi bi-arrow-right"></i></a>
                </div>

                <!-- Service 3 -->
                <div class="service-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="service-circle">
                        <img src="./assets/img/IMG/service3.png" alt="Absences Management">
                    </div>
                    <h3>Absences Management</h3>
                    <a href="/service" class="details-btn">Details <i class="bi bi-arrow-right"></i></a>
                </div>

                <!-- Service 4 -->
                <div class="service-card" data-aos="fade-up" data-aos-delay="400">
                    <div class="service-circle">
                        <img src="./assets/img/IMG/service4.png" alt="Performance Evaluations">
                    </div>
                    <h3>Performance Evaluations</h3>
                    <a href="/service" class="details-btn">Details <i class="bi bi-arrow-right"></i></a>
                </div>

                <!-- Service 5 -->
                <div class="service-card" data-aos="fade-up" data-aos-delay="500">
                    <div class="service-circle">
                        <img src="./assets/img/IMG/service5.png" alt="Reports">
                    </div>
                    <h3>Reports & Analytics</h3>
                    <a href="/service" class="details-btn">Details <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>

        <button class="scroll-arrow right-arrow" aria-label="Next services">
            <i class="bi bi-chevron-right"></i>
        </button>
    </div>
</section>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const scroller = document.querySelector('.services-scroller');
    const leftArrow = document.querySelector('.left-arrow');
    const rightArrow = document.querySelector('.right-arrow');
    const serviceCard = document.querySelector('.service-card');
    const cardWidth = serviceCard.offsetWidth + 30; // width + gap

    leftArrow.addEventListener('click', () => {
        scroller.scrollBy({ left: -cardWidth, behavior: 'smooth' });
    });

    rightArrow.addEventListener('click', () => {
        scroller.scrollBy({ left: cardWidth, behavior: 'smooth' });
    });

    // Hide arrows when at extremes
    scroller.addEventListener('scroll', () => {
        leftArrow.style.display = scroller.scrollLeft <= 10 ? 'none' : 'flex';
        rightArrow.style.display = scroller.scrollLeft >= scroller.scrollWidth - scroller.clientWidth - 10 ? 'none' : 'flex';
    });

    // Initial state
    leftArrow.style.display = 'none';
});
</script>
  


  @endsection