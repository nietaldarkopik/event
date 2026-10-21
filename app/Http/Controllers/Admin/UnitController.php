<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        $units = Unit::with('parent')->orderBy('type')->orderBy('name')->get();
        $treeUnits = Unit::where(function($query){
            $query->whereNull('parent_id');
            $query->orWhere('parent_id','=',0);
        })->with('children')->orderBy('type')->orderBy('name')->get();
        return view('admin.units.index', compact('units', 'treeUnits'));
    }

    public function create()
    {
        $parents = Unit::orderBy('name')->get();
        return view('admin.units.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'type' => 'required|in:rektorat,fakultas,prodi',
            'parent_id' => 'nullable|exists:units,id',
        ]);

        Unit::create([
            'name' => $request->name,
            'code' => $request->code,
            'type' => $request->type,
            'parent_id' => $request->parent_id,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('units.index')->with('success', 'Unit berhasil ditambahkan.');
    }

    public function edit(Unit $unit)
    {
        $parents = Unit::where('id', '!=', $unit->id)->orderBy('name')->get();
        return view('admin.units.edit', compact('unit', 'parents'));
    }

    public function update(Request $request, Unit $unit)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'type' => 'required|in:rektorat,fakultas,prodi',
            'parent_id' => 'nullable|exists:units,id',
        ]);

        $unit->update([
            'name' => $request->name,
            'code' => $request->code,
            'type' => $request->type,
            'parent_id' => $request->parent_id,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('units.index')->with('success', 'Unit berhasil diperbarui.');
    }

    public function destroy(Unit $unit)
    {
        $unit->delete();

        return redirect()->route('units.index')->with('success', 'Unit berhasil dihapus.');
    }

    public function show(Unit $unit)
    {
        return redirect()->route('units.index');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:units,id',
            'parent_id' => 'nullable|integer',
        ]);

        $id = (int) $request->id;
        $parentId = $request->parent_id !== null ? (int) $request->parent_id : null;

        if ($parentId === 0) {
            $parentId = null;
        }

        if ($id === $parentId) {
            return response()->json([
                'success' => false,
                'message' => 'Unit tidak boleh menjadi child dirinya sendiri.',
            ], 422);
        }

        if ($parentId !== null && !Unit::where('id', $parentId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Parent unit tidak ditemukan.',
            ], 422);
        }

        $unit = Unit::findOrFail($id);
        $unit->parent_id = $parentId;
        $unit->save();

        return response()->json(['success' => true]);
    }
}
