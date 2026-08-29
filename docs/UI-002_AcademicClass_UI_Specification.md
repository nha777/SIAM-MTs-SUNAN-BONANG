# UI-002: Academic Class UI Specification

## 1. Halaman List (Daftar Rombongan Belajar)
Halaman utama (index) untuk modul Kelas/Rombel.

### Layout
*   **Header**: Judul "Rombongan Belajar", *breadcrumbs* (SIAM > Akademik > Rombongan Belajar), dan *Primary Button* "Tambah Rombel" di pojok kanan atas.
*   **Toolbar**: Berada di bawah header, berisi kolom pencarian (kiri) dan filter (kanan).
*   **Content**: Tabel Data Grid.

### Filter & Search
*   **Search**: Input teks untuk mencari berdasarkan Nama Kelas (misal: "VII A").
*   **Filter**: Dropdown untuk memilih *Tahun Ajaran* (Default: Tahun ajaran aktif saat ini), Dropdown tingkat/Grade (Semua, VII, VIII, IX).

### Data Table
Kolom (berurutan dari kiri ke kanan):
1.  **Nama Kelas**: (misal: VII A) - Ditampilkan tebal (*semibold*).
2.  **Tahun Ajaran**: (misal: 2026/2027)
3.  **Tingkat**: Angka romawi/grade.
4.  **Kapasitas**: Jumlah kursi (misal: 32).
5.  **Status**: Badge Aktif.
6.  **Aksi**: Tombol Edit (Ikon Pensil), Detail (Ikon Mata), Hapus (Ikon Tempat Sampah - Danger).

### Pagination & Empty State
*   **Pagination**: Standar 15 baris per halaman.
*   **Empty State**: Ilustrasi bangku kosong. Teks "Belum ada Rombel di Tahun Ajaran ini", tombol "Buat Rombel Pertama".

### Responsive
*   Pada Mobile, tabel memiliki scroll horizontal, atau direpresentasikan dalam bentuk List Card dengan Nama Kelas sebagai judul *Card*.

## 2. Halaman Create (Tambah Rombel Baru)
Biasanya ditampilkan sebagai *Slide-over* (Drawer di kanan) atau *Modal*, tidak perlu pindah halaman penuh, agar operator tidak kehilangan konteks *List*. Jika form panjang, pindah ke halaman `/create`. Mengingat *AcademicClass* sederhana, modal/slide-over lebih disarankan, tetapi halaman penuh juga bisa diterima.

### Urutan Field
1.  **Tahun Ajaran (Select)**: Default terisi tahun ajaran aktif. Wajib.
2.  **Tingkat / Grade (Select)**: Pilihan 7, 8, atau 9. Wajib.
3.  **Nama / Grup (Input Text)**: Untuk mengetik "A", "B", "C", atau nama khusus (misal "Unggulan"). Wajib. Maksimal 20 karakter.
4.  **Kapasitas (Input Number)**: Default 32. Wajib.
5.  **Urutan Tampilan / Display Order (Input Number)**: Opsional.

### Validation UX & Cancel Flow
*   Pesan validasi langsung (inline) di bawah input (misal saat nama rombel sudah ada di tahun ajaran yang sama).
*   Tombol "Batal" menutup form (Secondary), tombol "Simpan" (Primary) memanggil aksi penyimpanan dengan transisi tombol *Loading*.

## 3. Halaman Edit
Formulirnya identik dengan form Create.
### Perubahan Field
*   Semua *field* bisa diubah, namun **Tahun Ajaran** mungkin harus diproteksi dengan konfirmasi tambahan apabila kelas tersebut sudah memiliki siswa terdaftar (Enrollment di masa depan).

## 4. Halaman Detail
Menampilkan rincian komprehensif dari sebuah rombel.
*   **Informasi yang ditampilkan**: Nama Rombel, Tahun Ajaran, Kapasitas, dan (nantinya) Nama Wali Kelas.
*   **Quick Action**: Tombol cetak daftar hadir kosong, cetak label absensi.
*   **Audit Information**: Footer kecil "Dibuat oleh [User] pada [Tanggal]", "Terakhir diubah oleh [User]".

## 5. Halaman Trash (Tong Sampah)
Halaman terpisah untuk melihat rombel yang dihapus (Soft Delete).
*   **Daftar Data**: Mirip tabel utama, tetapi menambahkan kolom "Waktu Dihapus".
*   **Restore**: Tombol *Undo* pada *Row Action* untuk mengembalikan data (memanggil permission `class.restore`).
*   **Permanent Delete (Force Delete)**: Saat ini belum diizinkan (future implementation).

## 6. UX Flow (Operator Menambahkan Kelas)
1. Operator TU membuka halaman Rombel.
2. Secara *default*, sistem memfilter dan menampilkan daftar Rombel untuk *Tahun Ajaran Aktif*.
3. Operator menyadari rombel "VII C" belum ada, menekan tombol **"Tambah Rombel"**.
4. Form muncul. Sistem otomatis menetapkan Tahun Ajaran saat ini. Operator memilih Grade "7", mengetik nama "C", dan menyimpan.
5. Permintaan dikirim. Jika validasi *business unique* gagal (ternyata VII C sudah dibuat orang lain), *inline error* muncul, form tidak tertutup.
6. Jika berhasil, form tertutup, Toast "Kelas VII C berhasil dibuat" muncul.
7. Tabel utama me-*refresh* diri (atau melakukan optimistik *append* baris baru) tanpa memuat ulang seluruh halaman penuh.

## 7. Future Integration (Hubungan antar Entitas)
Spesifikasi ini dirancang sebagai titik awal untuk fitur-fitur kompleks mendatang:
*   **Student Enrollment**: Halaman Detail Rombel nantinya akan menampilkan Tabel Daftar Siswa yang sedang menghuni kelas ini di semester aktif.
*   **Homeroom Teacher**: Penugasan wali kelas akan ditempatkan di halaman Detail atau Edit Rombel.
*   **Attendance & Report Card**: Daftar hadir per hari akan menjadikan data rombel (dan siswanya) sebagai dasar absensi dan rapor.

## 8. Design Decision Record
Beberapa keputusan UX krusial untuk dipahami oleh semua *developer*:

1.  **Mengapa Filter diletakkan di Toolbar Atas?**
    *Alasan*: Mengelompokkan semua kontrol data (Pencarian, Filter, Opsi Halaman) dalam satu area horizontal (Toolbar) membuat mata tidak perlu melompat-lompat mencari fungsi. Area konten di bawah murni untuk Data Grid.
2.  **Mengapa Action selalu di Kolom Terakhir Kanan?**
    *Alasan*: Ini mengikat aksi ke *row* secara tegas. Operator membaca data dari kiri (mengidentifikasi objek), memverifikasi detail di tengah, dan memutuskan aksi di akhir alur baca (kanan).
3.  **Mengapa Kapasitas dimunculkan di List View?**
    *Alasan*: Untuk keperluan Penerimaan Peserta Didik Baru (PPDB) atau Mutasi, operator bisa dengan cepat (sekilas) mengetahui batas kelas mana yang berpotensi penuh tanpa masuk ke Halaman Detail.
4.  **Mengapa Tombol Restore tidak digabung di List View utama (di-hidden/toggle)?**
    *Alasan*: Menjaga daftar data utama tetap "bersih" dari data yang dihapus (yang secara logika sudah dianggap hilang). Menu "Trash" tersendiri (diakses dari submenu atau ikon tempat sampah di sudut atas) memisahkan secara mental antara data aktif dan data arsip/sampah.
