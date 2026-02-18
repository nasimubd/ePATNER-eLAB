<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Skip check for super admin or users without business
        if (!$user || !$user->business_id) {
            return $next($request);
        }

        $business = $user->business;

        // Skip check for payment-related routes to avoid redirect loop
        if ($this->isPaymentRoute($request)) {
            return $next($request);
        }

        // Check if subscription is expired
        if ($business && $business->isSubscriptionExpired()) {
            // Mark business as inactive if not already
            if ($business->is_active) {
                $business->deactivateSubscription();
            }

            // Redirect to payment page
            return redirect()->route('business.payment.form')
                ->with('error', 'Your subscription has expired. Please make a payment to continue using the system.');
        }

        return $next($request);
    }

    /**
     * Check if current route is payment-related
     */
    private function isPaymentRoute(Request $request): bool
    {
        $currentRoute = $request->route()?->getName();

        $paymentRoutes = [
            'business.payment.form',
            'business.payment.submit',
            'logout',
        ];

        return in_array($currentRoute, $paymentRoutes);
    }
}
