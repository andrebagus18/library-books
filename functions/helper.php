<?php
function getImage($imageName)
{
    $path = 'images/';
    $defaultImage = 'foto11.jpeg';

    if (!empty($imageName) && file_exists($path . $imageName)) {
        return $path . $imageName;
    }

    return $path . $defaultImage;
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

    // 2. Hitung total data
    // $stmtTotal = $pdo->query("SELECT COUNT(*) FROM books");
    // $totalData = $stmtTotal->fetchColumn();
    // $totalHalaman = ceil($totalData / $limit);

    $books = fetchAll(
        "
        SELECT 
            books.*,
            categories.name AS category_name
        FROM books
        JOIN categories
        ON books.category_id = categories.id
        WHERE books.title ILIKE ?
        ORDER BY created_at DESC
        LIMIT $limit OFFSET $offset
        ",
        ['%' . $search . '%']
    );

    // 3. Ambil data buku dengan LIMIT & OFFSET
    //     $stmtBooks = $pdo->prepare("
    //     SELECT 
    //         books.*,
    //         categories.name AS category_name
    //     FROM books
    //     JOIN categories
    //     ON books.category_id = categories.id
    //     ORDER BY created_at DESC
    //     LIMIT :limit OFFSET :offset
    // ");
    //     $stmtBooks->bindValue(':limit', $limit, PDO::PARAM_INT);
    //     $stmtBooks->bindValue(':offset', $offset, PDO::PARAM_INT);
    //     $stmtBooks->execute();
    //     $books = $stmtBooks->fetchAll(PDO::FETCH_ASSOC);

    // 4. Kembalikan semua informasi dalam satu array
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
