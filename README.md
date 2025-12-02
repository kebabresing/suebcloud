# 🌌 Sueb Cloud Platform

**Akhmad Zamri Ardani | 202310370311406**  
Pemrograman Web C – Universitas Muhammadiyah Malang

---

## Ringkasan
Repositori ini sekarang terdiri dari dua bagian:

1. **Landing page statis** (`index.html`, `about.html`, `contact.php`) untuk kebutuhan presentasi front-end.
2. **REST API Laravel** (`suebcloud-backend/`) yang menyediakan CRUD `stored_files` lengkap dengan database MySQL, validasi request, Postman collection, dan file `.http` untuk demo.

Fokus tugas terbaru adalah memastikan backend meme
nuhi requirement:

- Tabel database khusus (`stored_files`) dengan tipe data terdefinisi.
- 5 endpoint utama (Get All + pagination/filter, Get Detail, Create, Update, Delete).
- Dokumentasi request melalui Postman collection dan file HTTP.

---

## Daftar Isi
- [Struktur Project](#struktur-project)
- [Frontend Ringkas](#frontend-ringkas)
- [Backend API](#backend-api)
- [Menjalankan Backend](#menjalankan-backend)
- [Dokumentasi & Demo](#dokumentasi--demo)
- [Migrasi ke Mesin Lain](#migrasi-ke-mesin-lain)
- [Catatan Tambahan](#catatan-tambahan)

---

## Struktur Project
```
├── index.html / about.html / contact.php
├── assets/ , main.js , style.css
├── docs/
│   ├── postman/suebcloud.postman_collection.json
│   └── requests/stored-files.http
└── suebcloud-backend/
    ├── app/
    │   ├── Http/Controllers/StoredFileController.php
    │   ├── Http/Requests/StoreStoredFileRequest.php
    │   └── Http/Requests/UpdateStoredFileRequest.php
    ├── database/migrations/2025_11_30_000000_create_stored_files_table.php
    ├── routes/api.php
    └── ... (struktur standar Laravel 11)
```

---

## Frontend Ringkas
- Multi-halaman dengan Tailwind CDN dan interaksi ringan via `main.js`.
- Form kontak `contact.php` dapat diuji dengan server PHP built-in.
- Widget status DigitalOcean tetap aktif untuk menunjukkan integrasi API publik.

Menjalankan front-end lokal:
```powershell
Set-Location -Path 'e:\PRAKTIKUM\PEMROGRAMAN WEB\suebcloud'
php -S 127.0.0.1:8000   # hanya bila ingin menguji contact.php
```
Buka `index.html` / `about.html` langsung di browser untuk melihat landing page.

---

## Backend API
### Tabel `stored_files`
Migrasi `database/migrations/2025_11_30_000000_create_stored_files_table.php` membuat kolom:

| Kolom        | Tipe                              | Keterangan                    |
|--------------|-----------------------------------|--------------------------------|
| `title`      | string(150)                       | Judul berkas                   |
| `description`| text nullable                     | Deskripsi opsional             |
| `category`   | enum `pribadi/kantor/kuliah/umum` | Kategori wajib                 |
| `size_mb`    | unsignedInteger                   | Ukuran file dalam MB           |
| `storage_path`| string                           | Lokasi penyimpanan             |
| `mime_type`  | string(120)                       | MIME                           |
| `is_public`  | boolean default false             | Status publik                  |
| `expires_at` | timestamp nullable                | Kadaluwarsa opsional           |
| `deleted_at` | softDeletes                       | Soft delete sesuai modul       |

### Endpoint CRUD
`routes/api.php` mendaftarkan `Route::apiResource('stored-files', StoredFileController::class)` sehingga tersedia:

| Endpoint | Deskripsi | Catatan |
|----------|-----------|---------|
| `GET /api/stored-files` | List dengan `limit`, `page`, `search`, `orderBy`, `sortBy`, `category`, `is_public` | Pagination via `paginate()` |
| `GET /api/stored-files/{id}` | Detail file | 404 otomatis bila tidak ditemukan |
| `POST /api/stored-files` | Create | Validasi via `StoreStoredFileRequest` |
| `PUT /api/stored-files/{id}` | Update | Validasi via `UpdateStoredFileRequest` |
| `DELETE /api/stored-files/{id}` | Soft delete | Field `deleted_at` terisi |

`StoredFileController` juga melakukan normalisasi `expires_at` menggunakan `preparePayload()`.

---

## Menjalankan Backend
```powershell
cd e:\PRAKTIKUM\PEMROGRAMAN WEB\suebcloud\suebcloud-backend
cp .env.example .env   # atau salin dari mesin lain
composer install
php artisan key:generate

# Konfigurasi koneksi MySQL di .env
php artisan migrate
php artisan serve
```
API dapat diakses di `http://127.0.0.1:8000/api/stored-files` (atau host yang digunakan).

---

## Dokumentasi & Demo
- **Postman**: impor `docs/postman/suebcloud.postman_collection.json`, set environment `base_url`. Semua 5 endpoint tersedia dengan contoh body.
- **VS Code REST Client**: `docs/requests/stored-files.http` berisi request GET/POST/PUT/DELETE; ubah variabel `@storedFileId` sesuai kebutuhan.
- **Validasi DB**: gunakan HeidiSQL/MySQL client untuk memeriksa tabel `stored_files`. Soft delete ditandai melalui kolom `deleted_at`.

Workflow demo yang direkomendasikan:
1. `GET /api/stored-files?limit=5&page=1&orderBy=created_at&sortBy=desc`
2. `POST /api/stored-files` dengan payload contoh.
3. `GET` detail ID yang baru dibuat.
4. `PUT` untuk mengubah sebagian field.
5. `DELETE` dan tunjukkan `deleted_at` terisi di database.

---

## Migrasi ke Mesin Lain
1. Clone repo / copy folder ke laptop presentasi.
2. Salin `.env` dan sesuaikan kredensial DB lokal.
3. Jalankan `composer install`, `php artisan migrate` (atau import dump hasil export dari PC bila ingin data yang sama).
4. Jalankan `php artisan serve` dan gunakan Postman collection yang sama.

Data database tidak otomatis ikut saat menyalin repo; gunakan export/import SQL atau seed melalui API untuk membuat contoh record baru.

---

## Catatan Tambahan
- Branch default di GitHub telah dipindah ke `main`. Gunakan `git push -u origin main` untuk update selanjutnya.
- File sensitif seperti `.env` tidak naik ke GitHub; simpan secara lokal atau gunakan `.env.example` sebagai template.
- Soft delete dipilih agar sesuai instruksi modul—baris akan hilang dari respon API, namun tetap terlihat di DB bila memeriksa kolom `deleted_at`.

---
