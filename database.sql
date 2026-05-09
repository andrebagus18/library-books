-- Buat database
CREATE DATABASE library_db;

-- Tabel Users
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    role VARCHAR(20) DEFAULT 'user', -- 'admin' atau 'user'
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Member
CREATE TABLE members (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users,
    member_code VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(15),
    address TEXT,
    registered_date DATE DEFAULT CURRENT_DATE,
    is_active BOOLEAN DEFAULT TRUE
);

-- Tabel Books
CREATE TABLE books (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(100) NOT NULL,
    publisher VARCHAR(100) NOT NULL,
    stock INTEGER DEFAULT 1,
    location VARCHAR(50),
    year INTEGER,
    image_url TEXT,
    category_id INTEGER REFERENCES kategories(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    description VARCHAR(255),
);

--Tabel Kategori
CREATE TABLE categories (
    id SERIAL PRIMARY KEY,
    name VARCHAR(50)
);


--Tabel Loans
CREATE TABLE loans (
    id SERIAL PRIMARY KEY,
    book_id INTEGER REFERENCES books(id) ON DELETE CASCADE,
    member_id INTEGER REFERENCES members(id) ON DELETE CASCADE,
    loan_date DATE DEFAULT CURRENT_DATE,
    due_date DATE,
    return_date DATE,
    status VARCHAR(20) DEFAULT 'dipinjam',
    fine DECIMAL(10,2) DEFAULT 0,
    fine_paid BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

--DATA AWAL
INSERT INTO users (username, email, password, full_name, role) VALUES 
('admin', 'admin@gmail.com', md5('admin123'), 'administrator', 'admin');

INSERT INTO books (title, author, publisher, stock, location, year, image_url, category_id, description) VALUES 
('The Art of Programming','John Doe','Tech Press', 5,'rak_B', 2021, 'foto1.jpeg', 1, 'Panduan lengkap untuk menguasai seni pemrograman modern.'),
('Modern Web Design','Jane Smith','Creative Minds', 0, 'rak_A', 2022, 'foto2.jpeg', 1, 'Eksplorasi tren desain web terbaru untuk tahun 2022.'),
('The Future of AI', 'Alan Turing', 'Future Books', 3, 'rak_C', 2023, 'foto3.jpeg', 1, 'Masa depan kecerdasan buatan dan dampaknya bagi manusia.'),
('JavaScript Masterclass', 'Brendan Eich', 'JS Guru', 8, 'rak_D', 2020, 'foto4.jpeg', 1, 'Kuasai JavaScript dari dasar hingga tingkat lanjut.'),
('UI/UX Essentials', 'Sarah Johnson', 'Design Co', 6, 'rak_C', 2021, 'foto5.jpeg', 3, 'Prinsip-prinsip penting dalam desain UI dan UX'),
('The Digital Nomad','Chris Brown','Traveler Ink', 12, 'rak_B', 2019, 'foto6.jpeg', 3, 'Cara menjalani hidup sebagai nomad digital yang sukses.'),
('Startup Secrets', 'Elon Musk', 'Innovation Press', 4, 'rak_A', 2023, 'foto7.jpeg', 2, 'Rahasia di balik kesuksesan startup raksasa.'),
('Productivity Hacks', 'Tim Ferriss', 'Efficient Life', 0, 'rak_D', 2021, 'foto8.jpeg', 2, 'Tips praktis untuk meningkatkan produktivitas harian Anda.'),
('Data Science 101', 'Andrew Ng', 'Code Academic', 7, 'rak_B', 2022, 'foto9.jpeg', 2, 'Langkah awal untuk memahami dunia data science.'),
('Creative Writing', 'Ernest Hemingway', 'Lit Books', 15, 'rak_A', 2020, 'foto10.jpeg', 3, 'Teknik menulis kreatif dari sang legenda sastra.');

INSERT INTO categories (name) VALUES 
('progamming'),
('data_science'),
('UI/UX');
