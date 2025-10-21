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

const EFFICIENCY_FACTOR = 0.85;
const SAFETY_FACTOR = 1.25;
const DAYS_IN_MONTH = 30.44;
const COST_PER_KWp = 15000000;

function calculateSavings(data) {
    try {
        if (!data['step-1'] || !data['step-1'].bill || !data['step-1'].daya || !data['step-1'].lokasi) {
            console.error("Data Step 1 tidak lengkap untuk perhitungan.");
            return null;
        }

        const billString = data['step-1'].bill.replace(/[^\d]/g, ''); 
        const monthlyBill = parseFloat(billString); 
        
        const powerVA = data['step-1'].daya;
        const city = data['step-1'].lokasi;

        if (isNaN(monthlyBill) || monthlyBill <= 0) {
            console.error("Input tagihan listrik tidak valid.");
            return null;
        }

        const tariff = TARIFF_PLN[powerVA];
        const irradiance = SOLAR_IRRADIANCE[city];

        if (!tariff || !irradiance) {
            console.error("Tarif atau Irradiance tidak ditemukan untuk input ini.");
            return null;
        }

        const consumptionKWh = monthlyBill / tariff;

        const idealKWp = (consumptionKWh * SAFETY_FACTOR) / (irradiance * DAYS_IN_MONTH);

        const potentialEnergyGenerated = idealKWp * irradiance * EFFICIENCY_FACTOR * DAYS_IN_MONTH;
        const estimatedSavingsRp = potentialEnergyGenerated * tariff;
        
        const totalInstallationCost = idealKWp * COST_PER_KWp;
        const annualSavings = estimatedSavingsRp * 12;
        const roiEstimate = annualSavings > 0 ? totalInstallationCost / annualSavings : 99; 

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

function displayResults(results) {
    if (!results) return;
    
    const formattedSavings = new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0 }).format(results.monthlySavings);
    
    document.getElementById('monthly-savings').textContent = 'Rp ' + formattedSavings;
    document.getElementById('system-capacity').textContent = results.systemCapacity + ' kWp';
    document.getElementById('roi-estimate').textContent = results.roiYears + ' years';
    
    const messageElement = document.querySelector('.result-dec-heading');
    
    if (results.monthlySavings >= results.initialBill * 0.98) { 
        messageElement.textContent = 
            `Nilai energi yang dihasilkan sistem ${results.systemCapacity} kWp menutupi 100% konsumsi Anda. Tagihan listrik bulanan Anda BERPOTENSI menjadi Rp 0!`;
    } else {
         messageElement.textContent = 
            `Penghematan yang diproyeksikan sangat signifikan. Sistem ${results.systemCapacity} kWp ini dapat mengurangi tagihan listrik Anda secara drastis setiap bulan.`;
    }

    const ctaButton = document.querySelector('.result-cta-btn');
    const ctaText = document.querySelector('.result-cta-btn .text-button');
    const ctaDec = document.querySelector('.result-dec-heading-small');
    
    ctaButton.href = '/html/contact-us.html'; 
    ctaText.textContent = 'Schedule Your FREE Consultation Now';
    ctaDec.textContent = 
        `Tim ahli kami siap memverifikasi kelayakan atap Anda dan memberikan penawaran harga yang akurat. Laporan ringkas sudah dikirim ke ${results.userEmail}.`;
}