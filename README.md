# 🎓 GoEdu - Sistem Manajemen Pembelajaran (LMS)

Sistem pembelajaran online berbasis Laravel dengan fitur lengkap:  
✅ Pendaftaran kelas & pembayaran (Midtrans)  
✅ Materi berurutan & tugas (Essay/Quiz)  
✅ Absensi berbasis foto  
✅ Penilaian otomatis & sertifikat PDF  
✅ Admin panel (Filament)

---

## 🛠️ Prasyarat

-   PHP >= 8.2
-   Composer
-   MySQL / PostgreSQL
-   Node.js & NPM

---

## 🚀 Instalasi

### 1. Clone repository

```bash
git clone https://github.com/erdi20/capstone-project-web-kursus.git
cd capstone-project-web-kursus
```

### 2. Install dependensi

```bash
composer install
npm install && npm run build
```

### 3. Salin file environment & konfigurasi

```bash
cp .env.example .env
```

Edit `.env` sesuai lingkungan Anda:

```env
APP_NAME="GoEdu"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=goedu
DB_USERNAME=root
DB_PASSWORD=

# Midtrans (Sandbox)
MIDTRANS_SERVER_KEY=your_sandbox_server_key
MIDTRANS_CLIENT_KEY=your_sandbox_client_key
MIDTRANS_IS_PRODUCTION=false

#SMTP
MAIL_MAILER=smtp
MAIL_SCHEME=null
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=username anda
MAIL_PASSWORD=password anda
MAIL_FROM_ADDRESS="admin@belajar-quiz.com"
MAIL_FROM_NAME="${APP_NAME}"

# File Storage
FILESYSTEM_DISK=public
```

### 4. Generate key aplikasi

```bash
php artisan key:generate
```

### 5. Jalankan migrasi & seeder

```bash
php artisan migrate --seed
# Atau hanya migrasi
php artisan migrate

# Jalankan seeder user (opsional)
php artisan db:seed --class=UserSeeder
```

### 6. Buat symbolic link storage

```bash
php artisan storage:link
```

### 7. Jalankan aplikasi

```bash
php artisan serve
```


Akses:

-   **Frontend**: [http://localhost:8000]
-   **Admin (Filament)**: [http://localhost:8000/app]

---

---
untuk menggunakan fitur payment gateway dengan midtrans
lakukan beberapa langkah
1. jalankan ngrok perintah 
ngrok http 8000

2. salin urlnya lalu gabungkan dengan route api. letakan di midtrans->pengaturan->payment->URL notifikasi->Endpoint URL notifikasi-> simpan
http://...../payment/notify


---

## 🔑 Akun Default (Setelah Seeder)

| Peran  | Email                 | Password   |
| ------ | --------------------- | ---------- |
| Admin  | `admin@example.com`   | `password` |
| Mentor | `mentor1@example.com` | `password` |
| Siswa  | `siswa1@example.com`  | `password` |

> 💡 Ganti password di produksi!

---

## 📦 Fitur Utama

### 🧑‍🎓 Sisi Siswa

-   Daftar kelas & bayar via Midtrans
-   Akses materi berurutan (harus selesaikan prasyarat)
-   Kerjakan tugas Essay & Quiz
-   Absensi berbasis foto saat sesi Zoom
-   Lihat nilai & unduh sertifikat PDF

### 👨‍🏫 Sisi Mentor (Filament)

-   Kelola kursus, kelas, materi
-   Buat tugas Essay & Quiz
-   Atur jadwal absensi
-   Pantau progres & nilai siswa
-   Generate sertifikat otomatis

### 💳 Pembayaran

-   Integrasi Midtrans (Sandbox/Production)
-   Callback otomatis untuk update status

### 📄 Sertifikat

-   Generate PDF on-the-fly
-   Template bisa dikustom per kursus

---

## 🧪 Environment Development

Untuk mengaktifkan fitur development:

```env
APP_DEBUG=true
LOG_LEVEL=debug
```

---

## 📬 Dukungan

Jika menemui masalah:

1. Pastikan semua langkah instalasi diikuti
2. Cek error di `storage/logs/laravel.log`
3. Pastikan `storage` dan `bootstrap/cache` bisa ditulis

---

> 🚀 **Proyek ini siap dikembangkan lebih lanjut!**  
> Kontribusi & saran sangat diterima.

---

