// Dropdown FAQ dan Dropdown Kalkulator
document.addEventListener('DOMContentLoaded', function() {
    
    // --- Logika FAQ ---
    const faqItems = document.querySelectorAll('.faq-item');
    faqItems.forEach(item => {
        const header = item.querySelector('.dropdown-faq');
        if (header) {
             header.addEventListener('click', function() {
                faqItems.forEach(otherItem => {
                    if (otherItem !== item && otherItem.classList.contains('active')) {
                        otherItem.classList.remove('active');
                    }
                });
                item.classList.toggle('active');
            });
        }
    });

    // --- Logika Custom Dropdown ---
    const allDropdowns = document.querySelectorAll('.custom-dropdown');

    allDropdowns.forEach(dropdown => {
        const header = dropdown.querySelector('.dropdown-header');
        const selectedValue = dropdown.querySelector('.selected-value');
        const optionsContainer = dropdown.querySelector('.options-container');
        const searchInput = dropdown.querySelector('.search-input'); 

        const optionsData = dropdown.getAttribute('data-options').split(',').map(item => item.trim());
        const isSearchable = dropdown.hasAttribute('data-searchable'); 
        
        // Fungsionalitas: Mengisi Opsi
        function populateOptions(dataArray) {
            optionsContainer.innerHTML = ''; 
            
            if (dataArray.length === 0) {
                const noResult = document.createElement('div');
                noResult.classList.add('dropdown-option');
                noResult.textContent = "No results found.";
                optionsContainer.appendChild(noResult);
                return;
            }
            
            dataArray.forEach(item => {
                const option = document.createElement('div');
                option.classList.add('dropdown-option');
                option.setAttribute('data-value', item);
                option.textContent = item;
                optionsContainer.appendChild(option);
            });
        }
        populateOptions(optionsData);
        
        // Fungsionalitas: Membuka/Menutup Dropdown
        header.addEventListener('click', function() {
            allDropdowns.forEach(otherDropdown => {
                if (otherDropdown !== dropdown && otherDropdown.classList.contains('open')) {
                    otherDropdown.classList.remove('open');
                }
            });

            dropdown.classList.toggle('open');
            
            if (isSearchable && dropdown.classList.contains('open')) {
                populateOptions(optionsData); 
                searchInput.value = ''; 
                searchInput.focus(); 
            }
        });
        
        // LOGIKA PENCARIAN
        if (isSearchable && searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = searchInput.value.toLowerCase();
                const filteredData = optionsData.filter(item => 
                    item.toLowerCase().includes(searchTerm)
                );
                populateOptions(filteredData);
            });
        }

        // Fungsionalitas: Memilih Opsi
        optionsContainer.addEventListener('click', function(event) {
            if (event.target.classList.contains('dropdown-option') && event.target.getAttribute('data-value')) {
                const newValue = event.target.getAttribute('data-value');
                
                selectedValue.textContent = newValue; 
                dropdown.classList.remove('open');
                
                if (isSearchable && searchInput) {
                    searchInput.value = ''; 
                }
            }
        });

        // Fungsionalitas: Tutup saat klik di luar
        document.addEventListener('click', function(event) {
            if (!dropdown.contains(event.target) && dropdown.classList.contains('open')) {
                dropdown.classList.remove('open');
                
                if (isSearchable && searchInput) {
                    searchInput.value = '';
                }
            }
        });
    });
});