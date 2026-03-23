<?php

namespace App\Http\Controllers;

use App\Models\Order;
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

            // Generate PDF receipt
            \Illuminate\Support\Facades\Log::info('OrderController: Generating PDF receipt');
            ReceiptGenerator::generateReceipt($order);

            // Send email to customer with status update and receipt
            \Illuminate\Support\Facades\Log::info('OrderController: Sending email notification');
            Mail::to($order->user->email)->send(new OrderStatusUpdated($order, $oldStatus, $newStatus));

            \Illuminate\Support\Facades\Log::info('OrderController: Email sent successfully');
            return back()->with('success', 'Order status updated to ' . ucfirst($newStatus) . ' and notification email sent to customer.');
        } catch (\Exception $e) {
            // Log the error
            \Illuminate\Support\Facades\Log::error('Error updating order status: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
