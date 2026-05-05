# 🔐 Issue: Halaman Member Area — LiBooks

## Deskripsi
Buat halaman `member.html` di dalam folder `public` yang berisi layout **Sidebar** dan **Main Content** untuk Member Area. Halaman ini akan menjadi dashboard bagi user yang sudah login, menampilkan statistik, daftar buku, peminjaman, riwayat, hingga simulasi bayar denda.

## Role
Tugas ini fokus di frontend. Cukup buat 1 file HTML saja yaitu `member.html` di dalam folder `public`. File ini harus statis tanpa database dulu, cukup gunakan desain yang *clean*, Tailwind CSS v4 via CDN, dan sedikit Javascript untuk interaksi ganti halaman (tab).

---

## 🎨 Referensi Desain & Layout
- **Tailwind CSS v4:** Gunakan Tailwind CSS versi terbaru via CDN.
- **Responsif:** Harus rapi di Mobile, Tablet, dan Desktop. Gunakan CSS Grid dan Flexbox.
- **Animasi:** Berikan animasi *smooth* (transisi lembut) saat *hover* elemen, tombol, dan saat ganti menu/konten (fade-in/slide).
- **Tipografi:** Gunakan heading `h4` atau `h5` untuk judul di atas tabel (jangan terlalu besar).

Struktur secara umum akan terbagi dua:
1. **Sidebar (Kiri)**: Berisi Logo, Profil User, dan Menu Navigasi.
2. **Main Content (Kanan)**: Berisi konten yang berubah-ubah tergantung menu sidebar yang diklik.

---

## 📋 Tahapan Implementasi

Berikut adalah panduan santai step-by-step untuk mengimplementasikannya:

### Task 1: Setup Layout Utama (Skeleton)
1. Buat file baru bernama `member.html` di dalam folder `public`.
2. Masukkan struktur dasar HTML5 dan *embed* Tailwind CSS v4 melalui CDN.
3. Buat pembagian layar (layouting) menggunakan Flexbox:
   - Kiri: **Sidebar** (Misal lebar `w-64` di desktop).
   - Kanan: **Main Content** (Sisanya / `flex-1`).
4. **Responsivitas**: Di layar kecil (mobile/tablet), sidebar bisa disembunyikan dan dimunculkan lewat tombol *hamburger menu*, atau dibuat *off-canvas* / *bottom navigation* sesuaikan dengan yang paling rapi.

### Task 2: Buat Komponen Sidebar
Di area Sidebar, tambahkan elemen-elemen berikut dari atas ke bawah:
1. **Logo**: Teks atau gambar "LiBooks" di pojok kiri atas.
2. **User Profile**: Buat desain profil sederhana dengan icon (atau foto *dummy*) dan Nama User di bawahnya.
3. **Menu Navigasi**: Buat daftar list menu:
   - Dashboard
   - Daftar Buku
   - Pinjam Buku
   - Riwayat Peminjaman
   - Bayar Denda
   - Logout
   *(Berikan efek hover yang smooth pada tiap menu, misal background berubah warna tipis saat di-hover).*

### Task 3: Siapkan Konten Dinamis di Main Area
Di bagian Main Content, buat beberapa buah `<div>` (container) untuk masing-masing halaman. Nantinya hanya satu yang ditampilkan secara default (Dashboard), sedangkan yang lain disembunyikan menggunakan class `hidden`.

#### 1. Konten Dashboard (Default)
- Berikan judul "Dashboard"
- Buat 4 kotak metrik (statistik pribadi) menggunakan CSS Grid (`grid-cols-1 sm:grid-cols-2 lg:grid-cols-4`).
- Isi 4 kotak tersebut adalah:
  1. Total Semua Buku (Dipinjam & Dikembalikan)
  2. Total Buku yang Sedang Dipinjam
  3. Total Denda
  4. Total Buku yang Berhasil Dikembalikan
- Buat desain kotaknya rapi, tambahkan *shadow* dan sudut *rounded*.

#### 2. Konten Daftar Buku
- Munculkan saat user klik menu "Daftar Buku".
- Judul: "Daftar Buku" (`h4` / `h5`).
- Buat tabel statis berisi daftar semua buku yang sudah/sedang dipinjam.
- Kolom tabel: `No`, `Judul Buku`, `Tanggal Peminjaman`, `Tanggal Pengembalian`, `Status`.
- *(Status bisa diisi: Dipinjam, Dikembalikan, Telat. Kasih warna badge yang beda biar menarik).*

#### 3. Konten Pinjam Buku
- Munculkan saat user klik menu "Pinjam Buku".
- Judul: "Pinjam Buku" (`h4` / `h5`).
- Buat tabel daftar buku yang sedang dipinjam.
- Kolom tabel: `No`, `Judul Buku`, `Tanggal Peminjaman`, `Tanggal Pengembalian`, `Status`, `Aksi`.
- Di kolom **Aksi**, buat 2 tombol:
  - Tombol **Kembalikan** (Warna biru / primary).
  - Tombol **Bayar Denda** (Warna merah / danger).

#### 4. Konten Riwayat Peminjaman
- Munculkan saat user klik menu "Riwayat Peminjaman".
- Judul: "Riwayat Peminjaman" (`h4` / `h5`).
- Buat tabel riwayat peminjaman.
- Kolom tabel: `No`, `Judul Buku`, `Tanggal Peminjaman`, `Tanggal Pengembalian`, `Status`, `Denda`.
- *Rules*: Jika statusnya "Telat", akan muncul jumlah denda (Rp 10.000 / hari keterlambatan). Isi dengan data statis *dummy*.

#### 5. Konten Bayar Denda
- Munculkan saat user klik menu "Bayar Denda".
- Judul: "Simulasi Pembayaran Denda" (`h4` / `h5`).
- Buat tabel simulasi pembayaran.
- Kolom tabel: `No`, `Judul Buku`, `Tanggal Dipinjam`, `Tanggal Pengembalian`, `Total Hari (Telat)`, `Total Denda`.
- *Rules*: Denda Rp 10.000 per hari keterlambatan dan total dendanya diakumulasikan. Isi datanya dengan *hardcode* saja sebagai contoh (misal telat 2 hari = Rp 20.000).

### Task 4: Tambahkan Javascript untuk Interaksi
Biar berasa seperti aplikasi sungguhan, tambahkan sedikit script JS di bagian bawah file `<script>`:
1. Tangkap semua event *klik* pada menu di sidebar.
2. Saat sebuah menu diklik, hapus class `hidden` pada *container* konten yang sesuai, dan tambahkan class `hidden` ke *container* lainnya.
3. Berikan *styling* aktif pada menu sidebar yang sedang dipilih (misal font jadi bold atau warna background lebih gelap).
4. Tambahkan *smooth transition* (misalnya animasi *fade-in* saat perpindahan tabel/konten).

---

## ✅ Checklist Sebelum Selesai
- [ ] File `public/member.html` sudah dibuat.
- [ ] Layout terbagi 2: Sidebar dan Main Content dengan rapi.
- [ ] Sidebar memiliki logo, profil user, dan daftar menu.
- [ ] Dashboard memiliki 4 grid statistik yang responsif.
- [ ] 4 Tabel (Daftar, Pinjam, Riwayat, Denda) sudah terbuat rapi.
- [ ] Javascript bisa mengganti (toggle) konten di Main area sesuai klik di Sidebar.
- [ ] Responsive di layar kecil (tabel bisa di *scroll horizontal*, layout aman).
- [ ] Animasi hover dan perpindahan tab terasa mulus.

Selamat mengoding! Fokus aja ke kerapian layout (Flex/Grid) dan fungsionalitas UI ganti-ganti menunya dulu. Data *dummy* bebas dikarang.
