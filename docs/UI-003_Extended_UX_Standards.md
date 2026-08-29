# UI-003: Extended UX Standards & Component Lifecycle Policy

Dokumen ini mendefinisikan standar tambahan (Extended UX Standards) dan kebijakan siklus hidup komponen (Component Lifecycle Policy) untuk memastikan konsistensi antarmuka pengguna pada proyek SIAM.

## 1. Component Lifecycle Policy
Aturan kapan dan bagaimana sebuah komponen boleh berubah.

*   **Stable**: Komponen yang sudah di-freeze dan digunakan di produksi. Tidak boleh ada perubahan *breaking* pada prop API.
*   **Deprecated**: Komponen yang akan dihilangkan di versi mayor berikutnya. Harus diberi warning (misal via console atau komentar kode) dan disediakan alternatifnya.
*   **Versioning / Breaking Change**: Jika sebuah komponen memerlukan perubahan drastis pada API-nya (misal mengganti prop `color` menjadi `variant`), komponen tersebut harus dibuatkan versi baru (misal `x-button-v2`), atau perubahannya ditunda hingga iterasi versi mayor. Komponen eksisting tidak boleh langsung diubah jika mematahkan implementasi di ratusan tempat.

## 2. State Standard
Standar penanganan state global pada antarmuka.

*   **Loading State**: Harus ada umpan balik visual (spinner, skeleton loading) saat proses asinkron (misal: submit form, fetch data).
*   **Disabled State**: Elemen interaktif harus diredupkan (`opacity-50`) dan kursor diubah (`cursor-not-allowed`) ketika tidak dapat diakses.
*   **Error/Validation State**: Kesalahan form harus ditandai dengan warna `danger` (border dan text) beserta pesan kesalahan yang jelas di bawah input.
*   **Success State**: Umpan balik positif (warna `success`, ikon centang) saat tindakan berhasil diselesaikan.

## 3. Notification Standard
Standar penggunaan notifikasi kepada pengguna.

*   **Toast (`x-toast`)**: Untuk notifikasi sementara yang tidak memblokir antarmuka (misal: "Data berhasil disimpan"). Auto-dismiss dalam waktu singkat (contoh: 3-5 detik).
*   **Alert / Banner**: Untuk peringatan yang perlu tetap terlihat di halaman (misal: "Akun belum diaktifkan").
*   **Modal Dialog (`x-modal`)**: Untuk aksi destruktif atau penting yang memerlukan konfirmasi pengguna (misal: "Hapus data ini?"). Memblokir antarmuka hingga pengguna merespons.

## 4. Empty State Library
Standar penggunaan *Empty State* ketika tidak ada data.

*   **Data Kosong / Belum Ada Data**: Menampilkan ilustrasi/ikon netral, judul yang jelas ("Belum ada data siswa"), deskripsi bantuan, dan tombol Call-to-Action ("Tambah Siswa").
*   **Hasil Pencarian Tidak Ditemukan**: Menampilkan ikon pencarian silang/kaca pembesar, teks yang mengindikasikan pencarian gagal, dan opsi untuk "Reset Filter" atau mengubah kata kunci.
*   **Error State Kosong**: Untuk kegagalan memuat data akibat kesalahan jaringan atau server. Harus memberikan tombol "Coba Lagi".

## 5. Icon Guideline
*   **Library Utama**: Semua ikon harus berasal dari sistem ikon yang disepakati (misalnya Blade UI Icons via `heroicons`).
*   **Implementasi**: Gunakan format komponen `<x-heroicon-o-* />` alih-alih inline SVG secara manual untuk menjaga kebersihan kode dan memudahkan penggantian massal.
*   **Ukuran Standar**: `h-5 w-5` untuk ikon dalam tombol/teks, `h-6 w-6` untuk header/sidebar, `h-12 w-12` untuk empty state.

## 6. Animation Standard
*   **Durasi (Duration)**: Gunakan `duration-100` atau `duration-200` untuk interaksi UI kecil (hover, focus, modal buka). Maksimal `duration-300`.
*   **Easing**: Gunakan `ease-out` untuk elemen yang masuk ke layar, `ease-in` untuk elemen yang keluar dari layar.
*   **Konsistensi**: Jangan menggunakan animasi berlebihan. Batasi pada transisi warna (hover), perubahan posisi ringan (transform scale untuk tombol), dan modal drop-in.

## 7. Responsive Breakpoint
Standar baku *responsive breakpoint* Tailwind CSS untuk SIAM:

*   **Mobile (`< sm` / `< 640px`)**: Satu kolom (`col-span-1`). Tampilan navigasi menggunakan hamburger menu. Padding disesuaikan lebih sempit (`p-4`).
*   **Tablet (`sm` hingga `md` / `640px` - `768px`)**: Layout dua kolom mulai diterapkan pada *grid*.
*   **Desktop (`lg` / `1024px` ke atas)**: Layout penuh. Sidebar navigasi muncul secara persisten. Padding dan jarak dimaksimalkan (`p-6` atau `p-8`).

## 8. Table UX Standard
*   **Scroll Horizontal**: Tabel yang panjang harus selalu dibungkus dengan container horizontal overflow (`overflow-x-auto`) agar tidak merusak layout halaman di layar kecil.
*   **Aksi (Action Column)**: Kolom aksi diletakkan di sisi paling kanan tabel. Gunakan tombol berukuran kecil (`size="sm"`) atau sekadar ikon.
*   **Pagination & Info**: Harus ada keterangan posisi paginasi (misal: "Menampilkan 1 hingga 10 dari 50 data") di bagian bawah tabel.

## 9. Form UX Standard
*   **Label & Placeholder**: Setiap input harus memiliki label yang eksplisit. Placeholder hanya sebagai petunjuk format, tidak menggantikan label.
*   **Group / Layout**: Form yang kompleks harus dibagi menjadi *section* atau *card* terpisah dengan judul per bagian (misal: "Data Pribadi", "Informasi Akademik").
*   **Aksi Simpan**: Tombol "Simpan" harus berada di bagian paling bawah atau pada bagian *footer* dari Modal/Card, selalu gunakan posisi kanan (Right-aligned) dengan opsi "Batal" di sebelah kirinya.

## 10. Print Layout Standard
Standar khusus untuk halaman yang akan dicetak (Kwitansi, Laporan, Surat).

*   **CSS Print Query (`@media print`)**: Halaman print harus menyembunyikan sidebar, topbar, dan tombol aksi (`.no-print`).
*   **Warna & Background**: Mematikan warna latar (`bg-transparent`) dan mengatur agar semua teks berwarna hitam (`text-black`) demi penghematan tinta dan kejelasan.
*   **Ukuran Kertas**: Menyesuaikan layout untuk proporsi kertas A4 potret atau lanskap secara default (menggunakan `print:w-[210mm]`).
