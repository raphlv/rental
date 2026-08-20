@extends('rental.layout')

@section('title', 'Category 1 - Sheets Mendata Pelanggan')

@section('styles')
<style>
    /* Sheets Inventory Header Stats */
    .stats-tally-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.25rem;
        margin-bottom: 2rem;
    }

    .tally-card {
        background-color: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 1.25rem;
        position: relative;
        overflow: hidden;
        transition: var(--transition);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .tally-card:hover {
        transform: translateY(-3px);
        border-color: var(--border-highlight);
    }

    .tally-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
    }

    .tally-ps3::before { background: #3b82f6; }
    .tally-ps4::before { background: #8b5cf6; }
    .tally-ps5::before { background: #06b6d4; }
    .tally-summary::before { background: #10b981; }

    .tally-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .tally-ps3 .tally-icon { background: rgba(59, 130, 246, 0.15); color: #60a5fa; }
    .tally-ps4 .tally-icon { background: rgba(139, 92, 246, 0.15); color: #a78bfa; }
    .tally-ps5 .tally-icon { background: rgba(6, 182, 212, 0.15); color: #22d3ee; }
    .tally-summary .tally-icon { background: rgba(16, 185, 129, 0.15); color: #34d399; }

    .tally-info h4 {
        font-family: var(--font-heading);
        font-size: 1rem;
        color: var(--text-muted);
        margin-bottom: 0.2rem;
    }

    .tally-info .count-number {
        font-family: var(--font-heading);
        font-size: 1.75rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 0.3rem;
    }

    .tally-badges {
        display: flex;
        gap: 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-pill-ada { color: #34d399; background: rgba(16, 185, 129, 0.12); padding: 0.15rem 0.5rem; border-radius: 12px; }
    .badge-pill-disewa { color: #fbbf24; background: rgba(245, 158, 11, 0.12); padding: 0.15rem 0.5rem; border-radius: 12px; }

    /* Category Filter bar */
    .filter-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.5rem;
        background-color: var(--bg-surface);
        padding: 0.85rem 1.25rem;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
    }

    .tab-pills {
        display: flex;
        gap: 0.5rem;
    }

    .tab-pill {
        padding: 0.5rem 1.1rem;
        border-radius: var(--radius-md);
        color: var(--text-muted);
        text-decoration: none;
        font-weight: 600;
        font-size: 0.85rem;
        transition: var(--transition);
        border: 1px solid transparent;
    }

    .tab-pill:hover {
        color: var(--text-main);
        background-color: var(--bg-card);
    }

    .tab-pill.active {
        background-color: var(--primary);
        color: #fff;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
    }

    /* Grid Sheets Cards */
    .sheets-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.25rem;
    }

    .unit-card {
        background-color: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        transition: var(--transition);
    }

    .unit-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }

    .unit-card.status-ada { border-top: 4px solid var(--accent-emerald); }
    .unit-card.status-disewa { border-top: 4px solid var(--accent-amber); }
    .unit-card.status-maintenance { border-top: 4px solid var(--accent-rose); }

    .unit-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.85rem;
    }

    .unit-code {
        font-family: var(--font-heading);
        font-size: 1.25rem;
        font-weight: 800;
        letter-spacing: -0.02em;
    }

    .status-tag {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        padding: 0.25rem 0.65rem;
        border-radius: 20px;
        letter-spacing: 0.05em;
    }

    .tag-ada { background-color: rgba(16, 185, 129, 0.15); color: #34d399; }
    .tag-disewa { background-color: rgba(245, 158, 11, 0.15); color: #fbbf24; }
    .tag-maintenance { background-color: rgba(244, 63, 94, 0.15); color: #fb7185; }

    .unit-body {
        margin-bottom: 1.25rem;
        flex: 1;
    }

    .price-text {
        font-size: 0.9rem;
        color: var(--text-muted);
        margin-bottom: 0.5rem;
    }

    .price-amount {
        color: var(--accent-cyan);
        font-weight: 700;
    }

    /* Active Rental Info Inside Card */
    .active-rental-box {
        background-color: var(--bg-input);
        border: 1px solid rgba(245, 158, 11, 0.25);
        border-radius: var(--radius-md);
        padding: 0.85rem;
        margin-top: 0.5rem;
    }

    .customer-profile-strip {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        margin-bottom: 0.65rem;
    }

    .customer-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--accent-amber);
    }

    .customer-details-text h5 {
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--text-main);
    }

    .customer-details-text p {
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    .timer-display {
        background-color: #0b0f19;
        border-radius: 0.5rem;
        padding: 0.4rem 0.75rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-family: var(--font-heading);
        font-size: 0.85rem;
    }

    .timer-clock {
        color: var(--accent-amber);
        font-weight: 800;
        font-size: 0.95rem;
    }

    /* Camera Box styling */
    .camera-container {
        position: relative;
        width: 100%;
        height: 220px;
        background-color: #000;
        border-radius: var(--radius-md);
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px dashed var(--border-highlight);
        margin-top: 0.5rem;
    }

    #webcamVideo, #webcamCanvas {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    #webcamCanvas {
        display: none;
    }

    .camera-btn-overlay {
        position: absolute;
        bottom: 10px;
        display: flex;
        gap: 0.5rem;
    }

    /* Duration Selector Buttons */
    .duration-buttons {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 0.5rem;
        margin-top: 0.4rem;
    }

    .duration-btn {
        padding: 0.5rem 0.2rem;
        background-color: var(--bg-input);
        border: 1px solid var(--border);
        color: var(--text-muted);
        border-radius: var(--radius-md);
        font-weight: 700;
        font-size: 0.85rem;
        cursor: pointer;
        text-align: center;
        transition: var(--transition);
    }

    .duration-btn:hover, .duration-btn.active {
        background-color: var(--primary);
        color: #fff;
        border-color: var(--primary-hover);
    }
</style>
@endsection

@section('content')
<!-- CATEGORY 1 HEADER BANNER & SHEETS TALLY COUNTERS -->
<div class="stats-tally-grid">
    <!-- PS 3 COUNTER: 30 UNITS -->
    <div class="tally-card tally-ps3">
        <div class="tally-icon">
            <i class="fa-solid fa-gamepad"></i>
        </div>
        <div class="tally-info">
            <h4>PS 3 (Total {{ $ps3Stats['total'] }} Unit)</h4>
            <div class="count-number">{{ $ps3Stats['total'] }} Unit</div>
            <div class="tally-badges">
                <span class="badge-pill-ada"><i class="fa-solid fa-check"></i> {{ $ps3Stats['ada'] }} Ada</span>
                <span class="badge-pill-disewa"><i class="fa-solid fa-play"></i> {{ $ps3Stats['disewa'] }} Disewa</span>
            </div>
        </div>
    </div>

    <!-- PS 4 COUNTER: 30 UNITS -->
    <div class="tally-card tally-ps4">
        <div class="tally-icon">
            <i class="fa-solid fa-gamepad"></i>
        </div>
        <div class="tally-info">
            <h4>PS 4 (Total {{ $ps4Stats['total'] }} Unit)</h4>
            <div class="count-number">{{ $ps4Stats['total'] }} Unit</div>
            <div class="tally-badges">
                <span class="badge-pill-ada"><i class="fa-solid fa-check"></i> {{ $ps4Stats['ada'] }} Ada</span>
                <span class="badge-pill-disewa"><i class="fa-solid fa-play"></i> {{ $ps4Stats['disewa'] }} Disewa</span>
            </div>
        </div>
    </div>

    <!-- PS 5 COUNTER -->
    <div class="tally-card tally-ps5">
        <div class="tally-icon">
            <i class="fa-solid fa-bolt"></i>
        </div>
        <div class="tally-info">
            <h4>PS 5 (Total {{ $ps5Stats['total'] }} Unit)</h4>
            <div class="count-number">{{ $ps5Stats['total'] }} Unit</div>
            <div class="tally-badges">
                <span class="badge-pill-ada"><i class="fa-solid fa-check"></i> {{ $ps5Stats['ada'] }} Ada</span>
                <span class="badge-pill-disewa"><i class="fa-solid fa-play"></i> {{ $ps5Stats['disewa'] }} Disewa</span>
            </div>
        </div>
    </div>

    <!-- OVERALL SUMMARY -->
    <div class="tally-card tally-summary">
        <div class="tally-icon">
            <i class="fa-solid fa-chart-pie"></i>
        </div>
        <div class="tally-info">
            <h4>Total Inventaris Unit</h4>
            <div class="count-number">{{ $totalUnitsCount }} Unit</div>
            <div class="tally-badges">
                <span class="badge-pill-ada">{{ $availableUnitsCount }} Siap Rent</span>
                <span class="badge-pill-disewa">{{ $rentedUnitsCount }} Aktif</span>
            </div>
        </div>
    </div>
</div>

<!-- FILTER SHEET TABS & ACTION BUTTON -->
<div class="filter-bar">
    <div class="tab-pills">
        <a href="{{ route('sheets', ['type' => 'Semua']) }}" class="tab-pill {{ $selectedType === 'Semua' ? 'active' : '' }}">
            <i class="fa-solid fa-layer-group"></i> Semua Console ({{ $totalUnitsCount }})
        </a>
        <a href="{{ route('sheets', ['type' => 'PS 3']) }}" class="tab-pill {{ $selectedType === 'PS 3' ? 'active' : '' }}">
            <i class="fa-solid fa-gamepad"></i> PS 3 (30 Unit)
        </a>
        <a href="{{ route('sheets', ['type' => 'PS 4']) }}" class="tab-pill {{ $selectedType === 'PS 4' ? 'active' : '' }}">
            <i class="fa-solid fa-gamepad"></i> PS 4 (30 Unit)
        </a>
        <a href="{{ route('sheets', ['type' => 'PS 5']) }}" class="tab-pill {{ $selectedType === 'PS 5' ? 'active' : '' }}">
            <i class="fa-solid fa-bolt"></i> PS 5 (10 Unit)
        </a>
    </div>

    <div>
        <button class="btn-custom btn-primary-custom" onclick="openStartRentalModal()">
            <i class="fa-solid fa-plus-circle"></i> Mendata Pelanggan / Sewa Baru
        </button>
    </div>
</div>

<!-- INVENTORY GRID SHEETS -->
<div class="sheets-grid">
    @forelse($units as $unit)
        <div class="unit-card status-{{ $unit->status }}">
            <div class="unit-header">
                <span class="unit-code">{{ $unit->code }}</span>
                <span class="status-tag tag-{{ $unit->status }}">
                    @if($unit->status === 'ada') <i class="fa-solid fa-circle-check"></i> Ada
                    @elseif($unit->status === 'disewa') <i class="fa-solid fa-spinner fa-spin"></i> Disewa
                    @else <i class="fa-solid fa-wrench"></i> Servis
                    @endif
                </span>
            </div>

            <div class="unit-body">
                <div class="price-text">
                    {{ $unit->name }} &bull; <span class="price-amount">Rp {{ number_format($unit->price_per_hour, 0, ',', '.') }}/jam</span>
                </div>

                @if($unit->status === 'disewa' && $unit->activeRental)
                    <div class="active-rental-box">
                        <div class="customer-profile-strip">
                            <img src="{{ $unit->activeRental->photo_url }}" alt="Foto Pelanggan" class="customer-avatar">
                            <div class="customer-details-text">
                                <h5>{{ $unit->activeRental->customer_name }}</h5>
                                <p><i class="fa-solid fa-phone"></i> {{ $unit->activeRental->customer_phone ?? '-' }}</p>
                            </div>
                        </div>

                        <div class="timer-display">
                            <span>Sisa Waktu:</span>
                            <span class="timer-clock" data-endtime="{{ $unit->activeRental->end_time->toIso8601String() }}" id="timer-{{ $unit->activeRental->id }}">
                                00:00:00
                            </span>
                        </div>
                    </div>
                @elseif($unit->status === 'ada')
                    <p style="font-size: 0.85rem; color: var(--text-dim); margin-top: 0.5rem;">
                        <i class="fa-solid fa-circle-info"></i> Unit siap digunakan untuk sewa pelanggan baru.
                    </p>
                @else
                    <p style="font-size: 0.85rem; color: var(--accent-rose); margin-top: 0.5rem;">
                        <i class="fa-solid fa-triangle-exclamation"></i> Dalam proses perawatan/maintenance.
                    </p>
                @endif
            </div>

            <div class="unit-footer">
                @if($unit->status === 'ada')
                    <button class="btn-custom btn-success-custom btn-sm" style="width: 100%;" onclick="openStartRentalModal({{ $unit->id }}, '{{ $unit->code }}', {{ $unit->price_per_hour }})">
                        <i class="fa-solid fa-play"></i> Sewa Unit Ini
                    </button>
                @elseif($unit->status === 'disewa' && $unit->activeRental)
                    <form action="{{ route('rentals.complete', $unit->activeRental->id) }}" method="POST" onsubmit="return confirm('Apakah rental unit {{ $unit->code }} ini sudah selesai?')">
                        @csrf
                        <button type="submit" class="btn-custom btn-primary-custom btn-sm" style="width: 100%;">
                            <i class="fa-solid fa-flag-checkered"></i> Selesai & Kembalikan
                        </button>
                    </form>
                @else
                    <button class="btn-custom btn-secondary-custom btn-sm" style="width: 100%;" disabled>
                        <i class="fa-solid fa-ban"></i> Tidak Tersedia
                    </button>
                @endif
            </div>
        </div>
    @empty
        <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; background-color: var(--bg-card); border-radius: var(--radius-lg);">
            <i class="fa-solid fa-gamepad fa-3x" style="color: var(--text-dim); margin-bottom: 1rem;"></i>
            <h3>Tidak Ada Unit PlayStation Ditemukan</h3>
            <p style="color: var(--text-muted);">Belum ada unit yang terdaftar pada kategori sheet ini.</p>
        </div>
    @endforelse
</div>

<!-- MODAL mendata PELANGGAN / SEWA PS BARU -->
<div class="modal-overlay" id="startRentalModal">
    <div class="modal-card">
        <div class="modal-header">
            <h3><i class="fa-solid fa-user-plus" style="color: var(--primary);"></i> Mendata Pelanggan / Sewa PlayStation</h3>
            <button class="modal-close" onclick="closeStartRentalModal()">&times;</button>
        </div>
        <form action="{{ route('rentals.start') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
                <!-- Select Unit -->
                <div class="form-group">
                    <label class="form-label"><i class="fa-solid fa-tv"></i> Pilih Unit PlayStation</label>
                    <select name="unit_id" id="modal_unit_id" class="form-select" required onchange="updateModalPrice()">
                        <option value="">-- Pilih Unit Tersedia --</option>
                        @foreach($availableUnits as $avail)
                            <option value="{{ $avail->id }}" data-price="{{ $avail->price_per_hour }}">
                                {{ $avail->code }} - {{ $avail->name }} (Rp {{ number_format($avail->price_per_hour, 0, ',', '.') }}/jam)
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Customer Mode Toggle -->
                <div class="form-group">
                    <label class="form-label"><i class="fa-solid fa-users"></i> Pilihan Pelanggan</label>
                    <div style="display: flex; gap: 1rem; margin-top: 0.3rem;">
                        <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.9rem; cursor: pointer;">
                            <input type="radio" name="customer_selection" value="existing" onclick="toggleCustomerMode('existing')" checked> Pelanggan Terdaftar
                        </label>
                        <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.9rem; cursor: pointer;">
                            <input type="radio" name="customer_selection" value="new" onclick="toggleCustomerMode('new')"> Pelanggan Baru / Walk-in
                        </label>
                    </div>
                </div>

                <!-- Existing Customer Dropdown -->
                <div class="form-group" id="existing_customer_group">
                    <label class="form-label">Pilih Pelanggan</label>
                    <select name="customer_id" class="form-select">
                        <option value="">-- Pilih dari Database Pelanggan --</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->phone ?? 'No Phone' }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- New Customer Inputs & Photo Capture (File Upload OR Live Camera) -->
                <div id="new_customer_group" style="display: none;">
                    <div class="form-group">
                        <label class="form-label">Nama Pelanggan</label>
                        <input type="text" name="customer_name" class="form-control" placeholder="Nama Lengkap">
                    </div>

                    <div class="form-group">
                        <label class="form-label">No. Telepon / WhatsApp</label>
                        <input type="text" name="customer_phone" class="form-control" placeholder="Contoh: 081234567890">
                    </div>
                </div>

                <!-- PHOTO MODULE: File Upload OR Live Webcam Snapshot -->
                <div class="form-group">
                    <label class="form-label"><i class="fa-solid fa-camera"></i> Foto Pelanggan (File ATAU Kamera)</label>
                    <div style="display: flex; gap: 1rem; margin-bottom: 0.5rem; font-size: 0.85rem;">
                        <label style="cursor: pointer;"><input type="radio" name="photo_mode" value="file" onclick="togglePhotoMode('file')" checked> Upload Berkas File</label>
                        <label style="cursor: pointer;"><input type="radio" name="photo_mode" value="camera" onclick="togglePhotoMode('camera')"> Ambil Foto Kamera (Webcam)</label>
                    </div>

                    <!-- File upload input -->
                    <div id="photo_file_box">
                        <input type="file" name="photo_file" accept="image/*" class="form-control">
                    </div>

                    <!-- Live Camera Webcam Box -->
                    <div id="photo_camera_box" style="display: none;">
                        <div class="camera-container">
                            <video id="webcamVideo" autoplay playsinline></video>
                            <canvas id="webcamCanvas"></canvas>
                            <div class="camera-btn-overlay">
                                <button type="button" class="btn-custom btn-primary-custom btn-sm" id="btnSnapPhoto" onclick="takeWebcamSnapshot()">
                                    <i class="fa-solid fa-camera"></i> Jepret Foto
                                </button>
                                <button type="button" class="btn-custom btn-secondary-custom btn-sm" id="btnRetakePhoto" onclick="retakeWebcamPhoto()" style="display: none;">
                                    <i class="fa-solid fa-rotate-left"></i> Foto Ulang
                                </button>
                            </div>
                        </div>
                        <input type="hidden" name="photo_camera" id="photo_camera_input">
                    </div>
                </div>

                <!-- Duration Picker -->
                <div class="form-group">
                    <label class="form-label"><i class="fa-solid fa-clock"></i> Durasi Sewa (Jam)</label>
                    <div class="duration-buttons">
                        <button type="button" class="duration-btn active" onclick="setDuration(1)">1 Jam</button>
                        <button type="button" class="duration-btn" onclick="setDuration(2)">2 Jam</button>
                        <button type="button" class="duration-btn" onclick="setDuration(3)">3 Jam</button>
                        <button type="button" class="duration-btn" onclick="setDuration(4)">4 Jam</button>
                        <button type="button" class="duration-btn" onclick="setDuration(5)">5 Jam</button>
                    </div>
                    <input type="number" name="duration_hours" id="duration_hours" class="form-control" value="1" min="0.5" step="0.5" style="margin-top: 0.5rem;" oninput="updateModalPrice()" required>
                </div>

                <!-- Payment Method & Total Calculator -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Metode Pembayaran</label>
                        <select name="payment_method" class="form-select" required>
                            <option value="Cash">Cash (Tunai)</option>
                            <option value="QRIS">QRIS</option>
                            <option value="Transfer">Transfer Bank</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status Pembayaran</label>
                        <select name="payment_status" class="form-select" required>
                            <option value="Lunas">Lunas</option>
                            <option value="Belum Lunas">Belum Lunas</option>
                        </select>
                    </div>
                </div>

                <div style="background-color: var(--bg-input); padding: 1rem; border-radius: var(--radius-md); text-align: center; border: 1px solid var(--border-highlight);">
                    <span style="font-size: 0.85rem; color: var(--text-muted);">Total Biaya Rental:</span>
                    <h3 id="modal_total_price" style="font-family: var(--font-heading); color: var(--accent-cyan); font-size: 1.5rem;">Rp 0</h3>
                </div>
            </div>

            <div class="modal-header" style="border-top: 1px solid var(--border); border-bottom: none; justify-content: flex-end; gap: 0.75rem;">
                <button type="button" class="btn-custom btn-secondary-custom" onclick="closeStartRentalModal()">Batal</button>
                <button type="submit" class="btn-custom btn-success-custom"><i class="fa-solid fa-check-circle"></i> Mulai Rental Sekarang</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let currentStream = null;

    // Countdown Timers for Active Rentals
    function initCountdownTimers() {
        const timerElements = document.querySelectorAll('.timer-clock');
        timerElements.forEach(el => {
            const endTimeStr = el.getAttribute('data-endtime');
            if (!endTimeStr) return;
            const endTime = new Date(endTimeStr).getTime();

            const updateTimer = () => {
                const now = new Date().getTime();
                const distance = endTime - now;

                if (distance < 0) {
                    el.innerHTML = '<span style="color: var(--accent-rose);"><i class="fa-solid fa-bell"></i> WAKTU HABIS!</span>';
                    return;
                }

                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                const hStr = String(hours).padStart(2, '0');
                const mStr = String(minutes).padStart(2, '0');
                const sStr = String(seconds).padStart(2, '0');

                el.innerHTML = `<i class="fa-solid fa-clock"></i> ${hStr}:${mStr}:${sStr}`;
            };

            updateTimer();
            setInterval(updateTimer, 1000);
        });
    }

    // Modal Handlers
    function openStartRentalModal(unitId = null, unitCode = '', price = 0) {
        document.getElementById('startRentalModal').classList.add('active');
        if (unitId) {
            document.getElementById('modal_unit_id').value = unitId;
        }
        updateModalPrice();
    }

    function closeStartRentalModal() {
        document.getElementById('startRentalModal').classList.remove('active');
        stopWebcamStream();
    }

    function toggleCustomerMode(mode) {
        const existingGroup = document.getElementById('existing_customer_group');
        const newGroup = document.getElementById('new_customer_group');
        if (mode === 'existing') {
            existingGroup.style.display = 'block';
            newGroup.style.display = 'none';
        } else {
            existingGroup.style.display = 'none';
            newGroup.style.display = 'block';
        }
    }

    function togglePhotoMode(mode) {
        const fileBox = document.getElementById('photo_file_box');
        const cameraBox = document.getElementById('photo_camera_box');
        if (mode === 'file') {
            fileBox.style.display = 'block';
            cameraBox.style.display = 'none';
            stopWebcamStream();
        } else {
            fileBox.style.display = 'none';
            cameraBox.style.display = 'block';
            startWebcamStream();
        }
    }

    function startWebcamStream() {
        const video = document.getElementById('webcamVideo');
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(stream => {
                    currentStream = stream;
                    video.srcObject = stream;
                    video.style.display = 'block';
                    document.getElementById('webcamCanvas').style.display = 'none';
                    document.getElementById('btnSnapPhoto').style.display = 'inline-flex';
                    document.getElementById('btnRetakePhoto').style.display = 'none';
                })
                .catch(err => {
                    alert('Gagal mengakses kamera/webcam: ' + err.message);
                });
        } else {
            alert('Browser Anda tidak mendukung fitur Webcam API.');
        }
    }

    function stopWebcamStream() {
        if (currentStream) {
            currentStream.getTracks().forEach(track => track.stop());
            currentStream = null;
        }
    }

    function takeWebcamSnapshot() {
        const video = document.getElementById('webcamVideo');
        const canvas = document.getElementById('webcamCanvas');
        const input = document.getElementById('photo_camera_input');

        canvas.width = video.videoWidth || 640;
        canvas.height = video.videoHeight || 480;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        const dataUrl = canvas.toDataURL('image/jpeg');
        input.value = dataUrl;

        video.style.display = 'none';
        canvas.style.display = 'block';
        document.getElementById('btnSnapPhoto').style.display = 'none';
        document.getElementById('btnRetakePhoto').style.display = 'inline-flex';
    }

    function retakeWebcamPhoto() {
        document.getElementById('photo_camera_input').value = '';
        document.getElementById('webcamVideo').style.display = 'block';
        document.getElementById('webcamCanvas').style.display = 'none';
        document.getElementById('btnSnapPhoto').style.display = 'inline-flex';
        document.getElementById('btnRetakePhoto').style.display = 'none';
    }

    function setDuration(hours) {
        document.getElementById('duration_hours').value = hours;
        const buttons = document.querySelectorAll('.duration-btn');
        buttons.forEach(btn => btn.classList.remove('active'));
        event.target.classList.add('active');
        updateModalPrice();
    }

    function updateModalPrice() {
        const unitSelect = document.getElementById('modal_unit_id');
        const selectedOpt = unitSelect.options[unitSelect.selectedIndex];
        const pricePerHour = selectedOpt ? parseFloat(selectedOpt.getAttribute('data-price') || 0) : 0;
        const duration = parseFloat(document.getElementById('duration_hours').value || 0);

        const total = pricePerHour * duration;
        document.getElementById('modal_total_price').innerText = 'Rp ' + total.toLocaleString('id-ID');
    }

    document.addEventListener('DOMContentLoaded', () => {
        initCountdownTimers();
    });
</script>
@endsection
