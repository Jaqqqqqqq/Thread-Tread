<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class OrderStatusUpdated extends Mailable
{

    public Order $order;
    public string $oldStatus;
    public string $newStatus;
    public string $statusMessage;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order, string $oldStatus, string $newStatus)
    {
        $this->order = $order->load('items.variant.product', 'user', 'paymentMethod', 'payment');
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        
        // Set status message based on new status
        $this->statusMessage = match($newStatus) {
            'pending' => 'Your order is pending confirmation',
            'confirmed' => 'Your order has been confirmed!',
            'processing' => 'Your order is being processed',
            'shipped' => 'Your order has been shipped!',
            'delivered' => 'Your order has been delivered!',
            'cancelled' => 'Your order has been cancelled',
            default => 'Your order status has been updated'
        };
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME')),
            subject: 'Order #' . $this->order->order_id . ' - ' . ucfirst($this->newStatus),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Determine if PDF will be attached
        $pdfPath = 'receipts/order_' . $this->order->order_id . '.pdf';
        $hasPdfAttached = \Illuminate\Support\Facades\Storage::exists($pdfPath);
        
        return new Content(
            view: 'emails.order-status-updated',
            with: [
                'order' => $this->order,
                'customerName' => $this->order->user->fname . ' ' . $this->order->user->lname,
                'oldStatus' => $this->oldStatus,
                'newStatus' => $this->newStatus,
                'statusMessage' => $this->statusMessage,
                'hasPdfAttached' => $hasPdfAttached,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        $attachments = [];
        $paymentMethod = $this->order->paymentMethod->method_name ?? null;
        
        // Check for the PDF file
        $pdfPath = 'receipts/order_' . $this->order->order_id . '.pdf';
        
        if (!\Illuminate\Support\Facades\Storage::exists($pdfPath)) {
            \Illuminate\Support\Facades\Log::info('⏭️ PDF file does not exist for attachment', [
                'order_id' => $this->order->order_id,
                'pdf_path' => $pdfPath
            ]);
            return $attachments;
        }
        
        // Attach PDF receipt for EVERY status update (for both Cash on Delivery and Online Payment)
        try {
            $attachments[] = Attachment::fromStorage($pdfPath)
                ->as('order_receipt_' . $this->order->order_id . '.pdf')
                ->withMime('application/pdf');
            
            \Illuminate\Support\Facades\Log::info('✅ PDF receipt attached to status update email', [
                'order_id' => $this->order->order_id,
                'payment_method' => $paymentMethod,
                'status' => $this->newStatus
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('❌ Failed to attach PDF receipt', [
                'order_id' => $this->order->order_id,
                'error' => $e->getMessage()
            ]);
        }
        
        return $attachments;
    }
}
