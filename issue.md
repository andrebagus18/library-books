# 🔐 Issue: Halaman Login & Register — LiBooks

## Deskripsi
Buat halaman `login.html` yang berisi form **Login** dan **Register** dalam satu halaman. Desain harus keren dan premium, tetap menggunakan warna utama website (`#A86E43` sebagai primary, `#E0E0E0` sebagai secondary, font `Outfit`). Gunakan Tailwind CSS via CDN seperti di `index.html`.

## Role
Buat folder bernama "public" lalu buat file di folder itu, bukan di folder project utama

---

## 🎨 Referensi Desain & Warna

| Token | Nilai | Keterangan |
|-------|-------|------------|
| `primary` | `#A86E43` | Warna cokelat utama |
| `secondary` | `#E0E0E0` | Abu-abu terang |
| `accent` | `#F5F5F5` | Background |
| Font | `Outfit` | Google Fonts, sudah ada di project |
| Hero Image | `hero.png` | Gambar yang sama dengan homepage |

---

## 📐 Layout — Desktop (≥ 1024px)

### Mode Login (Default)
```
┌──────────────────────────────────────────────────┐
│  [Form Login]          │   [Hero Panel Cokelat]   │
│                        │                          │
│  Logo "LiBooks"        │   hero.png               │
│  "Masuk ke Akun"       │   "Selamat Datang        │
│                        │    Di LiBooks"            │
│  📧 Email              │                          │
│  🔒 Password [👁]      │   "LiBooks adalah         │
│                        │    platform perpustakaan  │
│  [ Masuk ]             │    online terbaik..."     │
│                        │                          │
│  — atau —              │   [ Next → Register ]    │
│  [🔵 Login Google]     │                          │
│  Lupa Password?        │                          │
│  Belum punya akun?     │                          │
└──────────────────────────────────────────────────┘
```

### Mode Register (Setelah klik "Next" atau "Daftar")
```
┌──────────────────────────────────────────────────┐
│  [Hero Panel Cokelat]  │   [Form Register]        │
│                        │                          │
│   hero.png             │   Logo "LiBooks"         │
│   "Selamat Datang      │   "Buat Akun Baru"       │
│    Di LiBooks"         │                          │
│                        │   👤 Nama Lengkap        │
│   "Daftar dan          │   📧 Email               │
│    dapatkan akses ke   │   🔒 Password [👁]       │
│    ribuan koleksi      │   🔒 Konfirmasi [👁]     │
│    buku"               │                          │
│                        │   [ Daftar ]             │
│   [ ← Back to Login ]  │   — atau —              │
│                        │   [🔵 Daftar Google]     │
│                        │   Sudah punya akun?      │
└──────────────────────────────────────────────────┘
```

> **Penting:** Perpindahan Login ↔ Register menggunakan animasi **slide horizontal smooth** (bukan reload halaman). Panel hero dan form bertukar posisi kiri-kanan.

### Mode Mobile (< 768px)
- **Tidak ada panel hero/gambar** — hanya form saja, full width.
- Background tetap `#E0E0E0`, form di tengah layar.
- Perpindahan Login ↔ Register tetap smooth (slide atau fade).

---

## 📋 Tahapan Implementasi

### Task 1: Buat File `login.html` & Setup Dasar

**Apa yang harus dilakukan:**
1. Buat file baru `login.html` di root folder (sejajar dengan `index.html`).
2. Copy bagian `<head>` dari `index.html` — yang penting:
   - Meta charset & viewport
   - Google Fonts (Outfit)
   - Tailwind CSS CDN
   - Tailwind config (warna primary/secondary/accent)
3. Ganti `<title>` jadi `LiBooks - Login`.
4. Set body: `font-family: Outfit`, `background-color: #E0E0E0`.

**Template awal:**
```html
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>LiBooks - Login</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: "#A86E43",
            secondary: "#E0E0E0",
            accent: "#F5F5F5",
          },
          fontFamily: {
            outfit: ["Outfit", "sans-serif"],
          },
        },
      },
    };
  </script>
  <style>
    body { font-family: "Outfit", sans-serif; background-color: #e0e0e0; }
    /* Tambahkan CSS animasi di sini nanti */
  </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
  <!-- Container utama di sini -->
</body>
</html>
```

---

### Task 2: Buat Container Utama & Struktur Layout

**Apa yang harus dilakukan:**

Buat container utama yang menampung **dua panel** (form + hero) dalam satu baris. Gunakan CSS `overflow: hidden` untuk menyembunyikan konten yang geser keluar.

**Struktur HTML:**
```html
<div id="auth-container" class="w-full max-w-5xl bg-white rounded-[40px] shadow-2xl overflow-hidden relative"
     style="min-height: 600px;">

  <!-- Track — ini yang akan digeser kiri/kanan -->
  <div id="auth-track" class="flex transition-transform duration-700 ease-in-out"
       style="width: 200%;">

    <!-- ===== SLIDE 1: LOGIN ===== -->
    <div class="w-1/2 flex flex-col md:flex-row min-h-[600px]">
      <!-- Kiri: Form Login -->
      <div class="w-full md:w-1/2 p-8 md:p-12 flex flex-col justify-center">
        <!-- ISI FORM LOGIN -->
      </div>
      <!-- Kanan: Hero Panel (hidden di mobile) -->
      <div class="hidden md:flex w-1/2 bg-primary flex-col items-center justify-center p-12 text-white relative">
        <!-- ISI HERO LOGIN -->
      </div>
    </div>

    <!-- ===== SLIDE 2: REGISTER ===== -->
    <div class="w-1/2 flex flex-col md:flex-row-reverse min-h-[600px]">
      <!-- Kanan: Form Register -->
      <div class="w-full md:w-1/2 p-8 md:p-12 flex flex-col justify-center">
        <!-- ISI FORM REGISTER -->
      </div>
      <!-- Kiri: Hero Panel (hidden di mobile) -->
      <div class="hidden md:flex w-1/2 bg-primary flex-col items-center justify-center p-12 text-white relative">
        <!-- ISI HERO REGISTER -->
      </div>
    </div>

  </div>
</div>
```

**Catatan penting:**
- `auth-track` punya `width: 200%` karena menampung 2 slide.
- Setiap slide punya `w-1/2` (50% dari track = 100% viewport).
- Animasi geser dilakukan dengan mengubah `transform: translateX(0)` ↔ `translateX(-50%)`.
- Di mobile, panel hero di-*hide* pakai `hidden md:flex`.

---

### Task 3: Isi Form Login (Slide 1 — Bagian Kiri)

**Apa yang harus dilakukan:**

Isi bagian form login dengan komponen berikut (urutannya dari atas ke bawah):

1. **Logo** — Teks "LiBooks" dengan style `text-2xl font-bold text-primary`.
2. **Judul** — "Masuk ke Akun" (`text-3xl font-bold text-slate-800`).
3. **Subjudul** — "Masukkan email dan password untuk melanjutkan" (`text-sm text-slate-500`).
4. **Input Email** — Label + input field. Pakai icon amplop (SVG) di sebelah kiri.
5. **Input Password** — Label + input field. Pakai icon gembok (SVG) di kiri dan **icon mata (👁) di kanan** untuk toggle show/hide password.
6. **Link "Lupa Password?"** — Teks kecil rata kanan, warna primary.
7. **Tombol "Masuk"** — Full width, `bg-primary text-white rounded-2xl py-3 font-bold`.
8. **Divider** — Garis dengan teks "atau" di tengah.
9. **Tombol Google** — Full width, border, icon Google SVG + teks "Masuk dengan Google".
10. **Link "Belum punya akun? Daftar"** — Teks kecil di bawah. Kata "Daftar" warna primary, onclick pindah ke register.

**Contoh input dengan icon mata:**
```html
<div class="relative">
  <input id="login-password" type="password" placeholder="Password"
    class="w-full pl-10 pr-12 py-3 border border-secondary rounded-2xl outline-none
           focus:ring-2 focus:ring-primary/30 transition-all" />
  <!-- Icon gembok kiri -->
  <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" ...>
    <!-- path gembok -->
  </svg>
  <!-- Icon mata kanan (toggle) -->
  <button type="button" onclick="togglePassword('login-password', this)"
    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-primary">
    <svg id="eye-icon" class="w-5 h-5" ...>
      <!-- path mata terbuka -->
    </svg>
  </button>
</div>
```

**Default login yang harus berfungsi:**
- Email: `admin@libooks.com`
- Password: `admin123`

---

### Task 4: Isi Hero Panel Login (Slide 1 — Bagian Kanan)

**Apa yang harus dilakukan:**

Panel kanan saat mode login. Background `bg-primary` (cokelat).

Isinya (dari atas ke bawah, semua rata tengah):
1. **Gambar** — `hero.png` (file yang sama dengan homepage). Beri `max-w-[250px]`, rounded, dan shadow.
2. **Judul** — "Selamat Datang Di LiBooks" (`text-3xl font-bold text-white`).
3. **Deskripsi** — "LiBooks adalah platform perpustakaan online terbaik, dengan ribuan koleksi buku yang siap menemani waktu luangmu" (`text-white/80 text-sm`).
4. **Tombol "Next →"** — Border putih, teks putih, rounded. Onclick: geser ke register.

```html
<div class="hidden md:flex w-1/2 bg-primary flex-col items-center justify-center p-12 text-white text-center relative overflow-hidden">
  <!-- Ornamen blur (opsional, biar keren) -->
  <div class="absolute -top-20 -right-20 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
  <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>

  <div class="relative z-10 space-y-6">
    <img src="hero.png" alt="LiBooks" class="max-w-[250px] mx-auto rounded-3xl shadow-2xl" />
    <h2 class="text-3xl font-bold">Selamat Datang Di LiBooks</h2>
    <p class="text-white/80 text-sm max-w-xs mx-auto">
      LiBooks adalah platform perpustakaan online terbaik, dengan ribuan koleksi buku yang siap menemani waktu luangmu.
    </p>
    <button onclick="slideToRegister()" id="btn-next" aria-label="Berikutnya" 
          class="absolute -right-6 md:-right-10 top-1/2 -translate-y-1/2 z-20
                 w-12 h-12 rounded-full bg-primary text-white
                 flex items-center justify-center
                 shadow-lg hover:bg-amber-700 transition-all active:scale-95">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </button>
  </div>
</div>
```


---

### Task 5: Isi Form Register (Slide 2 — Bagian Kanan)

**Apa yang harus dilakukan:**

Sama seperti form login, tapi dengan field tambahan:

1. **Logo** — "LiBooks"
2. **Judul** — "Buat Akun Baru"
3. **Subjudul** — "Isi data di bawah untuk mendaftar"
4. **Input Nama Lengkap** — Icon orang di kiri.
5. **Input Email** — Icon amplop di kiri.
6. **Input Password** — Icon gembok + icon mata toggle.
7. **Input Konfirmasi Password** — Icon gembok + icon mata toggle.
8. **Tombol "Daftar"** — Full width, `bg-primary`.
9. **Divider** — "atau"
10. **Tombol Google** — "Daftar dengan Google"
11. **Link "Sudah punya akun? Masuk"** — Onclick pindah ke login.

**Default register:**
- Nama: `User LiBooks`
- Email: `user@libooks.com`
- Password: `user123`

---

### Task 6: Isi Hero Panel Register (Slide 2 — Bagian Kiri)

**Apa yang harus dilakukan:**

Sama seperti hero login, tapi:
- **Judul:** "Selamat Datang Di LiBooks"
- **Deskripsi:** "Daftar dan dapatkan akses ke ribuan koleksi buku tanpa batas!"
- **Tombol:** = <button onclick="slideToLogin()" id="btn-prev" aria-label="Sebelumnya" 
          class="absolute -left-6 md:-left-10 top-1/2 -translate-y-1/2 z-20
                 w-12 h-12 rounded-full bg-primary text-white
                 flex items-center justify-center
                 shadow-lg hover:bg-amber-700 transition-all active:scale-95">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </button> 
— Onclick: geser kembali ke login.

---

### Task 7: JavaScript — Animasi Slide & Toggle Password

**Apa yang harus dilakukan:**

Tambahkan script di bawah `</body>`:

```javascript
// ===== SLIDE LOGIN ↔ REGISTER =====
function slideToRegister() {
  document.getElementById('auth-track').style.transform = 'translateX(-50%)';
}

function slideToLogin() {
  document.getElementById('auth-track').style.transform = 'translateX(0)';
}

// ===== TOGGLE PASSWORD VISIBILITY =====
function togglePassword(inputId, btn) {
  const input = document.getElementById(inputId);
  const isPassword = input.type === 'password';
  input.type = isPassword ? 'text' : 'password';

  // Ganti icon mata (terbuka ↔ tertutup)
  btn.innerHTML = isPassword
    ? `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
           d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7
              a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878
              l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
       </svg>`
    : `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
           d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
           d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7
              -1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
       </svg>`;
}

// ===== HANDLE LOGIN =====
function handleLogin(e) {
  e.preventDefault();
  const email = document.getElementById('login-email').value;
  const password = document.getElementById('login-password').value;

  if (email === 'admin@libooks.com' && password === 'admin123') {
    alert('Login berhasil! Selamat datang 👋');
    window.location.href = 'index.html';
  } else {
    alert('Email atau password salah!');
  }
}

// ===== HANDLE REGISTER =====
function handleRegister(e) {
  e.preventDefault();
  const name = document.getElementById('reg-name').value;
  const email = document.getElementById('reg-email').value;
  const password = document.getElementById('reg-password').value;
  const confirm = document.getElementById('reg-confirm').value;

  if (!name || !email || !password) {
    alert('Semua field harus diisi!');
    return;
  }
  if (password !== confirm) {
    alert('Password dan konfirmasi tidak cocok!');
    return;
  }
  alert('Registrasi berhasil! Silakan login 🎉');
  slideToLogin();
}
```

**Catatan:**
- `transition-transform duration-700 ease-in-out` pada `#auth-track` sudah diset di HTML, jadi pergeseran akan otomatis smooth.
- Fungsi `togglePassword` menerima ID input dan element button, lalu toggle antara `type="password"` dan `type="text"`.

---

### Task 8: CSS Tambahan & Animasi

**Apa yang harus dilakukan:**

Tambahkan di dalam `<style>`:

```css
/* Animasi smooth untuk track */
#auth-track {
  transition: transform 0.7s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Input focus effect */
input:focus {
  border-color: #A86E43;
  box-shadow: 0 0 0 3px rgba(168, 110, 67, 0.15);
}

/* Tombol hover scale */
button[type="submit"]:hover {
  transform: scale(1.02);
}

/* Divider "atau" */
.divider {
  display: flex;
  align-items: center;
  gap: 12px;
}
.divider::before, .divider::after {
  content: "";
  flex: 1;
  height: 1px;
  background: #E0E0E0;
}
```

---

### Task 9: Responsive Mobile (< 768px)

**Apa yang harus dilakukan:**

Pada mobile:
- Panel hero **disembunyikan** (`hidden md:flex`).
- Form login dan register masing-masing tampil full-width.
- Container tidak perlu `rounded-[40px]` besar — bisa pakai `rounded-3xl`.
- Padding form dikurangi jadi `p-6`.

**Yang sudah otomatis ter-handle:**
- `hidden md:flex` pada panel hero sudah menyembunyikan gambar di mobile.
- Track tetap berfungsi — mobile hanya melihat form saja.

**Cek lagi:**
- Pastikan tombol "Belum punya akun? Daftar" dan "Sudah punya akun? Masuk" **tetap berfungsi** di mobile sebagai pengganti tombol Next/Back di panel hero.
- Pastikan tidak ada horizontal scroll.

---

### Task 10: Hubungkan ke Homepage

**Apa yang harus dilakukan:**

Di `index.html`, update link Login dan Sign Up di navbar:

```html
<!-- Sebelum -->
<a href="#">Login</a>
<a href="#">Sign Up</a>

<!-- Sesudah -->
<a href="login.html">Login</a>
<a href="login.html#register">Sign Up</a>
```

Di `login.html`, tambahkan logic untuk auto-slide ke register kalau URL punya hash `#register`:

```javascript
// Auto-slide jika URL mengandung #register
window.addEventListener('DOMContentLoaded', () => {
  if (window.location.hash === '#register') {
    slideToRegister();
  }
});
```

---

## ✅ Checklist Sebelum Selesai

- [ ] File `login.html` sudah dibuat dan bisa diakses
- [ ] Form login tampil dengan benar (email, password, icon mata, tombol masuk)
- [ ] Form register tampil dengan benar (nama, email, password, konfirmasi, icon mata)
- [ ] Animasi slide Login ↔ Register berjalan smooth
- [ ] Login default berfungsi: `admin@libooks.com` / `admin123`
- [ ] Register default berfungsi: `User LiBooks` / `user@libooks.com` / `user123`
- [ ] Tombol "Login dengan Google" ada (UI saja, belum fungsional)
- [ ] Link "Lupa Password?" ada (UI saja, belum fungsional)
- [ ] Toggle password (icon mata) berfungsi di semua field password
- [ ] Responsive mobile: hero tersembunyi, form full-width
- [ ] Tidak ada horizontal scroll di mobile
- [ ] Link Login/Sign Up di `index.html` mengarah ke `login.html`

---

## 📝 Catatan untuk Developer

1. **Jangan ubah warna!** Tetap pakai `#A86E43` (primary), `#E0E0E0` (secondary), `#F5F5F5` (accent).
2. **Jangan ubah font!** Tetap pakai `Outfit` dari Google Fonts.
3. **File `hero.png`** sudah ada di root folder, tinggal pakai `src="hero.png"`.
4. **Tailwind CSS** pakai CDN, sama persis seperti di `index.html`.
5. **Ini masih frontend only** — login/register pakai `alert()` dan hardcoded credentials. Backend nanti menyusul.
6. **Jangan lupa test** di Chrome DevTools → Toggle Device → iPhone SE (375px) untuk cek mobile.
