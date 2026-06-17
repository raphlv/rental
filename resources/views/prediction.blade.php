@extends('layouts.app')

@section('content')
<div class="fade-in">
    <div class="card">
        <h2 class="card-title">
            <i class="fa-solid fa-crystal-ball" style="color: var(--color-accent);"></i> Prediksi Mandiri Curah Hujan Ekstrem
        </h2>
        <p style="color: var(--text-secondary); line-height: 1.6; margin-bottom: 2rem;">
            Masukkan parameter iklim di bawah ini untuk memprediksi apakah kondisi cuaca berpotensi memicu curah hujan ekstrem 
            berdasarkan klasifikasi model Random Forest dan SVM, lengkap dengan rekomendasi mitigasi bencananya.
        </p>

        @if($isTrained)
            <form action="{{ route('prediction.predict') }}" method="POST" style="max-width: 600px; margin: 0 auto;">
                @csrf
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label" for="tavg"><i class="fa-solid fa-temperature-half"></i> Suhu Rata-rata (°C)</label>
                        <input type="number" id="tavg" name="tavg" class="form-control" placeholder="Contoh: 26.5" min="10" max="50" step="0.1" value="{{ old('tavg', session('prediction_result.input.tavg') ?? 26.0) }}" required>
                        <span style="font-size: 0.75rem; color: var(--text-muted); display: block; margin-top: 0.25rem;">Kisaran suhu tropis Indonesia: 20°C - 36°C</span>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="rh_avg"><i class="fa-solid fa-droplet"></i> Kelembapan Udara Rata-rata (%)</label>
                        <input type="number" id="rh_avg" name="rh_avg" class="form-control" placeholder="Contoh: 85" min="10" max="100" step="1" value="{{ old('rh_avg', session('prediction_result.input.rh_avg') ?? 85) }}" required>
                        <span style="font-size: 0.75rem; color: var(--text-muted); display: block; margin-top: 0.25rem;">Kelembapan tinggi (>80%) berpotensi memicu hujan lebat</span>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1.5rem; padding: 0.9rem;">
                    <i class="fa-solid fa-calculator"></i> Hitung Prediksi Curah Hujan
                </button>
            </form>
        @else
            <div style="text-align: center; padding: 2rem; color: var(--text-muted); display: flex; flex-direction: column; align-items: center; gap: 1rem;">
                <i class="fa-solid fa-circle-exclamation" style="font-size: 3rem; color: var(--color-warning);"></i>
                <p>Kedua model machine learning belum dilatih dalam sesi ini. Silakan latih model terlebih dahulu agar Anda dapat menggunakan kalkulator prediksi.</p>
                <a href="{{ route('training.index') }}" class="btn btn-secondary"><i class="fa-solid fa-brain"></i> Latih Model Sekarang</a>
            </div>
        @endif
    </div>

    <!-- Prediction Output Result -->
    @if($predictionResult)
        <div class="card fade-in" id="prediction-result-card" style="border-top: 4px solid var(--color-accent);">
            <h3 class="card-title">
                <i class="fa-solid fa-square-poll-horizontal" style="color: var(--color-accent);"></i> Hasil Klasifikasi Prediksi
            </h3>
            
            <div style="background: rgba(255, 255, 255, 0.02); padding: 1rem 1.5rem; border-radius: 8px; border: 1px solid var(--border-color); margin-bottom: 2rem; font-size: 0.95rem;">
                <span style="color: var(--text-secondary);">Parameter Input: </span> 
                <strong style="color: #fff; margin-right: 1.5rem;"><i class="fa-solid fa-temperature-half" style="color: var(--color-svm);"></i> Suhu = {{ $predictionResult['input']['tavg'] }} °C</strong>
                <strong style="color: #fff;"><i class="fa-solid fa-droplet" style="color: var(--color-rf);"></i> Kelembapan = {{ $predictionResult['input']['rh_avg'] }} %</strong>
            </div>

            <div class="grid-2">
                <!-- Random Forest Prediction -->
                <div style="background: rgba(16, 185, 129, 0.04); border: 1px solid rgba(16, 185, 129, 0.15); border-radius: 12px; padding: 1.5rem; text-align: center;">
                    <h4 style="color: var(--color-rf); font-size: 1.1rem; font-weight: 600; margin-bottom: 1rem;"><i class="fa-solid fa-tree"></i> Model Random Forest</h4>
                    
                    @if($predictionResult['rf']['class'] == 1)
                        <div style="font-size: 3rem; color: var(--color-danger); margin-bottom: 0.5rem;"><i class="fa-solid fa-cloud-showers-heavy"></i></div>
                        <span class="badge badge-extreme" style="font-size: 0.9rem; padding: 0.4rem 1rem;">Ekstrem</span>
                        <p style="margin-top: 1rem; font-size: 0.9rem; color: var(--text-secondary);">
                            Probabilitas Keyakinan: <strong>{{ $predictionResult['rf']['confidence'] }}%</strong>
                        </p>
                    @else
                        <div style="font-size: 3rem; color: var(--color-rf); margin-bottom: 0.5rem;"><i class="fa-solid fa-cloud-sun"></i></div>
                        <span class="badge badge-normal" style="font-size: 0.9rem; padding: 0.4rem 1rem;">Normal</span>
                        <p style="margin-top: 1rem; font-size: 0.9rem; color: var(--text-secondary);">
                            Probabilitas Keyakinan: <strong>{{ $predictionResult['rf']['confidence'] }}%</strong>
                        </p>
                    @endif
                </div>

                <!-- SVM Prediction -->
                <div style="background: rgba(6, 182, 212, 0.04); border: 1px solid rgba(6, 182, 212, 0.15); border-radius: 12px; padding: 1.5rem; text-align: center;">
                    <h4 style="color: var(--color-svm); font-size: 1.1rem; font-weight: 600; margin-bottom: 1rem;"><i class="fa-solid fa-bezier-curve"></i> Model SVM</h4>
                    
                    @if($predictionResult['svm']['class'] == 1)
                        <div style="font-size: 3rem; color: var(--color-danger); margin-bottom: 0.5rem;"><i class="fa-solid fa-cloud-showers-heavy"></i></div>
                        <span class="badge badge-extreme" style="font-size: 0.9rem; padding: 0.4rem 1rem;">Ekstrem</span>
                        <p style="margin-top: 1rem; font-size: 0.9rem; color: var(--text-secondary);">
                            Skor Fungsi Keputusan: <strong>+{{ $predictionResult['svm']['score'] }}</strong>
                        </p>
                    @else
                        <div style="font-size: 3rem; color: var(--color-svm); margin-bottom: 0.5rem;"><i class="fa-solid fa-cloud-sun"></i></div>
                        <span class="badge badge-normal" style="font-size: 0.9rem; padding: 0.4rem 1rem;">Normal</span>
                        <p style="margin-top: 1rem; font-size: 0.9rem; color: var(--text-secondary);">
                            Skor Fungsi Keputusan: <strong>{{ $predictionResult['svm']['score'] }}</strong>
                        </p>
                    @endif
                </div>
            </div>

            <!-- Mitigation Recommendation Box -->
            <div class="alert-banner alert-banner-{{ $predictionResult['alert']['color'] }}">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    @if($predictionResult['alert']['color'] === 'red')
                        <i class="fa-solid fa-circle-radiation" style="font-size: 1.8rem; color: var(--color-danger); animation: pulse 1.5s infinite;"></i>
                    @elseif($predictionResult['alert']['color'] === 'amber')
                        <i class="fa-solid fa-triangle-exclamation" style="font-size: 1.8rem; color: var(--color-warning);"></i>
                    @else
                        <i class="fa-solid fa-shield-halved" style="font-size: 1.8rem; color: var(--color-rf);"></i>
                    @endif
                    <div>
                        <span style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; display: block; opacity: 0.75;">Status Siaga Bencana</span>
                        <h4>LEVEL ALERT: {{ $predictionResult['alert']['level'] }}</h4>
                    </div>
                </div>
                
                <hr style="border: 0; border-top: 1px solid rgba(255,255,255,0.06); margin: 0.5rem 0;">
                
                <h5 style="font-size: 1rem; font-weight: 600; margin-bottom: 0.5rem;"><i class="fa-solid fa-list-check"></i> Rekomendasi Aksi Mitigasi:</h5>
                <ul>
                    @foreach($predictionResult['alert']['recommendations'] as $rec)
                        <li>{{ $rec }}</li>
                    @endforeach
                </ul>
            </div>
        </div>

        <script>
            // Automatically scroll to the results card when it renders
            document.getElementById('prediction-result-card').scrollIntoView({ behavior: 'smooth', block: 'start' });
        </script>
    @endif
</div>

<style>
    @keyframes pulse {
        0% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.1); opacity: 0.7; }
        100% { transform: scale(1); opacity: 1; }
    }
</style>
@endsection
