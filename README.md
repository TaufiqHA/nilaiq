<div align="center">

# NilaiQ

**Sistem Informasi Manajemen Nilai & Raport Sekolah**

Kelola nilai harian, absensi, ekstrakurikuler, hingga cetak raport — semua dalam satu aplikasi yang rapi, cepat, dan siap pakai.

[![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![PHPUnit](https://img.shields.io/badge/Tests-PHPUnit-3C9CD7?style=for-the-badge&logo=phpunit&logoColor=white)](https://phpunit.de)
[![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)](https://opensource.org/licenses/MIT)

</div>

---

## Tentang NilaiQ

**NilaiQ** adalah aplikasi berbasis web untuk membantu **Guru Mapel**, **Wali Kelas**, dan **Admin** dalam mengelola seluruh proses penilaian di sekolah. Mulai dari input nilai ulangan harian, tugas, PTS, PAS, rekap absensi, prestasi siswa, sampai **mencetak raport** lengkap dengan lampiran — semuanya tercatat otomatis dan konsisten.

Dibangun dengan **Laravel 13** dan **Tailwind CSS 4**, NilaiQ mengusung antarmuka modern (mode terang/gelap) serta dukungan API untuk aplikasi mobile, sehingga bisa digunakan dari mana saja.

---

## Fitur Unggulan

### Guru Mapel
- **Dashboard interaktif** — ringkasan aktivitas penilaian
- **Manajemen penilaian lengkap** — Ulangan Harian, Tugas, PTS, dan PAS
- **Absensi pertemuan** — catat kehadiran per pertemuan dengan tipe kegiatan
- **Scan nilai dari gambar (AI)** — ekstrak nilai otomatis dari foto daftar nilai menggunakan Google Gemini
- **Rekap nilai & nilai akhir** — kalkulasi otomatis dari seluruh komponen penilaian
- **Manajemen siswa & kelas** — termasuk impor massal dari file Excel
- **Backup database** — unduh & pulihkan cadangan data

### Wali Kelas
- **Informasi kelas & siswa** — data kelas binaan dalam satu tempat
- **Pengaturan mata pelajaran** — atur mapel, kelompok, dan bobot penilaian
- **Ekstrakurikuler, prestasi & sikap** — dokumentasi lengkap perkembangan siswa
- **Catatan wali kelas** — pesan dan bimbingan untuk setiap siswa
- **Nilai mapel** — input manual atau impor dari template Excel
- **Cetak raport** — raport per siswa atau cetak semua sekaligus
- **Rekap nilai kelas** — gambaran menyeluruh capaian siswa

### Admin
- **Monitoring server** — pantau CPU, memori, disk, dan uptime
- **Manajemen pengguna** — kelola akun guru, wali kelas, dan admin

### Umum
- **Mode terang & gelap** yang nyaman di mata
- **REST API (Sanctum)** — siap diintegrasikan dengan aplikasi mobile
- **Sinkronisasi offline-first** — didukung `client_uuid` untuk data pertemuan & nilai
- **Antarmuka Bahasa Indonesia** — ramah pengguna untuk guru

---

## Teknologi

| Lapisan | Teknologi |
| --- | --- |
| **Framework** | Laravel 13 |
| **Bahasa** | PHP 8.3+ |
| **Frontend** | Blade + Tailwind CSS 4 + Flowbite |
| **Build Tool** | Vite 8 |
| **Database** | SQLite / MySQL / PostgreSQL (mendukung semua driver Laravel) |
| **Autentikasi** | Session (web) & Laravel Sanctum (API) |
| **Excel** | PhpSpreadsheet |
| **Backup** | Spatie Laravel Backup + Google Drive |
| **AI** | Google Gemini (OCR daftar nilai) |
| **Testing** | PHPUnit 12 |
| **Code Style** | Laravel Pint |

---

## Mulai Cepat

### Prasyarat
- PHP **8.3+** beserta ekstensi standar Laravel
- Composer
- Node.js & npm

### Instalasi

```bash
# 1. Kloning repositori
git clone https://github.com/TaufiqHA/nilaiq.git
cd nilaiq

# 2. Jalankan setup otomatis (install, .env, key, migrate, build)
composer setup

# 3. Jalankan server pengembangan
composer dev
```

`composer dev` menjalankan **server Laravel**, **queue worker**, dan **Vite** secara bersamaan. Buka `http://localhost:8000` dan masuk menggunakan akun yang ada.

> Ingin menjalankan langkah secara manual? Lihat perintah `composer setup` di `composer.json`.

### Konfigurasi Opsional

Tambahkan pada file `.env` untuk mengaktifkan integrasi lanjutan:

```dotenv
# Google Gemini — untuk fitur scan nilai dari gambar
GEMINI_API_KEY=your-api-key

# Google Drive — untuk penyimpanan cadangan backup
GOOGLE_DRIVE_CLIENT_ID=
GOOGLE_DRIVE_CLIENT_SECRET=
GOOGLE_DRIVE_REFRESH_TOKEN=
GOOGLE_DRIVE_FOLDER=
```

---

## Peran Pengguna

NilaiQ mengelola akses berdasarkan tiga peran:

| Peran | Akses | Deskripsi |
| --- | --- | --- |
| `admin` | `/admin/monitoring`, `/admin/users` | Monitoring server & manajemen pengguna |
| `mapel` | `/dashboard`, `/students`, `/attendance-meetings`, dll. | Guru mata pelajaran — input & kelola nilai |
| `wali_kelas` | `/wali-kelas/*` | Wali kelas — informasi kelas, raport & rekap |

---

## API

NilaiQ menyediakan REST API yang diautentikasi dengan **Sanctum**, khusus untuk peran **Guru Mapel**:

| Method | Endpoint | Keterangan |
| --- | --- | --- |
| `GET` | `/api/health` | Health check |
| `POST` | `/api/login` | Login & dapatkan token |
| `GET` | `/api/me` | Profil pengguna aktif |
| `GET` | `/api/dashboard` | Data dashboard |
| — | `/api/attendance-meetings` | CRUD pertemuan absensi |
| — | `/api/attendances` | CRUD kehadiran |
| — | `/api/daily-test-meetings` | CRUD pertemuan ulangan harian |
| — | `/api/daily-test-scores` | CRUD nilai ulangan harian |

> Semua endpoint terproteksi menggunakan middleware `auth:sanctum` + `mapel`.

---

## Pengujian

```bash
# Semua test
php artisan test --compact

# Test spesifik
php artisan test --compact tests/Feature/RaportControllerTest.php

# Filter berdasarkan nama test
php artisan test --compact --filter=testName
```

Menjaga kualitas kode dengan **Laravel Pint**:

```bash
vendor/bin/pint
```

---

## Struktur Proyek

```
nilaiq/
├── app/
│   ├── Http/
│   │   ├── Controllers/      # Controller web & API
│   │   └── Middleware/       # Admin, Mapel, WaliKelas
│   └── Models/               # Model Eloquent (Nilai, Absensi, Raport, dll.)
├── database/
│   ├── migrations/           # Struktur tabel
│   └── seeders/              # Data awal & akun default
├── resources/
│   ├── views/                # Blade: auth, admin, waliKelas, komponen
│   ├── css/
│   └── js/                   # Flowbite & logika tema
├── routes/
│   ├── web.php               # Rute web berbasis peran
│   └── api.php               # Rute REST API (Sanctum)
└── tests/                    # Pengujian fitur (PHPUnit)
```

---

## Kontribusi

Kontribusi selalu terbuka! Silakan **fork** repositori ini, buat *branch* fitur, lalu ajukan **Pull Request**.

1. Fork repositori
2. Buat branch: `git checkout -b fitur/fitur-baru`
3. Commit perubahan: `git commit -m "Menambahkan fitur baru"`
4. Push: `git push origin fitur/fitur-baru`
5. Buka Pull Request

---

## Lisensi

NilaiQ adalah perangkat lunak open-source yang dirilis di bawah [MIT License](https://opensource.org/licenses/MIT).

<div align="center">

Dibuat untuk mempermudah pendidikan Indonesia.

</div>
