<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::orderBy('order')->get();
        $query = MenuItem::with('category');

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('is_available', $request->status === 'available');
        }

        if ($request->filled('search')) {
            $searchTerm = strtolower(str_replace(' ', '', $request->search));
            $query->whereRaw("LOWER(REPLACE(name, ' ', '')) LIKE ?", ["%{$searchTerm}%"]);
        }

        switch ($request->input('sort')) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('name', 'asc');
                break;
        }

        $menuItems = $query->get();

        return view('admin.menu.index', compact('menuItems', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'labels' => 'nullable|array',
            'labels.*' => 'string|in:best_seller,recommended,vegetarian,spicy,halal',
            'is_available' => 'nullable',
            'estimated_time' => 'nullable|integer|min:1',
        ]);

        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(5);
        $validated['is_available'] = $request->has('is_available');
        $validated['labels'] = $request->input('labels', []);
        $validated['estimated_time'] = $request->input('estimated_time', 15);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('menu', 'public');
        }

        MenuItem::create($validated);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Menu berhasil ditambahkan!']);
        }

        return role_redirect('admin.menu.index')
            ->with('success', 'Menu berhasil ditambahkan!');
    }

    public function update(Request $request, MenuItem $menuItem)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'labels' => 'nullable|array',
            'labels.*' => 'string|in:best_seller,recommended,vegetarian,spicy,halal',
            'is_available' => 'nullable',
            'estimated_time' => 'nullable|integer|min:1',
        ]);

        $validated['is_available'] = $request->has('is_available');
        $validated['labels'] = $request->input('labels', []);
        $validated['estimated_time'] = $request->input('estimated_time', 15);

        if ($request->hasFile('image')) {

            if ($menuItem->image) {
                Storage::disk('public')->delete($menuItem->image);
            }
            $validated['image'] = $request->file('image')->store('menu', 'public');
        }

        $menuItem->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Menu berhasil diperbarui!']);
        }

        return role_redirect('admin.menu.index')
            ->with('success', 'Menu berhasil diperbarui!');
    }

    public function destroy(Request $request, MenuItem $menuItem)
    {
        if ($menuItem->image) {
            Storage::disk('public')->delete($menuItem->image);
        }

        $menuItem->delete();

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Menu berhasil dihapus!']);
        }

        return role_redirect('admin.menu.index')
            ->with('success', 'Menu berhasil dihapus!');
    }

    public function toggleAvailability(MenuItem $menuItem)
    {
        $menuItem->update(['is_available' => !$menuItem->is_available]);

        return response()->json([
            'success' => true,
            'is_available' => $menuItem->is_available,
        ]);
    }
}