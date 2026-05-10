<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\Refund;
use App\Contracts\PaymentGatewayInterface;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.donations');
    }

    public function show($id)
    {
        $donation = Donation::with(['user', 'campaign'])->findOrFail($id);
        return view('admin.donations', compact('donation'));
    }

    public function refund(Request $request, $id, PaymentGatewayInterface $gateway)
    {
        $request->validate([
            'refund_amount' => 'required|numeric|min:1',
            'reason' => 'required|string',
        ]);

        $donation = Donation::findOrFail($id);

        if ($donation->status !== 'completed' || $donation->isRefunded()) {
            return back()->with('error', 'Donation cannot be refunded.');
        }

        if ($request->refund_amount > $donation->amount) {
            return back()->with('error', 'Refund amount cannot exceed donation amount.');
        }

        $transactionId = $donation->stripe_payment_intent_id ?? $donation->gateway_transaction_id;

        if (!$transactionId) {
            // Process as a manual, local refund
            $donation->update([
                'status' => 'refunded',
                'refunded_at' => now(),
            ]);

            Refund::create([
                'donation_id' => $donation->id,
                'user_id' => auth()->id(),
                'amount' => (float) $request->refund_amount,
                'currency' => $donation->currency,
                'reason' => $request->reason,
                'status' => 'completed',
            ]);

            event(new \App\Events\RefundIssued($donation, ['note' => 'Manual local refund']));

            return back()->with('success', 'Local refund recorded successfully (No gateway API call was made).');
        }

        $result = $gateway->refundCharge(
            $transactionId,
            (float) $request->refund_amount,
            $request->reason
        );

        if ($result['status'] === 'success') {
            $donation->update([
                'status' => 'refunded',
                'refunded_at' => now(),
                'gateway_refund_id' => $result['gateway_refund_id'] ?? null,
            ]);

            Refund::create([
                'donation_id' => $donation->id,
                'user_id' => auth()->id(),
                'amount' => (float) $request->refund_amount,
                'currency' => $donation->currency,
                'reason' => $request->reason,
                'gateway_refund_id' => $result['gateway_refund_id'] ?? null,
                'status' => 'completed',
            ]);

            event(new \App\Events\RefundIssued($donation, $result['data'] ?? []));

            return back()->with('success', 'Refund processed successfully.');
        }

        return back()->with('error', 'Refund failed: ' . ($result['message'] ?? 'Unknown error'));
    }
}
