<?php

namespace App\Services;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class ReceiptGenerator
{
    /**
     * Generate a PDF receipt for an order and save it to storage
     */
    public static function generateReceipt(Order $order): string
    {
        \Illuminate\Support\Facades\Log::info('ReceiptGenerator: Starting PDF generation', ['order_id' => $order->order_id]);
        
        $order->load('items.variant.product', 'user', 'paymentMethod', 'payment');

        // Create the PDF from view
        $pdf = Pdf::loadView('receipts.order-receipt', [
            'order' => $order,
            'customerName' => $order->user->fname . ' ' . $order->user->lname,
            'customerEmail' => $order->user->email,
            'orderDate' => date('F j, Y'),
        ]);

        // Set paper size and margins
        $pdf->setPaper('A4');

        // Save to storage
        $filename = 'order_' . $order->order_id . '.pdf';
        $path = 'receipts/' . $filename;
        
        // Ensure the receipts directory exists
        if (!Storage::exists('receipts')) {
            \Illuminate\Support\Facades\Log::info('ReceiptGenerator: Creating receipts directory');
            Storage::makeDirectory('receipts');
        }
        
        Storage::put($path, $pdf->output());
        
        // Verify the file was created
        if (Storage::exists($path)) {
            $size = Storage::size($path);
            \Illuminate\Support\Facades\Log::info('ReceiptGenerator: PDF created successfully', [
                'order_id' => $order->order_id,
                'path' => $path,
                'size' => $size
            ]);
        } else {
            \Illuminate\Support\Facades\Log::error('ReceiptGenerator: Failed to create PDF file', [
                'order_id' => $order->order_id,
                'path' => $path
            ]);
        }

        return $path;
    }

    /**
     * Generate and return PDF without saving
     */
    public static function generateReceiptForDownload(Order $order)
    {
        $order->load('items.variant.product', 'user', 'paymentMethod');

        return Pdf::loadView('receipts.order-receipt', [
            'order' => $order,
            'customerName' => $order->user->fname . ' ' . $order->user->lname,
            'customerEmail' => $order->user->email,
            'orderDate' => date('F j, Y'),
        ])->download('order_receipt_' . $order->order_id . '.pdf');
    }
}
