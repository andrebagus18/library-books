# 🚀 Task: Perbaikan & Peningkatan Fitur Katalog Buku

Halo tim! 👋 Katalog buku kita udah jalan, tapi masih ada beberapa *bug* dan penambahan fitur yang harus kita kerjain biar makin mantap. 

Tugas kali ini fokus pada perbaikan gambar, bikin tampilan lebih ramah di HP (mobile responsive), nambahin fitur stok buku, nambah data dummy, dan bikin pagination jadi lebih fungsional. Yuk, sikat!

## 🎯 Daftar Kerjaan (To-Do List)

### 1. Fix Gambar Rusak (Broken Images)
- **Problem**: Ada beberapa *card* buku yang gambarnya kosong atau gagal di-*load* (karena URL mati).
- **Solusi**: Cek lagi URL gambar di array data dummy JS. Pastikan semua link pakai URL gambar yang valid. Biar lebih tangguh, tambahkan atribut fallback gambar di tag `<img>` (bisa lewat fungsi JS atau atribut HTML `onerror`) supaya kalau gambar utama error, akan memuat gambar *placeholder* default.

### 2. Bikin Tampilan Lebih Nyaman di HP (Mobile Responsive)
- **Problem**: Tampilan katalog mungkin terlalu sesak atau ada margin/padding yang kegedean kalau dibuka di layar HP.
- **Solusi**: 
  - Cek padding container katalog. Kalau sebelumnya dipatok statis (misal `px-[70px]`), ubah jadi dinamis, misalnya `px-6 md:px-[70px]` agar di mobile padding-nya mengecil.
  - Pastikan grid *card* berubah jadi 1 kolom (`grid-cols-1`) di layar kecil, dan ukuran font judul buku disesuaikan biar rapi dan nggak makan tempat berlebihan.

### 3. Tambah Fitur Stok Buku & Update Aturan Peminjaman
- **Target**: Menampilkan info stok dan bikin buku hanya bisa dipinjam kalau stok tersedia.
- **Caranya**:
  - Di JavaScript (data dummy), tambahkan key/properti baru bernama `stok` (isi dengan angka acak, pastikan ada yang diisi `0` buat ngetes fitur). Kamu tidak perlu lagi properti `tersedia` (boolean) karena sekarang diganti menggunakan logic angka stok.
  - Di tampilan *card* HTML, tampilkan **"Stok: [jumlah]"** berjejeran atau digabung dengan info *Author*, *Publisher*, dan *Year*.
  - **Update Logic Status**: 
    - Jika `stok > 0`: Tombol "Pinjam Buku" berwarna hijau dan aktif. Card cerah.
    - Jika `stok === 0`: Tombol "Pinjam Buku" menjadi transparan dengan *border* cokelat (warna utama kita), tidak bisa diklik (*disabled*), dan teks berubah jadi "Stok Habis". Card buku tersebut juga dibuat sedikit transparan (contoh: class `opacity-70`).

### 4. Tambah 5 Data Dummy Baru
- **Target**: Biar koleksi buku makin banyak untuk ngetes *pagination*.
- **Caranya**: Tambahkan 5 data buku baru ke dalam array data dummy JS. Lengkapi semua informasinya (judul, penulis, penerbit, tahun, gambar, deskripsi, dan stok).

### 5. Upgrade Pagination (Tambah Tombol Prev & Next)
- **Target**: Memudahkan navigasi perpindahan halaman.
- **Caranya**:
  - Ubah kode JS pembentuk *pagination*. Selain menghasilkan tombol angka (1, 2, 3), buatkan tombol khusus untuk **Prev** (Sebelumnya) di posisi paling kiri, dan **Next** (Selanjutnya) di posisi paling kanan.
  - **Aturan Prev**: Berfungsi mengurangi angka halaman. Tombol ini harus mati (*disabled*) atau disembunyikan kalau *user* sedang berada di Halaman 1.
  - **Aturan Next**: Berfungsi menambah angka halaman. Tombol ini harus mati (*disabled*) kalau *user* sedang berada di halaman terakhir.

---

**📝 Note untuk Eksekutor (Kamu yang ngerjain):**
Ngerjainnya urut aja dari nomor 1 sampai 5. Tantangan serunya ada di merombak logic JS untuk ngebaca nilai `stok` alih-alih nilai boolean, dan juga ngebangun logic Prev/Next di pagination. Rajin-rajin ngetes di tampilan *mobile* ya. Semangat ngoding! 🚀

---

### 🐛 Bug Report (Sudah Diperbaiki)
**Masalah**: Terjadi *infinite loop* (looping tanpa henti) pada JavaScript yang menyebabkan *browser* *freeze* atau *crash*.
**Penyebab**: Atribut `onerror` pada tag `<img>` mencoba memuat URL *fallback* (gambar cadangan). Namun, ketika URL *fallback* tersebut juga gagal dimuat, *event* `onerror` akan terus memanggil dirinya sendiri secara berulang-ulang tanpa batas.
**Solusi**: Menghentikan *loop* dengan menetapkan `this.onerror=null;` sebelum menetapkan `this.src` yang baru. Hal ini memastikan bahwa jika gambar cadangan juga gagal, *browser* tidak akan memicu *event* `onerror` lagi.
