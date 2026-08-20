<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Abdillans Gaming') - Panel Admin Rental PlayStation</title>
    
    <!-- Google Fonts & Font Awesome Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --bg-dark: #0b0f19;
            --bg-surface: #131927;
            --bg-card: #1a2235;
            --bg-card-hover: #222c42;
            --bg-input: #0f1523;
            
            --primary: #6366f1; /* PlayStation Indigo */
            --primary-hover: #4f46e5;
            --accent-cyan: #06b6d4; /* PlayStation Cyan */
            --accent-emerald: #10b981; /* Green Status Ada */
            --accent-amber: #f59e0b; /* Amber Status Disewa */
            --accent-rose: #f43f5e; /* Rose Status Maintenance */
            
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --text-dim: #64748b;
            --border: #26334d;
            --border-highlight: #3b82f6;
            
            --font-heading: 'Outfit', sans-serif;
            --font-body: 'Plus Jakarta Sans', sans-serif;
            --shadow-lg: 0 10px 30px -5px rgba(0, 0, 0, 0.5), 0 0 20px rgba(99, 102, 241, 0.15);
            --radius-lg: 1rem;
            --radius-md: 0.75rem;
            --transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font-body);
            background-color: var(--bg-dark);
            color: var(--text-main);
            line-height: 1.5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-image: 
                radial-gradient(circle at 15% 15%, rgba(99, 102, 241, 0.08) 0%, transparent 40%),
                radial-gradient(circle at 85% 85%, rgba(6, 182, 212, 0.06) 0%, transparent 40%);
            background-attachment: fixed;
        }

        /* Top Header Navbar */
        header {
            background-color: rgba(19, 25, 39, 0.9);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }

        .nav-container {
            max-width: 1440px;
            margin: 0 auto;
            padding: 0.9rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            text-decoration: none;
        }

        .brand-icon {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, var(--primary), var(--accent-cyan));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.35rem;
            box-shadow: 0 0 15px rgba(99, 102, 241, 0.5);
        }

        .brand-text h1 {
            font-family: var(--font-heading);
            font-size: 1.35rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            background: linear-gradient(135deg, #ffffff 0%, #cbd5e1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .brand-text p {
            font-size: 0.75rem;
            color: var(--accent-cyan);
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        /* Nav Category Tabs (3 Categories) */
        .nav-menu {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background-color: var(--bg-input);
            padding: 0.35rem;
            border-radius: var(--radius-md);
            border: 1px solid var(--border);
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.6rem 1.1rem;
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            border-radius: 0.5rem;
            transition: var(--transition);
        }

        .nav-link:hover {
            color: var(--text-main);
            background-color: rgba(255, 255, 255, 0.05);
        }

        .nav-link.active {
            color: #ffffff;
            background: linear-gradient(135deg, var(--primary), #4338ca);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.35);
        }

        .nav-link i {
            font-size: 1rem;
        }

        /* Container Main Content */
        .main-container {
            max-width: 1440px;
            margin: 0 auto;
            padding: 1.75rem;
            width: 100%;
            flex: 1;
        }

        /* Alerts */
        .alert-box {
            padding: 1rem 1.25rem;
            border-radius: var(--radius-md);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.85rem;
            font-weight: 500;
            animation: slideDown 0.3s ease;
        }

        .alert-success {
            background-color: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #34d399;
        }

        .alert-danger {
            background-color: rgba(244, 63, 94, 0.12);
            border: 1px solid rgba(244, 63, 94, 0.3);
            color: #fb7185;
        }

        /* Buttons */
        .btn-custom {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.65rem 1.25rem;
            border-radius: var(--radius-md);
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            border: none;
            transition: var(--transition);
            text-decoration: none;
            font-family: var(--font-body);
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary), var(--primary-hover));
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35);
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.5);
        }

        .btn-secondary-custom {
            background-color: var(--bg-card);
            color: var(--text-main);
            border: 1px solid var(--border);
        }

        .btn-secondary-custom:hover {
            background-color: var(--bg-card-hover);
            border-color: var(--border-highlight);
        }

        .btn-success-custom {
            background: linear-gradient(135deg, var(--accent-emerald), #059669);
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);
        }

        .btn-success-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.5);
        }

        .btn-danger-custom {
            background: linear-gradient(135deg, var(--accent-rose), #e11d48);
            color: #ffffff;
        }

        .btn-danger-custom:hover {
            transform: translateY(-2px);
        }

        .btn-sm {
            padding: 0.4rem 0.85rem;
            font-size: 0.8rem;
            border-radius: 0.5rem;
        }

        /* Footer */
        footer {
            background-color: var(--bg-surface);
            border-top: 1px solid var(--border);
            padding: 1.25rem;
            text-align: center;
            color: var(--text-dim);
            font-size: 0.85rem;
            margin-top: auto;
        }

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(6px);
            z-index: 2000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .modal-overlay.active {
            display: flex;
            animation: fadeIn 0.25s ease;
        }

        .modal-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            width: 100%;
            max-width: 620px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: var(--shadow-lg);
        }

        .modal-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            font-family: var(--font-heading);
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .modal-close {
            background: transparent;
            border: none;
            color: var(--text-muted);
            font-size: 1.25rem;
            cursor: pointer;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }

        .modal-close:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: var(--text-main);
        }

        .modal-body {
            padding: 1.5rem;
        }

        /* Form Control */
        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.4rem;
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .form-control, .form-select {
            width: 100%;
            padding: 0.7rem 1rem;
            background-color: var(--bg-input);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            color: var(--text-main);
            font-family: var(--font-body);
            font-size: 0.9rem;
            outline: none;
            transition: var(--transition);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.96); }
            to { opacity: 1; transform: scale(1); }
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 900px) {
            .nav-container {
                flex-direction: column;
                gap: 1rem;
            }
            .nav-menu {
                width: 100%;
                justify-content: space-around;
            }
            .nav-link {
                padding: 0.5rem 0.75rem;
                font-size: 0.8rem;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <header>
        <div class="nav-container">
            <a href="{{ route('sheets') }}" class="brand-logo">
                <div class="brand-icon">
                    <i class="fa-solid fa-gamepad"></i>
                </div>
                <div class="brand-text">
                    <h1>ABDILLANS GAMING</h1>
                    <p>PlayStation Rental Management</p>
                </div>
            </a>
            
            <!-- 3 MAIN CATEGORIES NAVIGATION -->
            <nav class="nav-menu">
                <a href="{{ route('sheets') }}" class="nav-link {{ Request::routeIs('sheets') || Request::routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-table-cells"></i>
                    <span>1. Sheets (Mendata)</span>
                </a>
                <a href="{{ route('customers.index') }}" class="nav-link {{ Request::routeIs('customers.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-users"></i>
                    <span>2. Data Pelanggan</span>
                </a>
                <a href="{{ route('history') }}" class="nav-link {{ Request::routeIs('history') ? 'active' : '' }}">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    <span>3. Riwayat Pelanggan</span>
                </a>
            </nav>
        </div>
    </header>

    <main class="main-container">
        @if(session('success'))
            <div class="alert-box alert-success">
                <i class="fa-solid fa-circle-check fa-lg"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="alert-box alert-danger">
                <i class="fa-solid fa-triangle-exclamation fa-lg"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert-box alert-danger" style="flex-direction: column; align-items: flex-start;">
                <div style="display: flex; gap: 0.5rem; align-items: center; font-weight: bold;">
                    <i class="fa-solid fa-circle-xmark fa-lg"></i>
                    <span>Terdapat kesalahan input data:</span>
                </div>
                <ul style="margin-left: 1.75rem; font-size: 0.85rem; margin-top: 0.3rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <footer>
        <p>&copy; {{ date('Y') }} <strong>Abdillans Gaming PlayStation Rental</strong> &bull; System Admin Panel</p>
    </footer>

    @yield('scripts')
</body>
</html>
