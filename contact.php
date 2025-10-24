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
  <header class="fixed top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-md border-b border-gray-200">
    <nav class="container mx-auto px-4 py-4">
      <div class="flex items-center justify-between">
        <a href="index.html" class="flex items-center gap-2" aria-label="Sueb Space — beranda">
          <img src="assets/logo.svg" alt="Sueb Space" width="32" height="32" loading="lazy">
          <span class="text-xl font-bold">Sueb Space</span>
        </a>

        <div class="hidden md:flex items-center gap-8">
          <a href="index.html" class="text-gray-600 hover:text-gray-900 transition">Home</a>
          <a href="about.html" class="text-gray-600 hover:text-gray-900 transition">About</a>
          <a href="https://wa.me/6287735372986?text=Halo%20Sueb%20Space%2C%20saya%20ingin%20info%20lebih%20lanjut" target="_blank" rel="noopener noreferrer" class="text-gray-600 hover:text-gray-900 transition">Contact</a>
        </div>

        <div class="hidden md:flex items-center gap-4">
          <button class="px-4 py-2 text-gray-600 hover:text-gray-900 transition">Masuk</button>
          <button class="px-6 py-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg hover:opacity-90 transition">Mulai Gratis</button>
        </div>

        <button id="mobile-menu-btn" class="md:hidden p-2" aria-label="Buka menu mobile" aria-controls="mobile-menu" aria-expanded="false">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>
      </div>

      <div id="mobile-menu" class="mobile-menu md:hidden">
        <div class="flex flex-col gap-4 pt-4 pb-2">
          <a href="index.html" class="text-gray-600 hover:text-gray-900 transition py-2">Home</a>
          <a href="about.html" class="text-gray-600 hover:text-gray-900 transition py-2">About</a>
          <a href="https://wa.me/6287735372986?text=Halo%20Sueb%20Space%2C%20saya%20ingin%20info%20lebih%20lanjut" target="_blank" rel="noopener noreferrer" class="text-gray-600 hover:text-gray-900 transition py-2">Contact</a>
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

  <footer class="bg-gray-900 text-white py-12 px-4">
    <div class="container mx-auto max-w-6xl text-center">
      <p class="text-sm text-gray-400">© 2025 Sueb Space</p>
    </div>
  </footer>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="main.js" defer></script>
</body>
</html>
