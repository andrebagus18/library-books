# 🚀 Task: Pembuatan Halaman Utama LiBooks (Web Management Books)

Halo! 👋 Selamat datang di project LiBooks. Di task kali ini, misi utamanya adalah membangun halaman muka (landing page) yang kece, responsif, dan pastinya memanjakan mata pengunjung.

Biar gampang, kita breakdown langkah-langkahnya ya. Ikuti aja step-by-step di bawah ini:

## 🛠️ Persiapan Awal
1. **Buat file `index.html`**: Bikin file baru dengan nama `index.html` di root folder project.
2. **Setup Tailwind CSS via CDN**: Gak perlu repot install npm dulu, cukup pasang script Tailwind CSS CDN di bagian `<head>` file `index.html` kamu.

## 🎨 Design Guidelines (Aturan Main Warna & Gaya)
- **Warna Aksen Utama (`#A86E43`)**: Gunakan warna coklat elegan ini untuk elemen penting, seperti teks judul utama atau tombol aksi (Call to Action / CTA). Bebas berkreasi, yang penting kelihatan cakep!
- **Warna Background(`#E0E0E0`)**: Pakai warna abu-abu terang ini untuk warna latar belakang (background) atau ornamen-ornamen pendukung supaya tampilannya kalem dan bersih.
- **Warna pendukung** : gunakan warna pendukung lain sesauikan UI nya biar keren dan estetik.

## 🧱 Struktur Halaman (Bikin Layout-nya)

### 1. Navigation Bar (Navbar)
Navbar ini diletakkan paling atas ya buat agar tetap diatas ketika di scroll.
- **Kiri**: Teks logo / brand name **"LiBooks"** (Bikin agak tebal/bold biar standout).
- **Tengah**: Menu navigasi yang berisi: **Search**, **Beranda**, dan **Kategory**. (Sejajarkan secara horizontal dan kasih jarak yang enak dilihat).
- **Kanan**: Tombol **"Login"** dan **"Sign Up"**. (Bisa dibedakan stylenya, misal "Sign Up" pakai warna background `#A86E43` dan teks putih).

### 2. Hero Section (Bagian Utama yang Langsung Kelihatan)
Bagian ini wajib **Full Screen** (tingginya memenuhi layar, di Tailwind bisa pakai class `h-screen` atau `min-h-screen`). 

Bagi hero section ini jadi 2 kolom (kiri dan kanan) untuk tampilan desktop:

**Kolom Kiri (Konten Teks & Pencarian):**
- **Teks Judul Besar**: **"The LiBooks,"** (Bisa pakai ukuran teks yang super gede, misalnya `text-5xl` ke atas dan kasih warna `#A86E43`).
- **Teks Sub-judul (Kecil/Sedang)**: Di bawah judul, tambahkan teks: *"Browse from the largest collection of books Read stories from anywhere, at anytime."*
- **Form Pencarian**: Di bawah sub-judul, buat form pencarian yang terdiri dari:
  - Input text dengan placeholder: *"Cari judul atau penulis buku"*
  - Tombol dengan tulisan: **"Jelajah"** (Gunakan warna `#A86E43` untuk tombol ini). Bikin input dan tombolnya nyambung atau sebelahan yang rapi.

**Kolom Kanan (Visual / Gambar):**
- **Gambar Utama**: Pasang gambar `hero.png` (asumsikan filenya ada di folder yang sama atau folder assets).
- **Ornamen/Aksen Tambahan**: Tambahkan elemen visual pemanis (seperti stiker, shape abstrak, atau pattern) di sekitar gambar. **Catatan penting:** pastikan ornamen ini cuma jadi pendukung aja dan *jangan sampai menutupi atau mengganggu fokus* dari gambar `hero.png` itu sendiri.

## 📱 Responsiveness (Wajib Tampil Kece di HP!)
- Pastikan desain ini responsif sampai ke ukuran layar HP (mobile).
- Tips: Di layar HP, navbar mungkin jadi hamburger menu (atau disembunyikan/disederhanakan), dan Hero Section yang tadinya 2 kolom kiri-kanan, berubah jadi atas-bawah (gambar di atas, teks di bawah, atau sebaliknya). Gunakan utility classes responsive dari Tailwind (seperti `md:`, `lg:`) buat ngatur ini.

---

**📝 Note untuk yang mengerjakan:**
Kerjainnya pelan-pelan aja, yang penting rapi dan sesuai instruksi. Jangan ragu buat ngecek dokumentasi Tailwind CSS kalau ada class yang lupa. Semangat codingnya! 🔥
