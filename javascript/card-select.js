// Global object untuk menyimpan semua data kalkulator.
window.CALCULATOR_DATA = window.CALCULATOR_DATA || {
    'step-1': null, 
    'step-2': null, 
    'step-3': null, 
    'step-4': null, 
    'step-5': null, 
};

document.addEventListener('DOMContentLoaded', () => {
    
    const singleSelectContainers = document.querySelectorAll('.card-container.single-select');

    singleSelectContainers.forEach(container => {
        const cards = container.querySelectorAll('.card-option');
        
        // Ambil ID langkah
        const stepId = container.closest('.calc-container').id;
        
        // Inisialisasi: Cari kartu yang sudah aktif saat DOM load
        const initialActiveCard = container.querySelector('.card-option.active');
        if (initialActiveCard) {
            window.CALCULATOR_DATA[stepId] = initialActiveCard.dataset.value;
        }

        cards.forEach(card => {
            card.addEventListener('click', function() {
                // Cek apakah kartu yang diklik sudah aktif
                if (this.classList.contains('active')) {
                    return;
                }

                // 1. Hapus kelas 'active' dari semua kartu
                cards.forEach(c => {
                    c.classList.remove('active');
                });

                // 2. Tambahkan kelas 'active'
                this.classList.add('active');
                
                // 3. SIMPAN DATA
                window.CALCULATOR_DATA[stepId] = this.dataset.value;

                console.log(`[CardSelect] Selection saved for ${stepId}: ${window.CALCULATOR_DATA[stepId]}`);
            });
        });
    });
});