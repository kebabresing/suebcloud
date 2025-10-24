#  🌌 Sueb Space  Landing Page

**Akhmad Zamri Ardani | 202310370311406
Universitas Muhammadiyah Malang**

_Proyek ini dibuat sebagai tugas mata kuliah Pemrograman Web C._

---

## Ringkasan
Sueb Space adalah landing page sederhana yang menggabungkan markup statis (HTML), utilitas Tailwind CSS, interaksi ringan di client (JavaScript), dan endpoint kontak sederhana dengan PHP. Tujuannya: menunjukkan praktik front-end dasar dan backend minimal untuk keperluan tugas dan demo.

## Daftar Isi
- [Fitur utama](#fitur-utama)
- [Teknologi](#teknologi)
- [Struktur proyek](#struktur-proyek)
- [Menjalankan secara lokal](#menjalankan-secara-lokal)
- [Pengujian form kontak (PHP)](#pengujian-form-kontak-php)
- [Rekomendasi produksi](#rekomendasi-produksi)
- [Rencana pengembangan](#rencana-pengembangan)
- [Kontak & Catatan](#kontak--catatan)

---

## Fitur Utama
- Multi-halaman: `index.html`, `about.html`, `contact.php`.
- Halaman About yang telah diperbarui (hero, nilai inti, kartu tim  menampilkan pemilik).
- Kontak: form AJAX + endpoint PHP yang menyimpan pesan ke `messages.txt` (demo).
- Interaksi: smooth-scroll, reveal-on-scroll, back-to-top, dan CTA yang mengarah ke WhatsApp.
- Responsive: layout dibuat dengan Tailwind CSS.

## Teknologi
- Frontend: HTML, Tailwind CSS (CDN), custom `style.css`.
- Interaksi: `main.js` (vanilla JS with jQuery-friendly patterns).
- Backend/demo: `contact.php` (PHP built-in usage supported).

## Struktur Proyek
```
e:/PRAKTIKUM/PEMROGRAMAN WEB/Sueb
 index.html
 about.html
 contact.php
 style.css
 main.js
 assets/
    logo.svg
    akhmad.svg
```

## Menjalankan secara lokal
1. Buka file statis langsung (double-click) untuk melihat `index.html` atau `about.html` tanpa server.

2. Untuk menguji form kontak (PHP) jalankan server built-in dari folder proyek (PowerShell):

```powershell
Set-Location -Path 'e:\PRAKTIKUM\PEMROGRAMAN WEB\Sueb'
php -S 127.0.0.1:8000
```

Lalu buka: http://127.0.0.1:8000/contact.php

Saat form berhasil submit, pesan akan ditambahkan ke `messages.txt` di folder proyek.

## Pengujian dan Debugging
- Jika form tidak terkirim, lihat DevTools  Console / Network untuk respons server.
- Jika jQuery CDN gagal, `main.js` memiliki fallback ke fetch/vanilla. Untuk produksi, host dependensi yang stabil.

## Rekomendasi Produksi
- Jangan menyimpan data produksi di file teks  gunakan database atau layanan email (PHPMailer + SMTP).
- Tempatkan penyimpanan pesan di luar webroot dan lindungi aksesnya.
- Terapkan HTTPS, sanitasi/validasi input, dan rate-limiting / CAPTCHA untuk formulir.

## Rencana pengembangan (opsional)
- Integrasi email/SMTP (PHPMailer).
- Migrasi pesan ke SQLite/MySQL + halaman admin.
- Tambah halaman Privacy & Terms.
- Tambah image assets resmi (`assets/akhmad.jpg`) bila tersedia.

## Kontak & Catatan
- WhatsApp demo number (dipakai di beberapa tautan): `6287735372986`  ganti sesuai kebutuhan.
- Pemilik / kontak proyek: Akhmad Zamri Ardani.

---
