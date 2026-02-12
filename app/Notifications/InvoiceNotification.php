<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Invoice;
use Illuminate\Support\Facades\Mail;

class InvoiceNotification extends Notification
{
    use Queueable;

    public $invoice;

    /**
     * Create a new notification instance.
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $invoiceUrl = route('invoices.show', $this->invoice->invoice_number);
        $whatsappNumber = '+62895326395100';
        $whatsappMessage = urlencode("Halo MersifLab, saya ingin konfirmasi pembayaran untuk invoice {$this->invoice->invoice_number} sebesar {$this->invoice->formatted_total_amount}");
        $whatsappUrl = "https://wa.me/{$whatsappNumber}?text={$whatsappMessage}";
        
        // Get QRIS image path and prepare base64 as fallback
        $qrisImagePath = public_path('images/qris-payment.jpeg');
        $qrisImageExists = file_exists($qrisImagePath);
        $qrisImageBase64 = '';
        
        if ($qrisImageExists) {
            // Prepare base64 as fallback for email clients that don't support embedded attachments
            $imageData = file_get_contents($qrisImagePath);
            $qrisImageBase64 = 'data:image/jpeg;base64,' . base64_encode($imageData);
        }
        
        // Get logo path
        $logoPath = public_path('images/logo.png');
        $logoExists = file_exists($logoPath);
        
        // Prepare items list from metadata or invoice
        $items = [];
        if (isset($this->invoice->metadata['items']) && is_array($this->invoice->metadata['items'])) {
            $items = $this->invoice->metadata['items'];
        } else {
            // Fallback: create single item from invoice
            $items = [[
                'name' => $this->invoice->item_description,
                'price' => $this->invoice->amount
            ]];
        }
        
        return (new MailMessage)
            ->subject($this->invoice->title . ' - ' . $this->invoice->invoice_number)
            ->view('emails.invoice-with-qris', [
                'invoice' => $this->invoice,
                'whatsappUrl' => $whatsappUrl,
                'items' => $items,
                'qrisImagePath' => $qrisImageExists ? $qrisImagePath : null,
                'qrisImageBase64' => $qrisImageBase64,
                'logoPath' => $logoExists ? $logoPath : null
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'invoice_id' => $this->invoice->id,
            'invoice_number' => $this->invoice->invoice_number,
            'type' => $this->invoice->type,
            'amount' => $this->invoice->total_amount,
            'status' => $this->invoice->status,
            'due_date' => $this->invoice->due_date,
        ];
    }
}
