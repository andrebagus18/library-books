<?php
session_start();
require_once 'config/database.php';
require_once 'functions/helper.php';

$books = fetchAll("SELECT * FROM books WHERE stock > 0 ORDER BY id DESC");


$carouselBooks = getCarouselBooks($pdo);
$pagination = getPaginatedBooks();
$books = $pagination['data'];

$isLogin = isLogin();
$isAdmin = $isLogin ? isAdmin() : false;
$link = $isLogin ? ($isAdmin ? 'public/admin.php' : 'public/member.php') : 'public/login.php';

//ambil session user login
if ($isLogin) {
  $member = fetchOne(
    "SELECT * FROM members WHERE user_id = ?",
    [$_SESSION['user_id']]
  );
}
// Logic Pinjam
if (isset($_POST['pinjam'])) {
  $book_id = $_POST['book_id'];
  $member_id = $member['id'];
  $book = fetchOne(
    "SELECT stock FROM books WHERE id = ?",
    [$book_id]
  );
  if ($book && $book['stock'] > 0) {
    query("UPDATE books SET stock = stock - 1 WHERE id=?", [$book_id]);
    query("INSERT INTO loans (book_id, member_id, loan_date, due_date, status) VALUES (?, ?, CURRENT_DATE, CURRENT_DATE + INTERVAL '7 days', 'dipinjam')", [$book_id, $member_id]);
    redirect('index.php');
  }
}


?>

<!doctype html>
<html lang="id" style="scroll-behavior: smooth;">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>LiBooks - Koleksi Buku Terbesar & Terlengkap</title>
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet" />
  <!-- Tailwind CSS via CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: "#A86E43",
            secondary: "#E0E0E0",
            accent: "#F5F5F5",
          },
          fontFamily: {
            outfit: ["Outfit", "sans-serif"],
          },
        },
      },
    };
  </script>
  <style>
    body {
      font-family: "Outfit", sans-serif;
      background-color: #e0e0e0;
    }

    .glass-nav {
      background: rgba(224, 224, 224, 0.85);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    }

    .nav-link {
      position: relative;
      padding-bottom: 2px;
    }

    .nav-link::after {
      content: "";
      position: absolute;
      width: 0;
      height: 2px;
      bottom: -2px;
      left: 50%;
      background-color: #a86e43;
      transition: all 0.3s ease-in-out;
      transform: translateX(-50%);
    }

    .nav-link:hover::after {
      width: 100%;
    }

    .hero-gradient {
      background: radial-gradient(circle at top right,
          rgba(168, 110, 67, 0.05),
          transparent);
    }

    .floating-sticker {
      animation: float 6s ease-in-out infinite;
    }

    @keyframes float {
      0% {
        transform: translateY(0px) rotate(0deg);
      }

      50% {
        transform: translateY(-20px) rotate(5deg);
      }

      100% {
        transform: translateY(0px) rotate(0deg);
      }
    }

    .carousel-container {
      scroll-behavior: smooth;
    }

    .subscribe-alert {
      animation: slideIn 0.3s ease-out forwards;
    }

    @keyframes slideIn {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Modal Animation */
    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: scale(0.95);
      }

      to {
        opacity: 1;
        transform: scale(1);
      }
    }

    .animate-fadeIn {
      animation: fadeIn 0.25s ease-out forwards;
    }

    /* Carousel Fade */
    #carousel-content {
      transition: opacity 0.4s ease;
    }

    #carousel-content.fade-out {
      opacity: 0;
    }

    .carousel-info {
      display: flex;
      flex-direction: column;
      gap: 30px;
    }

    /* Slide Animation */
    #carousel-track {
      display: flex;
      transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
      will-change: transform;
    }

    .carousel-slide {
      flex: 0 0 100%;
      width: 100%;
      min-height: 500px;
      display: flex;
      align-items: center;
    }

    .carousel-image {
      width: 100%;
      max-width: 320px;
      height: 450px;
      object-fit: cover;
      border-radius: 30px;
    }
  </style>
</head>

<body class="text-slate-800 antialiased overflow-x-hidden">
  <!-- Navbar -->
  <nav class="fixed top-0 left-0 right-0 z-50 glass-nav">
    <div class="container mx-auto px-6 py-4 flex items-center justify-between">
      <!-- Left: Logo -->
      <div class="flex items-center">
        <a href="#" id="logo" class="text-2xl font-bold text-primary tracking-tight">LiBooks</a>
      </div>

      <!-- Middle: Navigation -->
      <div class="hidden md:flex items-center space-x-8">
        <a href="#katalog" class="nav-link font-medium hover:text-primary transition-colors">Koleksi Buku</a>
        <a href="#kategori" class="nav-link font-medium hover:text-primary transition-colors">Kategori</a>
        <a href="#carousel" class="nav-link font-medium hover:text-primary transition-colors">Terbaru</a>
        <a href="<?= $link ?>" class="nav-link font-medium hover:text-primary transition-colors">Dashboard</a>
      </div>

      <!-- Right: Auth -->
      <?php if ($isLogin) : ?>
        <div class="flex items-center space-x-4">
          <a href="public/logout.php"
            class="bg-primary text-white px-6 py-2 rounded-full font-semibold hover:bg-opacity-90 transition-all shadow-lg shadow-primary/20">Logout <?php $_SESSION['email'] ?></a>
        </div>
      <?php else : ?>
        <div class="flex items-center space-x-4">
          <a href="public/login.php" class="nav-link font-semibold text-slate-700 hover:text-primary transition-colors">Login</a>
          <a href="public/login.php#register"
            class="bg-primary text-white px-6 py-2 rounded-full font-semibold hover:bg-opacity-90 transition-all shadow-lg shadow-primary/20">Sign
            Up</a>
        </div>
      <?php endif; ?>

      <!-- Mobile Menu Toggle -->
      <div class="md:hidden">
        <button id="menu-btn" class="text-slate-800">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
          </svg>
        </button>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <section id="beranda" class="relative min-h-screen flex items-center hero-gradient">
    <div class="container mx-auto px-6 md:px-[70px] grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
      <!-- Left Column: Content -->
      <div class="order-2 lg:order-1 space-y-8">
        <div class="space-y-4">
          <h1 class="text-6xl md:text-8xl font-bold text-primary leading-tight">
            The LiBooks,
          </h1>
          <p class="text-lg md:text-xl text-slate-600 max-w-lg leading-relaxed">
            Jelajahi koleksi buku terbesar. Baca cerita dari
            mana saja, kapan saja. Temukan buku favoritmu dan biarkan imajinasimu terbang tinggi bersama LiBooks.
          </p>
        </div>

        <!-- Search Box -->
        <div
          class="flex flex-col sm:flex-row items-center bg-white p-2 rounded-2xl shadow-xl max-w-xl group focus-within:ring-2 focus-within:ring-primary/20 transition-all">
          <div class="flex-grow flex items-center px-4 w-full">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400 mr-3" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input type="text" placeholder="Cari judul atau penulis buku"
              class="w-full py-3 outline-none text-slate-700 bg-transparent" id="search-input" />
          </div>
          <button type="button" id="button-search"
            class="bg-primary text-white px-10 py-3 rounded-xl font-bold w-full sm:w-auto hover:bg-opacity-90 transition-all active:scale-95 shadow-md shadow-primary/30">
            Jelajah
          </button>
        </div>

        <!-- Social Proof -->
        <div class="flex items-center space-x-4 pt-4">
          <div class="flex -space-x-3">
            <div class="w-10 h-10 rounded-full border-2 border-secondary bg-slate-300 flex items-center justify-center">
              <svg class="w-6 h-6 text-slate-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd">
                </path>
              </svg>
            </div>
            <div class="w-10 h-10 rounded-full border-2 border-secondary bg-slate-400 flex items-center justify-center">
              <svg class="w-6 h-6 text-slate-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd">
                </path>
              </svg>
            </div>
            <div class="w-10 h-10 rounded-full border-2 border-secondary bg-slate-500 flex items-center justify-center">
              <svg class="w-6 h-6 text-slate-200" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd">
                </path>
              </svg>
            </div>
          </div>
          <p class="text-sm text-slate-500 font-medium">
            Bergabung dengan 10k+ pembaca aktif
          </p>
        </div>
      </div>

      <!-- Right Column: Visual -->
      <div class="order-1 lg:order-2 relative flex justify-center items-center overflow-hidden py-12">
        <div class="absolute w-[120%] h-[120%] bg-primary/5 rounded-full blur-3xl -z-10"></div>
        <div class="relative w-full max-w-sm">
          <img src="hero.png" alt="LiBooks Hero"
            class="w-full h-auto drop-shadow-2xl rounded-3xl" />
        </div>
      </div>
    </div>
  </section>

  <!-- Categories Section -->
  <section id="kategori" class="py-16 bg-white">
    <div class="container mx-auto px-6 pt-6 md:px-[70px]">
      <div class="text-center mb-10">
        <h2 class="text-4xl font-bold text-primary">Kategori</h2>
      </div>
      <div class="grid grid-cols-2 md:grid-cols-5 gap-6">
        <!-- Category 1 -->
        <div class="group relative overflow-hidden rounded-2xl aspect-square shadow-md transition-all duration-300 hover:-translate-y-2 hover:shadow-xl cursor-pointer">
          <img src="images/foto1.jpeg" alt="Fiksi" class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
          <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end p-4">
            <span class="text-white font-bold">Fiksi</span>
          </div>
        </div>
        <!-- Category 2 -->
        <div class="group relative overflow-hidden rounded-2xl aspect-square shadow-md transition-all duration-300 hover:-translate-y-2 hover:shadow-xl cursor-pointer">
          <img src="images/foto2.jpeg" alt="Non-Fiksi" class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
          <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end p-4">
            <span class="text-white font-bold">Non-Fiksi</span>
          </div>
        </div>
        <!-- Category 3 -->
        <div class="group relative overflow-hidden rounded-2xl aspect-square shadow-md transition-all duration-300 hover:-translate-y-2 hover:shadow-xl cursor-pointer">
          <img src="images/foto3.jpeg" alt="Sains" class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
          <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end p-4">
            <span class="text-white font-bold">Sains</span>
          </div>
        </div>
        <!-- Category 4 -->
        <div class="group relative overflow-hidden rounded-2xl aspect-square shadow-md transition-all duration-300 hover:-translate-y-2 hover:shadow-xl cursor-pointer">
          <img src="images/foto4.jpeg" alt="Teknologi" class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
          <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end p-4">
            <span class="text-white font-bold">Teknologi</span>
          </div>
        </div>
        <!-- Category 5 -->
        <div class="group relative overflow-hidden rounded-2xl aspect-square shadow-md transition-all duration-300 hover:-translate-y-2 hover:shadow-xl cursor-pointer">
          <img src="images/foto5.jpeg" alt="Anak-Anak" class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
          <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end p-4">
            <span class="text-white font-bold">Anak-Anak</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Book Catalog Section -->
  <section id="katalog" class="py-24 bg-white/50">
    <div class="container mx-auto px-6 md:px-[70px]">
      <div class="text-center mb-16 space-y-4">
        <h2 class="text-4xl md:text-5xl font-bold text-primary">
          Koleksi Buku Kami
        </h2>
        <p class="text-slate-600 max-w-2xl mx-auto">
          Jelajahi berbagai genre dan temukan cerita yang menginspirasi.
          Berikut adalah daftar buku terbaru yang tersedia di perpustakaan
          kami.
        </p>
      </div>

      <!-- Book Grid -->
      <div id="book-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
        <!-- Books will be injected here -->
        <?php if (count($books) > 0) : ?>
          <?php foreach ($books as $book) : ?>
            <?php $isLogin = isLogin();
            $canBorrow = $isLogin && $book['stock'] > 0;
            $isAvailable = $book['stock'] > 0; ?>
            <div class="group bg-white rounded-3xl overflow-hidden shadow-lg transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl border border-secondary/20 <?= !$isAvailable ? "opacity-70" : "" ?>">
              <div class="relative h-64 overflow-hidden">
                <img src="<?= $book['image_url'] ? $book['image_url'] : 'images/foto15.jpeg' ?>" alt="<?= htmlspecialchars($book['title']) ?>"
                  class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                <div class="absolute top-4 right-4">
                  <span class="px-3 py-1 rounded-md text-xs font-bold uppercase tracking-wider <?= $isAvailable ? "bg-green-100 text-green-600" : "bg-red-100 text-red-600" ?>">
                    <?= $isAvailable ? "Tersedia" : "Stok Habis" ?>
                  </span>
                </div>
              </div>
              <div class="p-6 space-y-4">
                <div class="space-y-1">
                  <h3 class="text-xl font-bold text-slate-800 line-clamp-1"><?= htmlspecialchars($book['title']) ?></h3>
                  <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-primary"><?= htmlspecialchars($book['author']) ?></p>
                    <span class="text-xs font-bold px-2 py-1 bg-secondary/30 rounded-lg text-slate-500">Stok: <?= $book['stock'] ?></span>
                  </div>
                </div>
                <div class="flex items-center text-xs text-slate-400 space-x-3">
                  <span><?= htmlspecialchars($book['publisher']) ?></span>
                  <span>•</span>
                  <span><?= $book['year'] ?></span>
                </div>
                <p class="text-sm text-slate-500 line-clamp-2 leading-relaxed">
                  <?= htmlspecialchars($book['description'] ?? 'Tidak ada deskripsi.') ?>
                </p>
                <form method="POST" class="grid grid-cols-2 gap-3 pt-2">
                  <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                  <input type="hidden" name="member_id" value="<?= $member['id'] ?>">
                  <button <?= !$canBorrow ? "disabled" : "" ?> type="submit" name="pinjam" class="py-2.5 rounded-xl font-bold text-sm transition-all <?= $canBorrow ? "bg-green-500 text-white hover:bg-green-600 shadow-md shadow-green-200" : "border border-primary text-primary cursor-not-allowed opacity-60" ?>">
                    <?= !$isLogin
                      ? "Pinjam Buku"
                      : ($isAvailable ? "Pinjam Buku" : "Stok Habis")
                    ?>
                  </button>
                  <button type="button" onclick='openModal(<?= json_encode($book) ?>)' class="py-2.5 rounded-xl font-bold text-sm border border-secondary text-slate-600 text-center hover:bg-secondary/10 transition-all">
                    Detail
                  </button>
                </form>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else : ?>
          <div class="col-span-full text-center py-20">
            <h4 class="text-slate-500 text-lg font-medium">
              Tidak ada buku yang ditemukan.
            </h4>
          </div>
        <?php endif; ?>
      </div>

      <!-- Pagination -->
      <div id="pagination" class="flex justify-center items-center space-x-2 mt-16">
        <!-- Pagination buttons will be injected here -->
        <!-- Prev Button -->
        <?php
        $firstPage = ($pagination['currentPage'] === 1);
        $prevClass = $firstPage ? "bg-secondary/20 text-slate-300 cursor-not-allowed" : "bg-white text-slate-600 hover:text-primary border border-secondary/30 shadow-sm";
        ?>
        <button type="button" <?= $firstPage ? 'disabled' : '' ?> onclick="changePage(<?= $pagination['currentPage'] - 1 ?>)" class="w-10 h-10 flex items-center justify-center rounded-xl font-bold transition-all">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
          </svg>
        </button>
        <?php for ($i = 1; $i <= $pagination['totalPage']; $i++) : ?>
          <?php $isCurrent = ($i === $pagination['currentPage']);
          $isPage = $isCurrent ? "bg-primary text-white shadow-lg shadow-primary/30" : "bg-white text-slate-400 hover:text-primary border border-secondary/30";
          ?>
          <button type="button" onclick="changePage(<?= $i ?>)" class="w-10 h-10 rounded-xl font-bold transition-all <?= $isPage ?>">
            <?= $i ?>
          </button>
        <?php endfor; ?>

        <!-- Next Button -->
        <?php
        $lastPage = ($pagination['currentPage']) >= $pagination['totalPage'];
        $nextClass = $firstPage ? "bg-secondary/20 text-slate-300 cursor-not-allowed" : "bg-white text-slate-600 hover:text-primary border border-secondary/30 shadow-sm";
        ?>
        <button type="button" <?= $lastPage ? 'disabled' : '' ?> onclick="changePage(<?= $pagination['currentPage'] + 1 ?>)" class="w-10 h-10 flex items-center justify-center rounded-xl font-bold transition-all">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
          </svg>
        </button>
      </div>
    </div>
  </section>

  <!-- Carousel Section -->
  <section id="carousel" class="py-24 bg-accent/30 overflow-hidden">
    <div class="container mx-auto px-6 md:px-[70px]">
      <div class="text-center mb-8 space-y-2">
        <h2 class="text-4xl md:text-4xl font-bold text-primary">
          Terbaru
        </h2>
        <p class="text-slate-500 text-sm md:text-base">
          Koleksi buku pilihan yang baru saja tiba di perpustakaan kami. Jangan sampai ketinggalan!
        </p>
      </div>

      <div class="relative">
        <!-- Viewport -->
        <div class="overflow-hidden rounded-[40px] shadow-2xl bg-white border border-secondary/20">
          <div id="carousel-track">
            <!-- Slides will be injected here -->
          </div>
        </div>

        <!-- Tombol PREV -->
        <button onclick="prevSlide()" id="btn-prev" aria-label="Sebelumnya"
          class="absolute -left-6 md:-left-10 top-1/2 -translate-y-1/2 z-20
                 w-12 h-12 rounded-full bg-primary text-white
                 flex items-center justify-center
                 shadow-lg hover:bg-amber-700 transition-all active:scale-95">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </button>

        <!-- Tombol NEXT -->
        <button onclick="nextSlide()" id="btn-next" aria-label="Berikutnya"
          class="absolute -right-6 md:-right-10 top-1/2 -translate-y-1/2 z-20
                 w-12 h-12 rounded-full bg-primary text-white
                 flex items-center justify-center
                 shadow-lg hover:bg-amber-700 transition-all active:scale-95">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </button>
      </div>
    </div>
  </section>

  <!-- Subscribe Section -->
  <section id="subscribe" class="py-24">
    <div class="container mx-auto px-6 md:px-[70px]">
      <div class="text-center mb-8 space-y-2">
        <h2 class="text-4xl font-bold text-primary">Mari Terhubung!</h2>
        <p class="text-slate-500 max-w-xl mx-auto">
          Berlangganan newsletter kami untuk mendapatkan rekomendasi buku terbaik dan promo menarik setiap minggunya.
        </p>
      </div>

      <div
        class="bg-primary rounded-[40px] p-12 text-center text-white space-y-8 shadow-2xl shadow-primary/20 relative overflow-hidden">
        <!-- Ornament -->
        <div class="absolute -top-20 -left-20 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-20 -right-20 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>

        <div class="relative z-10 max-w-lg mx-auto space-y-4">
          <div class="flex flex-col sm:flex-row gap-3">
            <input type="email" id="subscribe-email" placeholder="Alamat email Anda"
              class="flex-grow px-6 py-4 rounded-2xl text-slate-800 outline-none focus:ring-2 focus:ring-white/50 transition-all" />
            <button onclick="handleSubscribe()"
              class="bg-white text-primary px-8 py-4 rounded-2xl font-bold hover:bg-secondary transition-all active:scale-95 shadow-xl">
              Subscribe
            </button>
          </div>
          <div id="subscribe-msg" class="hidden">
            <div
              class="subscribe-alert inline-flex items-center space-x-2 bg-green-500/20 border border-green-500/30 px-4 py-2 rounded-xl text-green-100 text-sm">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                  d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                  clip-rule="evenodd"></path>
              </svg>
              <span>Terima kasih telah subscribe</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="mt-16 bg-primary py-12 text-white border-t border-white/10">
    <div class="container mx-auto px-6 md:px-[70px]">
      <div class="flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="text-2xl font-bold tracking-tight">LiBooks</div>
        <div class="text-white/60 text-sm font-medium">
          &copy; 2026 LiBooks. Hak Cipta Dilindungi.
        </div>
        <div class="flex space-x-6 text-sm opacity-80">
          <a href="#" class="hover:text-white transition-colors">Syarat & Ketentuan</a>
          <a href="#" class="hover:text-white transition-colors">Kebijakan Privasi</a>
        </div>
      </div>
    </div>
  </footer>

  <!-- Modal Detail Buku -->
  <div id="modal-detail"
    class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/50 backdrop-blur-sm p-4">
    <div
      class="bg-white rounded-[30px] shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto p-8 relative animate-fadeIn">
      <!-- Tombol tutup -->
      <button onclick="closeModal()" class="absolute top-4 right-4 text-slate-400 hover:text-primary transition-colors">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
      <!-- Konten modal diisi via JS -->
      <div id="modal-body"></div>
    </div>
  </div>

  <script>
    const carouselBooks = <?= json_encode($carouselBooks) ?>;

    // ===== MODAL DETAIL =====
    function openModal(carouselBooks) {
      const modal = document.getElementById('modal-detail');
      const body = document.getElementById('modal-body');
      const image = carouselBooks.image_url ? `images/${carouselBooks.image_url}` : 'images/foto1.jpeg';

      body.innerHTML = `
          <div class="flex flex-col sm:flex-row gap-6 items-start">
            <img src="${image}" alt="${carouselBooks.title}"
                 onerror="this.onerror=null;this.src='images/foto1.jpeg'"
                 class="w-full sm:w-40 h-56 object-cover rounded-2xl shadow-lg flex-shrink-0">
            <div class="space-y-3 flex-1">
              <h3 class="text-2xl font-bold text-slate-800">${carouselBooks.title}</h3>
              <p class="text-primary font-semibold">${carouselBooks.author}</p>
              <div class="flex gap-4 text-sm text-slate-400">
                <span>${carouselBooks.publisher}</span>
                <span>•</span>
                <span>${carouselBooks.year}</span>
              </div>
              <span class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider ${carouselBooks.stock > 0 ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'}">
                ${carouselBooks.stock > 0 ? 'Tersedia — Stok: ' + carouselBooks.stock : 'Stok Habis'}
              </span>
              <p class="text-slate-500 leading-relaxed text-sm">${carouselBooks.description}</p>
            </div>
          </div>
        `;
      modal.classList.remove('hidden');
      modal.classList.add('flex');
      document.body.style.overflow = 'hidden';
    }

    function closeModal() {
      const modal = document.getElementById('modal-detail');
      modal.classList.add('hidden');
      modal.classList.remove('flex');
      document.body.style.overflow = 'auto';
    }

    document.addEventListener('click', function(e) {
      const modal = document.getElementById('modal-detail');
      if (e.target === modal) closeModal();
    });

    // ===== SEARCH =====
    async function handleSearch() {
      const keyword = document.getElementById('search-input').value.trim();
      const response = await fetch(`?search=${keyword}`);
      const html = await response.text();
      const parser = new DOMParser();
      const doc = parser.parseFromString(html, 'text/html');
      const newContent = doc.querySelector('#katalog');

      document.querySelector('#katalog').innerHTML =
        newContent.innerHTML;
      history.pushState({}, '', `?search=${keyword}`);
      document.getElementById("katalog")
        .scrollIntoView({
          behavior: "smooth"
        });
    }

    // event enter dan klik
    document.getElementById('search-input').addEventListener('keypress', function(e) {
      if (e.key === 'Enter') handleSearch();
    });
    document.getElementById('button-search').addEventListener('click', handleSearch);

    // Helper potong kata
    function truncateWords(text, maxWords) {
      const words = text.split(' ');
      if (words.length <= maxWords) return text;
      return words.slice(0, maxWords).join(' ') + '...';
    }

    function getSynopsisLimit() {
      if (window.innerWidth >= 1024) return 300;
      if (window.innerWidth >= 768) return 200;
      return 100;
    }

    async function changePage(page) {
      const response = await fetch(`?page=${page}`);
      const html = await response.text();
      const parser = new DOMParser();
      const doc = parser.parseFromString(html, 'text/html');
      const newContent = doc.querySelector('#katalog');

      document.querySelector('#katalog').innerHTML =
        newContent.innerHTML;

      history.pushState({}, '', `?page=${page}`);
      document.getElementById("katalog")
        .scrollIntoView({
          behavior: "smooth"
        });
    }

    let currentSlide = 0;

    function renderCarousel() {
      const track = document.getElementById("carousel-track");
      track.innerHTML = carouselBooks.map((book, index) => {
        const sinopsisTampil = truncateWords(book.description, getSynopsisLimit());
        // book.isCarousel = true; //  modal logic

        return `
          <div class="carousel-slide p-8 md:p-12">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center text-center lg:text-left">
              <!-- Left: Cover -->
              <div class="lg:col-span-5 flex justify-center">
                <div class="relative group">
                  <div class="absolute inset-0 bg-primary/20 rounded-[30px] blur-2xl group-hover:bg-primary/30 transition-all"></div>
                  <img src="images/${book.image_url}" alt="${book.title}" 
                       onerror="this.onerror=null; this.src='images/foto1.jpeg'"
                       class="relative carousel-image shadow-2xl transform group-hover:scale-105 transition-transform duration-500">
                </div>
              </div>
              
              <!-- Right: Info -->
              <div class="lg:col-span-7 carousel-info">
                <div class="space-y-3">
                  <span class="inline-block px-4 py-1.5 rounded-full bg-primary/10 text-primary font-bold text-sm uppercase tracking-widest">${book.category_name}</span>
                  <h2 class="text-4xl md:text-5xl font-bold text-slate-800 leading-tight">${book.title}</h2>
                  <p class="text-xl font-medium text-primary/80 italic">${book.author}</p>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm font-semibold text-slate-500 border-y border-secondary/30 py-3">
                  <div class="space-y-1">
                    <span class="block text-slate-400 font-normal">Penerbit</span>
                    ${book.publisher}
                  </div>
                  <div class="space-y-1">
                    <span class="block text-slate-400 font-normal">Tahun</span>
                    ${book.year}
                  </div>
                  <div class="space-y-1">
                    <span class="block text-slate-400 font-normal">Stok</span>
                    ${book.stock}
                  </div>
                  <div class="space-y-1">
                    <span class="block text-slate-400 font-normal">Status</span>
                    <span class="${book.stock > 0 ? "text-green-500" : "text-red-500"}">${book.stock > 0 ? "Tersedia" : "Habis"}</span>
                  </div>
                </div>
                
                <div class="space-y-3">
                  <h4 class="font-bold text-slate-700">Sinopsis</h4>
                  <p class="text-slate-500 leading-relaxed text-justify h-[100px] overflow-hidden">
                    ${sinopsisTampil}
                  </p>
                  <div class="pt-2">
                    <button onclick='openCarouselModal(${book.id})' class="inline-flex items-center gap-2 bg-primary text-white px-6 py-2.5 rounded-xl font-semibold hover:bg-amber-700 transition-all active:scale-95 shadow-md text-sm">
                      Lihat Detail
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        `;
      }).join('');
      updateCarouselPosition();
    }
    renderCarousel();

    function openCarouselModal(id) {
      const book = carouselBooks.find(book => book.id === id);
      openModal(book);
    }

    function updateCarouselPosition() {
      const track = document.getElementById("carousel-track");
      track.style.transform = `translateX(-${currentSlide * 100}%)`;
    }

    function nextSlide() {
      currentSlide = (currentSlide + 1) % carouselBooks.length;
      updateCarouselPosition();
    }

    function prevSlide() {
      currentSlide = (currentSlide - 1 + carouselBooks.length) % carouselBooks.length;
      updateCarouselPosition();
    }

    function handleSubscribe() {
      const input = document.getElementById("subscribe-email");
      const msg = document.getElementById("subscribe-msg");

      if (input.value && input.value.includes("@")) {
        msg.classList.remove("hidden");
        input.value = "";
        setTimeout(() => {
          msg.classList.add("hidden");
        }, 3000);
      }
    }

    // Initial Render
    document.addEventListener("DOMContentLoaded", () => {
      renderCarousel();
    });

    window.addEventListener('resize', renderCarousel);
    window.addEventListener('load', () => {

      if (window.location.search) {

        window.location.href =
          window.location.pathname;

      }

    });
  </script>
</body>

</html>