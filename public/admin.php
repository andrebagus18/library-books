<?php
session_start();
require_once '../config/database.php';
require_once '../functions/helper.php';


// if (!isLogin()) {
//   redirect('login.php');
// }

$flash = getFlash();
$noBook = 1;
$noUser = 1;
$noMember = 1;

// Dashboard
$totalbooks = fetchOne("SELECT COUNT(*) as total FROM books")['total'];
$totalMembers = fetchOne("SELECT COUNT(*) as total FROM users WHERE role = 'user'")['total'];
// $booksBorrowed = fetchOne("SELECT COUNT(*) as total FROM bookd WHERE role = 'user'")['total'];


// Logic Buku
$books = fetchAll("SELECT * FROM books ORDER BY id DESC");
$editBook = isset($_GET['edit-buku']) ? fetchOne("SELECT * FROM books WHERE id = ?", [$_GET['edit-buku']]) : null;
// Hapus buku
if (isset($_GET['delete-buku'])) {
  query("DELETE FROM books WHERE id = ?", [$_GET['delete-buku']]);
  setFlash('success', 'Buku berhasil dihapus!');
  redirect('admin.php');
}
// Edit + Tambah buku
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if ($_POST['mode-editBook'] == 'edit') {
    query(
      "UPDATE books SET title=?, author=?, publisher=?, stock=?, location=?, year=?, description=? WHERE id=?",
      [
        $_POST['title'],
        $_POST['author'],
        $_POST['publisher'],
        $_POST['stock'],
        $_POST['location'],
        $_POST['year'],
        $_POST['description'],
        $_POST['id']
      ]
    );
    setFlash('success', 'Buku berhasil diupdate!');
  } else {
    query(
      "INSERT INTO books (title, author, publisher, stock, location, year, description) VALUES (?, ?, ?, ?, ?, ?, ?)",
      [
        $_POST['title'],
        $_POST['author'],
        $_POST['publisher'],
        $_POST['stock'] ?? 1,
        $_POST['location'],
        $_POST['year'] ?? null,
        $_POST['description']
      ]
    );
    setFlash('success', 'Buku berhasil ditambah!');
  }
  redirect('admin.php');
}


// Logic members
$members = fetchAll("SELECT members.id AS member_id, members.user_id, members.member_code,
        members.name,
        users.email,
        members.is_active FROM members JOIN users ON users.id = members.user_id WHERE users.role = 'user' ORDER BY members.id DESC");
//hapus member
if (isset($_GET['delete-member'])) {
  $member = fetchOne("SELECT user_id FROM members WHERE id = ?", [$_GET['delete-member']]);
  query("DELETE FROM members WHERE id = ?", [$_GET['delete-member']]);
  query("DELETE FROM users WHERE id = ?", [$member['user_id']]);
  setFlash('success', 'Member berhasil dihapus!');
  redirect('admin.php');
}
// Edit member
$editMember = isset($_GET['edit-member']) ? fetchOne("SELECT * FROM members WHERE id = ?", [$_GET['edit-member']]) : null;

// Logic User
$users = fetchAll("SELECT * FROM users ORDER BY id ASC");
$editUser = isset($_GET['edit-user']) ? fetchOne("SELECT * FROM users WHERE id = ?", [$_GET['edit-user']]) : null;
// Hapus user
if (isset($_GET['delete'])) {
  query("DELETE FROM users WHERE id = ?", [$_GET['delete']]);
  setFlash('success', 'User berhasil dihapus!');
  redirect('admin.php');
}
// edit user
// if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['simpan'])) {
//   query(
//     "UPDATE users SET username=?, email=?, full_name=?, role=? WHERE id=?",
//     [
//       $_POST['username'],
//       $_POST['email'],
//       $_POST['full_name'],
//       $_POST['role'],
//       $_POST['id']
//     ]
//   );
//   setFlash('success', 'User berhasil diupdate!');
//   redirect('admin.php');
// }
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (isset($_POST['simpan'])) {
    query(
      "UPDATE users SET username=?, email=?, full_name=?, role=? WHERE id=?",
      [
        $_POST['username'],
        $_POST['email'],
        $_POST['full_name'],
        $_POST['role'],
        $_POST['id']
      ]
    );
    setFlash('success', 'User berhasil diupdate!');
  } elseif (isset($_POST['simpan-member'])) {
    query(
      "UPDATE members SET member_code=?, name=?, is_active=? WHERE id=?",
      [
        $_POST['member_code'],
        $_POST['name'],
        $_POST['is_active'],
        $_POST['member_id']
      ]
    );
    query(
      "UPDATE users SET email=? WHERE id=?",
      [
        $_POST['email'],
        $_POST['user_id']
      ]
    );
    setFlash('success', 'Member berhasil diupdate!');
  }
  redirect('admin.php');
}


?>


<!doctype html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard - LiBooks</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap"
    rel="stylesheet" />
  <!-- Tailwind CSS v4 CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: "#A86E43",
            secondary: "#E0E0E0",
            accent: "#F5F5F5",
            danger: "#EF4444",
            success: "#10B981",
            info: "#3B82F6",
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
      visibility: hidden;
    }

    body.ready {
      visibility: visible;
    }

    .sidebar-transition {
      transition: transform 0.3s ease-in-out;
    }

    .content-fade {
      animation: fadeIn 0.4s ease-out;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(10px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .table-container::-webkit-scrollbar {
      height: 6px;
    }

    .table-container::-webkit-scrollbar-track {
      background: #f1f1f1;
    }

    .table-container::-webkit-scrollbar-thumb {
      background: #a86e43;
      border-radius: 10px;
    }

    .nav-link.active {
      background-color: #a86e43;
      color: white;
      box-shadow: 0 10px 15px -3px rgba(168, 110, 67, 0.2);
    }
  </style>
</head>

<body class="bg-gray-50 text-gray-800">
  <!-- Mobile Header -->
  <header
    class="lg:hidden bg-white border-b border-gray-200 p-4 flex items-center justify-between sticky top-0 z-50">
    <div class="flex items-center gap-2">
      <div class="bg-primary p-1.5 rounded-lg">
        <i data-lucide="book-open" class="text-white w-5 h-5"></i>
      </div>
      <span class="text-xl font-bold text-primary tracking-tight">LiBooks Admin</span>
    </div>
    <button
      id="mobile-menu-toggle"
      class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
      <i data-lucide="menu" class="text-gray-600"></i>
    </button>
  </header>

  <div class="flex min-h-screen relative overflow-hidden">
    <!-- Sidebar -->
    <aside
      id="sidebar"
      class="fixed inset-y-0 left-0 z-40 w-72 bg-white border-r border-gray-200 transform -translate-x-full lg:translate-x-0 lg:static sidebar-transition flex flex-col shadow-sm">
      <!-- Sidebar Header -->
      <div class="p-8 hidden lg:flex items-center gap-3">
        <div class="bg-primary p-2 rounded-xl shadow-lg shadow-primary/20">
          <i data-lucide="book-open" class="text-white w-6 h-6"></i>
        </div>
        <span class="text-2xl font-bold text-primary tracking-tight">LiBooks</span>
      </div>

      <!-- Admin Profile Section -->
      <div
        class="px-8 py-6 mb-4 flex flex-col items-center border-b border-gray-100 text-center">
        <div class="relative group">
          <div
            class="w-20 h-20 rounded-full bg-primary/10 flex items-center justify-center border-2 border-primary/20 p-1 group-hover:border-primary transition-all duration-300">
            <img
              src="https://api.dicebear.com/7.x/avataaars/svg?seed=Admin"
              alt="Admin Avatar"
              class="rounded-full w-full h-full object-cover" />
          </div>
          <div
            class="absolute bottom-0 right-0 w-5 h-5 bg-success border-2 border-white rounded-full"></div>
        </div>
        <h3 class="mt-4 font-bold text-gray-900 text-lg">Super Admin</h3>
        <p class="text-gray-500 text-sm font-medium">Administrator</p>
      </div>

      <!-- Navigation Links -->
      <nav class="flex-1 px-4 space-y-1 overflow-y-auto no-scrollbar">
        <button
          data-target="dashboard"
          class="nav-link w-full flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-primary/5 hover:text-primary transition-all duration-300 group">
          <i
            data-lucide="layout-dashboard"
            class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
          <span class="font-semibold">Dashboard</span>
        </button>
        <button
          data-target="buku"
          class="nav-link w-full flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-primary/5 hover:text-primary transition-all duration-300 group">
          <i
            data-lucide="book"
            class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
          <span class="font-semibold">Buku</span>
        </button>
        <button
          data-target="kategori"
          class="nav-link w-full flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-primary/5 hover:text-primary transition-all duration-300 group">
          <i
            data-lucide="tag"
            class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
          <span class="font-semibold">Kategori</span>
        </button>
        <button
          data-target="member"
          class="nav-link w-full flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-primary/5 hover:text-primary transition-all duration-300 group">
          <i
            data-lucide="users"
            class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
          <span class="font-semibold">Member</span>
        </button>
        <button
          data-target="user"
          class="nav-link w-full flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-primary/5 hover:text-primary transition-all duration-300 group">
          <i
            data-lucide="user-cog"
            class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
          <span class="font-semibold">User</span>
        </button>
        <button
          data-target="peminjaman"
          class="nav-link w-full flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-primary/5 hover:text-primary transition-all duration-300 group">
          <i
            data-lucide="arrow-left-right"
            class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
          <span class="font-semibold">Peminjaman</span>
        </button>
        <button
          data-target="denda"
          class="nav-link w-full flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-primary/5 hover:text-primary transition-all duration-300 group">
          <i
            data-lucide="banknote"
            class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
          <span class="font-semibold">Denda</span>
        </button>
        <button
          data-target="laporan"
          class="nav-link w-full flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-primary/5 hover:text-primary transition-all duration-300 group">
          <i
            data-lucide="file-text"
            class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
          <span class="font-semibold">Laporan</span>
        </button>
        <button
          data-target="pengaturan"
          class="nav-link w-full flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-primary/5 hover:text-primary transition-all duration-300 group">
          <i
            data-lucide="settings"
            class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
          <span class="font-semibold">Pengaturan</span>
        </button>
      </nav>

      <!-- Bottom Actions -->
      <div class="p-6 border-t border-gray-100 space-y-2">
        <button
          onclick="location.href = '../index.php'"
          class="w-full flex items-center gap-3 px-4 py-3.5 rounded-xl bg-success text-white hover:bg-emerald-600 transition-all duration-300 group shadow-md shadow-success/20">
          <i
            data-lucide="home"
            class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
          <span class="font-semibold text-sm"><- Back to Home</span>
        </button>
        <button
          onclick="location.href = 'logout.php'"
          class="w-full flex items-center gap-3 px-4 py-3.5 rounded-xl text-gray-500 hover:bg-red-50 hover:text-red-600 transition-all duration-300 group">
          <i
            data-lucide="log-out"
            class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
          <span class="font-semibold">Logout</span>
        </button>
      </div>
    </aside>

    <!-- Main Content Area -->
    <main
      class="flex-1 min-h-screen overflow-y-auto bg-[#FDFCFB] p-4 lg:p-10 no-scrollbar">
      <!-- Dashboard Section -->
      <section id="dashboard" class="content-section content-fade">
        <header
          class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
          <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">
              Dashboard Admin
            </h1>
            <p class="text-gray-500 mt-1">
              Pantau statistik perpustakaan hari ini.
            </p>
          </div>
        </header>

        <div
          class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
          <!-- Card 1 -->
          <div
            class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 hover:shadow-xl hover:shadow-primary/5 hover:border-gray-300 transition-all duration-500 group">
            <div
              class="bg-blue-50 text-blue-600 p-3 rounded-2xl w-fit mb-4 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
              <i data-lucide="book" class="w-6 h-6"></i>
            </div>
            <h2 class="text-3xl font-black text-gray-900 mb-1"><?= $totalbooks ?></h2>
            <p class="text-gray-500 text-sm font-semibold">
              Total Semua Buku
            </p>
          </div>
          <!-- Card 2 -->
          <div
            class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 hover:shadow-xl hover:shadow-primary/5 hover:border-gray-300 transition-all duration-500 group">
            <div
              class="bg-amber-50 text-amber-600 p-3 rounded-2xl w-fit mb-4 group-hover:bg-amber-600 group-hover:text-white transition-colors duration-300">
              <i data-lucide="book-open-check" class="w-6 h-6"></i>
            </div>
            <h2 class="text-3xl font-black text-gray-900 mb-1">42</h2>
            <p class="text-gray-500 text-sm font-semibold">
              Buku Sedang Dipinjam
            </p>
          </div>
          <!-- Card 3 -->
          <div
            class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 hover:shadow-xl hover:shadow-primary/5 hover:border-gray-300 transition-all duration-500 group">
            <div
              class="bg-success/10 text-success p-3 rounded-2xl w-fit mb-4 group-hover:bg-success group-hover:text-white transition-colors duration-300">
              <i data-lucide="banknote" class="w-6 h-6"></i>
            </div>
            <h2 class="text-3xl font-black text-gray-900 mb-1">Rp 450k</h2>
            <p class="text-gray-500 text-sm font-semibold">
              Total Pendapatan Denda
            </p>
          </div>
          <!-- Card 4 -->
          <div
            class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 hover:shadow-xl hover:shadow-primary/5 hover:border-gray-300 transition-all duration-500 group">
            <div
              class="bg-info/10 text-info p-3 rounded-2xl w-fit mb-4 group-hover:bg-info group-hover:text-white transition-colors duration-300">
              <i data-lucide="users" class="w-6 h-6"></i>
            </div>
            <h2 class="text-3xl font-black text-gray-900 mb-1"><?= $totalMembers ?></h2>
            <p class="text-gray-500 text-sm font-semibold">Total Member</p>
          </div>
        </div>
      </section>

      <!-- Buku Section -->
      <section id="buku" class="content-section content-fade hidden">
        <header class="mb-8">
          <h2 class="text-2xl font-bold text-gray-900">Manajemen Buku</h2>
          <p class="text-gray-500">
            Tambah, edit, dan kelola stok buku perpustakaan.
          </p>
        </header>

        <!-- Form Tambah Buku -->
        <div
          class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 mb-8">
          <h3 class="text-lg font-bold mb-6 flex items-center gap-2">
            <i data-lucide="plus-circle" class="w-5 h-5 text-primary"></i>
            <?= $editBook ? 'Edit Buku' : 'Tambah Buku Baru' ?>
          </h3>
          <form method="POST" id="tambah-buku"
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
            <input type="hidden" name="mode-editBook" id="mode-editBook" value="create">
            <input type="hidden" name="id" id="id">
            <div class="space-y-1">
              <label class="text-xs font-bold text-gray-500 uppercase ml-1">Judul Buku</label>
              <input
                type="text"
                placeholder="Masukkan judul..."
                name="title"
                id="title"
                value="<?= $editBook['title'] ?? '' ?>"
                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary transition-all text-sm" />
            </div>
            <div class="space-y-1">
              <label class="text-xs font-bold text-gray-500 uppercase ml-1">Penulis</label>
              <input
                type="text"
                placeholder="Masukkan nama penulis..."
                name="author"
                id="author"
                value="<?= $editBook['author'] ?? '' ?>"
                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary transition-all text-sm" />
            </div>
            <div class="space-y-1">
              <label class="text-xs font-bold text-gray-500 uppercase ml-1">Penerbit</label>
              <input
                type="text"
                placeholder="Nama penerbit..."
                name="publisher"
                id="publisher"
                value="<?= $editBook['publisher'] ?? '' ?>"
                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary transition-all text-sm" />
            </div>
            <div class="space-y-1">
              <label class="text-xs font-bold text-gray-500 uppercase ml-1">Stok</label>
              <input
                type="number"
                placeholder="1"
                name="stock"
                id="stock"
                value="<?= $editBook['stock'] ?? '' ?>"
                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary transition-all text-sm" />
            </div>
            <div class="space-y-1">
              <label class="text-xs font-bold text-gray-500 uppercase ml-1">Deskripsi</label>
              <textarea
                placeholder="Deskripsi singkat tentang buku..."
                name="description"
                id="description"

                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary transition-all text-sm overflow-y-auto no-scrollbar"
                rows="3"><?= $editBook['description'] ?? '' ?></textarea>
            </div>
            <div class="space-y-1">
              <label class="text-xs font-bold text-gray-500 uppercase ml-1">Lokasi (Rak)</label>
              <input
                type="text"
                placeholder="rak A, B2, C1, ..."
                name="location"
                id="location"
                value="<?= $editBook['location'] ?? '' ?>"
                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary transition-all text-sm" />
            </div>
            <div class="space-y-1">
              <label class="text-xs font-bold text-gray-500 uppercase ml-1">Tahun Terbit</label>
              <input
                type="number"
                placeholder="Contoh: 2024"
                name="year"
                id="year"
                value="<?= $editBook['year'] ?? '' ?>"
                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary transition-all text-sm" />
            </div>
            <button
              type="submit"
              id="btn-submit"
              class="bg-info text-white px-6 py-2.5 rounded-xl font-bold shadow-lg shadow-info/20 hover:scale-105 active:scale-95 transition-all text-sm">
              Simpan
            </button>
          </form>
        </div>

        <!-- Tabel Buku -->
        <div
          class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
          <div class="table-container overflow-x-auto no-scrollbar">
            <table class="w-full text-left border-collapse overflow-scroll">
              <thead>
                <tr class="bg-gray-50/50">
                  <th
                    class="px-8 py-5 text-xs font-bold text-gray-500 uppercase tracking-wider">
                    No
                  </th>
                  <th
                    class="px-8 py-5 text-xs font-bold text-gray-500 uppercase tracking-wider">
                    Judul Buku
                  </th>
                  <th
                    class="px-8 py-5 text-xs font-bold text-gray-500 uppercase tracking-wider">
                    Penulis
                  </th>
                  <th
                    class="px-8 py-5 text-xs font-bold text-gray-500 uppercase tracking-wider">
                    Penerbit
                  </th>
                  <th
                    class="px-8 py-5 text-xs font-bold text-gray-500 uppercase tracking-wider">
                    Stok
                  </th>
                  <th
                    class="px-8 py-5 text-xs font-bold text-gray-500 uppercase tracking-wider overflow-hidden">
                    Deskripsi
                  </th>
                  <th
                    class="px-8 py-5 text-xs font-bold text-gray-500 uppercase tracking-wider">
                    Lokasi (Rak)
                  </th>
                  <th
                    class="px-8 py-5 text-xs font-bold text-gray-500 uppercase tracking-wider">
                    Tahun
                  </th>
                  <th
                    class="px-8 py-5 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">
                    Aksi
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <?php foreach ($books as $book): ?>
                  <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-8 py-5 font-medium text-gray-600"><?= $noBook++ ?></td>
                    <td class="px-8 py-5 font-bold text-gray-900">
                      <?= $book['title'] ?>
                    </td>
                    <td class="px-8 py-5 text-gray-600"><?= $book['author'] ?></td>
                    <td class="px-8 py-5 text-gray-600"><?= $book['publisher'] ?></td>
                    <td class="px-8 py-5 text-gray-600 font-bold"><?= $book['stock'] ?></td>
                    <td class="px-8 py-5 text-gray-600">
                      <?= $book['description'] ?>
                    </td>
                    <td class="px-8 py-5 text-gray-600"><?= $book['location'] ?></td>
                    <td class="px-8 py-5 text-gray-600"><?= $book['year'] ?></td>
                    <td class="px-8 py-5">
                      <div class="flex items-center justify-center gap-3">
                        <a href="#"
                          class="edit-btn p-2 bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-600 hover:text-white transition-all" data-action="edit-buku" data-id='<?= $book['id'] ?>' data-title='<?= $book['title'] ?>' data-author='<?= $book['author'] ?>' data-publisher='<?= $book['publisher'] ?>' data-stock="<?= ($book['stock']) ?>" data-description="<?= ($book['description']) ?>" data-location="<?= ($book['location']) ?>" data-year="<?= $book['year'] ?>">
                          <i data-lucide="edit-3" class="w-4 h-4"></i>
                        </a>
                        <a href="?delete-buku=<?= $book['id'] ?>"
                          class=" delete-btn p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-all" onclick="return confirm('Apakah anda Yakin?')">
                          <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </a>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- Kategori Section -->
      <section id="kategori" class="content-section content-fade hidden">
        <header class="mb-8">
          <h2 class="text-2xl font-bold text-gray-900">Manajemen Kategori</h2>
          <p class="text-gray-500">
            Kelompokkan koleksi buku Anda berdasarkan kategori.
          </p>
        </header>

        <div
          class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 mb-8">
          <h3 class="text-lg font-bold mb-6 flex items-center gap-2">
            <i data-lucide="plus-circle" class="w-5 h-5 text-primary"></i>
            Tambah Kategori Baru
          </h3>
          <form method="POST" id="tambah-kategori" class="flex gap-4 items-end max-w-2xl">
            <input type="hidden" name="action" value="tambah-kategori">

            <div class="flex-1 space-y-1">
              <label class="text-xs font-bold text-gray-500 uppercase ml-1">Nama Kategori</label>
              <input
                type="text"
                placeholder="Masukkan nama kategori..."
                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary transition-all text-sm" />
            </div>
            <button
              type="button"
              class="bg-info text-white px-8 py-2.5 rounded-xl font-bold shadow-lg shadow-info/20 hover:scale-105 active:scale-95 transition-all text-sm">
              Simpan
            </button>
          </form>
        </div>

        <div
          class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
          <div class="table-container overflow-x-auto no-scrollbar">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="bg-gray-50/50">
                  <th
                    class="px-8 py-5 text-xs font-bold text-gray-500 uppercase tracking-wider">
                    No
                  </th>
                  <th
                    class="px-8 py-5 text-xs font-bold text-gray-500 uppercase tracking-wider">
                    Nama Kategori
                  </th>
                  <th
                    class="px-8 py-5 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">
                    Aksi
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr class="hover:bg-gray-50 transition-colors">
                  <td class="px-8 py-5 font-medium text-gray-600">01</td>
                  <td class="px-8 py-5 font-bold text-gray-900">
                    Pengembangan Diri
                  </td>
                  <td class="px-8 py-5">
                    <div class="flex items-center justify-center gap-3">
                      <button
                        class="p-2 bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-600 hover:text-white transition-all">
                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                      </button>
                      <button
                        class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-all">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- Member Section -->
      <section id="member" class="content-section content-fade hidden">
        <header class="mb-8">
          <h2 class="text-2xl font-bold text-gray-900">Data Member</h2>
          <p class="text-gray-500">
            Kelola status keaktifan dan informasi member.
          </p>
        </header>
        <div
          class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 mb-8">
          <h3 class="text-lg font-bold mb-6 flex items-center gap-2">
            <i data-lucide="user-plus" class="w-5 h-5 text-primary"></i>
            Data Member
          </h3>
          <form method="POST" id="edit-member" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <input type="hidden" id="id" name="id" value="<?= $editMember['member_id'] ?? '' ?>">
            <div class="space-y-1">
              <label class="text-xs font-bold text-gray-500 uppercase ml-1">Kode Member</label>
              <input
                type="text"
                placeholder="MBR-001"
                name="member_code"
                id="member_code"
                value="<?= $editMember['member_code'] ?? '' ?>"
                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary transition-all text-sm" />
            </div>
            <div class="space-y-1">
              <label class="text-xs font-bold text-gray-500 uppercase ml-1">Nama Lengkap</label>
              <input
                type="text"
                id="name"
                name="name"
                placeholder="Nama lengkap..."
                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary transition-all text-sm" value="<?= $editMember['name'] ?? '' ?>" />
            </div>
            <div class="space-y-1">
              <label class="text-xs font-bold text-gray-500 uppercase ml-1">Email</label>
              <input
                type="email"
                id="email"
                name="email"
                placeholder="contoh@email.com"
                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary transition-all text-sm" value="<?= $editMember['email'] ?? '' ?>" />
            </div>
            <div class="space-y-1">
              <label class="text-xs font-bold text-gray-500 uppercase ml-1">Status</label>
              <select
                id="is_active"
                name="is_active"
                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary transition-all text-sm" value="<?= $editMember['is_active'] ?? '' ?>">
                <option value="1">Aktif</option>
                <option value="0">Tidak Aktif</option>
              </select>
            </div>
            <div class="flex gap-2 items-end">
              <button
                type="submit"
                name="simpan-member"
                class="bg-info text-white px-8 py-2.5 rounded-xl font-bold shadow-lg shadow-info/20 hover:scale-105 active:scale-95 transition-all text-sm h-fit mb-0.5">
                Update Member
              </button>
            </div>
          </form>
        </div>

        <div
          class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
          <div class="table-container overflow-x-auto no-scrollbar">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="bg-gray-50/50">
                  <th
                    class="px-8 py-5 text-xs font-bold text-gray-500 uppercase tracking-wider">
                    No
                  </th>
                  <th
                    class="px-8 py-5 text-xs font-bold text-gray-500 uppercase tracking-wider">
                    Kode Member
                  </th>
                  <th
                    class="px-8 py-5 text-xs font-bold text-gray-500 uppercase tracking-wider">
                    Nama
                  </th>
                  <th
                    class="px-8 py-5 text-xs font-bold text-gray-500 uppercase tracking-wider">
                    Email
                  </th>
                  <th
                    class="px-8 py-5 text-xs font-bold text-gray-500 uppercase tracking-wider">
                    Status
                  </th>
                  <th
                    class="px-8 py-5 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">
                    Aksi
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <?php foreach ($members as $member): ?>
                  <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-8 py-5 font-medium text-gray-600"><?= $noMember++ ?></td>
                    <td class="px-8 py-5 font-bold text-gray-900">
                      <?= $member['member_code'] ?>
                    </td>
                    <td class="px-8 py-5 text-gray-600"><?= $member['name'] ?></td>
                    <td class="px-8 py-5 text-gray-600"><?= $member['email'] ?></td>
                    <td class="px-8 py-5">
                      <span
                        class="px-3 py-1 bg-success/10 text-success rounded-full text-xs font-bold border border-success/20"><?= $member['is_active'] ? 'Aktif' : 'Tidak Aktif' ?></span>
                    </td>
                    <td class="px-8 py-5">
                      <div class="flex items-center justify-center gap-3">
                        <a href="#"
                          class="edit-btn p-2 bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-600 hover:text-white transition-all" data-action="edit-member" data-id='<?= $member['member_id'] ?>' data-member_code='<?= $member['member_code'] ?>' data-name='<?= $member['name'] ?>' data-email='<?= $member['email'] ?>' data-isactive="<?= htmlspecialchars($member['is_active']) ?>">
                          <i data-lucide="edit-3" class="w-4 h-4"></i>
                        </a>
                        <a href="?delete-member=<?= $member['member_id'] ?>"
                          class="delete-btn p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-all" data-id="<?= $member['member_id'] ?>" onclick="return confirm('Apakah anda Yakin?')">
                          <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </a>
                        <button
                          class="p-2 bg-gray-50 text-gray-600 rounded-lg hover:bg-gray-600 hover:text-white transition-all"
                          title="Nonaktifkan">
                          <i data-lucide="user-minus" class="w-4 h-4"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- User Section -->
      <section id="user" class="content-section content-fade hidden">
        <header class="mb-8">
          <h2 class="text-2xl font-bold text-gray-900">Manajemen Role Akun</h2>
          <p class="text-gray-500">
            Kelola akun administrator dan member di sistem.
          </p>
        </header>

        <div
          class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 mb-8">
          <h3 class="text-lg font-bold mb-6 flex items-center gap-2">
            <i data-lucide="user-plus" class="w-5 h-5 text-primary"></i>
            Data Akun
          </h3>
          <form method="POST" id="edit-user" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <input type="hidden" id="id" name="id" value="<?= $editUser['id'] ?? '' ?>">
            <div class="space-y-1">
              <label class="text-xs font-bold text-gray-500 uppercase ml-1">Nama</label>
              <input
                type="text"
                id="username"
                name="username"
                placeholder="Nama lengkap..."
                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary transition-all text-sm" value="<?= $editUser['username'] ?? '' ?>" />
            </div>
            <div class="space-y-1">
              <label class="text-xs font-bold text-gray-500 uppercase ml-1">Email</label>
              <input
                type="email"
                id="email"
                name="email"
                placeholder="Email..."
                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary transition-all text-sm" value="<?= $editUser['email'] ?? '' ?>" />
            </div>
            <div class="space-y-1">
              <label class="text-xs font-bold text-gray-500 uppercase ml-1">Nama Lengkap</label>
              <input
                type="text"
                id="full_name"
                name="full_name"
                placeholder="Nama lengkap..."
                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary transition-all text-sm" value="<?= $editUser['full_name'] ?? '' ?>" />
            </div>
            <div class="space-y-1">
              <label class="text-xs font-bold text-gray-500 uppercase ml-1">Role</label>
              <select
                id="role"
                name="role"
                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary transition-all text-sm" value="<?= $editUser['role'] ?? '' ?>">
                <option value="user">Member</option>
                <option value="admin">Admin</option>
              </select>
            </div>
            <div class="flex gap-2 items-end">
              <button
                type="submit"
                name="simpan"
                class="bg-info text-white px-8 py-2.5 rounded-xl font-bold shadow-lg shadow-info/20 hover:scale-105 active:scale-95 transition-all text-sm h-fit mb-0.5">
                Update Akun
              </button>
            </div>
          </form>
        </div>

        <div
          class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
          <div class="table-container overflow-x-auto no-scrollbar">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="bg-gray-50/50">
                  <th
                    class="px-8 py-5 text-xs font-bold text-gray-500 uppercase tracking-wider">
                    No.
                  </th>
                  <th
                    class="px-8 py-5 text-xs font-bold text-gray-500 uppercase tracking-wider">
                    Nama
                  </th>
                  <th
                    class="px-8 py-5 text-xs font-bold text-gray-500 uppercase tracking-wider">
                    Email
                  </th>
                  <th
                    class="px-8 py-5 text-xs font-bold text-gray-500 uppercase tracking-wider">
                    Nama Lengkap
                  </th>
                  <th
                    class="px-8 py-5 text-xs font-bold text-gray-500 uppercase tracking-wider">
                    Role
                  </th>
                  <th
                    class="px-8 py-5 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">
                    Aksi
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <?php foreach ($users as $user): ?>
                  <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-8 py-5 font-medium text-gray-600"><?= $noUser++ ?></td>
                    <td class="px-8 py-5 font-bold text-gray-900">
                      <?= $user['username'] ?>
                    </td>
                    <td class="px-8 py-5 text-gray-600"><?= $user['email'] ?></td>
                    <td
                      class="px-8 py-5 font-bold text-primary text-xs uppercase tracking-widest">
                      <?= $user['full_name'] ?>
                    </td>
                    <td class="px-8 py-5">
                      <span
                        class="px-3 py-1 bg-success/10 text-success rounded-full text-xs font-bold border border-success/20"><?= $user['role'] ?></span>
                    </td>
                    <td class="px-8 py-5">
                      <div class="flex items-center justify-center gap-3">
                        <a href="#"
                          class="edit-btn p-2 bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-600 hover:text-white transition-all" data-action="edit-user" data-id='<?= $user['id'] ?>' data-name='<?= $user['username'] ?>' data-email='<?= $user['email'] ?>' data-fullname='<?= $user['full_name'] ?>' data-role="<?= htmlspecialchars($user['role']) ?>">
                          <i data-lucide="edit-3" class="w-4 h-4"></i>
                        </a>
                        <a href="?delete=<?= $user['id'] ?>"
                          class=" delete-btn p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-all" data-id="<?= $user['id'] ?>" onclick="return confirm('Apakah anda Yakin?')">
                          <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </a>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- Peminjaman Section -->
      <section id="peminjaman" class="content-section content-fade hidden">
        <header class="mb-8">
          <h2 class="text-2xl font-bold text-gray-900">Data Peminjaman</h2>
          <p class="text-gray-500">
            Lihat semua transaksi peminjaman buku yang sedang berlangsung.
          </p>
        </header>

        <div
          class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
          <div class="table-container overflow-x-auto no-scrollbar">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="bg-gray-50/50">
                  <th
                    class="px-8 py-5 text-xs font-bold text-gray-500 uppercase tracking-wider">
                    No
                  </th>
                  <th
                    class="px-8 py-5 text-xs font-bold text-gray-500 uppercase tracking-wider">
                    Nama Peminjam
                  </th>
                  <th
                    class="px-8 py-5 text-xs font-bold text-gray-500 uppercase tracking-wider">
                    Judul Buku
                  </th>
                  <th
                    class="px-8 py-5 text-xs font-bold text-gray-500 uppercase tracking-wider">
                    Tanggal Kembali
                  </th>
                  <th
                    class="px-8 py-5 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">
                    Aksi
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr class="hover:bg-gray-50 transition-colors">
                  <td class="px-8 py-5 font-medium text-gray-600">01</td>
                  <td class="px-8 py-5 font-bold text-gray-900">
                    Akhmad Fauzi
                  </td>
                  <td class="px-8 py-5 text-gray-600 font-semibold">
                    The Midnight Library
                  </td>
                  <td class="px-8 py-5 text-gray-600 font-bold">
                    12 Mei 2026
                  </td>
                  <td class="px-8 py-5">
                    <div class="flex items-center justify-center">
                      <button
                        class="flex items-center gap-2 bg-success text-white px-4 py-2 rounded-xl text-xs font-bold shadow-lg shadow-success/20 hover:scale-105 active:scale-95 transition-all">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        Konfirmasi Kembali
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- Denda Section -->
      <section id="denda" class="content-section content-fade hidden">
        <header class="mb-8">
          <h2 class="text-2xl font-bold text-gray-900">Data Denda</h2>
          <p class="text-gray-500">
            Pantau dan konfirmasi pembayaran denda keterlambatan.
          </p>
        </header>

        <div
          class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
          <div class="table-container overflow-x-auto no-scrollbar">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="bg-gray-50/50">
                  <th
                    class="px-8 py-5 text-xs font-bold text-gray-500 uppercase tracking-wider">
                    No
                  </th>
                  <th
                    class="px-8 py-5 text-xs font-bold text-gray-500 uppercase tracking-wider">
                    Nama Peminjam
                  </th>
                  <th
                    class="px-8 py-5 text-xs font-bold text-gray-500 uppercase tracking-wider">
                    Judul Buku
                  </th>
                  <th
                    class="px-8 py-5 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">
                    Denda
                  </th>
                  <th
                    class="px-8 py-5 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">
                    Aksi
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr class="hover:bg-gray-50 transition-colors">
                  <td class="px-8 py-5 font-medium text-gray-600">01</td>
                  <td class="px-8 py-5 font-bold text-gray-900">
                    Akhmad Fauzi
                  </td>
                  <td class="px-8 py-5 text-gray-600">Sapiens</td>
                  <td class="px-8 py-5 text-right font-black text-danger">
                    Rp 15.000
                  </td>
                  <td class="px-8 py-5">
                    <div class="flex items-center justify-center">
                      <button
                        class="flex items-center gap-2 bg-success text-white px-4 py-2 rounded-xl text-xs font-bold shadow-lg shadow-success/20 hover:scale-105 active:scale-95 transition-all">
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                        Lunas
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- Laporan Section -->
      <section id="laporan" class="content-section content-fade hidden">
        <header
          class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
          <div>
            <h2 class="text-2xl font-bold text-gray-900">
              Laporan Perpustakaan
            </h2>
            <p class="text-gray-500 mt-1">
              Cetak dan tinjau riwayat peminjaman serta denda.
            </p>
          </div>
          <div class="flex gap-2">
            <button
              class="flex items-center gap-2 bg-white border border-gray-200 px-6 py-3 rounded-2xl font-bold hover:bg-gray-50 transition-all text-sm shadow-sm">
              <i data-lucide="printer" class="w-4 h-4 text-gray-600"></i>
              Cetak PDF
            </button>
            <button
              class="flex items-center gap-2 bg-primary text-white px-6 py-3 rounded-2xl font-bold shadow-lg shadow-primary/20 hover:scale-105 active:scale-95 transition-all text-sm">
              <i data-lucide="download" class="w-4 h-4"></i>
              Export Excel
            </button>
          </div>
        </header>

        <div
          class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
          <div class="table-container overflow-x-auto no-scrollbar">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="bg-gray-50/50">
                  <th
                    class="px-8 py-5 text-xs font-bold text-gray-500 uppercase tracking-wider">
                    No
                  </th>
                  <th
                    class="px-8 py-5 text-xs font-bold text-gray-500 uppercase tracking-wider">
                    Peminjam
                  </th>
                  <th
                    class="px-8 py-5 text-xs font-bold text-gray-500 uppercase tracking-wider">
                    Judul Buku
                  </th>
                  <th
                    class="px-8 py-5 text-xs font-bold text-gray-500 uppercase tracking-wider">
                    Tanggal
                  </th>
                  <th
                    class="px-8 py-5 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">
                    Denda
                  </th>
                  <th
                    class="px-8 py-5 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">
                    Aksi
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr class="hover:bg-gray-50 transition-colors">
                  <td class="px-8 py-5 font-medium text-gray-600">01</td>
                  <td class="px-8 py-5 font-bold text-gray-900">
                    Akhmad Fauzi
                  </td>
                  <td class="px-8 py-5 text-gray-600">
                    The Psychology of Money
                  </td>
                  <td class="px-8 py-5 text-gray-600">01/05/2026</td>
                  <td class="px-8 py-5 text-right font-bold text-gray-600">
                    Rp 0
                  </td>
                  <td class="px-8 py-5">
                    <div class="flex items-center justify-center">
                      <button
                        class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-all"
                        title="Hapus">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- Pengaturan Section -->
      <section id="pengaturan" class="content-section content-fade hidden">
        <header class="mb-10">
          <h2 class="text-2xl font-bold text-gray-900">Pengaturan Sistem</h2>
          <p class="text-gray-500">
            Konfigurasi parameter operasional perpustakaan.
          </p>
        </header>

        <div
          class="bg-white p-10 rounded-[2.5rem] shadow-sm border border-gray-100 max-w-2xl">
          <h3 class="text-xl font-bold mb-8 flex items-center gap-3">
            <i data-lucide="wallet" class="w-6 h-6 text-primary"></i>
            Konfigurasi Denda
          </h3>
          <div class="space-y-6">
            <div class="space-y-2">
              <label
                class="text-sm font-bold text-gray-700 uppercase tracking-wider ml-1">Besaran Denda Per Hari (Rp)</label>
              <div class="relative">
                <span
                  class="absolute left-4 top-1/2 -translate-y-1/2 font-bold text-gray-400">Rp</span>
                <input
                  type="number"
                  value="5000"
                  class="w-full pl-12 pr-4 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all text-lg font-bold" />
              </div>
              <p class="text-xs text-gray-500 ml-1 italic">
                *Denda akan otomatis dihitung saat buku melewati batas tanggal
                kembali.
              </p>
            </div>

            <div class="pt-4">
              <button
                type="button"
                class="w-full bg-primary text-white py-4 rounded-2xl font-black shadow-xl shadow-primary/20 hover:scale-[1.02] active:scale-95 transition-all tracking-widest">
                SIMPAN PERUBAHAN
              </button>
            </div>
          </div>
        </div>
      </section>
    </main>
  </div>

  <!-- Sidebar Overlay for Mobile -->
  <div
    id="sidebar-overlay"
    class="fixed inset-0 bg-black/50 z-30 hidden transition-opacity duration-300 opacity-0"></div>

  <script>
    // Initialize Lucide Icons
    lucide.createIcons();

    // Logic Edit ke Form tanpa reload halaman
    document.querySelectorAll('.edit-btn').forEach(button => {
      button.addEventListener('click', function(event) {
        event.preventDefault();
        const action = this.dataset.action;
        if (action === 'edit-user') {
          document.getElementById('id').value = this.dataset.id;
          document.getElementById('username').value = this.dataset.name;
          document.getElementById('email').value = this.dataset.email;
          document.getElementById('full_name').value = this.dataset.fullname;
          document.getElementById('role').value = this.dataset.role;
        }
        if (action === 'edit-buku') {
          document.getElementById('id').value = this.dataset.id;
          document.getElementById('title').value = this.dataset.title;
          document.getElementById('author').value = this.dataset.author;
          document.getElementById('publisher').value = this.dataset.publisher;
          document.getElementById('stock').value = this.dataset.stock;
          document.getElementById('description').value = this.dataset.description;
          document.getElementById('location').value = this.dataset.location;
          document.getElementById('year').value = this.dataset.year;
          document.getElementById('mode-editBook').value = 'edit';
          document.getElementById('btn-submit').textContent = 'Update';
        }
        if (action === 'edit-member') {
          document.getElementById('id').value = this.dataset.id;
          document.getElementById('member_code').value = this.dataset.member_code;
          document.getElementById('name').value = this.dataset.name;
          document.getElementById('email').value = this.dataset.email;
          document.getElementById('is_active').value = this.dataset.isactive;
        }
      });
    });

    // Sidebar logic
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("sidebar-overlay");
    const mobileToggle = document.getElementById("mobile-menu-toggle");

    function toggleSidebar() {
      sidebar.classList.toggle("-translate-x-full");
      if (overlay.classList.contains("hidden")) {
        overlay.classList.remove("hidden");
        setTimeout(() => overlay.classList.add("opacity-100"), 10);
      } else {
        overlay.classList.remove("opacity-100");
        setTimeout(() => overlay.classList.add("hidden"), 300);
      }
    }

    mobileToggle.addEventListener("click", toggleSidebar);
    overlay.addEventListener("click", toggleSidebar);

    const navLinks = document.querySelectorAll(".nav-link");
    const sections = document.querySelectorAll(".content-section");

    function showTab(target) {
      // save state
      localStorage.setItem("activeTab", target);
      // update active link
      navLinks.forEach((l) => {
        l.classList.toggle("active", l.dataset.target === target);
      });
      // switch section
      sections.forEach((s) => {
        s.classList.add("hidden");
        if (s.id === target) {
          s.classList.remove("hidden");
        }
      });
      // close sidebar mobile
      if (window.innerWidth < 1024) {
        toggleSidebar();
      }
      // scroll top
      document.querySelector("main").scrollTo({
        top: 0,
        behavior: "smooth"
      });
    }
    navLinks.forEach((link) => {
      link.addEventListener("click", () => {
        const target = link.dataset.target;
        showTab(target);
      });
    });
    window.addEventListener("DOMContentLoaded", () => {
      const savedTab = localStorage.getItem("activeTab") || "dashboard";
      showTab(savedTab);
      document.body.classList.add("ready");
    });
  </script>
</body>

</html>