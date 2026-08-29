# Laporan Architecture Review - Sprint 2A: Dashboard UI Foundation

## 1. Tujuan Sprint
Membangun kerangka dasar User Interface (UI) untuk proyek SIAM (Sistem Informasi Akademik Madrasah) tanpa mengubah atau mengganggu backend business logic yang telah stabil pada fase sebelumnya. Kerangka ini mengambil inspirasi layout dan navigasi dari pola AdminLTE v4, namun diimplementasikan sepenuhnya secara custom menggunakan stack modern yang ringan (Tailwind CSS, Alpine.js, Blade Templates) untuk menghasilkan antarmuka yang bersih, profesional, dan responsif.

## 2. File Inventory
Daftar file yang telah dibuat selama Sprint 2A:

- `resources/views/layouts/app.blade.php`: Master layout aplikasi (struktur HTML, asset loading, tata letak utama).
- `resources/views/components/topbar.blade.php`: Komponen navigasi atas (hamburger menu, title, profil pengguna, role aktif, logout).
- `resources/views/components/sidebar.blade.php`: Komponen navigasi kiri modular dengan dukungan menu accordion (Dashboard, Master Data, Akademik, Administrasi).
- `resources/views/dashboard/index.blade.php`: Halaman dashboard awal dengan placeholder statistik akademik (Total Siswa, Total Wali Murid, Total Kelas, Tahun Ajaran Aktif).

**Struktur Direktori Blade:**
```text
resources/
└── views/
    ├── components/
    │   ├── sidebar.blade.php
    │   └── topbar.blade.php
    ├── dashboard/
    │   └── index.blade.php
    └── layouts/
        └── app.blade.php
```

## 3. Architecture Decisions
- **Strict UI-Only Scope**: Sprint ini murni berfokus pada presentation layer. Tidak ada perubahan yang dilakukan pada Service Layer, Repository Layer, Audit Trail, Migration, maupun business rules backend.
- **Tech Stack & Tooling**:
  - Menggunakan standar **Blade Templates** Laravel untuk server-side rendering.
  - Menggunakan **Tailwind CSS** via CDN (fallback) atau Vite build untuk styling utilitas.
  - Menggunakan **Alpine.js** (CDN) untuk interaktivitas komponen klien (misal: toggle sidebar, dropdown menu pengguna, dan accordion navigasi) guna menghindari keharusan menggunakan kerangka JavaScript berat seperti React/Vue.
- **Visual Design Rules**: Desain menerapkan filosofi bersih dan terang (clean & white). Tidak menggunakan warna-warna mencolok atau dark mode, memprioritaskan keterbacaan, white space yang cukup, dan kesesuaian dengan citra institusi pendidikan madrasah.

## 4. RBAC UI Mapping
Penerapan Role-Based Access Control (RBAC) pada antarmuka, secara khusus pada navigasi menu utama di `sidebar.blade.php`, dipetakan menggunakan fungsionalitas Spatie Permission (`@can`) dan pemeriksaan role (`hasRole`):

- **Master Data**:
  - *Siswa*: Membutuhkan permission `student.view`.
  - *Wali Murid*: Membutuhkan permission `guardian.view`.
- **Akademik**:
  - *Tahun Ajaran*: Membutuhkan permission `academic_year.view`.
  - *Semester*: Membutuhkan permission `semester.view`.
  - *Kelas*: Membutuhkan permission `class.view`.
- **Administrasi**:
  - Seluruh modul administrasi (Audit Log, Pengaturan) hanya diizinkan untuk user yang secara eksplisit memiliki role `'Super Admin'`.

## 5. Component Inventory
- **Layout App (`layouts.app`)**: Memuat wrapper utama dengan model tata letak Flexbox (layar penuh, non-scrollable body, sidebar tetap/off-canvas, dan konten yang dapat di-scroll).
- **Topbar (`components.topbar`)**: Menampilkan *hamburger button* untuk mode mobile, judul halaman via `@yield`, label *Role Badge* dinamis, dan *User Dropdown* interaktif dengan Alpine.js (menampilkan avatar inisial, nama, email, dan tombol form logout).
- **Sidebar (`components.sidebar`)**: Sidebar navigasi yang bersifat off-canvas pada perangkat mobile dan menetap pada desktop. Menu dikelompokkan ke dalam struktur akordeon Alpine.js dengan ikon dari library standar (Lucide / SVG inline) dan pengecekan otorisasi yang terintegrasi.
- **Dashboard Metrics Card**: Kerangka card sederhana untuk menampilkan 4 nilai indikator (saat ini sebagai nilai dummy/statik) dengan padding, layout flex, dan ikon SVG spesifik untuk menonjolkan fungsi statistik dasar.

## 6. Technical Debt
- **Asset Pipeline**: Pemanggilan CDN untuk Tailwind CSS dan Alpine.js masih dipasang sebagai fallback/sementara. Sebaiknya segera diintegrasikan sepenuhnya ke dalam asset bundler lokal (`npm run build` / Vite) sebelum rilis ke production.
- **Static Assets & Icons**: SVG icon dimasukkan secara hardcode (inline). Seiring berkembangnya UI, sebaiknya dipertimbangkan penggunaan library icon komponen blade (misal: Blade UI Kit / Blade Icons) untuk pemeliharaan yang lebih mudah.
- **Logout Action**: Rute logout `{{ route('logout') ?? '#' }}` saat ini belum tervalidasi penuh rute web auth-nya jika setup controller otentikasi belum ada di layer HTTP Web.
- **Route Linkages**: URL pada seluruh item sidebar masih berupa stub/hash (`#`) karena modul UI pengelolanya belum dikembangkan.

## 7. Sprint Exit Criteria
- [x] Master layout dengan sidebar, topbar, konten utama, dan footer selesai.
- [x] Tata letak bersih, terang (tanpa tema gelap), dan responsif untuk mobile dan desktop.
- [x] Menu sidebar tersembunyi secara otomatis berdasarkan izin dan peran (Spatie permissions) pengguna yang login.
- [x] Sidebar menggunakan komponen Blade `x-sidebar` dan Topbar menggunakan `x-topbar`.
- [x] Halaman dashboard awal siap sebagai template placeholder tanpa mengubah business rules.
- [x] Laporan dan dokumentasi arsitektur divalidasi.

## 8. Backlog Sprint 2B (Student Management UI)
Fokus berikutnya adalah membangun antarmuka web (UI) operasional CRUD untuk pengelolaan data siswa:
1. **Routing & Web Controller**:
   - Pembuatan `StudentController` untuk menangani antarmuka HTTP dan menghubungkannya dengan `StudentService`.
   - Definisi rute: `student.index`, `student.create`, `student.store`, `student.edit`, `student.update`, `student.show`, `student.destroy`.
2. **Halaman Indeks (Daftar Siswa) - `student/index.blade.php`**:
   - Pembuatan tabel data siswa dengan dukungan paginasi dari backend.
   - Penambahan fungsionalitas filter (misal: berdasarkan status) dan pencarian sederhana.
3. **Halaman Form (Tambah/Edit) - `student/form.blade.php`**:
   - Form field dengan penerapan UI constraint (NISN, Nama, dll).
   - Menangani state *old input* dan penandaan flash error dari request validation.
4. **Halaman Detail - `student/show.blade.php`**:
   - Layar profil rinci siswa yang menampilkan informasi demografis, akademik dasar, dan data audit trail (terbatas).
5. **UI Interactivity & Feedback**:
   - Modal peringatan untuk tindakan destruktif (Alpine.js delete confirmation).
   - *Toast Notifications* global atau komponen alert blade untuk notifikasi keberhasilan operasi.
