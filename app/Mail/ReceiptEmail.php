<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ReceiptEmail extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;
    public string $pdfPath;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order, ?string $pdfPath = null)
    {
        $this->order = $order->load('items.variant.product', 'paymentMethod', 'user', 'payment');
        $this->pdfPath = $pdfPath ?? 'receipts/order_' . $order->order_id . '.pdf';
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME')),
            subject: 'Order Receipt - #' . $this->order->order_id,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.receipt',
            with: [
                'order' => $this->order,
                'customerName' => $this->order->user->fname . ' ' . $this->order->user->lname,
                'orderTotal' => number_format($this->order->total_amount, 2),
                'orderDate' => date('F j, Y'),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        // Check if PDF file exists before attaching
        if (!Storage::exists($this->pdfPath)) {
            \Illuminate\Support\Facades\Log::warning('⚠️ PDF file does not exist for attachment', [
                'order_id' => $this->order->order_id,
                'pdf_path' => $this->pdfPath,
                'storage_disk' => config('filesystems.default')
            ]);
            return [];
        }
        
        try {
            \Illuminate\Support\Facades\Log::info('📎 Attaching PDF receipt to email', [
                'order_id' => $this->order->order_id,
                'pdf_path' => $this->pdfPath,
                'file_exists' => Storage::exists($this->pdfPath),
                'file_size' => Storage::size($this->pdfPath)
            ]);
            
            return [
                Attachment::fromStorage($this->pdfPath)
                    ->as('order_receipt_' . $this->order->order_id . '.pdf')
                    ->withMime('application/pdf'),
            ];
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('❌ Failed to attach PDF', [
                'order_id' => $this->order->order_id,
                'pdf_path' => $this->pdfPath,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }
}
