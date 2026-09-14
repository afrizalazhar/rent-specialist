# Rent Specialist

Rent Specialist adalah sistem manajemen penyewaan kendaraan (mobil, SUV, dan sepeda motor) untuk bisnis sewa yang dijalankan oleh satu operator. Situs publik hanya menampilkan katalog kendaraan (read-only), sementara seluruh data pemesanan, pelanggan, dan pembayaran dibuat serta dikelola oleh staf melalui dashboard admin. Lihat [CONTEXT.md](CONTEXT.md) untuk glosarium domain, dan [SPEC.md](SPEC.md) untuk ruang lingkup v1.

## Model WhatsApp-first

Bisnis ini tidak menerima pemesanan online. Pelanggan melihat kendaraan di katalog publik, lalu menghubungi operator melalui WhatsApp untuk menanyakan ketersediaan, harga, dan menyepakati jadwal. Staf kemudian membuatkan data pelanggan, pemesanan (booking), dan catatan pembayaran di dalam admin sebagai catatan dari hasil negosiasi tersebut.

Konsekuensinya:

- Tidak ada formulir pemesanan online, tidak ada login pelanggan, dan tidak ada pembayaran online.
- **Catatan Pembayaran** (Payment Record) adalah catatan yang diisi staf, bukan transaksi — sistem tidak pernah memindahkan uang.
- Kalender ketersediaan di situs publik bersifat informatif; admin adalah sumber kebenaran.
- Data pelanggan yang disimpan minimal (nama, telepon, WhatsApp, alamat). Dokumen identitas diverifikasi langsung saat pengambilan kendaraan.

Keputusan ini didokumentasikan di [docs/adr/0001-whatsapp-first-operator-model.md](docs/adr/0001-whatsapp-first-operator-model.md).

## Arsitektur

Aplikasi ini adalah satu repo dengan dua bagian:

1. **Situs publik** (`/`) — katalog kendaraan read-only berbasis Blade + Livewire: halaman beranda, daftar kendaraan (dengan filter tipe), detail kendaraan (galeri, spesifikasi, harga, kalender ketersediaan), halaman tentang, dan halaman kontak. Setiap kendaraan memiliki tombol "Chat di WhatsApp".
2. **Dashboard admin** (`/admin`) — dibangun dengan Filament v3: manajemen kendaraan (termasuk upload foto dan perubahan status), pelanggan, pemesanan (dengan kalkulasi biaya otomatis dan transisi status), catatan pembayaran, dan pengguna staf (khusus Manajer).

Logika harga sewa dipisahkan ke `App\Services\Pricing\PricingCalculator` (murni, tanpa I/O) dengan aturan: minimum setengah hari (12 jam), 12–24 jam dihitung satu hari, tarif mingguan berlaku mulai 7 hari, tarif bulanan mulai 30 hari, dan denda keterlambatan (overage) dihitung per jam.

## Tech stack

- Laravel 11 (PHP 8.3)
- Filament v3 untuk admin
- Livewire 3 + Blade untuk situs publik
- Tailwind CSS (desain neomorphism: palet krem/clay — lihat `tailwind.config.js`)
- MySQL 8 untuk produksi, SQLite untuk pengembangan lokal
- Pest untuk pengujian
- Lokal Indonesia (`id`), mata uang IDR (disimpan sebagai integer, tanpa subunit)

## Setup

Prasyarat: PHP 8.2+, Composer, Node.js + npm.

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
npm install
npm run build
php artisan storage:link
php artisan serve
```

Buka `http://localhost` untuk situs publik dan `http://localhost/admin` untuk dashboard admin.

> **Catatan:** `php artisan storage:link` **wajib** dijalankan agar foto kendaraan yang diunggah di admin dapat tampil di situs publik. Jika lupa, kendaraan akan tampil tanpa foto (placeholder).

## Kredensial demo

Seeder membuat satu cabang, satu Manajer, satu Staf, dan 10 kendaraan demo.

| Peran    | Email                | Kata sandi |
|----------|----------------------|------------|
| Manajer  | `manager@rent.local` | `password` |
| Staf     | `staff@rent.local`   | `password` |

Manajer memiliki akses penuh (termasuk menu Pengaturan → Pengguna Staf). Staf hanya memiliki akses operasional (kendaraan, pelanggan, pemesanan, pembayaran).

## Alur demo

1. Login di `/admin` menggunakan `manager@rent.local` / `password`.
2. **Buat kendaraan** — menu Kendaraan → Buat. Isi identitas, pilih tipe (Mobil/SUV/Sepeda Motor), isi spesifikasi yang tampil sesuai tipe, set harga harian, unggah foto (opsional), lalu simpan.
3. **Buat pelanggan** — menu Pelanggan → Buat. Isi nama, telepon, WhatsApp, dan alamat.
4. **Buat pemesanan** — menu Pemesanan → Buat. Pilih pelanggan, pilih kendaraan (hanya yang dapat dipesan), atur rencana ambil/kembali, lalu simpan (status awal: Draf). Gunakan aksi **"Hitung Ulang Biaya"** di baris pemesanan untuk mengisi biaya dasar terhitung; sesuaikan **Jumlah Ditagih** bila perlu.
5. **Tandai Diambil** — dari daftar pemesanan, jalankan aksi "Tandai Diambil" (status menjadi Diambil dan waktu ambil aktual tercatat).
6. **Catat pembayaran** — menu Catatan Pembayaran → Buat, hubungkan ke pemesanan, isi jumlah, metode, dan waktu diterima.
7. **Tandai Dikembalikan** — jalankan aksi "Tandai Dikembalikan". Jika terlambat, "Hitung Ulang Biaya" akan menambahkan denda keterlambatan.
8. **Selesaikan** — setelah kendaraan kembali dan pembayaran lunas, jalankan aksi "Selesaikan" untuk menutup pemesanan.

Cek dampaknya: kendaraan dengan pemesanan aktif otomatis berstatus "Sedang Dirental" di situs publik, dan widget dashboard menampilkan ringkasan (sedang dirental, akan kembali hari ini, belum dibayar, tersedia).

## Pindah ke MySQL

Untuk produksi, ganti koneksi database di `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rent_specialist
DB_USERNAME=root
DB_PASSWORD=
```

Buat database `rent_specialist` lalu jalankan:

```bash
php artisan migrate --seed
```

Blok komentar MySQL di `.env.example` berisi nilai contoh untuk referensi.

## Struktur direktori utama

```
app/
  Enums/          # BookingStatus, PaymentMethod, StaffRole, VehicleStatus, VehicleType
  Filament/
    Resources/    # Resource Filament (kendaraan, pelanggan, pemesanan, pembayaran, staf)
    Widgets/      # StatsOverview (widget dashboard)
  Models/         # Booking, Branch, Customer, PaymentRecord, StaffUser, Vehicle
  Services/Pricing/  # PricingCalculator + PricingResult
database/
  factories/ seeders/ migrations/
resources/
  css/            # app.css (situs publik), filament/admin.css (tema admin)
  lang/id/        # app.php (situs publik), filament.php (admin)
routes/web.php    # rute publik (jangan ubah)
```

## Lisensi

MIT