<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rental;
use App\Models\Unit;
use App\Models\Customer;
use Carbon\Carbon;

class RentalController extends Controller
{
    public function start(Request $request)
    {
        $request->validate([
            'unit_id' => 'required|exists:units,id',
            'customer_selection' => 'required|string', // 'existing' or 'new'
            'customer_id' => 'nullable|exists:customers,id',
            'customer_name' => 'required_if:customer_selection,new|nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'duration_hours' => 'required|numeric|min:0.5|max:24',
            'payment_method' => 'required|in:Cash,Transfer,QRIS',
            'payment_status' => 'required|in:Lunas,Belum Lunas',
            'photo_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5048',
            'photo_camera' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $unit = Unit::findOrFail($request->unit_id);

        if ($unit->status === 'disewa') {
            return redirect()->back()->with('error', 'Unit PS ini sedang disewa oleh pelanggan lain!');
        }

        $customerId = null;
        $customerName = '';
        $customerPhone = '';
        $customerPhoto = null;

        // Process photo if camera snapshot or file upload provided
        if ($request->filled('photo_camera') && str_starts_with($request->photo_camera, 'data:image')) {
            $customerPhoto = $request->photo_camera;
        } elseif ($request->hasFile('photo_file')) {
            $file = $request->file('photo_file');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $customerPhoto = $file->storeAs('rentals', $filename, 'public');
        }

        if ($request->customer_selection === 'existing' && $request->customer_id) {
            $customer = Customer::findOrFail($request->customer_id);
            $customerId = $customer->id;
            $customerName = $customer->name;
            $customerPhone = $customer->phone;
            if (!$customerPhoto) {
                $customerPhoto = $customer->photo_path;
            }
        } else {
            $customerName = $request->customer_name ?? 'Pelanggan Walk-in';
            $customerPhone = $request->customer_phone;

            // Auto save new customer to database for future quick lookup
            $newCustomer = Customer::create([
                'name' => $customerName,
                'phone' => $customerPhone,
                'photo_path' => $customerPhoto,
            ]);
            $customerId = $newCustomer->id;
        }

        $duration = (float) $request->duration_hours;
        $startTime = now();
        $endTime = (clone $startTime)->addMinutes($duration * 60);
        $totalPrice = $unit->price_per_hour * $duration;

        Rental::create([
            'unit_id' => $unit->id,
            'customer_id' => $customerId,
            'customer_name' => $customerName,
            'customer_phone' => $customerPhone,
            'customer_photo' => $customerPhoto,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'duration_hours' => $duration,
            'price_per_hour' => $unit->price_per_hour,
            'total_price' => $totalPrice,
            'payment_method' => $request->payment_method,
            'payment_status' => $request->payment_status,
            'status' => 'active',
            'notes' => $request->notes,
        ]);

        // Update unit status to disewa
        $unit->update(['status' => 'disewa']);

        return redirect()->back()->with('success', 'Rental berhasil dimulai untuk unit ' . $unit->code . ' (' . $customerName . ')!');
    }

    public function complete($id)
    {
        $rental = Rental::findOrFail($id);
        $rental->update(['status' => 'completed']);

        // Set unit status back to 'ada'
        if ($rental->unit) {
            $rental->unit->update(['status' => 'ada']);
        }

        return redirect()->back()->with('success', 'Rental unit ' . ($rental->unit ? $rental->unit->code : '') . ' telah selesai dan unit siap disewa kembali!');
    }

    public function cancel($id)
    {
        $rental = Rental::findOrFail($id);
        $rental->update(['status' => 'cancelled']);

        if ($rental->unit) {
            $rental->unit->update(['status' => 'ada']);
        }

        return redirect()->back()->with('success', 'Rental telah dibatalkan.');
    }
}
