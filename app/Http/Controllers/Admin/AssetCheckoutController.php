<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetCheckout;
use App\Models\User;
use App\Models\VisitorVisit;
use Illuminate\Http\Request;

class AssetCheckoutController extends Controller
{
    public function create(Asset $asset)
    {
        if ($asset->status !== 'available') {
            return redirect()->back()->with('error', 'Asset is not available for checkout.');
        }

        $users = User::where('is_active', true)->orderBy('name')->get();
        $activeVisits = VisitorVisit::with('visitor')
            ->where('status', 'active')
            ->latest('check_in_time')
            ->get();

        return view('admin.assets.checkout', compact('asset', 'users', 'activeVisits'));
    }

    public function store(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'checkout_type' => 'required|in:staff,visitor',
            'user_id' => 'required_if:checkout_type,staff|nullable|exists:users,id',
            'visitor_visit_id' => 'required_if:checkout_type,visitor|nullable|exists:visitor_visits,id',
            'expected_return_time' => 'required|date|after:now',
            'checkout_condition' => 'required|in:excellent,good,fair,poor,damaged',
            'checkout_notes' => 'nullable|string',
        ]);

        if ($asset->status !== 'available') {
            return redirect()->back()->with('error', 'Asset is not available for checkout.');
        }

        $requiresApproval = $asset->category->requires_approval;

        $checkout = AssetCheckout::create([
            'asset_id' => $asset->id,
            'user_id' => $validated['checkout_type'] === 'staff' ? $validated['user_id'] : null,
            'visitor_visit_id' => $validated['checkout_type'] === 'visitor' ? $validated['visitor_visit_id'] : null,
            'checked_out_by' => auth()->id(),
            'checkout_time' => now(),
            'expected_return_time' => $validated['expected_return_time'],
            'checkout_condition' => $validated['checkout_condition'],
            'checkout_notes' => $validated['checkout_notes'] ?? null,
            'status' => $requiresApproval ? 'pending_approval' : 'checked_out',
        ]);

        if (!$requiresApproval) {
            $asset->update(['status' => 'checked_out']);
        }

        return redirect()->route('admin.assets.show', $asset)
            ->with('success', $requiresApproval ? 'Checkout request submitted for approval.' : 'Asset checked out successfully!');
    }

    public function approve(AssetCheckout $checkout)
    {
        if ($checkout->status !== 'pending_approval') {
            return redirect()->back()->with('error', 'Checkout is not pending approval.');
        }

        $checkout->update([
            'status' => 'checked_out',
            'approved_by' => auth()->id(),
        ]);

        $checkout->asset->update(['status' => 'checked_out']);

        return redirect()->back()->with('success', 'Checkout approved successfully!');
    }

    public function returnForm(AssetCheckout $checkout)
    {
        if ($checkout->status !== 'checked_out') {
            return redirect()->back()->with('error', 'Asset is not currently checked out.');
        }

        return view('admin.assets.return', compact('checkout'));
    }

    public function return(Request $request, AssetCheckout $checkout)
    {
        $validated = $request->validate([
            'return_condition' => 'required|in:excellent,good,fair,poor,damaged',
            'return_notes' => 'nullable|string',
        ]);

        $checkout->update([
            'actual_return_time' => now(),
            'return_condition' => $validated['return_condition'],
            'return_notes' => $validated['return_notes'] ?? null,
            'status' => 'returned',
            'returned_by' => auth()->id(),
        ]);

        // Update asset status based on condition
        $assetStatus = match ($validated['return_condition']) {
            'damaged' => 'damaged',
            'poor' => 'maintenance',
            default => 'available',
        };

        $checkout->asset->update(['status' => $assetStatus]);

        return redirect()->route('admin.assets.show', $checkout->asset)
            ->with('success', 'Asset returned successfully!');
    }
}
