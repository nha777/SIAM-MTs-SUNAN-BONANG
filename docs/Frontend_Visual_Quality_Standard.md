# Frontend Visual Quality Standard

Dokumen ini adalah standar estetika, proporsi, tipografi, keseimbangan, kejelasan visual, dan keterbacaan untuk antarmuka Sistem Informasi Administrasi Madrasah (SIAM).

## 1. Visual Hierarchy ⭐⭐⭐⭐⭐
Hierarki mengatur ke mana mata pengguna akan tertuju pertama kali. Arah pandang alami haruslah:
**Judul ➔ Toolbar ➔ Table/Content ➔ Action**
*   **Judul paling dominan**: Menggunakan ukuran *Heading* terbesar di halaman.
*   **Primary Action paling mudah ditemukan**: Tombol utama memiliki warna mencolok dan kontras (hanya ada SATU di satu halaman).
*   **Secondary Action tidak mengalahkan Primary**: Tombol sekunder menggunakan *outline* atau *ghost*.
*   **Informasi penting lebih menonjol**: Menggunakan bobot font (Semibold/Bold) atau warna gelap.
*   **Informasi pendukung tidak mendominasi**: Menggunakan teks yang lebih kecil atau warna sekunder (abu-abu pudar).

## 2. Typography Scale ⭐⭐⭐⭐⭐
Skala tipografi menjaga konsistensi proporsi teks di seluruh aplikasi. **DILARANG** membuat ukuran font sembarangan (seperti 17px, 19px, 23px, 21px). Gunakan skala yang disediakan (misal: *Major Third 1.25* atau *Perfect Fourth 1.333* yang ada di Tailwind).
*   **H1**: Untuk judul halaman utama.
*   **H2 / H3**: Untuk sub-judul atau *section*.
*   **Body Large**: Untuk teks penekanan.
*   **Body**: Base ukuran teks (16px).
*   **Caption**: Teks pendukung kecil.
*   **Small**: *Helper text* form atau *timestamp* audit.

## 3. Spacing Rhythm ⭐⭐⭐⭐⭐
Semua jarak (margin, padding, gap) harus mengikuti grid matematika yang ketat.
*   **Grid Standar**: 4, 8, 12, 16, 24, 32, 48, 64.
*   **Dilarang**: Jarak ganjil/acak seperti 15px, 27px, atau 19px.

## 4. Visual Balance & Proportion ⭐⭐⭐⭐⭐
*   **Tidak berat sebelah**: Penempatan elemen mempertimbangkan keseimbangan *layout* keseluruhan.
*   **Kepadatan**: Tidak ada area yang kosong berlebihan atau terlalu padat menyulitkan pembacaan.
*   **Card & Toolbar**: Proporsional, tidak terlihat *kopong* atau terlalu sempit *padding*-nya.

## 5. Alignment ⭐⭐⭐⭐⭐
*Alignment* yang presisi membedakan desain amatir dan profesional.
*   Semua *field* sejajar rata kiri/kanan.
*   Semua *label* sejajar secara seragam.
*   Semua *tombol* dalam *toolbar* atau *form* disejajarkan rata (biasanya vertikal di *center*).
*   Ikon di dalam tombol atau tabel harus memiliki *center alignment* sempurna terhadap teks.

## 6. White Space
*   Tidak terlalu rapat (memberi ruang bernapas pada elemen).
*   Tidak terlalu renggang (sehingga kelompok informasi terpisah jauh).
*   *Grouping* yang jelas: *White space* digunakan untuk memisahkan section yang tidak berhubungan.
*   Membuat informasi mudah dipindai (*scannable*).

## 7. Cognitive Load ⭐⭐⭐⭐⭐
Beban kognitif (pikiran) pengguna harus dibuat serendah mungkin. Jangan membuat operator menebak di mana letak suatu fitur.
*   **Primary Action** langsung terlihat.
*   **Maksimal 1 CTA utama** per tampilan (jangan menjejalkan 3 tombol berwarna solid secara berdekatan).
*   **Tidak ada tombol tak penting** yang membingungkan.
*   **Informasi dikelompokkan** secara logis.

## 8. Gestalt Principle ⭐⭐⭐⭐⭐
Desain memanfaatkan hukum persepsi manusia untuk membangun struktur visual:
*   **Proximity**: Elemen yang berdekatan dianggap satu kelompok (misal: label dan inputnya).
*   **Similarity**: Elemen dengan bentuk/warna sama memiliki fungsi serupa (misal: tombol hapus semuanya merah).
*   **Continuity**: Mata diarahkan mengikuti garis tak terlihat (misal: urutan *step* atau *form*).
*   **Common Region**: Elemen di dalam satu *Card* atau dikelilingi *Border* dianggap satu kesatuan informasi.
*   **Figure-Ground**: *Modal* (Figure) tampil mencolok di atas kanvas gelap (*Overlay/Ground*).

## 9. Clarity & Scanability
Operator tidak membaca kata per kata, mereka *scanning* (memindai) halaman.
*   Mata harus bisa menemukan **Judul** dalam < 2 detik.
*   **Search & Filter** mudah dikenali bentuknya.
*   **Ikon** menggunakan metafora umum yang mudah dikenali (kaca pembesar untuk mencari, tempat sampah untuk menghapus).
*   **Label** eksplisit, tidak ambigu. Status langsung dipahami tanpa berpikir.

## 10. Information Density
Desain *Enterprise* butuh kepadatan informasi yang pas—tidak terlalu *airy* (seperti landing page) namun tidak sekacau Excel mentah.
*   Data mudah dipindai (menggunakan tabel bergaris, warna *stripes*, dsb).
*   Tidak penuh sesak.
*   Tidak terlalu banyak *whitespace* di area Data Grid.

## 11. Motion & Visual Consistency
*   **Motion**: Semua transisi, animasi *Modal*, *Toast*, *Dropdown*, dan *Loading* konsisten (durasi dan *easing*-nya sama).
*   **Visual**: *Border radius* sama (tidak ada yang membulat 16px lalu di sebelahnya kotak bersudut tajam), *shadow* konstan, *font family* tunggal, *padding* mengikuti irama yang sama.

## 12. Enterprise Readability ⭐⭐⭐⭐⭐
Standar mutlak untuk aplikasi yang digunakan 8 jam sehari oleh pengguna lintas usia.
*   Terbaca jelas dari jarak layar standar komputer meja (±60–80 cm).
*   **Tidak mengandalkan warna saja** (contoh: status "Gagal" berwarna merah juga ditambahkan ikon silang (X) atau teks "Gagal").
*   Teks angka dan tabel sejajar untuk kemudahan *skimming* (menggunakan *tabular nums*).
*   Form dibuat nyaman digunakan dalam sesi input data yang sangat panjang.
*   Kontras warna dan latar tidak menyilaukan atau cepat melelahkan mata (*low eye-strain*).

## 13. Progressive Disclosure ⭐⭐⭐⭐⭐
Prinsip mengungkapkan kompleksitas secara bertahap untuk menjaga antarmuka tetap bersih.
*   **Tampilkan yang perlu saja**: Tampilkan hanya informasi dan aksi yang benar-benar dibutuhkan saat itu.
*   **Sembunyikan sisanya**: Menu *advance*, opsi filter tambahan, atau pengaturan jarang dipakai disembunyikan di balik tombol "Lebih banyak" (More) atau ditaruh di dalam *Dropdown*/*Modal*.
*   Mencegah pengguna merasa terintimidasi saat pertama kali melihat form/halaman.

## 14. UX Response Time ⭐⭐⭐⭐⭐
Antarmuka SIAM tidak hanya harus terlihat bagus, tetapi juga terasa sangat responsif dan ringan (snappy).
*   **Button click ➔ Loading muncul**: `< 100 ms` (Instan di mata manusia).
*   **Toast notification muncul**: `< 300 ms` setelah operasi selesai.
*   **Modal terbuka**: `< 150 ms` (Cepat tapi cukup waktu bagi otak memproses transisi).
*   Jika sistem membutuhkan waktu lama untuk mengambil data, indikator *Loading/Skeleton* harus SEGERA muncul, jangan biarkan UI membeku (*freeze*).
