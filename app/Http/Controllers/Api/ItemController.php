<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::orderBy('category')->orderBy('name');

        if ($request->boolean('active')) {
            $query->active();
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'unit'     => 'required|string|max:50',
            'category' => 'nullable|string|max:100',
        ]);

        $item = Item::create([
            'name'      => $request->name,
            'unit'      => $request->unit,
            'category'  => $request->category ?: null,
            'is_active' => true,
        ]);

        return response()->json($item, 201);
    }

    public function update(Request $request, Item $item)
    {
        $request->validate([
            'name'     => 'sometimes|string|max:255',
            'unit'     => 'sometimes|string|max:50',
            'category' => 'nullable|string|max:100',
        ]);

        $item->update($request->only(['name', 'unit', 'category']));

        return response()->json($item);
    }

    public function toggle(Item $item)
    {
        $item->update(['is_active' => !$item->is_active]);

        return response()->json($item);
    }

    public function productNames()
    {
        $user  = auth()->user();
        $query = DB::table('delivery_items as di')
            ->join('delivery_orders as do', 'do.id', '=', 'di.delivery_order_id')
            ->select('di.product_name', 'di.unit')
            ->whereNotNull('di.product_name')
            ->where('di.product_name', '!=', '')
            ->groupBy('di.product_name', 'di.unit')
            ->orderByRaw('MAX(di.created_at) DESC');

        if ($user->role === 'branch' && $user->branch_id) {
            $query->where('do.branch_id', $user->branch_id);
        }

        return response()->json($query->get());
    }
}
