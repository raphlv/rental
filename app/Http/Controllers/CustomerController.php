<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('q');

        $query = Customer::withCount('rentals')
            ->withSum('rentals', 'total_price');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('nik_ktp', 'like', "%{$search}%");
            });
        }

        $customers = $query->orderBy('id', 'desc')->paginate(12);

        return view('rental.customers.index', compact('customers', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'nik_ktp' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'photo_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5048',
            'photo_camera' => 'nullable|string', // base64 encoded string from webcam
            'notes' => 'nullable|string',
        ]);

        $photoPath = null;

        // Check if webcam camera snapshot was submitted
        if ($request->filled('photo_camera') && str_starts_with($request->photo_camera, 'data:image')) {
            $photoPath = $request->photo_camera; // Store base64 or save to storage
        } elseif ($request->hasFile('photo_file')) {
            $file = $request->file('photo_file');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('customers', $filename, 'public');
            $photoPath = $path;
        }

        Customer::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'nik_ktp' => $request->nik_ktp,
            'address' => $request->address,
            'photo_path' => $photoPath,
            'notes' => $request->notes,
        ]);

        return redirect()->back()->with('success', 'Data pelanggan "' . $request->name . '" berhasil ditambahkan!');
    }

    public function show($id)
    {
        $customer = Customer::with(['rentals' => function ($q) {
            $q->with('unit')->orderBy('id', 'desc');
        }])->findOrFail($id);

        if (request()->wantsJson()) {
            return response()->json($customer);
        }

        return view('rental.customers.show', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'nik_ktp' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'photo_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5048',
            'photo_camera' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $photoPath = $customer->photo_path;

        if ($request->filled('photo_camera') && str_starts_with($request->photo_camera, 'data:image')) {
            $photoPath = $request->photo_camera;
        } elseif ($request->hasFile('photo_file')) {
            $file = $request->file('photo_file');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('customers', $filename, 'public');
            $photoPath = $path;
        }

        $customer->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'nik_ktp' => $request->nik_ktp,
            'address' => $request->address,
            'photo_path' => $photoPath,
            'notes' => $request->notes,
        ]);

        return redirect()->back()->with('success', 'Data pelanggan "' . $customer->name . '" berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $name = $customer->name;
        $customer->delete();

        return redirect()->back()->with('success', 'Data pelanggan "' . $name . '" telah dihapus.');
    }
}
