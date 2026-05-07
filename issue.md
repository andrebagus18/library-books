# Panduan Implementasi Halaman Admin Dashboard (admin.html)

Halo Tim / AI! 👋 

Kita ada tugas nih untuk membuat halaman dashboard admin statis (`admin.html`). Halaman ini akan ditaruh di dalam folder `public`. Untuk sekarang, **nggak perlu koneksi database dulu**, cukup pakai HTML, TailwindCSS v4, dan sedikit Vanilla JavaScript untuk interaksinya. 

Fokus utama kita adalah bikin tampilannya rapi, *responsive* (enak dilihat di mobile, tablet, maupun desktop), dan *smooth* saat interaksi. Gunakan Flexbox dan Grid biar layout-nya mantap!

Berikut adalah rincian tugasnya yang bisa kamu ikuti tahap demi tahap:

## 1. Persiapan Struktur Dasar
- Buat file `admin.html` di dalam folder `public`.
- Pastikan file ini sudah me-load TailwindCSS v4 (sesuai setup project yang sudah ada).
- Buat struktur layout utama menjadi 2 bagian besar: **Sidebar** (kiri) dan **Main Content** (kanan). 
- Di layar mobile, sidebar ini bisa dibuat tersembunyi (*offcanvas* atau *drawer*) dan muncul kalau tombol hamburger diklik.

---

## 2. Membuat Sidebar
Buat sidebar yang posisinya statis di kiri (atau *drawer* di mobile). Isinya:
1. **Logo**: Taruh di paling atas, kasih styling yang keren.
2. **Profil Admin**: Di bawah logo, kasih icon profil (bisa pakai placeholder gambar/SVG) beserta nama admin di bawahnya.
3. **Menu Navigasi**: Buat *list* menu berikut. Kasih efek *hover* yang *smooth* (misal: transisi background color):
   - 📚 **Buku** (CRUD buku)
   - 🏷️ **Kategori** (CRUD kategori)
   - 👥 **Member** (Manajemen member)
   - 🧑‍💻 **User** (Manajemen admin/member)
   - 🔁 **Peminjaman** (Konfirmasi pengembalian)
   - 💰 **Denda** (Konfirmasi pembayaran)
   - 📊 **Laporan** (Cetak laporan peminjaman, denda, buku populer)
   - ⚙️ **Pengaturan** (Atur besaran denda per hari)
   - 🚪 **Logout**

---

## 3. Membuat Main Content & Interaksi JavaScript
Area utama (kanan) akan menampilkan konten sesuai menu yang diklik.
**Instruksi JS:** Buat fungsi JS sederhana. Saat menu di sidebar diklik, sembunyikan semua section konten, lalu tampilkan section yang sesuai dengan menu yang diklik. Tambahkan transisi/animasi *fade in* yang *smooth* saat perpindahan konten!

### A. Dashboard Admin (Selalu Muncul di Atas / Default)
- Judul: **Dashboard Admin**
- Buat **4 Kotak Grid** untuk statistik (gunakan CSS Grid: 1 kolom di mobile, 2 di tablet, 4 di desktop):
  1. Total semua buku (dipinjam dan dikembalikan)
  2. Total buku yang sedang dipinjam
  3. Total pendapatan denda
  4. Total member

*(Catatan: Konten B sampai I di bawah ini hanya muncul bergantian ketika menu di sidebar diklik)*

### B. Halaman Buku (CRUD Buku)
- Buat form (kartu) di bagian atas tabel untuk menambahkan buku baru. Isinya:
  - Input: Judul Buku, Penerbit, Tahun Terbit, Stok
  - Tombol **Simpan** warna biru *primary*
- Di bawah form tersebut, bikin tabel manajemen buku dengan kolom: `No`, `Judul Buku`, `Penerbit`, `Tahun Terbit`, `Stok`, `Aksi`.
- Di kolom `Aksi`, tambahkan icon/tombol **Edit** (pensil) dan **Hapus** (tempat sampah).
- Di bagian bawah (atau sebagai struktur dasarnya), siapkan daftar baris (row) tabel buku yang sudah terdaftar beserta datanya secara dummy.

### C. Halaman Kategori (CRUD Kategori)
- Buat form (kartu) di bagian atas tabel untuk menambahkan kategori baru. Isinya:
  - Input: Nama Kategori (dan opsi relasi Judul Buku bila perlu)
  - Tombol **Simpan** warna biru *primary*
- Di bawah form tersebut, bikin tabel kategori dengan kolom: `No`, `Judul Buku` (bila diperlukan relasi, atau cukup `Nama Kategori`), `Kategori`, `Aksi`.
- Tombol **Edit** dan **Hapus** (pakai icon) di kolom `Aksi`.
- Isi dengan struktur baris dummy tabel kategori.

### D. Halaman Member
- Bikin tabel member dengan kolom: `No`, `Nama`, `Email`, `Status` (Aktif/Nonaktif, bisa pakai *badge*), `Aksi`.
- Tombol **Edit** dan **Hapus** di kolom `Aksi`.
- Isi dengan struktur baris dummy tabel member.

### E. Halaman User
- Buat sebuah form untuk menambahkan user, isinya:
  - Input: Nama, Email, Password, Konfirmasi Password
  - Select: Role (Admin/Member), Status (Aktif/Nonaktif)
- Di bawah form tersebut, buat tabel daftar user yang sudah terdaftar dengan kolom: `No`, `Nama`, `Email`, `Role` (admin/member), `Status` (aktif/nonaktif), `Aksi` (Edit/Hapus).

### F. Halaman Peminjaman
- Buat tabel peminjaman dengan kolom: `No`, `Nama Peminjam`, `Judul Buku`, `Tanggal Kembali`, `Aksi`.
- Di kolom `Aksi`, buat tombol **Konfirmasi Pengembalian** menggunakan icon **Centang**.

### G. Halaman Denda
- Buat tabel denda dengan kolom: `No`, `Nama Peminjam`, `Judul Buku`, `Tanggal Kembali`, `Denda` (Rp), `Aksi`.
- Di kolom `Aksi`, buat tombol **Konfirmasi Pembayaran** pakai icon **Centang**.

### H. Halaman Laporan
- Buat tabel laporan peminjaman/denda. Kolom: `No`, `Nama Peminjam`, `Judul Buku`, `Tanggal Kembali`, `Denda`, `Aksi`.
- Di kolom `Aksi`, sediakan tombol **Hapus** (icon tempat sampah).

### I. Halaman Pengaturan
- Buat form tabel/kartu untuk pengaturan, isinya:
  - Input: `Besaran Denda Per Hari`
- Tambahkan tombol **Simpan** warna biru *primary*.

---

## 4. Ceklis Penilaian (Quality Control)
Sebelum disubmit atau di-*deploy*, pastikan cek hal-hal ini ya:
- [ ] **Styling & Layout**: Apakah Flexbox dan Grid sudah dipakai dengan benar sehingga tabel dan konten terlihat rapi?
- [ ] **Responsiveness**: Coba kecilkan ukuran browser. Apakah di tampilan mobile tabelnya bisa di-*scroll* horizontal dan sidebar aman?
- [ ] **Animasi**: Apakah efek *hover* di tombol dan menu terasa *smooth*? Perpindahan antar menu juga tidak kaku?
- [ ] **Kerapian HTML**: Pastikan tidak ada tag yang belum tertutup dan class Tailwind ditulis dengan efisien (versi 4).

Semangat ngerjainnya! Pelan-pelan aja, pahami tiap langkahnya, dan pastikan hasilnya sekeren mungkin! 🚀
