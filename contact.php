<?php
// Contact processor with CSRF token and AJAX support.
session_start();

function h($s){ return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
$errors = [];
$success = false;
$name = '';
$email = '';
$message = '';

// Generate CSRF token if not present
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(20));
}

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
  // Basic CSRF check
  $postedToken = $_POST['csrf_token'] ?? '';
  if (!hash_equals($_SESSION['csrf_token'], (string)$postedToken)) {
    $errors[] = 'Token keamanan tidak valid. Silakan muat ulang halaman.';
  }

  $name = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $message = trim($_POST['message'] ?? '');

  if ($name === '') $errors[] = 'Nama harus diisi.';
  if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email tidak valid.';
  if ($message === '') $errors[] = 'Pesan tidak boleh kosong.';

  if (empty($errors)){
    $entry = sprintf("[%s] %s <%s>\n%s\n\n", date('c'), $name, $email, $message);
    // Append to messages file
    file_put_contents(__DIR__ . '/messages.txt', $entry, FILE_APPEND | LOCK_EX);
    $success = true;
    // Clear values to avoid resubmission display
    $name = $email = $message = '';
    // rotate CSRF token after successful post
    $_SESSION['csrf_token'] = bin2hex(random_bytes(20));
  }

  if ($isAjax) {
    header('Content-Type: application/json');
    echo json_encode([ 'success' => $success, 'errors' => $errors ]);
    exit;
  }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Contact - Sueb Space</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" href="assets/logo.svg" type="image/svg+xml">
  <link rel="apple-touch-icon" href="assets/logo.svg">
  <meta name="theme-color" content="#3b82f6">
  <link rel="stylesheet" href="style.css">
</head>
<body class="bg-gray-50 text-gray-900">
  <header class="fixed top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-md border-b border-gray-200" role="banner">
    <nav class="container mx-auto px-4 py-4" aria-label="Navigasi utama">
      <div class="flex items-center justify-between">
        <a href="index.html" class="flex items-center gap-2" aria-label="Sueb Space — beranda">
          <img src="assets/logo.svg" alt="Sueb Space" width="32" height="32" loading="lazy">
          <span class="text-xl font-bold">Sueb Space</span>
        </a>

        <div class="hidden md:flex items-center gap-8" role="navigation" aria-label="Menu utama">
          <a href="index.html#fitur" class="text-gray-600 hover:text-gray-900 transition">Fitur</a>
          <a href="index.html#harga" class="text-gray-600 hover:text-gray-900 transition">Harga</a>
          <a href="about.html" class="text-gray-600 hover:text-gray-900 transition">Tentang Kami</a>
          <a href="index.html#faq" class="text-gray-600 hover:text-gray-900 transition">FAQ</a>
          <a href="contact.php" class="text-gray-900 font-semibold transition" aria-current="page">Kontak</a>
        </div>

        <div class="hidden md:flex items-center gap-4">
          <button class="px-4 py-2 text-gray-600 hover:text-gray-900 transition">Masuk</button>
          <button class="px-6 py-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg hover:opacity-90 transition to-pricing">
            Mulai Gratis
          </button>
        </div>

        <button id="mobile-menu-btn" class="md:hidden p-2" aria-label="Buka menu mobile" aria-controls="mobile-menu" aria-expanded="false">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>
      </div>

      <div id="mobile-menu" class="mobile-menu md:hidden" aria-label="Menu mobile">
        <div class="flex flex-col gap-4 pt-4 pb-2">
          <a href="index.html#fitur" class="text-gray-600 hover:text-gray-900 transition py-2">Fitur</a>
          <a href="index.html#harga" class="text-gray-600 hover:text-gray-900 transition py-2">Harga</a>
          <a href="about.html" class="text-gray-600 hover:text-gray-900 transition py-2">Tentang Kami</a>
          <a href="index.html#faq" class="text-gray-600 hover:text-gray-900 transition py-2">FAQ</a>
          <a href="contact.php" class="text-gray-900 font-semibold transition py-2" aria-current="page">Kontak</a>
          <button class="px-4 py-2 text-gray-600 hover:text-gray-900 transition text-left">Masuk</button>
          <button class="px-6 py-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg hover:opacity-90 transition to-pricing">
            Mulai Gratis
          </button>
        </div>
      </div>
    </nav>
  </header>

  <main class="pt-32 pb-20 px-4">
    <div class="container mx-auto max-w-3xl">
      <div class="text-center mb-8">
        <h1 class="text-4xl font-bold mb-2">Hubungi Kami</h1>
        <p class="text-gray-600">Isi form berikut untuk mengirim pesan ke tim kami.</p>
      </div>

      <div class="bg-white p-8 rounded-2xl border border-gray-200">
        <?php if ($success): ?>
          <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded">Terima kasih, pesan Anda telah diterima.</div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
          <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded">
            <ul class="list-disc ml-5">
              <?php foreach($errors as $err): ?>
                <li><?php echo h($err); ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <form id="contact-form" method="post" action="contact.php" class="space-y-4" data-ajax="true">
          <input type="hidden" name="csrf_token" value="<?php echo h($_SESSION['csrf_token']); ?>">
          <div>
            <label class="block text-sm font-medium text-gray-700">Nama</label>
            <input type="text" name="name" value="<?php echo h($name); ?>" class="mt-1 block w-full rounded-md border-gray-200 shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="email" value="<?php echo h($email); ?>" class="mt-1 block w-full rounded-md border-gray-200 shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Pesan</label>
            <textarea name="message" rows="5" class="mt-1 block w-full rounded-md border-gray-200 shadow-sm focus:ring-blue-500 focus:border-blue-500" required><?php echo h($message); ?></textarea>
          </div>
          <div>
            <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded font-semibold">Kirim Pesan</button>
          </div>
        </form>
      </div>

    </div>
  </main>

  <footer id="kontak" class="bg-gray-900 text-white py-16 px-4" role="contentinfo">
    <div class="container mx-auto max-w-6xl">
      <div class="grid md:grid-cols-4 gap-8 mb-12">
        <div>
          <div class="flex items-center gap-2 mb-4" aria-label="Logo Sueb Space">
            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
              <rect width="32" height="32" rx="8" fill="url(#footer-logo-gradient)"/>
              <path d="M12 10L16 14L12 18" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M16 10L20 14L16 18" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <defs>
                <linearGradient id="footer-logo-gradient" x1="0" y1="0" x2="32" y2="32">
                  <stop stop-color="#3b82f6"/>
                  <stop offset="1" stop-color="#8b5cf6"/>
                </linearGradient>
              </defs>
            </svg>
            <span class="text-xl font-bold">Sueb Space</span>
          </div>
          <p class="text-gray-400 text-sm">Platform cloud terpercaya untuk developer modern.</p>
        </div>

        <div>
          <h4 class="font-semibold mb-4">Produk</h4>
          <ul class="space-y-2 text-sm text-gray-400">
            <li><a href="index.html#fitur" class="hover:text-white transition">Fitur</a></li>
            <li><a href="index.html#harga" class="hover:text-white transition">Harga</a></li>
          </ul>
        </div>

        <div>
          <h4 class="font-semibold mb-4">Perusahaan</h4>
          <ul class="space-y-2 text-sm text-gray-400">
            <li><a href="about.html" class="hover:text-white transition">Tentang Kami</a></li>
            <li><a href="contact.php" aria-current="page" class="hover:text-white transition">Kontak</a></li>
          </ul>
        </div>
      </div>

      <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
        <p class="text-sm text-gray-400">© 2025 Sueb Space. All rights reserved.</p>
        <div class="flex gap-4">
          <a href="#" class="text-gray-400 hover:text-white transition">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
              <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
            </svg>
          </a>
          <a href="#" class="text-gray-400 hover:text-white transition">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
            </svg>
          </a>
          <a href="#" class="text-gray-400 hover:text-white transition">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
              <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
            </svg>
          </a>
        </div>
      </div>
    </div>
  </footer>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="main.js" defer></script>
</body>
</html>
