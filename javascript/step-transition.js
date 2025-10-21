window.CALCULATOR_DATA = window.CALCULATOR_DATA || {};

document.addEventListener('DOMContentLoaded', () => {
    let currentStep = 1;

    const steps = document.querySelectorAll('.calc-container.step');
    const stepWrapper = document.querySelector('.step-wrapper');
    const allNextButtons = document.querySelectorAll('.btn-hitung.btn-next');
    const allBackButtons = document.querySelectorAll('.btn-hitung.btn-back');
    const unlockButton = document.getElementById('unlockButton'); 
    
    const alertContainer = document.getElementById('validation-alert-container');

    const showAlert = (message) => {
        alertContainer.innerHTML = ''; 
        const alertHTML = `
            <div class="custom-alert alert-primary " role="alert">
                <svg xmlns="http://www.w3.org/2000/svg" class="bi flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Warning:">
                    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a0.552 0 0 1-1.1 0L7.1 5.995A0.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                </svg>
                <div class="alert-message">
                    ${message}
                </div>
            </div>
        `;

        alertContainer.innerHTML = alertHTML;
        const alertElement = alertContainer.firstChild;
        
        setTimeout(() => {
            if (alertElement) {
                alertElement.classList.add('hidden');
                alertElement.addEventListener('transitionend', () => {
                    if (alertElement.parentElement) {
                        alertElement.parentElement.innerHTML = '';
                    }
                }, { once: true });
            }
        }, 4000); 
    };

    const showStep = (stepNumber) => {
        steps.forEach(step => {
            step.classList.remove('active-step');
        });

        const targetStep = document.getElementById(`step-${stepNumber}`);
        if (targetStep) {
            targetStep.classList.add('active-step');
            currentStep = stepNumber;
            
            if (stepWrapper) {
                const frame139 = document.querySelector('.frame-139');
                const targetY = (frame139 ? frame139.offsetTop : stepWrapper.offsetTop) - 50; 
                
                window.scrollTo({
                    top: targetY, 
                    behavior: 'smooth'
                });
            }
        }
    };
    
    const validateStep = (stepNumber) => {
        const stepId = `step-${stepNumber}`;
        alertContainer.innerHTML = ''; 
        
        if (stepNumber === 1) {
            const billInput = document.querySelector('#billInput'); 
            const dayaDropdown = document.querySelector('#step-1 .static-dropdown .selected-value');
            const lokasiDropdown = document.querySelector('#step-1 .custom-dropdown:not(.static-dropdown) .selected-value');

            if (!billInput || billInput.value.trim() === '') {
                 showAlert('Please enter your average monthly electricity bill.');
                 billInput.focus();
                 return false;
            }

            const dayaValue = dayaDropdown ? dayaDropdown.textContent.trim() : '';
            if (dayaValue === 'Pilih Daya VA' || dayaValue === '') {
                showAlert('Please select your VA Capacity.');
                return false;
            }

            const lokasiValue = lokasiDropdown ? lokasiDropdown.textContent.trim() : '';
            if (lokasiValue === 'Pilih Lokasi' || lokasiValue === '') {
                showAlert('Please select your home location.');
                return false;
            }
            
            window.CALCULATOR_DATA[stepId] = { 
                bill: billInput.value,
                daya: dayaValue,
                lokasi: lokasiValue 
            };
            return true;
        }

        if ([2, 3, 4].includes(stepNumber)) {
            const container = document.getElementById(stepId);
            const selectedValue = container.querySelector('.card-option.active');
            
            if (selectedValue) {
                window.CALCULATOR_DATA[stepId] = selectedValue.dataset.value;
                return true;
            } else {
                showAlert('Please select one option to proceed.');
                return false;
            }
        }
        
        if (stepNumber === 5) {
            const emailInput = document.querySelector('#step-5 #emailInput'); 
            const emailValue = emailInput.value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (!emailRegex.test(emailValue)) {
                showAlert('Please enter a valid email address.');
                emailInput.focus();
                return false;
            }

            window.CALCULATOR_DATA[stepId] = {
                email: emailValue,
            };
            return true; 
        }
        
        return true; 
    };

    allNextButtons.forEach(button => {
        if (button.id === 'unlockButton') return; 

        button.addEventListener('click', function() {
            
            const currentStepElement = this.closest('.calc-container');
            const currentStepNumber = parseInt(currentStepElement.dataset.step);
            const nextStepNumber = parseInt(this.dataset.nextStep);
            
            if (validateStep(currentStepNumber)) {
                if (nextStepNumber) {
                    showStep(nextStepNumber);
                }
            }
        });
    });

    if (unlockButton) {
        unlockButton.addEventListener('click', function(event) {
            event.preventDefault(); 
            
            const currentStepNumber = 5;
            const nextStepNumber = 6;
            
            if (validateStep(currentStepNumber)) { 
                
                const results = calculateSavings(window.CALCULATOR_DATA); 
                
                if (results) {
                    displayResults(results); 
                    showStep(nextStepNumber);
                } else {
                    showAlert('Calculation error. Please ensure all data is correctly entered.');
                }
            }
        });
    }

    allBackButtons.forEach(button => {
        button.addEventListener('click', function() {
            const prevStepNumber = parseInt(this.dataset.prevStep);
            if (prevStepNumber < currentStep) {
                showStep(prevStepNumber);
            }
        });
    });

    if (steps.length > 0) {
         showStep(currentStep);
    }
});