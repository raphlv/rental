@extends('rental.layout')

@section('title', 'Dashboard')

@section('styles')
<style>
    /* Stats grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background-color: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 1rem;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1.25rem;
        transition: var(--transition);
        box-shadow: var(--shadow);
    }

    .stat-card:hover {
        transform: translateY(-5px);
        border-color: var(--primary);
    }

    .stat-icon {
        padding: 1rem;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }

    .stat-icon-total { background-color: rgba(139, 92, 246, 0.15); color: var(--primary); }
    .stat-icon-ada { background-color: rgba(16, 185, 129, 0.15); color: var(--accent); }
    .stat-icon-disewa { background-color: rgba(245, 158, 11, 0.15); color: var(--warning); }
    .stat-icon-maint { background-color: rgba(239, 68, 68, 0.15); color: var(--danger); }

    .stat-info h3 {
        font-size: 0.9rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .stat-info p {
        font-size: 1.75rem;
        font-weight: 800;
        margin-top: 0.25rem;
    }

    /* Tabs (Sheets) */
    .sheets-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.5rem;
        border-bottom: 1px solid var(--border);
        padding-bottom: 1rem;
    }

    .sheets-tabs {
        display: flex;
        gap: 0.5rem;
        overflow-x: auto;
        padding-bottom: 0.25rem;
    }

    .sheet-tab {
        padding: 0.6rem 1.25rem;
        background-color: var(--bg-card);
        border: 1px solid var(--border);
        color: var(--text-muted);
        text-decoration: none;
        border-radius: 0.75rem;
        font-weight: 600;
        transition: var(--transition);
        white-space: nowrap;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .sheet-tab:hover {
        background-color: var(--bg-input);
        color: var(--text-main);
    }

    .sheet-tab.active {
        background: linear-gradient(135deg, var(--primary), #6d28d9);
        color: var(--text-main);
        border-color: var(--primary);
        box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
    }

    .sheet-tab .badge {
        background-color: rgba(255, 255, 255, 0.2);
        padding: 0.1rem 0.5rem;
        border-radius: 0.5rem;
        font-size: 0.75rem;
    }

    .sheet-actions {
        display: flex;
        gap: 0.75rem;
    }

    /* Grid of Units */
    .units-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 1.5rem;
    }

    .unit-card {
        background-color: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 1rem;
        overflow: hidden;
        transition: var(--transition);
        display: flex;
        flex-direction: column;
        position: relative;
        box-shadow: var(--shadow);
    }

    .unit-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.4);
    }

    .unit-card.status-disewa {
        border-color: rgba(245, 158, 11, 0.4);
        box-shadow: 0 0 15px rgba(245, 158, 11, 0.15);
    }

    .unit-card.status-maintenance {
        border-color: rgba(239, 68, 68, 0.3);
    }

    .unit-header {
        padding: 1.25rem;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .unit-title {
        font-size: 1.2rem;
        font-weight: 700;
    }

    .unit-type {
        font-size: 0.75rem;
        padding: 0.25rem 0.6rem;
        background-color: var(--bg-input);
        border-radius: 0.5rem;
        color: var(--text-muted);
        font-weight: 600;
        margin-top: 0.25rem;
        display: inline-block;
    }

    .status-badge {
        padding: 0.35rem 0.75rem;
        border-radius: 2rem;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .status-ada { background-color: rgba(16, 185, 129, 0.15); color: var(--accent); border: 1px solid rgba(16, 185, 129, 0.3); }
    .status-disewa { background-color: rgba(245, 158, 11, 0.15); color: var(--warning); border: 1px solid rgba(245, 158, 11, 0.3); }
    .status-maintenance { background-color: rgba(239, 68, 68, 0.15); color: var(--danger); border: 1px solid rgba(239, 68, 68, 0.3); }

    .unit-body {
        padding: 1.25rem;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 1rem;
    }

    .price-tag {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--primary);
    }

    .price-tag span {
        font-size: 0.85rem;
        color: var(--text-muted);
        font-weight: 400;
    }

    /* Rent details inside card */
    .rent-details {
        background-color: var(--bg-input);
        border-radius: 0.75rem;
        padding: 0.85rem;
        border-left: 3px solid var(--warning);
        font-size: 0.9rem;
    }

    .rent-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.4rem;
    }

    .rent-row:last-child {
        margin-bottom: 0;
    }

    .rent-label {
        color: var(--text-muted);
    }

    .rent-val {
        font-weight: 600;
        color: var(--text-main);
    }

    .unit-actions {
        padding: 1rem 1.25rem;
        background-color: rgba(0,0,0,0.15);
        border-top: 1px solid var(--border);
        display: flex;
        gap: 0.5rem;
        justify-content: flex-end;
    }

    /* Modal Styling */
    .modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.75);
        backdrop-filter: blur(5px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
    }

    .modal.show {
        opacity: 1;
        pointer-events: auto;
    }

    .modal-content {
        background-color: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 1.25rem;
        width: 100%;
        max-width: 500px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.5);
        transform: translateY(-20px);
        transition: transform 0.3s ease;
        overflow: hidden;
    }

    .modal.show .modal-content {
        transform: translateY(0);
    }

    .modal-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: rgba(0,0,0,0.1);
    }

    .modal-header h2 {
        font-size: 1.3rem;
        font-weight: 700;
    }

    .close-btn {
        background: none;
        border: none;
        color: var(--text-muted);
        font-size: 1.5rem;
        cursor: pointer;
        transition: var(--transition);
    }

    .close-btn:hover {
        color: var(--text-main);
    }

    .modal-body {
        padding: 1.5rem;
    }

    .form-group {
        margin-bottom: 1.25rem;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
        color: var(--text-muted);
    }

    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        background-color: var(--bg-input);
        border: 1px solid var(--border);
        border-radius: 0.75rem;
        color: var(--text-main);
        font-family: var(--font);
        font-size: 0.95rem;
        transition: var(--transition);
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.15);
    }

    .modal-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--border);
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        background-color: rgba(0,0,0,0.1);
    }

    /* empty state */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background-color: var(--bg-card);
        border: 1px dashed var(--border);
        border-radius: 1rem;
        margin-top: 2rem;
    }

    .empty-state svg {
        width: 64px;
        height: 64px;
        stroke: var(--text-muted);
        margin-bottom: 1rem;
    }

    .empty-state h3 {
        font-size: 1.25rem;
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        color: var(--text-muted);
    }
</style>
@endsection

@section('content')
<!-- Stats Bar -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon stat-icon-total">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
        </div>
        <div class="stat-info">
            <h3>Total Unit</h3>
            <p>{{ $stats['total'] }}</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-ada">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
        </div>
        <div class="stat-info">
            <h3>Tersedia (Ada)</h3>
            <p style="color: var(--accent);">{{ $stats['ada'] }}</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-disewa">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
        </div>
        <div class="stat-info">
            <h3>Disewa</h3>
            <p style="color: var(--warning);">{{ $stats['disewa'] }}</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-maint">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path></svg>
        </div>
        <div class="stat-info">
            <h3>Maintenance</h3>
            <p style="color: var(--danger);">{{ $stats['maintenance'] }}</p>
        </div>
    </div>
</div>

<!-- Sheets Navigation Header -->
<div class="sheets-header">
    <div class="sheets-tabs">
        <a href="{{ route('dashboard', ['type' => 'All']) }}" class="sheet-tab {{ $selectedType === 'All' ? 'active' : '' }}">
            Semua
            <span class="badge">{{ $stats['total'] }}</span>
        </a>
        @foreach($validTypes as $type)
            <a href="{{ route('dashboard', ['type' => $type]) }}" class="sheet-tab {{ $selectedType === $type ? 'active' : '' }}">
                {{ $type }}
                <span class="badge">{{ $typeStats[$type]['total'] }}</span>
            </a>
        @endforeach
    </div>
    
    <div class="sheet-actions">
        @if($selectedType !== 'All')
            <form action="{{ route('units.reset', $selectedType) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membalikkan semua data di tab {{ $selectedType }} ke data default?')">
                @csrf
                <button type="submit" class="btn btn-secondary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"></path></svg>
                    Default Sheet
                </button>
            </form>
        @endif
        <button onclick="openModal('addUnitModal')" class="btn btn-primary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Tambah Unit
        </button>
    </div>
</div>

<!-- Units Grid -->
@if($units->isEmpty())
    <div class="empty-state">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="8" y1="12" x2="16" y2="12"></line>
        </svg>
        <h3>Tidak Ada Unit</h3>
        <p>Belum ada unit terdaftar di kategori {{ $selectedType }}. Silakan tambah unit baru.</p>
    </div>
@else
    <div class="units-grid">
        @foreach($units as $unit)
            <div class="unit-card status-{{ $unit->status }}">
                <div class="unit-header">
                    <div>
                        <h4 class="unit-title">{{ $unit->name }}</h4>
                        <span class="unit-type">{{ $unit->type }}</span>
                    </div>
                    <span class="status-badge status-{{ $unit->status }}">
                        {{ $unit->status === 'ada' ? 'Tersedia' : ($unit->status === 'disewa' ? 'Disewa' : 'Maint.') }}
                    </span>
                </div>
                
                <div class="unit-body">
                    <div class="price-tag">
                        Rp {{ number_format($unit->price_per_hour, 0, ',', '.') }}<span>/jam</span>
                    </div>

                    @if($unit->status === 'disewa' && $unit->activeRental)
                        <div class="rent-details">
                            <div class="rent-row">
                                <span class="rent-label">Penyewa:</span>
                                <span class="rent-val">{{ $unit->activeRental->customer_name }}</span>
                            </div>
                            <div class="rent-row">
                                <span class="rent-label">Durasi:</span>
                                <span class="rent-val">{{ $unit->activeRental->duration }} Jam</span>
                            </div>
                            <div class="rent-row">
                                <span class="rent-label">Metode:</span>
                                <span class="rent-val">{{ $unit->activeRental->payment_method }}</span>
                            </div>
                            <div class="rent-row">
                                <span class="rent-label">Selesai:</span>
                                <span class="rent-val text-warning">{{ $unit->activeRental->end_time->format('H:i d M') }}</span>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="unit-actions">
                    @if($unit->status === 'ada')
                        <button onclick="openRentModal({{ json_encode($unit) }})" class="btn btn-accent btn-sm">Mulai Sewa</button>
                        <button onclick="openEditModal({{ json_encode($unit) }})" class="btn btn-secondary btn-sm">Edit</button>
                    @elseif($unit->status === 'disewa' && $unit->activeRental)
                        <form action="{{ route('rentals.complete', $unit->activeRental->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm">Selesai Sewa</button>
                        </form>
                    @elseif($unit->status === 'maintenance')
                        <form action="{{ route('units.update', $unit->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <input type="hidden" name="name" value="{{ $unit->name }}">
                            <input type="hidden" name="type" value="{{ $unit->type }}">
                            <input type="hidden" name="price_per_hour" value="{{ $unit->price_per_hour }}">
                            <input type="hidden" name="status" value="ada">
                            <button type="submit" class="btn btn-accent btn-sm">Selesai Maint.</button>
                        </form>
                        <button onclick="openEditModal({{ json_encode($unit) }})" class="btn btn-secondary btn-sm">Edit</button>
                    @endif

                    <form action="{{ route('units.destroy', $unit->id) }}" method="POST" onsubmit="return confirm('Hapus unit {{ $unit->name }}? Semua riwayat sewa unit ini akan ikut terhapus.')" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endif

<!-- Add Unit Modal -->
<div id="addUnitModal" class="modal">
    <div class="modal-content">
        <form action="{{ route('units.store') }}" method="POST">
            @csrf
            <div class="modal-header">
                <h2>Tambah Unit Baru</h2>
                <button type="button" class="close-btn" onclick="closeModal('addUnitModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="name">Nama Unit</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="Contoh: PS4 - Unit 04" required>
                </div>
                <div class="form-group">
                    <label for="type">Kategori (Sheet)</label>
                    <select id="type" name="type" class="form-control" required>
                        @foreach($validTypes as $type)
                            <option value="{{ $type }}" {{ $selectedType === $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="price_per_hour">Harga Sewa per Jam (Rupiah)</label>
                    <input type="number" id="price_per_hour" name="price_per_hour" class="form-control" placeholder="Contoh: 8000" min="0" required>
                </div>
                <div class="form-group">
                    <label for="status">Status Awal</label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="ada">Tersedia (Ada)</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addUnitModal')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Unit Modal -->
<div id="editUnitModal" class="modal">
    <div class="modal-content">
        <form id="editUnitForm" method="POST">
            @csrf
            <div class="modal-header">
                <h2>Edit Unit</h2>
                <button type="button" class="close-btn" onclick="closeModal('editUnitModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="edit_name">Nama Unit</label>
                    <input type="text" id="edit_name" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="edit_type">Kategori</label>
                    <select id="edit_type" name="type" class="form-control" required>
                        @foreach($validTypes as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit_price_per_hour">Harga Sewa per Jam</label>
                    <input type="number" id="edit_price_per_hour" name="price_per_hour" class="form-control" min="0" required>
                </div>
                <div class="form-group">
                    <label for="edit_status">Status</label>
                    <select id="edit_status" name="status" class="form-control" required>
                        <option value="ada">Tersedia (Ada)</option>
                        <option value="disewa">Disewa</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editUnitModal')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Start Rent Modal -->
<div id="rentModal" class="modal">
    <div class="modal-content">
        <form action="{{ route('rentals.start') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="rent_unit_id" name="unit_id">
            <div class="modal-header">
                <h2>Mulai Rental - <span id="rent_unit_name" style="color: var(--primary);"></span></h2>
                <button type="button" class="close-btn" onclick="closeModal('rentModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="customer_name">Nama Penyewa</label>
                    <input type="text" id="customer_name" name="customer_name" class="form-control" placeholder="Nama lengkap penyewa" required>
                </div>
                <div class="form-group">
                    <label for="duration">Durasi Sewa (Jam)</label>
                    <input type="number" id="duration" name="duration" class="form-control" placeholder="Contoh: 3" min="1" required>
                </div>
                <div class="form-group">
                    <label for="payment_method">Metode Pembayaran</label>
                    <select id="payment_method" name="payment_method" class="form-control" required>
                        <option value="Cash">Cash</option>
                        <option value="Transfer">Transfer Bank</option>
                        <option value="QRIS">QRIS</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="photo_proof">Bukti Foto Penerimaan Barang</label>
                    <input type="file" id="photo_proof" name="photo_proof" class="form-control" accept="image/*" required>
                    <small style="color: var(--text-muted); display: block; margin-top: 0.25rem;">Upload foto penerimaan barang/bukti pembayaran (Max 5MB)</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('rentModal')">Batal</button>
                <button type="submit" class="btn btn-accent">Mulai Sewa</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openModal(id) {
        document.getElementById(id).classList.add('show');
    }

    function closeModal(id) {
        document.getElementById(id).classList.remove('show');
    }

    function openEditModal(unit) {
        document.getElementById('edit_name').value = unit.name;
        document.getElementById('edit_type').value = unit.type;
        document.getElementById('edit_price_per_hour').value = Math.round(unit.price_per_hour);
        document.getElementById('edit_status').value = unit.status;
        
        // Update form action dynamically
        const form = document.getElementById('editUnitForm');
        form.action = `/rental/public/units/update/${unit.id}`;
        
        openModal('editUnitModal');
    }

    function openRentModal(unit) {
        document.getElementById('rent_unit_id').value = unit.id;
        document.getElementById('rent_unit_name').innerText = unit.name;
        openModal('rentModal');
    }

    // Close modal when clicking outside content
    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.classList.remove('show');
        }
    }
</script>
@endsection
