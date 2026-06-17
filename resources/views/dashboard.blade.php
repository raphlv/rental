@extends('layouts.app')

@section('content')
<div class="fade-in">
    <!-- Hero / Header Title -->
    <div class="card" style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(22, 30, 46, 0.7) 100%);">
        <h1 style="font-size: 2.2rem; font-weight: 800; margin-bottom: 0.75rem; letter-spacing: -0.025em; line-height: 1.2;">
            Analisis Perbandingan Algoritma Random Forest dan SVM untuk Klasifikasi Curah Hujan Ekstrem dalam Mitigasi Bencana
        </h1>
        <p style="color: var(--text-secondary); font-size: 1.1rem; max-width: 900px; line-height: 1.6;">
            Sistem dashboard riset skripsi untuk memetakan, melatih, dan membandingkan performa model klasifikasi 
            <strong>Random Forest</strong> dan <strong>Support Vector Machine (SVM)</strong> berdasarkan parameter iklim 
            suhu rata-rata (TAVG), kelembapan rata-rata (RH_AVG), dan curah hujan (RR).
        </p>
    </div>

    <!-- Stats Grid -->
    <div class="grid-4">
        <div class="stat-widget primary">
            <span class="stat-label">Total Data Historis</span>
            <span class="stat-value">{{ $totalDays }} Hari</span>
        </div>
        <div class="stat-widget rf">
            <span class="stat-label">Rata-rata Suhu (TAVG)</span>
            <span class="stat-value">{{ number_format($avgTemp, 1) }} °C</span>
        </div>
        <div class="stat-widget svm">
            <span class="stat-label">Rata-rata Kelembapan (RH)</span>
            <span class="stat-value">{{ number_format($avgHumid, 1) }} %</span>
        </div>
        <div class="stat-widget danger">
            <span class="stat-label">Kejadian Ekstrem</span>
            <span class="stat-value">
                {{ $extremeDays }} Hari ({{ number_format($extremePercentage, 1) }}%)
            </span>
        </div>
    </div>

    <!-- Charts and Context Grid -->
    <div class="grid-2" style="margin-top: 2rem;">
        <!-- Research Context Card -->
        <div class="card">
            <h3 class="card-title">
                <i class="fa-solid fa-circle-info" style="color: var(--color-accent);"></i> Latar Belakang & Metodologi
            </h3>
            <p style="color: var(--text-secondary); line-height: 1.7; font-size: 0.95rem; margin-bottom: 1rem;">
                Bencana hidrometeorologi seperti banjir dan tanah longsor sangat dipengaruhi oleh intensitas curah hujan ekstrem. 
                Melalui pengklasifikasian curah hujan ekstrem secara dini, mitigasi bencana dapat dijalankan lebih efektif.
            </p>
            <p style="color: var(--text-secondary); line-height: 1.7; font-size: 0.95rem; margin-bottom: 1.5rem;">
                Penelitian ini membandingkan dua algoritma machine learning populer:
            </p>
            
            <div style="display: flex; flex-direction: column; gap: 1rem; margin-bottom: 1.5rem;">
                <div style="display: flex; gap: 0.75rem; align-items: flex-start;">
                    <span class="badge badge-normal" style="background: rgba(16, 185, 129, 0.1); color: var(--color-rf); border-color: rgba(16, 185, 129, 0.3); font-size: 0.7rem; margin-top: 0.2rem;">Random Forest</span>
                    <div>
                        <h4 style="font-size: 0.95rem; font-weight: 600; margin-bottom: 0.25rem;">Random Forest Classifier</h4>
                        <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5;">
                            Algoritma ensemble berbasis decision trees yang bekerja dengan teknik bagging dan random feature selection. Sangat stabil dan handal menangani data iklim non-linear.
                        </p>
                    </div>
                </div>
                
                <div style="display: flex; gap: 0.75rem; align-items: flex-start;">
                    <span class="badge badge-normal" style="background: rgba(6, 182, 212, 0.1); color: var(--color-svm); border-color: rgba(6, 182, 212, 0.3); font-size: 0.7rem; margin-top: 0.2rem;">SVM</span>
                    <div>
                        <h4 style="font-size: 0.95rem; font-weight: 600; margin-bottom: 0.25rem;">Support Vector Machine (SVM)</h4>
                        <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5;">
                            Algoritma yang mencari hyperplane pemisah optimal dengan margin maksimum. Menggunakan kernel trick (Linear/RBF) untuk memetakan fitur ke dimensi lebih tinggi.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Trends Chart Card -->
        <div class="card">
            <h3 class="card-title">
                <i class="fa-solid fa-chart-line" style="color: var(--color-rf);"></i> Tren Curah Hujan Bulanan (Simulasi/Riil)
            </h3>
            
            @if(count($monthlyStats) > 0)
                <div style="position: relative; height: 300px; width: 100%;">
                    <canvas id="monthlyRainfallChart"></canvas>
                </div>
            @else
                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 300px; color: var(--text-muted); gap: 0.75rem;">
                    <i class="fa-solid fa-folder-open" style="font-size: 3rem;"></i>
                    <p>Database kosong. Silakan masuk ke menu <strong>Kelola Data</strong> untuk mengisi data.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Workflow Steps Card -->
    <div class="card">
        <h3 class="card-title">
            <i class="fa-solid fa-list-check" style="color: var(--color-svm);"></i> Alur Tahapan Penelitian Sistem
        </h3>
        
        <div class="grid-3" style="margin-top: 1rem; gap: 1.5rem;">
            <div style="background: rgba(255, 255, 255, 0.01); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.25rem; position: relative;">
                <span style="position: absolute; top: 1rem; right: 1rem; font-size: 1.5rem; font-weight: 800; color: rgba(255,255,255,0.05);">01</span>
                <h4 style="font-size: 1rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--color-accent);"><i class="fa-solid fa-cloud-arrow-down"></i> Input & Sanitasi Data</h4>
                <p style="font-size: 0.85rem; color: var(--text-secondary); line-height: 1.6;">
                    Mengunggah file CSV dari portal BMKG atau generate data simulasi. Melakukan imputasi mean jika terdapat data temperatur atau kelembapan yang kosong.
                </p>
            </div>
            
            <div style="background: rgba(255, 255, 255, 0.01); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.25rem; position: relative;">
                <span style="position: absolute; top: 1rem; right: 1rem; font-size: 1.5rem; font-weight: 800; color: rgba(255,255,255,0.05);">02</span>
                <h4 style="font-size: 1rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--color-rf);"><i class="fa-solid fa-arrows-spin"></i> Preprocessing & Training</h4>
                <p style="font-size: 0.85rem; color: var(--text-secondary); line-height: 1.6;">
                    Melakukan pembagian data latih/uji (80:20) dan standardisasi fitur Z-score. Model Random Forest dan SVM dilatih secara parallel pada data latih.
                </p>
            </div>
            
            <div style="background: rgba(255, 255, 255, 0.01); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.25rem; position: relative;">
                <span style="position: absolute; top: 1rem; right: 1rem; font-size: 1.5rem; font-weight: 800; color: rgba(255,255,255,0.05);">03</span>
                <h4 style="font-size: 1rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--color-svm);"><i class="fa-solid fa-shield-halved"></i> Perbandingan & Prediksi</h4>
                <p style="font-size: 0.85rem; color: var(--text-secondary); line-height: 1.6;">
                    Mengevaluasi akurasi, confusion matrix, dan kurva ROC dari kedua model pada data uji. Model kemudian digunakan untuk memprediksi curah hujan dan mitigasinya.
                </p>
            </div>
        </div>
    </div>
</div>

@if(count($monthlyStats) > 0)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('monthlyRainfallChart').getContext('2d');
        const months = @json($monthlyStats->pluck('month'));
        const rainfall = @json($monthlyStats->pluck('total_rain'));
        const extremes = @json($monthlyStats->pluck('extreme_count'));

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Total Curah Hujan (mm)',
                        data: rainfall,
                        backgroundColor: 'rgba(99, 102, 241, 0.3)',
                        borderColor: '#6366f1',
                        borderWidth: 2,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Frekuensi Hari Ekstrem',
                        data: extremes,
                        type: 'line',
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        borderWidth: 3,
                        pointRadius: 4,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: { color: '#f8fafc', font: { family: 'Outfit' } }
                    }
                },
                scales: {
                    x: {
                        ticks: { color: '#94a3b8' },
                        grid: { color: 'rgba(255,255,255,0.03)' }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        ticks: { color: '#94a3b8' },
                        title: { display: true, text: 'Curah Hujan (mm)', color: '#94a3b8' },
                        grid: { color: 'rgba(255,255,255,0.05)' }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        ticks: { color: '#94a3b8', stepSize: 1 },
                        title: { display: true, text: 'Frekuensi Hari Ekstrem', color: '#94a3b8' },
                        grid: { drawOnChartArea: false } // Only keep grid lines of the left axis
                    }
                }
            }
        });
    });
</script>
@endif
@endsection
