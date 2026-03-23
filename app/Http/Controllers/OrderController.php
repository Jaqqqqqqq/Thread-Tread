<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Mail\OrderStatusUpdated;
use App\Services\ReceiptGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function myOrders()
    {
        $orders = Order::where('user_id', Auth::user()->user_id)
            ->with(['items.variant.product', 'paymentMethod'])
            ->orderBy('order_id', 'desc')
            ->paginate(10);
        return view('orders.mine', compact('orders'));
    }

    /**
     * Show admin form to update order status
     */
    public function editStatus(Order $order)
    {
        $statuses = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'];
        return view('orders.edit-status', compact('order', 'statuses'));
    }

    /**
     * Update order status and send email notification
     */
    public function updateStatus(Request $request, Order $order)
    {
        // Load relationships if not already loaded
        $order->load(['paymentMethod', 'user']);
        
        // DEBUG: Log that this method was called
        \Illuminate\Support\Facades\Log::info('🔴 updateStatus method called', [
            'route_parameter' => request()->route('order'),
            'order_id_from_request' => $order->order_id ?? 'NULL',
            'request_all' => $request->all()
        ]);
        
        $request->validate([
            'order_status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled',
        ]);

        $oldStatus = $order->order_status;
        $newStatus = $request->order_status;

        // Don't proceed if status hasn't changed
        if ($oldStatus === $newStatus) {
            return back()->with('warning', 'Order status is already ' . $newStatus);
        }

        try {
            \Illuminate\Support\Facades\Log::info('OrderController: Updating order status', [
                'order_id' => $order->order_id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus
            ]);
            
            // Update order status
            $order->update(['order_status' => $newStatus]);

            // For COD orders: Only mark payment as 'paid' when order is completed
            if ($newStatus === 'completed') {
                $updateCount = Payment::where('order_id', $order->order_id)->update(['payment_status' => 'paid']);
                \Illuminate\Support\Facades\Log::info('✅ Payment update query executed', [
                    'order_id' => $order->order_id,
                    'rows_affected' => $updateCount
                ]);
                
                // Refresh entire order with all relationships to ensure payment status is updated
                $order = Order::with('items.variant.product', 'user', 'paymentMethod', 'payment')
                             ->where('order_id', $order->order_id)
                             ->first();
                
                // Verify the payment was actually updated
                \Illuminate\Support\Facades\Log::info('✅ Order refreshed, payment status is now:', [
                    'order_id' => $order->order_id,
                    'payment_status' => $order->payment->payment_status ?? 'NO PAYMENT RECORD'
                ]);
            }

            // Generate and send PDF receipt for every status update (for both Online Payment and COD)
            try {
                ReceiptGenerator::generateReceipt($order);
                \Illuminate\Support\Facades\Log::info('✅ PDF receipt generated for status update', [
                    'order_id' => $order->order_id,
                    'new_status' => $newStatus
                ]);
            } catch (\Exception $pdfError) {
                \Illuminate\Support\Facades\Log::error('❌ Failed to generate PDF for email', [
                    'order_id' => $order->order_id,
                    'error' => $pdfError->getMessage()
                ]);
            }

            // Send email to customer with status update and receipt PDF
            try {
                Mail::to($order->user->email)->send(new OrderStatusUpdated($order, $oldStatus, $newStatus));
                \Illuminate\Support\Facades\Log::info('✅ Status update email with PDF receipt sent', [
                    'order_id' => $order->order_id,
                    'new_status' => $newStatus
                ]);
            } catch (\Exception $mailError) {
                \Illuminate\Support\Facades\Log::error('⚠️ Failed to send status update email', [
                    'order_id' => $order->order_id,
                    'error' => $mailError->getMessage()
                ]);
            }
            
            // Prepare success message
            $successMsg = 'Order status updated to ' . ucfirst($newStatus);
            if ($newStatus === 'delivered') {
                $successMsg .= ' — Payment marked as PAID and receipt email with PDF sent to customer.';
            } else {
                $successMsg .= ' and receipt email with PDF sent to customer.';
            }
            
            return back()->with('success', $successMsg);
        } catch (\Exception $e) {
            // Log the error
            \Illuminate\Support\Facades\Log::error('Error updating order status: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
