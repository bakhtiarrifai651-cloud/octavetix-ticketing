# Sistem Manajemen Event & Tiket Kampus (Laravel 13 Modular Monolith)

Aplikasi web manajemen event dan pemesanan tiket berbasis **Laravel 13** dengan pendekatan arsitektur **Modular Monolith**.

## Teknologi yang Digunakan
- **Framework:** Laravel 13 (PHP 8.5)
- **Database:** SQLite
- **Autentikasi & Otorisasi:** Laravel Sanctum & Custom RBAC Middleware (`admin`, `peserta`)
- **Frontend:** Blade Template & Tailwind CSS (CDN)

## Struktur Arsitektur Modul (`app/Modules`)
1. **EventModule**
   - Manajemen CRUD Event dan Kategori (relasi *Many-to-Many* melalui tabel pivot `category_event`).
   - Mengambil daftar tiket pada halaman detail event melalui `TicketContract`.
2. **TicketModule**
   - Manajemen jenis tiket dan kuota per event (khusus Admin).
   - Menyediakan `TicketContract` (Interface), `TicketService` (Implementasi), serta `QuotaFullException` (Custom Exception).
3. **RegistrationModule**
   - Pemesanan tiket oleh Peserta dan riwayat tiket (`/my-tickets`).
   - Menggunakan `DB::transaction()` dan `lockForUpdate()` saat memanggil `TicketContract::reserveQuota()` untuk menjaga konsistensi kuota.

## Akun Demo (Seeder)
| Role | Email | Password |
| :--- | :--- | :--- |
| **Admin** | `admin@amikom.ac.id` | `password` |
| **Peserta** | `peserta@amikom.ac.id` | `password` |

## Cara Menjalankan Proyek
1. Clone repository ini lalu masuk ke folder proyek:
   ```bash
   git clone <url-repository-kamu>
   cd event-tiket-uts