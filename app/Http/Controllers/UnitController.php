<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Unit;

class UnitController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:units,code',
            'name' => 'required|string|max:255',
            'type' => 'required|in:PS 3,PS 4,PS 5,Nintendo Switch,TV Only',
            'price_per_hour' => 'required|numeric|min:0',
            'status' => 'required|in:ada,disewa,maintenance',
        ]);

        Unit::create($request->only(['code', 'name', 'type', 'price_per_hour', 'status', 'notes']));

        return redirect()->back()->with('success', 'Unit PlayStation baru berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $unit = Unit::findOrFail($id);

        $request->validate([
            'code' => 'required|string|unique:units,code,' . $id,
            'name' => 'required|string|max:255',
            'type' => 'required|in:PS 3,PS 4,PS 5,Nintendo Switch,TV Only',
            'price_per_hour' => 'required|numeric|min:0',
            'status' => 'required|in:ada,disewa,maintenance',
        ]);

        $unit->update($request->only(['code', 'name', 'type', 'price_per_hour', 'status', 'notes']));

        return redirect()->back()->with('success', 'Data unit ' . $unit->code . ' berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $unit = Unit::findOrFail($id);
        $code = $unit->code;
        $unit->delete();

        return redirect()->back()->with('success', 'Unit ' . $code . ' berhasil dihapus.');
    }
}
