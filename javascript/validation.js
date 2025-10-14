(() => {
    'use strict'

    // Ambil elemen-elemen yang dibutuhkan
    const form = document.querySelector('.needs-validation');
    const signupSuccessModal = new bootstrap.Modal(document.getElementById('signupSuccessModal'));

    const welcomeMessageElement = document.getElementById('welcomeMessage');
    const firstNameInput = document.getElementById('firstName');
    const lastNameInput = document.getElementById('lastName');

    // --- FUNGSI UNTUK VALIDASI DAN FORMAT ---

    /**
     * Memastikan hanya huruf yang diizinkan, menangani pesan error kustom,
     * dan mengupdate status validasi (merah/hijau) secara real-time.
     * Dipanggil pada event 'input'.
     */
    function validateNameInput(inputElement) {
        const value = inputElement.value;
        const invalidFeedback = inputElement.nextElementSibling;
        const validNameRegex = /^[a-zA-Z\s]*$/; // Hanya huruf dan spasi yang diizinkan
        
        // 1. Periksa validitas menggunakan Regex
        if (!validNameRegex.test(value)) {
            // TIDAK VALID: Mengandung karakter ilegal
            inputElement.setCustomValidity("Name must contain only letters and spaces.");
            invalidFeedback.textContent = "Your name should only contain letters (A-Z) and spaces.";
        } else if (value.trim() === "") {
            // TIDAK VALID: Kosong (sesuai required)
            inputElement.setCustomValidity("Please enter your name.");
            invalidFeedback.textContent = "Please enter your name.";
        } else {
            // VALID: Lolos semua pemeriksaan
            inputElement.setCustomValidity(""); // Hapus pesan error kustom
        }

        // 2. Terapkan kelas visual (Real-time Feedback)
        // Kita hanya perlu melakukan ini jika form sudah pernah dicoba submit (mengandung .was-validated)
        // atau kita ingin memaksa feedback muncul setelah input pertama.
        if (form.classList.contains('was-validated') || value.length > 0) {
            if (inputElement.checkValidity()) {
                inputElement.classList.remove('is-invalid');
                inputElement.classList.add('is-valid'); // Opsional: untuk border hijau jika valid
            } else {
                inputElement.classList.remove('is-valid');
                inputElement.classList.add('is-invalid');
            }
        }
    }

    /**
     * Mengubah huruf pertama dari setiap kata menjadi kapital saat blur.
     * Dipanggil pada event 'blur'.
     */
    function capitalizeFirstLetter(inputElement) {
        let value = inputElement.value.toLowerCase().trim();
        if (value) {
            value = value.split(' ')
                .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                .join(' ');
            inputElement.value = value;
        }
        // Setelah blur, panggil lagi validasi untuk mengupdate status visual
        validateNameInput(inputElement); 
    }


    // --- EVENT LISTENERS ---

    // 1. Validasi Real-time dan Kapitalisasi untuk First Name
    firstNameInput.addEventListener('input', () => validateNameInput(firstNameInput));
    firstNameInput.addEventListener('blur', () => capitalizeFirstLetter(firstNameInput));

    // 2. Validasi Real-time dan Kapitalisasi untuk Last Name
    lastNameInput.addEventListener('input', () => validateNameInput(lastNameInput));
    lastNameInput.addEventListener('blur', () => capitalizeFirstLetter(lastNameInput));
    
    // 3. Validasi Form saat Submit (Logic Modal)
    if (form) {
        form.addEventListener('submit', function (event) {
            
            // Panggil fungsi validasi manual untuk input lain (Email & Password)
            // Note: Email dan Password akan menggunakan validasi HTML5/Bootstrap default
            
            // Panggil validasi custom untuk nama
            validateNameInput(firstNameInput);
            validateNameInput(lastNameInput);
            
            // Cek validitas form
            if (!form.checkValidity()) {
                event.preventDefault(); 
                event.stopPropagation();
            } else {
                // Jika form valid,
                event.preventDefault(); 
                event.stopPropagation();
                
                // Ambil First Name
                const firstName = firstNameInput.value;
                
                // Perbarui pesan selamat datang di modal
                if (firstName && welcomeMessageElement) {
                    // Hanya perbarui teks di modal jika form benar-benar valid
                    welcomeMessageElement.textContent = `Welcome to GreenRay, ${firstName}!`;
                }

                // Tampilkan modal
                signupSuccessModal.show();
            }

            // Tambahkan class 'was-validated' untuk MENGAKTIFKAN visual feedback
            // Ini akan memastikan input lain (email/password) juga tampil feedbacknya
            form.classList.add('was-validated');
        }, false);
    }
})()