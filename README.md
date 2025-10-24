🌌 Sueb Space – Landing Page

Akhmad Zamri Ardani
Tugas Pemrograman Web C – Universitas XYZ

Deskripsi Singkat

Sueb Space adalah landing page sederhana yang dibuat menggunakan HTML, Tailwind CSS, JavaScript, dan PHP.
Tujuannya untuk mempraktikkan konsep front-end dan back-end dasar dalam konteks halaman pemasaran modern.

Fitur Utama

Struktur multi-halaman: index.html, about.html, contact.php

Hero section dan tampilan tim pada halaman About

Logo SVG terpusat (assets/logo.svg) juga digunakan sebagai favicon

Form kontak dengan validasi dan penyimpanan pesan di messages.txt

Efek interaktif: smooth scroll & reveal on scroll

Desain responsif dengan Tailwind CSS

Teknologi

Frontend: HTML, Tailwind CSS, JavaScript

Backend: PHP (untuk form kontak)

Assets: SVG, optional JPEG avatar

📁 Struktur Proyek
Sueb/
│
├── index.html          # Halaman utama
├── about.html          # Halaman About
├── contact.php         # Form kontak (PHP)
│
├── style.css           # Gaya kustom
├── main.js             # Logika interaktif client-side
│
└── assets/
    ├── logo.svg
    ├── akhmad.svg
    └── akhmad.jpg (opsional)

    🚀 Menjalankan Secara Lokal

Buka index.html langsung di browser untuk melihat tampilan statis.

Untuk menguji form kontak:

php -S 127.0.0.1:8000

lalu buka http://127.0.0.1:8000/contact.php

Rekomendasi Produksi

Gunakan database atau layanan email (misal PHPMailer + SMTP)
Tempatkan file data di luar webroot
Aktifkan HTTPS untuk keamanan
Tambahkan validasi dan sanitasi input lebih ketat

Rencana Pengembangan

Integrasi email notifikasi dengan PHPMailer
Migrasi pesan ke SQLite/MySQL
Tambah halaman Privacy Policy & Terms of Service

Lisensi: Bebas digunakan untuk keperluan pembelajaran atau pengembangan web dasar.
