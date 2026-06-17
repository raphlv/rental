<?php

namespace App\Http\Controllers;

use App\Models\RainfallData;
use App\ML\DataPreprocessor;
use App\ML\RandomForest;
use App\ML\SVM;
use Illuminate\Http\Request;

class ModelController extends Controller
{
    public function index()
    {
        $totalRecords = RainfallData::count();
        $isTrained = session()->has('trained_scaler');
        $savedMetrics = session('model_metrics', null);

        return view('training', compact('totalRecords', 'isTrained', 'savedMetrics'));
    }

    /**
     * Train models and return evaluation results.
     */
    public function train(Request $request)
    {
        $request->validate([
            'rf_trees' => 'required|integer|min:2|max:100',
            'rf_max_depth' => 'required|integer|min:1|max:30',
            'rf_min_split' => 'required|integer|min:2|max:10',
            'svm_c' => 'required|numeric|min:0.01|max:100',
            'svm_kernel' => 'required|string|in:linear,rbf',
            'svm_gamma' => 'required|numeric|min:0.01|max:10'
        ]);

        $data = RainfallData::all();
        $totalCount = $data->count();

        if ($totalCount < 10) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak mencukupi untuk pelatihan. Diperlukan minimal 10 data.'
            ], 400);
        }

        // 1. Prepare features and targets
        $X = [];
        $y = [];
        foreach ($data as $row) {
            $X[] = [(double)$row->tavg, (double)$row->rh_avg];
            $y[] = (int)$row->class_actual;
        }

        // 2. Impute and Split
        $preprocessor = new DataPreprocessor();
        // Impute inside arrays if any NaN exists
        $tempData = [];
        for ($i = 0; $i < $totalCount; $i++) {
            $tempData[] = [
                'tavg' => $X[$i][0],
                'rh_avg' => $X[$i][1]
            ];
        }
        $preprocessor->imputeMissing($tempData);
        for ($i = 0; $i < $totalCount; $i++) {
            $X[$i] = [$tempData[$i]['tavg'], $tempData[$i]['rh_avg']];
        }

        list($X_train, $X_test, $y_train, $y_test) = $preprocessor->trainTestSplit($X, $y, 0.2);

        if (count($X_train) === 0 || count($X_test) === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Pembagian data latih/uji menghasilkan data kosong.'
            ], 400);
        }

        // 3. Scale Features (Z-Score)
        // Note: For Random Forest, scaling is optional (trees are scale-invariant), 
        // but for SVM, scaling is CRITICAL. We will use scaled features for both or train RF on scaled as well.
        $X_train_scaled = $preprocessor->fitTransform($X_train);
        $X_test_scaled = $preprocessor->transform($X_test);

        // 4. Train Random Forest
        $rf_trees = (int)$request->input('rf_trees');
        $rf_max_depth = (int)$request->input('rf_max_depth');
        $rf_min_split = (int)$request->input('rf_min_split');

        $rf_start = microtime(true);
        $rf = new RandomForest($rf_trees, $rf_max_depth, $rf_min_split);
        $rf->train($X_train_scaled, $y_train);
        $rf_time = round((microtime(true) - $rf_start) * 1000, 2); // ms

        // 5. Train SVM
        $svm_c = (double)$request->input('svm_c');
        $svm_kernel = $request->input('svm_kernel');
        $svm_gamma = (double)$request->input('svm_gamma');

        $svm_start = microtime(true);
        $svm = new SVM($svm_c, $svm_kernel, $svm_gamma, 0.001, 15);
        $svm->train($X_train_scaled, $y_train);
        $svm_time = round((microtime(true) - $svm_start) * 1000, 2); // ms

        // 6. Evaluate Models
        $rf_metrics = $this->evaluateModel($rf, $X_test_scaled, $y_test);
        $svm_metrics = $this->evaluateModel($svm, $X_test_scaled, $y_test);

        // Add training times
        $rf_metrics['time'] = $rf_time;
        $svm_metrics['time'] = $svm_time;

        // 7. Calculate ROC Curves
        $rf_roc = $this->calculateRF_ROC($rf, $X_test_scaled, $y_test);
        $svm_roc = $this->calculateSVM_ROC($svm, $X_test_scaled, $y_test);

        // 8. Save Models in Session (Serialized)
        session([
            'trained_scaler' => serialize($preprocessor),
            'trained_rf' => serialize($rf),
            'trained_svm' => serialize($svm),
            'model_metrics' => [
                'rf' => $rf_metrics,
                'svm' => $svm_metrics,
                'rf_roc' => $rf_roc,
                'svm_roc' => $svm_roc,
                'params' => [
                    'rf_trees' => $rf_trees,
                    'rf_max_depth' => $rf_max_depth,
                    'svm_c' => $svm_c,
                    'svm_kernel' => $svm_kernel,
                    'svm_gamma' => $svm_gamma
                ]
            ]
        ]);

        return response()->json([
            'success' => true,
            'metrics' => [
                'rf' => $rf_metrics,
                'svm' => $svm_metrics
            ],
            'roc' => [
                'rf' => $rf_roc,
                'svm' => $svm_roc
            ]
        ]);
    }

    /**
     * Compute performance metrics and confusion matrix.
     */
    private function evaluateModel($model, $X_test, $y_test)
    {
        $predictions = $model->predict($X_test);
        $count = count($y_test);

        $tp = 0; $fp = 0; $tn = 0; $fn = 0;

        for ($i = 0; $i < $count; $i++) {
            $act = $y_test[$i];
            $pred = $predictions[$i];

            if ($act == 1 && $pred == 1) $tp++;
            if ($act == 0 && $pred == 1) $fp++;
            if ($act == 0 && $pred == 0) $tn++;
            if ($act == 1 && $pred == 0) $fn++;
        }

        $accuracy = $count > 0 ? ($tp + $tn) / $count : 0.0;
        $precision = ($tp + $fp) > 0 ? $tp / ($tp + $fp) : 0.0;
        $recall = ($tp + $fn) > 0 ? $tp / ($tp + $fn) : 0.0;
        $f1 = ($precision + $recall) > 0 ? 2 * ($precision * $recall) / ($precision + $recall) : 0.0;

        return [
            'accuracy' => round($accuracy * 100, 2),
            'precision' => round($precision * 100, 2),
            'recall' => round($recall * 100, 2),
            'f1_score' => round($f1 * 100, 2),
            'confusion_matrix' => [
                'tn' => $tn,
                'fp' => $fp,
                'fn' => $fn,
                'tp' => $tp
            ]
        ];
    }

    /**
     * Generate ROC points for Random Forest
     */
    private function calculateRF_ROC($rf, $X_test, $y_test)
    {
        $scores = [];
        $count = count($y_test);
        
        for ($i = 0; $i < $count; $i++) {
            $prob = $rf->predictProba($X_test[$i]);
            $scores[] = [
                'score' => $prob[1] ?? 0.0,
                'label' => $y_test[$i]
            ];
        }

        return $this->generateROCPoints($scores);
    }

    /**
     * Generate ROC points for SVM
     */
    private function calculateSVM_ROC($svm, $X_test, $y_test)
    {
        $scores = [];
        $count = count($y_test);
        
        for ($i = 0; $i < $count; $i++) {
            $score = $svm->decisionFunction($X_test[$i]);
            $scores[] = [
                'score' => $score,
                'label' => $y_test[$i]
            ];
        }

        return $this->generateROCPoints($scores);
    }

    /**
     * Calculate ROC Curve (FPR and TPR coordinates)
     */
    private function generateROCPoints($scores)
    {
        // Sort scores descending
        usort($scores, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        $totalPos = 0;
        $totalNeg = 0;
        foreach ($scores as $s) {
            if ($s['label'] == 1) $totalPos++;
            else $totalNeg++;
        }

        $points = [];
        $points[] = ['fpr' => 0.0, 'tpr' => 0.0];

        if ($totalPos === 0 || $totalNeg === 0) {
            $points[] = ['fpr' => 1.0, 'tpr' => 1.0];
            return $points;
        }

        $tp = 0;
        $fp = 0;
        $prevScore = null;

        foreach ($scores as $s) {
            if ($s['score'] !== $prevScore && $prevScore !== null) {
                $points[] = [
                    'fpr' => round($fp / $totalNeg, 3),
                    'tpr' => round($tp / $totalPos, 3)
                ];
            }
            if ($s['label'] == 1) {
                $tp++;
            } else {
                $fp++;
            }
            $prevScore = $s['score'];
        }

        $points[] = ['fpr' => 1.0, 'tpr' => 1.0];

        // Deduplicate and reduce points if too many (for smooth Chart.js rendering)
        $uniquePoints = [];
        foreach ($points as $p) {
            $key = $p['fpr'] . '-' . $p['tpr'];
            $uniquePoints[$key] = $p;
        }

        $finalPoints = array_values($uniquePoints);
        
        // Downsample if more than 30 points to avoid chart clutter
        $numPoints = count($finalPoints);
        if ($numPoints > 30) {
            $downsampled = [];
            $downsampled[] = $finalPoints[0];
            $step = ($numPoints - 2) / 28;
            for ($i = 1; $i <= 28; $i++) {
                $idx = (int)round($i * $step);
                if (isset($finalPoints[$idx])) {
                    $downsampled[] = $finalPoints[$idx];
                }
            }
            $downsampled[] = $finalPoints[$numPoints - 1];
            return $downsampled;
        }

        return $finalPoints;
    }
}
