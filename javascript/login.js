(() => {
    'use strict'

    const form = document.querySelector('.needs-validation');
    const loginSuccessModal = new bootstrap.Modal(document.getElementById('loginSuccessModal'));
    const welcomeBackMessage = document.getElementById('welcomeBackMessage');
    const emailInput = document.getElementById('email');

    if (form) {
        form.addEventListener('submit', function (event) {
            
            if (!form.checkValidity()) {
                event.preventDefault(); 
                event.stopPropagation();
            } else {
                event.preventDefault(); 
                event.stopPropagation();
                
                const email = emailInput.value;
                let username = "User";
                if (email.includes('@')) {
                    username = email.substring(0, email.indexOf('@'));
                    username = username.charAt(0).toUpperCase() + username.slice(1);
                }

                if (welcomeBackMessage) {
                    welcomeBackMessage.innerHTML = `Welcome back, **${username}**! You have successfully logged in to your GreenRay account.`;
                }

                loginSuccessModal.show();
            }

            form.classList.add('was-validated');
        }, false);
    }
})()