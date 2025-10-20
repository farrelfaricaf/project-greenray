# Project GreenRay: Solusi Energi Surya

## 📝 Deskripsi Proyek

Proyek GreenRay adalah implementasi antarmuka pengguna (UI/UX) untuk perusahaan penyedia solusi Pembangkit Listrik Tenaga Surya (PLTS) di Indonesia. Fokus utama proyek ini adalah menyediakan informasi mendalam tentang layanan PLTS dan menawarkan alat bantu yang interaktif untuk menarik prospek.

Aplikasi ini dikembangkan menggunakan kombinasi standar web modern dan **Bootstrap 5** untuk tampilan yang responsif dan *user-friendly*.

## 🌟 Fitur Utama

Proyek ini mencakup beberapa halaman dan fungsionalitas inti:

* **Landing Page (`landpage.html`):** Halaman pembuka yang berfokus pada pemasaran, memperkenalkan masalah tagihan listrik yang tinggi, dan menawarkan solusi PLTS GreenRay.
* **Halaman Beranda (`home.html`):** Informasi mendalam mengenai profil perusahaan (`Who We Are`), keunggulan, ulasan pelanggan, dan bagian **FAQ** interaktif.
* **Kalkulator Penghematan (`calc.html`):**
    * Alat interaktif 6 langkah untuk menghitung potensi **Penghematan Bulanan**, **Kapasitas Sistem Ideal (kWp)**, dan estimasi **ROI (Return on Investment)**.
    * Logika perhitungan inti diletakkan dalam `javascript/calculation-logic.js`.
* **Portofolio (`portofolio.html`):** Menampilkan studi kasus proyek yang telah diselesaikan (Residensial, Komersial, Edukasi, dll.).
* **Katalog Produk (`katalog.html`):** Menampilkan berbagai pilihan paket PLTS, inverter, dan baterai penyimpanan.
* **Formulir Kontak (`contact-us.html`):** Formulir kontak dan opsi *direct communication* (WhatsApp/Email).
* **Sistem Akses (`signin.html`, `signup.html`):** Halaman *Sign In* dan *Sign Up* dengan validasi *client-side*.

## 🛠️ Struktur Teknologi

* **Frontend:** HTML5, CSS3.
* **Framework Utama:** **Bootstrap 5** (Digunakan secara ekstensif untuk sistem *grid*, **elemen UI interaktif** seperti tombol, *modal* (pop-up), formulir, serta *styling* dan validasi formulir pada sisi *client-side*).
* **Styling Kustom:** CSS Murni untuk desain spesifik (`.css` files).
* **Interaktivitas:** JavaScript Vanilla (Murni) (Digunakan untuk logika kompleks, seperti fungsi inti kalkulator dan validasi/transisi antar langkah).
