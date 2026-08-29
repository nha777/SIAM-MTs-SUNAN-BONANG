# UI-001: SIAM Design System & Guidelines

## 1. Design Philosophy
Sistem Informasi Administrasi Madrasah (SIAM) ditujukan bagi tenaga pendidik, tata usaha, dan manajemen madrasah yang menggunakan sistem ini setiap hari dengan intensitas tinggi. Oleh karena itu, prinsip utama desain antarmukanya adalah:
*   **Clean & White**: Mengurangi beban kognitif dengan memanfaatkan ruang putih (negative space) yang lega dan latar belakang bersih agar konten (data) menjadi fokus utama.
*   **Professional & Minimal**: Menghindari elemen visual yang tidak perlu (gimmick) atau dekorasi berlebihan. Desain harus memancarkan kredibilitas institusi pendidikan formal.
*   **Fast**: Interaksi dan transisi harus terasa ringan dan seketika (snappy). Pengguna tidak boleh menunggu animasi panjang untuk tugas administratif rutin.
*   **Accessible**: Lulus standar kontras warna WCAG AA, memastikan keterbacaan yang optimal bagi pengguna dari berbagai kelompok usia (guru senior hingga operator muda).
*   **Consistent**: Elemen serupa harus berfungsi dan terlihat sama di seluruh modul untuk menekan kurva pembelajaran (learning curve).
*   **Responsive**: Mampu beradaptasi dari layar desktop kantor yang lebar, tablet saat rapat, hingga smartphone untuk pengecekan cepat.
*   **Keyboard Friendly**: Mendukung produktivitas tingkat tinggi (data entry) bagi operator TU melalui navigasi Tab, Enter, dan shortcut keyboard.

## 2. Color System
Sistem warna SIAM menggunakan pendekatan semantik, di mana fungsi lebih penting daripada hex code murni.
*   **Primary**: Warna identitas utama madrasah (biasanya varian hijau zamrud tua) yang digunakan untuk elemen aksi utama (Primary Button, Active Tab, Links).
*   **Secondary**: Warna netral (abu-abu gelap) untuk elemen sekunder (Secondary Button, subtitel, ikon pendukung).
*   **Success**: Warna hijau teduh untuk indikasi keberhasilan (Toast success, Status Aktif).
*   **Warning**: Warna kuning/oranye kecoklatan untuk peringatan (Validasi ringan, Status Draft/Pending).
*   **Danger**: Warna merah bata (tidak terlalu mencolok) untuk tindakan destruktif (Tombol Hapus, Error message, Status Dihapus).
*   **Info**: Warna biru muda untuk informasi situasional (Tooltip, Info banner).
*   **Background**: Putih absolut (`#FFFFFF`) atau abu-abu sangat terang untuk kanvas aplikasi, memastikan kontras tertinggi dengan teks.
*   **Surface**: Warna untuk card, modal, atau dropdown, sedikit berbeda dari Background untuk membedakan kedalaman (Z-axis).
*   **Border**: Abu-abu sangat terang yang halus, hanya cukup terlihat untuk membedakan batas antar kontainer tanpa mengalihkan perhatian.
*   **Text**: Hitam pudar (off-black, ex: `#111827`) untuk teks utama agar tidak membuat mata cepat lelah (eye-strain).

## 3. Typography
Menggunakan font sans-serif modern yang sangat terbaca pada ukuran kecil (misalnya Inter atau Plus Jakarta Sans).
*   **Heading**: Digunakan untuk judul halaman (H1) atau bagian utama (H2/H3). Tebal (Semibold/Bold), berwibawa, dan ringkas.
*   **Subtitle**: Mendukung Heading, memberikan penjelasan konteks. Ukuran sedikit lebih besar dari body, warna sekunder.
*   **Body**: 16px (baseline) dengan line-height 1.5, digunakan untuk teks paragraf, deskripsi form, dan konten utama.
*   **Caption**: 12px-14px untuk helper text, timestamp audit, atau informasi minor.
*   **Table**: Teks dalam tabel harus menggunakan varian angka tabular (tabular lining) agar kolom angka/nominal sejajar rapi.

## 4. Spacing System
Menggunakan skala 4px (4, 8, 12, 16, 24, 32, 48, 64) secara konsisten.
*   **Standar Margin**: Jarak antar section besar adalah 24px atau 32px.
*   **Standar Padding**: Padding dalam komponen (Card, Modal) minimum 16px, standar 24px.
*   **Standar Gap**: Jarak antar elemen dalam satu grup (misal: baris tombol, form input) adalah 8px atau 12px.
*   **Standar Card Spacing**: Antar Card diberi jarak 16px (mobile) hingga 24px (desktop).

## 5. Icon System
Ikon diambil dari set terstandar (misal: Lucide Icons) dengan ketebalan (stroke-width) yang konsisten (2px).
*   **Create**: Ikon `Plus` atau `PlusCircle`.
*   **Edit**: Ikon `Pencil` atau `PenLine`.
*   **Delete**: Ikon `Trash` atau `Trash2`.
*   **Restore**: Ikon `Undo` atau `RotateCcw`.
*   **View**: Ikon `Eye`.
*   **Search**: Ikon `Search` (Kaca pembesar).
*   **Filter**: Ikon `Filter` atau `SlidersHorizontal`.
*   **Export/Import**: Ikon `Download` dan `Upload` dengan indikator format file.
*   **Print**: Ikon `Printer`.
*   **Refresh**: Ikon `RefreshCw`.

## 6. Button Standard
Tombol adalah penggerak aksi di SIAM. Harus memiliki hit-area minimal 44px (touch-friendly).
*   **Primary Button**: Background warna Primary penuh dengan teks putih. Hanya boleh ada SATU di setiap tampilan (aksi utama).
*   **Secondary Button**: Background transparan/putih dengan border abu-abu dan teks abu-abu gelap. Untuk aksi opsional/batal.
*   **Danger Button**: Background merah (destruktif utama) atau teks/border merah (destruktif sekunder).
*   **Ghost Button**: Tanpa background dan tanpa border, muncul background saat di-hover. Digunakan untuk aksi tersier (seperti row action di tabel).
*   **Icon Button**: Tombol hanya ikon, wajib memiliki atribut aria-label atau tooltip untuk aksesibilitas.
*   **Loading Button**: Tombol berubah status menjadi disable dan memunculkan spinner (spinner menggantikan ikon atau berada di sebelah teks).
*   **Disabled Button**: Opasitas diturunkan (50%), kursor diubah ke `not-allowed`.

## 7. Badge Standard
Penyampai status secara visual dalam ruang sempit.
*   **Active**: Latar hijau pucat, teks hijau gelap.
*   **Inactive**: Latar abu-abu pucat, teks abu-abu gelap.
*   **Deleted**: Latar merah muda pucat, teks merah gelap.
*   **Draft**: Latar kuning/oranye pucat, teks oranye gelap.
*   **Archived**: Latar biru pucat, teks biru gelap.

## 8. Form Standard
Fokus pada produktivitas entri data operator.
*   **Input & Textarea**: Memiliki border yang jelas (jelas mana area yang bisa diketik). Border berubah warna (Primary) dan sedikit menebal/glow saat *focus*.
*   **Select**: Jelas bedanya dengan text input, memiliki ikon chevron di sisi kanan.
*   **Checkbox & Radio**: Berukuran cukup besar, label dapat diklik untuk men-toggle state (hit-area luas).
*   **Date Picker**: Mendukung input manual (ketik langsung) maupun pop-up kalender.
*   **Search Box**: Input teks khusus dengan ikon *search* di kiri, dan opsional tombol *clear* (x) di kanan.
*   **Validation Message**: Muncul tepat di bawah field yang bermasalah dengan warna teks merah dan (opsional) ikon alert berukuran kecil.
*   **Helper Text**: Teks penjelasan di bawah field, warna sekunder.
*   **Required Marker**: Tanda asterisk (*) berwarna merah ditempatkan setelah label form, disertai aria-required="true".
*   **Form Density**:
    *   **Desktop**: Form pendek dapat menggunakan tata letak *inline* (Label di sisi kiri, Input di sisi kanan) untuk menghemat ruang vertikal. Form kompleks menggunakan *stacked* (Label di atas Input).
    *   **Mobile**: Selalu gunakan tata letak *stacked* (Label di atas Input) untuk keterbacaan dan kemudahan layar sentuh.

## 9. Table Standard
Tabel adalah komponen yang paling sering dilihat di SIAM (Data Grid).
*   **Sorting**: Ikon panah (up/down) di samping header kolom yang mendukung pengurutan.
*   **Pagination**: Kontrol halaman di bagian bawah tabel, menahan posisi tabel agar tidak melompat.
*   **Search & Filter**: Berada di luar (atas) tabel, dibariskan horizontal (toolbar).
*   **Bulk Action**: Muncul saat checkbox baris dicentang.
*   **Row Action**: Tombol aksi per baris, diletakkan di ujung kanan (kolom terakhir). **Urutan standar**: View (Mata) ➔ Edit (Pensil) ➔ Delete (Tempat Sampah).
*   **Hover**: Latar baris tabel menggelap sedikit saat kursor berada di atasnya (untuk melacak baris di layar lebar).
*   **Striped**: Opsional, hanya jika tabel sangat lebar/banyak kolom.
*   **Sticky Header**: Header tabel tetap terlihat saat pengguna menggulir (scroll) ke bawah baris data.

## 10. Modal Standard
Digunakan untuk mempertahankan konteks pengguna di halaman saat ini (tidak memuat halaman baru).
*   **Confirmation**: Konfirmasi ya/tidak standar.
*   **Delete**: Konfirmasi destruktif, tombol aksi berwarna Danger, mengharuskan persetujuan.
*   **Restore**: Konfirmasi pengembalian data.
*   **Success & Warning**: Pesan status (biasanya digantikan oleh Toast jika informasinya tidak krusial untuk diblokir).
*   *Aturan*: Selalu memiliki tombol tutup (X) dan merespons tombol `Esc` serta klik *backdrop* luar.

## 11. Toast Notification
Feedback asinkron di sudut layar (misal: Kanan Bawah atau Kanan Atas).
*   **Success**: "Data berhasil disimpan".
*   **Error**: "Gagal menghubungi server".
*   **Info & Warning**: Pesan non-kritikal.
*   Otomatis menghilang dalam 3-5 detik, namun tidak pernah menghilang jika ada error kritis yang butuh intervensi.

## 12. Empty & Loading State
Komunikasi desain saat sistem memproses atau tidak ada data yang bisa ditampilkan.
*   **Loading Pattern**:
    *   **Skeleton**: Digunakan saat memuat halaman penuh atau *Data Grid* pertama kali. Menjaga bentuk antarmuka bayangan agar tidak terjadi *layout shift*.
    *   **Spinner**: Digunakan di dalam komponen kecil (Tombol Submit, Dropdown Async).
    *   **Progress Bar**: Digunakan untuk aksi *batch* (Bulk) atau unggah file yang terukur progress-nya.
    *   **Overlay Loading**: Latar semi-transparan dengan spinner di tengah, digunakan saat *submit* form krusial untuk memblokir interaksi ganda (mencegah double-submit).
*   **Error State**: Ilustrasi sederhana dengan tombol *Retry/Refresh*.
*   **No Data**: Pesan ramah bahwa entitas ini belum memiliki data, disertai *Primary Button* (Call to Action) untuk membuat data pertama.
*   **No Search Result**: Menginformasikan kata kunci tidak cocok, tombol *Clear Search*.

## 13. Accessibility
Aksesibilitas adalah keharusan, bukan pelengkap.
*   **Keyboard Navigation**: Seluruh elemen interaktif dapat diakses melalui tombol Tab, Spasi, dan Enter.
*   **Tab Order**: Mengikuti alur visual secara alami (kiri-kanan, atas-bawah).
*   **Focus State**: Wajib memiliki *focus ring* yang jelas (misal: ring tebal 2px warna Primary) saat dinavigasikan menggunakan keyboard. Dilarang me-remove outline tanpa memberikan pengganti visual.
*   **ARIA**: Menyertakan role dan state pada komponen khusus (seperti tabs, modal, accordions).
*   **Contrast**: Teks mematuhi rasio minimal 4.5:1 terhadap latar belakang (WCAG AA).

## 14. Responsive Breakpoint
*   **Desktop (> 1024px)**: Tata letak lengkap, multi-kolom, sidebar permanen, tabel dalam bentuk *data grid* komprehensif.
*   **Tablet (768px - 1024px)**: Kolom mulai dipersempit, tabel bisa memiliki scrollbar horizontal.
*   **Mobile (< 768px)**: Tampilan linier (satu kolom), tabel sering kali diubah menjadi tumpukan (card list), menu navigasi ditarik ke dalam *Hamburger menu* atau *Bottom bar*.

## 15. Component Library
Semua komponen yang akan digunakan (Tombol, Input, Tabel, Modal) akan diisolasi dan dibuat *reusable* (Blade Components) sehingga UI selalu seragam di tiap modul.

## 16. Navigation Pattern
Mendukung skalabilitas modul SIAM hingga puluhan menu tanpa membuat pengguna tersesat.
*   **Sidebar (Kiri)**: Navigasi utama berisi hierarki menu dan sub-menu modul. Mendukung mode *Collapsible* (menciut menjadi ikon saja) untuk memperluas area kerja (kanvas).
*   **Topbar (Atas)**: Berisi kotak pencarian global (opsional) dan elemen sekunder.
*   **Breadcrumb**: Selalu tampil di bawah Topbar / di dalam Header halaman untuk menunjukkan kedalaman navigasi (misal: *Akademik > Rombongan Belajar > Detail*).
*   **User Menu**: Berada di ujung kanan Topbar, berisi profil, pengaturan akun, dan *Logout*.
*   **Notification Area**: Ikon lonceng di Topbar untuk pusat pemberitahuan asinkron.

## 17. Design Token (Placeholder)
Untuk mempermudah implementasi CSS/Tailwind, SIAM mendefinisikan *Design Token* konseptual yang memetakan nilai konkret menjadi variabel semantik.
*   **Colors**: `primary-50` ... `primary-500`, `danger-500`, `surface-100`, `text-900`.
*   **Spacing**: `spacing-1` (4px), `spacing-2` (8px), `spacing-4` (16px).
*   **Radius**: `radius-sm` (4px), `radius-md` (6px), `radius-lg` (8px), `radius-full`.
*   **Shadows**: `shadow-sm` (tabel/card standar), `shadow-md` (dropdown), `shadow-lg` (modal).
