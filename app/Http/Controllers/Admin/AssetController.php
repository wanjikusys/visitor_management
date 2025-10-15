<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $query = Asset::with('category');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('asset_code', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('asset_category_id', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $assets = $query->latest()->paginate(20);
        $categories = AssetCategory::where('is_active', true)->get();

        return view('admin.assets.index', compact('assets', 'categories'));
    }

    public function create()
    {
        $categories = AssetCategory::where('is_active', true)->orderBy('name')->get();
        return view('admin.assets.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_category_id' => 'required|exists:asset_categories,id',
            'asset_code' => 'required|string|unique:assets,asset_code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'serial_number' => 'nullable|string|unique:assets,serial_number',
            'barcode' => 'nullable|string|unique:assets,barcode',
            'purchase_price' => 'nullable|numeric|min:0',
            'purchase_date' => 'nullable|date',
            'location' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:2048',
            'specifications' => 'nullable|string',
        ]);

        $validated['asset_code'] = strtoupper($validated['asset_code']);
        $validated['status'] = 'available';

        if ($request->hasFile('photo')) {
            $validated['photo_path'] = $request->file('photo')->store('assets', 'public');
        }

        $asset = Asset::create($validated);

        return redirect()->route('admin.assets.show', $asset)
            ->with('success', 'Asset created successfully!');
    }

    public function show(Asset $asset)
    {
        $asset->load(['category', 'currentCheckout', 'checkouts.checkedOutBy', 'checkouts.returnedBy']);
        
        $checkoutHistory = $asset->checkouts()
            ->with(['user', 'visitorVisit.visitor', 'checkedOutBy', 'returnedBy'])
            ->latest('checkout_time')
            ->paginate(10);

        return view('admin.assets.show', compact('asset', 'checkoutHistory'));
    }

    public function edit(Asset $asset)
    {
        $categories = AssetCategory::where('is_active', true)->orderBy('name')->get();
        return view('admin.assets.edit', compact('asset', 'categories'));
    }

    public function update(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'asset_category_id' => 'required|exists:asset_categories,id',
            'asset_code' => 'required|string|unique:assets,asset_code,' . $asset->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'serial_number' => 'nullable|string|unique:assets,serial_number,' . $asset->id,
            'barcode' => 'nullable|string|unique:assets,barcode,' . $asset->id,
            'purchase_price' => 'nullable|numeric|min:0',
            'purchase_date' => 'nullable|date',
            'status' => 'required|in:available,checked_out,maintenance,retired,lost,damaged',
            'location' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:2048',
            'specifications' => 'nullable|string',
        ]);

        $validated['asset_code'] = strtoupper($validated['asset_code']);

        if ($request->hasFile('photo')) {
            if ($asset->photo_path) {
                Storage::disk('public')->delete($asset->photo_path);
            }
            $validated['photo_path'] = $request->file('photo')->store('assets', 'public');
        }

        $asset->update($validated);

        return redirect()->route('admin.assets.show', $asset)
            ->with('success', 'Asset updated successfully!');
    }

    public function destroy(Asset $asset)
    {
        if ($asset->status === 'checked_out') {
            return redirect()->back()->with('error', 'Cannot delete asset that is currently checked out.');
        }

        if ($asset->photo_path) {
            Storage::disk('public')->delete($asset->photo_path);
        }

        $asset->delete();

        return redirect()->route('admin.assets.index')
            ->with('success', 'Asset deleted successfully!');
    }
}
