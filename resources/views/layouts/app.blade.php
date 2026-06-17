<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Rainfall ML - Skripsi Dashboard</title>
    
    <!-- CDN Icons & Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Style Asset -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

    <header>
        <div class="nav-container">
            <a href="{{ route('dashboard') }}" class="brand">
                <i class="fa-solid fa-cloud-showers-water brand-icon"></i>
                <span>Rainfall<span style="font-weight: 400; color: var(--color-svm);">ML</span></span>
            </a>
            
            <nav class="nav-links">
                <a href="{{ route('dashboard') }}" class="nav-item {{ Route::is('dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-house-chimney"></i> Ringkasan
                </a>
                <a href="{{ route('data.index') }}" class="nav-item {{ Route::is('data.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-database"></i> Kelola Data
                </a>
                <a href="{{ route('training.index') }}" class="nav-item {{ Route::is('training.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-brain"></i> Pelatihan Model
                </a>
                <a href="{{ route('prediction.index') }}" class="nav-item {{ Route::is('prediction.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-crystal-ball"></i> Prediksi Mandiri
                </a>
            </nav>
        </div>
    </header>

    <main>
        @if (session('success'))
            <div class="alert-box alert-success">
                <i class="fa-solid fa-circle-check"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="alert-box alert-error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @yield('content')
    </main>

    <footer style="text-align: center; padding: 2rem 0; color: var(--text-muted); font-size: 0.85rem; border-top: 1px solid var(--border-color); margin-top: 4rem;">
        <p>&copy; {{ date('Y') }} - Skripsi Analisis Perbandingan Algoritma Random Forest dan SVM untuk Klasifikasi Curah Hujan Ekstrem dalam Mitigasi Bencana</p>
    </footer>

    <!-- Custom JavaScript Asset -->
    <script src="{{ asset('js/dashboard.js') }}"></script>
</body>
</html>
