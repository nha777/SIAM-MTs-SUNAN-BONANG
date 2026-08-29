<?php
namespace App\Modules\Finance\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\Finance\Models\Invoice;
use App\Modules\Finance\Jobs\SendWhatsAppNotificationJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SendInvoiceReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'finance:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim pengingat tagihan ke wali murid (H-3 dan Hari H)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Mencari tagihan yang mendekati jatuh tempo...');

        $today = Carbon::today();
        $inThreeDays = Carbon::today()->addDays(3);

        // Get unpaid or partial invoices due today or in 3 days
        $invoices = Invoice::with(['student', 'student.guardian'])
            ->whereIn('status', ['Unpaid', 'Partial'])
            ->whereIn('due_date', [$today->toDateString(), $inThreeDays->toDateString()])
            ->get();

        $count = 0;
        foreach ($invoices as $invoice) {
            $guardian = DB::table('guardians')->where('id', $invoice->student->guardian_id)->first();
            
            if ($guardian && $guardian->phone_number) {
                $isDueToday = Carbon::parse($invoice->due_date)->isToday();
                $sisa = $invoice->amount - $invoice->paid_amount;
                $formattedSisa = 'Rp ' . number_format($sisa, 0, ',', '.');
                
                if ($isDueToday) {
                    $message = "Yth. Bpk/Ibu {$guardian->guardian_name},\n\nKami menginformasikan bahwa tagihan *{$invoice->title}* untuk ananda {$invoice->student->name} senilai {$formattedSisa} telah JATUH TEMPO HARI INI.\n\nMohon segera melakukan pembayaran. Abaikan pesan ini jika sudah membayar.\n\nSalam,\nAdmin SIAM";
                } else {
                    $message = "Yth. Bpk/Ibu {$guardian->guardian_name},\n\nKami mengingatkan bahwa tagihan *{$invoice->title}* untuk ananda {$invoice->student->name} senilai {$formattedSisa} akan jatuh tempo dalam 3 hari ({$invoice->due_date->format('d M Y')}).\n\nSalam,\nAdmin SIAM";
                }

                SendWhatsAppNotificationJob::dispatch($guardian->phone_number, $message);
                $count++;
            }
        }

        $this->info("Berhasil menjadwalkan {$count} pesan pengingat WhatsApp.");
    }
}
