# Sueb Space  Landing Page (Tugas Pemrograman Web C)

**Akhmad Zamri Ardani | 202310370311406**

## Deskripsi singkat
Proyek ini adalah sebuah landing page bernama "Sueb Space" yang dibuat sebagai bagian dari tugas mata kuliah Pemrograman Web C. Tujuannya adalah memperagakan praktik front-end dasar (HTML/CSS/JS) dan back-end ringan (PHP) dalam konteks halaman pemasaran sederhana.

## Ringkasan perubahan & fitur utama
- Struktur multi-halaman: `index.html`, `about.html`, `contact.php`.
- `about.html` telah diperbarui dengan hero yang ditingkatkan dan kartu tim yang menampilkan pemilik.
- Logo dipusatkan ke `assets/logo.svg` dan digunakan sebagai favicon.
- Tautan kontak diarahkan ke WhatsApp (nomor: `6287735372986`) dengan pesan pra-terisi pada beberapa CTA.
- Placeholder avatar pemilik tersedia di `assets/akhmad.svg`; Anda dapat menambahkan `assets/akhmad.jpg` untuk foto nyata.
- Testimonial section pada `index.html` dihapus untuk menyederhanakan tampilan.

## Teknologi
- HTML + Tailwind CSS (via CDN)
- Custom CSS: `style.css`
- JavaScript: `main.js` (interaksi, smooth-scroll, reveal-on-scroll, AJAX form)
- PHP: `contact.php` (validasi dasar, CSRF token, menyimpan pesan ke `messages.txt`)

## File penting
- `index.html`  Halaman landing utama
- `about.html`  Halaman About (diperbarui)
- `contact.php`  Halaman kontak + server-side processing
- `style.css`  Gaya kustom
- `main.js`  Logika interaktif client-side
- `assets/logo.svg`  Logo
- `assets/akhmad.svg`  Placeholder avatar pemilik
- `assets/akhmad.jpg`  (opsional) foto pemilik bila ditambahkan
- `messages.txt`  File teks yang berisi pesan (dibuat otomatis saat form berhasil dikirim)

## Menjalankan proyek (lokal)
1) Halaman statis
- Buka `index.html` atau `about.html` langsung di browser untuk melihat tampilan statis.

2) Menguji form kontak (PHP)
- Jalankan PHP built-in server dari folder proyek (PowerShell):

```powershell
Set-Location -Path 'e:\PRAKTIKUM\PEMROGRAMAN WEB\Sueb'
php -S 127.0.0.1:8000
```
- Buka http://127.0.0.1:8000/contact.php dan uji form kontak.

## Catatan penting
- Beberapa tautan membuka WhatsApp dengan nomor `6287735372986`. Ganti nomor tersebut bila perlu.
- `main.js` memiliki fallback bila jQuery CDN gagal dimuat, tetapi di lingkungan produksi lebih baik meng-host dependensi yang stabil.

## Keamanan & rekomendasi produksi
- Terapkan validasi dan sanitasi input yang lebih ketat.
- Gunakan database atau kirim email melalui SMTP (PHPMailer) alih-alih menyimpan pesan di file teks.
- Tempatkan file data di luar webroot dan gunakan HTTPS pada server produksi.

## Rencana pengembangan (opsional)
- Tambah PHPMailer + SMTP untuk notifikasi email.
- Migrasi penyimpanan pesan ke SQLite/MySQL dan buat halaman admin.
- Tambah halaman Privacy/Terms jika diperlukan.
- Tambah foto pemilik `assets/akhmad.jpg` dari lampiran untuk menggantikan placeholder.

---
README ini telah disusun ulang dan dirapikan agar profesional, serta mencantumkan nama pemilik dan tujuan penggunaan sebagai tugas Pemrograman Web C.


