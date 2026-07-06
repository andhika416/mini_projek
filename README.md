# LaporanKerja

Aplikasi Laravel untuk menyimpan, mengelola, dan mencetak laporan kerja bulanan.

## Fitur

- Autentikasi Laravel Breeze (login, registrasi, reset password, profil)
- Role `admin` dan `user`
- Middleware admin dan Policy per laporan
- CRUD laporan dengan tanggal, jam, koordinat GPS, tiga uraian, dan lampiran
- Pengambilan GPS langsung dari browser
- DataTables: pencarian, filter bulan/tahun, pagination, dan sorting
- PDF per laporan dan rekap PDF
- Pengelolaan role pengguna oleh admin
- Tampilan responsif dengan font Poppins

## Menjalankan secara lokal

Prasyarat: PHP 8.3+, Composer, Node.js 22+, dan MySQL 8+.

Buat database `laporan_kerja` melalui phpMyAdmin sebelum menjalankan perintah berikut. Konfigurasi lokal bawaan menggunakan MySQL pada `127.0.0.1:3306`, user `root`, dan password kosong sesuai bawaan Laragon.

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

Untuk development frontend gunakan `npm run dev`. Jika memakai disk `public`, jalankan:

```bash
php artisan storage:link
```

Akun demo setelah seeding:

- Admin: `admin@example.com` / `password`
- User: `user@example.com` / `password`

Ganti password akun demo sebelum aplikasi dipublikasikan.

## Deployment Vercel

Proyek menyediakan `api/index.php` dan `vercel.json` untuk runtime komunitas `vercel-php@0.7.4` (PHP 8.3). Vercel bersifat serverless dan filesystem-nya tidak persisten, sehingga production wajib memakai database eksternal serta object storage S3-compatible.

1. Push proyek ke GitHub lalu import repository di Vercel.
2. Tambahkan environment variables:

```dotenv
APP_NAME=LaporanKerja
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-anda.vercel.app
APP_KEY=base64:...
APP_LOCALE=id

DB_CONNECTION=pgsql
DB_HOST=...
DB_PORT=5432
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...
DB_SSLMODE=require

LOG_CHANNEL=stderr
CACHE_STORE=array
SESSION_DRIVER=cookie
VIEW_COMPILED_PATH=/tmp/views

REPORTS_DISK=s3
AWS_ACCESS_KEY_ID=...
AWS_SECRET_ACCESS_KEY=...
AWS_DEFAULT_REGION=...
AWS_BUCKET=...
AWS_ENDPOINT=...
AWS_USE_PATH_STYLE_ENDPOINT=true
```

3. Jalankan migration terhadap database production dari mesin lokal/CI:

```bash
php artisan migrate --force
```

4. Deploy ulang. Build script Vercel akan menjalankan build aset Vite melalui script Composer `vercel`.

`APP_KEY` dapat dibuat dengan `php artisan key:generate --show`. Jangan memakai SQLite atau `REPORTS_DISK=public` di Vercel karena data akan hilang ketika instance serverless diganti.

## Pengujian

```bash
php artisan test
```
