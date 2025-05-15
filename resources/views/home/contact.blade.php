@extends('home\layout\usehome')
@section('title','Contact Us')
@section('content')
<div class="contact-demo-container">
    <div class="demo-form-wrapper">
        <div class="form-header">
            <h1 class="form-title">Schedule a Personalized Demo</h1>
            <p class="form-subtitle">Experience SmartPunch HRMS in action. Fill out the form and our solutions specialist will contact you shortly.</p>
        </div>

        <form class="professional-contact-form" method="POST" action="{{ route('contact.send') }}">
            @csrf
            <div class="form-grid">
                <div class="form-field">
                    <label for="fullName">Full Name*</label>
                    <div class="input-with-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" id="fullName" name="fullName" required placeholder="Enter your full name">
                    </div>
                </div>

                <div class="form-field">
                    <label for="company">Company Name*</label>
                    <div class="input-with-icon">
                        <i class="fas fa-building"></i>
                        <input type="text" id="company" name="company" required placeholder="Your company name">
                    </div>
                </div>

                <div class="form-field">
                    <label for="email">Business Email*</label>
                    <div class="input-with-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" required placeholder="your@company.com">
                    </div>
                </div>

                <div class="form-field">
                    <label for="jobTitle">Job Title*</label>
                    <div class="input-with-icon">
                        <i class="fas fa-briefcase"></i>
                        <input type="text" id="jobTitle" name="jobTitle" required placeholder="Your position">
                    </div>
                </div>

                <div class="form-field">
                    <label for="phone">Phone Number*</label>
                    <div class="phone-input-group">
                        <div class="country-code-select-wrapper">
                            <i class="fas fa-globe-americas"></i>
                            <select id="countryCode" name="countryCode" class="country-code-select">
                                <option value="+962">+962 JO</option>
                                <option value="+966">+966 SA</option>
                                <option value="+971">+971 UAE</option>
                                <option value="+973">+973 BH</option>
                            </select>
                        </div>
                        <div class="input-with-icon">
                            <i class="fas fa-phone"></i>
                            <input type="tel" id="phone" name="phone" required placeholder="Phone number">
                        </div>
                    </div>
                </div>

                <div class="form-field">
                    <label for="employees">Number of Employees*</label>
                    <div class="select-with-icon">
                        <i class="fas fa-users"></i>
                        <select id="employees" name="employees" required>
                            <option value="" disabled selected>Select range</option>
                            <option value="1-10">1-10 employees</option>
                            <option value="11-50">11-50 employees</option>
                            <option value="51-200">51-200 employees</option>
                            <option value="201-500">201-500 employees</option>
                            <option value="501-1000">501-1000 employees</option>
                            <option value="1000+">1000+ employees</option>
                        </select>
                    </div>
                </div>
                <div class="form-field">
                    <label for="country">Country*</label>
                    <div class="select-with-icon">
                        <i class="fas fa-map-marker-alt"></i>
                        <select id="country" name="country" required>
                            <option value="" disabled selected>Select country</option>
                            <option value="Jordan">Jordan</option>
                        </select>
                    </div>
                </div>
                

                <div class="form-field">
                    <label for="industry">Industry*</label>
                    <div class="select-with-icon">
                        <i class="fas fa-industry"></i>
                        <select id="industry" name="industry" required>
                            <option value="" disabled selected>Select industry</option>
                            <option value="Technology">Technology</option>
                            <option value="Finance">Finance & Banking</option>
                            <option value="Healthcare">Healthcare</option>
                            <option value="Education">Education</option>
                            <option value="Retail">Retail</option>
                            <option value="Manufacturing">Manufacturing</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="submit-demo-request">
                    <span>Request Demo</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>

            <div class="sp-form-footer">
                <p class="sp-form-disclaimer">
                    By submitting this form, you agree to our 
                    <a href="#" class="sp-form-policy-link">Privacy Policy</a> 
                    and consent to contact.
                </p>
            </div>
        </form>
    </div>

    <div class="demo-benefits">
        <h3>What to expect from your demo:</h3>
        <ul class="benefits-list">
            <li>
                <div class="benefit-icon"><i class="fas fa-clock"></i></div>
                <div class="benefit-text">30-minute personalized walkthrough</div>
            </li>
            <li>
                <div class="benefit-icon"><i class="fas fa-sliders-h"></i></div>
                <div class="benefit-text">See key features tailored to your needs</div>
            </li>
            <li>
                <div class="benefit-icon"><i class="fas fa-comments"></i></div>
                <div class="benefit-text">Live Q&A with our HRMS expert</div>
            </li>
            <li>
                <div class="benefit-icon"><i class="fas fa-handshake"></i></div>
                <div class="benefit-text">No obligation, no pressure</div>
            </li>
            <li>
                <div class="benefit-icon"><i class="fas fa-tags"></i></div>
                <div class="benefit-text">Pricing options discussion</div>
            </li>
        </ul>
      
    </div>
</div>
@endsection

<style>
/* Font Import */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');

:root {
    --primary-color: #3d75f3;
    --primary-dark: #1a56ff;
    --text-dark: #2a3342;
    --text-medium: #4d5761;
    --text-light: #556987;
    --border-color: #e5e7eb;
    --light-bg: #f8fafc;
    --input-bg: #f9fafb;
    --placeholder: #98a2b3;
}

.contact-demo-container {
    font-family: 'Inter', sans-serif;
    display: flex;
    max-width: 1200px;
    margin: 50px auto;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

.demo-form-wrapper {
    flex: 1;
    padding: 40px;
    background: #fff;
}

.form-header {
    margin-bottom: 30px;
    text-align: center;
}

.form-title {
    color: var(--text-dark);
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 10px;
}

.form-subtitle {
    color: var(--text-light);
    font-size: 16px;
    line-height: 1.5;
    max-width: 500px;
    margin: 0 auto;
}

.professional-contact-form {
    margin-top: 30px;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    margin-bottom: 25px;
}

.form-field {
    margin-bottom: 15px;
}

.form-field label {
    display: block;
    margin-bottom: 8px;
    font-size: 14px;
    font-weight: 500;
    color: var(--text-dark);
}

.input-with-icon,
.select-with-icon,
.country-code-select-wrapper {
    position: relative;
}

.input-with-icon i,
.select-with-icon i,
.country-code-select-wrapper i {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--placeholder);
    font-size: 16px;
    z-index: 1;
}

.input-with-icon input,
.select-with-icon select,
.country-code-select {
    width: 100%;
    padding: 12px 15px 12px 45px;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    font-size: 14px;
    color: var(--text-dark);
    transition: all 0.3s ease;
    background-color: var(--input-bg);
    appearance: none;
}

.country-code-select {
    padding-left: 40px !important;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 10px center;
    background-size: 15px;
}

.input-with-icon input:focus,
.select-with-icon select:focus,
.country-code-select:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(61, 117, 243, 0.15);
    background-color: #fff;
}

.input-with-icon input::placeholder {
    color: var(--placeholder);
}

.phone-input-group {
    display: flex;
    gap: 10px;
}

.country-code-select-wrapper {
    flex: 0 0 120px;
}

.phone-input-group .input-with-icon {
    flex: 1;
}

.form-actions {
    margin-top: 30px;
    text-align: center;
}

.submit-demo-request {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    color: white;
    border: none;
    padding: 15px 30px;
    font-size: 16px;
    font-weight: 600;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 200px;
    box-shadow: 0 4px 6px rgba(61, 117, 243, 0.2);
}

.submit-demo-request:hover {
    background: linear-gradient(135deg, #3368e0 0%, #164bdb 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(61, 117, 243, 0.25);
}

.submit-demo-request i {
    margin-left: 10px;
    transition: transform 0.3s ease;
}

.submit-demo-request:hover i {
    transform: translateX(3px);
}

.sp-form-footer {
    margin-top: 20px;
    padding: 15px 0;
    border-top: 1px solid var(--border-color);
    text-align: center;
}

.sp-form-disclaimer {
    font-size: 12px;
    color: var(--placeholder);
    line-height: 1.5;
    margin: 0;
}

.sp-form-policy-link {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s ease;
}

.sp-form-policy-link:hover {
    text-decoration: underline;
    color: #2a5fd7;
}

.demo-benefits {
    flex: 0 0 350px;
    padding: 40px;
    background: var(--light-bg);
    border-left: 1px solid var(--border-color);
}

.demo-benefits h3 {
    color: var(--text-dark);
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 20px;
}

.benefits-list {
    list-style: none;
    padding: 0;
    margin-bottom: 30px;
}

.benefits-list li {
    margin-bottom: 15px;
    color: var(--text-medium);
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.benefit-icon {
    color: var(--primary-color);
    font-size: 18px;
    margin-top: 2px;
}

.benefit-text {
    flex: 1;
}

.trust-badges {
    display: flex;
    gap: 15px;
    margin-top: 40px;
}

.badge {
    display: flex;
    align-items: center;
    background: #fff;
    padding: 10px 15px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 500;
    color: var(--text-dark);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    gap: 8px;
}

.badge i {
    color: var(--primary-color);
}

/* Responsive Design */
@media (max-width: 900px) {
    .contact-demo-container {
        flex-direction: column;
    }
    
    .demo-benefits {
        border-left: none;
        border-top: 1px solid var(--border-color);
    }
    
    .form-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .demo-form-wrapper,
    .demo-benefits {
        padding: 25px;
    }
    
    .form-title {
        font-size: 24px;
    }
    
    .phone-input-group {
        flex-direction: column;
    }
    
    .country-code-select-wrapper {
        flex: 1;
        width: 100%;
    }
}
</style>