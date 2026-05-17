<?php
function getImage()
{
    $images = [
        'images/foto1.jpeg',
        'images/foto2.jpeg',
        'images/foto3.jpeg',
        'images/foto4.jpeg',
        'images/foto5.jpeg',
        'images/foto6.jpeg',
        'images/foto7.jpeg',
        'images/foto8.jpeg',
        'images/foto9.jpeg',
        'images/foto10.jpeg',
        'images/foto11.jpeg',
        'images/foto12.jpeg',
        'images/foto13.jpeg',
        'images/foto14.jpeg',
    ];
    return $images[array_rand($images)];
}

function getPaginatedBooks($limit = 6)
{
    // 1. Ambil halaman dari URL
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($page < 1) $page = 1;
    // (1-1) * 6 = 0, (2-1)*6=6, (3-1)*6=12 ....
    $offset = ($page - 1) * $limit;
    // $search = "carian";
    $search = $_GET['search'] ?? '';
    $offset = ($page - 1) * $limit;

    $totalData = fetchOne(
        "
        SELECT COUNT(*) as total
        FROM books
        WHERE title ILIKE ?
        ",
        ['%' . $search . '%']
    );
    $totalPage = ceil($totalData['total'] / $limit);
    $books = fetchAll(
        "
    SELECT 
        books.*,
        categories.name AS category_name
    FROM books
    LEFT JOIN categories
    ON books.category_id = categories.id
    WHERE books.title ILIKE ?
    ORDER BY created_at DESC
    LIMIT $limit OFFSET $offset
    ",
        ['%' . $search . '%']
    );
    return [
        'data' => $books,
        'currentPage' => $page,
        'totalPage' => $totalPage,
        'totalData' => $totalData['total']
    ];
}

function getCarouselBooks($pdo, $limit = 8)
{
    $stmt = $pdo->prepare("
        SELECT
            books.*,
            categories.name AS category_name
        FROM books
        JOIN categories
        ON books.category_id = categories.id
        ORDER BY created_at DESC
        LIMIT :limit
    ");

    $stmt->bindValue(
        ':limit',
        $limit,
        PDO::PARAM_INT
    );

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function isLogin()
{
    return isset($_SESSION['user_id']);
}

function isAdmin()
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}
function adminName()
{
    return $_SESSION['username'] ?? '';
}
function adminFullName()
{
    return $_SESSION['full_name'] ?? '';
}

function redirect($url)
{
    header("Location: $url");
    exit;
}

function setFlash($type, $message)
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash()
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function calculateLateDays($due_date)
{
    if (!$due_date) return 0;

    $due = strtotime(date('Y-m-d', strtotime($due_date)));
    $today = strtotime(date('Y-m-d'));

    if ($today <= $due) return 0;

    return (int)(($today - $due) / 86400);
}

function calculateFine($due_date)
{
    $lateDays = calculateLateDays($due_date);
    $finePerDay = getFinePerDay();
    return $lateDays * $finePerDay;
}
function getFinePerDay()
{
    $setting = fetchOne("SELECT value FROM configDenda WHERE key = 'fine_per_day'");
    return isset($setting['value']) ? (int)$setting['value'] : 0;
}

// membuat fungsi status dan badge warna berdasarkan status
function loanStatus($loan)
{
    $status = $loan['status'];
    $class = '';
    if (
        $status == 'dipinjam' &&
        date('Y-m-d') > $loan['due_date']
    ) {
        $status = 'telat';
    }
    switch ($status) {
        case 'dipinjam':
            $class = 'bg-blue-50 text-blue-600 border border-blue-100';
            break;
        case 'dikembalikan':
            $class = 'bg-green-50 text-green-600 border border-green-100';
            break;
        case 'telat':
            $class = 'bg-red-50 text-red-600 border border-red-100';
            break;
    }
    return [
        'status' => ucfirst($status),
        'class' => $class
    ];
}

// function lastDay activity
function loanActivity($loan)
{
    $status = 'Dipinjam';
    if ($loan['status'] == 'dikembalikan') {
        $status = 'Dikembalikan';
    }
    if (
        $loan['status'] == 'dipinjam' &&
        date('Y-m-d') > $loan['due_date']
    ) {
        $status = 'Telat';
    }
    // menghitung dan membulatkan hari dalam hitungan detik
    $days = floor(
        (time() - strtotime($loan['loan_date']))
            / (60 * 60 * 24)
    );
    $timeText = $days == 0
        ? 'Hari ini'
        : $days . ' hari yang lalu';
    return $status . ' ' . $timeText;
}
// format rupiah
function formatRupiah($angka)
{
    return "Rp " . number_format($angka, 0, ',', '.');
}

function updateAllFines()
{
    $finePerDay = getFinePerDay();

    // ambil semua pinjaman yang masih aktif
    $loans = fetchAll("SELECT id, due_date, return_date FROM loans WHERE status = 'dipinjam'");
    foreach ($loans as $loan) {
        // hitung keterlambatan (realtime kalau belum return)
        $lateDays = calculateLateDays($loan['due_date']);
        $fine = $lateDays * $finePerDay;
        query("UPDATE loans SET fine = ? WHERE id = ?", [
            $fine,
            $loan['id']
        ]);
    }
}
// total item telat
function getTotalLateDays($loans)
{
    $total = 0;
    foreach ($loans as $loan) {
        $lateDays = calculateLateDays($loan['due_date'], $loan['return_date']);
        if ($lateDays > 0) {
            $total++;
        }
    }
    return $total;
}
