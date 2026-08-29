# Laporan Architecture Review - Sprint 2B: Student Management UI

## 1. Tujuan Sprint
Sprint 2B bertujuan untuk mengembangkan User Interface (UI) pengelolaan data siswa (Student Management) menggunakan standard Blade Templates, Tailwind CSS, dan Alpine.js. Pengembangan ini diintegrasikan langsung dengan `StudentController` yang telah ada tanpa melakukan perubahan pada layer service, repository, dan business logic backend, melainkan dengan memodifikasi controller untuk mendukung respons berbasis view (HTML) sekaligus tetap mempertahankan kompatibilitas API JSON (menggunakan `wantsJson()`).

## 2. File Inventory
Daftar file yang diubah dan dibuat pada sprint ini:

### Controllers & Routes
- `app/Modules/Student/Routes/web.php` (Dimodifikasi: penambahan route `create` dan `edit`)
- `app/Modules/Student/Controllers/StudentController.php` (Dimodifikasi: mendukung pengembalian view Blade dan implementasi pagination + filter dasar)

### Komponen Blade (Shared Components)
- `resources/views/components/alert.blade.php` (Baru): Menampilkan flash messages (success/error).
- `resources/views/components/form-input.blade.php` (Baru): Komponen input form yang mendukung validasi error dan old input.
- `resources/views/components/confirm-modal.blade.php` (Baru): Modal konfirmasi destruktif (delete) berbasis Alpine.js.
- `resources/views/components/sidebar.blade.php` (Dimodifikasi: mengaktifkan URL untuk menu Siswa ke `students.index`).

### Views Utama (Student Management)
- `resources/views/students/index.blade.php` (Baru): Halaman utama (Tabel daftar siswa, pencarian, filter status, paginasi).
- `resources/views/students/form.blade.php` (Baru): Form input yang di-share untuk halaman Create dan Edit.
- `resources/views/students/create.blade.php` (Baru): Halaman tambah data siswa.
- `resources/views/students/edit.blade.php` (Baru): Halaman ubah data siswa.
- `resources/views/students/show.blade.php` (Baru): Halaman detail profil siswa, informasi akademik, dan wali murid.

## 3. Architecture Decisions
- **Content Negotiation di Controller**: Modifikasi pada `StudentController` tidak merusak endpoint API yang ada karena menggunakan pengecekan `$request->wantsJson()`. Jika request meminta JSON, respons tetap seperti semula (`JsonResponse`). Jika tidak, dikembalikan View Blade.
- **Frontend Interactivity**: Validasi konfirmasi penghapusan (Delete) ditangani penuh menggunakan Alpine.js event dispatching (`@open-confirm-modal.window`), memastikan pengalaman UI yang cepat tanpa request tambahan.
- **Form Reusability**: Penggunaan `students.form` memastikan layout input standar (DRY principle) untuk operasi pembuatan dan perubahan data.
- **UI RBAC Protection**: 
  - Tautan di Sidebar dibungkus menggunakan directive `@can('student.view')`.
  - Tombol-tombol aksi (Tambah, Ubah, Hapus) pada indeks dibungkus masing-masing menggunakan pengecekan otorisasi seperti `@can('create', ...)` atau `@can('delete', $student)`.

## 4. RBAC UI Mapping (Siswa)
Fitur telah disesuaikan agar menampilkan tombol sesuai dengan permissions:
- **Index/Tabel**: Terlindungi oleh `Gate::authorize('viewAny')`.
- **Tombol Tambah**: Ditampilkan jika memiliki `student.create`.
- **Tombol Detail**: Ditampilkan jika memiliki `student.view`.
- **Tombol Ubah**: Ditampilkan jika memiliki `student.update`.
- **Tombol Hapus**: Ditampilkan jika memiliki `student.delete`.
- **Tombol Pulihkan (Restore)**: Ditampilkan jika memiliki `student.restore` (dan siswa sedang tidak aktif).

## 5. Technical Debt
- **Filtering Ekstensif**: Proses pencarian dan filter saat ini dilakukan pada tingkat controller menggunakan query builder model secara langsung. Sebaiknya abstraksi pencarian (Criteria atau Scope) diletakkan di Service/Repository layer di kemudian hari agar konsisten dengan endpoint API.
- **Asset/Styles**: Alpine.js masih bergantung pada CDN.
- **Select Option Komponen**: Option agama/jenis kelamin bersifat hardcode di dalam Blade; pada rilis masa depan sebaiknya dimuat dari tabel referensi master (database) atau Enums.

## 6. Sprint Exit Criteria
- [x] Tabel siswa dengan pagination bawaan Laravel.
- [x] Fitur search dan filter (Aktif/Tidak Aktif) berfungsi.
- [x] Terdapat modal peringatan hapus berbasis Alpine.js.
- [x] Mendukung validasi input dengan display pesan error (warna merah dan old input retention).
- [x] Tampilan Profil Siswa yang bersih (termasuk status wali murid dan kelas).
- [x] Dokumentasi Architecture Review untuk Sprint 2B tersimpan.

## 7. Backlog Sprint 2C (Guardian & Academic Setup UI)
Fokus berikutnya adalah membangun UI untuk Wali Murid dan Setup Akademik:
1. **Wali Murid (Guardian UI)**:
   - Membuat `guardians/index.blade.php`, `form.blade.php`, `create.blade.php`, `edit.blade.php`, `show.blade.php`.
   - Menghubungkan wali murid ke UI siswa (fitur Add Guardian dari profil Siswa).
2. **Pengaturan Akademik**:
   - UI Tahun Ajaran (Academic Year) dan Semester (aktifasi semester).
   - UI Manajemen Kelas (Class Management) beserta UI penempatan siswa ke dalam kelas (Rombel).
3. **Form Refinements**:
   - Komponen Select dengan pencarian (Searchable Select) jika entitas berhubungan jumlahnya besar.
