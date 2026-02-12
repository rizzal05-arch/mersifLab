<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Invoice;

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
        
        return (new MailMessage)
            ->subject($this->invoice->title . ' - ' . $this->invoice->invoice_number)
            ->view('emails.invoice-with-qris', [
                'invoice' => $this->invoice,
                'whatsappUrl' => $whatsappUrl
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
