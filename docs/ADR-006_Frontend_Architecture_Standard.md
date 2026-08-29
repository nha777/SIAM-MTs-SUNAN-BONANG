# ADR-006: Frontend Architecture Standard

## Status
Accepted

## Context
Seiring dengan bertambahnya jumlah modul di Sistem Informasi Administrasi Madrasah (SIAM), konsistensi antarmuka tidak hanya diperlukan pada lapisan visual (UI/UX), tetapi juga pada lapisan implementasi kode (Frontend Architecture). Tanpa standar penulisan kode UI yang ketat, *developer* cenderung akan menulis ulang elemen dasar (seperti tombol, form, tabel) menggunakan tag HTML/Tailwind mentah di setiap halaman. Hal ini akan menyebabkan:
*   Inkonsistensi desain (perbedaan padding, warna, interaksi).
*   Kode *repetitive* (pengulangan kode yang panjang dan sulit dipelihara).
*   Kesulitan melakukan perubahan desain secara global (misal: mengubah *border radius* seluruh aplikasi akan mengharuskan perubahan di ribuan tempat).

Oleh karena itu, diperlukan sebuah standar arsitektur *frontend* yang mewajibkan isolasi UI ke dalam komponen-komponen yang dapat digunakan kembali (reusable components).

## Decision
1.  **Pendekatan Utama**: Seluruh UI aplikasi SIAM **WAJIB** dibangun menggunakan arsitektur **Blade Components** dari Laravel.
2.  **Mandatory Component Usage**: Pengembang **TIDAK BOLEH** merender elemen UI dasar secara mentah menggunakan HTML langsung (seperti `<button class="...">`, `<input class="...">`, atau mendesain tabel manual) jika komponen tersebut sudah tersedia dalam *Component Library*. Seluruh halaman harus dikonstruksi melalui agregasi *Blade Components*.
3.  **Konvensi Penamaan**: Seluruh *Blade Components* buatan sendiri (custom) akan diberi prefiks `x-` standar Laravel, yang mewakili desain SIAM (misal: `<x-button>`, `<x-input>`, `<x-table>`, `<x-modal>`, `<x-badge>`, dll).
4.  **Isolasi Layout dan Navigasi**: Struktur halaman *macro* seperti *Layout* Utama, *Sidebar*, *Topbar*, *Breadcrumb*, dan *Toolbar* harus diekstraksi menjadi komponen atau *layout* yang *reusable* (terpusat).
5.  **Theme Configuration**: Konfigurasi tema (warna, tipografi, ukuran) dikendalikan melalui sistem *Design Token* Tailwind (via `tailwind.config.js`). Perubahan warna utama (misal dari hijau menjadi biru) harus dapat dilakukan dengan hanya mengubah satu variabel atau file konfigurasi terpusat.
6.  **Kepatuhan Design System**: Semua komponen UI yang dibuat harus patuh 100% pada pedoman *SIAM Design System* (UI-001) dan tidak boleh menambahkan varian visual sembarangan yang tidak ada dalam pedoman tersebut.

## Consequences
### Positif
*   **Maintainability Ekstrem**: Jika suatu hari SIAM memutuskan mengubah gaya tombol atau mengganti sistem ikon, pengembang hanya perlu mengubah satu file komponen (misal `resources/views/components/button.blade.php`).
*   **Rapid Development**: Setelah *Component Library* (Sprint 2F.3.1) selesai dibangun, pengembangan halaman-halaman baru di *sprint* selanjutnya (Sprint 2F.3.2 dst) akan sangat cepat bagaikan merakit balok lego.
*   **Konsistensi Terjamin**: Kesalahan atau inkonsistensi tipografi, spasi, atau warna oleh *developer* dapat diminimalisasi karena kompleksitas Tailwind classes dibungkus di dalam komponen.
*   **Kode Halaman Bersih**: *File view* untuk sebuah halaman akan menjadi sangat bersih dan mudah dibaca karena hanya berisi logika *layouting* tingkat tinggi.

### Negatif / Tantangan
*   Diperlukan waktu tambahan di awal (upfront cost) pada **Sprint 2F.3.1** khusus untuk membangun *Component Library* dasar secara kokoh dan teruji sebelum mulai mengembangkan fitur UI modul Akademik.
*   *Developer* harus belajar dan terbiasa mencari komponen yang sudah ada alih-alih langsung mengetik HTML.

## Implementation Plan (Sprint 2F.3.1)
Sebelum mengimplementasikan UI Rombel (Academic Class), kita akan mendedikasikan satu *sub-sprint* (2F.3.1) untuk melahirkan:
*   `layouts/app.blade.php`, `layouts/guest.blade.php`
*   Micro Components: `<x-button>`, `<x-input>`, `<x-select>`, `<x-textarea>`, `<x-checkbox>`, `<x-radio>`, `<x-badge>`, `<x-empty-state>`, `<x-loading>`
*   Macro Components: `<x-card>`, `<x-table>`, `<x-modal>`, `<x-toast>`, `<x-pagination>`, `<x-breadcrumb>`, `<x-page-header>`, `<x-toolbar>`, `<x-filter-panel>`, `<x-search-box>`, `<x-confirm-dialog>`
*   Reusable Navigation: Sidebar, Topbar, Breadcrumb.
