# Frontend Review Checklist

Dokumen ini adalah *checklist* yang digunakan saat Code Review (Sprint Review) sebelum sebuah komponen, halaman, atau fitur UI dinyatakan selesai (PASS). *Reviewer* (baik developer maupun desainer) harus memeriksa poin-poin berikut:

### I. Component & Code Architecture
- [ ] Seluruh UI dibangun menggunakan *Blade Component* (`x-*`). Tidak ada HTML primitif berulang.
- [ ] *Component* mendukung atribut eksternal (`class`, `id`, `x-data`) dengan `$attributes->merge()`.
- [ ] Komponen fungsional dan terisolasi dari *business logic* spesifik modul.
- [ ] Tidak ada penggunaan *hardcoded HEX color* (`#123456`) atau *inline styling* (`style="..."`), semuanya memanggil *Design Token* Tailwind.

### II. Visual Hierarchy & Estetika
- [ ] Keseimbangan visual: Tidak berat sebelah, *layout* proporsional.
- [ ] Tipografi: Menggunakan kelas ukuran font standar (tidak ada nilai *arbitrary* seperti `text-[17px]`).
- [ ] Irama Spasi (Spacing Rhythm): Menggunakan skala spasi Tailwind standar (`p-4`, `gap-2`, `mt-6`) tanpa nilai serampangan (`mt-[13px]`).
- [ ] *Alignment*: Semua teks, *input*, tombol, dan ikon sejajar secara vertikal/horizontal dengan tepat.

### III. UI/UX & Cognitive Load
- [ ] Judul halaman adalah elemen teks terbesar.
- [ ] Hanya terdapat maksimal 1 (satu) *Primary Button* yang mencolok di layar.
- [ ] Tombol aksi berurutan konsisten (misal *Row Actions*: View ➔ Edit ➔ Delete).
- [ ] Kejelasan ikon: Ikon dilengkapi label (*tooltip* atau *aria-label*) jika tidak memiliki teks.
- [ ] Navigasi tidak mematikan konteks (menggunakan breadcrumb dan *sidebar active state*).

### IV. Status, States & Error Recovery
- [ ] **Empty State**: Tabel atau *list* memunculkan ilustrasi/pesan kosong saat tidak ada data.
- [ ] **Loading State**: Tombol aksi dikunci (*disabled*) dan memunculkan *spinner/skeleton* saat operasi asinkron/berjalan.
- [ ] **Error Recovery**: Jika gagal submit form: data isian tetap ada (tidak hilang), *cursor* bisa kembali fokus di *field* yang salah, tidak me-*reload* halaman secara kasar (SPA/Livewire feel), dan pesan *error* sangat jelas.
- [ ] **Feedback**: Sistem menampilkan notifikasi *Toast* (sukses/gagal) setelah tindakan *create/update/delete/restore* selesai.

### V. Enterprise Readability & Accessbility
- [ ] Kontras warna terpenuhi (tidak ada teks abu-abu terang di atas putih).
- [ ] Indikator status (Aktif/Nonaktif) menggunakan warna DAN teks/ikon, bukan hanya warna bulat.
- [ ] **Keyboard Navigasi**: Tab memindahkan kursor secara berurutan dan elemen yang disorot memiliki *focus ring* yang jelas.
- [ ] *Touch target* cukup besar pada versi *Mobile*.

### VI. Responsive Design
- [ ] Aman diakses dari Desktop (>1024px) dengan *layout grid/sidebar*.
- [ ] Tabel data tidak hancur atau bertumpuk paksa di Tablet (768px), memunculkan *scroll* horizontal atau tata letak menyesuaikan.
- [ ] Navigasi *mobile* tidak menutupi konten utama (*Collapsible* menu).

### VII. Performance & Execution
- [ ] Tidak ada eksekusi *database query* langsung di dalam file *view* Blade.
- [ ] Bebas lompatan *layout* (CLS) saat elemen asinkron/gambar dimuat.
- [ ] Tidak ada *nested component* berlebihan yang tidak perlu.

### VIII. Design Debt
- [ ] Tidak ada anotasi `TODO UI` atau `FIXME UI` yang ditinggalkan tanpa tiket *issue* pendamping.
- [ ] Tidak ada *Hardcode* CSS sementara atau *inline styles* yang tidak sesuai standar. (Segera selesaikan sebelum *merge*!).

### IX. UI Regression
- [ ] Perubahan pada komponen fundamental tidak merusak halaman lain (Contoh: `Button`, `Card`, `Spacing`, `Typography`, atau `Shadow` tetap konsisten di seluruh modul yang ada).

**Jika semua kotak dapat dicentang, fitur ini siap di-*merge* ke cabang utama dan masuk ke fase produksi/testing lebih lanjut.**

### X. Living Documentation Rule
- [ ] Komponen baru (jika ada) TELAH didaftarkan di halaman Gallery/Playground (`/dev/components`) beserta semua variasi state-nya (ukuran, warna, disabled, dsb). Tidak boleh me-merge komponen yang tidak terdokumentasi secara visual.
