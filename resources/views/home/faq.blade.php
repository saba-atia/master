@extends('home\layout\usehome')
@section('title','FAQ')
@section('content')

<style>
  :root {
    --primary-color: #2c3e50;
    --secondary-color: #3498db;
    --text-color: #333;
    --light-bg: #f9f9f9;
    --dark-bg: #2c3e50;
    --border-radius: 8px;
    --box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    --transition: all 0.3s ease;
  }
  
  .faq-hero {
    background: linear-gradient(135deg, var(--dark-bg) 0%, var(--secondary-color) 100%);
    color: white;
    padding: 80px 0 60px;
    text-align: center;
    margin-bottom: 40px;
  }
  
  .faq-hero h1 {
    font-size: 2.8rem;
    font-weight: 700;
    margin-bottom: 20px;
    color: whitesmoke;
  }
  
  .faq-hero p {
    font-size: 1.2rem;
    max-width: 700px;
    margin: 0 auto;
    opacity: 0.9;
  }
  
  .faq-container {
    max-width: 900px;
    margin: 0 auto 60px;
    padding: 0 20px;
  }
  
  .faq-intro {
    text-align: center;
    margin-bottom: 40px;
  }
  
  .faq-intro h2 {
    color: var(--dark-bg);
    font-size: 2rem;
    margin-bottom: 15px;
  }
  
  .faq-intro p {
    color: var(--text-color);
    font-size: 1.1rem;
    max-width: 700px;
    margin: 0 auto;
  }
  
  .faq-item {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    margin-bottom: 20px;
    overflow: hidden;
    transition: var(--transition);
  }
  
  .faq-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
  }
  
  .faq-question {
    width: 100%;
    text-align: left;
    padding: 20px 25px;
    background: none;
    border: none;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--dark-bg);
    transition: var(--transition);
  }
  
  .faq-question:hover {
    color: var(--secondary-color);
  }
  
  .faq-question .icon {
    font-size: 1.5rem;
    transition: var(--transition);
  }
  
  .faq-item.active .faq-question .icon {
    transform: rotate(45deg);
    color: var(--accent-color);
  }
  
  .faq-answer {
    padding: 0 25px;
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease, padding 0.3s ease;
  }
  
  .faq-item.active .faq-answer {
    padding: 0 25px 25px;
    max-height: 500px;
  }
  
  .faq-answer p {
    color: var(--text-color);
    line-height: 1.6;
    margin: 0;
  }
  
  .faq-categories {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 40px;
  }
  
  .faq-category {
    padding: 8px 20px;
    background: white;
    border: 1px solid #ddd;
    border-radius: 30px;
    cursor: pointer;
    transition: var(--transition);
    font-weight: 500;
  }
  
  .faq-category:hover, .faq-category.active {
    background: var(--secondary-color);
    color: white;
    border-color: var(--secondary-color);
  }
  
  .call-to-action {
    background: linear-gradient(135deg, var(--dark-bg) 0%, var(--primary-color) 100%);
    color: white;
    padding: 60px 0;
    text-align: center;
  }
  
  .call-to-action h3 {
    font-size: 1.8rem;
    margin-bottom: 25px;
  }
  
  .btn-action {
    background: white;
    color: var(--dark-bg);
    border: none;
    padding: 12px 30px;
    font-size: 1.1rem;
    font-weight: 600;
    border-radius: 30px;
    cursor: pointer;
    transition: var(--transition);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  }
  
  .btn-action:hover {
    transform: translateY(-3px);
    box-shadow: 0 7px 20px rgba(0, 0, 0, 0.15);
    background: var(--secondary-color);
    color: white;
  }
  
  @media (max-width: 768px) {
    .faq-hero h1 {
      font-size: 2.2rem;
    }
    
    .faq-question {
      padding: 15px 20px;
      font-size: 1rem;
    }
  }

  /* Hide all items initially */
  .faq-item {
    display: none;
  }

  /* Show items with active category */
  .faq-item[data-category="all"],
  .faq-item[data-category].show {
    display: block;
  }
</style>

<section class="faq-hero">
  <div class="container">
    <h1>Frequently Asked Questions</h1>
    <p>Find answers to common questions about Smart Punch HR Management System</p>
  </div>
</section>

<div class="faq-container">
  <div class="faq-intro">
    <h2>How can we help you?</h2>
    <p>Browse through our most frequently asked questions to learn more about our HR management solutions.</p>
  </div>
  
  <div class="faq-categories">
    <div class="faq-category active" data-category="all">All Questions</div>
    <div class="faq-category" data-category="attendance">Attendance</div>
    <div class="faq-category" data-category="leave">Leave Management</div>
    <div class="faq-category" data-category="reporting">Reporting</div>
    <div class="faq-category" data-category="settings">Account Settings</div>
    <div class="faq-category" data-category="security">Security</div>
  </div>
  
  <div class="faq-list">
    <!-- Attendance Questions -->
    <div class="faq-item" data-category="attendance">
      <button class="faq-question">
        How does the attendance tracking work in Smart Punch?
        <span class="icon">+</span>
      </button>
      <div class="faq-answer">
        <p>Smart Punch uses advanced geo-location technology to track employee attendance. Employees can check in/out via our mobile app which verifies their location against predefined company locations. Administrators can set acceptable radius ranges and receive alerts for suspicious check-ins.</p>
      </div>
    </div>
    
    <div class="faq-item" data-category="attendance">
      <button class="faq-question">
        Can employees check in from multiple locations?
        <span class="icon">+</span>
      </button>
      <div class="faq-answer">
        <p>Yes, administrators can define multiple approved locations for each employee or department. The system supports branch-specific attendance policies and can automatically detect which location the employee is checking in from.</p>
      </div>
    </div>
    
    <div class="faq-item" data-category="attendance">
      <button class="faq-question">
        What if an employee forgets to check in or out?
        <span class="icon">+</span>
      </button>
      <div class="faq-answer">
        <p>Managers receive notifications for missing check-ins and can manually add them. Employees can also submit late check-in requests with explanations. The system maintains an audit log of all manual adjustments.</p>
      </div>
    </div>
    
    <!-- Leave Management Questions -->
    <div class="faq-item" data-category="leave">
      <button class="faq-question">
        How do employees request time off?
        <span class="icon">+</span>
      </button>
      <div class="faq-answer">
        <p>Employees can submit leave requests through the mobile app or web portal, specifying the type of leave (vacation, sick, personal, etc.), dates, and any supporting notes. The request is then routed to their manager for approval.</p>
      </div>
    </div>
    
    <div class="faq-item" data-category="leave">
      <button class="faq-question">
        Can we customize leave types and policies?
        <span class="icon">+</span>
      </button>
      <div class="faq-answer">
        <p>Yes, Smart Punch allows complete customization of leave types, accrual policies, and approval workflows. You can define different rules for different employee groups or departments.</p>
      </div>
    </div>
    
    <div class="faq-item" data-category="leave">
      <button class="faq-question">
        How are leave balances calculated?
        <span class="icon">+</span>
      </button>
      <div class="faq-answer">
        <p>The system automatically calculates leave balances based on your configured policies (annual accrual, monthly accrual, etc.). Balances are updated in real-time as leave is approved and taken.</p>
      </div>
    </div>
    
    <!-- Reporting Questions -->
    <div class="faq-item" data-category="reporting">
      <button class="faq-question">
        What types of reports are available?
        <span class="icon">+</span>
      </button>
      <div class="faq-answer">
        <p>Smart Punch offers comprehensive reporting including attendance summaries, late arrival/early departure tracking, leave balance reports, overtime calculations, and custom reports by department/team. All reports can be exported to Excel, PDF, or scheduled for automatic delivery.</p>
      </div>
    </div>
    
    <div class="faq-item" data-category="reporting">
      <button class="faq-question">
        Can I create custom reports?
        <span class="icon">+</span>
      </button>
      <div class="faq-answer">
        <p>Yes, our advanced reporting module allows you to create custom reports with specific filters, date ranges, and employee groups. You can save these custom reports for future use.</p>
      </div>
    </div>
    
    <div class="faq-item" data-category="reporting">
      <button class="faq-question">
        How do I access payroll reports?
        <span class="icon">+</span>
      </button>
      <div class="faq-answer">
        <p>Payroll-ready reports are available in the Reports section, formatted specifically for integration with payroll systems. You can generate them on demand or schedule automatic delivery before each payroll cycle.</p>
      </div>
    </div>
    
    <!-- Account Settings Questions -->
    <div class="faq-item" data-category="settings">
      <button class="faq-question">
        How do I add new employees to the system?
        <span class="icon">+</span>
      </button>
      <div class="faq-answer">
        <p>Administrators can add new employees individually through the dashboard or import multiple employees via CSV. The system supports bulk updates and automatically sends welcome emails with setup instructions.</p>
      </div>
    </div>
    
    <div class="faq-item" data-category="settings">
      <button class="faq-question">
        Can employees update their own information?
        <span class="icon">+</span>
      </button>
      <div class="faq-answer">
        <p>Yes, employees can update personal details through their profile, while sensitive information requires administrator approval. You can configure which fields are employee-editable in the system settings.</p>
      </div>
    </div>
    
    <div class="faq-item" data-category="settings">
      <button class="faq-question">
        How do I change an employee's department or position?
        <span class="icon">+</span>
      </button>
      <div class="faq-answer">
        <p>Administrators can update employee department/position assignments in the employee profile. The system maintains a history of all organizational changes for audit purposes.</p>
      </div>
    </div>
    
    <!-- Security Questions -->
    <div class="faq-item" data-category="security">
      <button class="faq-question">
        How is employee data secured in Smart Punch?
        <span class="icon">+</span>
      </button>
      <div class="faq-answer">
        <p>We use enterprise-grade security measures including encryption of all sensitive data, regular security audits, and role-based access controls. The system is compliant with major data protection regulations.</p>
      </div>
    </div>
    
    <div class="faq-item" data-category="security">
      <button class="faq-question">
        What authentication methods are supported?
        <span class="icon">+</span>
      </button>
      <div class="faq-answer">
        <p>Smart Punch supports multiple authentication methods including password, biometric (fingerprint/facial recognition on mobile), and two-factor authentication for enhanced security.</p>
      </div>
    </div>
    
    <div class="faq-item" data-category="security">
      <button class="faq-question">
        Can we restrict access by IP address?
        <span class="icon">+</span>
      </button>
      <div class="faq-answer">
        <p>Yes, administrators can configure IP restrictions to limit access to specific company networks or locations for additional security.</p>
      </div>
    </div>
    
    <!-- General Questions (appear in "All" category) -->
    <div class="faq-item" data-category="all">
      <button class="faq-question">
        What makes Smart Punch different from other HR systems?
        <span class="icon">+</span>
      </button>
      <div class="faq-answer">
        <p>Smart Punch combines advanced attendance tracking with comprehensive HR management in a user-friendly interface. Our geo-location technology, real-time analytics, and customizable workflows provide a complete solution tailored to modern businesses.</p>
      </div>
    </div>
    
    <div class="faq-item" data-category="all">
      <button class="faq-question">
        How long does implementation typically take?
        <span class="icon">+</span>
      </button>
      <div class="faq-answer">
        <p>Most implementations are completed within 2-4 weeks, depending on complexity. Our customer success team guides you through each step including data migration, configuration, and employee training.</p>
      </div>
    </div>
    
    <div class="faq-item" data-category="all">
      <button class="faq-question">
        Is training provided for administrators and employees?
        <span class="icon">+</span>
      </button>
      <div class="faq-answer">
        <p>Yes, we provide comprehensive training for administrators through live webinars and documentation. Employees receive guided onboarding within the app, plus access to video tutorials and a knowledge base.</p>
      </div>
    </div>
  </div>
</div>



<script>
  document.addEventListener('DOMContentLoaded', function() {
    const faqItems = document.querySelectorAll('.faq-item');
    const categories = document.querySelectorAll('.faq-category');
    
    // Initialize - show all items
    showAllQuestions();
    
    // Accordion functionality
    faqItems.forEach(item => {
      const question = item.querySelector('.faq-question');
      
      question.addEventListener('click', () => {
        item.classList.toggle('active');
      });
    });
    
    // Category filtering
    categories.forEach(category => {
      category.addEventListener('click', () => {
        // Update active category
        categories.forEach(c => c.classList.remove('active'));
        category.classList.add('active');
        
        const selectedCategory = category.dataset.category;
        
        if (selectedCategory === 'all') {
          showAllQuestions();
        } else {
          // Hide all items first
          faqItems.forEach(item => {
            item.classList.remove('show');
            item.classList.remove('active');
          });
          
          // Show items for selected category
          document.querySelectorAll(`.faq-item[data-category="${selectedCategory}"]`).forEach(item => {
            item.classList.add('show');
          });
          
          // Also show general (all) items
          document.querySelectorAll('.faq-item[data-category="all"]').forEach(item => {
            item.classList.add('show');
          });
        }
      });
    });
    
    function showAllQuestions() {
      faqItems.forEach(item => {
        item.classList.add('show');
        item.classList.remove('active');
      });
    }
  });
</script>

@endsection