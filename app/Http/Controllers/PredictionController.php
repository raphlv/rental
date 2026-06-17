<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ML\DataPreprocessor;
use App\ML\RandomForest;
use App\ML\SVM;

class PredictionController extends Controller
{
    public function index()
    {
        $isTrained = session()->has('trained_scaler') && session()->has('trained_rf') && session()->has('trained_svm');
        $predictionResult = session('prediction_result', null);
        
        return view('prediction', compact('isTrained', 'predictionResult'));
    }

    /**
     * Perform classification prediction on manual inputs.
     */
    public function predict(Request $request)
    {
        $request->validate([
            'tavg' => 'required|numeric|between:10,50',
            'rh_avg' => 'required|numeric|between:10,100'
        ]);

        if (!session()->has('trained_scaler') || !session()->has('trained_rf') || !session()->has('trained_svm')) {
            return back()->with('error', 'Silakan latih model terlebih dahulu di tab Pelatihan Model.');
        }

        $tavg = (double)$request->input('tavg');
        $rh_avg = (double)$request->input('rh_avg');

        try {
            // 1. Deserialize models and preprocessor
            $preprocessor = unserialize(session('trained_scaler'));
            $rf = unserialize(session('trained_rf'));
            $svm = unserialize(session('trained_svm'));

            // 2. Preprocess input (Standardization using training scaling parameters)
            $input = [[$tavg, $rh_avg]];
            $scaledInput = $preprocessor->transform($input);
            $x_scaled = $scaledInput[0];

            // 3. Classify
            $rf_class = $rf->predictSample($x_scaled);
            $svm_class = $svm->predictSample($x_scaled);

            // Get confidences/scores
            $rf_proba = $rf->predictProba($x_scaled);
            $rf_confidence = round(($rf_proba[$rf_class] ?? 0.0) * 100, 1);

            $svm_score = $svm->decisionFunction($x_scaled);

            // 4. Determine Alert Level and Mitigation Advice
            $alertLevel = 'AMAN';
            $alertColor = 'emerald';
            $recommendations = [];

            if ($rf_class == 1 && $svm_class == 1) {
                $alertLevel = 'BAHAYA / EKSTREM';
                $alertColor = 'red';
                $recommendations = [
                    'Siapkan tas siaga bencana (dokumen penting, pakaian, obat-obatan, senter, makanan instan).',
                    'Pantau terus informasi cuaca real-time dari aplikasi/situs resmi BMKG.',
                    'Bersihkan saluran pembuangan air di sekitar tempat tinggal untuk mencegah banjir genangan.',
                    'Bagi warga di lereng bukit/pegunungan, segera mengungsi ke tempat aman jika hujan lebat berlangsung lebih dari 2 jam berturut-turut (potensi tanah longsor).',
                    'Hindari berkendara di jalan raya yang rawan banjir atau di bawah pohon besar/baliho yang berpotensi tumbang.'
                ];
            } elseif ($rf_class == 1 || $svm_class == 1) {
                $alertLevel = 'WASPADA';
                $alertColor = 'amber';
                $recommendations = [
                    'Periksa kembali kondisi atap rumah dan pastikan tidak ada kebocoran besar.',
                    'Pastikan got/drainase di depan rumah bebas dari sumbatan sampah.',
                    'Batasi aktivitas di luar ruangan, terutama berkendara saat hujan mulai deras.',
                    'Tetap waspada terhadap perubahan cuaca mendadak dan ikuti perkembangan informasi BMKG.'
                ];
            } else {
                $alertLevel = 'NORMAL / AMAN';
                $alertColor = 'emerald';
                $recommendations = [
                    'Kondisi cuaca terpantau kondusif dan curah hujan diprediksi berada pada ambang batas normal.',
                    'Tetap lakukan pemeliharaan rutin drainase lingkungan secara berkala.',
                    'Gunakan waktu ini untuk bersosialisasi mengenai mitigasi bencana bersama warga setempat.'
                ];
            }

            $predictionResult = [
                'input' => [
                    'tavg' => $tavg,
                    'rh_avg' => $rh_avg
                ],
                'rf' => [
                    'class' => $rf_class, // 0 or 1
                    'confidence' => $rf_confidence
                ],
                'svm' => [
                    'class' => $svm_class, // 0 or 1
                    'score' => round($svm_score, 4)
                ],
                'alert' => [
                    'level' => $alertLevel,
                    'color' => $alertColor,
                    'recommendations' => $recommendations
                ]
            ];

            return back()->with('prediction_result', $predictionResult);

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat memproses prediksi: ' . $e->getMessage());
        }
    }
}
