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
            'confirmed' => 'Your order has been confirmed! 🎉',
            'processing' => 'Your order is being processed',
            'shipped' => 'Your order has been shipped! 📦',
            'delivered' => 'Your order has been delivered! ✓',
            'completed' => 'Your order has been completed! ✓',
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
            \Illuminate\Support\Facades\Log::info('⏭️ PDF file does not exist', [
                'order_id' => $this->order->order_id,
                'pdf_path' => $pdfPath
            ]);
            return $attachments;
        }
        
        // Determine if we should attach PDF based on payment method and status
        $shouldAttachPdf = false;
        
        if ($paymentMethod === 'Cash on Delivery') {
            // For COD, attach PDF when order is delivered
            $shouldAttachPdf = in_array($this->newStatus, ['delivered', 'completed']);
            \Illuminate\Support\Facades\Log::info('📦 COD Order - Attaching PDF on delivery', [
                'order_id' => $this->order->order_id,
                'new_status' => $this->newStatus,
                'should_attach_pdf' => $shouldAttachPdf
            ]);
        } elseif ($paymentMethod === 'Online Payment') {
            // For online payment, always attach PDF (it was generated at order placement)
            $shouldAttachPdf = true;
            \Illuminate\Support\Facades\Log::info('💳 Online Payment - Attaching PDF on all updates', [
                'order_id' => $this->order->order_id,
                'new_status' => $this->newStatus
            ]);
        }
        
        if (!$shouldAttachPdf) {
            \Illuminate\Support\Facades\Log::info('⏭️ PDF attachment skipped - status not triggering', [
                'order_id' => $this->order->order_id,
                'payment_method' => $paymentMethod,
                'current_status' => $this->newStatus
            ]);
            return $attachments;
        }
        
        // Try to attach PDF
        try {
            $attachments[] = Attachment::fromStorage($pdfPath)
                ->as('order_receipt_' . $this->order->order_id . '.pdf')
                ->withMime('application/pdf');
            
            \Illuminate\Support\Facades\Log::info('✅ PDF attachment added successfully', [
                'order_id' => $this->order->order_id,
                'payment_method' => $paymentMethod
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('❌ Failed to add PDF attachment', [
                'order_id' => $this->order->order_id,
                'error' => $e->getMessage()
            ]);
        }
        
        return $attachments;
    }
}
