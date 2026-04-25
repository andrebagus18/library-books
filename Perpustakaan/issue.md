# 🚀 Task: Update & Perbaikan Tampilan Landing Page LiBooks

Halo lagi! 👋 Melanjutkan progress kita di project LiBooks, kali ini ada beberapa penyesuaian dan *polishing* (perbaikan kecil) biar tampilan website kita makin mulus dan interaktif. 

Tugas kali ini fokus pada beberapa penyesuaian jarak, penambahan ikon, animasi menu, dan perbaikan *bug* kecil di versi mobile. Yuk, ikuti langkah-langkah di bawah ini!

## 🎯 Daftar Kerjaan (To-Do List)

### 1. Update Spacing (Jarak) di Hero Section
- **Target**: Berikan jarak ekstra di sisi kanan dan kiri Hero Section sebesar **70px**.
- **Caranya**: Di elemen container/pembungkus bagian hero (yang berisi teks besar dan gambar), tambahkan padding kiri dan kanan sebesar `70px`. Kalau kamu pakai utility class Tailwind, kamu bisa gunakan arbitrary value seperti `px-[70px]`. Pastikan jarak ini diterapkan dengan rapi, terutama di layar desktop.

### 2. Tambah Ikon Orang di Bagian Social Proof
- **Target**: Bikin bagian "Bergabung dengan 10k+ pembaca aktif" (yang letaknya di bawah tombol Jelajah) jadi lebih hidup.
- **Caranya**: Di lingkaran-lingkaran kecil (avatar) yang bersebelahan itu, tambahkan ikon orang (user icon). Kamu bisa pakai file SVG (misalnya dari Heroicons) atau font icon. Pastikan ikonnya diletakkan persis di tengah-tengah lingkaran tersebut biar proporsional.

### 3. Update Menu Navbar ("Search" ➡️ "Terbaru")
- **Target**: Ganti teks menu navigasi.
- **Caranya**: Cari elemen teks link bertuliskan **"Search"** di area navbar (di bagian tengah), lalu ubah teksnya menjadi **"Terbaru"**.

### 4. Animasi Garis Bawah (Hover Effect) di Menu Navbar
- **Target**: Menu "Beranda", "Kategory", "Terbaru", dan "Login" harus punya efek garis bawah (border-bottom) yang keren saat di-hover. Animasinya harus bergerak **melebar dari tengah ke arah luar (kiri dan kanan)**.
- **Caranya**: 
  - Gunakan trik CSS pseudo-element (`::after`).
  - Beri posisi `absolute` di bagian bawah teks menu.
  - Set `width` (lebar) jadi `0%` secara default, posisikan di `left: 50%`, lalu beri properti `transition: all 0.3s ease-in-out` (atau sejenisnya).
  - Ketika menu di-hover (`:hover::after`), ubah `width` menjadi `100%` dan geser posisi `left` menjadi `0%`. Triks ini bakal menghasilkan efek animasi garis bawah yang *smooth* dari tengah ke samping! Jangan lupa bungkus link-nya dengan class `relative`.

### 5. Fix Bug Navbar Tertutup Separuh di Mobile
- **Target**: Saat ini navbar suka ikut ke-scroll atau hilang separuh saat dibuka lewat HP. Kita harus memastikan navbar ini selalu terlihat penuh dan menempel manis di atas.
- **Caranya**: 
  - Cek class yang dipakai pada tag `<nav>`. Pastikan kamu menggunakan kombinasi posisi yang kuat seperti `fixed`, `top-0`, `left-0`, dan `w-full`.
  - Pastikan juga layer-nya berada paling atas dengan memberikan z-index yang cukup tinggi (misalnya `z-50`).
  - Jika konten hero terdorong ke atas dan tertutup oleh navbar, tambahkan *padding-top* (misal `pt-20` atau `pt-24`) pada kontainer body/hero section sesuai dengan tinggi navbarnya.

---

**📝 Note untuk yang mengerjakan:**
Kerjakan per poin ya! Habis ngedit satu bagian, langsung save dan cek hasilnya di browser supaya gampang kalau ada yang salah (terutama buat animasi *hover*-nya). Semangat dan jangan ragu buat eksplorasi CSS-nya! 🔥
