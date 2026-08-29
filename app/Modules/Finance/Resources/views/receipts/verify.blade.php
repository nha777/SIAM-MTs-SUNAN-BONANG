<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validasi Kuitansi - {{ $payment->payment_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-green-600 p-6 text-center">
            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h2 class="text-xl font-bold text-white tracking-tight">KUITANSI VALID</h2>
            <p class="text-green-100 text-sm mt-1">Pembayaran tercatat resmi di sistem SIAM</p>
        </div>
        
        <div class="p-6">
            <dl class="divide-y divide-gray-100">
                <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                    <dt class="text-sm font-medium text-gray-500">No. Kuitansi</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0 font-mono font-semibold">{{ $payment->payment_number }}</dd>
                </div>
                <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                    <dt class="text-sm font-medium text-gray-500">Siswa</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0 font-semibold">{{ $payment->invoice->student->name ?? '-' }}</dd>
                </div>
                <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                    <dt class="text-sm font-medium text-gray-500">Keterangan</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $payment->invoice->title ?? '-' }}</dd>
                </div>
                <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                    <dt class="text-sm font-medium text-gray-500">Tanggal Bayar</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $payment->payment_date->format('d/m/Y') }}</dd>
                </div>
                <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                    <dt class="text-sm font-medium text-gray-500">Nominal</dt>
                    <dd class="mt-1 text-lg font-bold text-green-600 sm:col-span-2 sm:mt-0">Rp {{ number_format($payment->amount, 0, ',', '.') }}</dd>
                </div>
            </dl>
            
            <div class="mt-6 flex justify-center">
                <a href="/" class="text-sm font-semibold text-green-600 hover:text-green-500">Kembali ke Beranda</a>
            </div>
        </div>
    </div>
</body>
</html>
