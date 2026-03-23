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
        return new Content(
            view: 'emails.order-status-updated',
            with: [
                'order' => $this->order,
                'customerName' => $this->order->user->fname . ' ' . $this->order->user->lname,
                'oldStatus' => $this->oldStatus,
                'newStatus' => $this->newStatus,
                'statusMessage' => $this->statusMessage,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        $attachments = [];
        
        // Determine if we should attach PDF based on payment method and status
        $shouldAttachPdf = false;
        $paymentMethod = $this->order->paymentMethod->method_name ?? null;
        
        if ($paymentMethod === 'Online Payment') {
            // For online payment, attach PDF when admin confirms the order (status = 'confirmed')
            $shouldAttachPdf = $this->newStatus === 'confirmed';
            \Illuminate\Support\Facades\Log::info('💳 Online Payment - Checking confirmation status', [
                'order_id' => $this->order->order_id,
                'new_status' => $this->newStatus,
                'should_attach_pdf' => $shouldAttachPdf
            ]);
        } elseif ($paymentMethod === 'COD') {
            // For COD, attach PDF when admin marks order as completed
            $shouldAttachPdf = $this->newStatus === 'completed';
            \Illuminate\Support\Facades\Log::info('📦 COD Order - Checking completion status', [
                'order_id' => $this->order->order_id,
                'new_status' => $this->newStatus,
                'should_attach_pdf' => $shouldAttachPdf
            ]);
        }
        
        if (!$shouldAttachPdf) {
            \Illuminate\Support\Facades\Log::info('⏭️ PDF attachment skipped - status not matching trigger', [
                'order_id' => $this->order->order_id,
                'payment_method' => $paymentMethod,
                'current_status' => $this->newStatus
            ]);
            return $attachments;
        }
        
        // Check for the PDF file
        $pdfPath = 'receipts/order_' . $this->order->order_id . '.pdf';
        
        \Illuminate\Support\Facades\Log::info('🔍 Checking for PDF attachment', [
            'order_id' => $this->order->order_id,
            'pdf_path' => $pdfPath
        ]);
        
        // Try to attach if PDF exists
        if (\Illuminate\Support\Facades\Storage::exists($pdfPath)) {
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
        } else {
            \Illuminate\Support\Facades\Log::warning('⚠️ PDF file not found for attachment', [
                'order_id' => $this->order->order_id,
                'expected_path' => $pdfPath
            ]);
        }
        
        return $attachments;
    }
}
