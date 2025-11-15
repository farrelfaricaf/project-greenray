// Menjalankan kode setelah semua konten HTML dimuat
document.addEventListener('DOMContentLoaded', function () {

    // --- Inisialisasi Modal & Variabel ---
    let currentValidForm = null; 
    
    // Inisialisasi Modal Konfirmasi Submit
    const submitModalEl = document.getElementById('submitModal');
    if (!submitModalEl) {
        console.error('Modal #submitModal tidak ditemukan di HTML.');
        return;
    }
    const submitModal = new bootstrap.Modal(submitModalEl);
    const confirmSubmitBtn = document.getElementById('confirmSubmitBtn');

    // (Kita TIDAK LAGI menginisialisasi #successModal di sini, 
    // karena PHP yang akan menampilkannya)

    // --- SCRIPT VALIDASI BOOTSTRAP ---
    var forms = document.querySelectorAll('#orderForm.needs-validation'); // Target 'orderForm'

    Array.prototype.slice.call(forms)
        .forEach(function (form) {
            
            // Saat tombol "Kirim Pesanan" (type="submit") diklik...
            form.addEventListener('submit', function (event) {
                
                // Selalu hentikan submit bawaan
                event.preventDefault();
                event.stopPropagation();
                
                if (!form.checkValidity()) {
                    console.log('Form tidak valid, tampilkan error.');
                    form.classList.add('was-validated'); // Tampilkan error validasi
                } else {
                    console.log('Form valid, tampilkan modal konfirmasi.');
                    // Jika form valid, simpan form-nya dan tampilkan modal konfirmasi
                    currentValidForm = form;
                    submitModal.show();
                }
            }, false);
        });

    // --- Event Listener untuk Tombol "Ya, Kirim Pesanan" di dalam Modal ---
    if (confirmSubmitBtn) {
        confirmSubmitBtn.addEventListener('click', function () {
            if (currentValidForm) {
                console.log('Konfirmasi diterima. Memproses form...');
                submitModal.hide(); // Sembunyikan modal konfirmasi
                submitValidForm(currentValidForm); // Panggil fungsi submit
            }
        });
    }

    // --- FUNGSI SUBMIT UTAMA (INI YANG DIPERBAIKI) ---
    function submitValidForm(form) {
        
        // Tampilkan loading spinner di tombol
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            <span>Mengirim...</span>
        `;

        // ▼▼▼ LOGIKA "JEMBATAN" (KITA PINDAH KE SINI) ▼▼▼
        console.log('Menyalin data dari JS ke hidden input...');
        const data = window.CALCULATOR_DATA;
        const results = window.CALCULATOR_RESULTS;

        // Salin data dari Step 1 (key dari step-transition.js)
        if (data['step-1']) {
            document.getElementById('hidden_monthly_bill').value = data['step-1'].bill.replace(/\D/g, '');
            document.getElementById('hidden_va_capacity').value = data['step-1'].daya;
            document.getElementById('hidden_location').value = data['step-1'].lokasi;
        }
        // Salin data dari Step 2, 3, 4 (key dari card-select.js)
        document.getElementById('hidden_property_type').value = data['step-2'];
        document.getElementById('hidden_installation_timeline').value = data['step-3'];
        document.getElementById('hidden_roof_constraints').value = data['step-4'];
        // Salin data dari Step 5 (key dari step-transition.js)
        document.getElementById('hidden_email').value = data['step-5'];
        // Salin data dari Step 6 (Hasil Kalkulasi)
        if (results) {
            document.getElementById('hidden_monthly_savings').value = results.monthlySavings;
            document.getElementById('hidden_system_capacity_kwp').value = results.systemCapacity;
            document.getElementById('hidden_investment_estimate').value = results.investment;
            document.getElementById('hidden_roi_years').value = results.roiYears;
        }
        console.log('Salin data selesai.');
        // ▲▲▲ AKHIR LOGIKA "JEMBATAN" ▲▲▲

        // Kirim form ke PHP
        console.log('Mengirim form ke server...');
        form.submit(); // Ini adalah submit HTML tradisional
    }

    // --- FILTER INPUT (Biarkan) ---
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function (e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    }
});