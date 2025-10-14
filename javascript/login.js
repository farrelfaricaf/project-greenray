(() => {
    'use strict'

    // Ambil elemen-elemen yang dibutuhkan
    const form = document.querySelector('.needs-validation');
    const loginSuccessModal = new bootstrap.Modal(document.getElementById('loginSuccessModal'));
    const welcomeBackMessage = document.getElementById('welcomeBackMessage');
    const emailInput = document.getElementById('email');

    if (form) {
        form.addEventListener('submit', function (event) {
            
            // Cek validitas form menggunakan fitur bawaan browser/Bootstrap
            if (!form.checkValidity()) {
                event.preventDefault(); // Mencegah submit jika tidak valid
                event.stopPropagation();
            } else {
                // Jika form valid,
                event.preventDefault(); // Mencegah form submit/reload
                event.stopPropagation();
                
                // Ambil bagian lokal (sebelum '@') dari email untuk digunakan sebagai nama
                const email = emailInput.value;
                // Cek apakah email memiliki format yang benar sebelum mencoba substring
                let username = "User";
                if (email.includes('@')) {
                    username = email.substring(0, email.indexOf('@'));
                    // Buat huruf pertama kapital (opsional)
                    username = username.charAt(0).toUpperCase() + username.slice(1);
                }

                // Perbarui pesan selamat datang di modal
                if (welcomeBackMessage) {
                    welcomeBackMessage.innerHTML = `Welcome back, **${username}**! You have successfully logged in to your GreenRay account.`;
                }

                // Tampilkan modal
                loginSuccessModal.show();
            }

            // Tambahkan class 'was-validated' untuk menampilkan visual feedback
            form.classList.add('was-validated');
        }, false);
    }
})()