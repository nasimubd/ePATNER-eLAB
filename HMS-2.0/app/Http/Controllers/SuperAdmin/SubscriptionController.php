<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Payment;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SubscriptionController extends Controller
{
    /**
     * Show subscription settings
     */
    public function settings()
    {
        $monthlyFee = Setting::get('monthly_fee', 500.00);
        $paymentQrPath = Setting::get('payment_qr_path', 'images/Payment-QR.png');

        return view('super-admin.subscriptions.settings', compact('monthlyFee', 'paymentQrPath'));
    }

    /**
     * Update subscription settings
     */
    public function updateSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'monthly_fee' => 'required|numeric|min:0',
            'payment_qr_path' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        Setting::set('monthly_fee', $request->monthly_fee, 'decimal', 'Monthly subscription fee for businesses');
        Setting::set('payment_qr_path', $request->payment_qr_path ?? 'images/Payment-QR.png', 'string', 'Path to payment QR code image');

        return back()->with('success', 'Subscription settings updated successfully.');
    }

    /**
     * Show pending payments
     */
    public function pendingPayments()
    {
        $payments = Payment::with('business')
            ->pending()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('super-admin.subscriptions.pending-payments', compact('payments'));
    }

    /**
     * Approve payment
     */
    public function approvePayment(Request $request, Payment $payment)
    {
        if ($payment->status !== 'pending') {
            return back()->with('error', 'Payment is not in pending status.');
        }

        $payment->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        // Extend business subscription
        $business = $payment->business;
        $business->extendSubscription($payment->months_paid);

        return back()->with('success', 'Payment approved and subscription extended successfully.');
    }

    /**
     * Reject payment
     */
    public function rejectPayment(Request $request, Payment $payment)
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $payment->update([
            'status' => 'rejected',
            'notes' => $request->reason,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Payment rejected successfully.');
    }

    /**
     * Show all businesses with subscription info
     */
    public function businesses()
    {
        $businesses = Business::with('payments')
            ->orderBy('hospital_name')
            ->paginate(20);

        return view('super-admin.subscriptions.businesses', compact('businesses'));
    }

    /**
     * Manually update business subscription
     */
    public function updateBusinessSubscription(Request $request, $businessId)
    {
        $business = Business::findOrFail($businessId);

        $validator = Validator::make($request->all(), [
            'due_date' => 'nullable|date|after:today',
            'custom_monthly_fee' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $business->update([
            'due_date' => $request->due_date,
            'custom_monthly_fee' => $request->custom_monthly_fee,
            'is_active' => $request->has('is_active'),
        ]);

        return back()->with('success', 'Business subscription updated successfully.');
    }

    /**
     * Show payment history
     */
    public function paymentHistory()
    {
        $payments = Payment::with(['business', 'approvedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('super-admin.subscriptions.payment-history', compact('payments'));
    }
}
