# Dokumentasi Proyek EVENTUNWIM

## 1. Ikhtisar Proyek
**EVENTUNWIM** adalah sistem manajemen acara (Event Management System) berbasis web yang komprehensif, dirancang untuk memfasilitasi seluruh siklus penyelenggaraan acara di lingkungan Universitas Winaya Mukti (UNWIM). Sistem ini mengintegrasikan pendaftaran peserta, manajemen acara, penerbitan sertifikat digital yang dapat diverifikasi, sistem absensi berbasis QR Code, serta pelaporan dan analitik.

## 2. Arsitektur Teknis

### Backend
- **Framework:** Laravel 8.x
- **Database:** MySQL
- **Bahasa Pemrograman:** PHP 7.3+ / 8.0+
- **Fitur Utama:**
  - **Autentikasi:** Laravel Sanctum & Laravel Socialite (untuk integrasi SSO).
  - **PDF Generation:** `barryvdh/laravel-dompdf` untuk pembuatan sertifikat dan laporan PDF.
  - **QR Code:** `simplesoftwareio/simple-qrcode` untuk generasi QR Code unik pada sertifikat dan absensi.
  - **HTTP Client:** GuzzleHttp untuk komunikasi API eksternal.

### Frontend
- **Template Admin:** AdminLTE 3.15 (Berbasis Bootstrap 4/5).
- **View Engine:** Laravel Blade Templates.
- **JavaScript:** jQuery, Axios (untuk request AJAX).
- **CSS:** Custom CSS + AdminLTE styles.
- **Build Tools:** Laravel Mix.

## 3. Modul & Fitur Utama

### A. Manajemen Acara (Event Management)
Fitur inti untuk pengelolaan siklus hidup acara.
- **CRUD Acara:** Pembuatan, pengeditan, dan penghapusan acara.
- **Detail Acara:** Menyimpan informasi detail seperti nama, deskripsi, tanggal, waktu, lokasi (fisik/online), banner, dan kontak panitia.
- **Publikasi:** Acara dapat diatur status publikasinya untuk tampil di halaman depan.
- **URL Ramah SEO:** Menggunakan slug unik untuk setiap acara.

### B. Manajemen Peserta (Participant Management)
Pengelolaan data peserta yang terdaftar pada setiap acara.
- **Pendaftaran:** Formulir pendaftaran publik yang terintegrasi.
- **Impor Data:** Kemampuan mengimpor data peserta secara massal (bulk import).
- **Manajemen Kursi:** Penugasan nomor kursi untuk acara offline.
- **Kategorisasi:** Pengelompokan peserta (misal: Peserta, Panitia, Narasumber) untuk keperluan sertifikat.

### C. Sertifikat Digital (E-Certificate)
Sistem penerbitan sertifikat yang canggih dan aman.
- **Template Builder:** Editor visual untuk membuat desain sertifikat custom per acara.
- **Generator Massal:** Menghasilkan sertifikat PDF secara otomatis untuk ribuan peserta sekaligus.
- **Verifikasi Keaslian:** 
  - Setiap sertifikat memiliki **QR Code** dan **Hash Unik**.
  - Halaman verifikasi publik (`/verify/{hash}`) untuk mengecek validitas sertifikat.
- **Pencarian Mandiri:** Peserta dapat mencari dan mengunduh sertifikat mereka melalui portal publik.
- **Manajemen Aset:** Upload dan pengelolaan tanda tangan digital serta logo sertifikat.

### D. Absensi & Kehadiran (Attendance)
Sistem pencatatan kehadiran yang efisien.
- **QR Code Scanner:** Fitur scanner berbasis web (menggunakan kamera perangkat) untuk memindai QR Code peserta saat registrasi ulang/masuk.
- **Input Manual:** Opsi bagi admin untuk mencatat kehadiran secara manual jika diperlukan.
- **Laporan Kehadiran:** Ekspor data kehadiran ke format PDF atau CSV.
- **Log Real-time:** Pencatatan waktu tepat saat peserta melakukan scan.

### E. Undangan (Invitation)
Sistem distribusi undangan acara.
- **Template Undangan:** Pembuatan template pesan undangan yang dapat dikustomisasi.
- **Integrasi WhatsApp:** Pengiriman undangan langsung ke WhatsApp peserta (menggunakan layanan pihak ketiga/gateway).
- **Status Pengiriman:** Pemantauan status apakah undangan berhasil terkirim.

### F. Keuangan (Finance)
Modul untuk acara berbayar.
- **Pencatatan Pembayaran:** Melacak status pembayaran peserta (Lunas/Belum).
- **Verifikasi:** Admin dapat memverifikasi bukti pembayaran.

### G. Laporan & Utilitas (Reports & Settings)
- **Tracer Study:** Integrasi data untuk penelusuran alumni/peserta.
- **Manajemen User:** Pengelolaan pengguna admin dan hak aksesnya.
- **Log WhatsApp:** Riwayat pengiriman pesan WhatsApp untuk audit.

## 4. Hak Akses Pengguna (Roles & Permissions)
Sistem menggunakan kontrol akses berbasis peran (RBAC) yang ketat:

1. **Super Admin**
   - Akses penuh ke seluruh sistem.
   - Manajemen pengguna, pengaturan sistem global, log, dan laporan eksekutif.

2. **Admin Event**
   - Fokus: Operasional acara.
   - Hak Akses: Membuat acara, mengelola peserta, mengatur undangan, dan melakukan proses absensi.

3. **Admin Sertifikat**
   - Fokus: Pasca-acara dan legalitas.
   - Hak Akses: Membuat template sertifikat, mengelola tanda tangan/logo, dan memproses generasi sertifikat massal.

4. **Admin Keuangan**
   - Fokus: Monetisasi.
   - Hak Akses: Memantau dan memverifikasi data pembayaran peserta.

## 5. Struktur Direktori Penting

```
d:\www\EVENTUNWIM\
├── app\
│   ├── Http\Controllers\
│   │   ├── Admin\          # Controller untuk fitur dashboard admin
│   │   ├── Api\            # Controller API
│   │   ├── Auth\           # Autentikasi & SSO
│   │   └── PublicEventController.php  # Halaman depan & publik
│   ├── Models\             # Model Eloquent (Event, Participant, Certificate, dll)
│   └── Services\           # Business logic (CertificateService, WhatsAppService)
├── database\
│   ├── migrations\         # Skema database
│   └── seeders\            # Data awal (dummy/default)
├── public\                 # Aset publik (CSS, JS, Images)
├── resources\
│   ├── views\
│   │   ├── admin\          # View file untuk dashboard admin
│   │   ├── layouts\        # Layout utama (header, footer, sidebar)
│   │   └── public\         # View file untuk halaman pengunjung
│   └── js\                 # Vue.js / Vanilla JS scripts
└── routes\
    ├── web.php             # Definisi rute web utama
    └── api.php             # Definisi rute API
```

## 6. Alur Kerja Utama (Contoh)

### Siklus Acara Standar:
1. **Pra-Acara:**
   - Admin Event membuat **Event** baru.
   - Admin Event menyebarkan link pendaftaran.
   - Peserta mendaftar melalui halaman publik.
   - Admin mengirim undangan via WhatsApp.

2. **Saat Acara:**
   - Peserta datang dan menunjukkan QR Code (dari tiket/undangan).
   - Panitia (Admin) melakukan scan QR Code di menu **Attendance Scanner**.
   - Data kehadiran tercatat otomatis.

3. **Pasca-Acara:**
   - Admin Sertifikat membuat **Template Sertifikat** dan menghubungkannya dengan Event.
   - Admin melakukan **Generate Sertifikat** untuk peserta yang hadir (status 'hadir').
   - Peserta membuka halaman **Cek Sertifikat**, memasukkan data, dan mengunduh sertifikat mereka.