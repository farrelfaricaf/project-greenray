// Menjalankan kode setelah semua konten HTML dimuat
document.addEventListener('DOMContentLoaded', function () {

    // --- Inisialisasi Modal & Variabel ---
    let currentValidForm = null; // Untuk menyimpan form yang siap disubmit
    
    // Inisialisasi Modal Konfirmasi Submit
    const submitModalEl = document.getElementById('submitModal');
    if (!submitModalEl) {
        console.error('Modal #submitModal tidak ditemukan di HTML.');
        return;
    }
    const submitModal = new bootstrap.Modal(submitModalEl);
    const confirmSubmitBtn = document.getElementById('confirmSubmitBtn');

    // Inisialisasi Modal Sukses
    const successModalEl = document.getElementById('successModal');
    if (!successModalEl) {
        console.error('Modal #successModal tidak ditemukan di HTML.');
        return;
    }
    const successModal = new bootstrap.Modal(successModalEl);
    const orderNumberDisplay = document.getElementById('order-number-display');

    // --- SCRIPT VALIDASI BOOTSTRAP ---
    var forms = document.querySelectorAll('#step-7 .needs-validation');

    Array.prototype.slice.call(forms)
        .forEach(function (form) {
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                event.stopPropagation();
                
                if (!form.checkValidity()) {
                    console.log('Form tidak valid.');
                } else {
                    currentValidForm = form; 
                    submitModal.show();
                }

                form.classList.add('was-validated');
            }, false);
        });

    // --- Tambahkan Listener ke Tombol "Ya, Kirim" di dalam Modal ---
    if (confirmSubmitBtn) {
        confirmSubmitBtn.addEventListener('click', function() {
            submitModal.hide();
            if (currentValidForm) {
                handleFormSubmission(currentValidForm);
            }
        });
    }

    // --- FUNGSI SUBMIT KUSTOM KAMU ---
    function handleFormSubmission(form) {
        const formData = new FormData(form);
        const data = {};
        formData.forEach((value, key) => { data[key] = value; });
        console.log('Form Data:', data);

        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            <span>Mengirim...</span>
        `;

        // Simulate API call
        setTimeout(function () {
            
            const orderNumber = 'GR-' + Math.floor(Math.random() * 100000);
            if (orderNumberDisplay) {
                orderNumberDisplay.innerText = orderNumber;
            }
            successModal.show();

            submitBtn.disabled = false;
            submitBtn.innerHTML = `
                <span>Kirim Pesanan</span>
                <svg class="step7-arrow-icon" viewBox="0 0 23 23" fill="none">
                    <path d="M11.5 2L11.5 21M11.5 21L2 11.5M11.5 21L21 11.5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" transform="rotate(-90 11.5 11.5)"/>
                </svg>
            `;
            
        }, 1500);
    }

    // --- Event listener saat Modal Sukses ditutup ---
    successModalEl.addEventListener('hidden.bs.modal', function () {
        // Reset form
        if (currentValidForm) {
            currentValidForm.reset();
            currentValidForm.classList.remove('was-validated');
            currentValidForm = null; 
        }
        
        // === TAMBAHKAN BARIS INI ===
        // Pindahkan pengguna ke halaman home
        window.location.href = '../html/home.php';
        // ============================
    });

    // --- FILTER INPUT ---
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function (e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    }

    const postalCodeInput = document.getElementById('postalCode');
    if (postalCodeInput) {
        postalCodeInput.addEventListener('input', function (e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    }

});