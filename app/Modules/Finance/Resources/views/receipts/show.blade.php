<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuitansi Pembayaran - {{ $payment->payment_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="bg-gray-100 py-10 flex justify-center text-gray-800 antialiased font-sans">

    <div class="max-w-2xl w-full bg-white shadow-lg p-10 border-t-8 border-green-600 relative">
        <!-- Print Button -->
        <div class="absolute top-4 right-4 no-print flex gap-2">
            <a href="javascript:history.back()" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded text-sm font-semibold transition">Kembali</a>
            <button onclick="window.print()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm font-semibold shadow transition">Cetak / Simpan PDF</button>
        </div>

        <!-- Header -->
        <div class="flex justify-between items-start mb-8 mt-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">KUITANSI</h1>
                <p class="text-sm text-gray-500 mt-1">Sistem Informasi Administrasi Madrasah</p>
                <p class="text-sm text-gray-500">Jl. Pendidikan No. 123, Kota Bahagia</p>
            </div>
            <div class="text-right">
                <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">No. Kuitansi</p>
                <p class="text-lg font-mono text-gray-900">{{ $payment->payment_number }}</p>
                <p class="text-sm text-gray-500 mt-2">Tanggal: {{ $payment->payment_date->format('d/m/Y') }}</p>
            </div>
        </div>

        <hr class="border-gray-200 mb-8">

        <!-- Info Grid -->
        <div class="grid grid-cols-2 gap-8 mb-8">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Diterima Dari</p>
                <p class="font-bold text-gray-900 text-lg">{{ $payment->invoice->student->name ?? 'Siswa' }}</p>
                <p class="text-sm text-gray-600">NISN: {{ $payment->invoice->student->nisn ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Metode Pembayaran</p>
                <p class="font-bold text-gray-900">{{ $payment->payment_method }}</p>
                <p class="text-sm text-gray-600">Ref: {{ $payment->reference_number ?? '-' }}</p>
            </div>
        </div>

        <!-- Payment Details Table -->
        <div class="mb-8 rounded-lg overflow-hidden border border-gray-200">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="py-3 px-4 font-semibold text-sm text-gray-700 border-b border-gray-200">Keterangan Tagihan</th>
                        <th class="py-3 px-4 font-semibold text-sm text-gray-700 text-right border-b border-gray-200">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="py-4 px-4 text-gray-900">
                            {{ $payment->invoice->title }} <br>
                            <span class="text-sm text-gray-500">{{ $payment->invoice->invoice_number }}</span>
                        </td>
                        <td class="py-4 px-4 text-right font-medium text-gray-900">
                            Rp {{ number_format($payment->amount, 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td class="py-4 px-4 font-bold text-gray-900 text-right uppercase text-sm">Total Dibayar</td>
                        <td class="py-4 px-4 text-right font-bold text-green-600 text-xl border-t border-gray-200">
                            Rp {{ number_format($payment->amount, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Footer / Signature & QR -->
        <div class="flex justify-between items-end mt-12">
            <!-- QR Code for Verification -->
            <div class="flex flex-col items-center">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode($validationUrl) }}&color=111827" alt="QR Code" class="w-24 h-24 border p-1 rounded-md mb-2 bg-white">
                <p class="text-xs text-gray-500 text-center max-w-[120px]">Scan untuk validasi keaslian dokumen</p>
            </div>
            
            <div class="text-center">
                <p class="text-sm text-gray-600 mb-12">Penerima / Verifikator</p>
                <p class="font-bold text-gray-900 uppercase border-b border-gray-400 inline-block px-4 pb-1">
                    {{ $payment->verifiedBy->name ?? 'Admin Keuangan' }}
                </p>
                <p class="text-xs text-gray-500 mt-1">Dicetak: {{ now()->format('d/m/Y H:i') }}</p>
            </div>
        </div>
        
        <div class="mt-12 text-center text-xs text-gray-400 border-t pt-4">
            Dokumen ini dihasilkan secara otomatis oleh sistem SIAM dan sah tanpa stempel basah.<br>
            Status Validasi: <strong class="text-green-600">TERVERIFIKASI</strong>
        </div>
    </div>
</body>
</html>
