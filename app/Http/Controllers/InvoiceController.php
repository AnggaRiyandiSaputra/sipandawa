<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use App\Models\Invoices;
use App\Models\Messages;
use App\Models\Employees;
use App\Models\Transactions;
use FontLib\Table\Type\name;
use GuzzleHttp\Psr7\Message;
use Illuminate\Http\Request;
use App\Models\InvoiceDetails;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;

class InvoiceController extends Controller
{
    public function preview($id)
    {
        $invoice = Invoices::with(['detailInvoice', 'Client', 'Employee'])->find($id);
        $items = $invoice->detailInvoice;
        $pajak = $invoice->pajak_rate / 100;
        $logoBase64 = $this->getLogoBase64();
        
        return view('invoice.index', compact('invoice', 'items', 'pajak', 'logoBase64'));
    }

    public function download($id)
    {
        $invoice = Invoices::with(['detailInvoice', 'Client', 'Employee'])->find($id);
        $items = $invoice->detailInvoice;
        $pajak = $invoice->pajak_rate / 100;
        $logoBase64 = $this->getLogoBase64();
        
        $pdf = Pdf::loadView('invoice.index', [
            'invoice' => $invoice,
            'items' => $items,
            'pajak' => $pajak,
            'logoBase64' => $logoBase64
        ])->setOptions(['isRemoteEnabled' => true]);
        $name =  $invoice->no_invoice . '.pdf';
        
        return $pdf->download($name);
    }

    public function makeMessage($id)
    {
        $invoice = Invoices::with(['detailInvoice', 'Client', 'Employee'])->find($id);
        $items = $invoice->detailInvoice; // Mengambil semua InvoiceDetails terkait
        $clients = $invoice->Client; // Mengambil data Client terkait
        $PJ = $invoice->Employee; // Mengambil data Employee terkait
        $linkInvoice = url('/preview-invoice/' . $id);

        //jika belum bayar
        if ($invoice->is_paid == 0) {
            $message = "Halo ," . $clients->name . "\n\n";
            $message .= "Berikut adalah rincian tagihan Anda:\n\n";
            $message .= "Tanggal Jatuh Tempo: " . $invoice->due_date->format('d-m-Y') . "\n";
            $message .= "Layanan:\n";

            // Perulangan untuk item layanan
            foreach ($items as $item) {
                $message .= "- {$item->item} (Rp. " . number_format($item->price, 0, ',', '.') . ")\n";
            }

            $message .= "\n";
            $message .= "Sub Total = Rp. " . number_format($invoice->sub_total, 0, ',', '.') . "\n";
            if ($invoice->is_pajak != 0) {
                $message .= "PPN 11% = Rp. " . number_format($invoice->sub_total * 0.11, 0, ',', '.') . "\n";
            } else if ($invoice->diskon != 0) {
                $message .= "Diskon = Rp. " . number_format($invoice->diskon, 0, ',', '.') . "\n";
            }

            $message .= "Total = Rp. " . number_format($invoice->grand_total, 0, ',', '.') . "\n\n";
            $message .= "* Mohon lakukan pembayaran sebelum tanggal jatuh tempo\n\n";
            $message .= "* Untuk detail invoice silahkan lihat pada link berikut:\n";
            $message .= $linkInvoice . "\n";
            $message .= "---------------------\n";
            $message .= "BCA: 1772653021 \n";
            $message .= "an: CV PANDAWA DIGITAL MEDIA\n";
            $message .= "---------------------\n\n";
            $message .= "Website Pandawa:\n";
            $message .= "https://pandawa.biz.id/\n\n";
            $message .= "Instagram Pandawa:\n";
            $message .= "@agensipandawa\n";

            //kirim  $message ke tabel message
            Messages::updateOrCreate(['no_invoice' => $invoice->no_invoice], [
                'employee_id' => $PJ->id,
                'client_id' => $clients->id,
                'no_invoice' => $invoice->no_invoice,
                'name' => $invoice->name,
                'message' => $message
            ]);

            Notification::make()
                ->title('Berhasil membuat pesan')
                ->success()
                ->send();

            return redirect()->back();
        } else if ($invoice->is_paid == 1) {
            $message = "Halo ," . $clients->name . "\n\n";
            $message .= "Pembayaran anda telah kami terima:\n\n";
            $message .= "Tanggal Pembayaran: " . $invoice->paid_date->format('d-m-Y') . "\n";
            $message .= "Layanan:\n";

            // Perulangan untuk item layanan
            foreach ($items as $item) {
                $message .= "- {$item->item} (Rp. " . number_format($item->price, 0, ',', '.') . ")\n";
            }

            $message .= "\n";
            $message .= "Sub Total = Rp. " . number_format($invoice->sub_total, 0, ',', '.') . "\n";
            if ($invoice->is_pajak != 0) {
                $message .= "PPN 11% = Rp. " . number_format($invoice->sub_total * 0.11, 0, ',', '.') . "\n";
            } else if ($invoice->diskon != 0) {
                $message .= "Diskon = Rp. " . number_format($invoice->diskon, 0, ',', '.') . "\n";
            }

            $message .= "Total = Rp. " . number_format($invoice->grand_total, 0, ',', '.') . "\n\n";
            $message .= "* Terima kasih telah mempercayakan kebutuhan anda kepada PANDAWA\n\n";
            $message .= "* Untuk detail invoice silahkan lihat pada link berikut:\n";
            $message .= $linkInvoice . "\n";
            $message .= "---------------------\n\n";
            $message .= "Website Pandawa:\n";
            $message .= "https://pandawa.biz.id/\n\n";
            $message .= "Instagram Pandawa:\n";
            $message .= "@agensipandawa\n";

            //kirim  $message ke tabel message
            Messages::updateOrCreate(['no_invoice' => $invoice->no_invoice], [
                'employee_id' => $PJ->id,
                'client_id' => $clients->id,
                'no_invoice' => $invoice->no_invoice,
                'name' => $invoice->name,
                'message' => $message
            ]);

            Notification::make()
                ->title('Berhasil membuat pesan')
                ->success()
                ->send();

            return redirect()->back();
        } else {
            Notification::make()
                ->title('Gagal membuat pesan')
                ->danger()
                ->send();

            return redirect()->back();
        };
    }

   private function getLogoBase64(){
     $logoPath = Storage::disk('public')->path('/img/pandawa.png');
     $logoData = file_get_contents($logoPath);
     $logoBase64 = 'data:image/' . pathinfo($logoPath, PATHINFO_EXTENSION) . ';base64,' . base64_encode($logoData);
     return $logoBase64;
   }
}
