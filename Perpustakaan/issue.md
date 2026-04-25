# 🚀 Task: Pembuatan Section Katalog Buku (Beranda)

Halo lagi! 👋 Sekarang kita masuk ke bagian inti dari LiBooks, yaitu menampilkan daftar buku yang ada di perpustakaan kita.

Tugas kali ini lumayan seru: kita akan bikin section "Katalog Buku" lengkap dengan *card* buku yang interaktif, data dummy, dan juga sistem pagination sederhana. Santai aja, ikuti panduan di bawah ini step-by-step ya!

## 🎯 Daftar Kerjaan (To-Do List)

### 1. Buat Header Section Katalog
- **Target**: Bikin area baru di bawah Hero Section.
- **Caranya**: Tambahkan section baru. Di bagian atasnya, kasih **Judul Utama** (misal: "Koleksi Buku Kami" atau "Jelajahi Dunia Lewat Membaca"). Di bawah judul, kasih teks deskripsi kecil (subtitle) yang menggambarkan koleksi buku di perpustakaan ini. Bikin tampilannya rata tengah (center) biar cakep.

### 2. Siapkan 18 Data Dummy & Pagination (Tampil 6 per Halaman)
- **Target**: Punya data buku bohongan untuk ngetes tampilan.
- **Caranya**: 
  - Buat array/list berisi 18 objek data buku (di JavaScript). Data ini harus memuat: `judul`, `penulis`, `penerbit`, `tahun_terbit`, `gambar_sampul`, `deskripsi_singkat`, dan `status` ("tersedia" atau "tidak_tersedia").
  - Untuk gambar sampul, **jangan pakai warna polos** ya! Cari URL gambar ilustrasi buku yang bagus.
  - Tampilkan **hanya 6 buku** di halaman pertama. Nanti di bawah daftar buku, buatkan komponen **Pagination** (tombol angka 1, 2, 3) untuk mensimulasikan perpindahan halaman.

### 3. Buat Desain Card Buku
- **Target**: Menampilkan data buku dalam bentuk *card* (kartu) yang rapi.
- **Caranya**: Susun *card* menggunakan Grid layout (misalnya 3 kolom di desktop, 1 kolom di mobile). Setiap *card* harus menampilkan:
  - **Sampul Buku**: Taruh di bagian paling atas card.
  - **Judul, Penulis, Penerbit, Tahun Terbit**: Susun rapi dengan hierarki tipografi yang pas (judul lebih besar/tebal).
  - **Deskripsi Singkat**: Jangan terlalu panjang, potong aja kalau kepanjangan (bisa pakai utility line-clamp).
  - **Status Ketersediaan**: Buat sebagai *badge* atau tombol info kecil dengan style `rounded-md`.
  - **Tombol Aksi**: Siapkan tombol "Pinjam Buku" dan "Detail Buku".
  - **Link/Clickable**: Berikan fungsi agar saat *card* diklik (atau ada tombol khusus) akan mengarah ke halaman detail. Karena halaman detailnya belum dibikin, kasih atribut `href="#"` atau link *dummy* aja dulu, yang penting atributnya siap disamber.
  - **Animasi Hover**: Tambahkan efek transisi saat *card* di-hover (misalnya *card* sedikit terangkat ke atas seperti `hover:-translate-y-2` dan shadow-nya menebal).

### 4. Logic Status Ketersediaan (Sangat Penting!)
- **Target**: Membedakan secara visual antara buku yang bisa dipinjam dan yang tidak.
- **Caranya**:
  - **Jika Status = "Tersedia"**: 
    - Tombol "Pinjam Buku" diberi warna *background* hijau (misal `bg-green-500` atau warna hijau yang kalem), teks putih, dan bisa diklik secara normal.
  - **Jika Status = "Tidak Tersedia"**: 
    - Tombol "Pinjam Buku" **tidak boleh punya warna background** (transparan).
    - Sebagai gantinya, berikan **border warna cokelat** utama kita (misal `border border-[#A86E43]`) dan teks warna cokelat.
    - Bikin tombol ini *disabled* (tidak bisa diklik, hilangkan efek hover-nya, atau tambah `cursor-not-allowed`).
    - Bikin keseluruhan *card* buku tersebut menjadi **sedikit transparan** (kasih class seperti `opacity-60` atau `opacity-75`).

---

**📝 Note untuk Eksekutor (Kamu yang ngerjain):**
Tugas ini fokus ke penyiapan struktur data (JS) dan *conditional styling* di HTML/Tailwind (gaya yang berubah tergantung status ketersediaan buku). Buat fungsi render JS sederhana untuk me-looping 6 buku dari 18 data dummy itu ke dalam grid HTML ya. Jangan lupa perhatikan estetika UI-nya biar hasilnya premium! Semangat! 🚀
