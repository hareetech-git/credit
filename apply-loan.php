
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Styles -->
    <style>
        :root {
            --primary-color: #dc2626;
            --primary-dark: #b91c1c;
            --secondary-color: #1e293b;
            --light-bg: #f8fafc;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'jost' serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
        }
        
        .loan-application-section {
            padding: 30px 0;
            min-height: 100vh;
        }
        
        .application-container {
            max-width: 940px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .application-header {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .application-header h1 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 8px;
        }
        
        .application-header p {
            color: #64748b;
            font-size: 0.95rem;
            max-width: 500px;
            margin: 0 auto;
        }
        
        .progress-steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
            position: relative;
            max-width: 700px;
            margin: 0 auto 30px;
        }
        
        .progress-steps::before {
            content: '';
            position: absolute;
            top: 18px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e2e8f0;
            z-index: 1;
        }
        
        .step {
            text-align: center;
            position: relative;
            z-index: 2;
            flex: 1;
        }
        
        .step-circle {
            width: 32px;
            height: 32px;
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 6px;
            font-weight: 600;
            font-size: 0.85rem;
            color: #94a3b8;
            transition: all 0.3s ease;
        }
        
        .step.active .step-circle {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }
        
        .step-label {
            font-size: 0.75rem;
            color: #64748b;
            font-weight: 500;
        }
        
        .step.active .step-label {
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .application-form-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            max-height: 600px;
            overflow-y: auto;
        }
        
        .form-section {
            padding: 25px;
            display: none;
        }
        
        .form-section.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .section-header {
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f1f5f9;
        }
        
        .section-header h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 4px;
        }
        
        .section-header p {
            color: #64748b;
            font-size: 0.85rem;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }
        
        .form-group {
            margin-bottom: 16px;
        }
        
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        
        .form-label {
            display: block;
            margin-bottom: 5px;
            color: #334155;
            font-weight: 600;
            font-size: 0.85rem;
        }
        
        .form-label .required {
            color: var(--primary-color);
            margin-left: 2px;
        }
        
        .form-control {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            background: #f8fafc;
            height: 36px;
        }
        
        textarea.form-control {
            height: auto;
            min-height: 80px;
            resize: vertical;
            font-size: 0.85rem;
            padding: 8px 10px;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
            background: white;
        }
        
        /* Number input specific styles */
        input[type="number"] {
            -moz-appearance: textfield;
        }
        
        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        
        .number-input {
            position: relative;
        }
        
        .number-input .form-control {
            padding-left: 26px;
        }
        
        .rupee-symbol {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            font-weight: 500;
            font-size: 0.85rem;
            z-index: 2;
        }
        
        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%2394a3b8' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 12px;
            padding-right: 30px;
            font-size: 0.85rem;
        }
        
        .form-help {
            font-size: 0.75rem;
            color: #94a3b8;
            margin-top: 3px;
        }
        
        .emi-calculator {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border: 1px solid #fecaca;
            border-radius: 10px;
            padding: 16px;
            margin-top: 16px;
        }
        
        .emi-result {
            text-align: center;
            padding: 12px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.04);
        }
        
        .emi-amount {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 6px;
        }
        
        .emi-details {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-top: 12px;
        }
        
        .emi-detail-item {
            text-align: center;
            padding: 10px;
            background: white;
            border-radius: 6px;
            border: 1px solid #f1f5f9;
        }
        
        .emi-detail-label {
            font-size: 0.7rem;
            color: #64748b;
            margin-bottom: 3px;
        }
        
        .emi-detail-value {
            font-size: 0.9rem;
            font-weight: 600;
            color: #1e293b;
        }
        
        .form-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #f1f5f9;
        }
        
        .btn-prev, .btn-next {
            padding: 8px 20px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            font-size: 0.85rem;
        }
        
        .btn-prev {
            background: #f1f5f9;
            color: #64748b;
        }
        
        .btn-prev:hover {
            background: #e2e8f0;
        }
        
        .btn-next {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            box-shadow: 0 4px 10px rgba(220, 38, 38, 0.25);
        }
        
        .btn-next:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 14px rgba(220, 38, 38, 0.35);
        }
        
        .btn-submit {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.25);
        }
        
        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 14px rgba(16, 185, 129, 0.35);
        }
        
        /* Alerts */
        .alert {
            padding: 10px 14px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 0.85rem;
            border: none;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        }
        
        .alert-success {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
            border-left: 3px solid #10b981;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
            border-left: 3px solid #ef4444;
        }
        
        .alert i {
            margin-right: 6px;
            font-size: 1rem;
        }
        
        /* Form check */
        .form-check {
            display: flex;
            align-items: flex-start;
            gap: 6px;
            margin-top: 4px;
        }
        
        .form-check-input {
            width: 14px;
            height: 14px;
            margin-top: 3px;
            flex-shrink: 0;
        }
        
        .form-check-label {
            font-size: 0.8rem;
            color: #64748b;
            line-height: 1.3;
        }
        
        .form-check-label a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }
        
        .form-check-label a:hover {
            text-decoration: underline;
        }
        
        /* Custom scrollbar for form container */
        .application-form-container::-webkit-scrollbar {
            width: 6px;
        }
        
        .application-form-container::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        
        .application-form-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        
        .application-form-container::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .form-grid {
                grid-template-columns: 1fr;
                gap: 14px;
            }
            
            .form-section {
                padding: 20px;
            }
            
            .emi-details {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .application-form-container {
                max-height: 550px;
            }
        }
        
        @media (max-width: 768px) {
            .loan-application-section {
                padding: 20px 0;
            }
            
            .application-header h1 {
                font-size: 1.6rem;
            }
            
            .progress-steps {
                max-width: 600px;
                margin-bottom: 25px;
            }
            
            .step-circle {
                width: 30px;
                height: 30px;
                font-size: 0.8rem;
            }
            
            .step-label {
                font-size: 0.7rem;
            }
            
            .emi-details {
                grid-template-columns: 1fr;
            }
            
            .form-actions {
                flex-direction: column;
                gap: 10px;
            }
            
            .btn-prev, .btn-next {
                width: 100%;
            }
            
            .application-form-container {
                max-height: 500px;
            }
        }
        
        @media (max-width: 576px) {
            .form-section {
                padding: 16px;
            }
            
            .application-header h1 {
                font-size: 1.4rem;
            }
            
            .section-header h3 {
                font-size: 1rem;
            }
            
            .emi-amount {
                font-size: 1.3rem;
            }
            
            .form-control {
                padding: 6px 8px;
                font-size: 0.8rem;
                height: 34px;
            }
            
            textarea.form-control {
                min-height: 60px;
                padding: 6px 8px;
            }
            
            .rupee-symbol {
                left: 8px;
                font-size: 0.8rem;
            }
            
            .number-input .form-control {
                padding-left: 22px;
            }
            
            select.form-control {
                padding-right: 26px;
                background-position: right 8px center;
            }
            
            .application-container {
                padding: 0 15px;
            }
        }
        
        /* Input focus states */
        .form-control.is-invalid {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }
        
        .form-control.is-valid {
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }
    </style>
</head>
<body>
    <?php require_once 'includes/header.php'; ?>
    
    <section class="loan-application-section">
        <div class="container application-container">
            <div class="application-header">
                <h1>Apply for Loan</h1>
                <p>Complete the application form below to get your loan approved quickly</p>
            </div>
            
            <div class="progress-steps">
                <div class="step active" data-step="1">
                    <div class="step-circle">1</div>
                    <div class="step-label">Personal Info</div>
                </div>
                <div class="step" data-step="2">
                    <div class="step-circle">2</div>
                    <div class="step-label">Income Details</div>
                </div>
                <div class="step" data-step="3">
                    <div class="step-circle">3</div>
                    <div class="step-label">Loan Details</div>
                </div>
                <div class="step" data-step="4">
                    <div class="step-circle">4</div>
                    <div class="step-label">References</div>
                </div>
            </div>
            
            <div class="application-form-container">
                <form id="loanApplicationForm" method="POST" action="#">
                    <!-- Step 1: Personal Information -->
                    <div class="form-section active" id="step2">
                        <div class="section-header">
                            <h3>Personal Information</h3>
                            <p>Enter your personal details and identification information</p>
                        </div>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Full Name <span class="required">*</span></label>
                                <input type="text" name="full_name" class="form-control" 
                                       placeholder="Enter your full name" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Email Address <span class="required">*</span></label>
                                <input type="email" name="email" class="form-control" 
                                       placeholder="Enter your email" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Phone Number <span class="required">*</span></label>
                                <input type="tel" name="phone" class="form-control" 
                                       placeholder="10-digit mobile number"
                                       pattern="[0-9]{10}" maxlength="10" required>
                                <div class="form-help">10-digit mobile number</div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Date of Birth <span class="required">*</span></label>
                                <input type="date" name="dob" class="form-control" 
                                       max="" required>
                                <div class="form-help">Must be 18 years or older</div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Gender <span class="required">*</span></label>
                                <select name="gender" class="form-control" required>
                                    <option value="">Select Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">PAN Number <span class="required">*</span></label>
                                <input type="text" name="pan_number" class="form-control" 
                                       placeholder="ABCDE1234F"
                                       pattern="[A-Z]{5}[0-9]{4}[A-Z]{1}" required>
                                <div class="form-help">Format: ABCDE1234F</div>
                            </div>
                        </div>
                        
                        <!-- Aadhar and Address in same row -->
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Aadhar Number <span class="required">*</span></label>
                                <input type="text" name="aadhar_number" class="form-control" 
                                       placeholder="12-digit Aadhar number"
                                       pattern="[0-9]{12}" maxlength="12" required>
                                <div class="form-help">12-digit Aadhar number</div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Complete Address <span class="required">*</span></label>
                                <textarea name="address" class="form-control" rows="2" 
                                          placeholder="Enter your complete address" required></textarea>
                            </div>
                        </div>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">City <span class="required">*</span></label>
                                <input type="text" name="city" class="form-control" 
                                       placeholder="Enter city" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">State <span class="required">*</span></label>
                                <input type="text" name="state" class="form-control" 
                                       placeholder="Enter state" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Pincode <span class="required">*</span></label>
                                <input type="text" name="pincode" class="form-control" 
                                       placeholder="6-digit pincode"
                                       pattern="[0-9]{6}" maxlength="6" required>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="button" class="btn-prev" disabled>Previous</button>
                            <button type="button" class="btn-next" data-next="2">Next: Income Details</button>
                        </div>
                    </div>
                    
                    <!-- Step 2: Income Details -->
                    <div class="form-section" id="step1">
                        <div class="section-header">
                            <h3>Income & Employment Details</h3>
                            <p>Provide information about your employment and income sources</p>
                        </div>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Employment Status <span class="required">*</span></label>
                                <select name="employment_status" class="form-control" required>
                                    <option value="">Select Status</option>
                                    <option value="salaried">Salaried</option>
                                    <option value="self_employed">Self Employed</option>
                                    <option value="business">Business Owner</option>
                                    <option value="professional">Professional</option>
                                    <option value="retired">Retired</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Company/Business Name</label>
                                <input type="text" name="company_name" class="form-control" 
                                       placeholder="Enter company name">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Designation/Profession</label>
                                <input type="text" name="designation" class="form-control" 
                                       placeholder="Enter designation">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Work Experience (Years)</label>
                                <input type="text" name="work_experience" class="form-control" 
                                       placeholder="Enter years"
                                       pattern="[0-9]*" inputmode="numeric">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Source of Income <span class="required">*</span></label>
                                <select name="source_of_income" class="form-control" required>
                                    <option value="">Select Source</option>
                                    <option value="salary">Salary</option>
                                    <option value="business">Business</option>
                                    <option value="professional">Professional Practice</option>
                                    <option value="agriculture">Agriculture</option>
                                    <option value="rental">Rental Income</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Monthly Salary/Income (₹) <span class="required">*</span></label>
                                <div class="number-input">
                                    <span class="rupee-symbol">₹</span>
                                    <input type="text" name="monthly_salary" class="form-control" 
                                           placeholder="Enter amount"
                                           pattern="[0-9]*" inputmode="numeric" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Additional Monthly Income (₹)</label>
                                <div class="number-input">
                                    <span class="rupee-symbol">₹</span>
                                    <input type="text" name="additional_income" class="form-control" 
                                           placeholder="Enter amount"
                                           pattern="[0-9]*" inputmode="numeric">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Monthly Obligations (₹)</label>
                                <div class="number-input">
                                    <span class="rupee-symbol">₹</span>
                                    <input type="text" name="monthly_obligations" class="form-control" 
                                           placeholder="Enter amount"
                                           pattern="[0-9]*" inputmode="numeric">
                                    <div class="form-help">Existing EMI, rent, etc.</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="button" class="btn-prev" data-prev="1">Previous</button>
                            <button type="button" class="btn-next" data-next="3">Next: Loan Details</button>
                        </div>
                    </div>
                    
                    <!-- Step 3: Loan Details -->
                    <div class="form-section" id="step3">
                        <div class="section-header">
                            <h3>Loan Requirements</h3>
                            <p>Specify the loan amount, purpose, and tenure</p>
                        </div>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Loan Type <span class="required">*</span></label>
                                <select name="loan_type" id="loanType" class="form-control" required>
                                    <option value="">Select Loan Type</option>
                                    <option value="personal" data-rate="10.5">Personal Loan</option>
                                    <option value="business" data-rate="12.0">Business Loan</option>
                                    <option value="home" data-rate="8.5">Home Loan</option>
                                    <option value="vehicle" data-rate="9.5">Vehicle Loan</option>
                                    <option value="education" data-rate="9.0">Education Loan</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Loan Amount (₹) <span class="required">*</span></label>
                                <div class="number-input">
                                    <span class="rupee-symbol">₹</span>
                                    <input type="text" name="loan_amount" id="loanAmount" class="form-control" 
                                           placeholder="Enter loan amount"
                                           pattern="[0-9]*" inputmode="numeric" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Loan Tenure (Years) <span class="required">*</span></label>
                                <input type="text" name="tenure_years" id="loanTenure" class="form-control" 
                                       placeholder="Enter years (1-30)"
                                       pattern="[0-9]*" inputmode="numeric" required>
                            </div>
                        </div>
                        
                        <div class="form-group full-width">
                            <label class="form-label">Purpose of Loan</label>
                            <textarea name="loan_purpose" class="form-control" rows="2" 
                                      placeholder="Describe the purpose of your loan"></textarea>
                        </div>
                        
                        <!-- EMI Calculator -->
                        <div class="emi-calculator">
                            <h4 style="margin-bottom: 10px; color: #1e293b; font-size: 0.9rem;">EMI Calculator</h4>
                            <div class="emi-result">
                                <div class="emi-amount" id="emiAmount">₹0</div>
                                <div style="color: #64748b; margin-bottom: 12px; font-size: 0.8rem;">Monthly Installment</div>
                                
                                <div class="emi-details">
                                    <div class="emi-detail-item">
                                        <div class="emi-detail-label">Loan Amount</div>
                                        <div class="emi-detail-value" id="displayLoanAmount">₹0</div>
                                    </div>
                                    <div class="emi-detail-item">
                                        <div class="emi-detail-label">Interest Rate</div>
                                        <div class="emi-detail-value" id="displayInterestRate">0%</div>
                                    </div>
                                    <div class="emi-detail-item">
                                        <div class="emi-detail-label">Tenure</div>
                                        <div class="emi-detail-value" id="displayTenure">0 years</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="button" class="btn-prev" data-prev="2">Previous</button>
                            <button type="button" class="btn-next" data-next="4">Next: References</button>
                        </div>
                    </div>
                    
                    <!-- Step 4: References -->
                    <div class="form-section" id="step4">
                        <div class="section-header">
                            <h3>References</h3>
                            <p>Provide contact details of two references</p>
                        </div>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Reference 1 Name</label>
                                <input type="text" name="reference_name1" class="form-control" 
                                       placeholder="Enter reference name">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Reference 1 Phone</label>
                                <input type="tel" name="reference_phone1" class="form-control" 
                                       placeholder="10-digit mobile number"
                                       pattern="[0-9]{10}" maxlength="10">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Reference 2 Name</label>
                                <input type="text" name="reference_name2" class="form-control" 
                                       placeholder="Enter reference name">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Reference 2 Phone</label>
                                <input type="tel" name="reference_phone2" class="form-control" 
                                       placeholder="10-digit mobile number"
                                       pattern="[0-9]{10}" maxlength="10">
                            </div>
                        </div>
                        
                        <div class="form-group full-width" style="margin-top: 20px;">
                            <div class="form-check">
                                <input type="checkbox" name="terms" id="terms" class="form-check-input" required>
                                <label for="terms" class="form-check-label">
                                    I hereby declare that the information provided is true and correct to the best of my knowledge. 
                                    I agree to the <a href="terms.php">terms and conditions</a> of Udhaar Capital.
                                </label>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="button" class="btn-prev" data-prev="3">Previous</button>
                            <button type="submit" class="btn-submit">
                                <i class="fas fa-paper-plane me-2"></i> Submit Application
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Set max date for DOB (18 years ago)
            const today = new Date();
            const minDate = new Date(today.getFullYear() - 18, today.getMonth(), today.getDate());
            document.querySelector('input[name="dob"]').max = minDate.toISOString().split('T')[0];
            
            // Multi-step form functionality
            const steps = document.querySelectorAll('.step');
            const formSections = document.querySelectorAll('.form-section');
            const nextButtons = document.querySelectorAll('.btn-next');
            const prevButtons = document.querySelectorAll('.btn-prev');
            const progressSteps = document.querySelectorAll('.step');
            
            let currentStep = 1;
            
            // Update progress steps
            function updateProgress() {
                progressSteps.forEach(step => {
                    const stepNum = parseInt(step.dataset.step);
                    if (stepNum < currentStep) {
                        step.classList.add('completed');
                        step.classList.add('active');
                    } else if (stepNum === currentStep) {
                        step.classList.add('active');
                        step.classList.remove('completed');
                    } else {
                        step.classList.remove('active', 'completed');
                    }
                });
            }
            
            // Show current step
            function showStep(stepNumber) {
                formSections.forEach(section => {
                    section.classList.remove('active');
                });
                
                const currentSection = document.getElementById(`step${stepNumber}`);
                if (currentSection) {
                    currentSection.classList.add('active');
                    currentStep = stepNumber;
                    updateProgress();
                    
                    // Scroll form container to top when changing steps
                    const formContainer = document.querySelector('.application-form-container');
                    formContainer.scrollTop = 0;
                }
            }
            
            // Next button click
            nextButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const nextStep = parseInt(this.dataset.next);
                    if (validateCurrentStep(currentStep)) {
                        showStep(nextStep);
                    }
                });
            });
            
            // Previous button click
            prevButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const prevStep = parseInt(this.dataset.prev);
                    showStep(prevStep);
                });
            });
            
            // Step validation
            function validateCurrentStep(step) {
                const currentSection = document.getElementById(`step${step}`);
                const requiredInputs = currentSection.querySelectorAll('[required]');
                let isValid = true;
                
                requiredInputs.forEach(input => {
                    if (!input.value.trim()) {
                        input.classList.add('is-invalid');
                        isValid = false;
                        
                        // Scroll to first error
                        if (isValid === false) {
                            input.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            input.focus();
                        }
                    } else {
                        input.classList.remove('is-invalid');
                        
                        // Additional validation for specific fields
                        if (input.name === 'phone' && !/^\d{10}$/.test(input.value)) {
                            alert('Please enter a valid 10-digit phone number');
                            input.focus();
                            isValid = false;
                        }
                        
                        if (input.name === 'pan_number' && !/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/.test(input.value.toUpperCase())) {
                            alert('Please enter a valid PAN number (Format: ABCDE1234F)');
                            input.focus();
                            isValid = false;
                        }
                        
                        if (input.name === 'aadhar_number' && !/^\d{12}$/.test(input.value)) {
                            alert('Please enter a valid 12-digit Aadhar number');
                            input.focus();
                            isValid = false;
                        }
                        
                        // Number field validation
                        if (input.hasAttribute('pattern') && input.pattern === '[0-9]*') {
                            if (!/^\d+$/.test(input.value.replace(/,/g, ''))) {
                                alert('Please enter only numbers');
                                input.focus();
                                isValid = false;
                            }
                        }
                    }
                });
                
                if (!isValid) {
                    alert('Please fill all required fields correctly.');
                }
                
                return isValid;
            }
            
            // EMI Calculation
            function calculateEMI() {
                const loanAmount = parseFloat(document.getElementById('loanAmount').value.replace(/,/g, '')) || 0;
                const loanType = document.getElementById('loanType');
                const selectedOption = loanType.options[loanType.selectedIndex];
                const interestRate = parseFloat(selectedOption?.dataset.rate) || 10.5;
                const tenureYears = parseFloat(document.getElementById('loanTenure').value.replace(/,/g, '')) || 5;
                
                // Validate inputs
                if (loanAmount < 10000 || tenureYears < 1 || tenureYears > 30) {
                    return;
                }
                
                const monthlyRate = (interestRate / 12) / 100;
                const tenureMonths = tenureYears * 12;
                
                let emi = 0;
                if (monthlyRate > 0) {
                    emi = (loanAmount * monthlyRate * Math.pow(1 + monthlyRate, tenureMonths)) / 
                          (Math.pow(1 + monthlyRate, tenureMonths) - 1);
                } else {
                    emi = loanAmount / tenureMonths;
                }
                
                // Format numbers with Indian numbering system
                function formatIndianNumber(num) {
                    return num.toLocaleString('en-IN', {
                        maximumFractionDigits: 2,
                        minimumFractionDigits: 2
                    });
                }
                
                // Update display
                document.getElementById('emiAmount').textContent = '₹' + formatIndianNumber(emi);
                document.getElementById('displayLoanAmount').textContent = '₹' + loanAmount.toLocaleString('en-IN');
                document.getElementById('displayInterestRate').textContent = interestRate + '%';
                document.getElementById('displayTenure').textContent = tenureYears + ' years';
            }
            
            // Number formatting for input fields
            function formatNumberInput(input) {
                input.addEventListener('input', function() {
                    // Remove all non-digit characters
                    let value = this.value.replace(/[^0-9]/g, '');
                    
                    // Store cursor position
                    const cursorPosition = this.selectionStart;
                    
                    // Format with commas
                    if (value) {
                        const formattedValue = parseInt(value).toLocaleString('en-IN');
                        this.value = formattedValue;
                        
                        // Adjust cursor position
                        const diff = formattedValue.length - this.value.length;
                        this.setSelectionRange(cursorPosition + diff, cursorPosition + diff);
                    } else {
                        this.value = '';
                    }
                    
                    // Trigger EMI calculation if it's loan amount or tenure
                    if (this.id === 'loanAmount' || this.id === 'loanTenure') {
                        calculateEMI();
                    }
                });
            }
            
            // Format number on blur (when user leaves field)
            function formatNumberOnBlur(input) {
                input.addEventListener('blur', function() {
                    let value = this.value.replace(/[^0-9]/g, '');
                    if (value) {
                        this.value = parseInt(value).toLocaleString('en-IN');
                    }
                });
                
                input.addEventListener('focus', function() {
                    let value = this.value.replace(/[^0-9]/g, '');
                    if (value) {
                        this.value = value;
                    }
                });
            }
            
            // Apply formatting to all number input fields
            const numberFields = document.querySelectorAll('input[pattern="[0-9]*"]');
            numberFields.forEach(input => {
                formatNumberInput(input);
                formatNumberOnBlur(input);
            });
            
            // Attach EMI calculation events
            document.getElementById('loanAmount').addEventListener('input', calculateEMI);
            document.getElementById('loanType').addEventListener('change', calculateEMI);
            document.getElementById('loanTenure').addEventListener('input', calculateEMI);
            
            // Form submission
            document.getElementById('loanApplicationForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                if (!validateCurrentStep(currentStep)) {
                    alert('Please complete all steps correctly.');
                    return;
                }
                
                if (!document.getElementById('terms').checked) {
                    alert('Please accept the terms and conditions.');
                    return;
                }
                
                // Show success message
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-success';
                alertDiv.innerHTML = `
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <strong>Application Submitted Successfully!</strong><br>
                        Thank you for applying. Our team will contact you within 24 hours.
                    </div>
                `;
                
                // Insert alert before form
                const formContainer = document.querySelector('.application-form-container');
                formContainer.parentNode.insertBefore(alertDiv, formContainer);
                
                // Scroll to top
                window.scrollTo({ top: 0, behavior: 'smooth' });
                
                // Reset form after 5 seconds
                setTimeout(() => {
                    this.reset();
                    showStep(1);
                    calculateEMI(); // Reset EMI calculator
                    alertDiv.remove();
                }, 5000);
            });
            
            // Auto-uppercase for PAN number
            const panInput = document.querySelector('input[name="pan_number"]');
            panInput.addEventListener('input', function() {
                this.value = this.value.toUpperCase();
            });
            
            // Initialize EMI calculation
            calculateEMI();
            
            // Initialize first step
            updateProgress();
        });
    </script>
    
    <?php require_once 'includes/footer.php'; ?>
</body>
</html>