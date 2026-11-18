# OctaveTix — Metal & Underground Gig Ticketing System

**OctaveTix** adalah sistem manajemen acara musik dan pemesanan tiket konser berbasis **Laravel Modular Monolith** dengan antarmuka **Dark Mode** (`Zinc-950` / `Red-600`).

## Arsitektur Modular Monolith

Aplikasi dipisahkan ke dalam tiga domain modul independen di dalam `app/Modules/` yang berkomunikasi lintas-modul melalui **Service Contract (Interface)** dan Dependency Injection:

- **`EventModule`**: Mengelola jadwal gigs/konser, lokasi venue, serta relasi *Many-to-Many* kategori genre musik.
- **`TicketModule`**: Mengelola jenis tiket per event, harga, kontrol kuota atomik (`lockForUpdate`), dan mengekspos `TicketContract`.
- **`RegistrationModule`**: Mengelola transaksi pemesanan tiket peserta, penerbitan kode tiket unik (`OCT-XXXXXX`), pembatalan pesanan dengan pengembalian kuota, validasi *Gate Check-In*, serta laporan rekap peserta melalui `RegistrationContract`.

## Fitur Utama

1. **Role-Based Access Control (`admin` & `peserta`)** via `RoleMiddleware`.
2. **Manajemen Event & Kategori Genre** lengkap dengan pencarian berdasarkan judul/lokasi serta filter pill berdasarkan genre musik.
3. **Transaksi Tiket Aman (Anti-Overbooking)** menggunakan `DB::transaction` dan `lockForUpdate()`.
4. **E-Ticket Gate Pass & QR Code** siap cetak di halaman *Tiket Saya*, beserta fitur pembatalan pesanan.
5. **Gate Security Scanner & Rekap Pendapatan** khusus Admin di halaman detail event.

## Instalasi & Menjalankan Proyek

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve