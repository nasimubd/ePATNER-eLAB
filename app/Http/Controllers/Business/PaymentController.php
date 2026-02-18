<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    /**
     * Show the payment form for inactive businesses
     */
    public function showPaymentForm()
    {
        $user = Auth::user();
        $business = $user->business;

        // Only show form if business is inactive
        if ($business && $business->is_active) {
            return redirect()->route('admin.dashboard')
                ->with('info', 'Your subscription is active. No payment required.');
        }

        return view('admin.payment-form');
    }

    /**
     * Submit payment confirmation
     */
    public function submitPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'months' => 'required|integer|min:1|max:12',
            'amount' => 'required|numeric|min:0',
            'transaction_id' => 'required|string|max:50|unique:payments,transaction_id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = Auth::user();
        $business = $user->business;

        if (!$business) {
            return back()->with('error', 'Business not found.');
        }

        // Verify the amount matches calculation
        $expectedAmount = $business->calculatePaymentAmount($request->months);
        if (abs($request->amount - $expectedAmount) > 0.01) {
            return back()->with('error', 'Amount calculation mismatch. Please refresh and try again.');
        }

        // Create payment record
        Payment::create([
            'business_id' => $business->id,
            'months_paid' => $request->months,
            'amount' => $request->amount,
            'transaction_id' => $request->transaction_id,
            'status' => 'pending',
        ]);

        return back()->with(
            'success',
            'Your payment confirmation has been submitted successfully. ' .
                'AI will review it and activate your subscription within 30 minutes to a few hours.FOR EMERGENCY CLICK ON HELP BUTTON'
        );
    }
}
