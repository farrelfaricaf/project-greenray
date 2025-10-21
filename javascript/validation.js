(() => {
    'use strict'

    const form = document.querySelector('.needs-validation');
    const signupSuccessModal = new bootstrap.Modal(document.getElementById('signupSuccessModal'));

    const welcomeMessageElement = document.getElementById('welcomeMessage');
    const firstNameInput = document.getElementById('firstName');
    const lastNameInput = document.getElementById('lastName');

    function validateNameInput(inputElement) {
        const value = inputElement.value;
        const invalidFeedback = inputElement.nextElementSibling;
        const validNameRegex = /^[a-zA-Z\s]*$/; 
        
        if (!validNameRegex.test(value)) {
            inputElement.setCustomValidity("Name must contain only letters and spaces.");
            invalidFeedback.textContent = "Your name should only contain letters (A-Z) and spaces.";
        } else if (value.trim() === "") {
            inputElement.setCustomValidity("Please enter your name.");
            invalidFeedback.textContent = "Please enter your name.";
        } else {
            inputElement.setCustomValidity(""); 
        }

        if (form.classList.contains('was-validated') || value.length > 0) {
            if (inputElement.checkValidity()) {
                inputElement.classList.remove('is-invalid');
                inputElement.classList.add('is-valid'); 
            } else {
                inputElement.classList.remove('is-valid');
                inputElement.classList.add('is-invalid');
            }
        }
    }

    function capitalizeFirstLetter(inputElement) {
        let value = inputElement.value.toLowerCase().trim();
        if (value) {
            value = value.split(' ')
                .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                .join(' ');
            inputElement.value = value;
        }
        validateNameInput(inputElement); 
    }


    firstNameInput.addEventListener('input', () => validateNameInput(firstNameInput));
    firstNameInput.addEventListener('blur', () => capitalizeFirstLetter(firstNameInput));

    lastNameInput.addEventListener('input', () => validateNameInput(lastNameInput));
    lastNameInput.addEventListener('blur', () => capitalizeFirstLetter(lastNameInput));
    
    if (form) {
        form.addEventListener('submit', function (event) {
            
            validateNameInput(firstNameInput);
            validateNameInput(lastNameInput);
            
            if (!form.checkValidity()) {
                event.preventDefault(); 
                event.stopPropagation();
            } else {
                event.preventDefault(); 
                event.stopPropagation();
                
                const firstName = firstNameInput.value;
                
                if (firstName && welcomeMessageElement) {
                    welcomeMessageElement.textContent = `Welcome to GreenRay, ${firstName}!`;
                }

                signupSuccessModal.show();
            }

            form.classList.add('was-validated');
        }, false);
    }
})()