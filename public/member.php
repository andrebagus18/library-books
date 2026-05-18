<?php
session_start();
require_once  '../config/database.php';
require_once '../functions/helper.php';


$flash = getFlash();
$isLogin = isLogin();
$totalFine = 0;
$totalLateDays = 0;
$noBuku = 1;
$noDenda = 1;
$noPinjam = 1;
$noRiwayat = 1;
$settingFine = getFinePerDay();
$totalFine = updateAllFines();

//ambil session user login
if ($isLogin) {
    $member = fetchOne(
        "SELECT * FROM members WHERE user_id = ?",
        [$_SESSION['user_id']]
    );
}
$member_id = $member['id'];


$totalLoans = fetchOne("SELECT COUNT(*) as total FROM loans WHERE member_id = ?", [$member['id']])['total'];
$totalDipinjam = fetchOne(
    "SELECT COUNT(*) as total
     FROM loans
     WHERE member_id = ?
     AND status = 'dipinjam'",
    [$member['id']]
)['total'];
$totalKembali = fetchOne(
    "SELECT COUNT(*) as total
     FROM loans
     WHERE member_id = ?
     AND status = 'dikembalikan'",
    [$member['id']]
)['total'];
$totalFine = fetchOne("
    SELECT COALESCE(SUM(fine), 0) as total
    FROM loans
    WHERE fine_paid = false
")['total'];

// select all loans id member
$loans = fetchAll(
    "SELECT
        loans.*,
        books.title,
        books.author,
        books.publisher,
        books.year
     FROM loans
     JOIN books ON books.id = loans.book_id
     WHERE loans.member_id = ?
     ORDER BY loans.id DESC",
    [$member_id]
);

// khusus logic denda
$loansDenda = fetchAll("
    SELECT loans.*, books.title, members.name
    FROM loans
    JOIN books ON books.id = loans.book_id
    JOIN members ON members.id = loans.member_id
    WHERE loans.fine_paid = false
    AND loans.due_date < CURRENT_DATE
");
$lateTotal = count($loansDenda);

// recent activities
$activities = fetchAll(
    "SELECT
        loans.*,
        books.image_url,
        books.title
     FROM loans
     JOIN books ON books.id = loans.book_id
     WHERE loans.member_id = ?
     ORDER BY loans.id DESC
     LIMIT 5",
    [$member_id]
);

/// logic kembalikan, update fine, update stock buku
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ((isset($_POST['kembalikan']))) {
        $id = $_POST['id'];
        $loan_id = $_POST['loan_id'];
        $book_id = $_POST['book_id'];
        query("UPDATE loans SET status= 'dikembalikan', return_date= CURRENT_DATE WHERE id=?", [$loan_id]);
        query("UPDATE books SET stock = stock + 1 WHERE id=?", [$book_id]);
        setFlash('success', 'Update buku berhasil!');
    } elseif (isset($_POST['pay_fine'])) {
        $member_id = $_POST['member_id'];
        // ambil data loan unutk 1 member
        query("UPDATE loans SET fine = 0, fine_paid = true WHERE member_id = ?
        AND fine > 0
    ", [$member_id]);
        setFlash('success', 'Denda berhasil dibayar');
    }
    redirect('member.php');
}



?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Area - LiBooks</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Tailwind CSS v4 CDN  -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#A86E43',
                        secondary: '#E0E0E0',
                        accent: '#F5F5F5',
                        danger: '#EF4444',
                        success: '#10B981',
                    },
                    fontFamily: {
                        outfit: ['Outfit', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #F8F9FA;
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

        /* Custom scrollbar  */
        .table-container::-webkit-scrollbar {
            height: 6px;
        }

        .table-container::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .table-container::-webkit-scrollbar-thumb {
            background: #A86E43;
            border-radius: 10px;
        }

        .selesai {
            text-decoration: line-through;
            opacity: .5;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800">

    <!-- Mobile Header -->
    <header class="lg:hidden bg-white border-b border-gray-200 p-4 flex items-center justify-between sticky top-0 z-50">
        <div class="flex items-center gap-2">
            <div class="bg-primary p-1.5 rounded-lg">
                <i data-lucide="book-open" class="text-white w-5 h-5"></i>
            </div>
            <span class="text-xl font-bold text-primary tracking-tight">LiBooks</span>
        </div>
        <button id="mobile-menu-toggle" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
            <i data-lucide="menu" class="text-gray-600"></i>
        </button>
    </header>

    <div class="flex min-h-screen relative overflow-hidden">

        <!-- Sidebar -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-72 bg-white border-r border-gray-200 transform -translate-x-full lg:translate-x-0 lg:static sidebar-transition flex flex-col shadow-sm">
            <!-- Sidebar Header -->
            <div class="p-8 hidden lg:flex items-center gap-3">
                <div class="bg-primary p-2 rounded-xl shadow-lg shadow-primary/20">
                    <i data-lucide="book-open" class="text-white w-6 h-6"></i>
                </div>
                <span class="text-2xl font-bold text-primary tracking-tight">LiBooks</span>
            </div>

            <!-- User Profile Section -->
            <div class="px-8 py-6 mb-4 flex flex-col items-center border-b border-gray-100">
                <div class="relative group">
                    <div class="w-20 h-20 rounded-full bg-primary/10 flex items-center justify-center border-2 border-primary/20 p-1 group-hover:border-primary transition-all duration-300">
                        <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Felix" alt="User Avatar" class="rounded-full w-full h-full object-cover">
                    </div>
                    <div class="absolute bottom-0 right-0 w-5 h-5 bg-success border-2 border-white rounded-full"></div>
                </div>
                <h3 class="mt-4 font-bold text-gray-900 text-lg"><?= $member['name'] ?></h3>
                <p class="text-gray-500 text-sm font-medium">Member <?= $member['member_code'] ?></p>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-4 space-y-1 overflow-y-auto">
                <button data-target="dashboard" class="nav-link w-full flex items-center gap-3 px-4 py-3.5 rounded-xl text-gray-600 hover:bg-primary/5 hover:text-primary transition-all duration-300 group">
                    <i data-lucide="layout-dashboard" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                    <span class="font-semibold">Dashboard</span>
                </button>
                <button data-target="daftar-buku" class="nav-link w-full flex items-center gap-3 px-4 py-3.5 rounded-xl text-gray-600 hover:bg-primary/5 hover:text-primary transition-all duration-300 group">
                    <i data-lucide="book-copy" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                    <span class="font-semibold">Daftar Buku</span>
                </button>
                <button data-target="pinjam-buku" class="nav-link w-full flex items-center gap-3 px-4 py-3.5 rounded-xl text-gray-600 hover:bg-primary/5 hover:text-primary transition-all duration-300 group">
                    <i data-lucide="shopping-bag" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                    <span class="font-semibold">Pinjam Buku</span>
                </button>
                <button data-target="riwayat" class="nav-link w-full flex items-center gap-3 px-4 py-3.5 rounded-xl text-gray-600 hover:bg-primary/5 hover:text-primary transition-all duration-300 group">
                    <i data-lucide="history" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                    <span class="font-semibold">Riwayat Peminjaman</span>
                </button>
                <button data-target="bayar-denda" class="nav-link w-full flex items-center gap-3 px-4 py-3.5 rounded-xl text-gray-600 hover:bg-primary/5 hover:text-primary transition-all duration-300 group">
                    <i data-lucide="credit-card" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                    <span class="font-semibold">Bayar Denda</span>
                </button>
            </nav>

            <!-- Bottom Actions -->
            <div class="p-6 border-t border-gray-100 space-y-2">
                <button onclick="location.href = '../index.php'" class="w-full flex items-center gap-3 px-4 py-3.5 rounded-xl bg-success text-white hover:bg-emerald-600 transition-all duration-300 group shadow-md shadow-success/20">
                    <i data-lucide="home" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
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
        <main class="flex-1 min-h-screen overflow-y-auto bg-[#FDFCFB] p-4 lg:p-10">

            <!-- Dashboard Section -->
            <section id="dashboard" class="content-section content-fade">
                <header class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h4 class="text-2xl font-bold text-gray-900">Dashboard</h4>
                        <p class="text-gray-500 mt-1">Selamat datang kembali, mari baca buku hari ini!</p>
                    </div>
                    <div class="flex items-center gap-3 bg-white p-2 rounded-2xl shadow-sm border border-gray-100">
                        <div class="bg-primary/10 text-primary p-2 rounded-xl">
                            <i data-lucide="calendar" class="w-5 h-5"></i>
                        </div>
                        <span id="current-date" class="font-bold text-gray-700 pr-4">Mei 06, 2026</span>
                    </div>
                </header>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                    <!-- Card 1 -->
                    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 hover:shadow-xl hover:shadow-primary/5 transition-all duration-500 group">
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-blue-50 text-blue-600 p-3 rounded-2xl group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                                <i data-lucide="book" class="w-6 h-6"></i>
                            </div>
                            <span class="text-xs font-bold px-2 py-1 bg-blue-100 text-blue-700 rounded-full">Semua</span>
                        </div>
                        <h2 class="text-3xl font-black text-gray-900 mb-1"><?= $totalLoans ?></h2>
                        <p class="text-gray-500 text-sm font-semibold">Total Semua Buku</p>
                    </div>
                    <!-- Card 2 -->
                    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 hover:shadow-xl hover:shadow-primary/5 transition-all duration-500 group">
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-amber-50 text-amber-600 p-3 rounded-2xl group-hover:bg-amber-600 group-hover:text-white transition-colors duration-300">
                                <i data-lucide="book-open-check" class="w-6 h-6"></i>
                            </div>
                            <span class="text-xs font-bold px-2 py-1 bg-amber-100 text-amber-700 rounded-full">Aktif</span>
                        </div>
                        <h2 class="text-3xl font-black text-gray-900 mb-1"><?= $totalDipinjam ?></h2>
                        <p class="text-gray-500 text-sm font-semibold">Buku Dipinjam</p>
                    </div>
                    <!-- Card 3 -->
                    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 hover:shadow-xl hover:shadow-primary/5 transition-all duration-500 group">
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-red-50 text-red-600 p-3 rounded-2xl group-hover:bg-red-600 group-hover:text-white transition-colors duration-300">
                                <i data-lucide="alert-circle" class="w-6 h-6"></i>
                            </div>
                            <span class="text-xs font-bold px-2 py-1 bg-red-100 text-red-700 rounded-full">Denda</span>
                        </div>
                        <h2 class="text-3xl font-black text-gray-900 mb-1"><?= formatRupiah($totalFine) ?></h2>
                        <p class="text-gray-500 text-sm font-semibold">Total Denda</p>
                    </div>
                    <!-- Card 4 -->
                    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 hover:shadow-xl hover:shadow-primary/5 transition-all duration-500 group">
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-green-50 text-green-600 p-3 rounded-2xl group-hover:bg-green-600 group-hover:text-white transition-colors duration-300">
                                <i data-lucide="check-circle-2" class="w-6 h-6"></i>
                            </div>
                            <span class="text-xs font-bold px-2 py-1 bg-green-100 text-green-700 rounded-full">Selesai</span>
                        </div>
                        <h2 class="text-3xl font-black text-gray-900 mb-1"><?= $totalKembali ?></h2>
                        <p class="text-gray-500 text-sm font-semibold">Buku Dikembalikan</p>
                    </div>
                </div>

                <!-- Recent Activity/Mini Table -->
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-6">
                        <h5 class="text-xl font-bold text-gray-900">Aktivitas Terakhir</h5>
                        <button class="text-primary font-bold text-sm hover:underline">Lihat Semua</button>
                    </div>
                    <div class="space-y-4">
                        <?php if (count($activities) > 0) : ?>
                            <?php foreach ($activities as $activity) : ?>
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-16 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
                                            <img
                                                src="<?= $activity['image_url'] ? $activity['image_url'] : '../images/foto7.jpeg' ?>"
                                                alt="<?= $activity['title'] ?>"
                                                class="w-full h-full object-cover">
                                        </div>
                                        <div>
                                            <h6 class="font-bold text-gray-800"><?= $activity['title'] ?></h6>
                                            <p class="text-xs text-gray-500"><?= loanActivity($activity) ?></p>
                                        </div>
                                    </div>
                                    <span class="px-4 py-1.5 rounded-full text-xs font-bold bg-blue-50 text-blue-600 border border-blue-100"><?= $activity['status'] ?></span>
                                </div>
                            <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <div class="col-span-full text-center py-20">
                        <h4 class="text-slate-500 text-lg font-medium">
                            Tidak ada Aktivitas Terakhir!.
                        </h4>
                    </div>
                <?php endif; ?>
                </div>
            </section>

            <!-- Daftar Buku Section -->
            <section id="daftar-buku" class="content-section content-fade hidden">
                <header class="mb-10">
                    <h5 class="text-2xl font-bold text-gray-900">Daftar Buku</h5>
                    <p class="text-gray-500 mt-1">Semua koleksi buku yang pernah di pinjam.</p>
                </header>

                <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
                    <?php if (count($loans) > 0) : ?>
                        <div class="table-container overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50/50">
                                        <th class="px-8 py-5 text-sm font-bold text-gray-500 uppercase tracking-wider">No</th>
                                        <th class="px-8 py-5 text-sm font-bold text-gray-500 uppercase tracking-wider">Judul Buku</th>
                                        <th class="px-8 py-5 text-sm font-bold text-gray-500 uppercase tracking-wider">Penulis</th>
                                        <th class="px-8 py-5 text-sm font-bold text-gray-500 uppercase tracking-wider">Penerbit</th>
                                        <th class="px-8 py-5 text-sm font-bold text-gray-500 uppercase tracking-wider">Tanggal Pinjam</th>
                                        <th class="px-8 py-5 text-sm font-bold text-gray-500 uppercase tracking-wider">Tahun Terbit</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <?php foreach ($loans as $loan) : ?>
                                        <tr class="hover:bg-gray-50 transition-colors border-b border-gray-100/50">
                                            <td class="px-8 py-5 font-medium text-gray-600"><?= $noBuku++ ?></td>
                                            <td class="px-8 py-5 font-bold text-gray-900"><?= $loan['title'] ?></td>
                                            <td class="px-8 py-5 text-gray-600"><?= $loan['author'] ?></td>
                                            <td class="px-8 py-5 text-gray-600"><?= $loan['publisher'] ?></td>
                                            <td class="px-8 py-5 text-gray-600"><?= $loan['loan_date'] ?></td>
                                            <td class="px-8 py-5 text-gray-600"><?= $loan['year'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else : ?>
                        <div class="col-span-full text-center py-20">
                            <h4 class="text-slate-500 text-lg font-medium">
                                Tidak ada buku ditemukan.
                            </h4>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Pinjam Buku Section -->
            <section id="pinjam-buku" class="content-section content-fade hidden">
                <header class="mb-10">
                    <h5 class="text-2xl font-bold text-gray-900">Pinjam Buku</h5>
                    <p class="text-gray-500 mt-1">Konfirmasi peminjaman dan aksi pengembalian buku.</p>
                </header>

                <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
                    <?php if (count($loans) > 0) : ?>
                        <div class="table-container overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50/50">
                                        <th class="px-8 py-5 text-sm font-bold text-gray-500 uppercase tracking-wider">No</th>
                                        <th class="px-8 py-5 text-sm font-bold text-gray-500 uppercase tracking-wider">Judul Buku</th>
                                        <th class="px-8 py-5 text-sm font-bold text-gray-500 uppercase tracking-wider">Tgl Pinjam</th>
                                        <th class="px-8 py-5 text-sm font-bold text-gray-500 uppercase tracking-wider">Tgl Kembali</th>
                                        <th class="px-8 py-5 text-sm font-bold text-gray-500 uppercase tracking-wider">Batas Pinjam</th>
                                        <th class="px-8 py-5 text-sm font-bold text-gray-500 uppercase tracking-wider text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <?php foreach ($loans as $loan) : ?>
                                        <tr class="hover:bg-gray-50 transition-colors border-b border-gray-100/50">
                                            <td class="px-8 py-5 font-medium text-gray-600"><?= $noPinjam++ ?></td>
                                            <td class="px-8 py-5 font-bold text-gray-900<?= $loan['status'] == 'dikembalikan' ? 'selesai' : '' ?> "><?= $loan['title'] ?></td>
                                            <td class="px-8 py-5 text-gray-600"><?= $loan['loan_date'] ?></td>
                                            <td class="px-8 py-5 text-gray-600"><?= $loan['return_date'] ?></td>
                                            <td class="px-8 py-5">
                                                <span class="px-2 py-1.5 bg-red-50 text-red-600 rounded-full text-xs font-bold border border-red-100"><?= $loan['due_date'] ?></span>
                                            </td>
                                            <td class="px-8 py-5">
                                                <div class="flex items-center justify-center gap-2">
                                                    <form method="POST">
                                                        <input type="hidden" name="loan_id" value="<?= $loan['id'] ?>">
                                                        <input type="hidden" name="book_id" value="<?= $loan['book_id'] ?>">
                                                        <button type="submit" name="kembalikan"
                                                            class="flex items-center gap-2 bg-success text-white px-4 py-2 rounded-xl text-xs font-bold shadow-lg shadow-success/20 hover:scale-105 active:scale-95 transition-all <?= $loan['status'] === 'dipinjam' ? "bg-green-500 text-white hover:bg-green-600 shadow-md shadow-green-200" : "border border-primary text-primary cursor-not-allowed opacity-60" ?>"
                                                            <?= $loan['status'] !== 'dipinjam' ? 'disabled' : '' ?> data-id="<?= $loan['id'] ?>">
                                                            <i data-lucide="check" class="w-4 h-4 rounded-full border border-slate-200"></i>
                                                            <?= $loan['status'] !== 'dipinjam' ? 'Sudah Kembali' : 'Kembalikan' ?>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else : ?>
                        <div class="col-span-full text-center py-20">
                            <h4 class="text-slate-500 text-lg font-medium">
                                Tidak ada Buku yang dipinjam.
                            </h4>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Riwayat Peminjaman Section -->
            <section id="riwayat" class="content-section content-fade hidden">
                <header class="mb-10">
                    <h5 class="text-2xl font-bold text-gray-900">Riwayat Peminjaman</h5>
                    <p class="text-gray-500 mt-1">Lacak semua history peminjaman dan denda Anda.</p>
                </header>

                <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
                    <?php if (count($loans) > 0) : ?>
                        <div class="table-container overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50/50">
                                        <th class="px-8 py-5 text-sm font-bold text-gray-500 uppercase tracking-wider">No</th>
                                        <th class="px-8 py-5 text-sm font-bold text-gray-500 uppercase tracking-wider">Judul Buku</th>
                                        <th class="px-8 py-5 text-sm font-bold text-gray-500 uppercase tracking-wider">Penulis</th>
                                        <th class="px-8 py-5 text-sm font-bold text-gray-500 uppercase tracking-wider">Tgl Pinjam</th>
                                        <th class="px-8 py-5 text-sm font-bold text-gray-500 uppercase tracking-wider">Tgl Kembali</th>
                                        <th class="px-8 py-5 text-sm font-bold text-gray-500 uppercase tracking-wider">Status</th>

                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <?php foreach ($loans as $loan) : ?>
                                        <?php
                                        $badge = loanStatus($loan);
                                        ?>
                                        <tr class="hover:bg-gray-50 transition-colors border-b border-gray-100/50">
                                            <td class="px-8 py-5 font-medium text-gray-600"><?= $noRiwayat++ ?></td>
                                            <td class="px-8 py-5 font-bold text-gray-900"><?= $loan['title'] ?></td>
                                            <td class="px-8 py-5 font-bold text-gray-900"><?= $loan['author'] ?></td>
                                            <td class="px-8 py-5 text-gray-600"><?= $loan['loan_date'] ?></td>
                                            <td class="px-8 py-5 text-gray-600"><?= $loan['return_date'] ?></td>
                                            <td class="px-8 py-5">
                                                <span class="px-4 py-1.5 rounded-full text-xs font-bold <?= $badge['class'] ?>"><?= $badge['status'] ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else : ?>
                        <div class="col-span-full text-center py-20">
                            <h4 class="text-slate-500 text-lg font-medium">
                                Tidak ada Riwayat peminjaman buku.
                            </h4>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Bayar Denda Section -->
            <section id="bayar-denda" class="content-section content-fade hidden">
                <header class="mb-10">
                    <h5 class="text-2xl font-bold text-gray-900">Simulasi Bayar Denda</h5>
                    <p class="text-gray-500 mt-1">Total denda terakumulasi dari keterlambatan pengembalian buku.</p>
                </header>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2">
                        <?php if (!empty($loans)) : ?>
                            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
                                <div class="table-container overflow-x-auto">
                                    <table class="w-full text-left border-collapse">
                                        <thead>
                                            <tr class="bg-gray-50/50">
                                                <th class="px-8 py-5 text-sm font-bold text-gray-500 uppercase tracking-wider">No</th>
                                                <th class="px-8 py-5 text-sm font-bold text-gray-500 uppercase tracking-wider">Judul</th>
                                                <th class="px-8 py-5 text-sm font-bold text-gray-500 uppercase tracking-wider">Telat</th>
                                                <th class="px-8 py-5 text-sm font-bold text-gray-500 uppercase tracking-wider">Denda</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            <?php foreach ($loansDenda as $loanDenda) : ?>
                                                <?php
                                                $lateDays = calculateLateDays(
                                                    $loanDenda['due_date']
                                                );
                                                $fine = $settingFine * $lateDays;
                                                ?>
                                                <tr class="hover:bg-gray-50 transition-colors border-b border-gray-100/50">
                                                    <td class="px-8 py-5 font-bold text-gray-900"><?= $noDenda++ ?></td>
                                                    <td class="px-8 py-5 font-bold text-gray-900"><?= $loanDenda['title'] ?></td>
                                                    <td class="px-8 py-5 text-gray-600"><?= $lateDays ?></td>
                                                    <td class="px-8 py-5 font-bold text-red-600"><?= formatRupiah($fine) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else : ?>
                                <div class="col-span-full text-center py-20">
                                    <h4 class="text-slate-500 text-lg font-medium">
                                        Tidak ada Denda yang dibayarkan.
                                    </h4>
                                </div>
                            <?php endif; ?>
                            </div>
                    </div>

                    <div class="lg:col-span-1">
                        <div class="bg-primary p-8 rounded-[2rem] text-white shadow-xl shadow-primary/20 flex flex-col justify-between min-h-[300px]">
                            <div>
                                <h6 class="text-white/80 font-bold uppercase tracking-widest text-xs mb-2">Total Akumulasi</h6>
                                <h2 class="text-4xl font-black mb-6"><?= formatRupiah($totalFine) ?></h2>
                                <div class="space-y-3">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-white/70">Total Item</span>
                                        <span class="font-bold text-white"><?= $lateTotal ?></span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-white/70">Biaya Admin</span>
                                        <span class="font-bold text-white">Rp 0</span>
                                    </div>
                                    <div class="h-px bg-white/20 my-4"></div>
                                </div>
                            </div>
                            <form method="POST" action="">
                                <input type="hidden" name="member_id" value="<?= $member['id'] ?>">
                                <button type="submit" name="pay_fine"
                                    class="w-full bg-white text-primary py-4 rounded-2xl font-black hover:bg-opacity-90 active:scale-95 transition-all shadow-lg">
                                    BAYAR SEKARANG
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </section>

        </main>
    </div>

    <!-- Mobile Overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-30 hidden transition-opacity duration-300"></div>

    <script>
        // Initialize Lucide Icons
        lucide.createIcons();

        // Mobile Menu Toggle 
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const toggleBtn = document.getElementById('mobile-menu-toggle');

        function toggleSidebar() {
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
            setTimeout(() => overlay.classList.toggle('opacity-0'), 10);
        }

        toggleBtn.addEventListener('click', toggleSidebar);
        overlay.addEventListener('click', toggleSidebar);

        // Navigation Logic
        const navLinks = document.querySelectorAll('.nav-link');
        const contentSections = document.querySelectorAll('.content-section');

        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                const targetId = link.getAttribute('data-target');

                // Link State
                navLinks.forEach(l => {
                    l.classList.remove('active', 'bg-primary', 'text-white', 'shadow-lg', 'shadow-primary/20');
                    l.classList.add('text-gray-600', 'hover:bg-primary/5', 'hover:text-primary');
                });

                link.classList.add('active', 'bg-primary', 'text-white', 'shadow-lg', 'shadow-primary/20');
                link.classList.remove('text-gray-600', 'hover:bg-primary/5', 'hover:text-primary');

                // Switch Content
                contentSections.forEach(section => {
                    section.classList.add('hidden');
                    if (section.id === targetId) {
                        section.classList.remove('hidden');
                    }
                });

                // Close sidebar on mobile after click
                if (window.innerWidth < 1024) {
                    toggleSidebar();
                }
            });
        });

        // active dashboard link
        document.querySelector('[data-target="dashboard"]').classList.add('bg-primary', 'text-white', 'shadow-lg', 'shadow-primary/20');
        document.querySelector('[data-target="dashboard"]').classList.remove('text-gray-600');

        // Date update
        const dateEl = document.getElementById('current-date');
        const options = {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        };
        dateEl.textContent = new Date().toLocaleDateString('id-ID', options);
    </script>
</body>

</html>