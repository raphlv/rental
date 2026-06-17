<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Unit;
use App\Models\Rental;
use Illuminate\Support\Facades\File;

class RentalController extends Controller
{
    public function start(Request $request)
    {
        $request->validate([
            'unit_id' => 'required|exists:units,id',
            'customer_name' => 'required|string|max:255',
            'duration' => 'required|integer|min:1',
            'payment_method' => 'required|in:Cash,Transfer,QRIS',
            'photo_proof' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // Max 5MB
        ]);

        $unit = Unit::findOrFail($request->unit_id);
        if ($unit->status !== 'ada') {
            return redirect()->back()->with('error', 'Unit sedang tidak tersedia (disewa/maintenance).');
        }

        // Handle photo upload
        $photoName = null;
        if ($request->hasFile('photo_proof')) {
            $file = $request->file('photo_proof');
            $photoName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            // Ensure target directory exists in public path
            $targetDir = public_path('uploads/proofs');
            if (!File::exists($targetDir)) {
                File::makeDirectory($targetDir, 0755, true);
            }
            
            $file->move($targetDir, $photoName);
        }

        // Calculations
        $startTime = now();
        $endTime = now()->addHours($request->duration);
        $totalPrice = $request->duration * $unit->price_per_hour;

        // Create transaction
        Rental::create([
            'unit_id' => $unit->id,
            'customer_name' => $request->customer_name,
            'duration' => $request->duration,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'payment_method' => $request->payment_method,
            'photo_proof' => $photoName ? 'uploads/proofs/' . $photoName : null,
            'status' => 'active',
            'total_price' => $totalPrice,
        ]);

        // Update unit status
        $unit->update(['status' => 'disewa']);

        return redirect()->back()->with('success', 'Rental berhasil dimulai untuk unit ' . $unit->name);
    }

    public function complete(Request $request, $id)
    {
        $rental = Rental::findOrFail($id);
        $rental->update([
            'status' => 'completed',
            'end_time' => now() // Record exact actual end time
        ]);

        // Free up the unit
        $unit = $rental->unit;
        if ($unit) {
            $unit->update(['status' => 'ada']);
        }

        return redirect()->back()->with('success', 'Rental untuk ' . $rental->customer_name . ' telah selesai.');
    }
}
