<?php

namespace App\Http\Controllers;

use App\Models\RainfallData;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RainfallController extends Controller
{
    public function index()
    {
        $dataList = RainfallData::orderBy('tanggal', 'desc')->paginate(15);
        $totalRecords = RainfallData::count();
        return view('data', compact('dataList', 'totalRecords'));
    }

    /**
     * Upload CSV file from BMKG
     */
    public function upload(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
            'threshold' => 'required|numeric|min:1'
        ]);

        $file = $request->file('csv_file');
        $filePath = $file->getRealPath();
        
        $threshold = (double)$request->input('threshold');
        
        $fileHandle = fopen($filePath, 'r');
        $header = fgetcsv($fileHandle, 1000, ';'); // BMKG often uses semicolon or comma
        if (count($header) === 1) {
            // Retry with comma
            rewind($fileHandle);
            $header = fgetcsv($fileHandle, 1000, ',');
            $delimiter = ',';
        } else {
            $delimiter = ';';
        }

        // Clean headers (remove BOM if present, trim whitespace, lowercase)
        $header = array_map(function($h) {
            $h = preg_replace('/[\x{00FF}-\x{0000}]/u', '', $h);
            return strtolower(trim($h, " \t\n\r\0\x0B\"'"));
        }, $header);

        // Find column indices
        $dateIdx = $this->findHeaderIndex($header, ['tanggal', 'date', 'time', 'yymmdd']);
        $tavgIdx = $this->findHeaderIndex($header, ['tavg', 'temperature', 'suhu', 't_avg']);
        $rhIdx = $this->findHeaderIndex($header, ['rh_avg', 'rh', 'humidity', 'kelembapan', 'rh_avg']);
        $rrIdx = $this->findHeaderIndex($header, ['rr', 'rainfall', 'curah hujan', 'curahhujan']);

        if ($dateIdx === -1 || $rrIdx === -1) {
            fclose($fileHandle);
            return back()->with('error', 'Format CSV tidak dikenali. Kolom Tanggal dan RR (Curah Hujan) wajib ada.');
        }

        $insertedCount = 0;
        $updatedCount = 0;

        // Start transaction for speed
        \DB::beginTransaction();

        try {
            while (($row = fgetcsv($fileHandle, 1000, $delimiter)) !== false) {
                if (count($row) <= max($dateIdx, $rrIdx)) continue;

                $rawDate = trim($row[$dateIdx]);
                if (empty($rawDate)) continue;

                // Parse Date
                try {
                    $tanggal = Carbon::parse($rawDate)->format('Y-m-d');
                } catch (\Exception $e) {
                    continue; // Skip invalid dates
                }

                // Parse values, handle "8888" (missing data in BMKG), "9999" (not measured), and nulls
                $tavg = $tavgIdx !== -1 ? $this->cleanBMKGValue($row[$tavgIdx]) : null;
                $rh = $rhIdx !== -1 ? $this->cleanBMKGValue($row[$rhIdx]) : null;
                $rr = $this->cleanBMKGValue($row[$rrIdx]);

                if ($rr === null) continue; // Target class cannot be null

                $classActual = $rr >= $threshold ? 1 : 0;

                // Insert or update
                $existing = RainfallData::where('tanggal', $tanggal)->first();
                if ($existing) {
                    $existing->update([
                        'tavg' => $tavg,
                        'rh_avg' => $rh,
                        'rr' => $rr,
                        'class_actual' => $classActual
                    ]);
                    $updatedCount++;
                } else {
                    RainfallData::create([
                        'tanggal' => $tanggal,
                        'tavg' => $tavg,
                        'rh_avg' => $rh,
                        'rr' => $rr,
                        'class_actual' => $classActual
                    ]);
                    $insertedCount++;
                }
            }
            \DB::commit();
            fclose($fileHandle);
            return back()->with('success', "Berhasil memproses data CSV. {$insertedCount} data baru diimpor, {$updatedCount} data diperbarui.");
        } catch (\Exception $e) {
            \DB::rollBack();
            fclose($fileHandle);
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Generate 2 years of realistic mock weather data.
     */
    public function generateMockData(Request $request)
    {
        $request->validate([
            'threshold' => 'required|numeric|min:1'
        ]);

        $threshold = (double)$request->input('threshold');
        
        // Start from 2 years ago up to today
        $startDate = Carbon::now()->subYears(2);
        $endDate = Carbon::now();
        
        $insertedCount = 0;
        
        \DB::beginTransaction();
        try {
            // Clear existing data to avoid conflicts on generate
            RainfallData::truncate();

            $currentDate = $startDate->copy();
            while ($currentDate->lte($endDate)) {
                $month = $currentDate->month;
                $isWetSeason = ($month >= 10 || $month <= 4); // October to April is rainy season in Indo

                if ($isWetSeason) {
                    // Rainy Season: Lower temp, high humidity, higher probability of heavy rain
                    $tavg = round(rand(240, 275) / 10, 1); // 24.0°C to 27.5°C
                    $rh = round(rand(85, 98));            // 85% to 98%
                    
                    // Rainfall generation
                    $randVal = rand(1, 100);
                    if ($randVal <= 25) {
                        $rr = 0.0; // No rain
                    } elseif ($randVal <= 65) {
                        $rr = round(rand(1, 199) / 10, 1); // Light rain (0.1 - 19.9 mm)
                    } elseif ($randVal <= 85) {
                        $rr = round(rand(200, 499) / 10, 1); // Moderate rain (20 - 49.9 mm)
                    } elseif ($randVal <= 95) {
                        $rr = round(rand(500, 999) / 10, 1); // Heavy rain (50 - 99.9 mm) -> Extreme!
                    } else {
                        $rr = round(rand(1000, 1800) / 10, 1); // Very heavy/extreme rain (100 - 180 mm) -> Extreme!
                    }
                } else {
                    // Dry Season: Higher temp, lower humidity, low probability of rain
                    $tavg = round(rand(270, 330) / 10, 1); // 27.0°C to 33.0°C
                    $rh = round(rand(60, 82));            // 60% to 82%
                    
                    $randVal = rand(1, 100);
                    if ($randVal <= 75) {
                        $rr = 0.0;
                    } elseif ($randVal <= 95) {
                        $rr = round(rand(1, 150) / 10, 1); // Light rain
                    } else {
                        $rr = round(rand(150, 450) / 10, 1); // Moderate rain
                    }
                }

                $classActual = $rr >= $threshold ? 1 : 0;

                RainfallData::create([
                    'tanggal' => $currentDate->format('Y-m-d'),
                    'tavg' => $tavg,
                    'rh_avg' => $rh,
                    'rr' => $rr,
                    'class_actual' => $classActual
                ]);

                $insertedCount++;
                $currentDate->addDay();
            }

            \DB::commit();
            return back()->with('success', "Berhasil menghasilkan {$insertedCount} data simulasi cuaca selama 2 tahun terakhir.");
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Gagal menghasilkan data simulasi: ' . $e->getMessage());
        }
    }

    /**
     * Clear all records in the table.
     */
    public function reset()
    {
        RainfallData::truncate();
        return back()->with('success', 'Database berhasil dikosongkan.');
    }

    /**
     * Find index in headers list.
     */
    private function findHeaderIndex($headerList, $aliases)
    {
        foreach ($headerList as $index => $header) {
            foreach ($aliases as $alias) {
                if (str_contains($header, $alias)) {
                    return $index;
                }
            }
        }
        return -1;
    }

    /**
     * Clean BMKG values (e.g. handle 8888, 9999, non-numeric strings, and commas)
     */
    private function cleanBMKGValue($value)
    {
        $value = trim($value);
        if ($value === '' || $value === null) return null;
        
        // BMKG uses 8888 for missing value and 9999 for trace amounts/undocumented
        if ($value == '8888' || $value == '9999') return 0.0;
        
        // Handle comma decimal separator
        $value = str_replace(',', '.', $value);
        
        if (!is_numeric($value)) return null;
        
        return (double)$value;
    }
}
