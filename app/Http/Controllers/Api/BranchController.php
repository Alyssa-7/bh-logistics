<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        return response()->json(Branch::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'lat' => 'nullable|numeric|between:-90,90',
            'lng' => 'nullable|numeric|between:-180,180',
        ]);

        $branch = Branch::create($request->only(['name', 'address', 'lat', 'lng']));

        return response()->json($branch, 201);
    }

    public function update(Request $request, Branch $branch)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'address' => 'sometimes|nullable|string',
            'lat' => 'sometimes|nullable|numeric|between:-90,90',
            'lng' => 'sometimes|nullable|numeric|between:-180,180',
            'is_active' => 'sometimes|boolean',
        ]);

        $branch->update($request->only(['name', 'address', 'lat', 'lng', 'is_active']));

        return response()->json($branch);
    }

    public function destroy(Branch $branch)
    {
        $branch->delete();

        return response()->json(['message' => 'Branch deleted successfully.']);
    }
}
