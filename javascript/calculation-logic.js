/**
 * calculation-logic.js
 * Berisi semua data konstanta dan fungsi perhitungan Tiga Langkah.
 * Ini adalah otak kalkulator.
 */

// --- KONSTANTA DATA UNTUK RUMUS (HARDCODED) ---
const TARIFF_PLN = {
    "900 VA": 1352.00,
    "1300 VA": 1444.70, 
    "2200 VA": 1444.70, 
    "3500 VA": 1699.53,
    "5500 VA": 1699.53,
    "6600+ VA": 1699.53
};

const SOLAR_IRRADIANCE = {
    "Jakarta": 4.50,
    "Bandung": 4.20,
    "Surabaya": 5.26, 
    "Medan": 4.10,
};

const EFFICIENCY_FACTOR = 0.85; // Efisiensi Sistem (85%)
const SAFETY_FACTOR = 1.25;      // Faktor Keamanan (25% buffer)
const DAYS_IN_MONTH = 30.44;     // Rata-rata hari dalam sebulan
const COST_PER_KWp = 15000000;   // Asumsi Biaya Instalasi Panel Surya Sederhana: Rp 15 Juta per kWp

// --- FUNGSI UTAMA PERHITUNGAN ---
function calculateSavings(data) {
    try {
        // 1. Validasi Data Inti (Step 1)
        if (!data['step-1'] || !data['step-1'].bill || !data['step-1'].daya || !data['step-1'].lokasi) {
            console.error("Data Step 1 tidak lengkap untuk perhitungan.");
            return null;
        }

        // 2. Ambil Input dan Bersihkan Format Rupiah
        const billString = data['step-1'].bill.replace(/[^\d]/g, ''); 
        const monthlyBill = parseFloat(billString); 
        
        const powerVA = data['step-1'].daya;
        const city = data['step-1'].lokasi;

        if (isNaN(monthlyBill) || monthlyBill <= 0) {
            console.error("Input tagihan listrik tidak valid.");
            return null;
        }

        // 3. Ambil Konstanta dari Data Hardcoded
        const tariff = TARIFF_PLN[powerVA];
        const irradiance = SOLAR_IRRADIANCE[city];

        if (!tariff || !irradiance) {
            console.error("Tarif atau Irradiance tidak ditemukan untuk input ini.");
            return null;
        }

        // --- RUMUS LANGKAH 1: Hitung Konsumsi kWh ---
        const consumptionKWh = monthlyBill / tariff;

        // --- RUMUS LANGKAH 2: Hitung Kapasitas kWp Ideal ---
        const idealKWp = (consumptionKWh * SAFETY_FACTOR) / (irradiance * DAYS_IN_MONTH);

        // --- RUMUS LANGKAH 3: Hitung Nilai Penghematan Biaya (Rp) ---
        const potentialEnergyGenerated = idealKWp * irradiance * EFFICIENCY_FACTOR * DAYS_IN_MONTH;
        const estimatedSavingsRp = potentialEnergyGenerated * tariff;
        
        // --- Hitung Estimasi ROI (Sederhana) ---
        const totalInstallationCost = idealKWp * COST_PER_KWp;
        const annualSavings = estimatedSavingsRp * 12;
        const roiEstimate = annualSavings > 0 ? totalInstallationCost / annualSavings : 99; // 99 jika pembagian nol

        // 4. Kembalikan Hasil Final
        return {
            monthlySavings: Math.round(estimatedSavingsRp),
            systemCapacity: Math.round(idealKWp * 10) / 10, 
            roiYears: Math.round(roiEstimate * 10) / 10, 
            initialBill: monthlyBill,
            userEmail: data['step-5'] ? data['step-5'].email : 'N/A' 
        };
        
    } catch (e) {
        console.error("Error during calculation:", e);
        return null;
    }
}

// --- FUNGSI UNTUK MENAMPILKAN HASIL DI FRONTEND ---
function displayResults(results) {
    if (!results) return;
    
    // 1. Format Angka untuk Tampilan
    const formattedSavings = new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0 }).format(results.monthlySavings);
    
    // 2. Update Elemen Tampilan Hasil (Step 6)
    document.getElementById('monthly-savings').textContent = 'Rp ' + formattedSavings;
    document.getElementById('system-capacity').textContent = results.systemCapacity + ' kWp';
    document.getElementById('roi-estimate').textContent = results.roiYears + ' years';
    
    // 3. Modifikasi Teks Pesan di Halaman Hasil (Conditional Text)
    const messageElement = document.querySelector('.result-dec-heading');
    
    if (results.monthlySavings >= results.initialBill * 0.98) { 
        messageElement.textContent = 
            `Nilai energi yang dihasilkan sistem ${results.systemCapacity} kWp menutupi 100% konsumsi Anda. Tagihan listrik bulanan Anda BERPOTENSI menjadi Rp 0!`;
    } else {
         messageElement.textContent = 
            `Penghematan yang diproyeksikan sangat signifikan. Sistem ${results.systemCapacity} kWp ini dapat mengurangi tagihan listrik Anda secara drastis setiap bulan.`;
    }

    // 4. Update CTA Final (Tanpa WA)
    const ctaButton = document.querySelector('.result-cta-btn');
    const ctaText = document.querySelector('.result-cta-btn .text-button');
    const ctaDec = document.querySelector('.result-dec-heading-small');
    
    ctaButton.href = '/html/contact-us.html'; 
    ctaText.textContent = 'Schedule Your FREE Consultation Now';
    ctaDec.textContent = 
        `Tim ahli kami siap memverifikasi kelayakan atap Anda dan memberikan penawaran harga yang akurat. Laporan ringkas sudah dikirim ke ${results.userEmail}.`;
}