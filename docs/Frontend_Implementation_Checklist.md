# Frontend Implementation Checklist

Dokumen ini mendefinisikan standar teknis implementasi komponen *frontend* di SIAM. Setiap komponen baru atau halaman yang dikembangkan harus memenuhi kriteria di bawah ini agar dapat dipelihara dengan mudah dan digunakan kembali (*reusable*).

## 1. Blade Component & Naming Convention
- [ ] Menggunakan *namespace* `x-*` dengan aturan penamaan ketat:
  - **Micro Components**: `<x-button>`, `<x-input>`, `<x-select>`, `<x-table>`, `<x-card>`.
  - **Macro Components**: `<x-page-header>`, `<x-filter-panel>`, `<x-search-box>`, `<x-confirm-dialog>`. (Dilarang memunculkan variasi liar seperti `x-input-search` atau `x-search` untuk tujuan yang sama).
- [ ] Tidak ada HTML duplikat untuk elemen dasar yang sama.
- [ ] Mendukung `slot` bila diperlukan untuk injeksi konten yang fleksibel.
- [ ] Mendukung atribut tambahan (`$attributes->merge(...)`) sehingga bisa menerima `class`, `id`, `disabled`, `wire:model`, dll.

## 2. Component API Standard & Documentation
- [ ] **API yang Konsisten**: Setiap komponen wajib memakai *props* yang seragam (misal: menggunakan `variant`, `size`, `icon`, `loading`, `disabled`). Dilarang mencampuradukkan penamaan (seperti `color` di tombol A, tapi `type` di tombol B untuk hal yang sama).
- [ ] **Component Documentation**: Setiap komponen wajib memiliki dokumentasi (di dalam komentar atau *Component Library Preview*) yang berisi:
  - *Description*
  - *Props* (tipe data dan nilai *default*)
  - *Slots*
  - *Events*
  - *Example* (contoh penggunaan)
  - *Accessibility Notes*

## 3. Component Versioning
- [ ] **Breaking Change**: Perubahan mayor pada struktur komponen yang merusak kompatibilitas mundur harus dikomunikasikan ke tim.
- [ ] **Deprecated Component**: Komponen usang yang akan diganti harus ditandai dan jangan langsung dihapus sebelum masa transisi selesai.
- [ ] **Migration Guide**: Menyediakan panduan singkat apabila API komponen berubah secara signifikan.

## 4. Accessibility (Aksesibilitas)
- [ ] **Keyboard accessible**: Semua elemen interaktif dapat dijangkau menggunakan tombol `Tab`.
- [ ] **ARIA**: Menggunakan atribut ARIA bila diperlukan (seperti `aria-expanded`, `aria-label`, `aria-hidden` untuk ikon).
- [ ] **Focus state**: Memiliki *focus ring* yang terlihat jelas saat elemen menerima fokus keyboard.
- [ ] **Kontras**: Kontras teks terhadap latar belakang mematuhi standar rasio WCAG AA (minimal 4.5:1 untuk teks normal).

## 5. Performance Quality
- [ ] **No Nested Overkill**: Tidak ada *nested component* berlebihan yang membebani proses *render* server.
- [ ] **No Query in Blade**: Sama sekali tidak boleh melakukan eksekusi query *database* di dalam *file view* atau *Blade Component*.
- [ ] **SVG Management**: SVG icon menggunakan *sprite* atau *Blade UI Icons* agar struktur HTML halaman tidak bengkak.
- [ ] **No Duplicate Assets**: Asset CSS/JS (jika didaftarkan via komponen) tidak di-render berulang kali.
- [ ] **Lazy Load**: Komponen berat (seperti Modal eksternal, Image, Chart) menerapkan *lazy loading* bila memungkinkan.
- [ ] **Zero CLS**: Tidak ada *Cumulative Layout Shift* (lompatan layout) ketika komponen asinkron selesai dimuat.
- [ ] **Efficient Render**: Tidak ada *render* komponen yang tidak perlu.

## 6. Responsive Design
- [ ] **Desktop**: Komponen merender dengan proporsional pada layar lebar.
- [ ] **Tablet**: Layout beradaptasi, form atau tabel tidak terpotong (misal: memunculkan horizontal scroll untuk tabel).
- [ ] **Mobile**: Layout berubah menjadi *stacked* (bertumpuk vertikal), *touch target* cukup besar (minimal 44px).

## 7. Reusability, Maintainability & Future Theme
- [ ] **Tidak *hardcode* warna**: Dilarang menggunakan nilai warna HEX atau RGB mentah (contoh: `#10b981`).
- [ ] **Agnostik Modul**: Komponen UI murni (seperti Modal, Card, Button) tidak boleh bergantung pada variabel spesifik modul tertentu (misalnya `$academicClass`). Data harus di-*pass* melalui *props*.
- [ ] **Future Theme Ready**: Menggunakan *Design Token* Tailwind secara penuh (`bg-primary-500`, `text-surface-900`) sehingga perubahan *brand color* dapat diterapkan dalam sekejap.
- [ ] **Dark Mode & High Contrast Ready**: Struktur markup tidak menghalangi penambahan *prefix* `dark:` atau skema kontras tinggi di masa depan.

## 8. Testing & States
- [ ] **State Screenshot/Preview**: (Disarankan) Menyediakan tangkapan layar untuk dokumentasi internal.
- [ ] **Dark Mode (Future)**: Struktur *markup* tidak menghalangi implementasi `dark:` variant di masa depan.
- [ ] **Empty State**: Halaman daftar / tabel harus mampu merender komponen `<x-empty-state>` bila data kosong.
- [ ] **Loading State**: Aksi asinkron memunculkan indikator *loading* (spinner/skeleton) dan menonaktifkan tombol (disabled).
- [ ] **Error State**: Pesan validasi atau *feedback* kegagalan tertangani dan dirender dengan warna/ikon indikator peringatan/danger.
