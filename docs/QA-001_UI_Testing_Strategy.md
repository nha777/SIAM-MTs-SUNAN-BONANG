# QA-001: UI Testing Strategy

Dokumen ini mendefinisikan strategi pengujian antarmuka (UI Testing Strategy) untuk memastikan komponen dan halaman di SIAM bekerja dengan benar dan tidak mengalami regresi (kerusakan akibat perubahan di tempat lain).

## 1. Browser Test (End-to-End)
*   **Tujuan**: Menguji alur kritikal dari sudut pandang pengguna (misal: login, mengisi form tambah siswa, lalu klik submit).
*   **Implementasi**: Akan menggunakan **Laravel Dusk** (atau framework E2E setara seperti Cypress/Playwright jika menggunakan stack JS yang kental).
*   **Ruang Lingkup**: 
    *   Form submission (memastikan data tersimpan).
    *   Validasi error (memastikan form tidak lolos jika data salah).
    *   Navigasi utama.

## 2. Visual Regression Testing (Future Scope)
*   **Tujuan**: Mendeteksi perubahan visual yang tidak disengaja akibat modifikasi CSS atau Blade Component.
*   **Implementasi**: Menyimpan *snapshot* gambar (screenshot) halaman atau komponen, lalu membandingkannya saat ada *Pull Request* baru.
*   **Ruang Lingkup**:
    *   Halaman *Gallery/Playground* Komponen (`/dev/components`).
    *   *Dashboard* utama.

## 3. Snapshot / Component Testing
*   **Tujuan**: Menguji logika *Blade Component* atau *Livewire Component* (misal: memastikan tombol *disabled* ketika *prop* `loading=true`).
*   **Implementasi**: PHPUnit / Pest.

## 4. Manual QA & Code Review (Current Focus)
Sebelum QA otomatis di atas diimplementasikan secara penuh, strategi utama kita saat ini (Sprint 2F.3) adalah:
*   Mewajibkan *Reviewer* untuk merujuk pada `Frontend_Review_Checklist.md`.
*   Mengecek komponen di halaman *Living Documentation* (`/dev/components`).
*   Mengecek perubahan di mode *Desktop* dan *Mobile* secara manual sebelum melakukan *Merge*.
