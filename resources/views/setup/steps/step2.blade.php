<!-- Step 2 - Professional Details -->
<style>
.step-container {
    max-width: 100%;
    margin: 0 auto;
    padding: 0 15px;
}

.step-header {
    text-align: center;
    margin-bottom: 30px;
}

.step-title {
    font-size: 24px;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 8px;
}

.step-subtitle {
    font-size: 14px;
    color: #6c757d;
}

.professional-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 15px;
}

.input-wrapper {
    position: relative;
}

.input-icon {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
    font-size: 18px;
    pointer-events: none;
}

.form-control-professional {
    width: 100%;
    padding: 14px 15px 14px 45px;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    font-size: 14px;
    transition: all 0.3s ease;
    background-color: white;
}

.form-control-professional:focus {
    outline: none;
    border-color: #db5363;
    box-shadow: 0 0 0 4px rgba(219, 83, 99, 0.1);
}

.form-control-professional::placeholder {
    color: #adb5bd;
}

.info-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    padding: 20px;
    color: white;
    margin-bottom: 25px;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.info-card h6 {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 8px;
}

.info-card p {
    font-size: 13px;
    margin: 0;
    opacity: 0.95;
}

@media (min-width: 768px) {
    .professional-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .grid-full {
        grid-column: 1 / -1;
    }
}

@media (max-width: 767px) {
    .step-title {
        font-size: 20px;
    }
    
    .form-control-professional {
        padding: 12px 12px 12px 40px;
    }
    
    .info-card {
        padding: 15px;
    }
}
</style>

<div class="step-container">
    <div class="step-header">
        <h6 class="step-title">Professional Details</h6>
        <p class="step-subtitle">Tell us about your professional background</p>
    </div>

    <div class="info-card">
        <h6>💼 Build Your Profile</h6>
        <p>Add your professional information to create a complete profile that showcases your expertise and helps others connect with you.</p>
    </div>

    <div class="professional-grid">
        <div class="input-wrapper">
            <span class="input-icon">💼</span>
            <input 
                placeholder="Job Title" 
                type="text" 
                class="form-control-professional" 
                name="title" 
                id="title" 
                value=""
            >
        </div>

        <div class="input-wrapper">
            <span class="input-icon">🏢</span>
            <input 
                placeholder="Company / Organization" 
                type="text" 
                class="form-control-professional" 
                name="company" 
                id="company" 
                value=""
            >
        </div>

        <div class="input-wrapper">
            <span class="input-icon">📍</span>
            <input 
                placeholder="Location" 
                type="text" 
                class="form-control-professional" 
                name="location" 
                id="location" 
                value=""
            >
        </div>

        <div class="input-wrapper">
            <span class="input-icon">🌍</span>
            <input 
                placeholder="Country" 
                type="text" 
                class="form-control-professional" 
                name="country" 
                id="country" 
                value=""
            >
        </div>

        <div class="input-wrapper grid-full">
            <span class="input-icon">✉️</span>
            <input 
                placeholder="E-Mail" 
                type="email" 
                class="form-control-professional" 
                name="email" 
                id="email" 
                value=""
            >
        </div>

        <div class="input-wrapper grid-full">
            <span class="input-icon">📱</span>
            <input 
                placeholder="Phone Number" 
                type="text" 
                class="form-control-professional" 
                name="mobile" 
                id="mobile" 
                value=""
            >
        </div>

        <div class="input-wrapper grid-full">
            <span class="input-icon">👔</span>
            <input 
                placeholder="Role" 
                type="text" 
                class="form-control-professional" 
                name="role" 
                id="role" 
                value=""
            >
        </div>
    </div>
</div>