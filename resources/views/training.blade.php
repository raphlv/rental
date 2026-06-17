@extends('layouts.app')

@section('content')
<div class="fade-in">
    <!-- Training Form Configuration Card -->
    <div class="card">
        <h2 class="card-title">
            <i class="fa-solid fa-brain" style="color: var(--color-accent);"></i> Pelatihan Model Machine Learning
        </h2>
        <p style="color: var(--text-secondary); line-height: 1.6; margin-bottom: 2rem;">
            Konfigurasikan hyperparameter untuk masing-masing model di bawah ini, lalu klik tombol latih untuk memulai 
            proses pelatihan dan evaluasi performa model secara real-time.
        </p>

        @if($totalRecords >= 10)
            <form id="train-form" action="{{ route('training.run') }}" method="POST">
                @csrf
                <div class="grid-2">
                    <!-- Random Forest Config -->
                    <div style="background: rgba(255, 255, 255, 0.01); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.5rem; border-top: 3px solid var(--color-rf);">
                        <h3 style="font-size: 1.15rem; font-weight: 600; margin-bottom: 1.25rem; color: var(--color-rf); display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fa-solid fa-tree"></i> Hyperparameter Random Forest
                        </h3>

                        <div class="form-group">
                            <label class="form-label" for="rf_trees">Jumlah Pohon Keputusan (n_estimators)</label>
                            <input type="range" id="rf_trees" name="rf_trees" min="2" max="50" value="{{ $savedMetrics['params']['rf_trees'] ?? 10 }}" class="form-control" style="padding: 0.25rem;" oninput="this.nextElementSibling.innerText = this.value">
                            <span style="font-size: 0.85rem; color: var(--color-rf); font-weight: 600; display: block; margin-top: 0.25rem;">{{ $savedMetrics['params']['rf_trees'] ?? 10 }}</span>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="rf_max_depth">Kedalaman Maksimum Pohon (max_depth)</label>
                            <input type="range" id="rf_max_depth" name="rf_max_depth" min="1" max="20" value="{{ $savedMetrics['params']['rf_max_depth'] ?? 8 }}" class="form-control" style="padding: 0.25rem;" oninput="this.nextElementSibling.innerText = this.value">
                            <span style="font-size: 0.85rem; color: var(--color-rf); font-weight: 600; display: block; margin-top: 0.25rem;">{{ $savedMetrics['params']['rf_max_depth'] ?? 8 }}</span>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="rf_min_split">Sampel Minimum untuk Split Node (min_samples_split)</label>
                            <select id="rf_min_split" name="rf_min_split" class="form-control">
                                <option value="2" selected>2 (Default)</option>
                                <option value="3">3</option>
                                <option value="5">5</option>
                                <option value="8">8</option>
                            </select>
                        </div>
                    </div>

                    <!-- SVM Config -->
                    <div style="background: rgba(255, 255, 255, 0.01); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.5rem; border-top: 3px solid var(--color-svm);">
                        <h3 style="font-size: 1.15rem; font-weight: 600; margin-bottom: 1.25rem; color: var(--color-svm); display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fa-solid fa-bezier-curve"></i> Hyperparameter SVM
                        </h3>

                        <div class="form-group">
                            <label class="form-label" for="svm_c">Parameter Regularisasi (C)</label>
                            <select id="svm_c" name="svm_c" class="form-control">
                                <option value="0.1" {{ (isset($savedMetrics['params']['svm_c']) && $savedMetrics['params']['svm_c'] == 0.1) ? 'selected' : '' }}>0.1 (Soft Margin)</option>
                                <option value="1.0" {{ (!isset($savedMetrics['params']['svm_c']) || $savedMetrics['params']['svm_c'] == 1.0) ? 'selected' : '' }}>1.0 (Default)</option>
                                <option value="10.0" {{ (isset($savedMetrics['params']['svm_c']) && $savedMetrics['params']['svm_c'] == 10.0) ? 'selected' : '' }}>10.0</option>
                                <option value="100.0" {{ (isset($savedMetrics['params']['svm_c']) && $savedMetrics['params']['svm_c'] == 100.0) ? 'selected' : '' }}>100.0 (Hard Margin)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="svm_kernel">Fungsi Kernel (Kernel Function)</label>
                            <select id="svm_kernel" name="svm_kernel" class="form-control" onchange="toggleGammaField(this.value)">
                                <option value="linear" {{ (isset($savedMetrics['params']['svm_kernel']) && $savedMetrics['params']['svm_kernel'] == 'linear') ? 'selected' : '' }}>Linear</option>
                                <option value="rbf" {{ (!isset($savedMetrics['params']['svm_kernel']) || $savedMetrics['params']['svm_kernel'] == 'rbf') ? 'selected' : '' }}>RBF (Radial Basis Function)</option>
                            </select>
                        </div>

                        <div class="form-group" id="gamma-group">
                            <label class="form-label" for="svm_gamma">Parameter Gamma (untuk Kernel RBF)</label>
                            <input type="range" id="svm_gamma" name="svm_gamma" min="0.05" max="2" step="0.05" value="{{ $savedMetrics['params']['svm_gamma'] ?? 0.5 }}" class="form-control" style="padding: 0.25rem;" oninput="this.nextElementSibling.innerText = this.value">
                            <span style="font-size: 0.85rem; color: var(--color-svm); font-weight: 600; display: block; margin-top: 0.25rem;">{{ $savedMetrics['params']['svm_gamma'] ?? 0.5 }}</span>
                        </div>
                    </div>
                </div>

                <div style="display: flex; justify-content: center; margin-top: 2rem;">
                    <button type="submit" class="btn btn-primary" style="padding: 1rem 3rem; font-size: 1.05rem;">
                        <i class="fa-solid fa-bolt-lightning"></i> Latih Model & Bandingkan
                    </button>
                </div>
            </form>
        @else
            <div style="text-align: center; padding: 2rem; color: var(--text-muted); display: flex; flex-direction: column; align-items: center; gap: 1rem;">
                <i class="fa-solid fa-circle-exclamation" style="font-size: 3rem; color: var(--color-warning);"></i>
                <p>Data historis curah hujan tidak mencukupi untuk melakukan pelatihan model. Diperlukan minimal <strong>10 data</strong>. Silakan menuju tab <strong>Kelola Data</strong> terlebih dahulu.</p>
                <a href="{{ route('data.index') }}" class="btn btn-secondary"><i class="fa-solid fa-database"></i> Isi Data Sekarang</a>
            </div>
        @endif
    </div>

    <!-- AJAX Loading Spinner -->
    <div id="loading-spinner" class="spinner-container">
        <div class="spinner"></div>
        <p style="color: var(--text-secondary); font-weight: 500; font-size: 1.1rem; letter-spacing: 0.025em;">Sedang melatih model Random Forest dan SVM di sisi server...</p>
    </div>

    <!-- JS Error Alert -->
    <div id="js-error-alert" class="alert-box alert-error" style="display: none;">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <span id="js-error-text"></span>
    </div>

    <!-- Results Section -->
    <div id="results-container" style="display: {{ $savedMetrics ? 'block' : 'none' }};">
        
        <!-- Metrics Table Card -->
        <div class="card">
            <h3 class="card-title">
                <i class="fa-solid fa-chart-simple" style="color: var(--color-rf);"></i> Perbandingan Metrik Evaluasi (Data Uji 20%)
            </h3>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Metrik Evaluasi</th>
                            <th style="color: var(--color-rf);"><i class="fa-solid fa-tree"></i> Random Forest</th>
                            <th style="color: var(--color-svm);"><i class="fa-solid fa-bezier-curve"></i> SVM</th>
                            <th>Keterangan Penelitian</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Akurasi (Accuracy)</strong></td>
                            <td id="rf-accuracy" style="font-size: 1.1rem; font-weight: 700; color: var(--color-rf);">
                                {{ $savedMetrics['rf']['accuracy'] ?? '-' }}%
                            </td>
                            <td id="svm-accuracy" style="font-size: 1.1rem; font-weight: 700; color: var(--color-svm);">
                                {{ $savedMetrics['svm']['accuracy'] ?? '-' }}%
                            </td>
                            <td style="font-size: 0.85rem; color: var(--text-secondary);">
                                Proporsi tebakan klasifikasi yang benar (Ekstrem & Normal) dari keseluruhan data uji.
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Presisi (Precision)</strong></td>
                            <td id="rf-precision" style="font-size: 1.1rem; font-weight: 700; color: var(--color-rf);">
                                {{ $savedMetrics['rf']['precision'] ?? '-' }}%
                            </td>
                            <td id="svm-precision" style="font-size: 1.1rem; font-weight: 700; color: var(--color-svm);">
                                {{ $savedMetrics['svm']['precision'] ?? '-' }}%
                            </td>
                            <td style="font-size: 0.85rem; color: var(--text-secondary);">
                                Keakuratan tebakan curah hujan ekstrem (ketika model menebak ekstrem, seberapa sering ia benar).
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Recall / Sensitivitas</strong></td>
                            <td id="rf-recall" style="font-size: 1.1rem; font-weight: 700; color: var(--color-rf);">
                                {{ $savedMetrics['rf']['recall'] ?? '-' }}%
                            </td>
                            <td id="svm-recall" style="font-size: 1.1rem; font-weight: 700; color: var(--color-svm);">
                                {{ $savedMetrics['svm']['recall'] ?? '-' }}%
                            </td>
                            <td style="font-size: 0.85rem; color: var(--text-secondary);">
                                Kemampuan model mendeteksi seluruh hari yang secara riil mengalami curah hujan ekstrem (sangat penting untuk mitigasi bencana).
                            </td>
                        </tr>
                        <tr>
                            <td><strong>F1-Score</strong></td>
                            <td id="rf-f1" style="font-size: 1.1rem; font-weight: 700; color: var(--color-rf);">
                                {{ $savedMetrics['rf']['f1_score'] ?? '-' }}%
                            </td>
                            <td id="svm-f1" style="font-size: 1.1rem; font-weight: 700; color: var(--color-svm);">
                                {{ $savedMetrics['svm']['f1_score'] ?? '-' }}%
                            </td>
                            <td style="font-size: 0.85rem; color: var(--text-secondary);">
                                Nilai rata-rata harmonis gabungan antara presisi dan recall. Indikator seimbang pada data imbalanced.
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Waktu Komputasi Training</strong></td>
                            <td id="rf-time" style="font-weight: 600; color: var(--text-muted);">
                                {{ $savedMetrics['rf']['time'] ?? '-' }} ms
                            </td>
                            <td id="svm-time" style="font-weight: 600; color: var(--text-muted);">
                                {{ $savedMetrics['svm']['time'] ?? '-' }} ms
                            </td>
                            <td style="font-size: 0.85rem; color: var(--text-secondary);">
                                Durasi waktu yang dibutuhkan masing-masing algoritma dalam melatih bobot model.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Confusion Matrices Card -->
        <div class="grid-2">
            <!-- RF Confusion Matrix -->
            <div class="card">
                <h3 class="card-title" style="color: var(--color-rf);">
                    <i class="fa-solid fa-square-poll-vertical"></i> Confusion Matrix - Random Forest
                </h3>
                
                <div class="confusion-matrix-grid" style="margin-top: 1.5rem;">
                    <!-- Row 1: Columns Header -->
                    <div></div>
                    <div class="cm-header">Pred Normal</div>
                    <div class="cm-header">Pred Ekstrem</div>

                    <!-- Row 2: Actual Normal -->
                    <div class="cm-label">Akt Normal</div>
                    <div class="cm-box highlight-correct" title="True Negative">
                        <span class="cm-number" id="rf-tn">{{ $savedMetrics['rf']['confusion_matrix']['tn'] ?? 0 }}</span>
                        <span class="cm-desc">TN</span>
                    </div>
                    <div class="cm-box highlight-incorrect" title="False Positive">
                        <span class="cm-number" id="rf-fp">{{ $savedMetrics['rf']['confusion_matrix']['fp'] ?? 0 }}</span>
                        <span class="cm-desc">FP</span>
                    </div>

                    <!-- Row 3: Actual Extreme -->
                    <div class="cm-label">Akt Ekstrem</div>
                    <div class="cm-box highlight-incorrect" title="False Negative">
                        <span class="cm-number" id="rf-fn">{{ $savedMetrics['rf']['confusion_matrix']['fn'] ?? 0 }}</span>
                        <span class="cm-desc">FN</span>
                    </div>
                    <div class="cm-box highlight-correct" title="True Positive">
                        <span class="cm-number" id="rf-tp">{{ $savedMetrics['rf']['confusion_matrix']['tp'] ?? 0 }}</span>
                        <span class="cm-desc">TP</span>
                    </div>
                </div>
            </div>

            <!-- SVM Confusion Matrix -->
            <div class="card">
                <h3 class="card-title" style="color: var(--color-svm);">
                    <i class="fa-solid fa-square-poll-vertical"></i> Confusion Matrix - SVM
                </h3>
                
                <div class="confusion-matrix-grid" style="margin-top: 1.5rem;">
                    <!-- Row 1: Columns Header -->
                    <div></div>
                    <div class="cm-header">Pred Normal</div>
                    <div class="cm-header">Pred Ekstrem</div>

                    <!-- Row 2: Actual Normal -->
                    <div class="cm-label">Akt Normal</div>
                    <div class="cm-box highlight-correct" title="True Negative">
                        <span class="cm-number" id="svm-tn">{{ $savedMetrics['svm']['confusion_matrix']['tn'] ?? 0 }}</span>
                        <span class="cm-desc">TN</span>
                    </div>
                    <div class="cm-box highlight-incorrect" title="False Positive">
                        <span class="cm-number" id="svm-fp">{{ $savedMetrics['svm']['confusion_matrix']['fp'] ?? 0 }}</span>
                        <span class="cm-desc">FP</span>
                    </div>

                    <!-- Row 3: Actual Extreme -->
                    <div class="cm-label">Akt Ekstrem</div>
                    <div class="cm-box highlight-incorrect" title="False Negative">
                        <span class="cm-number" id="svm-fn">{{ $savedMetrics['svm']['confusion_matrix']['fn'] ?? 0 }}</span>
                        <span class="cm-desc">FN</span>
                    </div>
                    <div class="cm-box highlight-correct" title="True Positive">
                        <span class="cm-number" id="svm-tp">{{ $savedMetrics['svm']['confusion_matrix']['tp'] ?? 0 }}</span>
                        <span class="cm-desc">TP</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ROC Curve Comparison Card -->
        <div class="card">
            <h3 class="card-title">
                <i class="fa-solid fa-chart-line" style="color: var(--color-accent);"></i> Kurva ROC (Receiver Operating Characteristic)
            </h3>
            <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 1.5rem; line-height: 1.5;">
                Kurva ROC memetakan <em>True Positive Rate (Sensitivity)</em> terhadap <em>False Positive Rate (1 - Specificity)</em> 
                pada berbagai nilai threshold pemisah. Model dengan kurva yang lebih mendekati pojok kiri atas 
                memiliki performa klasifikasi yang lebih unggul.
            </p>

            <div style="position: relative; height: 400px; width: 100%;">
                <canvas id="rocChartCanvas"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
    // Toggle gamma input visibility depending on kernel choice
    function toggleGammaField(kernel) {
        const gammaGroup = document.getElementById('gamma-group');
        if (kernel === 'linear') {
            gammaGroup.style.opacity = '0.3';
            gammaGroup.style.pointerEvents = 'none';
        } else {
            gammaGroup.style.opacity = '1';
            gammaGroup.style.pointerEvents = 'auto';
        }
    }

    // Trigger on load for SVM Linear condition check
    document.addEventListener('DOMContentLoaded', function() {
        const kernelVal = document.getElementById('svm_kernel').value;
        toggleGammaField(kernelVal);
        
        // If metrics are already saved in the session, plot the ROC chart on load
        @if($savedMetrics)
            const rfRoc = @json($savedMetrics['rf_roc']);
            const svmRoc = @json($savedMetrics['svm_roc']);
            
            // Wait slightly for Chart.js to load and DOM to settle
            setTimeout(() => {
                const rfPoints = rfRoc.map(p => ({ x: p.fpr, y: p.tpr }));
                const svmPoints = svmRoc.map(p => ({ x: p.fpr, y: p.tpr }));
                const baselinePoints = [{ x: 0, y: 0 }, { x: 1, y: 1 }];

                const ctx = document.getElementById('rocChartCanvas').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        datasets: [
                            {
                                label: 'Random Forest',
                                data: rfPoints,
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.05)',
                                borderWidth: 3,
                                pointRadius: 3,
                                tension: 0.1,
                                fill: false
                            },
                            {
                                label: 'Support Vector Machine (SVM)',
                                data: svmPoints,
                                borderColor: '#06b6d4',
                                backgroundColor: 'rgba(6, 182, 212, 0.05)',
                                borderWidth: 3,
                                pointRadius: 3,
                                tension: 0.1,
                                fill: false
                            },
                            {
                                label: 'Random Guess',
                                data: baselinePoints,
                                borderColor: 'rgba(148, 163, 184, 0.4)',
                                borderWidth: 1,
                                borderDash: [5, 5],
                                pointRadius: 0,
                                fill: false
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                type: 'linear',
                                position: 'bottom',
                                title: { display: true, text: 'False Positive Rate (FPR)', color: '#94a3b8' },
                                min: 0, max: 1,
                                ticks: { color: '#64748b' },
                                grid: { color: 'rgba(255, 255, 255, 0.05)' }
                            },
                            y: {
                                type: 'linear',
                                title: { display: true, text: 'True Positive Rate (TPR)', color: '#94a3b8' },
                                min: 0, max: 1,
                                ticks: { color: '#64748b' },
                                grid: { color: 'rgba(255, 255, 255, 0.05)' }
                            }
                        },
                        plugins: {
                            legend: { labels: { color: '#f8fafc', font: { family: 'Outfit' } } }
                        }
                    }
                });
            }, 100);
        @endif
    });
</script>
@endsection
