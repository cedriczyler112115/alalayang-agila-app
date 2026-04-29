<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SubscriptionPaymentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'receipt' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        if ($request->hasFile('receipt')) {
            $path = $request->file('receipt')->store('receipts', 'public');

            auth()->user()->subscriptionPayments()->create([
                'receipt_path' => $path,
                'status' => 'pending',
            ]);

            return redirect()->route('dashboard')->with('status', 'Your payment proof has been submitted and is currently under review.');
        }

        return back()->withErrors(['receipt' => 'Failed to upload receipt.']);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'access_type_id' => 'nullable|exists:access_types,id',
        ]);

        $payment = SubscriptionPayment::findOrFail($id);
        $payment->status = $request->status;
        $payment->save();

        if ($request->status === 'approved' && $request->filled('access_type_id')) {
            $payment->user->update([
                'access_type_id' => $request->access_type_id
            ]);
        }

        return back()->with('status', 'Subscription payment status updated successfully.');
    }
}
